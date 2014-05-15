using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace CubbyHole.ApiClasses
{
    class User
    {
        public int id { get; set; }
        public string email { get; set; }
        public string password { get; set; }
        public DateTime registration_date { get; set; }
        public string user_location_ip { get; set; }
        public bool is_admin { get; set; }
    }
}
