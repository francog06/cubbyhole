<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$viewModel["view"] = "admin/home";
		$this->load->view('layouts/main', $viewModel);
	}

	public function users()
	{
		$users = Entities\User::getAllUsers();
		$viewModel["view"] = "admin/users";
		$viewModel["users"] = $users;
		$this->load->view('layouts/main', $viewModel);
		
	}
}
