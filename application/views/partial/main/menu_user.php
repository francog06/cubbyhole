<div class="masthead clearfix">
    <div>
        <h3 class="masthead-brand"><a href="/"><img src="<?=img("logo.png")?>" height="60" alt="logo" /></a></h3>
        <ul class="nav nav-pills pull-right" style="margin-top:20px;">
            <li class="active"><a href="/user">Accueil</a></li>
            <li class="dropdown-toggle"><a id="dropdownMenu2" data-toggle="dropdown" href="/user/account">Mon compte <b class="caret"></b></a>
            	<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu2">
			    	<li role="presentation"><a role="menuitem" tabindex="-1" href="/user/account/">Modifier mes infos</a></li>
				    <!--<li role="presentation" class="divider"></li>-->
				    <li role="presentation"><a role="menuitem" tabindex="-1" href="/user/upgrade">Upgrader mon compte</a></li>
				 </ul>
            </li>
            <?php if($this->session->userdata("user_is_admin")==true): ?>
                <li class="dropdown-toggle"><a id="dropdownMenu1" data-toggle="dropdown" href="/admin/index">Administration <b class="caret"></b></a>
                	<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                		<li role="presentation"><a role="menuitem" tabindex="-1" href="#">Panel d'administration</a></li>
                		<li role="presentation" class="divider"></li>
                		<li role="presentation" class="dropdown-header">Raccourcis</li>
				    	<li role="presentation"><a role="menuitem" tabindex="-1" href="#">Gestion des Users</a></li>
					    <!---->
					    <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Gestion des Plans</a></li>
					 </ul>
				</li>
           	<?php endif; ?>
            <li><a href="/user/logout">Déconnexion</a></li>
        </ul>
    </div>
</div>