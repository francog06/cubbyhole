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
		$from = $this->input->post("from");
		$to = $this->input->post("to");

		if($from == false || $to == false){
			$users = Entities\User::getAllUsersByPeriod(mktime(0, 0, 0, date("m")-3, date("d"), date("Y")), mktime(0, 0, 0, date("m"), date("d")+1, date("Y")));
			$viewModel["from"] = date('Y-m-d',strtotime("-3 month"));
			$viewModel["to"] = date('Y-m-d'); 
		}
		else{
			$viewModel["to"] = $to;
			$viewModel["from"] = $from;

			$from = strtotime($from);
			$to = strtotime($to); 

			if($to<$from) {
				$to = mktime(0, 0, 0, date("m"), date("d")+1, date("Y")); 
				$users = Entities\User::getAllUsersByPeriod($from,$to);
				$to = date('Y-m-d'); 
				$viewModel["to"] = $to;
			}else $users = Entities\User::getAllUsersByPeriod($from,$to);
		}
		$viewModel["users"] = $users;

		
		//Graphique nombre user
		$result=array();
		//On calcule le nombre de jours entre from et to pour créer l'array
		if($from == false || $to == false){
			$nbJours = (mktime(0, 0, 0, date("m"), date("d")+1, date("Y")) - mktime(0, 0, 0, date("m")-3, date("d"), date("Y")))/86400; 
		}
		else{
			$nbJours = (($to+(3600*24)) - $from)/86400; 
		}

		for($i=0;$i<$nbJours;$i++){
			@$result[$i]=0;
		}

	    
	    $jourmois=null;
	    foreach($users as $user){
	        $date = "";
	        foreach ($user["registration_date"] as $v) {
	            $date = new DateTime($user["registration_date"]->date);
	        }

	        // résultat par jour/mois/annee
	        if($from == false || $to == false)
	        	$jourmois = (mktime(0, 0, 0,$date->format("m"),$date->format("d"),$date->format("Y")) - mktime(0, 0, 0, date("m")-3, date("d"), date("Y")))/86400;
	        else
	        	$jourmois = (strtotime($date->format("Y-m-d")) - $from)/86400;

	        $result[$jourmois]+=1;
	    }
	    $viewModel["users_reult"] = $result;


	  	$viewModel["nbUser"] = sizeof($users);
		//moyenne pour storage (gauge)
		$fsByUser = array();
		foreach($users as $user){
			$u = Entities\User::getUserById($user["id"]);
			//En Go, ex : 1 pour 1 Go
			$totalStorage = $u->getActivePlanHistory()->getPlan()->getUsableStorageSpace();
			$pack = $u->getActivePlanHistory()->getPlan()->getId();
			// En Mo, ex 17.0 pour 17Mo
			$fileSize=$u->getFiles();
			foreach($fileSize as $fsize)
				@$fsByUser[$pack][$u->getId()]["size"] += intval($fsize->getSize());
			if(!isset($fsByUser[$pack][$u->getId()]["size"])) $fsByUser[$pack][$u->getId()]["size"] = 0;
			@$fsByUser[$pack][$u->getId()]["usableStorage"] = $totalStorage*1024;
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
		if($from == false || $to == false)
			$users_ip = Entities\User::getAllIpUsers(mktime(0, 0, 0, date("m")-3, date("d"), date("Y")), mktime(0, 0, 0, date("m"), date("d")+1, date("Y")));
		else
			$users_ip = Entities\User::getAllIpUsers($from,$to);
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

		//Graphique Total download par période
		$nbDownload = array();
		$resultDownload = array();
		//On prend tous les plans
		foreach ($nbPlans as $plan) {
			$plan = Entities\Plan::getPlanById($plan["id"]);
			$resultDownload[$plan->getId()] = array();
			// On prend tous les downloads data_history pour chaque plan
			if($from == false || $to == false)
				$nbDownload[$plan->getId()]= $plan->getTotalDownloads(mktime(0, 0, 0, date("m")-3, date("d"), date("Y")), mktime(0, 0, 0, date("m"), date("d")+1, date("Y")));
			else
				$nbDownload[$plan->getId()]= $plan->getTotalDownloads($from,$to);
		}
		//Puis pour chaque plan, on trie le nbre par jour
		//_p($nbPlans);

			for($i=1;$i<=sizeof($nbPlans);$i++){

				//On calcule le nombre de jours entre from et to pour créer l'array
				if($from == false || $to == false){
					$nbJours = (mktime(0, 0, 0, date("m"), date("d")+1, date("Y")) - mktime(0, 0, 0, date("m")-3, date("d"), date("Y")))/86400; 
				}
				else{
					$nbJours = (($to+(3600*24)) - $from)/86400; 
				}
				for($j=0;$j<$nbJours;$j++){
					$resultDownload[$nbPlans[$i-1]["id"]][$j]=0;
				}

				foreach($nbDownload[$i] as $down){
			        $date = $down->getDate();  
			         // résultat par jour/mois/annee
			        if($from == false || $to == false)
			        	$jourmois = (mktime(0, 0, 0,$date->format("m"),$date->format("d"),$date->format("Y")) - mktime(0, 0, 0, date("m")-3, date("d"), date("Y")))/86400;
			        else
			        	$jourmois = (strtotime($date->format("Y-m-d")) - $from)/86400;

			        @$resultDownload[$nbPlans[$i-1]["id"]][$jourmois]+=1;
				}
			}
		
		$viewModel["nbDownloads"] = $resultDownload;

		//Graphique Total Shares par période
		$nbShare = array();
		$resulShare = array();
		//On prend tous les plans
		foreach ($nbPlans as $plan) {
			$plan = Entities\Plan::getPlanById($plan["id"]);
			$resultShare[$plan->getId()] = array();
			// On prend tous les downloads data_history pour chaque plan
			if($from == false || $to == false)
				$nbShare[$plan->getId()]= $plan->getTotalShares(mktime(0, 0, 0, date("m")-3, date("d"), date("Y")), mktime(0, 0, 0, date("m"), date("d")+1, date("Y")));
			else
				$nbShare[$plan->getId()]= $plan->getTotalShares($from,$to);
		}
		//Puis pour chaque plan, on trie le nbre par jour
		//_p($nbPlans);

			for($i=1;$i<=sizeof($nbPlans);$i++){
				//On calcule le nombre de jours entre from et to pour créer l'array
				if($from == false || $to == false){
					$nbJours = (mktime(0, 0, 0, date("m"), date("d")+1, date("Y")) - mktime(0, 0, 0, date("m")-3, date("d"), date("Y")))/86400; 
				}
				else{
					$nbJours = (($to+(3600*24)) - $from)/86400; 
				}
				for($j=0;$j<$nbJours;$j++){
					$resultShare[$nbPlans[$i-1]["id"]][$j]=0;
				}

				foreach($nbShare[$i] as $down){
			        $date = $down->getDate();  
			         // résultat par jour/mois/annee
			        if($from == false || $to == false)
			        	$jourmois = (mktime(0, 0, 0,$date->format("m"),$date->format("d"),$date->format("Y")) - mktime(0, 0, 0, date("m")-3, date("d"), date("Y")))/86400;
			        else
			        	$jourmois = (strtotime($date->format("Y-m-d")) - $from)/86400;

			        @$resultShare[$nbPlans[$i-1]["id"]][$jourmois]+=1;
				}
			}
		
		$viewModel["nbShares"] = $resultShare;

		$viewModel["view"] = "admin/stats";
		$viewModel["menu_active"] = "admin";
		$this->load->view('layouts/main', $viewModel);
	}
}
