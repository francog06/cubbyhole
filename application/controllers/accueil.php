<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Accueil extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$user = new Entities\User;
		/*
		$user->setPassword('azertyuiop');
		$user->setEmail('pseudo@monmail.fr');
		$user->setRegistrationDate(new \DateTime("now"));
		$this->doctrine->em->persist($user);
		$this->doctrine->em->flush();
		*/

		/*
		$user = $this->doctrine->em->find('Entities\User', 1);
		echo $user->getEmail();
		*/
		$this->load->helper(array("url", "assets"));
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
