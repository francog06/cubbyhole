using CubbyHole.ApiClasses;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace CubbyHole.ApiResponse
{
    class FolderResponse
    {
        public List<Folder> folders { get; set; }
        public User user { get; set; }
        public List<File> files { get; set; }
        public Folder folder { get; set; }
        
    }
}
