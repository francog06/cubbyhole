using System;
using System.Collections.Generic;
using System.Diagnostics;
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

        async private void Identification_Click(object sender, RoutedEventArgs e)
        {
            this.UserName = userName.Text;
            this.Password = password.Password;

            // Make request for validation
            bool response = await Request.doLogin(UserName, Password, Label);
            if (response)
            {
                Debug.WriteLine("Minimize application");
                //SHOW IN TASK BAR
                /*this.WindowState = WindowState.Minimized;
                this.ShowInTaskbar = true; */
            }
        }

        private void Cancel_Click(object sender, RoutedEventArgs e)
        {
            App.Current.Shutdown();
        }
    }
}
