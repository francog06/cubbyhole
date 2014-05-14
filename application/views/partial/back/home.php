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
      <form class="form-horizontal" role="form" method="post" id="formEditUser">
        <input type="hidden" id="user_id" name="user_id" value="" />
        <input type="hidden" id="folder_id" name="folder_id" value="" />
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

<!-- Modal folder upload -->
<div class="modal fade" id="newFolderModal" tabindex="-1" role="dialog" aria-labelledby="Nouveau dossier" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Nouveau Dossier</h4>
      </div>
      <form class="form-horizontal" role="form" method="post" id="formEditUser">
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
    var user_id = <?= $user->getId(); ?>;
    var sprite,type;
    $(document).ready(function(){    
        getRoot();
        // Hover actions tableau
        $(".table tbody tr").hover(function(){
            $(this).find('td > div').css("display","inline-block");
        },function(){
            $(this).find('td > div').css("display","none");
        });
        $.bootstrapSortable();
        $("#refresh").click(function(){
            if($(".breadcrumb a:last-child").attr("data-id") == undefined){
                getRoot();
            }else{
                getFolder($(".breadcrumb a:last-child").attr("data-id"));
            }
        });
        $("#submitNewFolder").click(function(e){
            e.preventDefault();
            $.ajax({
                url: '/api/folder/add',
                type: 'POST',
                headers:{
                    "X-API-KEY":"5422e102a743fd70a22ee4ff7c2ebbe8"
                },
                data:{folder_id:$("#folder_id").val(),user_id:user_id,name:$("#folder_name").val()},
                success: function(result) {
                    if(result.error == false){
                       $('#newFolderModal').modal("toggle");
                       $("div.result").append('<p class="bg-success" style="padding: 5px 0px;">'+result["message"]+'</p>');
                    }
                    else{
                        $("div.result").append('<p class="bg-danger" style="padding: 5px 0px;">'+result["message"]+'</p>');
                        $('#newFolderModal').modal("toggle");
                    }
                },
                error: function(result) {
                    $("div.result").append('<p class="bg-danger" style="padding: 5px 0px;">Erreur lors de la transaction.</p>');
                    $('#newFolderModal').modal("toggle");
                }
           });
        });
        
    });
    </script>
