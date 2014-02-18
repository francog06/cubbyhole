
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="">

    <title>CubbyHole</title>

    <!-- Bootstrap core CSS -->
    <link href="<?php echo css("bootstrap.min"); ?>" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<?php echo css("style"); ?>" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="site-wrapper">

      <div class="site-wrapper-inner">

        <div class="cover-container">
        <!-- Menu -->
          <div class="masthead clearfix">
            <div class="">
              <h3 class="masthead-brand"><img src="<?php echo img("logo.png");?>" height="60" alt="logo" /></h3>
              <ul class="nav nav-pills pull-right" style="margin-top:20px;">
                <li class="active"><a href="#">Accueil</a></li>
                <li><a href="#">Prix</a></li>
                <li><a href="#">Télécharger</a></li>
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
							<div class="plan">
								<div class="plan-name">
									<h2>Basic</h2>
									<p class="muted">Perfect for small budget</p>
								</div>
								<div class="plan-price">
									<b>$19</b> / month
								</div>
								<div class="plan-details">
									<div>
										<b>Unlimited</b> Download
									</div>
									<div>
										<b>Free</b> Priority Shipping
									</div>
									<div>
										<b>Unlimited</b> Warranty
									</div>
								</div>
								<div class="plan-action">
									<a href="#" class="btn btn-block btn-large">Choose Plan</a>
								</div>
							</div>
						</div>

						<div class="span4" style="float:left;margin-right:15px;">
							<div class="plan prefered">
								<div class="plan-name">
									<h2>Standard</h2>
									<p class="muted">Perfect for medium budget</p>
								</div>
								<div class="plan-price">
									<b>$39</b> / month
								</div>
								<div class="plan-details">
									<div>
										<b>Unlimited</b> Download
									</div>
									<div>
										<b>Free</b> Priority Shipping
									</div>
									<div>
										<b>Unlimited</b> Warranty
									</div>
								</div>
								<div class="plan-action">
									<a href="#" class="btn btn-success btn-block btn-large">Choose Plan</a>
								</div>
							</div>
						</div>

						<div class="span4" style="float:left;">
							<div class="plan">
								<div class="plan-name">
									<h2>Advance</h2>
									<p class="muted">Perfect for large budget</p>
								</div>
								<div class="plan-price">
									<b>$59</b> / month
								</div>
								<div class="plan-details">
									<div>
										<b>Unlimited</b> Download
									</div>
									<div>
										<b>Free</b> Priority Shipping
									</div>
									<div>
										<b>Unlimited</b> Warranty
									</div>
								</div>
								<div class="plan-action">
									<a href="#" class="btn btn-block btn-large">Choose Plan</a>
								</div>
							</div>
						</div>
						<div style="clear:both"></div>
					</div>
					<p class="muted text-center">Note: You can change or cancel your plan at anytime in your account settings.</p>
				</div>
			</section>
 </div>

      </div>

    </div>

    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="<?php js("bootstrap.min"); ?>"></script>
    <script src="<?php js("assets/js/docs.min"); ?>"></script>
  </body>
</html>
