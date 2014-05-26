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

		$DataHistory = $this->doctrine->em->find('Entities\DataHistory', $DataHistory_ip);
		if (is_null($DataHistory)) {
			$this->response(array('error' => true, 'message' => 'DataHistory not found.'), 400);
		}

		$File = $this->doctrine->em->find('Entities\File', $DataHistory_file);
		if (is_null($File)) {
			$this->response(array('error' => true, 'message' => 'File not found.'), 400);
		}

		$DataHistoryNew = new Entities\DataHistory;
		$DataHistoryNew->setDate(new DateTime('now', new DateTimeZone('Europe/Berlin')))
				->setIp($DataHistory_ip)
				->setCountry($DataHistory_country)
				->setFile($DataHistory_file);

		$this->doctrine->em->persist($DataHistoryNew);
		//$this->doctrine->em->flush();

		$this->response(array('error' => false, 'message' => 'DataHistory successfully created.', 'DataHistory' => $DataHistoryNew), 201);
	
	}

	// @UPDATE
	public function update_put($id = null)
	{
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.'), 400);
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
			$this->response(array('error' => true, 'message' => 'id not defined.'), 400);
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
			$this->response(array('error' => true, 'message' => 'id not defined.', 'data' => $data), 400);
		}

		$DataHistory = $this->doctrine->em->find('Entities\DataHistory', (int)$id);
		if (is_null($DataHistory)) {
			$this->response(array('error' => true, 'message' => 'DataHistory not found.', 'data' => $data), 400);
		}
		$data->DataHistory = $DataHistory;
		$this->response(array('error' => false, 'message' => 'DataHistory successfully retrieved', 'data' => $data), 200);
	}



	//RETRIEVE ALL IP WHOM DOWNLOADED A SPECIFIC FILE
	public function  stat_ip($StatIpFile, $dateBegin = null, $dateEnd = null)
	{
		$query = $this->doctrine->em->createQueryBuilder()
					->add('select', 'p.ip')
					->add('from', 'Entities\DataHistory p')
					->add('where', 'p.file = :file');
					if($dateBegin && $dateEnd)
					{
						 $query->add('where', 'p.date BETWEEN :dateBegin AND :dateEnd')
					    ->setParameter('dateBegin', new DateTime($dateBegin, new DateTimeZone('Europe/Berlin')))
					    ->setParameter('dateEnd', new DateTime($dateEnd, new DateTimeZone('Europe/Berlin')));
					}

		$query->setParameter('file', $StatIpFile)
		->getQuery();

		$result = $query->getArrayResult();
		$data = new StdClass();
		$data->ip = $result;
		if (empty($result)) 
			$this->response(array('error' => true, 'data' => $data), 400);
		else 
			$this->response(array('error' => false, 'data ' => $data), 200);
	}

	//RETRIEVE ALL FILES DOWNLOADED BEETWEEN TWO DATES 
	public function  stat_file($dateBegin, $dateEnd)
	{
		$query = $this->doctrine->em->createQueryBuilder()
					->add('select', 'p.file')
					->add('from', 'Entities\DataHistory p')
					->add('where', 'p.date = :date')
					->where('p.date BETWEEN :dateBegin AND :dateEnd')
				    ->setParameter('dateBegin', new DateTime($dateBegin, new DateTimeZone('Europe/Berlin')))
				    ->setParameter('dateEnd', new DateTime($dateEnd, new DateTimeZone('Europe/Berlin')))		
					->getQuery();

		$result = $query->getArrayResult();
		$data = new StdClass();
		$data->file = $result;
		if (empty($result)) 
			$this->response(array('error' => true, 'data' => $data), 400);
		else 
			$this->response(array('error' => false, 'data ' => $data), 200);
	}

	//RETRIEVE ALL DATA HISTORY BY COUNTRY CODE
	public function stat_DataHistory($countryCode, $dateBegin = null, $dateEnd = null)
	{

	$query = $this->doctrine->em->createQueryBuilder()
					->add('select', 'p')
					->add('from', 'Entities\DataHistory p')
					->add('where', 'p.country = :country');
					if($dateBegin && $dateEnd)
					{
						 $query->add('where', 'p.date BETWEEN :dateBegin AND :dateEnd')
					    ->setParameter('dateBegin', new DateTime($dateBegin, new DateTimeZone('Europe/Berlin')))
					    ->setParameter('dateEnd', new DateTime($dateEnd, new DateTimeZone('Europe/Berlin')));
					}
					$query->setParameter('country', $countryCode)
					->getQuery();

	$result = $query->getArrayResult();
	$data = new StdClass();
	$data->DataHistory = $result;
	if (empty($result)) 
		$this->response(array('error' => true, 'data' => $data), 400);
	else 
		$this->response(array('error' => false, 'data ' => $data), 200);

	}

	//RETRIEVE MOST DOWNLOAD FILE EVERYWHERE
	public function stat_mostDownloadFile($dateBegin = null, $dateEnd = null)
	{
		$query = $this->doctrine->em->createQueryBuilder()
			->select('count(dh.file)')
			->from('Entities\DataHistory', 'dh')
			->add('where', 'dh.is_active = :active');
			if($dateBegin && $dateEnd)
			{
				 $query->add('where', 'p.date BETWEEN :dateBegin AND :dateEnd')
			    ->setParameter('dateBegin', new DateTime($dateBegin, new DateTimeZone('Europe/Berlin')))
			    ->setParameter('dateEnd', new DateTime($dateEnd, new DateTimeZone('Europe/Berlin')));
			}

		$query->groupBy('dh.file')
			->orderBy( 'dh.file DESC')
			->setParameter('active', '1')
			->getQuery();

		$result = $query->getArrayResult();
		$data = new StdClass();
		$data->file = $result;
		if (empty($result)) 
			$this->response(array('error' => true, 'data' => $data), 400);
		else 
			$this->response(array('error' => false, 'data ' => $data), 200);
	}


	//RETRIEVE MOST DOWNLOAD FILE BY COUNTRY
	public function stat_mostDownloadFileByCountry($countryCode, $dateBegin = null, $dateEnd = null)
	{
		$query = $this->doctrine->em->createQueryBuilder()
			->select('count(dh.file)')
			->from('Entities\DataHistory', 'dh')
			->add('where', 'dh.is_active = :active AND dh.country =:country');
			if($dateBegin && $dateEnd)
			{
				 $query->add('where', 'p.date BETWEEN :dateBegin AND :dateEnd')
			    ->setParameter('dateBegin', new DateTime($dateBegin, new DateTimeZone('Europe/Berlin')))
			    ->setParameter('dateEnd', new DateTime($dateEnd, new DateTimeZone('Europe/Berlin')));
			}
		$query->groupBy('dh.file')
			->orderBy( 'dh.file DESC')
			->setParameter('country', $countryCode)
			->setParameter('active', '1')
			->getQuery();

		$result = $query->getArrayResult();
		$data = new StdClass();
		$data->file = $result;
		if (empty($result)) 
			$this->response(array('error' => true, 'data' => $data), 400);
		else 
			$this->response(array('error' => false, 'data ' => $data), 200);
	}





}