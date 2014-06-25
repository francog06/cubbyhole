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
using DSOFile;
using System.Runtime.InteropServices.ComTypes;




namespace CubbyHole
{

    public static class Request
    {

        private static Stack<Folder> myfolder = new Stack<Folder>();
        private static Queue<CubbyHole.ApiClasses.File> myfile = new Queue<CubbyHole.ApiClasses.File>();
        private static bool isSynchronizeFinished = false;
        private static FileSystemWatcher _fileWatcher;
        private static FileSystemWatcher _dirWatcher;

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

        async public static Task<bool> CreateFolder(string folderName)
        {
            WebRequest request;
            request = WebRequest.Create(Properties.Settings.Default.SiteUrl + "api/folder/add");
            request.Method = "POST";
            request.ContentType = "application/x-www-form-urlencoded";

            ArrayList queryList = new ArrayList();
            queryList.Add(string.Format("{0}={1}", "name", folderName));

            string Parameters = String.Join("&", (String[])queryList.ToArray(typeof(string)));
            request.ContentLength = Parameters.Length;

            StreamWriter sw = new StreamWriter(request.GetRequestStream());
            sw.Write(Parameters);
            sw.Close();

            Task<string> Tjson = Request.GetResponseAsync(request);
            string json = await Tjson;

            Debug.WriteLine("Json CreateFolder: " + json);
            Response<FolderResponse> resp = JsonConvert.DeserializeObject<Response<FolderResponse>>(json);

            if (resp.error)
            {
                return false;
            }
            else
            {
                FolderResponse data = resp.data;
                return true;
            }
        }

        async public static Task<bool> UpdateFolder(int folderId)
        {
            WebRequest request;
            request = WebRequest.Create(Properties.Settings.Default.SiteUrl + "api/folder/update/" + folderId);
            request.Method = "PUT";
            request.ContentType = "application/x-www-form-urlencoded";


            Task<string> Tjson = Request.GetResponseAsync(request);
            string json = await Tjson;

               Debug.WriteLine("Json UpdateFolder: " + json);
            Response<FolderResponse> resp = JsonConvert.DeserializeObject<Response<FolderResponse>>(json);

            if (resp.error)
            {
                return false;
            }
            else
            {
                FolderResponse data = resp.data;
                return true;
            }
        }

        async public static Task<bool> RemoveFolder(int folderId)
        {
            WebRequest request;
            request = WebRequest.Create(Properties.Settings.Default.SiteUrl + "api/folder/remove/" + folderId);
            request.Method = "DELETE";
            request.ContentType = "application/x-www-form-urlencoded";

            Task<string> Tjson = Request.GetResponseAsync(request);
            string json = await Tjson;

              Debug.WriteLine("Json RemoveFolder: " + json);
            Response<FolderResponse> resp = JsonConvert.DeserializeObject<Response<FolderResponse>>(json);

            if (resp.error)
            {
                return false;
            }
            else
            {
                FolderResponse data = resp.data;
                return true;
            }
        }


        async public static Task<bool> CreateFile(string fileName)
        {
            WebRequest request;
            request = WebRequest.Create(Properties.Settings.Default.SiteUrl + "api/file/add");
            request.Method = "POST";
            request.ContentType = "application/x-www-form-urlencoded";

            ArrayList queryList = new ArrayList();
            queryList.Add(string.Format("{0}={1}", "name", fileName));

            string Parameters = String.Join("&", (String[])queryList.ToArray(typeof(string)));
            request.ContentLength = Parameters.Length;

            StreamWriter sw = new StreamWriter(request.GetRequestStream());
            sw.Write(Parameters);
            sw.Close();

            Task<string> Tjson = Request.GetResponseAsync(request);
            string json = await Tjson;

               Debug.WriteLine("Json CreateFile: " + json);
            Response<FolderResponse> resp = JsonConvert.DeserializeObject<Response<FolderResponse>>(json);

            if (resp.error)
            {
                return false;
            }
            else
            {
                FolderResponse data = resp.data;
                return true;
            }
        }

