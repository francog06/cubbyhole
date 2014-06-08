<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Folder extends REST_Controller {
	function __construct()
	{
		parent::__construct();
	}

	public function shares_get($id = null) {
		$data = new StdClass();
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.', 'data' => $data), 400);
		}

		$folder = $this->doctrine->em->find('Entities\Folder', (int)$id);
		if (is_null($folder)) {
			$this->response(array('error' => true, 'message' => 'folder not found', 'data' => $data), 404);
		}

		if ($folder->getUser() != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL)
			$this->response(array('error' => true, 'message' => "You are not allowed to do this.", 'data' => $data), 401);

		$data->shares = $folder->getShares()->toArray();
		$this->response(array('error' => false, 'message' => 'Successfully retrieved folder details.', 'data' => $data), 200);
	}

	public function details_get($id = null) {
		$data = new StdClass();
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.', 'data' => $data), 400);
		}

		$folder = $this->doctrine->em->find('Entities\Folder', (int)$id);
		if (is_null($folder)) {
			$this->response(array('error' => true, 'message' => 'folder not found.', 'data' => $data), 400);
		}

		$shared_with_me = false;
		$user = $this->rest->user;
		$shares = $folder->getShares()->filter(function($e) use($user) {
			return $e->getUser() == $user;
		});

		if ( count($shares->toArray()) == 1 )
			$shared_with_me = true;

		if ($folder->getUser() != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL && !$shared_with_me)
			$this->response(array('error' => true, 'message' => "You are not allowed to do this.", 'data' => $data), 401);

		$data->folder = $folder;
		$this->response(array('error' => false, 'message' => 'Successfully retrieved folder details', 'data' => $data), 200);
	}

	public function remove_delete($id = null) {
		$data = new StdClass();
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.', 'data' => $data), 400);
		}

		$folder = $this->doctrine->em->find('Entities\Folder', (int)$id);
		if (is_null($folder)) {
			$this->response(array('error' => true, 'message' => 'folder not found.', 'data' => $data), 400);
		}

		if ($folder->getUser() != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL)
			$this->response(array('error' => true, 'message' => "You are not allowed to do this.", 'data' => $data), 401);

		$files = $folder->getFiles()->toArray();
		$this->doctrine->em->remove($folder);
		$this->doctrine->em->flush();

		$this->response(array('error' => false, 'message' => 'Folder has been removed.', 'data' => $data), 200);
	}

	public function add_post() {
		$folder_name = $this->mandatory_value('name', 'post');
		$data = new StdClass();
		$user = $this->rest->user;

		if (is_null($user)) {
			$this->response(array('error' => true, 'message' => 'user not found.', 'data' => $data), 400);
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
				$this->response(array('error' => true, 'message' => 'Parent folder not found.', 'data' => $data), 400);
			}

			$folder->setParent($parentFolder);
		}

		$this->doctrine->em->persist($folder);
		$this->doctrine->em->flush();

		$data->folder = $folder;
		$this->response(array('error' => false, 'message' => 'Dossier créé.', 'data' => $data), 200);
	}

	public function update_put($id = null) {
		$data = new StdClass();
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'id not defined.', 'data' => $data), 400);
		}

		$folder = $this->doctrine->em->find('Entities\Folder', (int)$id);
		if (is_null($folder)) {
			$this->response(array('error' => true, 'message' => 'folder not found.', 'data' => $data), 400);
		}

		if ( ($name = $this->put('name')) !== false && ($shared_with_me || $this->rest->user == $file->getUser()) )
			$folder->setName($name);

		if ( ($folder_id = $this->put('folder_id')) !== false && $this->rest->user == $folder->getUser()) {
			if ((int)$folder_id == $folder->getId())
				$this->response(array('error' => true, 'message' => "You can't move a folder into himself", 'data' => $data), 400);

			if ( $folder_id == "null")
				$folder->setParent(null);
			else {
				$parentFolder = $this->doctrine->em->find('Entities\Folder', (int)$folder_id);
				if (is_null($parentFolder)) {
					$this->response(array('error' => true, 'message' => 'Parent folder not found.', 'data' => $data), 400);
				}
				$folder->setParent($parentFolder);
			}
		}

		if ( ($user_id = $this->post('user_id')) !== false && $this->rest->level == ADMIN_KEY_LEVEL) {
			$user = $this->doctrine->em->find('Entities\User', (int)$user_id);
			if (is_null($user)) {
				$this->response(array('error' => true, 'message' => 'user not found.', 'data' => $data), 400);
			}
			$folder->setUser($user);
		}

		if ( ($is_public = $this->put('is_public')) !== false && $this->rest->user == $folder->getUser()) {
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

		$data->folder = $folder;
		$this->response(array('error' => false, 'message' => 'Dossier mis à jour.', 'data' => $data), 200);
	}

	public function user_root_get($user_id = null) {
		$data = new StdClass();
		$user = $this->rest->user;

		if ( !is_null($user_id) && $user->getId() != $user_id && $this->rest->level != ADMIN_KEY_LEVEL) {
			$this->response(array('error' => true, 'message' => "You're not allowed to do that.", 'data' => $data), 401);
		}

		if ( !is_null($user_id) && $this->rest->user->getId() != $user_id && $this->rest->level == ADMIN_KEY_LEVEL) 
			$user = $this->doctrine->em->find('Entities\User', (int)$user_id);
		if (is_null($user)) {
			$this->response(array('error' => true, 'message' => 'user not found.', 'data' => $data), 400);
		}

		// User files & folders
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

        $foldersRet = [];
        foreach ($folders as $folder) {
        	$foldersRet[] = $folder;
        }

        // Files & folder shared with the user
        $sharedFolders = $user->getSharedWithMe()->filter(function($e) {
        	if ( ($folder = $e->getFolder()) != null) {
        		$parentFolder = $folder->getParent();

        		if ($parentFolder == null)
        			return true;

        		$shares = $parentFolder->getShares()->filter(function($e) use($user) {
        			return $e->getUser() != $user;
        		});

        		if ( count($shares) == 1 ) {
        			return true;
        		}
        		else
        			return false;
        	}
        	return false;
        });

        $sharedFiles = $user->getSharedWithMe()->filter(function($e) use($user) {
        	if ( ($file = $e->getFile()) != null) {
        		$folder = $file->getFolder();

        		if (is_null($folder))
        			return true;

        		$shares = $folder->getShares()->filter(function($e) use($user) {
        			return $e->getUser() != $user;
        		});

        		if ( count($shares) == 1 ) {
        			return true;
        		}
        		else
        			return false;
        	}
        	return false;
        });

        $filesSharedRet = [];
        foreach ($sharedFiles->toArray() as $file) {
        	$filesSharedRet[] = $file;
        }

        $foldersSharedRet = [];
        foreach ($sharedFolders->toArray() as $folder) {
        	$foldersSharedRet[] = $folder;
        }

        $data->folders = $foldersRet;
        $data->files = $filesRet;
        $data->shares = array('folders' => $foldersSharedRet, 'files' => $filesSharedRet);
        $this->response(array('error' => false, 'data' => $data), 200);
	}
}