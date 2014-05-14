using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace CubbyHole.ApiClasses
{
    class Error
    {
        public bool error {get; set;}
        public string error_message {get; set;}
        public string message { get; set; }

        public string ToString()
        {
            return "Message: " + error_message + " " + message;
        }
    }
}
