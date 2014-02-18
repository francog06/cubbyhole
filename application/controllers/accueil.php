<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Accueil extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array("url","assets"));
	}

	public function index()
	{
		$this->load->view('Front/accueil');
	}

	public function prix()
	{
		$this->load->view('Front/prix');
	}
}
