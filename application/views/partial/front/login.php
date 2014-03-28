<div class="site-wrapper">
    <div class="site-wrapper-inner">
        <div class="cover-container">
            <!-- Menu -->
            <div class="masthead clearfix">
                <div class="">
                    <h3 class="masthead-brand"><img src="<?=img("logo.png")?>" height="60" alt="logo" /></h3>
                    <ul class="nav nav-pills pull-right" style="margin-top:20px;">
                        <li class="active"><a href="/Home/index">Accueil</a></li>
                        <li><a href="/Home/price">Prix</a></li>
                        <li><a href="/Home/download">Télécharger</a></li>
                    </ul>
                </div>
            </div>

            <div style="float:left;">
                <img src="<?=img("Front/accueil.png")?>" />
            </div>
            <div class="inner cover">
                <?php if ( isset($prevent_messages) ) : ?>
                    <?php foreach ($prevent_messages as $message): ?>
                        <div class="alert alert-<?=$message['type']?>"><?=$message['message']?></div>
                    <?php endforeach ?>
                <?php endif; ?>
                <?php if ( $this->session->flashdata( 'message' ) ) : ?>
                    <div class="alert alert-success"><?=$this->session->flashdata('message')?></div>
                <?php endif; ?>
                <?=form_open('Login', ['class' => 'form-signin', 'role' => 'form'])?>
                    <?=form_fieldset( 'Login Form' )?>
                        <p>
                            <?=form_error('user_email');?>
                            <?=form_input(['name' => 'user_email', 'id' => 'email', 'class' => 'form-control', 'value' => set_value('user_email'), 'placeholder' => 'Email', 'value' => set_value('user_email')])?>
                        </p>
                        <p>
                            <?=form_error('user_pass');?>
                            <?=form_password(['name' => 'user_pass', 'id' => 'password', 'class' => 'form-control', 'placeholder' => 'Password', 'value' => set_value('user_pass')])?>
                        </p>
                        <div style="margin-top: 10px;">
                            <?=form_submit( 'login', 'Login', 'class="btn btn-primary btn-block"');?>
                        </div>

                    <?php echo form_fieldset_close(); ?>
                <?php echo form_close(); ?>
            </div>

            <div class="mastfoot">
                <div class="inner">
                    <p>Cubbyhole powered baby !</p>
                </div>
            </div>
        </div>
    </div>
</div>