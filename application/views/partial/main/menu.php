<div class="masthead clearfix">
    <div>
        <h3 class="masthead-brand"><a href="/"><img src="<?=img("logo.png")?>" height="60" alt="logo" /></a></h3>
        <ul class="nav nav-pills pull-right" style="margin-top:20px;">
            <li <?= $menu_active=="accueil"?'class="active"':''; ?>><a href="/home/index">Accueil</a></li>
            <li <?= $menu_active=="prix"?'class="active"':''; ?>><a href="/home/price">Prix</a></li>
            <li <?= $menu_active=="telecharger"?'class="active"':''; ?>><a href="/home/download">Télécharger</a></li>
            <li <?= $menu_active=="register"?'class="active"':''; ?>><a href="/login">Connexion</a></li>
            <li <?= $menu_active=="login"?'class="active"':''; ?>><a href="/login/register">Inscription</a></li>
        </ul>
    </div>
</div>