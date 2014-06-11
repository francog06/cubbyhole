<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/REST_Controller.php';
require_once APPPATH . '/controllers/api/data_history.php';

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
		$this->methods['preview_get']['key'] = FALSE;
		$this->methods['thumbnail_get']['key'] = FALSE;
	}

	public function shares_get($id = null) {
		$data = new StdClass();
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.', 'data' => $data), 400);
		}

		$file = $this->doctrine->em->find('Entities\File', (int)$id);
		if (is_null($file)) {
			$this->response(array('error' => true, 'message' => 'file not found', 'data' => $data), 404);
		}

		if ($file->getUser() != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL)
			$this->response(array('error' => true, 'message' => "You are not allowed to do this.", 'data' => $data), 401);

		$data->shares = $file->getShares()->toArray();
		$this->response(array('error' => false, 'message' => 'Successfully retrieved file details.', 'data' => $data), 200);
	}

	public function thumbnail_get($id = null) {
		$data = new StdClass();
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.', 'data' => $data), 400);
		}

		$file = $this->doctrine->em->find('Entities\File', (int)$id);
		if (is_null($file)) {
			$this->response(array('error' => true, 'message' => 'file not found', 'data' => $data), 404);
		}

		if ( file_exists($file->getAbsolutePath()) && is_file($file->getAbsolutePath()) ) {
			$size = getimagesize($file->getAbsolutePath());
			$source_image = false;

		    switch($size["mime"]) {
		    	case "image/jpeg":
				case "image/jpeg":
					$source_image = imagecreatefromjpeg($file->getAbsolutePath()); //jpeg file
				break;
				case "image/gif":
					$source_image = imagecreatefromgif($file->getAbsolutePath()); //gif file
				break;
				case "image/png":
					$source_image = imagecreatefrompng($file->getAbsolutePath()); //png file
				break;
				default: 
					$source_image=false;
				break;
		    }

		    if (!$source_image) {
		    	$this->response(array('error' => true, 'message' => 'file not an image', 'data' => $data), 400);
		    }

			$width = imagesx($source_image);
			$height = imagesy($source_image);
			
			$desired_height = floor($height * (200 / $width));
			$virtual_image = imagecreatetruecolor(200, 200);
			imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, 200, 200, $width, $height);
			header('Content-Type: ' . $size["mime"]);
			imagejpeg($virtual_image, NULL, 100);
		}
		else {
			$this->response(array('error' => true, 'message' => 'file not found (hard drive)', 'data' => $data), 404);
		}
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

		$share = $file->isSharedWith($this->rest->user);

		if ($file->getUser() != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL && !$share)
			$this->response(array('error' => true, 'message' => "Ceci n'est pas votre fichier et n'a pas été partagé avec vous.", 'data' => $data), 400);

		if ( file_exists($file->getAbsolutePath()) && is_file($file->getAbsolutePath()) ) {
		    $data = file_get_contents($file->getAbsolutePath());
		    force_download($file->getName(), $data);
		}
		else {
			$this->response(array('error' => true, 'message' => 'file not found (hard drive)', 'data' => $data), 404);
		}
	}

	function visitor_country() {
		    $ipaddress = '';
		    if (getenv('HTTP_CLIENT_IP'))
		        $ipaddress = getenv('HTTP_CLIENT_IP');
		    else if(getenv('HTTP_X_FORWARDED_FOR'))
		        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
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
	        return $result <> NULL ? $result : "Local";
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

		if (isset($_REQUEST['X-API-KEY'])) {
			$key = $_REQUEST['X-API-KEY'];

			if ( ! ($row = $this->rest->db->where(config_item('rest_key_column'), $key)->get(config_item('rest_keys_table'))->row()))
			{
				$this->response(array('error' => true, 'message' => "Vous ne pouvez pas télécharger ce fichier.", 'data' => $data), 400);
			}
			else
			{
				$user = $this->doctrine->em->find('Entities\User', (int)$row->user_id);
				if (is_null($user))
					$this->response(array('error' => true, 'message' => "User don't exist (APIKEY).", 'data' => $data), 400);

				$share = $file->isSharedWith($user);
				if ($user != $file->getUser() && !$share)
					$this->response(array('error' => true, 'message' => "Ceci n'est pas votre fichier et n'a pas été partagé avec vous.", 'data' => $data), 400);
			}
		}
		else {
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
		}

		if ($file->getIsPublic() || $file->getUser() != $user) {
			$start_of_day = new DateTime("now", new DateTimeZone("Europe/Berlin")); $start_of_day->setTime(0, 0);
			$end_of_day = new DateTime("now", new DateTimeZone("Europe/Berlin")); $end_of_day->setTime(23, 59);
			$now = new DateTime("now", new DateTimeZone("Europe/Berlin"));
			$downloads = $file->getDataHistories()->filter(function($e) use ($start_of_day, $end_of_day, $now) {
				return ( $start_of_day->getTimestamp() < $now->getTimestamp() && $now->getTimestamp() < $end_of_day->getTimestamp() );
			});

			$totalDownloaded = count($downloads->toArray()) * $file->getSize(); // Mo
			$planHistory = $file->getUser()->getActivePlanHistory();
			if ($totalDownloaded + $file->getSize() > $planHistory->getPlan()->getDailyDataTransfert()) {
				die("Vous ne pouvez pas télécharger ce fichier (Quotat dépassé)");
			}
		}

		$DataHistory_ip = $this->input->ip_address();
		$DataHistory_country = $this->visitor_country();
		if ( file_exists($file->getAbsolutePath()) && is_file($file->getAbsolutePath()) ) {

			$DataHistoryNew = new Entities\DataHistory;
			$DataHistoryNew->setDate(new DateTime('now', new DateTimeZone('Europe/Berlin')))
					->setIp($DataHistory_ip)
					->setCountry($DataHistory_country)
					->setFile($file);

			$this->doctrine->em->persist($DataHistoryNew);
			$this->doctrine->em->flush($DataHistoryNew);

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

		$share = $file->isSharedWith($this->rest->user);

		if ($file->getUser() != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL && !$share)
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

		$share = $share = $file->isSharedWith($this->rest->user);

		if ($file->getUser() != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL && (!$share || !$share->getIsWritable()))
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

		$share = $file->isSharedWith($this->rest->user);

		if ($file->getUser() != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL && (!$share || !$share->getIsWritable()))
			$this->response(array('error' => true, 'message' => "You are not allowed to do this.", 'data' => $data), 401);

		if ( ($folder_id = $this->post('folder_id')) !== false) {
			$folder = $this->doctrine->em->find('Entities\Folder', (int)$folder_id);
			if (is_null($folder)) {
				$this->response(array('error' => true, 'message' => 'Folder not found.', 'data' => $data), 404);
			}

			$shareFolder = $folder->isSharedWith($this->rest->user);

			if (!$shareFolder || !$shareFolder->getIsWritable() || $folder->getUser() != $file->getUser())
				$this->response(array('error' => true, 'message' => "You are not allowed to do this.", 'data' => $data), 401);

			$file->setFolder($folder);

			foreach ($folder->getShares()->toArray() as $shareApply) {
				$shareForFile = new Entities\Share;

				if ($share->getUser() != $shareApply->getUser() && $shareApply->getIsWritable() != $share->getIsWritable()) {
					$shareForFile->setIsWritable($shareApply->getIsWritable());
		            $shareForFile->setUser($shareApply->getUser());
		            $shareForFile->setOwner($shareApply->getOwner());
		            $shareForFile->setFile($file);
		            $shareForFile->setDate(new \DateTime("now", new \DateTimeZone("Europe/Berlin")));
		            $file->addShare($shareForFile);
		            $this->doctrine->em->persist($shareApply);
	        	}
			}
		}

		if ( ($user_id = $this->post('user_id')) !== false && $this->rest->level == ADMIN_KEY_LEVEL ) {
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
		else
			$user = $this->rest->user;

		if ( ($is_public = $this->post('is_public')) !== false && ($this->rest->user == $file->getUser() || $this->rest->level == ADMIN_KEY_LEVEL) ) {
			$file->setIsPublic( $is_public == "0" ? false : true );
			if ($file->getIsPublic() == true) {
				$file->setAccessKey(substr(md5(time()), 15));
			} else {
				$file->setAccessKey(null);
			}
		}

		if ( ($name = $this->post('name')) !== false && ($share->getIsWritable() || $this->rest->user == $file->getUser()) ) {
			$file->setName($name);
		}

		// Verify is the file is not too big for the plan
		if ( isset($_FILES['file']) && ($share->getIsWritable() || $this->rest->user == $file->getUser()) ) {
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
			$file->setSize(round( ($fileData['file_size'] * KB) / MB, 2));
			$file->setAbsolutePath($fileData['full_path']);
			$file->setCreationDate(new DateTime('now', new DateTimeZone('Europe/Berlin')));
			$file->setLastUpdateDate(new DateTime('now', new DateTimeZone('Europe/Berlin')));
			$file->setIsPublic(false);

			if ( ($folder_id = $this->input->post('folder_id')) ) {
				$folder = $this->doctrine->em->find('Entities\Folder', (int)$folder_id);
				if (is_null($folder)) {
					$this->response(array('error' => true, 'message' => 'folder not found.', 'data' => $data), 404);
				}

				$shareFolder = $folder->isSharedWith($this->rest->user);

				if ($folder->getUser() != $this->rest->user && (!$shareFolder || !$shareFolder->getIsWritable()) )
					$this->response(array('error' => true, 'message' => "You are not allowed to do this.", 'data' => $data), 401);

				$file->setFolder($folder);
				$file->setUser($folder->getUser());
				foreach ($folder->getShares()->toArray() as $shareToApply) {
					$shareForFile = new Entities\Share;

					$shareForFile->setIsWritable($shareToApply->getIsWritable());
		            $shareForFile->setUser($shareToApply->getUser());
		            $shareForFile->setOwner($shareToApply->getOwner());
		            $shareForFile->setFile($file);
		            $shareForFile->setDate(new \DateTime("now", new \DateTimeZone("Europe/Berlin")));
		            $file->addShare($shareForFile);
		            $this->doctrine->em->persist($shareToApply);
				}
			}

			$this->doctrine->em->persist($file);
			$this->doctrine->em->flush();
			$data->file = $file;
			$this->response(array('error' => false, 'message' => 'Fichier créé.', 'data' => $data), 200);
		}
	}
}