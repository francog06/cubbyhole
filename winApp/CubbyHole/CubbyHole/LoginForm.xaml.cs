using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Data;
using System.Windows.Documents;
using System.Windows.Forms;
using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Imaging;
using System.Windows.Shapes;

namespace CubbyHole
{
    public partial class LoginForm : Window
    {
        private string UserName { get; set; }
        private string Password { get; set; }
        private System.Windows.Forms.NotifyIcon notifyIcon1  = new NotifyIcon();
      
        public LoginForm()
        {
            if (string.IsNullOrEmpty(Properties.Settings.Default.Token))
            {
                Debug.WriteLine("Do not show login");
            }
            else
            {
                InitializeComponent();
            }
        }

        private void notifyIcon1_DoubleClick(object Sender, EventArgs e)
        {
            // Show the form when the user double clicks on the notify icon.
            Console.WriteLine("logo double click");

            // Set the WindowState to normal if the form is minimized.
            if (this.WindowState == WindowState.Minimized)
                this.WindowState = WindowState.Normal;

            // Activate the form.
            this.Activate();
        }

        private void notifyIcon1_MouseDoubleClick(object sender, System.Windows.Forms.MouseEventArgs e)
        {
            this.Show();
            this.WindowState = WindowState.Normal;
        }

        async private void Identification_Click(object sender, RoutedEventArgs e)
        {
            userName.Text = "igor.morenosemedo@supinfo.com";
            password.Password = "test";
            this.UserName = userName.Text;
            this.Password =  password.Password;

            // Make request for validation
            bool response = await Request.doLogin(UserName, Password, Label);
            if (response)
            {
                this.notifyIcon1.Icon = new System.Drawing.Icon("logo.ico");
      
                Debug.WriteLine("Minimize application");
                notifyIcon1.BalloonTipTitle = "Cubbyhole";
                notifyIcon1.BalloonTipText = "Synchronisation de vos dossiers...";
                this.WindowState = WindowState.Minimized;

                if (WindowState.Minimized == this.WindowState)
                {
                    notifyIcon1.Visible = true;
                    notifyIcon1.ShowBalloonTip(500);
                    this.Hide();
                    this.notifyIcon1.MouseDoubleClick += new System.Windows.Forms.MouseEventHandler(this.notifyIcon1_MouseDoubleClick);
                    DownLoadLocal();
                    Request.watch();
                }
                else
                {
                    notifyIcon1.Visible = false;
                }
            }
         //   Request.watch();
        }


        async private void DownLoadLocal()
        {
      //      await Request.Synchronize(103);
            //API ROOT
         bool responseRoot =   await Request.FolderUserRoot(Properties.Settings.Default.IdUser);
         Console.WriteLine("DownLoadLocal okay {0}", responseRoot);

            // root ok
         if (responseRoot)
         {
             bool responseDepileFolder = await Request.DepileFolder();

             //si folder empty
             if (responseDepileFolder)
             {
                 Request.DepileFiles();
             }
             
         }         
       }

        private void Cancel_Click(object sender, RoutedEventArgs e)
        {
            App.Current.Shutdown();
        }

   
    }
}
