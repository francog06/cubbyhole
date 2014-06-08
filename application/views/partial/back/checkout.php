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
    <?php
    // Si c'est le premier passage, on va vers paypal
    if(!isset($return_success) || $return_success != true)
    {
        $ch = curl_init($req);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $resultat_paypal = curl_exec($ch);
        // S'il y a une erreur, on affiche "Erreur", suivi du détail de l'erreur.
        if (!$resultat_paypal)
            {echo "<p class='bg-danger' style='padding: 5px 0px;'>Erreur</p><p>".curl_error($ch)."</p>";}
        else 
        {
            $liste_parametres = explode("&",$resultat_paypal);
            foreach($liste_parametres as $param_paypal)
            {
                list($nom, $valeur) = explode("=", $param_paypal);
                $liste_param_paypal[$nom]=urldecode($valeur); 
            }
                
                // Si la requête a été traitée avec succès
            if ($liste_param_paypal['ACK'] == 'Success')
            {
                // Redirige le visiteur sur le site de PayPal
                header("Location: https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&token=".$liste_param_paypal['TOKEN']);
                exit();
            }
            else // En cas d'échec, affiche la première erreur trouvée.
            {
                echo "<p class='bg-danger' style='padding: 5px 0px;'>Erreur de communication avec le serveur PayPal.<br />".$liste_param_paypal['L_SHORTMESSAGE0']."<br />".$liste_param_paypal['L_LONGMESSAGE0']."</p>";
            } 
        }
        curl_close($ch);
    }
    elseif(isset($return_success) && $return_success == true)
    {
        $ch = curl_init($req);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $resultat_paypal = curl_exec($ch);

        if (!$resultat_paypal) // S'il y a une erreur, on affiche "Erreur", suivi du détail de l'erreur.
            {echo "<p>Erreur</p><p>".curl_error($ch)."</p>";}
        else
        {
            $liste_parametres = explode("&",$resultat_paypal);
            foreach($liste_parametres as $param_paypal)
            {
                list($nom, $valeur) = explode("=", $param_paypal);
                $liste_param_paypal[$nom]=urldecode($valeur); 
            }
            
            // Si la requête a été traitée avec succès
            if ($liste_param_paypal['ACK'] == 'Success')
            {
                ?>

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
                                    "X-API-KEY":"<?= $this->session->userdata('user_token'); ?>"
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

                <?php
            }
            else // En cas d'échec, affiche la première erreur trouvée.
            {
                echo "<p>Erreur de communication avec le serveur PayPal.<br />".$liste_param_paypal['L_SHORTMESSAGE0']."<br />".$liste_param_paypal['L_LONGMESSAGE0']."</p>";
            }
        }
        // On ferme notre session cURL.
        curl_close($ch);
    }
    ?>
    <div class="result" style="display:none;"></div>
</div>
