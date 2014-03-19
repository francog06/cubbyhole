<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class PlanHistory extends CI_Model {
	public $id;
	public $subscripttionPlanDate = new date();
	public $expirationPlanDate = new date();
	public $storageUsed = 0;
	public $dailyDataTransfertUsed = 0;
	public $isActive = false;
}
