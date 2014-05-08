<!DOCTYPE html>
<html lang="fr">
	<!-- Head -->
	<?php $this->load->view('partial/main/head'); ?>

	<!-- Content -->
	<div class="site-wrapper">
    	<div class="site-wrapper-inner">
	        <div class="cover-container">
	            <!-- Menu -->
	            <?php $this->session->userdata('user') ? $this->load->view('partial/main/menu_user'):$this->load->view("partial/main/menu"); ?>
				<!-- Contenu -->
				<?php isset($view) ? $this->load->view('partial/' . $view) : ''; ?>
				<!-- Footer -->
				<div class="mastfoot">
	                <div class="inner">
	                    <p>Cubbyhole powered baby !</p>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>

	<!-- Bottom Scripts -->
	<?php $this->load->view('partial/main/bottom-scripts'); ?>
</html>