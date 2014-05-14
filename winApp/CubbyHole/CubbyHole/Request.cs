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

            string sLine = "";
            int i = 0;

            string json = "";
            while (sLine != null)
            {
                i++;
                sLine = objReader.ReadLine();
                if (sLine != null)
                    json += sLine;
            }

            return json;
        }

        async public static void doLogin(string username, string password) {
            WebRequest request;
            request = WebRequest.Create(Properties.Settings.Default.SiteUrl + "api/user/login");
            request.Method = "POST";
            request.ContentType = "application/x-www-form-urlencoded";

            // add sample form data
            ArrayList queryList = new ArrayList();
            queryList.Add(string.Format("{0}={1}", "email", username));
            queryList.Add(string.Format("{0}={1}", "password", password));

            // Set the encoding type
            string Parameters = String.Join("&", (String[])queryList.ToArray(typeof(string)));
            request.ContentLength = Parameters.Length;

            // Write stream
            StreamWriter sw = new StreamWriter(request.GetRequestStream());
            sw.Write(Parameters);
            sw.Close();

            Task<string> Tjson = Request.GetResponseAsync(request);
            string json = await Tjson;

            Debug.WriteLine(json);
            Error err = JsonConvert.DeserializeObject<Error>(json);
            if (err.error)
            {
                Debug.WriteLine(err.ToString());
            }
            else
            {
                User user = JsonConvert.DeserializeObject<User>(json);
                Debug.Write("Successfully connected");
            }
        }
    }
}
