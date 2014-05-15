using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace CubbyHole.ApiClasses
{
    class File
    {
        public int id { get; set; }
        public ApiDateTime creation_date { get; set; }
        public ApiDateTime last_update_date { get; set; }
        public string absolute_path { get; set; }
        public bool is_public { get; set; }
        public string access_key { get; set; }
        public float size { get; set; }
    }
}
