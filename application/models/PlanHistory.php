<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class PlanHistory extends CI_Model {

	public $id;
	public $subscripttionPlanDate = new date();
	public $expirationPlanDate = new date();
	public $storageUsed = 0;
	public $dailyDataTransfertUsed = 0;
	public $isActive = false;

/*
	//  ID 
	public function setId($idValue) {
		$this->id = $idValue;
	}
	public function getId(){
		return $this->id;
	}

	//  PLAN NAME
	public function setPlanName($planNameValue) {
		$this->planName = $planNameValue;
	}
	public function getPlanName(){
		return $this->planName;
	}

	//  PLAN SUB 
	public function setPlanSubscriptionDuration($subValue) {
		$this->planSubscriptionDuration = $subValue;
	}
	public function getId(){
		return $this->planSubscriptionDuration;
	}

*/

}
