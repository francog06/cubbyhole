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
		$viewModel["menu_active"] = "accueil";
		$this->load->view('layouts/main', $viewModel);
	}

	public function logout()
	{
		$this->session->unset_userdata("user");
		$this->session->unset_userdata("user_is_admin");
		$this->session->set_flashdata('message', 'Vous êtes maintenant déconnecté.');
		redirect("/login");
	}

	public function upgrade()
	{
		$viewModel["plans"] = Entities\Plan::getAllPlans();
		$viewModel["user"] = Entities\User::getUserById($this->session->userdata('user'));
		$viewModel["view"] = "back/upgrade";
		$viewModel["menu_active"] = "upgrade";
		$this->load->view('layouts/main', $viewModel);
	}

	public function account()
	{
		$viewModel["user"] = Entities\User::getUserById($this->session->userdata('user'));
		$viewModel["user_plan"] = $viewModel["user"]->getActivePlanHistory();
		$viewModel["view"] = "back/account";
		$viewModel["menu_active"] = "account";
		$this->load->view('layouts/main', $viewModel);
	}

	public function checkout()
	{	
		$plan_id = $this->input->post('plan_id');
        $duration = $this->input->post('duration');
        $viewModel["plan"] = ["plan_id"=>$plan_id, "duration"=>$duration];
		$viewModel["user"] = Entities\User::getUserById($this->session->userdata('user'));
		$viewModel["view"] = "back/checkout";
		$viewModel["menu_active"] = "upgrade";
		$this->load->view('layouts/main', $viewModel);
	}
}
