<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . '/libraries/REST_Controller.php';

/**
 * @class Share
 * @brief Toutes les méthodes possibles concernant les Share.
 */
class Share extends REST_Controller {

    /**
     * @fn __construct()
     * @brief Méthode de construction de Share
     */
    function __construct()
    {
        parent::__construct();

        $this->load->library('email');
        $this->load->helper('email');
    }

    /**
     * @fn index_get()
     * @brief Méthode pour récuperer tout les share.\n
     * @URL{cubbyhole.name/api/share}\n
     * @HTTPMethod{GET}
     * @return $data
     */
    public function index_get()
    {
        $data = new StdClass();
        $query = $this->doctrine->em->createQueryBuilder()
            ->add('select', 'sh')
            ->add('from', 'Entities\Share sh')
            ->getQuery();
        $result = $query->getArrayResult();
        $data->shares = $result;
        $this->response(array('error' => false, 'data' => $data), 200);
    }

    /**
     * @fn user_shares_get()
     * @brief Méthode pour récuperer tout les share d'un utilisateur.\n
     * @URL{api/user/details/:id/shares}\n
     * @HTTPMethod{GET}
     * @return $data
     */
    public function user_shares_get($id = null)
    {
        $data = new StdClass();
        if (is_null($id)) {
            $this->response(array('error' => true, 'message' => 'Vous n\'avez défini aucun ID', 'data' => $data), 400);
        }

        $user = $this->rest->user;

        if ($this->rest->level == ADMIN_KEY_LEVEL) 
            $user = $this->doctrine->em->find('Entities\User', (int)$id);

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

        $data->files = $filesSharedRet;
        $data->folders = $foldersSharedRet;
        $this->response(array('error' => false, 'message' => 'Récupérations des shares.', 'data' => $data), 200);
    }

    /**
     * @fn details_get()
     * @brief Méthode pour récuperer les infos d'un share donné.\n
     * @URL{cubbyhole.name/api/share/details:id}\n
     * @HTTPMethod{GET}
     * @param $id @REQUIRED
     * @return $data
     */
    public function details_get($id = null) {
        $data = new StdClass();
        if (is_null($id)) {
            $this->response(array('error' => true, 'message' => 'Vous n\'avez défini aucun ID', 'data' => $data), 400);
        }

        $share = $this->doctrine->em->find('Entities\Share', (int)$id);
        if (is_null($share)) {
            $this->response(array('error' => true, 'message' => 'Partage non trouvé.', 'data' => $data), 400);
        }

        $data->share = $share;
        $this->response(array('error' => false, 'message' => 'Récupération du partage réussi.', 'data' => $data), 200);
    }

