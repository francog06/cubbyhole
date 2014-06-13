<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Event extends REST_Controller {

	function __construct()
	{
		parent::__construct();
	}

	public function synchronize_done_get() {
		$user = $this->rest->user;

		$user->setLastSynchronizeCall(new DateTime("now", new DateTimeZone("Europe/Berlin")));
		$this->doctrine->em->merge($user);
		$this->doctrine->em->flush();

		$this->response(array('error' => false, 'message' => "Mise à jour de la synchronisation réussie"), 200);
	}

	public function index_get() {
		$user = $this->rest->user;

		$userEvents = $user->getEvents()->filter(function($e) use($user) {
			if (!$user->getLastSynchronizeCall())
				return true;

			return $e->getDate()->getTimestamp() > $user->getLastSynchronizeCall()->getTimestamp();
		});

		$userEvents = $userEvents->filter(function($e) {
			if ( ($file_id = $e->getFileId()) ) {
				$entity = $this->doctrine->em->Find('Entities\File', (int)$file_id);
				return !(is_null($entity) && $e->getStatus() != "DELETE");
			}

			if ( ($folder_id = $e->getFolderId()) ) {
				$entity = $this->doctrine->em->Find('Entities\Folder', (int)$folder_id);
				return !(is_null($entity) && $e->getStatus() != "DELETE");
			}
			return false;
		});

		$userEvents->map(function($e) {
			$entity = null;
			$e->parent = null;
			if ($e->getStatus() != "DELETE") {
				if ( ($file_id = $e->getFileId()) ) {
					$entity = $this->doctrine->em->Find('Entities\File', (int)$file_id);
					$e->type = "file";
					if ( ($parent = $entity->getFolder()) )
						$e->parent = $parent->getId();
				}
				else if ( ($folder_id = $e->getFolderId()) ) {
					$entity = $this->doctrine->em->Find('Entities\Folder', (int)$folder_id);
					$e->type = "folder";
					if ( ($parent = $entity->getParent()) )
						$e->parent = $parent->getId();
				}
				else
					$e = null;
			}
			return $e;
		});

		$userEvents->filter(function($e) {
			return !is_null($e);
		});

		$data = new StdClass();
		$events = [];
		foreach ($userEvents as $event) {
			$events[] = $event;
		}
		$data->events = $events;
		$this->response(array('error' => false, 'message' => "Récupération des évènements réussie.", "data" => $data), 200);
	}
}