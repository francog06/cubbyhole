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
						<div class="span4" style="float:left;margin-right:15px;">
							<div class="plan prefered">
								<div class="plan-name">
									<h2>Gratuit</h2>
									<p class="muted">Inclus dès l'inscription</p>
								</div>
								<div class="plan-price">
									<b>Gratuit</b> à vie
								</div>
								<div class="plan-details">
									<div>
										<b>1 Go</b> d'espace
									</div>
									<div>
										<b>100</b> kB/s réels 
									</div>
									<div>
										<b>100 Mo/jour</b> de partage
									</div>
								</div>
								<div class="plan-action">
									<a href="#" class="btn btn-block btn-large">Choisir ce Plan</a>
								</div>
							</div>
						</div>
					<?php foreach($plans as $plan): ?>
						<div class="span4" style="float:left;margin-right:15px;">
							<div class="plan">
								<div class="plan-name">
									<h2><?php echo $plan["name"]; ?></h2>
									<p class="muted"><?php echo $plan["description"]; ?></p>
								</div>
								<div class="plan-price">
									<b><?php echo $plan["price"]; ?>€</b> / <?php echo $plan["duration"]==30?"mois":$plan["duration"]." jours"; ?>
								</div>
								<div class="plan-details">
									<div>
										<b><?php echo $plan["usable_storage_space"]; ?> Go</b> d'espace 
									</div>
									<div>
										<b><?php echo $plan["max_bandwidth"]; ?></b> kB/s réels 
									</div>
									<div>
										<b><?php echo $plan["daily_data_transfert"]; ?> Mo/jour</b>  de partage
									</div>
								</div>
								<div class="plan-action">
									<a href="#" class="btn btn-success btn-block btn-large">Choisir ce Plan</a>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
						
						<div style="clear:both"></div>
					</div>
					<p class="muted text-center">Note: You can change or cancel your plan at anytime in your account settings.</p>
				</div>
			</section>
		</div>
	</div>
</div>