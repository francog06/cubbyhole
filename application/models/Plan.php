<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Plan extends CI_Model {
	public $id ;
	public $planName = "";
	public $price = 0;
	public $duration = 0;
	public $usableStorageSpace = 0;
	public $maxBandWidth = 0;
	public $dailyDataTransfert = 0;
}
