﻿using System;
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

       /* protected override void Dispose(bool disposing)
        {
            // Clean up any components being used.
            if (disposing)
                if (components != null)
                    components.Dispose();

            base.Dispose(disposing);
        }*/

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
            this.UserName = userName.Text;
            this.Password = password.Password;

            Debug.WriteLine("login !!!!!!!!!!");

            // Make request for validation
            bool response = await Request.doLogin(UserName, Password, Label);
            if (response)
            {

                this.notifyIcon1.Icon = new System.Drawing.Icon("logo.ico");
      
                Debug.WriteLine("Minimize application");
                //SHOW IN TASK BAR
                 /*this.WindowState = WindowState.Minimized;
                this.ShowInTaskbar = true; */
                //Test notificy
                notifyIcon1.BalloonTipTitle = "Cubbyhole";
                notifyIcon1.BalloonTipText = "Synchronisation de vos dossiers";
                this.WindowState = WindowState.Minimized;

                if (WindowState.Minimized == this.WindowState)
                {
                    notifyIcon1.Visible = true;
                    notifyIcon1.ShowBalloonTip(500);
                    this.Hide();
                    this.notifyIcon1.MouseDoubleClick += new System.Windows.Forms.MouseEventHandler(this.notifyIcon1_MouseDoubleClick);
                    //            notifyIcon1.DoubleClick += new System.EventHandler(this.notifyIcon1_DoubleClick);
                }
                else
                {

                }
            }
        }

        private void Cancel_Click(object sender, RoutedEventArgs e)
        {
            App.Current.Shutdown();
        }

   
    }
}
