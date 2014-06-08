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
	public function plans()
	{
		$viewModel["menu_active"] = "admin";
		$plans = Entities\Plan::getAllPlansAdmin();
		$viewModel["plans"] = $plans;
		$viewModel["view"] = "admin/plans";
		$this->load->view('layouts/main', $viewModel);
	}

	public function stats()
	{		
		$users_period = Entities\User::getAllUsersByPeriod(mktime(0, 0, 0, date("m"), date("d"), date("Y")-1), mktime(0, 0, 0, date("m"), date("d")+1, date("Y")));
		$viewModel["users"] = $users_period;

		$users = Entities\User::getAllUsers();
		$viewModel["nbUser"] = sizeof($users);
		//moyenne pour storage (gauge)
		$fsByUser = array();
		foreach($users as $user){
			$u = Entities\User::getUserById($user["id"]);
			$totalStorage = $u->getActivePlanHistory()->getPlan()->getUsableStorageSpace();
			$pack = $u->getActivePlanHistory()->getPlan()->getId();
			$fileSize=$u->getFiles();
			foreach($fileSize as $fsize)
				@$fsByUser[$pack][$u->getId()]["size"] += intval($fsize->getSize());
			if(!isset($fsByUser[$pack][$u->getId()]["size"])) $fsByUser[$pack][$u->getId()]["size"] = 0;
			@$fsByUser[$pack][$u->getId()]["usableStorage"] = $totalStorage*MB;
		}
		$viewModel["fsByUser"] = $fsByUser;
		
		//Tableau nombre de user par pack
		$nbPlans = Entities\Plan::getAllPlans();
		$viewModel["nbPlans"] = $nbPlans;

		$users_plan = array("Gratuit"=>0);
		foreach ($users as $user) {
			$u = Entities\User::getUserById($user["id"]);
			$plan = $u->getActivePlanHistory()->getPlan();
			if($plan->getIsDefault() == 1)
				@$users_plan["Gratuit"]+=1;
			else 
				@$users_plan[$plan->getName()]+=1;
		}
		$viewModel["users_plan"] = $users_plan;
		
		//Geolocation
		$users_ip = Entities\User::getAllIpUsers();
		$users_country = array("Autres"=>0);
		foreach($users_ip as $v){
			// _p($v["user_location_ip"]);
			$result = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$v["user_location_ip"]));
			// _p($result['geoplugin_countryName']);
			if($result['geoplugin_countryName'] == "" || $result['geoplugin_countryName'] == " " || $result['geoplugin_countryName'] == null)
				$users_country["Autres"] += 1;
			else @$users_country[$result['geoplugin_countryName']] += 1;

		}
		$viewModel["users_country"] = $users_country;

		$viewModel["view"] = "admin/stats";
		$viewModel["menu_active"] = "admin";
		$this->load->view('layouts/main', $viewModel);
	}
}
