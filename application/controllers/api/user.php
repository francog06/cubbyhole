<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

/**
 * @class User
 * @brief Toutes les méthodes possibles concernant les utilisateurs.
 */
class User extends REST_Controller {

    /**
     * @fn __construct()
     * @brief Methode de construction de User.
     */
	function __construct()
	{
		parent::__construct();

		$this->load->library('email');
		$this->load->helper('email');

		$this->methods['login_post']['key'] = FALSE;
		$this->methods['register_post']['key'] = FALSE;
		$this->methods['forget_post']['key'] = FALSE;
	}

    /**
     * @fn details_get()
     * @brief Méthode pour récuperer les infos d'un utilisateur donné.\n
     * @URL{cubbyhole.name/api/user/details/id}\n
     * @HTTPMethod{GET}
     * @param $id @REQUIRED
     * @return $data
     */
	public function details_get($id = null) {
		$data = new StdClass();
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.', 'data' => $data), 400);
		}

		$user = $this->doctrine->em->find('Entities\User', (int)$id);
		if (is_null($user)) {
			$this->response(array('error' => true, 'message' => 'user not found.', 'data' => $data), 400);
		}

		if ($user != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL)
			$this->response(array('error' => true, 'message' => "You can't view that user", 'data' => $data), 401);

		$data = new StdClass();
		$data->user = $user;
		$this->response(array('error' => false, 'message' => 'Successfully retrieved user.', 'data' => $data), 200);
	}

    /**
     * @fn login_post()
     * @brief Méthode pour se connecter.\n
     * @URL{cubbyhole.name/api/user/login}\n
     * @HTTPMethod{POST}
     * @param email @REQUIRED
     * @param password @REQUIRED
     * @return $data
     * */
	public function login_post() {
		$user_email = $this->mandatory_value('email', 'post');
		$user_pass = $this->mandatory_value('password', 'post');

		$data = new StdClass();
		if (!valid_email($user_email)) {
			$this->response(array('error' => true, 'message' => 'Invalid email.', 'data' => $data), 400);
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
			$this->response(array('error' => true, 'message' => 'No user exist for the email specified.', 'data' => $data), 400);
			return null;
		} catch (Exception $e) {
			$this->response(array('error' => true, 'message' => 'An error occured, please contact administrator: '. $e->getMessage(), 'data' => $data), 400);
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
			$this->response(array('error' => true, 'message' => 'Invalid password', 'data' => $data), 400);
		}
	}

    /**
     * @fn register_post()
     * @brief Méthode pour s'inscrire.\n
     * @URL{cubbyhole.nam/api/user/register}\n
     * @HTTPMethod{POST}
     * @param email @REQUIRED
     * @param password @REQUIRED
     * @return $data
     * */
	public function register_post() {
		$user_email = $this->mandatory_value('email', 'post');
		$user_pass = $this->mandatory_value('password', 'post');

		$data = new StdClass();
		if (!valid_email($user_email)) {
			$this->response(array('error' => true, 'message' => 'Invalid email.', 'data' => $data), 400);
		}

		$query = $this->doctrine->em->createQueryBuilder()
					->add('select', 'u')
					->add('from', 'Entities\User u')
					->add('where', 'u.email = :email')
					->setParameter('email', $user_email)
					->getQuery();

		$result = $query->getArrayResult();
		if (!empty($result)) {
			$this->response(array('error' => true, 'message' => 'A user with this email is already specified.', 'data' => $data), 400);
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
			$this->email->message($this->load->view('layouts/main', array('user' => $user, 'view' => 'email/registration'), TRUE));
			@$this->email->send();

			$data->user = $user;
			$data->token = $key;
			$this->response(array('error' => false, 'message' => 'Successfull registration.', 'data' => $data), 201);
		}
	}

    /**
     * @fn update_put()
     * @brief Méthode pour mettre à jour les infos d'un utilisateur.\n
     * @URL{cubbyhole.name/api/user/update}\n
     * @HTTPMethod{PUT}
     * @param $id @REQUIRED
     * @return $data
     * */
	public function update_put($id = null) {
		$data = new StdClass();
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.', 'data' => $data), 400);
		}

		$user = $this->doctrine->em->find('Entities\User', (int)$id);
		if (is_null($user)) {
			$this->response(array('error' => true, 'message' => 'user not found.', 'data' => $data), 400);
		}

		if ($user != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL)
			$this->response(array('error' => true, 'message' => "You can't modify that user", 'data' => $data), 401);

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
		$this->response(array('error' => false, 'message' => 'user updated successfully.', 'data' => $data), 200);
	}

    /**
     * @fn delete_delete()
     * @brief Méthode pour suprimer un utilisateur.\n
     * @URL{cubbyhole.name/api/user/delete}\n
     * @HTTPMethod{DELETE}
     * @param $id @REQUIRED
     * @return $data
     * */
	public function delete_delete($id = null) {
		$data = new StdClass();
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.', 'data' => $data), 400);
		}

		$user = $this->doctrine->em->find('Entities\User', (int)$id);
		if (is_null($user)) {
			$this->response(array('error' => true, 'message' => 'user not found.', 'data' => $data), 400);
		}

		if ($user != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL)
			$this->response(array('error' => true, 'message' => "You can't modify that user", 'data' => $data), 401);

		$this->doctrine->em->remove($user);
		$this->doctrine->em->flush();

		$this->response(array('error' => false, 'message' => 'User has been removed.', 'data' => $data), 200);
	}

    /**
     * @fn forget_post()
     * @brief Méthode pour l'oublie de mot de passe.\n
     * @URL{cubbyhole.name/api/user/forget}\n
     * @HTTPMethod{POST}
     * @param email @REQUIRED
     * @return $data
     * */
	public function forget_post() {
		$user_email = $this->mandatory_value('email', 'post');
		$data = new StdClass();

		if (!valid_email($user_email)) {
			$this->response(array('error' => true, 'message' => 'Invalid email.', 'data' => $data), 400);
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
			$this->response(array('error' => true, 'message' => 'No user exist for the email specified.', 'data' => $data), 400);
			return null;
		} catch (Exception $e) {
			$this->response(array('error' => true, 'message' => 'An error occured, please contact an administrator: '. $e->getMessage(), 'data' => $data), 400);
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
		@$this->email->send();

		$this->response(array('error' => false, 'message' => 'Mail sent.', 'data' => $data), 200);
	}
}