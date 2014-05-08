<div class="site-wrapper">
    <div class="">
        <div class="cover-container">
            <!-- Menu -->
            <div class="masthead clearfix">
                <div class="">
                    <h3 class="masthead-brand"><img src="<?=img("logo.png")?>" height="60" alt="logo" /></h3>
                    <ul class="nav nav-pills pull-right" style="margin-top:20px;">
                        <li class="active"><a href="/user">Accueil</a></li>
                        <li><a href="">Mon compte</a></li>
                        <li><a href="/user/deconnexion">Déconnexion</a></li>
                    </ul>
                </div>
            </div>

            <div class="inner cover admin">
               <h1>Mon Cubbyhole - <?= $user->getId(); ?></h1>
               <script type="text/javascript">
                    $(document).ready(function(){
                        $("span.sprite").tooltip();
                    });
                    
               </script>
                <p style="float:left">
                    <a id="newFile" data-toggle="modal" data-target="#newFileModal">
                        <span class="sprite newFile" data-toggle="tooltip" data-placement="top" title="Ajouter un fichier"></span>
                    </a>
                </p>
                <div style="text-align:right;">
                    <span class="glyphicon glyphicon-hdd" style="color:#39b3d7;top:-2px;margin-right:4px;"></span>
                    <div class="progress" style="width:200px;display:inline-block;margin:0">
                      <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;">
                        600 Mo libres (60%)
                      </div>
                    </div>
                    &nbsp; 
                    <a style="vertical-align:top;">Plus d'espace ?</a>
                </div>
                     
               <table class="table table-striped table-hover sortable">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th data-defaultsort="asc">Type</th>
                        <th>Modifié le</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="sprite dossierPartage"></span> Bonjour Supinfo</td>
                        <td>Dossier partagé</td>
                        <td>--</td>
                        <td>Supprimer</td>
                    </tr>
                    <tr>
                        <td><span class="sprite dossierPartage"></span> Cubbyhole Project</td>
                        <td>Dossier partagé</td>
                        <td>--</td>
                        <td>Supprimer</td>
                    </tr>
                    <tr>
                        <td><span class="sprite dossier"></span> Projet perso</td>
                        <td>Dossier</td>
                        <td>--</td>
                        <td>Supprimer</td>
                    </tr>
                    <tr>
                        <td><span class="sprite dossierPartage"></span> Supinfo - Cours - M1</td>
                        <td>Dossier partagé</td>
                        <td>--</td>
                        <td>Supprimer</td>
                    </tr>
                    <tr>
                        <td><span class="sprite file"></span> Fichier 1</td>
                        <td>Fichier</td>
                        <td>28/04/2014 16:45</td>
                        <td>Supprimer</td>
                    </tr>
                    <tr>
                        <td><span class="sprite file"></span> Fichier 2</td>
                        <td>Fichier</td>
                        <td>28/04/2014 16:45</td>
                        <td>Supprimer</td>
                    </tr>
                    <tr>
                        <td><span class="sprite file"></span> Fichier 3</td>
                        <td>Fichier</td>
                        <td>28/04/2014 16:45</td>
                        <td>Supprimer</td>
                    </tr>
                    <tr>
                        <td><span class="sprite filePartage"></span> Fichier 4</td>
                        <td>Fichier Partagé</td>
                        <td>28/04/2014 16:45</td>
                        <td>Supprimer</td>
                    </tr>
                    <tr>
                        <td><span class="sprite file"></span> Fichier 5</td>
                        <td>Fichier</td>
                        <td>28/04/2014 16:45</td>
                        <td>Supprimer</td>
                    </tr>
                </tbody>
               </table>
            </div>

            <div class="mastfoot">
                <div class="inner">
                    <p>Cubbyhole powered baby !</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $.bootstrapSortable();
