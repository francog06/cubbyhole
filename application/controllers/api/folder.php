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
			$this->response(array('error' => true, 'message' => 'Vous n\'avez défini aucun ID', 'data' => $data), 400);
		}

		$folder = $this->doctrine->em->find('Entities\Folder', (int)$id);
		if (is_null($folder)) {
			$this->response(array('error' => true, 'message' => 'Dossier non trouvé.', 'data' => $data), 404);
		}

		if ($folder->getUser() != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL)
			$this->response(array('error' => true, 'message' => "Vous n'êtes pas autorisé à modifier cela.", 'data' => $data), 401);

		$data->shares = $folder->getShares()->toArray();
		$this->response(array('error' => false, 'message' => 'Récupération du dossier réussie.', 'data' => $data), 200);
	}

	public function details_get($id = null) {
		$data = new StdClass();
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'Vous n\'avez défini aucun ID', 'data' => $data), 400);
		}

		$folder = $this->doctrine->em->find('Entities\Folder', (int)$id);
		if (is_null($folder)) {
			$this->response(array('error' => true, 'message' => 'Dossier non trouvé.', 'data' => $data), 400);
		}

		$share = $folder->isSharedWith($this->rest->user);
		if ($folder->getUser() != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL && !$share)
			$this->response(array('error' => true, 'message' => "Vous n'êtes pas autorisé à faire cette action.", 'data' => $data), 401);

		$data->folder = $folder;
		if ($share)
			$data->folder = $folder->showOnlyShared($this->rest->user);
		$this->response(array('error' => false, 'message' => 'Récupération du dossier réussie.', 'data' => $data), 200);
	}

	public function remove_delete($id = null) {
		$data = new StdClass();
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'Vous n\'avez défini aucun ID', 'data' => $data), 400);
		}

		$folder = $this->doctrine->em->find('Entities\Folder', (int)$id);
		if (is_null($folder)) {
			$this->response(array('error' => true, 'message' => 'Dossier non trouvé.', 'data' => $data), 400);
		}

		$share = $folder->isSharedWith($this->rest->user);

		if ($folder->getUser() != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL && !$share->getIsWritable())
			$this->response(array('error' => true, 'message' => "Vous n'êtes pas autorisé à faire cette action.", 'data' => $data), 401);

		$files = $folder->getFiles()->toArray();
		$this->doctrine->em->remove($folder);
		$this->doctrine->em->flush();

		$this->response(array('error' => false, 'message' => 'Dossier supprimé.', 'data' => $data), 200);
	}

	public function add_post() {
		$folder_name = $this->mandatory_value('name', 'post');
		$data = new StdClass();
		$user = $this->rest->user;

		if (is_null($user)) {
			$this->response(array('error' => true, 'message' => 'Utilisateur non trouvé.', 'data' => $data), 400);
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
				$this->response(array('error' => true, 'message' => 'Dossier parent non trouvé.', 'data' => $data), 400);
			}

			$shareParentFolder = $parentFolder->isSharedWith($user);
			if ($parentFolder->getUser() != $this->rest->user && (!$shareParentFolder || !$shareParentFolder->getIsWritable()) )
				$this->response(array('error' => true, 'message' => "Vous n'êtes pas autorisé à faire cela.", 'data' => $data), 401);

			$i = 1;
			$original_name = $folder_name;
			while ($parentFolder->hasFoldernameAlreadyTaken($folder_name)) {
				$folder_name = $original_name . '(' . $i . ')';
				$i++;
			}
			$folder->setName($folder_name);

			$folder->setParent($parentFolder);
			$folder->setUser($parentFolder->getUser());
			foreach ($parentFolder->getShares()->toArray() as $shareToApply) {
				$shareForFolder = new Entities\Share;

				$shareForFolder->setIsWritable($shareToApply->getIsWritable());
	            $shareForFolder->setUser($shareToApply->getUser());
	            $shareForFolder->setOwner($shareToApply->getOwner());
	            $shareForFolder->setFolder($folder);
	            $shareForFolder->setDate(new \DateTime("now", new \DateTimeZone("Europe/Berlin")));
	            $folder->addShare($shareForFolder);
	            $this->doctrine->em->persist($shareToApply);
			}
		}
		else
		{
			$i = 1;
			$original_name = $folder_name;
			while ($this->rest->user->hasFoldernameInRoot($folder_name)) {
				$folder_name = $original_name . '(' . $i . ')';
				$i++;
			}
			$folder->setName($folder_name);
		}

		$this->doctrine->em->persist($folder);
		$this->doctrine->em->flush();

		$data->folder = $folder;
		$this->response(array('error' => false, 'message' => 'Dossier créé.', 'data' => $data), 200);
	}

	public function update_put($id = null) {
		$data = new StdClass();
		if (is_null($id)) {
			$this->response(array('error' => true, 'message' => 'Vous n\'avez défini aucun ID', 'data' => $data), 400);
		}

		$folder = $this->doctrine->em->find('Entities\Folder', (int)$id);
		if (is_null($folder)) {
			$this->response(array('error' => true, 'message' => 'Dossier non trouvé.', 'data' => $data), 400);
		}

		$share = $folder->isSharedWith($this->rest->user);

		if ($folder->getUser() != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL && (!$share || !$share->getIsWritable()))
			$this->response(array('error' => true, 'message' => "Vous n'êtes pas autorisé à faire cela.", 'data' => $data), 401);

		if ( ($name = $this->put('name')) !== false && $name != $folder->getName()) {
			$i = 1;
			$original_name = $name;
			if ($folder->getParent() != null) {
				while ($folder->getParent()->hasFoldernameAlreadyTaken($name)) {
					$name = $original_name . '(' . $i . ')';
					$i++;
				}
			}
			else {
				while ($folder->getUser()->hasFoldernameInRoot($name)) {
					$name = $original_name . '(' . $i . ')';
					$i++;
				}
			}
			$folder->setName($name);
		}

		if ( ($folder_id = $this->put('folder_id')) !== false) {
			if ((int)$folder_id == $folder->getId())
				$this->response(array('error' => true, 'message' => "Vous ne pouvez pas déplacer un dossier dans lui même.", 'data' => $data), 400);

			if ( $folder_id == "null") {
				$i = 1;
				$folder_name = $folder->getName();
				$original_name = $folder_name;
				while ($folder->getUser()->hasFoldernameInRoot($folder_name)) {
					$folder_name = $original_name . '(' . $i . ')';
					$i++;
				}
				$folder->setName($folder_name);
				$folder->setParent(null);
			}
			else {
				$parentFolder = $this->doctrine->em->find('Entities\Folder', (int)$folder_id);
				if (is_null($parentFolder)) {
					$this->response(array('error' => true, 'message' => 'Dossier parent non trouvé.', 'data' => $data), 400);
				}

				if ($parentFolder != $folder->getParent()) {
					$shareFolder = $parentFolder->isSharedWith($this->rest->user);

					if ($parentFolder->getUser() != $this->rest->user && (!$shareFolder || !$shareFolder->getIsWritable()) )
						$this->response(array('error' => true, 'message' => "Vous n'êtes pas autorisé à effectuer cet action.", 'data' => $data), 401);

					$i = 1;
					$original_name = $folder->getName();
					$name = $folder->getName();
					while ($parentFolder->hasFoldernameAlreadyTaken($name)) {
						$name = $original_name . '(' . $i . ')';
						$i++;
					}
					$folder->setName($name);
					$folder->setParent($parentFolder);
					foreach ($parentFolder->getShares()->toArray() as $shareToApply) {
						if ( $this->rest->user == $folder->getUser() || $share && $share->getUser() != $shareToApply->getUser() ) {
							if ( !$folder->searchShareByUser($shareToApply->getUser()) ) {
								$shareForFolder = new Entities\Share;

								$shareForFolder->setIsWritable($shareToApply->getIsWritable());
					            $shareForFolder->setUser($shareToApply->getUser());
					            $shareForFolder->setOwner($shareToApply->getOwner());
					            $shareForFolder->setFolder($folder);
					            $shareForFolder->setDate(new \DateTime("now", new \DateTimeZone("Europe/Berlin")));
					            $folder->addShare($shareForFolder);
					            $this->doctrine->em->persist($shareToApply);
				        	}
				        }
					}
					$folder->setUser($parentFolder->getUser());
					foreach ($folder->getShares() as $share) {
						$share->setOwner($parentFolder->getUser());
						$this->doctrine->em->merge($share);
					}
				}
			}
		}

		if ( ($user_id = $this->post('user_id')) !== false && $this->rest->level == ADMIN_KEY_LEVEL) {
			$user = $this->doctrine->em->find('Entities\User', (int)$user_id);
			if (is_null($user)) {
				$this->response(array('error' => true, 'message' => 'Utilisateur non trouvé.', 'data' => $data), 400);
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
			$this->response(array('error' => true, 'message' => "Vous n'êtes pas autoriser à effectuer cela.", 'data' => $data), 401);
		}

		if ( !is_null($user_id) && $this->rest->user->getId() != $user_id && $this->rest->level == ADMIN_KEY_LEVEL) 
			$user = $this->doctrine->em->find('Entities\User', (int)$user_id);
		if (is_null($user)) {
			$this->response(array('error' => true, 'message' => 'Utilisateur non trouvé.', 'data' => $data), 400);
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
        $sharedFolders = $user->getSharedWithMe()->filter(function($e) use($user) {
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