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
            InitializeComponent();
        }

        private void Identification_Click(object sender, RoutedEventArgs e)
        {
            this.UserName = userName.Text;
            this.Password = password.Password;

            // Make request for validation
            Request.doLogin(UserName, Password);
        }

        private void Cancel_Click(object sender, RoutedEventArgs e)
        {
            App.Current.Shutdown();
        }
    }
}
