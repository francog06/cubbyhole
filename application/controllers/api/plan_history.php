<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Plan_history extends REST_Controller {
	function __construct()
	{
		parent::__construct();
	}

	// @GET details
	public function details_get($id = null) {
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'Id not defined.'), 400);
		}

		$PlanHistory = $this->doctrine->em->find('Entities\PlanHistory', (int)$id);
		if (is_null($PlanHistory)) {
			$this->response(array('error' => true, 'message' => 'PlanHistory not found.'), 400);
		}

		if ($PlanHistory->getUser() != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL)
			$this->response(array('error' => true, 'message' => "You are not allowed to do this."), 401);

		$this->response(array('error' => false, 'PlanHistory' => $PlanHistory), 200);
	}

	// @GET user
	public function user_get($id = null) {
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'Id not defined.'), 400);
		}

		$User = $this->doctrine->em->find('Entities\User', (int)$id);
		if (is_null($User)) {
			$this->response(array('error' => true, 'message' => 'User not found.'), 400);
		}

		if ($User != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL)
			$this->response(array('error' => true, 'message' => "You are not allowed to do this."), 401);

		$planHistorys = $User->getPlanHistorys();
		$response = null;
		if (isset($planHistorys) && $planHistorys->count() > 0) {
			foreach ($planHistorys as $planHistory) {
				if($planHistory->getIsActive() == true) {
					$this->response(array('error' => false, 'PlanHistory' => $planHistory), 200);
				}
			}
			$this->response(array('error' => true, 'message' => 'No active plan'), 400);
		} else {
			$this->response(array('error' => true, 'message' => 'This user does not have an active plan yet.'), 400);
		}
	}

	// @POST create
	public function create_post() {
		$PlanHistory_planId = $this->mandatory_value('plan_id', 'post');
		$PlanHistory_userId = $this->mandatory_value('user_id', 'post');

		$User = $this->doctrine->em->find('Entities\User', (int)$PlanHistory_userId);
		if (is_null($User)) {
			$this->response(array('error' => true, 'message' => 'User not found.'), 400);
		}

		if ($User != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL)
			$this->response(array('error' => true, 'message' => "You are not allowed to do this."), 401);

		$Plan = $this->doctrine->em->find('Entities\Plan', (int)$PlanHistory_planId);
		if (is_null($Plan)) {
			$this->response(array('error' => true, 'message' => 'Plan not found.'), 400);
		}

		$this->doctrine->em->createQueryBuilder()
			->update('Entities\PlanHistory', 'ph')
			->set('ph.is_active', '0')
			->where('ph.user = :user AND ph.is_active = :active')
			->setParameter('user', $User)
			->setParameter('active', '1')
			->getQuery()
			->execute();

		$PlanHistory = new Entities\PlanHistory;

		$expiration = new DateTime('now');
		$PlanHistory->setUser($User)
		->setPlan($Plan)
		->setSubscriptionPlanDate(new DateTime('now'))
		->setIsActive(true);

		if ( ($duration = $this->post('duration')) !== false ) {
			$expiration->add(new DateInterval('P'. $duration . 'D'));
		}
		else {
			$expiration->add(new DateInterval('P'. $Plan->getDuration() . 'D'));
		}
		$PlanHistory->setExpirationPlanDate($expiration);

		$this->doctrine->em->persist($PlanHistory);
		$this->doctrine->em->flush();

		$User->addPlanHistory($PlanHistory);
		$this->response(array('error' => false, 'message' => 'A new plan history has been added.', 'PlanHistory' => $PlanHistory), 201);
	}

	// @UPDATE
	public function update_put($id = null) {
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'Id not defined.'), 400);
		}

		$PlanHistory = $this->doctrine->em->find('Entities\PlanHistory', (int)$id);
		if (is_null($PlanHistory)) {
			$this->response(array('error' => true, 'message' => 'PlanHistory not found.'), 400);
		}

		if ($PlanHistory->getUser() != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL)
			$this->response(array('error' => true, 'message' => "You are not allowed to do this."), 401);

		$PlanHistory->setIsActive(!$PlanHistory->getIsActive());

		if ($PlanHistory->getIsActive()) {
			$test = $this->doctrine->em->createQueryBuilder()
				->update('Entities\PlanHistory', 'ph')
				->set('ph.is_active', '0')
				->where('ph.user = :user AND ph.is_active = :active')
				->setParameter('user', $PlanHistory->getUser())
				->setParameter('active', '1')
				->getQuery()
				->execute();
		}

		$this->doctrine->em->merge($PlanHistory);
		$this->doctrine->em->flush();

		$this->response(array('error' => false, 'message' => 'Successfully updated the plan history.', 'PlanHistory' => $PlanHistory), 200);
	}

	// @DELETE delete PlanHistory
	public function delete_delete($id = null) {
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.'), 400);
		}

		$PlanHistory = $this->doctrine->em->find('Entities\PlanHistory', (int)$id);
		if (is_null($PlanHistory)) {
			$this->response(array('error' => true, 'message' => 'PlanHistory not found.'), 400);
		}

		if ($PlanHistory->getUser() != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL)
			$this->response(array('error' => true, 'message' => "You are not allowed to do this."), 401);

		$this->doctrine->em->remove($PlanHistory);
		$this->doctrine->em->flush();

		$this->response(array('error' => false, 'message' => 'PlanHistory has been removed.'), 200);
	}

	// @GET plan
	public function plan_get($id = null) {
		if ($this->rest->level != ADMIN_KEY_LEVEL)
			$this->response(array('error' => true, 'message' => "You are not allowed to do this."), 401);

		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'Id not defined.'), 400);
		}

		$Plan = $this->doctrine->em->find('Entities\Plan', (int)$id);
		if (is_null($Plan)) {
			$this->response(array('error' => true, 'message' => 'Plan not found.'), 400);
		}

		$query = $this->doctrine->em->createQueryBuilder()
			->select('count(ph)')
			->from('Entities\PlanHistory', 'ph')
			->where('ph.plan = :plan AND ph.is_active = :active')
			->setParameter('plan', $Plan)
			->setParameter('active', '1')
			->getQuery();

		$res = $query->getSingleScalarResult();
		$this->response(array('error' => false, 'numbers' => $res), 200);
	}

	// @GET expires
	public function expires_get() {
		if ($this->rest->level != ADMIN_KEY_LEVEL)
			$this->response(array('error' => true, 'message' => "You are not allowed to do this."), 401);

		$expiration = new DateTime('now');
		$expiration->add(new DateInterval('P7D'));
		$query = $this->doctrine->em->createQueryBuilder()
			->select('ph')
			->from('Entities\PlanHistory', 'ph')
			->where('ph.expiration_plan_date < :expiration AND ph.is_active = :active')
			->setParameter('expiration', $expiration)
			->setParameter('active', '1')
			->getQuery();

		$res = $query->getArrayResult();
		$this->response(array('error' => false, 'numbers' => count($res), 'planHistories' => $res), 200);
	}
}