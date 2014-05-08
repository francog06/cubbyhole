<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if($this->session->userdata('user'))
			redirect("/user");
	}

	public function index()
	{
		$viewModel["view"] = "front/home";
		$viewModel["menu_active"] = "accueil";
		$this->load->view('layouts/main', $viewModel);
	}

	public function price()
	{
		$viewModel["plans"] = Entities\Plan::getAllPlans();
		$viewModel["view"] = "front/price";
		$viewModel["menu_active"] = "prix";
		$this->load->view('layouts/main', $viewModel);
	}

	public function download()
	{
		$viewModel["view"] = "front/download";
		$viewModel["menu_active"] = "telecharger";
		$this->load->view('layouts/main', $viewModel);
	}
}
