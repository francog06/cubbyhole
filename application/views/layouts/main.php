<!DOCTYPE html>
<html lang="fr">
	<!-- Head -->
	<?php $this->load->view('partial/main/head'); ?>

	<!-- Content -->
	<?php isset($view) ? $this->load->view('partial/' . $view) : ''; ?>

	<!-- Bottom Scripts -->
	<?php $this->load->view('partial/main/bottom-scripts'); ?>
</html>