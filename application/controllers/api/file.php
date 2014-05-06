<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class File extends REST_Controller {
	function __construct()
	{
		parent::__construct();

		$this->load->library(array('email'));
		$this->load->helper('email');
	}

	public function details_get($id = null) {
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.'), 400);
		}

		$file = $this->doctrine->em->find('Entities\File', (int)$id);
		if (is_null($file)) {
			$this->response(array('error' => true, 'message' => 'file not found.'), 400);
		}

		$this->response(array('error' => false, 'file' => $file));
	}

	public function remove_delete($id = null) {
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.'), 400);
		}

		$file = $this->doctrine->em->find('Entities\File', (int)$id);
		if (is_null($file)) {
			$this->response(array('error' => true, 'message' => 'file not found.'), 400);
		}

		@unlink($file->getAbsolutePath());
		$this->doctrine->em->remove($file);
		$this->doctrine->em->flush();

		$this->response(array('error' => false, 'message' => 'File has been removed.'), 200);
	}

	public function user_get($id = null) {
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.'), 400);
		}

		$user = $this->doctrine->em->find('Entities\User', (int)$id);
		if (is_null($user)) {
			$this->response(array('error' => true, 'message' => 'user not found.'), 400);
		}

		$this->response(array('error' => false, 'files' => $user->getFiles()->toArray()));
	}

	public function add_post() {
		$user_id = $this->mandatory_value('user_id', 'post');

		$config['upload_path'] = APPPATH . 'uploads/' . $user_id . "/";
		$config['allowed_types'] = '*';
		$config['max_size']	= '100';
		$config['overwrite'] = TRUE;
		$config['encrypt_name'] = FALSE;
		$config['file_name'] = substr(md5(time()), 15);

		$user = $this->doctrine->em->find('Entities\User', (int)$user_id);
		if (is_null($user)) {
			$this->response(array('error' => true, 'message' => 'user not found.'), 400);
		}

		if (!is_dir($config['upload_path'])) {
			mkdir($config['upload_path'], 0777, true);
		}

		$planHistory = $user->getActivePlanHistory($user);
		if (is_null($planHistory))
			$this->response(array('error' => true, 'message' => 'User has no active plan.'), 400);
		$plan = $planHistory->getPlan();

		// Verify is the file is not too big for the plan
		if (isset($_FILES['file'])) {
			$fileSize = $_FILES['file']['size'] / (1024 * 1024);

			if ($fileSize > $plan->getUsableStorageSpace() ||
				($fileSize + $user->getStorageUsed()) > $plan->getUsableStorageSpace())
				$this->response(array('error' => true, 'message' => 'Not enough space.'), 400);
		}
		else
			$this->response(array('error' => true, 'message' => 'file not found'), 400);

		$this->load->library('upload', $config);
		if ( ! $this->upload->do_upload('file')) {
			$this->response(array('error' => true, 'message' => $this->upload->display_errors('', '')), 400);
		} else {
			$fileData = $this->upload->data();
			$file = new Entities\File();

			$file->setUser($user);
			$file->setName($fileData['file_name']);
			$file->setSize($fileData['file_size']);
			$file->setAbsolutePath($fileData['full_path']);
			$file->setCreationDate(new DateTime('now', new DateTimeZone('Europe/Berlin')));
			$file->setLastUpdateDate(new DateTime('now', new DateTimeZone('Europe/Berlin')));
			$file->setIsPublic(false);

			if ( ($folder_id = $this->input->post('folder_id')) ) {
				$folder = $this->doctrine->em->find('Entities\Folder', (int)$folder_id);
				if (is_null($folder)) {
					$this->response(array('error' => true, 'message' => 'folder not found.'), 400);
				}

				$file->setFolder($folder);
			}

			$this->doctrine->em->persist($file);
			$this->doctrine->em->flush();
			$this->response(array('error' => false, 'file' => $file), 400);
		}
	}
}