        async public static Task<bool> UpdateFile(int fileId)
        {
            WebRequest request;
            request = WebRequest.Create(Properties.Settings.Default.SiteUrl + "api/file/update/" + fileId);
            request.Method = "POST";
            request.ContentType = "application/x-www-form-urlencoded";

            Task<string> Tjson = Request.GetResponseAsync(request);
            string json = await Tjson;

               Debug.WriteLine("Json UpdateFile: " + json);
            Response<FolderResponse> resp = JsonConvert.DeserializeObject<Response<FolderResponse>>(json);

            if (resp.error)
            {
                return false;
            }
            else
            {
                FolderResponse data = resp.data;
                return true;
            }
        }

        async public static Task<bool> RemoveFile(int fileId)
        {
            WebRequest request;
            request = WebRequest.Create(Properties.Settings.Default.SiteUrl + "api/file/remove/" + fileId);
            request.Method = "DELETE";
            request.ContentType = "application/x-www-form-urlencoded";

            Task<string> Tjson = Request.GetResponseAsync(request);
            string json = await Tjson;

               Debug.WriteLine("Json RemoveFile: " + json);
            Response<FolderResponse> resp = JsonConvert.DeserializeObject<Response<FolderResponse>>(json);

            if (resp.error)
            {
                return false;
            }
            else
            {
                FolderResponse data = resp.data;
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
              Folder fol = myfolder.Pop();
            
              string folderName =  fol.name;
              int folderId = fol.id;
              string folPath = fol.local_path;
              bool detailsFolder = await DetailsFolder(folderId);
              //ManageMetaData(folPath, folderId);


              if (detailsFolder)
                  createFolderLocal(fol.local_path, folderName);

              OleDocumentProperties myFileMetaData = new OleDocumentProperties();
              myFileMetaData.Open(folPath, false, dsoFileOpenOptions.dsoOptionDefault);

              myFileMetaData.SummaryProperties.Author = folderId.ToString();
              Console.WriteLine("folderId {0}", myFileMetaData.SummaryProperties.Author);
              myFileMetaData.Save();
              myFileMetaData.Close(true); 

          } while (myfolder.Count > 0);

           return true;         
       }

        public static void watch()
        {
                _fileWatcher = new FileSystemWatcher(Properties.Settings.Default.ApplicationFolder);
                _fileWatcher.IncludeSubdirectories = true;
                _fileWatcher.Path = Properties.Settings.Default.ApplicationFolder;
                _fileWatcher.NotifyFilter = NotifyFilters.LastAccess | NotifyFilters.LastWrite | NotifyFilters.FileName
                                            | NotifyFilters.Size | NotifyFilters.CreationTime;
                _fileWatcher.Filter = "*.*";
                _fileWatcher.Changed += new FileSystemEventHandler(OnChanged);
                _fileWatcher.Created += new FileSystemEventHandler(OnChanged);
                _fileWatcher.Deleted += new FileSystemEventHandler(OnChanged);
                _fileWatcher.Renamed += new RenamedEventHandler(OnChanged);
                _fileWatcher.EnableRaisingEvents = true;

                _dirWatcher = new FileSystemWatcher(Properties.Settings.Default.ApplicationFolder);
                _dirWatcher.Path = Properties.Settings.Default.ApplicationFolder;
                _dirWatcher.IncludeSubdirectories = true;
                _dirWatcher.NotifyFilter = NotifyFilters.LastAccess | NotifyFilters.LastWrite | NotifyFilters.DirectoryName
                                        | NotifyFilters.Size | NotifyFilters.CreationTime; _fileWatcher.EnableRaisingEvents = true;
                _dirWatcher.Filter = "*.*";
                _dirWatcher.Changed += new FileSystemEventHandler(OnChanged);
                _dirWatcher.Created += new FileSystemEventHandler(OnChanged);
                _dirWatcher.Deleted += new FileSystemEventHandler(OnChanged);
                _dirWatcher.Renamed += new RenamedEventHandler(OnChanged);
                _dirWatcher.EnableRaisingEvents = true;
        }