    /**
     * @fn create_post()
     * @brief Méthode pour creer un share.\n
     * @URL{cubbyhole.name/api/share/create}\n
     * @HTTPMethod{POST}
     * @return $data
     */
    public function create_post() {
        $type = null;
        $data = new StdClass();
        $email = $this->mandatory_value('email', 'post');
        $write = $this->mandatory_value('write', 'post');
        $entity = null;

        try {
            $user = Entities\User::getByEmail($email);
        } catch (Exception $e) {
            $this->response(array('error' => true, 'message' => 'Utilisateur non trouvé.', 'data' => $data), 404);
        }
        if (is_null($user)) {
            $this->response(array('error' => true, 'message' => 'Utilisateur non trouvé.', 'data' => $data), 404);
        }

        if ($user == $this->rest->user)
            $this->response(array('error' => true, 'message' => 'Vous ne pouvez pas partager ce fichier a vous même.', 'data' => $data), 401);

        if ( ($file_id = $this->post('file')) !== false ) {
            $file = $this->doctrine->em->find('Entities\File', (int)$file_id);
            if (is_null($file)) {
                $this->response(array('error' => true, 'message' => 'Fichier non trouvé', 'data' => $data), 404);
            }

            if ($file->getUser() != $this->rest->user)
                $this->response(array('error' => true, 'message' => "Vous ne pouvez pas partager les fichiers d'un autre utilisateur. Namého", 'data' => $data), 400);
            $type = "file";
            $entity = $file;
        }

        if ( ($folder_id = $this->post('folder')) !== false && is_null($type) ) {
            $folder = $this->doctrine->em->find('Entities\Folder', (int)$folder_id);
            if (is_null($folder)) {
                $this->response(array('error' => true, 'message' => 'Dossier non trouvé', 'data' => $data), 404);
            }

            if ($folder->getUser() != $this->rest->user)
                $this->response(array('error' => true, 'message' => "Vous ne pouvez pas partager les dossiers d'un autre utilisateur. Namého", 'data' => $data), 400);
            $type = "folder";
            $entity = $folder;
        }

        if ($type == null) {
            $this->response(array('error' => true, 'message' => "Vous n'avez définie aucune entity (fichier ou dossier)", 'data' => $data), 400);
        }

        if (Entities\Share::entityAlreadyShared($entity, $user, $type)) {
            $this->response(array('error' => true, 'message' => "Vous partagez déjà cet entité avec cet utilisateur.", 'data' => $data), 400);
        }
        $share = new Entities\Share;
        $share->setOwner($this->rest->user);
        $share->setDate(new DateTime("now", new DateTimeZone("Europe/Berlin")));
        $share->setIsWritable( ($write == "1" ? true : false) );
        $share->setUser($user);

        if ($type == "folder") {
            $share->setFolder($folder);
            $folder->recursiveShare($share);
        }
        if ($type == "file")
            $share->setFile($file);

        $user->addSharedWithMe($share);
        $this->doctrine->em->merge($user);
        $this->doctrine->em->persist($share);
        $this->doctrine->em->flush();

        $this->email->clear();
        $this->email->initialize(array(
            'mailtype' => 'html',
            'charset'  => 'utf-8',
            'priority' => '1'
        ));
        $this->email->to($user->getEmail());
        $this->email->from('share@cubbyhole.name');
        $this->email->subject($user->getEmail() . ' veut partager un ' . $type . ' avec vous.');
        $this->email->message($this->load->view('layouts/email', array('user' => $user, 'share' => $share, 'type' => ($type == "folder" ? "dossier" : "fichier"), 'view' => 'email/share'), TRUE));
        @$this->email->send();

        $data->share = $share;
        $this->response(array('error' => false, 'message' => 'Partage créé avec succès', 'data' => $data), 200);
    }

    /**
     * @fn update_put()
     * @brief Méthode pour mettre a jour un share donné.\n
     * @URL{cubbyhole.name/api/share/update:id}\n
     * @HTTPMethod{PUT}
     * @param $id @REQUIRED
     * @return $data
     */
    public function update_put($id = null) {
        $data = new StdClass();
        if (is_null($id)) {
            $this->response(array('error' => true, 'message' => 'Vous n\'avez défini aucun ID', 'data' => $data), 400);
        }

        $share = $this->doctrine->em->find('Entities\Share', (int)$id);
        if (is_null($share)) {
            $this->response(array('error' => true, 'message' => 'Partage non trouvé.', 'data' => $data), 400);
        }

        $share->setIsWritable( ($this->put('write') == "1" ? true : false) );

        $this->doctrine->em->merge($share);
        $this->doctrine->em->flush();

        $data->share = $share;
        $this->response(array('error' => false, 'message' => 'Mise à jour du partage réussie.', 'data' => $data), 200);
    }

    /**
     * @fn delete_delete()
     * @brief Méthode pour supprimer un share donné.\n
     * @URL{cubbyhole.name/api/share/delete:id}\n
     * @HTTPMethod{DELETE}
     * @param $id @REQUIRED
     * @return $data
     */
    public function delete_delete($id = null) {
        $data = new StdClass();
        if (is_null($id)) {
            $this->response(array('error' => true, 'message' => 'Vous n\'avez défini aucun ID', 'data' => $data), 400);
        }

        $share = $this->doctrine->em->find('Entities\Share', (int)$id);
        if (is_null($share)) {
            $this->response(array('error' => true, 'message' => 'Partage non trouvé.', 'data' => $data), 400);
        }

        if ($share->getFolder() != null) {
            $share->getFolder()->removeShare($share);
            $share->getFolder()->recursiveShare(null, $share->getUser());
            $this->doctrine->em->merge($share->getFolder());
        }

        $this->doctrine->em->remove($share, $share->getUser());
        $this->doctrine->em->flush();

        $this->response(array('error' => false, 'message' => 'Partage supprimé.', 'data' => $data), 200);
    }
}