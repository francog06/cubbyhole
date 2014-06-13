using CubbyHole.ApiClasses;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace CubbyHole.ApiResponse
{
    class FolderDetailsResponse
    {
        public Folder folders { get; set; }
        public File files { get; set; }
    }
}
