<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$viewModel["view"] = "back/home";
		$this->load->view('layouts/main', $viewModel);
	}

}
