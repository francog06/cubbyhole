<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';


class Plan extends REST_Controller {
	
	function __construct()
	{
		parent::__construct();

		$this->methods['index_get']['key'] = FALSE;
		$this->methods['details_get']['key'] = FALSE;
	}

	public function index_get() 
	{
		$query = $this->doctrine->em->createQueryBuilder()
					->add('select', 'p')
					->add('from', 'Entities\Plan p')
					->getQuery();
		$result = $query->getArrayResult();
		$this->response(array('error' => false, 'plans' => $result), 200);
	}

	public function update_put($id = null)
	{
		if ($this->rest->level != ADMIN_KEY_LEVEL)
			$this->response(array('error' => true, 'message' => "You can't update a plan"), 401);

		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.'), 400);
		}

		$plan = $this->doctrine->em->find('Entities\Plan', (int)$id);
		if (is_null($plan)) {
			$this->response(array('error' => true, 'message' => 'plan not found.'), 400);
		}

		if ( ($name = $this->put('name')) !== false ) {
			$plan->setName($name);
		}
		if ( ($price = $this->put('price')) !== false ) {
			$plan->setPrice($price);
		}
		if ( ($duration = $this->put('duration')) !== false ) {
			$plan->setDuration($duration);
		}
		if ( ($usable_storage_space = $this->put('usable_storage_space')) !== false ) {
			$plan->setUsableStorageSpace($usable_storage_space);
		}
		if ( ($max_bandwidth = $this->put('max_bandwidth')) !== false ) {
			$plan->setMaxBandwidth($max_bandwidth);
		}
		if ( ($daily_data_transfert = $this->put('daily_data_transfert')) !== false ) {
			$plan->setDailyDataTransfert($daily_data_transfert);
		}
		if ( ($description = $this->put('description')) !== false ) {
			$plan->setDescription($description);
		}
		if ( ($is_active = $this->put('is_active')) !== false ) {
			$plan->setIsActive( ($is_active == "0") ? false : true );
		}
		if ( ($is_default = $this->put('is_default')) !== false ) {
			$plan->setIsDefault( ($is_default == "0") ? false : true );

			if ($plan->getIsDefault()) {
				$this->doctrine->em->createQueryBuilder()
					->update('Entities\Plan', 'p')
					->set('p.is_default', '0')
					->where('p.is_default = :default')
					->setParameter('default', '1')
					->getQuery()
					->execute();

				$plan->setIsActive(true);
			}
		}

		$this->doctrine->em->merge($plan);
		$this->doctrine->em->flush();
		$this->response(array('error' => false, 'message' => 'plan updated successfully.', 'plan' => $plan), 200);

	}

	public function details_get($id = null) 
	{
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.'), 400);
		}

		$plan = $this->doctrine->em->find('Entities\Plan', (int)$id);
		if (is_null($plan)) {
			$this->response(array('error' => true, 'message' => 'plan not found.'), 400);
		}

		$this->response(array('error' => false, 'plan' => $plan), 200);
	}

	 public function create_post()
	 {
	 	if ($this->rest->level != ADMIN_KEY_LEVEL)
			$this->response(array('error' => true, 'message' => "You can't create a plan"), 401);

		$plan_name = $this->mandatory_value('name', 'post');
		$plan_description = $this->mandatory_value('description', 'post');
		$plan_price = $this->mandatory_value('price', 'post');
		$plan_duration = $this->mandatory_value('duration', 'post');
		$plan_usable_storage_space = $this->mandatory_value('usable_storage_space', 'post');
		$plan_max_bandwidth = $this->mandatory_value('max_bandwidth', 'post');
		$plan_daily_data_transfert = $this->mandatory_value('daily_data_transfert', 'post');
		$plan_is_default = $this->mandatory_value('is_default', 'post');
		$plan_is_active = $this->mandatory_value('is_active', 'post');

		$query = $this->doctrine->em->createQueryBuilder()
					->add('select', 'p')
					->add('from', 'Entities\Plan p')
					->add('where', 'p.name = :name')
					->setParameter('name', $plan_name)
					->getQuery();

		$result = $query->getArrayResult();
		if (!empty($result)) {
			$this->response(array('error' => true, 'message' => 'A plan  with that name is already specified.'), 400);
		} else {
			$plan = new Entities\Plan;

			$plan->setName($plan_name)
				->setPrice($plan_price)
				->setDuration($plan_duration)
				->setUsableStorageSpace($plan_usable_storage_space)
				->setMaxBandwidth($plan_max_bandwidth)
				->setDailyDataTransfert($plan_daily_data_transfert)
				->setDescription($plan_description)
				->setIsDefault($plan_is_default)
				->setIsActive($plan_is_active);

			$this->doctrine->em->persist($plan);
			$this->doctrine->em->flush();

			$this->response(array('error' => false, 'message' => 'Plan successfully created.', 'plan' => $plan), 201);
		}
	 }

	public function delete_delete($id = null)
	{
		if ($this->rest->level != ADMIN_KEY_LEVEL)
			$this->response(array('error' => true, 'message' => "You can't delete a plan"), 401);

		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.'), 400);
		}

		$plan = $this->doctrine->em->find('Entities\Plan', (int)$id);
		if (is_null($plan)) {
			$this->response(array('error' => true, 'message' => 'plan not found.'), 400);
		}	 	

		$this->doctrine->em->remove($plan);
		$this->doctrine->em->flush();
		$this->response(array('error' => false, 'message' => 'Plan: '. $plan->getName() . ' has been removed.'), 200);

	}
}