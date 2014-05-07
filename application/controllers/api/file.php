<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class File extends REST_Controller {
	function __construct()
	{
		parent::__construct();

		$this->load->library(array('email'));
		$this->load->helper('email');

		$this->uploadConfig = [
			'allowed_types' => '*',
			'max_size'	=> '0',
			'overwrite' => TRUE,
			'encrypt_name' => FALSE,
			'file_name' => substr(md5(time()), 15)
		];
	}

	public function details_get($id = null) {
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.'), 400);
		}

		$file = $this->doctrine->em->find('Entities\File', (int)$id);
		if (is_null($file)) {
			$this->response(array('error' => true, 'message' => 'file not found.'), 400);
		}

		$this->response(array('error' => false, 'file' => $file), 200);
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

		$this->response(array('error' => false, 'files' => $user->getFiles()->toArray()), 200);
	}

	public function update_post($id = null) {
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.'), 400);
		}

		$file = $this->doctrine->em->find('Entities\File', (int)$id);
		if (is_null($file)) {
			$this->response(array('error' => true, 'message' => 'file not found.'), 400);
		}

		if ( ($folder_id = $this->post('folder_id')) !== false ) {
			$folder = $this->doctrine->em->find('Entities\Folder', (int)$folder_id);
			if (is_null($folder)) {
				$this->response(array('error' => true, 'message' => 'Folder not found.'), 400);
			}
			$file->setFolder($folder);
		}

		if ( ($share_id = $this->post('share_id')) !== false ) {
			$share = $this->doctrine->em->find('Entities\Share', (int)$share_id);
			if (is_null($share)) {
				$this->response(array('error' => true, 'message' => 'Share not found.'), 400);
			}
			$file->setShare($share);
		}

		if ( ($user_id = $this->post('user_id')) !== false ) {
			$user = $this->doctrine->em->find('Entities\User', (int)$user_id);
			if (is_null($user)) {
				$this->response(array('error' => true, 'message' => 'user not found.'), 400);
			}
			$file->setUser($user);
		}

		if ( ($is_public = $this->post('is_public')) !== false ) {
			$file->setIsPublic( $is_public == "0" ? false : true );
			if ($file->getIsPublic() == true) {
				$file->setAccessKey(substr(md5(time()), 15));
			} else {
				$file->setAccessKey(null);
			}
		}

		// Verify is the file is not too big for the plan
		if (isset($_FILES['file'])) {
			$fileSize = $_FILES['file']['size'] / (1024 * 1024);
			$fileName = $_FILES['file']['name'];

			$planHistory = $file->getUser()->getActivePlanHistory();
			if (is_null($planHistory))
				$this->response(array('error' => true, 'message' => 'User has no active plan.'), 400);

			$plan = $planHistory->getPlan();
			if ($fileSize > $plan->getUsableStorageSpace() ||
				($fileSize + $file->getUser()->getStorageUsed()) > $plan->getUsableStorageSpace())
				$this->response(array('error' => true, 'message' => 'Not enough space.'), 400);
		}

		$this->uploadConfig['upload_path'] = APPPATH . 'uploads/' . $file->getUser()->getId() . "/";
		if (!is_dir($this->uploadConfig['upload_path'])) {
			mkdir($this->uploadConfig['upload_path'], 0777, true);
		}

		$this->load->library('upload', $this->uploadConfig);
		if ( $this->upload->do_upload('file')) {
			@unlink($file->getAbsolutePath());
			$fileData = $this->upload->data();

			$file->setName($fileName);
			$file->setSize(round($fileData['file_size'] / 1024, 2));
			$file->setAbsolutePath($fileData['full_path']);
		}

		$file->setLastUpdateDate(new DateTime('now', new DateTimeZone('Europe/Berlin')));
		$this->doctrine->em->merge($file);
		$this->doctrine->em->flush($file);
		$this->response(array('error' => false, 'file' => $file), 200);
	}

	public function add_post() {
		$user_id = $this->mandatory_value('user_id', 'post');

		$this->uploadConfig['upload_path'] = APPPATH . 'uploads/' . $user_id . "/";

		$user = $this->doctrine->em->find('Entities\User', (int)$user_id);
		if (is_null($user)) {
			$this->response(array('error' => true, 'message' => 'user not found.'), 400);
		}

		if (!is_dir($this->uploadConfig['upload_path'])) {
			mkdir($this->uploadConfig['upload_path'], 0777, true);
		}

		$planHistory = $user->getActivePlanHistory();
		if (is_null($planHistory))
			$this->response(array('error' => true, 'message' => 'User has no active plan.'), 400);
		$plan = $planHistory->getPlan();

		// Verify is the file is not too big for the plan
		if (isset($_FILES['file'])) {
			$fileName = $_FILES['file']['name'];
			$fileSize = $_FILES['file']['size'] / (1024 * 1024);

			if ($fileSize > $plan->getUsableStorageSpace() ||
				($fileSize + $user->getStorageUsed()) > $plan->getUsableStorageSpace())
				$this->response(array('error' => true, 'message' => 'Not enough space.'), 400);
		}
		else
			$this->response(array('error' => true, 'message' => 'file not found'), 400);

		$this->load->library('upload', $this->uploadConfig);
		if ( ! $this->upload->do_upload('file')) {
			$this->response(array('error' => true, 'message' => $this->upload->display_errors('', '')), 400);
		} else {
			$fileData = $this->upload->data();
			$file = new Entities\File();

			$file->setUser($user);
			$file->setName($fileName);
			$file->setSize(round($fileData['file_size'] / 1024, 2));
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
			$this->response(array('error' => false, 'file' => $file), 200);
		}
	}
}