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


namespace CubbyHole
{

    public static class Request
    {
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

            Debug.WriteLine("Json Login: " + json);
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
            Debug.WriteLine("JSON FolderUserRoot/root: " + json);

            Response<FolderResponse> resp = JsonConvert.DeserializeObject<Response<FolderResponse>>(json);

            if (resp.error)
            {
                Debug.WriteLine("ERROR FolderUserRoot");
                return false;
            }
            else
            {
                FolderResponse data = new FolderResponse();
                data = resp.data;
                foreach( Folder  el in data.folders)
                {
                    Debug.WriteLine("USER ID: " + el.id);
                }

                /*   CubbyHole.ApiClasses.Folder fol = respFol.data;
                     Debug.WriteLine("RespFol Name: " + fol.user);  */

                Debug.WriteLine("FolderUserRoot OK");
                Debug.WriteLine(resp.message);
                return true;
            }
        }

        async public static Task<bool> Synchronize(int id) 
        {
            WebRequest request; //api/file/synchronize/{:ID}?hash=ab14d0415c485464a187d5a9c97cc27c
            request = WebRequest.Create(Properties.Settings.Default.SiteUrl + "api/file/synchronize/" + id + "?hash=ab14d0415c485464a187d5a9c97cc27c");
            request.Method = "GET";
            request.Headers.Add("X-API-KEY", Properties.Settings.Default.Token);

    
      /*   using (WebResponse response = request.GetResponse())
                {
                    using (Stream stream = response.GetResponseStream())
                    {
                        XmlTextReader reader = new XmlTextReader(stream);
                        //debut

                    }
            }        */


            Task<string> Tjson = Request.GetResponseAsync(request);
            string json = await Tjson;
            Debug.WriteLine("JSON SYNCHRO: " + json);

            Response<CubbyHole.ApiClasses.File> resp = JsonConvert.DeserializeObject<Response<CubbyHole.ApiClasses.File>>(json);

            if (resp.error)
            {
                Debug.WriteLine("ERROR SYNCHRONIZE");
                return false;
            }
            else
            {
                Debug.WriteLine("OKAY  SYNCHRONIZE");

                return true;
            }

        } 

    }
}
