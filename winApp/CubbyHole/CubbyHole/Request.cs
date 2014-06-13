using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Net;
using System.IO;
using System.Web;
using System.Threading;
using Newtonsoft.Json;
using CubbyHole.ApiClasses;
using System.Diagnostics;
using System.Collections.Specialized;
using System.Collections;
using CubbyHole.ApiResponse;
using System.Windows.Controls;
using System.Windows.Media;
using Microsoft.Win32;
using System.Windows.Forms;
using System.Runtime.InteropServices;
using System.Xml;
using System.ComponentModel;
using System.Runtime.Serialization.Formatters.Binary;


namespace CubbyHole
{

    public static class Request
    {

        private static Stack<Folder> myfolder = new Stack<Folder>();
        private static Queue<CubbyHole.ApiClasses.File> myfile = new Queue<CubbyHole.ApiClasses.File>();

        async public static Task<string> GetResponseAsync(this WebRequest request)
        {
            if (request == null)
            {
                throw new Exception("request");
            }

            WebResponse response;
            try
            {
                Task<WebResponse> Tresponse = request.GetResponseAsync();
                response = await Tresponse;
            }
            catch (WebException ex)
            {
                using (response = ex.Response)
                {
                    HttpWebResponse httpResponse = (HttpWebResponse)response;
                    Console.WriteLine("Error code: {0}", httpResponse.StatusCode);
                    using (Stream data = response.GetResponseStream())
                    using (var reader = new StreamReader(data))
                    {
                        string text = reader.ReadToEnd();
                        return text;
                    }
                }
            }

            Stream objStream;
            objStream = response.GetResponseStream();
            StreamReader objReader = new StreamReader(objStream);

            using (var reader = new StreamReader(objStream))
            {
                string json = reader.ReadToEnd();
                return json;
            }
        }
        
        async public static Task<bool> doLogin(string username, string password, TextBlock label) {
            WebRequest request;
            request = WebRequest.Create(Properties.Settings.Default.SiteUrl + "api/user/login");
            request.Method = "POST";
            request.ContentType = "application/x-www-form-urlencoded";

            ArrayList queryList = new ArrayList();
            queryList.Add(string.Format("{0}={1}", "email", username));
            queryList.Add(string.Format("{0}={1}", "password", password));

            string Parameters = String.Join("&", (String[])queryList.ToArray(typeof(string)));
            request.ContentLength = Parameters.Length;

            StreamWriter sw = new StreamWriter(request.GetRequestStream());
            sw.Write(Parameters);
            sw.Close();

            Task<string> Tjson = Request.GetResponseAsync(request);
            string json = await Tjson;

         //   Debug.WriteLine("Json Login: " + json);
            Response<LoginResponse> resp = JsonConvert.DeserializeObject<Response<LoginResponse>>(json);
            Response<User> respUser = JsonConvert.DeserializeObject<Response<User>>(json);

            if (resp.error)
            {
                label.Text = resp.message;
                label.Foreground = new SolidColorBrush(Colors.Red);
                return false;
            }
            else
            {
                LoginResponse data = resp.data;
                Properties.Settings.Default.Token = data.token;
                Properties.Settings.Default.IdUser = data.user.id;
                Debug.WriteLine("data.user.id Login: " + data.user.id);
                Properties.Settings.Default.Save();

                FolderBrowserDialog dialog = new FolderBrowserDialog();
                dialog.ShowDialog();

                Properties.Settings.Default.ApplicationFolder = dialog.SelectedPath;
                Properties.Settings.Default.Save();
                return true;
            }
        }

        async public static Task<bool> FolderUserRoot(int id)
        {
            WebRequest request;
            request = WebRequest.Create(Properties.Settings.Default.SiteUrl + "api/folder/user/" + id + "/root");
            request.Method = "GET";
            request.Headers.Add("X-API-KEY", Properties.Settings.Default.Token);

            Task<string> Tjson = Request.GetResponseAsync(request);
            string json = await Tjson;
       //     Debug.WriteLine("JSON FolderUserRoot/root: " + json);

            Response<FolderResponse> resp = JsonConvert.DeserializeObject<Response<FolderResponse>>(json);

            if (resp.error)
            {
                Debug.WriteLine("ERROR FolderUserRoot");
                return false; ; 
            }
            else
            {
                Debug.WriteLine("FolderUserRoot OK");
            //    Debug.WriteLine(resp.message);

                FolderResponse data = new FolderResponse();
                data = resp.data;
                // EMPILE FIFO AND LIFO
                foreach(Folder el in data.folders)
                {
                    el.local_path = Properties.Settings.Default.ApplicationFolder;
                    myfolder.Push(el);
                //  Console.WriteLine("Foreach folder: " + el.name);
                }
                foreach (CubbyHole.ApiClasses.File il in data.files)
                {
                    il.local_path = Properties.Settings.Default.ApplicationFolder;
                    myfile.Enqueue(il);
                //  Console.WriteLine("Foreach file: " + il.name);
                }
                return true;
            }
        }

