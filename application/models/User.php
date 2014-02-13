<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Model {

	public $id;
	public $email = "";
	public $password = "";
	public $registratrionDate = date();  //formattage? US?  FR?
	public $userLocationIp = "";


}
