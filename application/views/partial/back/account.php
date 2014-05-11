<div class="inner cover admin">
  <h1>Mon compte</h1>
  <div class="well" style="width:50%;float:left;margin-right:5%">
    <h4>Editer mes informations </h4>
    <br>
    <div id="result"></div>
    <form class="form-horizontal" role="form" method="post">
      <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label" style="text-align:left;">Email</label>
        <div class="col-sm-10">
          <input type="email" class="form-control" id="inputEmail3" placeholder="<?= $user->getEmail(); ?>">
        </div>
      </div>
      <div class="form-group">
        <label for="inputPassword3" class="col-sm-2 control-label" style="text-align:left;">Password</label>
        <div class="col-sm-10">
          <input type="password" class="form-control" id="inputPassword3" placeholder="Password">
        </div>
      </div>
      <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
          <button id="submit_account" type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
      </div>
    </form>
  </div>
  <div class="well" style="width:40%;float:left;">
  <h4>Autres informations</h4>
    <br>
    <ul style="text-align:left;">
      <li>Vous êtes isncrit de puis le <b><?= $user->getRegistrationDate()->format("d/m/Y"); ?></b></li>
      <li>Plan actuel : <?= $user_plan->getPlan()->getName(); ?> (<a href="/user/upgrade/">upgrader mon compte</a>)</li>
      <li>Expire le : <b><?= $user_plan->getExpirationPlanDate()->format("d/m/Y");; ?></b> (<i><?php $date = new DateTime("now"); echo $date->diff($user_plan->getExpirationPlanDate())->format("%a"); ?> jours restants</i>)</li>
    </ul>
  </div>
</div>
<script type="text/javascript">
  $("#submit_account").click(function(e){
      e.preventDefault();
      var email;
      var password;
      if($("inputEmail3").val() == "<?= $user->getEmail(); ?>" || $("inputEmail3").val() == "")
        email = false;
      else
        email = $("inputEmail3").val();

      if($("inputPassword3").val() == "<?= $user->getPassword(); ?>" || $("inputPassword3").val() == "" || $("inputPassword3").val() == "Password")
        password = false;
      else
        password = $("inputPassword3").val();

      var userId = <?= $user->getId(); ?>;
      $.ajax({
          url: '/api/user/update/'+userId,
          type: 'PUT',
          data:{email:email,password:password},
          headers:{
              "X-API-KEY":"5422e102a743fd70a22ee4ff7c2ebbe8"
          },
          success: function(result) {
              if(result.error == false){
                  $("#result").append('<div class="alert alert-success">'+result["message"]+'</div>');
              }
              else{
                  $("#result").append('<div class="alert alert-danger">'+result["message"]+'</div>');
              }
          }
      });
    });
</script>