        async public static Task<bool> DepileFolder()
        {
          do {
          //    Console.WriteLine("DEPILEFOLER");
              Folder fol = myfolder.Pop();
            
              string folderName =  fol.name;
              int folderId = fol.id;
              bool detailsFolder = await DetailsFolder(folderId);

             // Console.WriteLine("fol.local_path: {0}", fol.local_path);
              if (detailsFolder)
                  createFolderLocal(fol.local_path, folderName);
          } while (myfolder.Count > 0);

           return true;         
       }

        async public static void DepileFiles()
        {
            do
            {

                CubbyHole.ApiClasses.File fil =  myfile.Dequeue();
                string fileName = fil.name;
                string filePath = "";
                if (fil.local_path == Properties.Settings.Default.ApplicationFolder)
                    filePath = fil.local_path + "\\" + fileName;
                else
                    filePath = fil.local_path;
                int fileId = fil.id;

                Console.WriteLine("fileId {0} - fileName  ({1}) -  PATH {2}", fileId, fileName, filePath);
                await Synchronize(fileId, filePath);
            } while (myfile.Count > 0);
        }

        
        async public static Task<bool> DetailsFolder(int id)
        {
            WebRequest request;
            request = WebRequest.Create(Properties.Settings.Default.SiteUrl + "api/folder/details/" + id);
            request.Method = "GET";
            request.Headers.Add("X-API-KEY", Properties.Settings.Default.Token);

            Task<string> Tjson = Request.GetResponseAsync(request);
            string json = await Tjson;
         //   Debug.WriteLine("JSON DetailsFolder: " + json);
            Response<FolderResponse> resp = JsonConvert.DeserializeObject<Response<FolderResponse>>(json);
            
            if (resp.error)
            {
                Debug.WriteLine("ERROR DetailsFolder");
                return false;
            }
            else
            {
                FolderResponse data = resp.data;

                //Debug.WriteLine(" DetailsFolder DATA: " + resp.data.folder.folders.Count);
                 foreach (Folder el in data.folder.folders)
                {
                     el.local_path = Properties.Settings.Default.ApplicationFolder + "\\" + resp.data.folder.name;
                     myfolder.Push(el);
                   // Console.WriteLine("DetailsFolder folder: " + el.local_path);
                } 
                foreach (CubbyHole.ApiClasses.File el in data.folder.files)
                {
                    Console.WriteLine("DetailsFolder files name: " + resp.data.folder.name + " el name file: " + el.name);
                    el.local_path = Properties.Settings.Default.ApplicationFolder + "\\" + resp.data.folder.name + "\\" + el.name;
                    myfile.Enqueue(el);
                }
               // Debug.WriteLine(" DetailsFolder OK");
                return true;
            }

        }

        public static void createFolderLocal(string path, string name)
        {
            string pathcomplete = "";
            pathcomplete = path + "\\" + name;

            Console.WriteLine("createFolderLocal " + pathcomplete);

            try
            {
                DirectoryInfo di = Directory.CreateDirectory(pathcomplete);
               // Console.WriteLine("The directory was created successfully at {0}.", Directory.GetCreationTime(pathcomplete));
            }
            catch (Exception e)
            {
                Console.WriteLine("The process failed: {0}", e.ToString());
            } 
        }

        async public static Task<bool> Synchronize(int id, string file) 
        {
            WebRequest request; 
            request = WebRequest.Create(Properties.Settings.Default.SiteUrl + "api/file/synchronize/" + id + "?hash=ab14d0415c485464a187d5a9c97cc27c");
            request.Method = "GET";
            request.Headers.Add("X-API-KEY", Properties.Settings.Default.Token);

            Task<string> Tjson = Request.GetResponseAsync(request);
            string json = await Tjson;
            byte[] rawData = System.Text.Encoding.UTF8.GetBytes(json);
            Console.WriteLine("FILE SYNCHO " + file);
            System.IO.File.WriteAllBytes(file, rawData);

            return true;
        } 

    }
}
