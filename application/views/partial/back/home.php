<div class="inner cover admin">
   <h1>Mon Cubbyhole</h1>
   <script type="text/javascript">
        $(document).ready(function(){
            $("span.sprite").tooltip();
            $("div.space").tooltip();
        });
        
   </script>
    <p style="float:left">
        <a id="newFile" data-toggle="modal" data-target="#newFileModal">
            <span class="sprite newFile" data-toggle="tooltip" data-placement="top" title="Ajouter un fichier"></span>
        </a>
    </p>
    <?php 
        // en GO dans la base
        $total_storage = $user->getActivePlanHistory()->getPlan()->getUsableStorageSpace()*1000;
        //en MB dans la base
        $space_used = $user->getStorageUsed()*100;
        // en %, libre
        $percent_free = 100*(($total_storage-$space_used)/$total_storage); 
        $percent_used = 100*(($space_used)/$total_storage);
    ?>
    <style type="text/css">
    /*.progress span {
        font-size: 12px;
        line-height: 20px;
        text-align: center;
        position: absolute;
        width: 100%;
        left: 0;
    }*/
    </style>
    <div style="text-align:right;">
        <span class="glyphicon glyphicon-hdd" style="color:#39b3d7;top:-2px;margin-right:4px;"></span>
        <div class="progress space" style="width:200px;display:inline-block;margin:0;position:relative"  data-toggle="tooltip" data-placement="top" title="Espace : <?= $space_used; ?> / <?= $total_storage; ?> Mo utilisés (<?= intval($percent_free); ?>% libres)">
          <div class="progress-bar" role="progressbar" aria-valuenow="<?= intval($space_used); ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= intval($percent_used); ?>%;">
            <?= $total_storage-$space_used; ?> Mo libres (<?= intval($percent_free); ?>%)
          </div>
        </div>
        &nbsp; 
        <a style="vertical-align:top;">Plus d'espace ?</a>
    </div>
         
    <script type="text/javascript">
    $(document).ready(function(){
        //Chargement fichiers User


        // Hover actions tableau
        $(".table tbody tr").hover(function(){
            $(this).find('td > div').css("display","inline-block");
        },function(){
            $(this).find('td > div').css("display","none");
        });
    });
    </script>
    <style type="text/css">
    .table.sortable>tbody>tr>td{
        height:40px;
    }
    </style>
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
            <td style="width:175px;"><div style="display:none"><button type="button" class="btn btn-xs btn-info editer" data-loading-text="Loading..."><span class="glyphicon glyphicon-pencil"></span>&nbsp; Editer</button> 
                 &nbsp; 
                <button type="button" class="btn btn-xs btn-danger supprimer"><span class="glyphicon glyphicon-trash"></span>&nbsp; Supprimer</button></div>
            </td>
        </tr>
        <tr>
            <td><span class="sprite dossier"></span> Projet perso</td>
            <td>Dossier</td>
            <td>--</td>
            <td><div style="display:none"><button type="button" class="btn btn-xs btn-info editer" data-loading-text="Loading..."><span class="glyphicon glyphicon-pencil"></span>&nbsp; Editer</button> 
                 &nbsp; 
                <button type="button" class="btn btn-xs btn-danger supprimer"><span class="glyphicon glyphicon-trash"></span>&nbsp; Supprimer</button></div>
            </td>
        </tr>
        <tr>
            <td><span class="sprite file"></span> Fichier 1</td>
            <td>Fichier</td>
            <td>28/04/2014 16:45</td>
            <td><div style="display:none"><button type="button" class="btn btn-xs btn-info editer" data-loading-text="Loading..."><span class="glyphicon glyphicon-pencil"></span>&nbsp; Editer</button> 
                 &nbsp; 
                <button type="button" class="btn btn-xs btn-danger supprimer"><span class="glyphicon glyphicon-trash"></span>&nbsp; Supprimer</button></div>
            </td>
        </tr>
        <tr>
            <td><span class="sprite filePartage"></span> Fichier 4</td>
            <td>Fichier Partagé</td>
            <td>28/04/2014 16:45</td>
            <td><div style="display:none"><button type="button" class="btn btn-xs btn-info editer" data-loading-text="Loading..."><span class="glyphicon glyphicon-pencil"></span>&nbsp; Editer</button> 
                 &nbsp; 
                <button type="button" class="btn btn-xs btn-danger supprimer"><span class="glyphicon glyphicon-trash"></span>&nbsp; Supprimer</button></div>
            </td>
        </tr>
    </tbody>
   </table>
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
                <label for="user_email" class="col-sm-5 control-label">Sélectionner le fichier</label>
                <div class="col-sm-4">
                  <input type="file" class="form-control" id="file_name" name='file_name' />
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
    var user_id = <?= $user->getId(); ?>;
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
            fd.append('user_id', user_id);
     
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
        var extraData ={}; //Extra Data.
        console.log(formData);
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
            url: "/api/file/add",
            type: "POST",
            contentType:false,
            processData: false,
            cache: false,
            data: formData,
            headers:{
                "X-API-KEY":"5422e102a743fd70a22ee4ff7c2ebbe8"
            },
            success: function(data){
                status.setProgress(100);
     
                //$("#status1").append("File upload Done<br>");           
            }
        }); 
     
        status.setAbort(jqXHR);
}
</script>