     async   private static void OnChanged(object source, FileSystemEventArgs e)
        {
            string name = @"" + e.FullPath.ToString();
            int id = 0;
         OleDocumentProperties myFileMetaData   = new OleDocumentProperties();
            
         //Console.WriteLine("ONCHANGED File: " + name + " " + e.ChangeType);
          /*  if (source == _fileWatcher)
            Console.WriteLine("source FILE", source);
            else
                Console.WriteLine("source DIRECT", source);
            */
            try
            {
                myFileMetaData.Open(name, false, dsoFileOpenOptions.dsoOptionDefault);
               
                myFileMetaData.Save();
                myFileMetaData.Close(true);
            }
            catch (Exception ex)
            {
                 Console.WriteLine(ex.ToString());
            }
          
            if (isSynchronizeFinished)
            {
                if (source == _fileWatcher)
                {
                    Console.WriteLine("folder name {0} - folder id ", name, id);

                    if (e.ChangeType.ToString() == "Created")
                    {
                        await CreateFile(name);
                    }
                    else if (e.ChangeType.ToString() == "Renamed" || e.ChangeType.ToString() == "Changed")
                    {
                        await UpdateFile(id);
                    }
                    else if (e.ChangeType.ToString() == "Deleted")
                    {
                        await RemoveFile(id);
                    }

                }
                else
                {
                    Console.WriteLine("file name {0} - file id ", name, id);

                    if (e.ChangeType.ToString() == "Created")
                    {
                        await CreateFolder(name);
                    }
                    else if (e.ChangeType.ToString() == "Renamed" || e.ChangeType.ToString() == "Changed")
                    {
                        await UpdateFolder(id);
                    }
                    else if (e.ChangeType.ToString() == "Deleted")
                    {
                        await RemoveFolder(id);
                    }
                } 
            }


        }

         public static void ManageMetaData(string filename, int id)
        {
           OleDocumentProperties myFileMetaData = new DSOFile.OleDocumentProperties();
            myFileMetaData.Open(filename, false, DSOFile.dsoFileOpenOptions.dsoOptionDefault);
            object val = id;
            Random m = new Random();
            string MetaDataName = "FolderID";
           
            if (myFileMetaData.CustomProperties.Count == 0)
            {
                Console.WriteLine("here");
                myFileMetaData.CustomProperties.Add(MetaDataName, ref val);
            }
            else
           {
               foreach (DSOFile.CustomProperty property in myFileMetaData.CustomProperties)
                {
                    if (property.Name == MetaDataName)
                    {
                        Console.WriteLine("property.Name {0}", property.Name);
                        property.set_Value(val);
                        Console.WriteLine("PROPERTy Value {0}", property.get_Value().ToString());
                    }
                }
            } 
            myFileMetaData.Save();
            myFileMetaData.Close(true);
        }

        async public static void DepileFiles()
        {
            do
            {
                CubbyHole.ApiClasses.File fil =  myfile.Dequeue();
                string fileName = fil.name;
                string filePath = "";
                int fileId = fil.id;
                if (fil.local_path == Properties.Settings.Default.ApplicationFolder)
                    filePath = fil.local_path + "\\" + fileName;
                else
                    filePath = fil.local_path;

            
                await Synchronize(fileId, filePath);
            
                
                //ManageMetaData(filePath, fileId);
               OleDocumentProperties myFileMetaData = new OleDocumentProperties();
             /*   myFileMetaData.Open(filePath, false, dsoFileOpenOptions.dsoOptionDefault);

                myFileMetaData.SummaryProperties.Author = fileId.ToString();
                Console.WriteLine("fileId {0}", myFileMetaData.SummaryProperties.Author);
                myFileMetaData.Save();
                myFileMetaData.Close(true);*/
                isSynchronizeFinished = false;
            } while (myfile.Count > 0);
            isSynchronizeFinished = true;
            Console.WriteLine("BOOOOl");
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
           // Console.WriteLine("createFolderLocal " + pathcomplete);

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
