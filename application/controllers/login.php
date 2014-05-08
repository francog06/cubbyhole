<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->library(array("form_validation", "email"));
		$this->load->helper(array("form"));

		$this->prevent_messages = [];
	}

	public function index()
	{
		$this->form_validation->set_rules( 'user_email', 'username', 'required|valid_email' );
		$this->form_validation->set_rules( 'user_pass', 'password', 'required' );
		$this->form_validation->set_error_delimiters( '<em>','</em>' );

		if ($this->input->post('login')) {
		    if ($this->form_validation->run()) {
		       $this->connect_user();
		    }
		}

		$viewModel["view"] = "front/login";
		$viewModel["prevent_messages"] = $this->prevent_messages;
		$this->load->view('layouts/main', $viewModel);
	}

	public function register()
	{
		$this->form_validation->set_rules( 'user_email', 'username', 'required|valid_email' );
		$this->form_validation->set_rules( 'user_pass', 'password', 'required' );
		$this->form_validation->set_error_delimiters( '<em>','</em>' );

		if ($this->input->post('register')) {
		    if ($this->form_validation->run()) {
		       $this->register_user();
		    }
		}

		$viewModel["view"] = "front/register";
		$viewModel["prevent_messages"] = $this->prevent_messages;
		$this->load->view('layouts/main', $viewModel);
	}

	private function connect_user() {
		$user_email = $this->input->post('user_email');
        $user_pass = $this->input->post('user_pass');

        $query = $this->doctrine->em->createQueryBuilder()
					->add('select', 'u')
					->add('from', 'Entities\User u')
					->add('where', 'u.email = :email')
					->setParameter('email', $user_email)
					->getQuery();

		try {
			$user = $query->getSingleResult();
		} catch (Doctrine\ORM\NoResultException $e) {
			$this->prevent_messages[] = array('type' => 'danger', 'message' => 'No user exist for the email specified.');
			return null;
		} catch (Exception $e) {
			$this->prevent_messages[] = array('type' => 'danger', 'message' => 'An error occured, please contact an administrator: '. $e->getMessage());
			return null;
		}

		if ($this->encrypt->decode($user->getPassword()) == $user_pass) {
			// Redirect to Dashboard
			$this->session->set_userdata("user", $user->getId());
			// _p($this->session->userdata('user'));

			redirect('/user/');
		} else {
			$this->prevent_messages[] = array('type' => 'danger', 'message' => 'Invalid password.');
		}
	}

	private function register_user() {
		$user_email = $this->input->post('user_email');
        $user_pass = $this->input->post('user_pass');

        $query = $this->doctrine->em->createQueryBuilder()
					->add('select', 'u')
					->add('from', 'Entities\User u')
					->add('where', 'u.email = :email')
					->setParameter('email', $user_email)
					->getQuery();

		$result = $query->getArrayResult();
		if (!empty($result)) {
			$this->prevent_messages[] = array('type' => 'danger', 'message' => 'A user with this email is already specified.');
		} else {
			$user = new Entities\User;

			$user->setEmail($user_email)
				->setPassword($this->encrypt->encode($user_pass))
				->setRegistrationDate(new DateTime('now'))
				->setUserLocationIp($this->input->ip_address())
				->setIsAdmin(false);

			$this->doctrine->em->persist($user);
			$this->doctrine->em->flush();

			// Send user email

			$this->email->clear();
			$this->email->initialize(array(
				'mailtype' => 'html',
				'charset'  => 'utf-8',
				'priority' => '1'
            ));
			$this->email->to($user->getEmail());
			$this->email->from('registration@cubbyhole.name');
			$this->email->subject('Votre inscription sur Cubbyhole');
			$this->email->message($this->load->view('layouts/main', array('user' => $user, 'view' => 'email/registration'), TRUE));
			$this->email->send();

			$this->session->set_flashdata('message', 'Successfull registration.');
			redirect('/login', 'location');
		}
	}
}
