<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Plan extends CI_Model {

	public $id ;
	public $planName = "";
	public $price = 0;
	public $duration = 0;
	public $usableStorageSpace = 0;
	public $maxBandWidth = 0;
	public $dailyDataTransfert = 0;

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

	//  PRICE 
	public function setPrice($priceValue) {
		$this->price = $priceValue;
	}
	public function getPrice(){
		return $this->price;
	}

	//*  EXPIRATION DATE 
	public function setExpirationDate($exDateValue) {
		$this->expirationDate = $exDateValue;
	}
	public function getExpirationDate(){
		return $this->expirationDate;
	}

	//  STORAGE SPACE 
	public function setUsableStorageSpace($spaceValue) {
		$this->usableStorageSpace = $spaceValue;
	}
	public function getUsableStorageSpace(){
		return $this->usableStorageSpace;
	}

	//  MAX BAND WIDTH
	public function setMaxBandWidth($maxBandWidthValue) {
		$this->maxBandWidth = $maxBandWidthValue;
	}
	public function getMaxBandWidth(){
		return $this->maxBandWidth;
	}

	//  DATA TRANSFERT 
	public function setDailyDataTransfert($dataTransfert) {
		$this->dailyDataTransfert = $dataTransfert;
	}
	public function getDailyDataTransfert(){
		return $this->dailyDataTransfert;
	}

*/

}