</script>
<!-- Modal edit user -->
<div class="modal fade" id="newFileModal" tabindex="-1" role="dialog" aria-labelledby="Nouveau fichier" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Nouveau fichier</h4>
      </div>
      <form class="form-horizontal" role="form" method="post" id="formEditUser">
        <input type="hidden" id="user_id" name="user_id" value="" />
          <div class="modal-body">
              <div class="form-group">
                <label for="user_email" class="col-sm-2 control-label">Nom du fichier</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="file_name" placeholder="mon_fichier.txt" name='file_name' />
                </div>
                <br />
                <h2>Ou</h2>
                <br /> 
                <div id="dragandrophandler">Drag and drop</div>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary" id="submitEditUser" data-loading-text="Loading...">Enregistrer</button>
          </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
    var obj = $("#dragandrophandler");
    obj.on('dragenter', function (e) 
    {
        e.stopPropagation();
        e.preventDefault();
        $(this).css('border', '2px solid #ccc');
    });
    obj.on('dragover', function (e) 
    {
         e.stopPropagation();
         e.preventDefault();
    });
    obj.on('drop', function (e) 
    {
     
         $(this).css('border', '2px dashed #ccc');
         e.preventDefault();
         var files = e.originalEvent.dataTransfer.files;
     
         //We need to send dropped files to Server
         handleFileUpload(files,obj);
    });

    function handleFileUpload(files,obj)
    {
       for (var i = 0; i < files.length; i++) 
       {
            var fd = new FormData();
            fd.append('file', files[i]);
     
            var status = new createStatusbar(obj); //Using this we can set progress.
            status.setFileNameSize(files[i].name,files[i].size);
            sendFileToServer(fd,status);
     
       }
    }

    var rowCount=0;
    function createStatusbar(obj)
    {
         rowCount++;
         var row="odd";
         if(rowCount %2 ==0) row ="even";
         this.statusbar = $("<div class='statusbar "+row+"'></div>");
         this.filename = $("<div class='filename'></div>").appendTo(this.statusbar);
         this.size = $("<div class='filesize'></div>").appendTo(this.statusbar);
         this.progressBar = $("<div class='progressBar'><div></div></div>").appendTo(this.statusbar);
         this.abort = $("<div class='abort'>Abort</div>").appendTo(this.statusbar);
         obj.after(this.statusbar);
     
        this.setFileNameSize = function(name,size)
        {
            var sizeStr="";
            var sizeKB = size/1024;
            if(parseInt(sizeKB) > 1024)
            {
                var sizeMB = sizeKB/1024;
                sizeStr = sizeMB.toFixed(2)+" MB";
            }
            else
            {
                sizeStr = sizeKB.toFixed(2)+" KB";
            }
     
            this.filename.html(name);
            this.size.html(sizeStr);
        }
        this.setProgress = function(progress)
        {       
            var progressBarWidth =progress*this.progressBar.width()/ 100;  
            this.progressBar.find('div').animate({ width: progressBarWidth }, 10).html(progress + "% ");
            if(parseInt(progress) >= 100)
            {
                this.abort.hide();
            }
        }
        this.setAbort = function(jqxhr)
        {
            var sb = this.statusbar;
            this.abort.click(function()
            {
                jqxhr.abort();
                sb.hide();
            });
        }
    }

    function sendFileToServer(formData,status)
    {
        var uploadURL ="http://hayageek.com/examples/jquery/drag-drop-file-upload/upload.php"; //Upload URL
        var extraData ={}; //Extra Data.
        var jqXHR=$.ajax({
                xhr: function() {
                var xhrobj = $.ajaxSettings.xhr();
                if (xhrobj.upload) {
                        xhrobj.upload.addEventListener('progress', function(event) {
                            var percent = 0;
                            var position = event.loaded || event.position;
                            var total = event.total;
                            if (event.lengthComputable) {
                                percent = Math.ceil(position / total * 100);
                            }
                            //Set progress
                            status.setProgress(percent);
                        }, false);
                    }
                return xhrobj;
            },
            url: uploadURL,
            type: "POST",
            contentType:false,
            processData: false,
            cache: false,
            data: formData,
            success: function(data){
                status.setProgress(100);
     
                //$("#status1").append("File upload Done<br>");           
            }
        }); 
     
        status.setAbort(jqXHR);
}
</script>