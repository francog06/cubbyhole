using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace CubbyHole.ApiClasses
{
    class Response<T>
    {
        public bool error {get; set;}
        public string message { get; set; }
        public T data { get; set; }

        override public string ToString()
        {
            return "Message: " + message;
        }
    }
}
