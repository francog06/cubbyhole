<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if( !$this->session->userdata('user') ){
			$this->session->set_flashdata('message', 'Vous devez être connecté pour effectuer cette action.');
			redirect("/login", "location");
		}
	}

	public function index()
	{
		$viewModel["user"] = Entities\User::getUserById($this->session->userdata('user'));
		$viewModel["view"] = "back/home";
		$this->load->view('layouts/main', $viewModel);
	}

	public function deconnexion(){
		$this->session->unset_userdata("user");
		$this->session->set_flashdata('message', 'Vous êtes maintenant déconnecté.');
		redirect("/login");
	}

}
