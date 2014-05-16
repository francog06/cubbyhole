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

            Debug.WriteLine(json);
            Response<LoginResponse> resp = JsonConvert.DeserializeObject<Response<LoginResponse>>(json);
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
                Properties.Settings.Default.Save();

                FolderBrowserDialog dialog = new FolderBrowserDialog();
                dialog.ShowDialog();

                Properties.Settings.Default.ApplicationFolder = dialog.SelectedPath;
                Properties.Settings.Default.Save();

                return true;
            }
        }
    }
}
