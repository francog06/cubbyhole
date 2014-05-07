<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$viewModel["view"] = "front/home";
		$this->load->view('layouts/main', $viewModel);
	}

	public function price()
	{
		$viewModel["plans"] = Entities\Plan::getAllPlans();
		$viewModel["view"] = "front/price";
		$this->load->view('layouts/main', $viewModel);
	}

	public function download()
	{
		$viewModel["view"] = "front/download";
		$this->load->view('layouts/main', $viewModel);
	}
}
