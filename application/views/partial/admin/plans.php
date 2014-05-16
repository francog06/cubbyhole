            <div class="inner cover admin">
               <h1>Gestion plans</h1>
               <div class="result"></div>
               <button class="btn btn-success" id="create">Créer un plan</button>
               <br /><br />
               <table class="table table-striped">
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Espace</th>
                    <th>Prix</th>
                    <th>Bandwidth</th>
                    <th>Défaut</th>
                    <th>Actif</th>
                    <th>Actions</th>
                </tr>

               <?php 
               echo empty($plans)?"<tr><td colspan='5' style='text-align:center'>Aucun utilisateur en base</td></tr>":"";
               foreach ($plans as $plan): ?>
                <tr>
                    <td><?php echo $plan["id"]; ?></td>
                    <td><?php echo $plan["name"]; ?></td>
                    <td><?php echo $plan["usable_storage_space"]; ?> Go</td>
                    <td><?php echo $plan["price"]; ?> €</td>
                    <td><?php echo $plan["max_bandwidth"]; ?> kB/s</td>
                    <td><?php echo $plan["is_default"]==true?"Oui":"Non"; ?></td>
                    <td><?php echo $plan["is_active"]==true?"Oui":"Non"; ?></td>
                    <td id="<?php echo $plan["id"]; ?>">
                        <button type="button" class="btn btn-xs btn-info editer" data-loading-text="Loading..."><span class="glyphicon glyphicon-pencil"></span>&nbsp; Editer</button> 
                         &nbsp; 
                        <button type="button" class="btn btn-xs btn-danger supprimer"><span class="glyphicon glyphicon-trash"></span>&nbsp; Supprimer</button>
                    </td>
                </tr>
               <?php endforeach; ?>
               </table>

               
            </div>

        
<script type="text/javascript">
    $("button#create").click(function(){
        $("#editplan").modal("show"); 7
    }); 
    var deleteencours;
    $("button.supprimer").click(function(){
        if(deleteencours == true){
            alert("Doucement garçon... un edit à la fois !");
        }
        else{
            deleteencours = true;
            var planId = $(this).parent().attr('id');
            $.ajax({
                url: '/api/plan/delete/'+planId,
                type: 'DELETE',
                headers:{
                    "X-API-KEY":"5422e102a743fd70a22ee4ff7c2ebbe8"
                },
                success: function(result) {
                    if(result.error == false){
                        $("div.result").append('<p class="bg-success" style="padding: 5px 0px;">'+result["message"]+'</p>');
                        $("td#"+planId).parent().remove();
                    }
                    else{
                        $("div.result").append('<p class="bg-danger" style="padding: 5px 0px;">'+result["message"]+'</p>');
                    }
                    deleteencours = false;
                },
                error: function(result) {
                        $("div.result").append('<p class="bg-danger" style="padding: 5px 0px;">Erreur lors du delete.</p>');
                        deleteencours = false;
                }
            });
        }
    });
    var editencours;
    $("button.editer").click(function(){
        if(editencours == true){
            alert("Doucement garçon... un edit à la fois !");
        }
        else{
            editencours = true;
            var btn = $(this);
            btn.button('loading');
            var planId = $(this).parent().attr('id');
            $.ajax({
                url: '/api/plan/details/'+planId,
                type: 'GET',
                headers:{
                    "X-API-KEY":"5422e102a743fd70a22ee4ff7c2ebbe8"
                },
                success: function(result) {
                    if(result.data.plan.id > 0){
                        $("#plan_id").val(result.data.plan.id);
                        $("#plan_name").val(result.data.plan.name);
                        $("#plan_price").val(result.data.plan.price);
                        $("#plan_duration").val(result.data.plan.duration);
                        $("#plan_description").val(result.data.plan.description);
                        $("#plan_bandwidth").val(result.data.plan.max_bandwidth);
                        $("#plan_storage").val(result.data.plan.usable_storage_space);
                        $("#plan_daily").val(result.data.plan.daily_data_transfert);
                        if(result.data.plan.is_active == true){
                            $("#plan_is_active_yes").prop("checked", true);
                            $("#plan_is_active_no").prop("checked", false);
                        }
                        else{
                            $("#plan_is_active_yes").prop("checked", false);
                            $("#plan_is_active_no").prop("checked", true);
                        }
                       if(result.data.plan.is_default == true){
                            $("#plan_is_default_yes").prop("checked", true);
                            $("#plan_is_default_no").prop("checked", false);
                        }
                        else{
                            $("#plan_is_default_yes").prop("checked", false);
                            $("#plan_is_default_no").prop("checked", true);
                        }
                        $("#editplan").modal("show");  
                        
                    }
                    else alert("bug api");
                }
            }).always(function() {
              btn.button('reset');
               editencours = false;
            });
        }
    });
</script>

