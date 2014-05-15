using CubbyHole.ApiClasses;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace CubbyHole.ApiResponse
{
    class LoginResponse
    {
        public User user { get; set; }
        public string token { get; set; }
    }
}
