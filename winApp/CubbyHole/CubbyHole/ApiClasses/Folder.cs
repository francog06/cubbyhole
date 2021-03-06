﻿using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace CubbyHole.ApiClasses
{
    class Folder
    {
        public int id { get; set; }
        public string name { get; set; }
        public ApiDateTime creation_date { get; set; }
        public ApiDateTime last_update_date { get; set; }
        public bool is_public { get; set; }
        public string access_key { get; set; }
        public string local_path { get; set; }
        public List<Folder> folders { get; set; }
        public List<File> files { get; set; }
    }
}