<script type="text/javascript" src="http://interactjs.io/js/interact.min.js"></script>
<script type="text/javascript">
    var obj = $("#dragandrophandler");
    var user_id = <?= $user->getId(); ?>;

    interact('.folder')
    // enable draggables to be dropped into this
    .dropzone(true)
    // only accept elements matching this CSS selector
    .accept('.file')
    .accept('.folder')
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
        if($(event.relatedTarget).attr("class") == "file"){
            $.ajax({
                url: '/api/file/update/'+$(event.relatedTarget).attr("data-id"),
                type: 'POST',
                headers:{
                    "X-API-KEY":"5422e102a743fd70a22ee4ff7c2ebbe8"
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
        else if($(event.relatedTarget).attr("class") == "folder"){
            $.ajax({
                url: '/api/folder/update/'+$(event.relatedTarget).attr("data-id"),
                type: 'PUT',
                headers:{
                    "X-API-KEY":"5422e102a743fd70a22ee4ff7c2ebbe8"
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


    interact('.file')
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
    .inertia(true)
    .restrict({ drag: 'parent' });

    interact('.folder')
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
    .inertia(true)
    .restrict({ drag: 'parent' });



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
                "X-API-KEY":"5422e102a743fd70a22ee4ff7c2ebbe8"
            },
            success: function(data){
                status.setProgress(100);
                $("#status1").append("File upload Done<br>");       
                $("#newFileModal").modal("toggle");
                 if($(".breadcrumb a:last-child").attr("data-id") == undefined){
                    getRoot();
                }else{
                    getFolder($(".breadcrumb a:last-child").attr("data-id"));
                }    
            }
        }); 
     
        status.setAbort(jqXHR);
}

function getRoot(){
    $('#folder_id').val("");
    $('#loadingModal').modal("toggle");
    $.ajax({
            url: '/api/folder/user/'+user_id+'/root',
            type: 'GET',
            headers:{
                "X-API-KEY":"5422e102a743fd70a22ee4ff7c2ebbe8"
            },
            success: function(result) {
                $("#cubbyhole tbody").html("");
                if(result.error == false){
                    $(".breadcrumb").html("<a href='javascript:getRoot()'><span class='glyphicon glyphicon-home'></span></a>");
                    for(var loop_folder in result.folders){
                        if(result.folders.hasOwnProperty(loop_folder)){
                            if(result.folders[loop_folder].share == null){
                                sprite = "dossier";
                                type = "Dossier";
                            }
                            else{
                                sprite = "dossierPartage";
                                type = "Dossier Partagé";
                            }
                            $("#cubbyhole tbody").append('\
                                <tr class="folder" data-id="'+result.folders[loop_folder].id+'">\
                                    <td><a href="javascript:getFolder('+result.folders[loop_folder].id+')"><span class="sprite '+sprite+'"></span>'+result.folders[loop_folder].name+'</a></td>\
                                    <td>'+type+'</td>\
                                    <td>'+result.folders[loop_folder].last_update_date.date+'</td>\
                                    <td style="width:175px;"><div style="display:none">\
                                        <button type="button" class="btn btn-xs btn-info editer" data-loading-text="Loading..."><span class="glyphicon glyphicon-pencil"></span>&nbsp; Editer</button> \
                                         &nbsp; \
                                        <button type="button" class="btn btn-xs btn-danger supprimer"><span class="glyphicon glyphicon-trash"></span>&nbsp; Supprimer</button></div>\
                                    </td>\
                                </tr>\
                            ').fadeIn();
                        }
                    }
                    for(var loop_file in result.files){
                        if(result.files[loop_file].share == null){
                                sprite = "file";
                                type = "Fichier";
                        }
                        else{
                            sprite = "filePartage";
                            type = "Fichier Partagé";
                        }
                        if(result.files.hasOwnProperty(loop_file)){
                            $("#cubbyhole tbody").append('\
                                <tr class="file" data-id="'+result.files[loop_file].id+'">\
                                    <td><span class="sprite '+sprite+'"></span>'+result.files[loop_file].name+'</td>\
                                    <td>'+type+'</td>\
                                    <td>'+result.files[loop_file].last_update_date.date+'</td>\
                                    <td style="width:175px;"><div style="display:none">\
                                        <button type="button" class="btn btn-xs btn-info editer" data-loading-text="Loading..."><span class="glyphicon glyphicon-pencil"></span>&nbsp; Editer</button> \
                                         &nbsp; \
                                        <button type="button" class="btn btn-xs btn-danger supprimer"><span class="glyphicon glyphicon-trash"></span>&nbsp; Supprimer</button></div>\
                                    </td>\
                                </tr>\
                            ').fadeIn();
                        }
                    }
                    $('#loadingModal').modal("toggle");
                    $.bootstrapSortable();
                }
                else{
                    $("div.result").append('<p class="bg-danger" style="padding: 5px 0px;">'+result["message"]+'</p>');
                    $('#loadingModal').modal("toggle");
                }
            },
            error: function(result) {
                $(".panel").fadeOut();
                $("div.result").append('<p class="bg-danger" style="padding: 5px 0px;">Erreur lors de la transaction.</p>');
                $('#loadingModal').modal("toggle");
            }
       });
}

function getFolder(id){
    $('#folder_id').val(id);
    $('#loadingModal').modal("toggle");
    $.ajax({
            url: '/api/folder/details/'+id,
            type: 'GET',
            headers:{
                "X-API-KEY":"5422e102a743fd70a22ee4ff7c2ebbe8"
            },
            success: function(result) {
                $("#cubbyhole tbody").html("");
                if(result.error == false){
                    if($(".breadcrumb a[data-id='"+id+"']").length == 0){
                        $(".breadcrumb").append("<a data-id='"+result.folder.id+"' href='javascript:getFolder("+result.folder.id+")'> / "+result.folder.name+"</a>");
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
                        <tr data-id="'+$(parent).attr("data-id")+'">\
                            <td><a href="javascript:'+funct+'('+parent_id+')"><span class="glyphicon glyphicon-backward"></span> &nbsp; ...</a></td>\
                            <td data-value="a">Dossier Parent</td>\
                            <td data-value="0">--</td>\
                            <td style="width:175px;"></td>\
                        </tr>\
                    ').fadeIn();
                    for(var loop_folder in result.folder.folders){
                        if(result.folder.folders.hasOwnProperty(loop_folder)){
                            if(result.folder.folders[loop_folder].share == null){
                                sprite = "dossier";
                                type = "Dossier";
                            }
                            else{
                                sprite = "dossierPartage";
                                type = "Dossier Partagé";
                            }
                            $("#cubbyhole tbody").append('\
                                <tr class="folder" data-id="'+result.folder.folders[loop_folder].id+'">\
                                    <td><a href="javascript:getFolder('+result.folder.folders[loop_folder].id+')"><span class="sprite '+sprite+'"></span>'+result.folder.folders[loop_folder].name+'</a></td>\
                                    <td>'+type+'</td>\
                                    <td>'+result.folder.folders[loop_folder].last_update_date.date+'</td>\
                                    <td style="width:175px;"><div style="display:none">\
                                        <button type="button" class="btn btn-xs btn-info editer" data-loading-text="Loading..."><span class="glyphicon glyphicon-pencil"></span>&nbsp; Editer</button> \
                                         &nbsp; \
                                        <button type="button" class="btn btn-xs btn-danger supprimer"><span class="glyphicon glyphicon-trash"></span>&nbsp; Supprimer</button></div>\
                                    </td>\
                                </tr>\
                            ').fadeIn();
                        }
                    }
                    for(var loop_file in result.folder.files){
                        if(result.folder.files[loop_file].share == null){
                                sprite = "file";
                                type = "Fichier";
                        }
                        else{
                            sprite = "filePartage";
                            type = "Fichier Partagé";
                        }
                        if(result.folder.files.hasOwnProperty(loop_file)){
                            $("#cubbyhole tbody").append('\
                                <tr class="file" data-id="'+result.folder.files[loop_file].id+'">\
                                    <td><span class="sprite '+sprite+'"></span>'+result.folder.files[loop_file].name+'</td>\
                                    <td>'+type+'</td>\
                                    <td>'+result.folder.files[loop_file].last_update_date.date+'</td>\
                                    <td style="width:175px;"><div style="display:none">\
                                        <button type="button" class="btn btn-xs btn-info editer" data-loading-text="Loading..."><span class="glyphicon glyphicon-pencil"></span>&nbsp; Editer</button> \
                                         &nbsp; \
                                        <button type="button" class="btn btn-xs btn-danger supprimer"><span class="glyphicon glyphicon-trash"></span>&nbsp; Supprimer</button></div>\
                                    </td>\
                                </tr>\
                            ').fadeIn();
                        }
                    }
                    $('#loadingModal').modal("toggle");
                    $.bootstrapSortable();
                }
                else{
                    $('#loadingModal').modal("toggle");
                    $("div.result").append('<p class="bg-danger" style="padding: 5px 0px;">'+result["message"]+'</p>');
                }
            },
            error: function(result) {
                $(".panel").fadeOut();
                $("div.result").append('<p class="bg-danger" style="padding: 5px 0px;">Erreur lors de la transaction.</p>');
                $('#loadingModal').modal("toggle");
            }
       });
}
</script>