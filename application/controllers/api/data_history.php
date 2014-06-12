<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/REST_Controller.php';

class Data_history extends REST_Controller {
	function __construct()
	{
		parent::__construct();
	}

	//@GET ALL PLAN
	public function index_get() 
	{
		$query = $this->doctrine->em->createQueryBuilder()
					->add('select', 'p')
					->add('from', 'Entities\DataHistory p')
					->getQuery();
		$result = $query->getArrayResult();
		$this->response(array('error' => false, 'DataHistory' => $result), 200);
	}

	// @POST create
	public function create_post() {
		$DataHistory_ip = $this->mandatory_value('ip', 'post');
		$DataHistory_country = $this->mandatory_value('country', 'post');
		$DataHistory_file = $this->mandatory_value('file', 'post');

		$File = $this->doctrine->em->find('Entities\File', $DataHistory_file);
		if (is_null($File)) {
			$this->response(array('error' => true, 'message' => 'Fichier non trouvé.'), 400);
		}

		$DataHistoryNew = new Entities\DataHistory;
		$DataHistoryNew->setDate(new DateTime('now', new DateTimeZone('Europe/Berlin')))
				->setIp($DataHistory_ip)
				->setCountry($DataHistory_country)
				->setFile($DataHistory_file);

		$this->doctrine->em->persist($DataHistoryNew);
		$this->doctrine->em->flush();

		$data->data_history = $DataHistoryNew;
		$this->response(array('error' => false, 'message' => 'DataHistory successfully created.', 'data' => $DataHistoryNew), 201);
	
	}

	// @UPDATE
	public function update_put($id = null)
	{
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'Vous n\'avez défini aucun ID'), 400);
		}

		$DataHistory = $this->doctrine->em->find('Entities\DataHistory', (int)$id);
		if (is_null($DataHistory)) {
			$this->response(array('error' => true, 'message' => 'Data History not found.'), 400);
		}

		if ( ($ip = $this->put('ip')) !== false ) {
			$DataHistory->setIp($ip);
		}
		if ( ($country = $this->put('country')) !== false ) {
			$DataHistory->setCountry($country);
		}
		if ( ($file = $this->put('file')) !== false ) {
			$DataHistory->setFile($file);
		}

		if ( ($is_default = $this->put('is_default')) !== false ) {
			$DataHistory->setIsDefault( ($is_default == "0") ? false : true );

			if ($DataHistory->getIsDefault()) {
				$this->doctrine->em->createQueryBuilder()
					->update('Entities\DataHistory', 'p')
					->set('p.is_default', '0')
					->add('where' ,'p.is_default = :default')
					->setParameter('default', '1')
					->getQuery()
					->execute();

				$DataHistory->setIsActive(true);
			}
		}

		$this->doctrine->em->merge($DataHistory);
		$this->doctrine->em->flush();
		$this->response(array('error' => false, 'message' => 'Data History updated successfully.', 'DataHistory' => $DataHistory), 200);

	}


	// @DELETE delete DataHistory
	public function delete_delete($id = null) {
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'Vous n\'avez défini aucun ID'), 400);
		}

		$DataHistory = $this->doctrine->em->find('Entities\DataHistory', (int)$id);
		if (is_null($DataHistory)) {
			$this->response(array('error' => true, 'message' => 'Data History not found.'), 400);
		}

		$this->doctrine->em->remove($DataHistory);
		$this->doctrine->em->flush();

		$this->response(array('error' => false, 'message' => 'DataHistory has been removed.'), 200);
	}

	// @GET details
	public function details_get($id = null) {
		$data = new StdClass();
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'Vous n\'avez défini aucun ID', 'data' => $data), 400);
		}

		$DataHistory = $this->doctrine->em->find('Entities\DataHistory', (int)$id);
		if (is_null($DataHistory)) {
			$this->response(array('error' => true, 'message' => 'DataHistory not found.', 'data' => $data), 400);
		}
		$data->DataHistory = $DataHistory;
		$this->response(array('error' => false, 'message' => 'DataHistory successfully retrieved', 'data' => $data), 200);
	}
}