<div class="inner cover">
    <div class="panel panel-default" style="width:400px;margin:0 auto;">
        <div class="panel-body">
            <img src="<?= img('ajax-loader.gif'); ?>" style="display:inline-block;vertical-align:initial;margin-right:15px;" /> 
            <div style="display:inline-block;text-align:left;">
            	Veuillez patienter s.v.p. 
	            <br />
	            Votre transaction est en cours de traitement...
            </div>
           
        </div>
    </div>
    <div class="result" style="display:none;"></div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		var plan_id = <?= $plan["plan_id"]; ?>;
		var duration = <?= $plan["duration"]; ?>;
		var user_id = <?= $user->getId(); ?>;
		
		$.ajax({
                url: '/api/plan_history/create',
                type: 'POST',
                data: {plan_id:plan_id, duration:duration, user_id:user_id},
                headers:{
                    "X-API-KEY":"5422e102a743fd70a22ee4ff7c2ebbe8"
                },
                success: function(result) {
                	$(".panel").fadeOut();
                    if(result.error == false){
                        $("div.result").append('<p class="bg-success" style="padding: 5px 0px;">'+result["message"]+' <br />Vous allez être redirigé vers votre compte...</p>').fadeIn();
                        setTimeout(function(){
						    document.location.href="/user";
						}, 5000);
                        
                    }
                    else{
                        $("div.result").append('<p class="bg-danger" style="padding: 5px 0px;">'+result["message"]+'</p>');
                    }
                },
                error: function(result) {
                	$(".panel").fadeOut();
                    $("div.result").append('<p class="bg-danger" style="padding: 5px 0px;">Erreur lors de la transaction.</p>');
                }
        });
	});
</script>