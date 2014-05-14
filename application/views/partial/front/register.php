<div style="float:left;">
    <img src="<?=img("Front/accueil.png")?>" />
</div>
<div class="inner cover">
    <?php if ( isset($prevent_messages) ) : ?>
    	<?php foreach ($prevent_messages as $message): ?>
    		<div class="alert alert-<?=$message['type']?>"><?=$message['message']?></div>
        <?php endforeach ?>
    <?php endif; ?>
    <?=form_open('/login/register', ['class' => 'form-signin', 'role' => 'form'])?>
        <?=form_fieldset( 'Register Form' )?>
            <p>
                <?=form_error('user_email');?>
                <?=form_input(['name' => 'user_email', 'id' => 'email', 'class' => 'form-control', 'value' => set_value('user_email'), 'placeholder' => 'Email', 'value' => set_value('user_email')])?>
            </p>
            <p>
                <?=form_error('user_pass');?>
                <?=form_password(['name' => 'user_pass', 'id' => 'password', 'class' => 'form-control', 'placeholder' => 'Password', 'value' => set_value('user_pass')])?>
            </p>
            <div style="margin-top: 10px;">
                <?=form_submit( 'register', 'Register', 'class="btn btn-primary btn-block"');?>
            </div>

        <?php echo form_fieldset_close(); ?>
    <?php echo form_close(); ?>
    <br />
    <a class="btn btn-default" href="/login">J'ai déjà un compte</a>
</div>

            