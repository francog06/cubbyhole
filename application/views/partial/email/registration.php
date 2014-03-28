<?php 
/*
<h3>Welcome, Elijah Baily</h3>
<p class="lead">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et.</p>

<!-- A Real Hero (and a real human being) -->
<p><img src="http://placehold.it/600x300" /></p><!-- /hero -->

<!-- Callout Panel -->
<p class="callout">
	Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt. <a href="#">Do it Now! &raquo;</a>
</p><!-- /Callout Panel -->

<h3>Title Ipsum <small>This is a note.</small></h3>
<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
<a class="btn">Click Me!</a>
*/ ?>
<h3>Bienvenue, <?=$user->getEmail()?></h3>
<p class="lead">Merci de vous être inscrit sur CubbyHole !<br />Voici un rappel de vos identifiants: </p>

<!-- Callout Panel -->
<p class="callout">
	<b>Login</b>: <?=$user->getEmail()?><br />
	<b>Password</b>: <?=$this->encrypt->decode($user->getPassword())?>
</p><!-- /Callout Panel -->
