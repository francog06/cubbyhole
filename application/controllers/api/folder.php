<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Folder extends REST_Controller {
	function __construct()
	{
		parent::__construct();
	}

	public function details_get($id = null) {
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.'), 400);
		}

		$folder = $this->doctrine->em->find('Entities\Folder', (int)$id);
		if (is_null($folder)) {
			$this->response(array('error' => true, 'message' => 'folder not found.'), 400);
		}

		$this->response(array('error' => false, 'folder' => $folder), 200);
	}

	public function remove_delete($id = null) {
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.'), 400);
		}

		$folder = $this->doctrine->em->find('Entities\Folder', (int)$id);
		if (is_null($folder)) {
			$this->response(array('error' => true, 'message' => 'folder not found.'), 400);
		}

		$files = $folder->getFiles()->toArray();
		$user = $folder->getUser();
		foreach ($files as $file) {
			$this->doctrine->em->remove($file);
		}

		$this->doctrine->em->remove($folder);
		$this->doctrine->em->flush();

		$this->response(array('error' => false, 'message' => 'Folder has been removed.'), 200);
	}

	public function add_post() {
		$user_id = $this->mandatory_value('user_id', 'post');
		$folder_name = $this->mandatory_value('name', 'post');

		$user = $this->doctrine->em->find('Entities\User', (int)$user_id);
		if (is_null($user)) {
			$this->response(array('error' => true, 'message' => 'user not found.'), 400);
		}

		$folder = new Entities\Folder;
		$folder->setUser($user);
		$folder->setName($folder_name);
		$folder->setCreationDate(new DateTime('now', new DateTimeZone('Europe/Berlin')));
		$folder->setLastUpdateDate(new DateTime('now', new DateTimeZone('Europe/Berlin')));
		$folder->setIsPublic(false);

		if ( ($folder_id = $this->input->post('folder_id')) ) {
			$parentFolder = $this->doctrine->em->find('Entities\Folder', (int)$folder_id);
			if (is_null($parentFolder)) {
				$this->response(array('error' => true, 'message' => 'Parent folder not found.'), 400);
			}

			$folder->setParent($parentFolder);
		}

		$this->doctrine->em->persist($folder);
		$this->doctrine->em->flush();
		$this->response(array('error' => false, 'folder' => $folder), 200);
	}
}