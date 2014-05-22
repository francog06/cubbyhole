<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
require_once APPPATH . './data_history.php' 

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

		$this->load->helper(['file', 'download']);

		$this->methods['download_get']['key'] = FALSE;
	}

	public function preview_get($id = null) {
		$specialHash = "ab14d0415c485464a187d5a9c97cc27c";

		$data = new StdClass();
		if ( ($hash = $this->input->get('hash')) === false && $hash != $specialHash )
			$this->response(array('error' => true, 'message' => "You are not allowed to do this.", 'data' => $data), 401);

		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.', 'data' => $data), 400);
		}

		$file = $this->doctrine->em->find('Entities\File', (int)$id);
		if (is_null($file)) {
			$this->response(array('error' => true, 'message' => 'file not found', 'data' => $data), 404);
		}

		if ( file_exists($file->getAbsolutePath()) && is_file($file->getAbsolutePath()) ) {
			$fileMime = getimagesize($file->getAbsolutePath());

			if ($fileMime !== false) {
			    header('Content-Type: ' . $fileMime['mime']);
			    header('Expires: 0');
			    header('Cache-Control: must-revalidate');
			    header('Pragma: public');
			    header('Content-Length: ' . filesize($file->getAbsolutePath()));
			    flush();
			    set_time_limit(0);

			    readfile($file->getAbsolutePath());
			    exit;
			}
			else
				$this->response(array('error' => true, 'message' => 'No preview available.', 'data' => $data), 400);
		}
		else {
			$this->response(array('error' => true, 'message' => 'file not found (hard drive)', 'data' => $data), 404);
		}
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
			$this->response(array('error' => true, 'message' => 'file not found', 'data' => $data), 404);
		}

		if ( file_exists($file->getAbsolutePath()) && is_file($file->getAbsolutePath()) ) {
		    $data = file_get_contents($file->getAbsolutePath());
		    force_download($file->getName(), $data);
		}
		else {
			$this->response(array('error' => true, 'message' => 'file not found (hard drive)', 'data' => $data), 404);
		}
	}

	function get_client_ip() {
	    $ipaddress = '';
	    if (getenv('HTTP_CLIENT_IP'))
	        $ipaddress = getenv('HTTP_CLIENT_IP');
	    else if(getenv('HTTP_X_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_X_FORWARDED_FOR'&quot;);
	    else if(getenv('HTTP_X_FORWARDED'))
	        $ipaddress = getenv('HTTP_X_FORWARDED');
	    else if(getenv('HTTP_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_FORWARDED_FOR');
	    else if(getenv('HTTP_FORWARDED'))
	       $ipaddress = getenv('HTTP_FORWARDED');
	    else if(getenv('REMOTE_ADDR'))
	        $ipaddress = getenv('REMOTE_ADDR');
	    else
	        $ipaddress = 'UNKNOWN';
	    return $ipaddress;
	}

function visitor_country() {
	    $ipaddress = '';
	    if (getenv('HTTP_CLIENT_IP'))
	        $ipaddress = getenv('HTTP_CLIENT_IP');
	    else if(getenv('HTTP_X_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_X_FORWARDED_FOR'&quot;);
	    else if(getenv('HTTP_X_FORWARDED'))
	        $ipaddress = getenv('HTTP_X_FORWARDED');
	    else if(getenv('HTTP_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_FORWARDED_FOR');
	    else if(getenv('HTTP_FORWARDED'))
	       $ipaddress = getenv('HTTP_FORWARDED');
	    else if(getenv('REMOTE_ADDR'))
	        $ipaddress = getenv('REMOTE_ADDR');
	    else
	        $ipaddress = 'UNKNOWN';
        $result = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ipaddress))
                ->geoplugin_countryCode;
        return $result <> NULL ? $result : "Unknown";
}



	public function download_get($id = null) {
		$data = new StdClass();

		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.', 'data' => $data), 400);
		}

		$file = $this->doctrine->em->find('Entities\File', (int)$id);
		if (is_null($file)) {
			show_404();
		}

		if (!$file->getIsPublic()) {
			$this->response(array('error' => true, 'message' => 'This file is not public.', 'data' => $data), 400);
		}

		if ( ($access_key = $this->input->get('accessKey')) !== false ) {
			if ($access_key != $file->getAccessKey())
				$this->response(array('error' => true, 'message' => 'Invalid access key.', 'data' => $data), 400);
		}
		else {
			$this->response(array('error' => true, 'message' => 'No access key.', 'data' => $data), 400);
		}

		/**
		 * TODO: share
		*/
	/*
	TO TEST
		$DataHistory_ip = $this->get_client_ip();
		$DataHistory_country = $this->visitor_country();
		if ( file_exists($file->getAbsolutePath()) && is_file($file->getAbsolutePath()) ) {

			$DataHistoryNew = new Entities\DataHistory;
			$DataHistoryNew->setDate(new DateTime('now', new DateTimeZone('Europe/Berlin')))
					->setIp($DataHistory_ip)
					->setCountry($DataHistory_country)
					->setFile($file)

			$this->doctrine->em->persist($DataHistoryNew); */

		    $data = file_get_contents($file->getAbsolutePath());
		    force_download($file->getName(), $data);
		    exit;
		}
		else {
			show_404();
		}
	}

	public function details_get($id = null) {
		$data = new StdClass();
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.', 'data' => $data), 400);
		}

		$file = $this->doctrine->em->find('Entities\File', (int)$id);
		if (is_null($file)) {
			$this->response(array('error' => true, 'message' => 'file not found.', 'data' => $data), 404);
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
			$this->response(array('error' => true, 'message' => 'file not found.', 'data' => $data), 404);
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
			$this->response(array('error' => true, 'message' => 'file not found.', 'data' => $data), 404);
		}

		if ($file->getUser() != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL)
			$this->response(array('error' => true, 'message' => "You are not allowed to do this.", 'data' => $data), 401);

		if ( ($folder_id = $this->post('folder_id')) !== false ) {
			$folder = $this->doctrine->em->find('Entities\Folder', (int)$folder_id);
			if (is_null($folder)) {
				$this->response(array('error' => true, 'message' => 'Folder not found.', 'data' => $data), 404);
			}
			$file->setFolder($folder);
		}

		if ( ($share_id = $this->post('share_id')) !== false ) {
			$share = $this->doctrine->em->find('Entities\Share', (int)$share_id);
			if (is_null($share)) {
				$this->response(array('error' => true, 'message' => 'Share not found.', 'data' => $data), 404);
			}
			$file->setShare($share);
		}

		if ( ($user_id = $this->post('user_id')) !== false && $this->rest->level == ADMIN_KEY_LEVEL) {
			$user = $this->doctrine->em->find('Entities\User', (int)$user_id);
			if (is_null($user)) {
				$this->response(array('error' => true, 'message' => 'user not found.', 'data' => $data), 404);
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
			$this->response(array('error' => true, 'message' => 'user not found.', 'data' => $data), 404);
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
			$this->response(array('error' => true, 'message' => 'file not found', 'data' => $data), 404);

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
					$this->response(array('error' => true, 'message' => 'folder not found.', 'data' => $data), 404);
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