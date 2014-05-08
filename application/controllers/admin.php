<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if( $this->session->userdata("user_is_admin") == false ){
			$this->session->set_flashdata('message', 'Vous devez être connecté pour effectuer cette action.');
			redirect("/login", "location");			
		}
	}

	public function index()
	{
		$viewModel["view"] = "admin/home";
		$viewModel["menu_active"] = "admin";
		$this->load->view('layouts/main', $viewModel);
	}

	public function users()
	{
		$viewModel["menu_active"] = "admin";
		$users = Entities\User::getAllUsers();
		$viewModel["view"] = "admin/users";
		$viewModel["users"] = $users;
		$this->load->view('layouts/main', $viewModel);
		
	}
}
