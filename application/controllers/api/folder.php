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

		if ($folder->getUser() != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL)
			$this->response(array('error' => true, 'message' => "You are not allowed to do this."), 401);

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

		if ($folder->getUser() != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL)
			$this->response(array('error' => true, 'message' => "You are not allowed to do this."), 401);

		$files = $folder->getFiles()->toArray();
		$this->doctrine->em->remove($folder);
		$this->doctrine->em->flush();

		$this->response(array('error' => false, 'message' => 'Folder has been removed.'), 200);
	}

	public function add_post() {
		$folder_name = $this->mandatory_value('name', 'post');

		$user = $this->rest->user;
		if (is_null($user)) {
			$this->response(array('error' => true, 'message' => 'user not found.'), 400);
		}

		$folder = new Entities\Folder;
		$folder->setUser($user);
		$folder->setName($folder_name);
		$folder->setCreationDate(new DateTime('now', new DateTimeZone('Europe/Berlin')));
		$folder->setLastUpdateDate(new DateTime('now', new DateTimeZone('Europe/Berlin')));
		$folder->setIsPublic(false);

		if ( ($folder_id = $this->input->post('folder_id')) !== false && !empty($folder_id)) {
			$parentFolder = $this->doctrine->em->find('Entities\Folder', (int)$folder_id);
			if (is_null($parentFolder)) {
				$this->response(array('error' => true, 'message' => 'Parent folder not found.'), 400);
			}

			$folder->setParent($parentFolder);
		}

		$this->doctrine->em->persist($folder);
		$this->doctrine->em->flush();
		$this->response(array('error' => false, 'message' => 'Dossier créé.' 'folder' => $folder), 200);
	}

	public function update_put($id = null) {
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.'), 400);
		}

		$folder = $this->doctrine->em->find('Entities\Folder', (int)$id);
		if (is_null($folder)) {
			$this->response(array('error' => true, 'message' => 'folder not found.'), 400);
		}

		if ( ($name = $this->put('name')) !== false )
			$folder->setName($name);

		if ( ($folder_id = $this->put('folder_id')) !== false) {
			if ((int)$folder_id == $folder->getId())
				$this->response(array('error' => true, 'message' => "You can't move a folder into himself"), 400);

			$parentFolder = $this->doctrine->em->find('Entities\Folder', (int)$folder_id);
			if (is_null($parentFolder)) {
				$this->response(array('error' => true, 'message' => 'Parent folder not found.'), 400);
			}

			$folder->setParent($parentFolder);
		}

		if ( ($user_id = $this->post('user_id')) !== false && $this->rest->level == ADMIN_KEY_LEVEL) {
			$user = $this->doctrine->em->find('Entities\User', (int)$user_id);
			if (is_null($user)) {
				$this->response(array('error' => true, 'message' => 'user not found.'), 400);
			}
			$folder->setUser($user);
		}

		if ( ($is_public = $this->put('is_public')) !== false ) {
			$folder->setIsPublic( $is_public == "0" ? false : true );
			if ($folder->getIsPublic() == true) {
				$folder->setAccessKey(substr(md5(time()), 15));
			} else {
				$folder->setAccessKey(null);
			}
		}

		$folder->setLastUpdateDate(new DateTime('now', new DateTimeZone('Europe/Berlin')));
		$this->doctrine->em->merge($folder);
		$this->doctrine->em->flush();

		$this->response(array('error' => false, 'message' => 'Dossier mis à jour.', 'folder' => $folder), 200);
	}

	public function user_root_get($user_id = null) {
		$user = $this->rest->user;
		if ( !is_null($user_id) && $this->rest->user->getId() != $user_id && $this->rest->level != ADMIN_KEY_LEVEL) {
			$this->response(array('error' => true, 'message' => "You're not allowed to do that."), 401);
		}

		if ( !is_null($user_id) && $this->rest->user->getId() != $user_id && $this->rest->level == ADMIN_KEY_LEVEL) {
			$user = $this->doctrine->em->find('Entities\User', (int)$user_id);
		if (is_null($user)) {
			$this->response(array('error' => true, 'message' => 'user not found.'), 400);
		}

		$folders = $user->getFolders()->filter(function($_) {
            return ($_->getParent() == null);
        });

        $files = $user->getFiles()->filter(function($_) {
           	return ($_->getFolder() == null);
        });

        $filesRet = [];
        foreach ($files as $file) {
        	$filesRet[] = $file;
        }

        $this->response(array('error' => false, 'folders' => $folders->toArray(), 'files' => $filesRet), 200);
	}
}