<!-- Modal edit plan -->
<div class="modal fade" id="editplan" tabindex="-1" role="dialog" aria-labelledby="Edit plan" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Edit plan </h4>
      </div>
      <form class="form-horizontal" role="form" method="post" id="formEditplan">
        <input type="hidden" id="plan_id" name="plan_id" value="" />
          <div class="modal-body">
              <div class="form-group">
                <label for="plan_" class="col-sm-2 control-label">Nom</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="plan_name" placeholder="" name="plan_name" required />
                </div>
              </div>
              <label for="plan_" class="col-sm-2 control-label">Prix</label>
              <div class="input-group">
                  <input type="text" class="form-control" id="plan_price" placeholder="" name="plan_price" required />
                  <span class="input-group-addon">€</span>
              </div>
              <label for="plan_" class="col-sm-2 control-label">Durée</label>
              <div class="input-group">
                  <input type="text" class="form-control" id="plan_duration" placeholder="" name="plan_duration" required />
                  <span class="input-group-addon">jours</span>
              </div>
              <label for="plan_" class="col-sm-2 control-label">Espace</label>
              <div class="input-group">
                  <input type="text" class="form-control" id="plan_storage" placeholder="" name="plan_storage" required />
                  <span class="input-group-addon">Go</span>
              </div>
              <label for="plan_" class="col-sm-2 control-label">Bandwidth</label>
              <div class="input-group">
                  <input type="text" class="form-control" id="plan_bandwidth" placeholder="" name="plan_bandwidth" required />
                  <span class="input-group-addon">kB/s</span>
              </div>
              <label for="plan_" class="col-sm-2 control-label">Data/jour</label>
              <div class="input-group">
                  <input type="text" class="form-control" id="plan_daily" placeholder="" name="plan_daily" required />
                  <span class="input-group-addon">Mo</span>
              </div>
              <div class="form-group">
                <label for="plan_" class="col-sm-2 control-label">Description</label>
                <div class="col-sm-10">
                  <input type="text" class="form-control" id="plan_description" placeholder="" name="plan_description" required />
                </div>
              </div>
              <div class="form-group ">
                <label for="plan_password" class="col-sm-2 control-label">Défaut ?</label>
                <div class="col-sm-10">
                    <label class="radio-inline">
                      <input type="radio" id="plan_is_default_yes" name="plan_is_default" value="1"> Oui
                    </label>
                    <label class="radio-inline">
                      <input type="radio" id="plan_is_default_no" name="plan_is_default" value="0"> Non
                    </label>
                </div>
              </div>
              <div class="form-group">
                <label for="plan_password" class="col-sm-2 control-label">Actif ?</label>
                <div class="col-sm-10">
                    <label class="radio-inline">
                      <input type="radio" id="plan_is_active_yes" name="plan_is_active" value="1"> Oui
                    </label>
                    <label class="radio-inline">
                      <input type="radio" id="plan_is_active_no" name="plan_is_active" value="0"> Non
                    </label>
                </div>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary" id="submitEditplan" data-loading-text="Loading...">Enregistrer</button>
          </div>
      </form>
      <script type="text/javascript">
      $("form").submit(function(e){
        e.preventDefault();
        if($("#plan_id").val() != ""){
        var btn = $("#submitEditplan");
        btn.button('loading');
        $.ajax({
                url: '/api/plan/update/'+$("#plan_id").val(),
                type: 'PUT',
                headers:{
                    "X-API-KEY":"5422e102a743fd70a22ee4ff7c2ebbe8",
                    "Content-Type":"application/x-www-form-urlencoded"
                },
                data:{name:$("#plan_name").val(),price:$("#plan_price").val(),duration:$("#plan_duration").val(),usable_storage_space:$("#plan_storage").val(),max_bandwidth:$("#plan_bandwidth").val(),daily_data_transfert:$("#plan_daily").val(),description:$("#plan_description").val(), is_default:$("input[name=plan_is_default]:checked").val(), is_active:$("input[name=plan_is_active]:checked").val()},
                success: function(result) {
                    if(result.error == false){
                        $("div.result").append('<p class="bg-success" style="padding: 5px 0px;">'+result["message"]+'</p>');
                    }else{
                        $("div.result").append('<p class="bg-danger" style="padding: 5px 0px;">'+result["message"]+'</p>');
                    }
                    $("#editplan").modal("hide");
                },
                 error: function(result) {
                        $("div.result").append('<p class="bg-danger" style="padding: 5px 0px;">Erreur lors de l\'edit.</p>');
                        $("#editplan").modal("hide");
                }
        }).done(function(){
            setTimeout(function() {
                location.reload();
            }, 3000);
        }).always(function() {
              btn.button('reset');
               editencours = false;
            });
        }
        else {
            $.ajax({
                url: '/api/plan/create/',
                type: 'POST',
                headers:{
                    "X-API-KEY":"5422e102a743fd70a22ee4ff7c2ebbe8",
                },
                data:{name:$("#plan_name").val(),price:$("#plan_price").val(),duration:$("#plan_duration").val(),usable_storage_space:$("#plan_storage").val(),max_bandwidth:$("#plan_bandwidth").val(),daily_data_transfert:$("#plan_daily").val(),description:$("#plan_description").val(), is_default:$("input[name=plan_is_default]:checked").val(), is_active:$("input[name=plan_is_active]:checked").val()},
                success: function(result) {
                    if(result.error == false){
                        $("div.result").append('<p class="bg-success" style="padding: 5px 0px;">'+result["message"]+'</p>');
                    }else{
                        $("div.result").append('<p class="bg-danger" style="padding: 5px 0px;">'+result["message"]+'</p>');
                    }
                    $("#editplan").modal("hide");
                },
                 error: function(result) {
                        $("div.result").append('<p class="bg-danger" style="padding: 5px 0px;">Erreur lors de l\'edit.</p>');
                        $("#editplan").modal("hide");
                }
            });
        }
      });
      </script>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->