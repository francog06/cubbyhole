<script type="text/javascript">
    var user_id = <?= $user->getId(); ?>;
    var sprite,type;

    $(document).ready(function(){   
        //First load 
        getRoot();
        // Hover actions tableau
        $.bootstrapSortable();

        /* ------------------
            BOUTONS HEADER
        --------------------- */

        //Button refresh
        $("#refresh").click(function(){
            if($(".breadcrumb a:last-child").attr("data-id") == undefined){
                getRoot();
            }else{
                getFolder($(".breadcrumb a:last-child").attr("data-id"));
            }
        });
        // Nouveau fichier
        $("#formNewFile").submit(function(e){
            e.preventDefault();
            var formData = new FormData();
            var user_id = <?= $user->getId(); ?>;
            formData.append('file',document.getElementById('file_name').files[0]);
            formData.append('user_id', user_id);
            formData.append('folder_id', $("#folder_id").val());
            $("#loadingModal").modal("show");
            $.ajax({
                url: "/api/file/add",
                type: "POST",
                contentType:false,
                processData: false,
                cache: false,
                data: formData,
                headers:{
                    "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
                },
                success: function(result) {
                    if(result.error == false){
                       $('#newFileModal').modal("hide");
                       $("div.result").append('<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                       if($(".breadcrumb a:last-child").attr("data-id") == undefined)
                            getRoot();
                        else
                            getFolder($(".breadcrumb a:last-child").attr("data-id"));
                    }
                    else{
                        $("div.result").append('<div class="alert alert-warning fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                        $('#newFolderModal').modal("hide");
                    }
                    $("#loadingModal").modal("hide");
                },
                error: function(result) {
                    $("div.result").append('<p class="bg-danger" style="padding: 5px 0px;">Erreur lors de la transaction.</p>');
                    $('#newFileModal').modal("hide");
                    $("#loadingModal").modal("hide");
                }
            });
        });
        //Nouveau dossier
        $("#submitNewFolder").click(function(e){
            e.preventDefault();
            $.ajax({
                url: '/api/folder/add',
                type: 'POST',
                headers:{
                    "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
                },
                data:{folder_id:$("#folder_id").val(),user_id:user_id,name:$("#folder_name").val()},
                success: function(result) {
                    if(result.error == false){
                       $('#newFolderModal').modal("hide");
                       $("div.result").append('<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                       if($(".breadcrumb a:last-child").attr("data-id") == undefined)
                            getRoot();
                        else
                            getFolder($(".breadcrumb a:last-child").attr("data-id"));
                    }
                    else{
                        $("div.result").append('<div class="alert alert-warning fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                        $('#newFolderModal').modal("hide");
                    }
                },
                error: function(result) {
                    $("div.result").append('<p class="bg-danger" style="padding: 5px 0px;">Erreur lors de la transaction.</p>');
                    $('#newFolderModal').modal("hide");
                }
           });
        });

    /* ------------------
        BOUTONS LISTE
    --------------------- */
    //Eidter Folder
        $("#submitEditFolder").click(function(e){
            e.preventDefault();
            $.ajax({
                url: '/api/folder/update/'+$("#edit_folder_id").val(),
                type: 'PUT',
                headers:{
                    "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
                },
                data:{user_id:user_id,name:$("#edit_folder_name").val()},
                success: function(result) {
                    if(result.error == false){
                       $('#editFolderModal').modal("hide");
                       $("div.result").append('<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                       if($(".breadcrumb a:last-child").attr("data-id") == undefined)
                            getRoot();
                        else
                            getFolder($(".breadcrumb a:last-child").attr("data-id"));
                    }
                    else{
                        $("div.result").append('<div class="alert alert-warning fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                        $('#editFolderModal').modal("hide");
                    }
                },
                error: function(result) {
                    $("div.result").append('<p class="bg-danger" style="padding: 5px 0px;">Erreur lors de la transaction.</p>');
                    $('#editFolderModal').modal("hide");
                }
           });
        });
    //Editer Fichier
        $("#submitEditFile").click(function(e){
            e.preventDefault();
            $.ajax({
                url: '/api/file/update/'+$("#edit_file_id").val(),
                type: 'POST',
                headers:{
                    "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
                },
                data:{user_id:user_id,name:$("#edit_file_name").val()+$("#edit_file_ext").html()},
                success: function(result) {
                    if(result.error == false){
                       $('#editFileModal').modal("hide");
                       $("div.result").append('<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                       if($(".breadcrumb a:last-child").attr("data-id") == undefined)
                            getRoot();
                        else
                            getFolder($(".breadcrumb a:last-child").attr("data-id"));
                    }
                    else{
                        $("div.result").append('<div class="alert alert-warning fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                        $('#editFileModal').modal("hide");
                    }
                },
                error: function(result) {
                    $("div.result").append('<p class="bg-danger" style="padding: 5px 0px;">Erreur lors de la transaction.</p>');
                    $('#editFileModal').modal("hide");
                }
           });
        });
        
    });</script>
<script type="text/javascript" src="http://interactjs.io/js/interact.min.js"></script>
<script type="text/javascript">
    var obj = $("#dragandrophandler");
    var user_id = <?= $user->getId(); ?>;

    /* ------------------
        DRAG AND DROP LISTE
    --------------------- */
    
    //Drag&drop file/folder
    interact('.folder')
    // enable draggables to be dropped into this
    .dropzone(true)
    // only accept elements matching this CSS selector
    .accept('.file .drag, .folder .drag')
    // listen for drop related events
    .on('dragenter', function (event) {
        var draggableElement = event.relatedTarget,
            dropzoneElement = event.target;

        // feedback the possibility of a drop
        dropzoneElement.classList.add('drop-target');
        //draggableElement.classList.add('can-drop');
    })
    .on('dragleave', function (event) {
        // remove the drop feedback style
        event.target.classList.remove('drop-target');
        //event.relatedTarget.classList.remove('can-drop');
    })
    .on('drop', function (event) {
        //event.relatedTarget.textContent = 'Dropped';
        event.target.classList.remove('drop-target');
        //alert($(event.relatedTarget).attr("data-id"));
        if($(event.relatedTarget).parent().attr("class") == "file"){
            $.ajax({
                url: '/api/file/update/'+$(event.relatedTarget).parent().attr("data-id"),
                type: 'POST',
                headers:{
                    "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
                },
                data:{folder_id:$(event.target).attr("data-id")},
                success: function(result) {
                    if(result.error == false){
                        if($(".breadcrumb a:last-child").attr("data-id") == undefined)
                            getRoot();
                        else
                            getFolder($(".breadcrumb a:last-child").attr("data-id"));
                            
                    }
                },
                error: function(result) {
                }
            });
        }
        else if($(event.relatedTarget).parent().attr("class") == "folder"){
            $.ajax({
                url: '/api/folder/update/'+$(event.relatedTarget).parent().attr("data-id"),
                type: 'PUT',
                headers:{
                    "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
                },
                data:{folder_id:$(event.target).attr("data-id")},
                success: function(result) {
                    if(result.error == false){
                        if($(".breadcrumb a:last-child").attr("data-id") == undefined)
                            getRoot();
                        else
                            getFolder($(".breadcrumb a:last-child").attr("data-id"));
                            
                    }
                },
                error: function(result) {
                }
            });
        }
        else alert('error');
        
    });
    interact('.parentFolder')
        // enable draggables to be dropped into this
        .dropzone(true)
        // only accept elements matching this CSS selector
        .accept('.file .drag, .folder .drag')
        // listen for drop related events
        .on('dragenter', function (event) {
            var draggableElement = event.relatedTarget,
                dropzoneElement = event.target;

            // feedback the possibility of a drop
            dropzoneElement.classList.add('drop-target');
            //draggableElement.classList.add('can-drop');
        })
        .on('dragleave', function (event) {
            // remove the drop feedback style
            event.target.classList.remove('drop-target');
            //event.relatedTarget.classList.remove('can-drop');
        })
        .on('drop', function (event) {
            //event.relatedTarget.textContent = 'Dropped';
            event.target.classList.remove('drop-target');
            //alert($(event.relatedTarget).attr("data-id"));
            if($(event.relatedTarget).parent().attr("class") == "file"){
                $.ajax({
                    url: '/api/file/update/'+$(event.relatedTarget).parent().attr("data-id"),
                    type: 'POST',
                    headers:{
                        "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
                    },
                    data:{folder_id:$(event.target).attr("data-id")},
                    success: function(result) {
                        if(result.error == false){
                            if($(".breadcrumb a:last-child").attr("data-id") == undefined)
                                getRoot();
                            else
                                getFolder($(".breadcrumb a:last-child").attr("data-id"));
                                
                        }
                    },
                    error: function(result) {
                    }
                });
            }
            else if($(event.relatedTarget).parent().attr("class") == "folder"){
                $.ajax({
                    url: '/api/folder/update/'+$(event.relatedTarget).parent().attr("data-id"),
                    type: 'PUT',
                    headers:{
                        "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
                    },
                    data:{folder_id:$(event.target).attr("data-id")},
                    success: function(result) {
                        if(result.error == false){
                            if($(".breadcrumb a:last-child").attr("data-id") == undefined)
                                getRoot();
                            else
                                getFolder($(".breadcrumb a:last-child").attr("data-id"));
                                
                        }
                    },
                    error: function(result) {
                    }
                });
            }
            else alert('error');
            
        });
    interact('.file .drag')
    .draggable({
        onmove: function (event) {
            var target = event.target;
            //target.x = (target.x|0) + event.dx;
            target.y = (target.y|0) + event.dy;
            target.style.webkitTransform = target.style.transform = 'translate( 0px, ' + target.y + 'px)';
        },
        onend  : function (event) {
            var target = event.target;
            target.style.webkitTransform = target.style.transform = 'translate( 0px, 0px)';
        }
    })
    .inertia(true);

    interact('.folder .drag')
    .draggable({
        onmove: function (event) {
            var target = event.target;
            //target.x = (target.x|0) + event.dx;
            target.y = (target.y|0) + event.dy;
            target.style.webkitTransform = target.style.transform = 'translate( 0px, ' + target.y + 'px)';
        },
        onend  : function (event) {
            var target = event.target;
            target.style.webkitTransform = target.style.transform = 'translate( 0px, 0px)';
        }
    })
    .inertia(true);</script>
<script type="text/javascript">
    /* ------------------
        DRAG AND DROP UPLOAD FILE
    --------------------- */
    
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
            fd.append('folder_id', $("#folder_id").val());
     
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
                "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
            },
            success: function(data){
                status.setProgress(100);
                $("#status1").append("File upload Done<br>");       
                $("#newFileModal").modal("hide");
                 if($(".breadcrumb a:last-child").attr("data-id") == undefined){
                    getRoot();
                }else{
                    getFolder($(".breadcrumb a:last-child").attr("data-id"));
                }    
            }
        }); 
     
        status.setAbort(jqXHR);
    }</script>
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
        <a id="newFolder" data-toggle="modal" data-target="#newFolderModal">
            <span class="sprite newFolder" data-toggle="tooltip" data-placement="top" title="Ajouter un dossier"></span>
        </a>
        <a id="refresh">
            <span class="sprite refresh" data-toggle="tooltip" data-placement="top" title="Rafraîchir"></span>
        </a>
    </p>
    <?php 
        // en GO dans la base
        $total_storage = $user->getActivePlanHistory()->getPlan()->getUsableStorageSpace()*1000;
        //en MB dans la base
        $space_used = $user->getStorageUsed();
        // en %, libre
        $percent_free = 100*(($total_storage-$space_used)/$total_storage); 
        $percent_used = 100*(($space_used)/$total_storage);
    ?>
    <style type="text/css">
    .progress span {
        font-size: 12px;
        line-height: 20px;
        text-align: center;
        position: absolute;
        width: 100%;
        left: 0;
    }
    </style>
    <div style="text-align:right;">
        <span class="glyphicon glyphicon-hdd" style="color:#39b3d7;top:-2px;margin-right:4px;"></span>
        <div class="progress space" style="width:200px;display:inline-block;margin:0;position:relative"  data-toggle="tooltip" data-placement="top" title="Espace : <?= $space_used; ?> / <?= $total_storage; ?> Mo utilisés (<?= intval($percent_free); ?>% libres)">
          <div class="progress-bar" role="progressbar" aria-valuenow="<?= intval($space_used); ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= intval($percent_used); ?>%;">
            
          </div>
          <span><?= $total_storage-$space_used; ?> Mo libres (<?= intval($percent_free); ?>%)</span>
        </div>
        &nbsp; 
        <a style="vertical-align:top;" href="/user/upgrade/">Plus d'espace ?</a>
    </div>
    <div class="result"></div>
    <br />
    <div style="text-align:left;">Arborescence : <span class="breadcrumb"></span></div>
    <style type="text/css">
    .table.sortable>tbody>tr>td{
        height:40px;
    }
    </style>
   <table id="cubbyhole" class="table sortable">
    <thead>
        <tr>
            <th>Nom</th>
            <th data-defaultsort="asc">Type</th>
            <th>Modifié le</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
   </table>
</div>

<!-- Modal file upload -->
<div class="modal fade" id="newFileModal" tabindex="-1" role="dialog" aria-labelledby="Nouveau fichier" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Nouveau fichier</h4>
      </div>
      <form class="form-horizontal" role="form" method="post" id="formNewFile" enctype="multipart/form-data">
        <input type="hidden" id="user_id" name="user_id" value="" />
        <input type="hidden" id="folder_id" name="folder_id" value="" />
          <div class="modal-body">
              <div class="form-group">
                <label for="user_email" class="col-sm-5 control-label">Sélectionner le fichier</label>
                <div class="col-sm-4">
                  <input type="file" class="form-control" id="file_name" name='file' />
                </div>
                <br />
                <h2>Ou</h2>
                <br /> 
                <div id="dragandrophandler">Drag and drop</div>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary" id="submitNewFile" data-loading-text="Loading...">Enregistrer</button>
          </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Modal folder upload -->
<div class="modal fade" id="newFolderModal" tabindex="-1" role="dialog" aria-labelledby="Nouveau dossier" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Nouveau Dossier</h4>
      </div>
      <form class="form-horizontal" role="form" method="post" id="formNewFolder">
          <div class="modal-body">
              <div class="form-group">
                <label for="user_email" class="col-sm-5 control-label">Nom du dossier </label>
                <div class="col-sm-4">
                  <input type="text" class="form-control" id="folder_name" name='folder_name' />
                </div>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary" id="submitNewFolder" data-loading-text="Loading...">Enregistrer</button>
          </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Modal partage -->
<style type="text/css">
    .btn-default {
        color: #333;
        background-color: #fff;
        border-color: #ccc;
    }
    .btn-default:hover, .btn-default:focus, .btn-default:active, .btn-default.active, .open .dropdown-toggle.btn-default {
        color: #333;
        background-color: #ebebeb;
        border-color: #adadad;
    }
    .btn:active, .btn.active {
        outline: 0;
        background-image: none;
        -webkit-box-shadow: inset 0 3px 5px rgba(0,0,0,.125);
        box-shadow: inset 0 3px 5px rgba(0,0,0,.125);
    }
</style>
<div class="modal fade" id="partageModal" tabindex="-1" role="dialog" aria-labelledby="Partage" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Partager cet élément</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6" id="sharewithall" style="border-right:1px dotted #ccc">
                        <h4>Avec tout le monde</h4>
                        <div class="btn-group" data-toggle="buttons">
                          <label class="btn btn-default" id="shared_y">
                            <input type="radio" name="shared"> Oui
                          </label>
                          <label class="btn btn-default" id="shared_n">
                            <input type="radio" name="shared"> Non
                          </label>
                        </div>
                        <script type="text/javascript">
                            $("#shared_n").click(function(){ $("#publicPartage").fadeOut() });
                            $("#shared_y").click(function(){ $("#publicPartage").fadeIn() });
                        </script>
                        <div id="publicPartage" class="row" style="margin-top:25px;">
                            <label class="col-sm-2 control-label">Url</label>
                            <div class="col-sm-9">
                                <input class="form-control" type="text" id="shareUrl" Placeholder="Generating..." value="" onclick="this.select();" />
                            </div>
                            <br><br><br>
                            Partager sur mes réseaux
                            <br>
                            <a href="" id="mail"><span class="sprite mail"></span></a>
                            <a href="" id="facebook"><span class="sprite facebook"></span></a>
                            <a href="" id="twitter"><span class="sprite twitter"></span></a>
                        </div>

                    </div>
                    <div class="col-md-6">
                        <h4>Avec un autre utilisateur</h4>
                        <div class="resultShare"></div>
                        <br>
                        <form id="shareUser">
                        <div class="row">
                            <label class="col-sm-3 control-label">E-mail</label>
                            <div class="col-sm-8">
                                <input class="form-control" type="text" id="shareEmail" name="shareEmail" Placeholder="E-mail de l'utilisateur à inviter" />
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <label class="col-sm-3 control-label">Permission </label>
                            <div class="col-sm-8">
                            <input type="hidden" name="shareFile" id="shareFile" />
                            <input type="hidden" name="shareFolder" id="shareFolder" />
                                <select class="form-control" id="permission" name="permission">
                                  <option value="0">Lecture seulement</option>
                                  <option value="1">Lecture + Modification/Suppression</option>
                                </select>
                            </div>
                            <br />
                        </div>
                        <br>
                        <div class="row">
                            <button class="btn btn-info" type="submit">Enregistrer</button>
                        </div>
                        </form>
                            <br>
                            <table id="shareUsers" class="table table-condensed" style="width:90%;margin:0 auto;">
                                <thead>
                                    <tr>
                                        <th>Utilisateur</th>
                                        <th>Permission</th>
                                        <th></th>
                                    </tr>
                                </thead> 
                                <tbody></tbody>
                            </table>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Modal folder edit -->
<div class="modal fade" id="editFolderModal" tabindex="-1" role="dialog" aria-labelledby="Edition dossier" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Edition Dossier</h4>
      </div>
      <form class="form-horizontal" role="form" method="post" id="formNewFolder">
          <div class="modal-body">
              <div class="form-group">
                <label for="user_email" class="col-sm-5 control-label">Nom du dossier </label>
                <div class="col-sm-4">
                  <input type="hidden" name="edit_folder_id" id="edit_folder_id" value="" />
                  <input type="text" class="form-control" id="edit_folder_name" name='folder_name' />
                </div>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary" id="submitEditFolder" data-loading-text="Loading...">Enregistrer</button>
          </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Modal file edit -->
<div class="modal fade" id="editFileModal" tabindex="-1" role="dialog" aria-labelledby="Edition file" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Edition Fichier</h4>
      </div>
      <form class="form-horizontal" role="form" method="post" id="formNewFolder">
          <div class="modal-body">
            <label for="user_email" class="col-sm-2 control-label">Nom du fichier </label>
              <div class="input-group">  
                  <input type="hidden" name="edit_file_id" id="edit_file_id" value="" />
                  <input type="text" class="form-control" id="edit_file_name" name='file_name' value="" />
                  <span class="input-group-addon" id="edit_file_ext"></span>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary" id="submitEditFile" data-loading-text="Loading...">Enregistrer</button>
          </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Chargement modal -->
<div id="loadingModal" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="loadingModal" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
        <br />
        <img src='<?= img("ajax-loader.gif"); ?>' alt="loading" /> &nbsp; Veuillez patienter s.v.p.
        <br />
        <br />
    </div>
  </div>
</div>


<script type="text/javascript">

function getFile(id){
    if(id > 0){
        
        var $preparingFileModal  = $('#loadingModal');
        $preparingFileModal.modal("show");
        $.fileDownload("/api/file/download/"+id+"?X-API-KEY=<?= $this->session->userdata('user_token'); ?>",{
            successCallback: function (url) { $preparingFileModal.modal("hide"); }
        })
            .done(function(){alert("test");$preparingFileModal.modal("hide");})
            .fail(function(){alert("fuck"); $preparingFileModal.modal("hide");});
        return false; 
    }
}

function getRoot(){
    $('#folder_id').val("");
    $('#loadingModal').modal("show");
    $.ajax({
            url: '/api/folder/user/'+user_id+'/root',
            type: 'GET',
            headers:{
                "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
            },
            success: function(result) {
                $("#cubbyhole tbody").html("");
                if(result.error == false){
                    $(".breadcrumb").html("<a href='javascript:getRoot()'><span class='glyphicon glyphicon-home'></span></a>");
                    for(var loop_folder in result.data.folders){
                        if(result.data.folders.hasOwnProperty(loop_folder)){
                            if(result.data.folders[loop_folder].share == null){
                                sprite = "dossier";
                                type = "Dossier";
                            }
                            else{
                                sprite = "dossierPartage";
                                type = "Dossier Partagé";
                            }
                            $("#cubbyhole tbody").append('\
                                <tr class="folder" data-id="'+result.data.folders[loop_folder].id+'">\
                                    <td class="drag"><a href="javascript:getFolder('+result.data.folders[loop_folder].id+')"><span class="sprite '+sprite+'"></span>'+result.data.folders[loop_folder].name+'</a></td>\
                                    <td class="drag" data-value="a'+type+'">'+type+'</td>\
                                    <td class="drag">'+result.data.folders[loop_folder].last_update_date.date+'</td>\
                                    <td style="width:255px;"><div style="display:none">\
                                        <button type="button" class="btn btn-xs btn-warning partagerFolder"><span class="glyphicon glyphicon-link"></span> Partager</button>\
                                        <button type="button" class="btn btn-xs btn-info editer" data-loading-text="Loading..." data-fname="'+result.data.folders[loop_folder].name+'"><span class="glyphicon glyphicon-pencil"></span> Editer</button> \
                                        <button type="button" class="btn btn-xs btn-danger supprimer"><span class="glyphicon glyphicon-trash"></span> Supprimer</button></div>\
                                    </td>\
                                </tr>\
                            ').fadeIn();
                        }
                    }
                    for(var loop_file in result.data.files){
                        var extension = result.data.files[loop_file].absolute_path;
                        extension = extension.substr((~-extension.lastIndexOf(".") >>> 0) + 2);
                        var sprite;
                        switch(extension.toLowerCase()){
                            case "csv": 
                            case "xls": 
                            case "xlsx": sprite = "excel"; type = "Classeur";
                            break;
                            case "png": 
                            case "jpg": 
                            case "gif": 
                            case "psd": 
                            case "jpeg": sprite = "picture"; type = "Image";
                            break;
                            case "ppt": 
                            case "pptx": sprite = "powerpoint"; type = "Présentation";
                            break;
                            case "pdf": sprite = "pdf"; type = "PDF";
                            break;
                            case "doc":
                            case "docx": sprite = "word"; type = "Document";
                            break;
                            case "zip":
                            case "rar":
                            case "tar":
                            case "tar.gz":
                            case "gzip": sprite = "zip"; type = "Archive";
                            break;
                            default: sprite = "file"; type = "Fichier";
                            break;
                        }
                        result.data.files[loop_file].share != null?type += " Partagé":"";
                        var share;
                        result.data.files[loop_file].access_key == null?share="":share="shared";
                        if(result.data.files.hasOwnProperty(loop_file)){
                            $("#cubbyhole tbody").append('\
                                <tr class="file '+share+'" data-id="'+result.data.files[loop_file].id+'" data-key="'+result.data.files[loop_file].access_key+'">\
                                    <td class="drag"><a href="javascript:getFile('+result.data.files[loop_file].id+')"><span class="fileSprite '+sprite+'"></span>'+result.data.files[loop_file].name+'</a></td>\
                                    <td class="drag">'+type+'</td>\
                                    <td class="drag">'+result.data.files[loop_file].last_update_date.date+'</td>\
                                    <td style="width:255px;"><div style="display:none">\
                                        <button type="button" class="btn btn-xs btn-warning partager"><span class="glyphicon glyphicon-link"></span> Partager</button>\
                                        <button type="button" class="btn btn-xs btn-info editer" data-loading-text="Loading..." data-fname="'+result.data.files[loop_file].name+'"><span class="glyphicon glyphicon-pencil"></span> Editer</button> \
                                        <button type="button" class="btn btn-xs btn-danger supprimer"><span class="glyphicon glyphicon-trash"></span> Supprimer</button></div>\
                                    </td>\
                                </tr>\
                            ').fadeIn();
                        }
                    }
                    $('#loadingModal').modal("hide");
                    $.bootstrapSortable();
                }
                else{
                    $("div.result").append('<div class="alert alert-warning fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                    $('#loadingModal').modal("hide");
                }
                $("#cubbyhole tbody tr").hover(function(){
                    $(this).find('td > div').css("display","inline-block");
                },function(){
                    $(this).find('td > div').css("display","none");
                });

                $(document).ready(function(){
                    var filepartageid,filepartagekey;
                    // Click modal Folder edit
                     $("tr.folder td div button.editer").click(function(){
                        $("#editFolderModal").modal("show");
                        $("#edit_folder_id").val($(this).parent().parent().parent().attr("data-id"));
                        $("#edit_folder_name").val($(this).attr("data-fname"));
                    });
                     //Click modal partage
                    $("button.partagerFolder").click(function(){
                        folderpartageid = $(this).parent().parent().parent().attr("data-id");
                        $("#shareFolder").val(folderpartageid); 
                        $("#sharewithall").css("display","none");
                        $("#partageModal").modal("show");


                        //List share user
                        $.ajax({
                            url:"/api/folder/details/"+folderpartageid+"/shares",
                            type:"GET",
                            headers:{
                                "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
                            },
                            success: function(result){
                                for(var i in result.data.shares){
                                    if(result.data.shares.hasOwnProperty(i)){
                                        var write = result.data.shares[i].is_writable==true?"All":"Lecture seule";
                                        $("#shareUsers tbody").append("\
                                            <tr data-shareid='"+result.data.shares[i].id+"'>\
                                                <td>"+result.data.shares[i].user.email+"</td>\
                                                <td>"+write+"</td>\
                                                <td><button class='btn btn-danger btn-xs supprimerShare'><span class='glyphicon glyphicon-trash'></span> Supprimer</button></td>\
                                            </tr>\
                                        ");
                                    }
                                }
                                $(".supprimerShare").click(function(){
                                    var parent = $(this).parent().parent();
                                    $.ajax({
                                    url:"/api/share/delete/"+$(this).parent().parent().attr("data-shareid"),
                                    type:"DELETE",
                                    headers:{
                                        "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
                                    },
                                    success: function(result) {
                                        if(result.error == false){
                                            $("div.resultShare").append('<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                                            $(parent).remove();
                                        }
                                        else{
                                            $("div.result").append('<div class="alert alert-warning fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                                        }
                                    }
                                })
                                });
                            }
                        });

                        


                        //Share With user
                        $("#shareUser").submit(function(e){
                           e.preventDefault();
                           $.ajax({
                                url:"/api/share/create",
                                type:"POST",
                                headers:{
                                    "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
                                },
                                data:{write:$("#permission").val(),email:$("#shareEmail").val(),folder:$("#shareFolder").val()},
                                success: function(result) {
                                    $("#loadingModal").modal("hide");
                                    if(result.error == false){
                                         $("div.resultShare").append('<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                                         document.location.href="/";
                                    }else{
                                         $("div.resultShare").append('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                                    }
                                }
                            })
                        });


                    });

                     $("button.partager").click(function(){
                        filepartageid = $(this).parent().parent().parent().attr("data-id");
                        filepartagekey = $(this).parent().parent().parent().attr("data-key");
                        $("#sharewithall").css("display","block");
                        $("#shareFile").val(filepartageid);
                        $("#shared_y, #shared_n").removeClass("active");
                        $("#partageModal").modal("show");
                        if($(this).parent().parent().parent().hasClass("shared")){
                            $("#shared_y").addClass("active");
                            $("#publicPartage").fadeIn();
                            $("#shareUrl").val("http://www.cubbyhole.name/api/file/download/"+filepartageid+"?accessKey="+filepartagekey);
                            $("#facebook").attr("href","https://www.facebook.com/sharer/sharer.php?u=http://www.cubbyhole.name/api/file/download/"+filepartageid+"?accessKey="+filepartagekey);
                            $("#twitter").attr("href","http://twitter.com/intent/tweet/?url=http://www.cubbyhole.name/api/file/download/"+filepartageid+"?accessKey="+filepartagekey+"&text=Télécharge ce fichier depuis Cubbyhole !");
                            $("#mail").attr("href","mailto:?subject=Cubbyhole&body=http://www.cubbyhole.name/api/file/download/"+filepartageid+"?accessKey="+filepartagekey+"&text=Télécharge ce fichier depuis Cubbyhole !");
                        }
                        else {
                            $("#shared_n").addClass("active");
                            $("#publicPartage").attr("style",$("#publicPartage").attr("style")+";display:none;");
                        }

                        //List share user
                        $.ajax({
                            url:"/api/file/details/"+filepartageid+"/shares",
                            type:"GET",
                            headers:{
                                "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
                            },
                            success: function(result){
                                for(var i in result.data.shares){
                                    if(result.data.shares.hasOwnProperty(i)){
                                        var write = result.data.shares[i].is_writable==true?"All":"Lecture seule";
                                        $("#shareUsers tbody").append("\
                                            <tr data-shareid='"+result.data.shares[i].id+"'>\
                                                <td>"+result.data.shares[i].user.email+"</td>\
                                                <td>"+write+"</td>\
                                                <td><button class='btn btn-danger btn-xs supprimerShare'><span class='glyphicon glyphicon-trash'></span> Supprimer</button></td>\
                                            </tr>\
                                        ");
                                    }
                                }
                                $(".supprimerShare").click(function(){
                                    var parent = $(this).parent().parent();
                                    $.ajax({
                                    url:"/api/share/delete/"+$(this).parent().parent().attr("data-shareid"),
                                    type:"DELETE",
                                    headers:{
                                        "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
                                    },
                                    success: function(result) {
                                        if(result.error == false){
                                            $("div.resultShare").append('<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                                            $(parent).remove();
                                        }
                                        else{
                                            $("div.result").append('<div class="alert alert-warning fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                                        }
                                    }
                                })
                                });
                            }
                        });

                        


                        //Share With user
                        $("#shareUser").submit(function(e){
                           e.preventDefault();
                           $.ajax({
                                url:"/api/share/create",
                                type:"POST",
                                headers:{
                                    "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
                                },
                                data:{write:$("#permission").val(),email:$("#shareEmail").val(),file:$("#shareFile").val()},
                                success: function(result) {
                                    $("#loadingModal").modal("hide");
                                    if(result.error == false){
                                         $("div.resultShare").append('<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                                         document.location.href="/";
                                    }else{
                                         $("div.resultShare").append('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                                    }
                                }
                            })
                        });

                        // Click sur le bouton OUI ou NON
                        $("#shared_y").click(function(){
                            $("#loadingModal").modal("show");
                            $.ajax({
                                url:"/api/file/update/"+filepartageid,
                                type:"POST",
                                headers:{
                                    "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
                                },
                                data:{is_public:true},
                                success: function(result) {
                                    $("#loadingModal").modal("hide");
                                    if(result.error == false){
                                        $("#shareUrl").val("http://www.cubbyhole.name/api/file/download/"+filepartageid+"?accessKey="+result.data.file.access_key);
                                        $("#facebook").attr("href","https://www.facebook.com/sharer/sharer.php?u=http://www.cubbyhole.name/api/file/download/"+filepartageid+"?accessKey="+result.data.file.access_key);
                                        $("#twitter").attr("href","http://twitter.com/intent/tweet/?url=http://www.cubbyhole.name/api/file/download/"+filepartageid+"?accessKey="+result.data.file.access_key+"&text=Télécharge ce fichier depuis Cubbyhole !");
                                        $("#mail").attr("href","mailto:?subject=Cubbyhole&body=http://www.cubbyhole.name/api/file/download/"+filepartageid+"?accessKey="+result.data.file.access_key+"&text=Télécharge ce fichier depuis Cubbyhole !");
                                    }
                                }
                            })
                        });
                         $("#shared_n").click(function(){
                            $.ajax({
                                url:"/api/file/update/"+filepartageid,
                                type:"POST",
                                headers:{
                                    "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
                                },
                                data:{is_public:"0"},
                                success: function(result) {

                                }
                            })
                        });
                    });
                    //refresh after close modal
                    $('#partageModal').on('hide.bs.modal', function (e) {
                      $("#refresh").click();
                      $("#shareUsers tbody").html("");
                    })
                     //Click modal File edit
                     $("tr.file td div button.editer").click(function(){
                        $("#editFileModal").modal("show");
                        $("#edit_file_id").val($(this).parent().parent().parent().attr("data-id"));
                        var fn = $(this).attr("data-fname");
                        var ext = fn.substr((~-fn.lastIndexOf(".") >>> 0) + 2);
                        if (ext == "gz") ext = "tar.gz"
                        ext = "."+ext;
                        fn = fn.replace(ext, "");
                        $("#edit_file_ext").html(ext);
                        $("#edit_file_name").val(fn);
                    });
                     // Click supprimer
                     var rid;
                    $("button.supprimer").click(function(){
                            if($(this).parent().parent().parent().hasClass("file")){
                                rid = $(this).parent().parent().parent().attr('data-id');
                                var method = "file";
                            }
                            else if($(this).parent().parent().parent().hasClass("folder")){
                                rid = $(this).parent().parent().parent().attr('data-id');
                                var method = "folder";
                            }

                            $.ajax({
                                url: '/api/'+method+'/remove/'+rid,
                                type: 'DELETE',
                                headers:{
                                    "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
                                },
                                success: function(result) {
                                    if(result.error == false){
                                        $("div.result").append('<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                                        $("tr[data-id='"+rid+"']").remove();
                                    }
                                    else{
                                        $("div.result").append('<div class="alert alert-warning fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                                    }
                                },
                                error: function(result) {
                                        $("div.result").append('<p class="bg-danger" style="padding: 5px 0px;">Erreur lors du delete.</p>');
                                }
                            });
                    });
                });

            },
            error: function(result) {
                $(".panel").fadeOut();
                $("div.result").append('<p class="bg-danger" style="padding: 5px 0px;">Erreur lors de la transaction.</p>');
                $('#loadingModal').modal("hide");
            }
       });
}




function getFolder(id){
    $('#folder_id').val(id);
    $('#loadingModal').modal("show");
    $.ajax({
            url: '/api/folder/details/'+id,
            type: 'GET',
            headers:{
                "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
            },
            success: function(result) {
                $("#cubbyhole tbody").html("");
                if(result.error == false){
                    if($(".breadcrumb a[data-id='"+id+"']").length == 0){
                        $(".breadcrumb").append("<a data-id='"+result.data.folder.id+"' href='javascript:getFolder("+result.data.folder.id+")'> / "+result.data.folder.name+"</a>");
                    }
                    else{
                        var pos = $(".breadcrumb a").index($(".breadcrumb a[data-id='"+id+"']")); 
                        $(".breadcrumb a").slice(pos+1).remove();
                    }
                    var pos = $(".breadcrumb a").index($(".breadcrumb a[data-id='"+id+"']")); 
                    var parent = $(".breadcrumb a").slice(pos-1);
                    if($(parent).attr("data-id") == undefined){
                        funct = "getRoot";
                        parent_id = '';
                    }
                    else{
                        funct = "getFolder";
                        parent_id = $(parent).attr("data-id");
                    }
                    $("#cubbyhole tbody").append('\
                        <tr class="parentFolder" data-id="null">\
                            <td><a href="javascript:'+funct+'('+parent_id+')"><span class="glyphicon glyphicon-backward"></span> &nbsp; ...</a></td>\
                            <td data-value="a">Dossier Parent</td>\
                            <td data-value="0">--</td>\
                            <td style="width:175px;"></td>\
                        </tr>\
                    ').fadeIn();
                    for(var loop_folder in result.data.folder.folders){
                        if(result.data.folder.folders.hasOwnProperty(loop_folder)){
                            if(result.data.folder.folders[loop_folder].share == null){
                                sprite = "dossier";
                                type = "Dossier";
                            }
                            else{
                                sprite = "dossierPartage";
                                type = "Dossier Partagé";
                            }
                            $("#cubbyhole tbody").append('\
                                <tr class="folder" data-id="'+result.data.folder.folders[loop_folder].id+'">\
                                    <td class="drag"><a href="javascript:getFolder('+result.data.folder.folders[loop_folder].id+')"><span class="sprite '+sprite+'"></span>'+result.data.folder.folders[loop_folder].name+'</a></td>\
                                    <td class="drag">'+type+'</td>\
                                    <td class="drag">'+result.data.folder.folders[loop_folder].last_update_date.date+'</td>\
                                    <td style="width:175px;"><div style="display:none">\
                                        <button type="button" class="btn btn-xs btn-warning partager"><span class="glyphicon glyphicon-link"></span> Partager</button>\
                                        <button type="button" class="btn btn-xs btn-info editer" data-loading-text="Loading..." data-fname="'+result.data.folder.folders[loop_folder].name+'"><span class="glyphicon glyphicon-pencil"></span> Editer</button> \
                                        <button type="button" class="btn btn-xs btn-danger supprimer"><span class="glyphicon glyphicon-trash"></span> Supprimer</button></div>\
                                    </td>\
                                </tr>\
                            ').fadeIn();
                        }
                    }
                    for(var loop_file in result.data.folder.files){
                        var extension = result.data.folder.files[loop_file].absolute_path;
                        extension = extension.substr((~-extension.lastIndexOf(".") >>> 0) + 2);
                        var sprite;
                        switch(extension.toLowerCase()){
                            case "csv": 
                            case "xls": 
                            case "xlsx": sprite = "excel"; type = "Classeur";
                            break;
                            case "png": 
                            case "jpg": 
                            case "gif": 
                            case "psd": 
                            case "jpeg": sprite = "picture"; type = "Image";
                            break;
                            case "ppt": 
                            case "pptx": sprite = "powerpoint"; type = "Présentation";
                            break;
                            case "pdf": sprite = "pdf"; type = "PDF";
                            break;
                            case "doc":
                            case "docx": sprite = "word"; type = "Document";
                            break;
                            case "zip":
                            case "rar":
                            case "tar":
                            case "tar.gz":
                            case "gzip": sprite = "zip"; type = "Archive";
                            break;
                            default: sprite = "file"; type = "Fichier";
                            break;
                        }
                        result.data.folder.files[loop_file].share != null?type += " Partagé":"";
                        if(result.data.folder.files.hasOwnProperty(loop_file)){
                            $("#cubbyhole tbody").append('\
                                <tr class="file" data-id="'+result.data.folder.files[loop_file].id+'">\
                                    <td class="drag"><a href="javascript:getFile('+result.data.folder.files[loop_file].id+')"><span class="fileSprite '+sprite+'"></span>'+result.data.folder.files[loop_file].name+'</a></td>\
                                    <td class="drag">'+type+'</td>\
                                    <td class="drag">'+result.data.folder.files[loop_file].last_update_date.date+'</td>\
                                    <td style="width:235px;"><div style="display:none">\
                                        <button type="button" class="btn btn-xs btn-warning partager"><span class="glyphicon glyphicon-link"></span> Partager</button>\
                                        <button type="button" class="btn btn-xs btn-info editer" data-loading-text="Loading..." data-fname="'+result.data.folder.files[loop_file].name+'"><span class="glyphicon glyphicon-pencil"></span> Editer</button> \
                                        <button type="button" class="btn btn-xs btn-danger supprimer"><span class="glyphicon glyphicon-trash"></span> Supprimer</button></div>\
                                    </td>\
                                </tr>\
                            ').fadeIn();
                        }
                    }
                    $('#loadingModal').modal("hide");
                    $.bootstrapSortable();
                }
                else{
                    $('#loadingModal').modal("hide");
                    $("div.result").append('<div class="alert alert-warning fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                }
                $("#cubbyhole tbody tr").hover(function(){
                    $(this).find('td > div').css("display","inline-block");
                },function(){
                    $(this).find('td > div').css("display","none");
                });
 $(document).ready(function(){
                    var filepartageid,filepartagekey;
                    // Click modal Folder edit
                     $("tr.folder td div button.editer").click(function(){
                        $("#editFolderModal").modal("show");
                        $("#edit_folder_id").val($(this).parent().parent().parent().attr("data-id"));
                        $("#edit_folder_name").val($(this).attr("data-fname"));
                    });
                     //Click modal partage
                    $("button.partagerFolder").click(function(){
                        folderpartageid = $(this).parent().parent().parent().attr("data-id");
                        $("#shareFolder").val(folderpartageid); 
                        $("#sharewithall").css("display","none");
                        $("#partageModal").modal("show");


                        //List share user
                        $.ajax({
                            url:"/api/folder/details/"+folderpartageid+"/shares",
                            type:"GET",
                            headers:{
                                "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
                            },
                            success: function(result){
                                for(var i in result.data.shares){
                                    if(result.data.shares.hasOwnProperty(i)){
                                        var write = result.data.shares[i].is_writable==true?"All":"Lecture seule";
                                        $("#shareUsers tbody").append("\
                                            <tr data-shareid='"+result.data.shares[i].id+"'>\
                                                <td>"+result.data.shares[i].user.email+"</td>\
                                                <td>"+write+"</td>\
                                                <td><button class='btn btn-danger btn-xs supprimerShare'><span class='glyphicon glyphicon-trash'></span> Supprimer</button></td>\
                                            </tr>\
                                        ");
                                    }
                                }
                                $(".supprimerShare").click(function(){
                                    var parent = $(this).parent().parent();
                                    $.ajax({
                                    url:"/api/share/delete/"+$(this).parent().parent().attr("data-shareid"),
                                    type:"DELETE",
                                    headers:{
                                        "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
                                    },
                                    success: function(result) {
                                        if(result.error == false){
                                            $("div.resultShare").append('<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                                            $(parent).remove();
                                        }
                                        else{
                                            $("div.result").append('<div class="alert alert-warning fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                                        }
                                    }
                                })
                                });
                            }
                        });

                        


                        //Share With user
                        $("#shareUser").submit(function(e){
                           e.preventDefault();
                           $.ajax({
                                url:"/api/share/create",
                                type:"POST",
                                headers:{
                                    "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
                                },
                                data:{write:$("#permission").val(),email:$("#shareEmail").val(),folder:$("#shareFolder").val()},
                                success: function(result) {
                                    $("#loadingModal").modal("hide");
                                    if(result.error == false){
                                         $("div.resultShare").append('<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                                         document.location.href="/";
                                    }else{
                                         $("div.resultShare").append('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                                    }
                                }
                            })
                        });


                    });

                     $("button.partager").click(function(){
                        filepartageid = $(this).parent().parent().parent().attr("data-id");
                        filepartagekey = $(this).parent().parent().parent().attr("data-key");
                        $("#sharewithall").css("display","block");
                        $("#shareFile").val(filepartageid);
                        $("#shared_y, #shared_n").removeClass("active");
                        $("#partageModal").modal("show");
                        if($(this).parent().parent().parent().hasClass("shared")){
                            $("#shared_y").addClass("active");
                            $("#publicPartage").fadeIn();
                            $("#shareUrl").val("http://www.cubbyhole.name/api/file/download/"+filepartageid+"?accessKey="+filepartagekey);
                            $("#facebook").attr("href","https://www.facebook.com/sharer/sharer.php?u=http://www.cubbyhole.name/api/file/download/"+filepartageid+"?accessKey="+filepartagekey);
                            $("#twitter").attr("href","http://twitter.com/intent/tweet/?url=http://www.cubbyhole.name/api/file/download/"+filepartageid+"?accessKey="+filepartagekey+"&text=Télécharge ce fichier depuis Cubbyhole !");
                            $("#mail").attr("href","mailto:?subject=Cubbyhole&body=http://www.cubbyhole.name/api/file/download/"+filepartageid+"?accessKey="+filepartagekey+"&text=Télécharge ce fichier depuis Cubbyhole !");
                        }
                        else {
                            $("#shared_n").addClass("active");
                            $("#publicPartage").attr("style",$("#publicPartage").attr("style")+";display:none;");
                        }

                        //List share user
                        $.ajax({
                            url:"/api/file/details/"+filepartageid+"/shares",
                            type:"GET",
                            headers:{
                                "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
                            },
                            success: function(result){
                                for(var i in result.data.shares){
                                    if(result.data.shares.hasOwnProperty(i)){
                                        var write = result.data.shares[i].is_writable==true?"All":"Lecture seule";
                                        $("#shareUsers tbody").append("\
                                            <tr data-shareid='"+result.data.shares[i].id+"'>\
                                                <td>"+result.data.shares[i].user.email+"</td>\
                                                <td>"+write+"</td>\
                                                <td><button class='btn btn-danger btn-xs supprimerShare'><span class='glyphicon glyphicon-trash'></span> Supprimer</button></td>\
                                            </tr>\
                                        ");
                                    }
                                }
                                $(".supprimerShare").click(function(){
                                    var parent = $(this).parent().parent();
                                    $.ajax({
                                    url:"/api/share/delete/"+$(this).parent().parent().attr("data-shareid"),
                                    type:"DELETE",
                                    headers:{
                                        "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
                                    },
                                    success: function(result) {
                                        if(result.error == false){
                                            $("div.resultShare").append('<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                                            $(parent).remove();
                                        }
                                        else{
                                            $("div.result").append('<div class="alert alert-warning fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                                        }
                                    }
                                })
                                });
                            }
                        });

                        


                        //Share With user
                        $("#shareUser").submit(function(e){
                           e.preventDefault();
                           $.ajax({
                                url:"/api/share/create",
                                type:"POST",
                                headers:{
                                    "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
                                },
                                data:{write:$("#permission").val(),email:$("#shareEmail").val(),file:$("#shareFile").val()},
                                success: function(result) {
                                    $("#loadingModal").modal("hide");
                                    if(result.error == false){
                                         $("div.resultShare").append('<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                                         document.location.href="/";
                                    }else{
                                         $("div.resultShare").append('<div class="alert alert-danger fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                                    }
                                }
                            })
                        });

                        // Click sur le bouton OUI ou NON
                        $("#shared_y").click(function(){
                            $("#loadingModal").modal("show");
                            $.ajax({
                                url:"/api/file/update/"+filepartageid,
                                type:"POST",
                                headers:{
                                    "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
                                },
                                data:{is_public:true},
                                success: function(result) {
                                    $("#loadingModal").modal("hide");
                                    if(result.error == false){
                                        $("#shareUrl").val("http://www.cubbyhole.name/api/file/download/"+filepartageid+"?accessKey="+result.data.file.access_key);
                                        $("#facebook").attr("href","https://www.facebook.com/sharer/sharer.php?u=http://www.cubbyhole.name/api/file/download/"+filepartageid+"?accessKey="+result.data.file.access_key);
                                        $("#twitter").attr("href","http://twitter.com/intent/tweet/?url=http://www.cubbyhole.name/api/file/download/"+filepartageid+"?accessKey="+result.data.file.access_key+"&text=Télécharge ce fichier depuis Cubbyhole !");
                                        $("#mail").attr("href","mailto:?subject=Cubbyhole&body=http://www.cubbyhole.name/api/file/download/"+filepartageid+"?accessKey="+result.data.file.access_key+"&text=Télécharge ce fichier depuis Cubbyhole !");
                                    }
                                }
                            })
                        });
                         $("#shared_n").click(function(){
                            $.ajax({
                                url:"/api/file/update/"+filepartageid,
                                type:"POST",
                                headers:{
                                    "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
                                },
                                data:{is_public:"0"},
                                success: function(result) {

                                }
                            })
                        });
                    });
                    //refresh after close modal
                    $('#partageModal').on('hide.bs.modal', function (e) {
                      $("#refresh").click();
                      $("#shareUsers tbody").html("");
                    })
                     //Click modal File edit
                     $("tr.file td div button.editer").click(function(){
                        $("#editFileModal").modal("show");
                        $("#edit_file_id").val($(this).parent().parent().parent().attr("data-id"));
                        var fn = $(this).attr("data-fname");
                        var ext = fn.substr((~-fn.lastIndexOf(".") >>> 0) + 2);
                        if (ext == "gz") ext = "tar.gz"
                        ext = "."+ext;
                        fn = fn.replace(ext, "");
                        $("#edit_file_ext").html(ext);
                        $("#edit_file_name").val(fn);
                    });
                     // Click supprimer
                     var rid;
                    $("button.supprimer").click(function(){
                            if($(this).parent().parent().parent().hasClass("file")){
                                rid = $(this).parent().parent().parent().attr('data-id');
                                var method = "file";
                            }
                            else if($(this).parent().parent().parent().hasClass("folder")){
                                rid = $(this).parent().parent().parent().attr('data-id');
                                var method = "folder";
                            }

                            $.ajax({
                                url: '/api/'+method+'/remove/'+rid,
                                type: 'DELETE',
                                headers:{
                                    "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
                                },
                                success: function(result) {
                                    if(result.error == false){
                                        $("div.result").append('<div class="alert alert-success fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                                        $("tr[data-id='"+rid+"']").remove();
                                    }
                                    else{
                                        $("div.result").append('<div class="alert alert-warning fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'+result["message"]+'</div>');
                                    }
                                },
                                error: function(result) {
                                        $("div.result").append('<p class="bg-danger" style="padding: 5px 0px;">Erreur lors du delete.</p>');
                                }
                            });
                    });
                });

            },
            error: function(result) {
                $(".panel").fadeOut();
                $("div.result").append('<p class="bg-danger" style="padding: 5px 0px;">Erreur lors de la transaction.</p>');
                $('#loadingModal').modal("hide");
            }
       });
}
</script>