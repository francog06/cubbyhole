<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class File extends REST_Controller {
	function __construct()
	{
		parent::__construct();

		$this->uploadConfig = [
			'allowed_types' => '*',
			'max_size'	=> '0',
			'overwrite' => TRUE,
			'encrypt_name' => FALSE,
			'file_name' => substr(md5(time()), 15)
		];

		$this->methods['download_get']['key'] = FALSE;
	}

	public function synchronize_get($id = null) {
		$specialHash = "ab14d0415c485464a187d5a9c97cc27c";

		$data = new StdClass();
		if ( ($hash = $this->input->get('hash')) === false && $hash != $specialHash )
			$this->response(array('error' => true, 'message' => "You are not allowed to do this.", 'data' => $data), 401);

		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.', 'data' => $data), 400);
		}

		$file = $this->doctrine->em->find('Entities\File', (int)$id);
		if (is_null($file)) {
			$this->response(array('error' => true, 'message' => 'file not found.', 'data' => $data), 400);
		}

		if ( file_exists($file->getAbsolutePath()) && is_file($file->getAbsolutePath()) ) {
		    header('Content-Description: File Transfer');
		    header('Content-Type: application/octet-stream');
		    header('Content-Disposition: attachment; filename='.basename($file->getName()));
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate');
		    header('Pragma: public');
		    header('Content-Length: ' . filesize($file->getAbsolutePath()));
		    flush();
		    set_time_limit(0);

		    readfile($file->getAbsolutePath());
		    exit;
		}
		else {
			$this->response(array('error' => true, 'message' => 'file not found.', 'data' => $data), 400);
		}
	}

	public function download_get($id = null) {
		$data = new StdClass();
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.', 'data' => $data), 400);
		}

		$file = $this->doctrine->em->find('Entities\File', (int)$id);
		if (is_null($file)) {
			$this->response(array('error' => true, 'message' => 'file not found.', 'data' => $data), 400);
		}

		/**
		 * TODO: Verify access_key / share / is_public
		 */

		if ( file_exists($file->getAbsolutePath()) && is_file($file->getAbsolutePath()) ) {
		    header('Content-Description: File Transfer');
		    header('Content-Type: application/octet-stream');
		    header('Content-Disposition: attachment; filename='.basename($file->getName()));
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate');
		    header('Pragma: public');
		    header('Content-Length: ' . filesize($file->getAbsolutePath()));
		    flush();
		    set_time_limit(0);

		    // set the download rate limit (=> 20,5 kb/s)
		    $planHistory = $file->getUser()->getActivePlanHistory();

		    if (!is_null($planHistory))
				$download_rate = $planHistory->getPlan()->getMaxBandwith();
			else
				$download_rate = 10;

			$fileLocal = fopen($file->getAbsolutePath(), "r");
		    while (!feof($fileLocal)) {
		        print fread($fileLocal, round($download_rate * KB));
		        flush();
		        sleep(1);
		    }  
		    exit;
		}
		else {
			$this->response(array('error' => true, 'message' => 'file not found.', 'data' => $data), 400);
		}
	}

	public function details_get($id = null) {
		$data = new StdClass();
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.', 'data' => $data), 400);
		}

		$file = $this->doctrine->em->find('Entities\File', (int)$id);
		if (is_null($file)) {
			$this->response(array('error' => true, 'message' => 'file not found.', 'data' => $data), 400);
		}

		if ($file->getUser() != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL)
			$this->response(array('error' => true, 'message' => "You are not allowed to do this.", 'data' => $data), 401);

		$data->file = $file;
		$this->response(array('error' => false, 'message' => 'Successfully retrieved file details.', 'data' => $data), 200);
	}

	public function remove_delete($id = null) {
		$data = new StdClass();
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.', 'data' => $data), 400);
		}

		$file = $this->doctrine->em->find('Entities\File', (int)$id);
		if (is_null($file)) {
			$this->response(array('error' => true, 'message' => 'file not found.', 'data' => $data), 400);
		}

		if ($file->getUser() != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL)
			$this->response(array('error' => true, 'message' => "You are not allowed to do this.", 'data' => $data), 401);

		@unlink($file->getAbsolutePath());
		$this->doctrine->em->remove($file);
		$this->doctrine->em->flush();

		$this->response(array('error' => false, 'message' => 'File has been removed.', 'data' => $data), 200);
	}

	public function update_post($id = null) {
		$data = new StdClass();
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.', 'data' => $data), 400);
		}

		$file = $this->doctrine->em->find('Entities\File', (int)$id);
		if (is_null($file)) {
			$this->response(array('error' => true, 'message' => 'file not found.', 'data' => $data), 400);
		}

		if ($file->getUser() != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL)
			$this->response(array('error' => true, 'message' => "You are not allowed to do this.", 'data' => $data), 401);

		if ( ($folder_id = $this->post('folder_id')) !== false ) {
			$folder = $this->doctrine->em->find('Entities\Folder', (int)$folder_id);
			if (is_null($folder)) {
				$this->response(array('error' => true, 'message' => 'Folder not found.', 'data' => $data), 400);
			}
			$file->setFolder($folder);
		}

		if ( ($share_id = $this->post('share_id')) !== false ) {
			$share = $this->doctrine->em->find('Entities\Share', (int)$share_id);
			if (is_null($share)) {
				$this->response(array('error' => true, 'message' => 'Share not found.', 'data' => $data), 400);
			}
			$file->setShare($share);
		}

		if ( ($user_id = $this->post('user_id')) !== false && $this->rest->level == ADMIN_KEY_LEVEL) {
			$user = $this->doctrine->em->find('Entities\User', (int)$user_id);
			if (is_null($user)) {
				$this->response(array('error' => true, 'message' => 'user not found.', 'data' => $data), 400);
			}
			$file->setUser($user);

			$uploadPath = APPPATH . 'uploads/' . $user->getId() . "/";
			if (!is_dir($uploadPath)) {
				mkdir($uploadPath, 0777, true);
			}
			@rename($file->getAbsolutePath(), $uploadPath . basename($file->getAbsolutePath()));
			$file->setAbsolutePath(realpath($uploadPath . basename($file->getAbsolutePath())));
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
			$fileSize = $_FILES['file']['size']; // Valeur octale
			$fileName = $_FILES['file']['name'];

			$planHistory = $file->getUser()->getActivePlanHistory();
			if (is_null($planHistory))
				$this->response(array('error' => true, 'message' => 'User has no active plan.', 'data' => $data), 400);

			$plan = $planHistory->getPlan();
			if ($fileSize > $plan->getUsableStorageSpace() * GB ||
				($fileSize + ($user->getStorageUsed() * MB) ) > $plan->getUsableStorageSpace() * GB)
				$this->response(array('error' => true, 'message' => 'Not enough space.', 'data' => $data), 400);
		}

		$this->uploadConfig['upload_path'] = APPPATH . 'uploads/' . $file->getUser()->getId() . "/";
		if (!is_dir($this->uploadConfig['upload_path'])) {
			mkdir($this->uploadConfig['upload_path'], 0777, true);
		}

		$this->load->library('upload', $this->uploadConfig);
		if ( $this->upload->do_upload('file') ) {
			@unlink($file->getAbsolutePath());
			$fileData = $this->upload->data();

			$file->setName($fileName);
			$file->setSize(round($fileData['file_size'] / MB, 2));
			$file->setAbsolutePath($fileData['full_path']);
		}

		$file->setLastUpdateDate(new DateTime('now', new DateTimeZone('Europe/Berlin')));
		$this->doctrine->em->merge($file);
		$this->doctrine->em->flush($file);
		$data->file = $file;
		$this->response(array('error' => false, 'message' => 'Fichier mis à jour.', 'data' => $data), 200);
	}

	public function add_post() {
		$data = new StdClass();
		$user = $this->rest->user;
		if (is_null($user)) {
			$this->response(array('error' => true, 'message' => 'user not found.', 'data' => $data), 400);
		}

		$this->uploadConfig['upload_path'] = APPPATH . 'uploads/' . $user->getId() . "/";

		if (!is_dir($this->uploadConfig['upload_path'])) {
			mkdir($this->uploadConfig['upload_path'], 0777, true);
		}

		$planHistory = $user->getActivePlanHistory();
		if (is_null($planHistory))
			$this->response(array('error' => true, 'message' => 'User has no active plan.', 'data' => $data), 400);
		$plan = $planHistory->getPlan();

		// Verify is the file is not too big for the plan
		if (isset($_FILES['file'])) {
			$fileName = $_FILES['file']['name'];
			$fileSize = $_FILES['file']['size']; // Valeur octale
			if ($fileSize > $plan->getUsableStorageSpace() * GB ||
				($fileSize + ($user->getStorageUsed() * MB) ) > $plan->getUsableStorageSpace() * GB)
				$this->response(array('error' => true, 'message' => 'Not enough space.', 'data' => $data), 400);
		}
		else
			$this->response(array('error' => true, 'message' => 'file not found', 'data' => $data), 400);

		$this->load->library('upload', $this->uploadConfig);
		if ( ! $this->upload->do_upload('file')) {
			$this->response(array('error' => true, 'message' => $this->upload->display_errors('', ''), 'data' => $data), 400);
		} else {
			$fileData = $this->upload->data();
			$file = new Entities\File();

			$file->setUser($user);
			$file->setName($fileName);
			$file->setSize(round($fileData['file_size'] / MB, 2));
			$file->setAbsolutePath($fileData['full_path']);
			$file->setCreationDate(new DateTime('now', new DateTimeZone('Europe/Berlin')));
			$file->setLastUpdateDate(new DateTime('now', new DateTimeZone('Europe/Berlin')));
			$file->setIsPublic(false);

			if ( ($folder_id = $this->input->post('folder_id')) ) {
				$folder = $this->doctrine->em->find('Entities\Folder', (int)$folder_id);
				if (is_null($folder)) {
					$this->response(array('error' => true, 'message' => 'folder not found.', 'data' => $data), 400);
				}

				$file->setFolder($folder);
			}

			$this->doctrine->em->persist($file);
			$this->doctrine->em->flush();
			$data->file = $file;
			$this->response(array('error' => false, 'message' => 'Fichier créé.', 'data' => $data), 200);
		}
	}
}