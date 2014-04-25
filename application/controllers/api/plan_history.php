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

		$response = ["id" => $PlanHistory->getId(),
		"subscriptionPlanDate" => $PlanHistory->getSubscriptionPlanDate(),
		"expirationPlanDate" => $PlanHistory->getExpirationPlanDate(),
		"isActive" => $PlanHistory->getIsActive(),
		"user" => $PlanHistory->getUser(),
		"plan" => $PlanHistory->getPlan()];
		$this->response(array('error' => false, 'PlanHistory' => $response), 200);
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

		$planHistorys = $User->getPlanHistorys();
		$response = null;
		if (isset($planHistorys) && $planHistorys->count() > 0) {
			foreach ($planHistorys as $planHistory) {
				if($planHistory->getIsActive() == true) {
					$response = ["id" => $planHistory->getId(),
					"subscriptionPlanDate" => $planHistory->getSubscriptionPlanDate(),
					"expirationPlanDate" => $planHistory->getExpirationPlanDate(),
					"isActive" => $planHistory->getIsActive(),
					"user" => $planHistory->getUser(),
					"plan" => $planHistory->getPlan()];
				}
			}
			if (isset($response)) {
				$this->response(array('error' => false, 'PlanHistory' => $response), 200);
			} else {
				$this->response(array('error' => false, 'PlanHistory' => $response), 204);
			}
		} else {
			$this->response(array('error' => true, 'message' => 'This user does not have an active plan yet.'), 400);
		}
	}

	// @POST create
	public function create_post() {
		$PlanHistory_planId = $this->mandatory_value('planId', 'post');
		$PlanHistory_userId = $this->mandatory_value('userId', 'post');

		$User = $this->doctrine->em->find('Entities\User', (int)$PlanHistory_userId);
		if (is_null($User)) {
			$this->response(array('error' => true, 'message' => 'User not found.'), 400);
		}

		$Plan = $this->doctrine->em->find('Entities\Plan', (int)$PlanHistory_planId);
		if (is_null($Plan)) {
			$this->response(array('error' => true, 'message' => 'Plan not found.'), 400);
		}

		$query = $this->doctrine->em->createQueryBuilder()
					->add('SELECT', 'ph')
					->add('FROM', 'Entities\PlanHistory ph')
					->add('WHERE', 'ph.user = :user')
					->add('AND', 'ph.isActive = TRUE')
					->setParameter('user', $User)
					->getQuery();

		$result = $query->getArrayResult();
		if (!empty($result)) {
			foreach ($result as $activePlan) {
				$activePlan->setIsActive(false);
			}
		}
		$PlanHistory = new Entities\PlanHistory;

		$expiration = new DateTime('now');
		$expiration->add(new DateInterval('P'. $Plan->getDuration(). 'D'));
		$PlanHistory->setUser($User)
		->setPlan($Plan)
		->setSubscriptionPlanDate(new DateTime('now'))
		->setExpirationPlanDate($expiration)
		->setIsActive(true);

		$this->doctrine->em->persist($PlanHistory);
		$this->doctrine->em->flush();

		$User->addPlanHistory($PlanHistory);
		$response = ["id" => $planHistory->getId(),
		"subscriptionPlanDate" => $PlanHistory->getSubscriptionPlanDate(),
		"expirationPlanDate" => $PlanHistory->getExpirationPlanDate(),
		"isActive" => $PlanHistory->getIsActive(),
		"user" => $PlanHistory->getUser(),
		"plan" => $PlanHistory->getPlan()];
		$this->response(array('error' => false, 'message' => 'A new plan has been added.', 'PlanHistory' => $response), 201);
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

		$this->doctrine->em->remove($PlanHistory);

		// Does we will need remove PlanHistory's files & folders.
		$this->doctrine->em->flush();

		$this->response(array('error' => false, 'message' => 'PlanHistory has been removed.'), 200);
	}
}