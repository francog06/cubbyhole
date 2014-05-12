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
		// Si le paiement a été effectué -> /user/checkout/success
		if($this->input->get('token') && $this->input->get("PayerID")){
			$viewModel["return_success"] = true;
			$viewModel["plan"] = ["plan_id"=>$this->session->userdata('checkout_plan_id'), "duration"=>$this->session->userdata('checkout_duration')];
			$viewModel["user"] = Entities\User::getUserById($this->session->userdata('user'));
			$viewModel["view"] = "back/checkout";
			$viewModel["menu_active"] = "upgrade";

			$current_plan = Entities\Plan::getPlanById($this->session->userdata('checkout_plan_id'));

			$api_paypal = 'https://api-3t.sandbox.paypal.com/nvp?'; // Site de l'API PayPal. 
			$version = 56.0; // Version de l'API
			$user = '125899-facilitator_api1.supinfo.com'; // Utilisateur API
			$pass = '1399387932'; // Mot de passe API
			$signature = 'An5ns1Kso7MWUdW4ErQKJJJ4qi4-Aqs1j4s75mnDYbYinwS8YwSpi4RJ'; // Signature de l'API

			$api_paypal = $api_paypal.'VERSION='.$version.'&USER='.$user.'&PWD='.$pass.'&SIGNATURE='.$signature; 
			$requete = $api_paypal."&METHOD=DoExpressCheckoutPayment".
			"&TOKEN=".htmlentities($_GET['token'], ENT_QUOTES). // Ajoute le jeton qui nous a été renvoyé
			"&AMT=".$current_plan->getPrice()*($this->session->userdata('checkout_duration')/$current_plan->getDuration()).
			"&CURRENCYCODE=EUR".
			"&PayerID=".htmlentities($_GET['PayerID'], ENT_QUOTES). // Ajoute l'identifiant du paiement qui nous a également été renvoyé
			"&PAYMENTACTION=sale";

			$viewModel["req"] = $requete;

			$this->load->view('layouts/main', $viewModel);
		}
		// Sinon premier passage
		elseif($this->input->post("plan_id"))
		{
			$plan_id = $this->input->post('plan_id');
	        $duration = $this->input->post('duration');
	        $this->session->set_userdata(["checkout_plan_id"=>$plan_id, "checkout_duration"=>$duration]);
	        $current_plan = Entities\Plan::getPlanById($plan_id);

	        $viewModel["plan"] = ["plan_id"=>$plan_id, "duration"=>$duration];
			$viewModel["user"] = Entities\User::getUserById($this->session->userdata('user'));
			$viewModel["view"] = "back/checkout";
			$viewModel["menu_active"] = "upgrade";
			

			// API Paypal
			$this->load->helper('url');
			$api_paypal = 'https://api-3t.sandbox.paypal.com/nvp?'; // Site de l'API PayPal. 
			$version = 56.0; // Version de l'API
			$user = '125899-facilitator_api1.supinfo.com'; // Utilisateur API
			$pass = '1399387932'; // Mot de passe API
			$signature = 'An5ns1Kso7MWUdW4ErQKJJJ4qi4-Aqs1j4s75mnDYbYinwS8YwSpi4RJ'; // Signature de l'API

			$api_paypal = $api_paypal.'VERSION='.$version.'&USER='.$user.'&PWD='.$pass.'&SIGNATURE='.$signature; 

			$requete = $api_paypal."&METHOD=SetExpressCheckout".
				"&CANCELURL=".urlencode(site_url("user/upgrade")).
				"&RETURNURL=".urlencode(site_url("user/checkout")).
				"&AMT=".$current_plan->getPrice()*($duration/$current_plan->getDuration()).
				"&CURRENCYCODE=EUR".
				"&DESC=".urlencode($current_plan->getName()." : ".$current_plan->getDescription()).
				"&LOCALECODE=FR".
				"&HDRIMG=".urlencode(img("logo.png"));

			$viewModel["req"] = $requete;

			$this->load->view('layouts/main', $viewModel);
		}
		else{redirect("/user/upgrade");}
	}
}
