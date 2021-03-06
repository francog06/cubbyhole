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

	public function login_post() {
		$user_email = $this->mandatory_value('email', 'post');
		$user_pass = $this->mandatory_value('password', 'post');

		$data = new StdClass();
		if (!valid_email($user_email)) {
			$this->response(array('error' => true, 'message' => 'Adresse email invalide.', 'data' => $data), 400);
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
			$this->response(array('error' => true, 'message' => 'Aucun utilisateur trouvé.', 'data' => $data), 400);
			return null;
		} catch (Exception $e) {
			$this->response(array('error' => true, 'message' => 'Une erreur est survenue, merci de contacter un administrateur: '. $e->getMessage(), 'data' => $data), 400);
			return null;
		}

		if ($this->encrypt->decode($user->getPassword()) == $user_pass) {
			$q = $this->db->get_where('keys', array('user_id' => $user->getId()))->row();

			$data->user = $user;
			$data->token = $q->key;
			$this->response(array(
				'error' => false,
				'message' => 'Connection successfull',
				'data' => $data
			), 200);
		} else {
			$this->response(array('error' => true, 'message' => 'Identifiants invalide.', 'data' => $data), 400);
		}
	}

	public function register_post() {
		$user_email = $this->mandatory_value('email', 'post');
		$user_pass = $this->mandatory_value('password', 'post');

		$data = new StdClass();
		if (!valid_email($user_email)) {
			$this->response(array('error' => true, 'message' => 'Adresse email invalide', 'data' => $data), 400);
		}

		$query = $this->doctrine->em->createQueryBuilder()
					->add('select', 'u')
					->add('from', 'Entities\User u')
					->add('where', 'u.email = :email')
					->setParameter('email', $user_email)
					->getQuery();

		$result = $query->getArrayResult();
		if (!empty($result)) {
			$this->response(array('error' => true, 'message' => 'Cette adresse email est déjà utilisée.', 'data' => $data), 400);
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

			$key = $user->createKey();

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
			$this->email->message($this->load->view('layouts/email', array('user' => $user, 'view' => 'email/registration'), TRUE));
			@$this->email->send();

			$data->user = $user;
			$data->token = $key;
			$this->response(array('error' => false, 'message' => 'Vous êtes désormais inscrit.', 'data' => $data), 201);
		}
	}

	public function details_get($id = null) {
		$data = new StdClass();
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'Vous n\'avez défini aucun ID', 'data' => $data), 400);
		}

		$user = $this->doctrine->em->find('Entities\User', (int)$id);
		if (is_null($user)) {
			$this->response(array('error' => true, 'message' => 'Utilisateur non trouvé.', 'data' => $data), 400);
		}

		if ($user != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL)
			$this->response(array('error' => true, 'message' => "Vous n'avez pas accès à cet utilisateur.", 'data' => $data), 401);

		$data = new StdClass();
		$data->user = $user;
		$this->response(array('error' => false, 'message' => 'Récupération de l\'utilisateur réussi.', 'data' => $data), 200);
	}

	public function update_put($id = null) {
		$data = new StdClass();
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'Vous n\'avez défini aucun ID', 'data' => $data), 400);
		}

		$user = $this->doctrine->em->find('Entities\User', (int)$id);
		if (is_null($user)) {
			$this->response(array('error' => true, 'message' => 'Utilisateur non trouvé.', 'data' => $data), 400);
		}

		if ($user != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL)
			$this->response(array('error' => true, 'message' => "Vous ne pouvez pas modifier cet utilisateur.", 'data' => $data), 401);

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

		$data->user = $user;
		$this->response(array('error' => false, 'message' => "Mise à jour de l'utilisateur réussie.", 'data' => $data), 200);
	}

	public function delete_delete($id = null) {
		$data = new StdClass();
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'Vous n\'avez défini aucun ID', 'data' => $data), 400);
		}

		$user = $this->doctrine->em->find('Entities\User', (int)$id);
		if (is_null($user)) {
			$this->response(array('error' => true, 'message' => 'Utilisateur non trouvé.', 'data' => $data), 400);
		}

		if ($user != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL)
			$this->response(array('error' => true, 'message' => "Vous ne pouvez pas modifier cet utilisateur.", 'data' => $data), 401);

		$this->doctrine->em->remove($user);
		$this->doctrine->em->flush();

		$this->response(array('error' => false, 'message' => "L'utilisateur a été supprimé.", 'data' => $data), 200);
	}

	public function forget_post() {
		$user_email = $this->mandatory_value('email', 'post');
		$data = new StdClass();

		if (!valid_email($user_email)) {
			$this->response(array('error' => true, 'message' => "Adresse email invalide.", 'data' => $data), 400);
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
			$this->response(array('error' => true, 'message' => "Aucune utilisateur n'a été trouvé.", 'data' => $data), 400);
			return null;
		} catch (Exception $e) {
			$this->response(array('error' => true, 'message' => 'Une erreur est survenue, merci de contact un administrateur: '. $e->getMessage(), 'data' => $data), 400);
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
		$this->email->message($this->load->view('layouts/email', array('user' => $user, 'view' => 'email/forget_password'), TRUE));
		@$this->email->send();

		$this->response(array('error' => false, 'message' => 'Mail envoyé.', 'data' => $data), 200);
	}

	public function synchronize_done() {
		$user = $this->rest->user;

		$user->setLastSynchronizeCall(new DateTime("now", new DateTimeZone("Europe/Berlin")));
		$this->doctrine->em->merge($user);
		$this->doctrine->flush();
	}
}