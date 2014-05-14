<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class User extends REST_Controller {
	function __construct()
	{
		parent::__construct();

		$this->load->library('email');
		$this->load->helper('email');

		$this->methods['login_post']['key'] = FALSE;
		$this->methods['register_post']['key'] = FALSE;
		$this->methods['forget_post']['key'] = FALSE;
	}

	public function details_get($id = null) {
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.'), 400);
		}

		$user = $this->doctrine->em->find('Entities\User', (int)$id);
		if (is_null($user)) {
			$this->response(array('error' => true, 'message' => 'user not found.'), 400);
		}

		if ($user != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL)
			$this->response(array('error' => true, 'message' => "You can't view that user"), 401);

		$response = [
			"error" => false,
			"user" => $user
		];
		$this->response(array('error' => false, 'user' => $user), 200);
	}

	public function login_post() {
		$user_email = $this->mandatory_value('email', 'post');
		$user_pass = $this->mandatory_value('password', 'post');

		if (!valid_email($user_email)) {
			$this->response(array('error' => true, 'message' => 'Invalid email.'), 400);
		}

		$query = $this->doctrine->em->createQueryBuilder()
					->add('select', 'u')
					->add('from', 'Entities\User u')
					->add('where', 'u.email = :email')
					->setParameter('email', $user_email)
					->getQuery();

		try {
			$user = $query->getSingleResult();
		} catch (Doctrine\ORM\NoResultException $e) {
			$this->response(array('error' => true, 'message' => 'No user exist for the email specified.'), 400);
			return null;
		} catch (Exception $e) {
			$this->response(array('error' => true, 'message' => 'An error occured, please contact administrator: '. $e->getMessage()), 400);
			return null;
		}

		if ($this->encrypt->decode($user->getPassword()) == $user_pass) {
			$q = $this->db->get_where('keys', array('user_id' => $user->getId()))->row();

			$this->response(array(
				'error' => false,
				'message' => 'Connection successfull',
				'user' => $user,
				'token' => $q->key
			), 200);
		} else {
			$this->response(array('error' => true, 'message' => 'Invalid password'), 400);
		}
	}

	public function register_post() {
		$user_email = $this->mandatory_value('email', 'post');
		$user_pass = $this->mandatory_value('password', 'post');

		if (!valid_email($user_email)) {
			$this->response(array('error' => true, 'message' => 'Invalid email.'), 400);
		}

		$query = $this->doctrine->em->createQueryBuilder()
					->add('select', 'u')
					->add('from', 'Entities\User u')
					->add('where', 'u.email = :email')
					->setParameter('email', $user_email)
					->getQuery();

		$result = $query->getArrayResult();
		if (!empty($result)) {
			$this->response(array('error' => true, 'message' => 'A user with this email is already specified.'), 400);
		} else {
			$user = new Entities\User;

			$user->setEmail($user_email)
				->setPassword($this->encrypt->encode($user_pass))
				->setRegistrationDate(new DateTime('now'))
				->setUserLocationIp($this->input->ip_address())
				->setIsAdmin(false);

			if ( ($is_admin = $this->post('is_admin')) !== false ) {
				$user->setIsAdmin( $is_admin == "0" ? false : true );
			}

			$this->doctrine->em->persist($user);
			$this->doctrine->em->flush();

			$plan_history = new Entities\PlanHistory;
			$plan = Entities\Plan::getDefaultPlan();

			$expiration = new DateTime('now');
			$plan_history->setUser($user)
				->setPlan($plan)
				->setSubscriptionPlanDate(new DateTime('now'))
				->setIsActive(true);
			$expiration->add(new DateInterval('P'. $plan->getDuration() . 'D'));
			$plan_history->setExpirationPlanDate($expiration);
			$this->doctrine->em->persist($plan_history);
			$this->doctrine->em->flush();

			$user->createKey();

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

			$this->response(array('error' => false, 'message' => 'Successfull registration.', 'user' => $user), 201);
		}
	}

	public function update_put($id = null) {
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.'), 400);
		}

		$user = $this->doctrine->em->find('Entities\User', (int)$id);
		if (is_null($user)) {
			$this->response(array('error' => true, 'message' => 'user not found.'), 400);
		}

		if ($user != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL)
			$this->response(array('error' => true, 'message' => "You can't modify that user"), 401);

		if ( ($email = $this->put('email')) !== false ) {
			$user->setEmail($email);
		}

		if ( ($password = $this->put('password')) !== false ) {
			$user->setPassword($this->encrypt->encode($password));
		}

		if ( ($user_location_ip = $this->put('user_location_ip')) !== false ) {
			$user->setUserLocationIp($user_location_ip);
		}

		if ( ($is_admin = $this->put('is_admin')) !== false ) {
			$user->setIsAdmin( ($is_admin == 1 ? true : false) );
		}

		$this->doctrine->em->merge($user);
		$this->doctrine->em->flush();

		$user->updateKey();
		$this->response(array('error' => false, 'message' => 'user updated successfully.', 'user' => $user), 200);
	}

	public function delete_delete($id = null) {
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.'), 400);
		}

		$user = $this->doctrine->em->find('Entities\User', (int)$id);
		if (is_null($user)) {
			$this->response(array('error' => true, 'message' => 'user not found.'), 400);
		}

		if ($user != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL)
			$this->response(array('error' => true, 'message' => "You can't modify that user"), 401);

		$this->doctrine->em->remove($user);
		$this->doctrine->em->flush();

		$this->response(array('error' => false, 'message' => 'User has been removed.'), 200);
	}

	public function forget_post() {
		$user_email = $this->mandatory_value('email', 'post');

		if (!valid_email($user_email)) {
			$this->response(array('error' => true, 'message' => 'Invalid email.'), 400);
		}

		$query = $this->doctrine->em->createQueryBuilder()
					->add('select', 'u')
					->add('from', 'Entities\User u')
					->add('where', 'u.email = :email')
					->setParameter('email', $user_email)
					->getQuery();

		try {
			$user = $query->getSingleResult();
		} catch (Doctrine\ORM\NoResultException $e) {
			$this->response(array('error' => true, 'message' => 'No user exist for the email specified.'), 400);
			return null;
		} catch (Exception $e) {
			$this->response(array('error' => true, 'message' => 'An error occured, please contact an administrator: '. $e->getMessage()), 400);
			return null;
		}

		$this->email->clear();
		$this->email->initialize(array(
			'mailtype' => 'html',
			'charset'  => 'utf-8',
			'priority' => '1'
        ));
		$this->email->to($user->getEmail());
		$this->email->from('password@cubbyhole.name');
		$this->email->subject('Votre mot de passe sur Cubbyhole');
		$this->email->message($this->load->view('layouts/main', array('user' => $user, 'view' => 'email/forget_password'), TRUE));
		$this->email->send();

		$this->response(array('error' => false, 'message' => 'Mail sent.'), 200);
	}
}