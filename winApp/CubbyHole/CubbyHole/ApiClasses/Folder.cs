using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace CubbyHole.ApiClasses
{
    class Folder
    {
        public int id { get; set; }
        public DateTime creation_date { get; set; }
        public DateTime last_update_date { get; set; }
        public bool is_public { get; set; }
        public string access_key { get; set; }
    }
}
