<div class="site-wrapper">
	<div class="site-wrapper-inner">
		<div class="cover-container">
        <!-- Menu -->
			<div class="masthead clearfix">
			<div class="">
				<h3 class="masthead-brand"><img src="<?php echo img("logo.png");?>" height="60" alt="logo" /></h3>
				<ul class="nav nav-pills pull-right" style="margin-top:20px;">
					<li><a href="/home/index">Accueil</a></li>
					<li class="active"><a href="/home/price">Prix</a></li>
					<li><a href="/home/download">Télécharger</a></li>
				</ul>
			</div>
		</div>
			<section class="section">
				<div class="section-headlines text-center">
					<h2>Nos différents plans</h2>
					<p>Choissisez votre plan selon vos besoins.</p>
				</div>

				<div class="row-fluid">
					<div class="pricing-table row-fluid text-center">
					<?php foreach($plans as $plan): ?>
						<div class="span4" style="float:left;margin-right:15px;">
							<div class="plan <?php echo $plan["is_default"]==true?"prefered":""; ?>">
								<div class="plan-name">
									<h2><?php echo $plan["name"]; ?></h2>
									<p class="muted"><?php echo $plan["description"]; ?></p>
								</div>
								<div class="plan-price">
									<b><?php echo $plan["price"]==0?"Gratuit":$plan["price"]."€"; ?></b> 
									<?php 
										if ($plan["is_default"]==true) {
											echo "à vie";
										}
										else{
											echo $plan["duration"]==30?" / mois":$plan["duration"]." / jour"; 
										}
									?>
								</div>
								<div class="plan-details">
									<div>
										<b><?php echo $plan["usable_storage_space"]; ?> Go</b> d'espace 
									</div>
									<div>
										<b><?php echo $plan["max_bandwidth"]; ?> kB/s</b> réels 
									</div>
									<div>
										<b><?php echo $plan["daily_data_transfert"]; ?> Mo/jour</b>  de partage
									</div>
								</div>
								<div class="plan-action">
									<?php  if ($plan["is_default"]==false): ?>
										<form method="post" action="/user/checkout/">
										<input type="hidden" name="plan_id" value="<?php echo $plan['id']; ?>" />
										<select name="duration" class="form-control">
											<option value="<?php echo $plan["duration"]; ?>">
												<?php echo $plan["duration"]==30?"1 mois":$plan["duration"]." jours"; ?> - 
												<?php echo $plan["price"]."€"; ?>
											</option>
											<option value="<?php echo $plan["duration"]*3; ?>">
												<?php echo $plan["duration"]==30?"3 mois":($plan["duration"]*3)." jours"; ?> - 
												<?php echo ($plan["price"]*3)."€"; ?>
											</option>
											<option value="<?php echo $plan["duration"]*12; ?>">
												<?php echo $plan["duration"]==30?"12 mois":($plan["duration"]*12)." jours"; ?> - 
												<?php echo ($plan["price"]*12)."€"; ?>
											</option>
										</select>
										<br />
										<button class="btn <?php echo $plan["duration"]!=0?"btn-success":""; ?> btn-block btn-large">Choisir ce Plan</button>
										</form>
									<?php else: ?>
										<a href="/login/register" class="btn btn-block btn-large">S'inscrire Gratuitement</a>
									<?php endif; ?>
									
								</div>
							</div>
						</div>
						<?php if(current($plan)!=1 && current($plan)%3==1){
							echo'<div style="clear:both"></div>';
						} ?>
					<?php endforeach; ?>
						
						
					</div>
					<div style="clear:both"></div>
					<p class="muted text-center">Note: Vous pouvez migrer d'offre à tout moment depuis votre compte utilisateur.</p>
				</div>
			</section>
		</div>
	</div>
</div>