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
            $this->response(array('error' => true, 'message' => 'Id not defined.', 'data' => $data), 400);
        }

        $user = $this->rest->user;

        if ($this->rest->level == ADMIN_KEY_LEVEL) 
            $user = $this->doctrine->em->find('Entities\User', (int)$user_id);

        $this->response(array('error' => false, 'message' => 'Successfully retrieved shares.', 'data' => $data), 200);
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
            $this->response(array('error' => true, 'message' => 'Id not defined.', 'data' => $data), 400);
        }

        $share = $this->doctrine->em->find('Entities\Share', (int)$id);
        if (is_null($share)) {
            $this->response(array('error' => true, 'message' => 'Share not found.', 'data' => $data), 400);
        }

        $data->share = $share;
        $this->response(array('error' => false, 'data' => $data), 200);
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
            $this->response(array('error' => true, 'message' => 'User not found', 'data' => $data), 404);
        }
        if (is_null($user)) {
            $this->response(array('error' => true, 'message' => 'User not found', 'data' => $data), 404);
        }

        if ( ($file_id = $this->post('file')) !== false ) {
            $file = $this->doctrine->em->find('Entities\File', (int)$file_id);
            if (is_null($file)) {
                $this->response(array('error' => true, 'message' => 'File not found', 'data' => $data), 404);
            }

            if ($file->getUser() != $this->rest->user)
                $this->response(array('error' => true, 'message' => "You can't share other user's file", 'data' => $data), 400);
            $type = "file";
            $entity = $file;
        }

        if ( ($folder_id = $this->post('folder')) !== false && is_null($type) ) {
            $folder = $this->doctrine->em->find('Entities\Folder', (int)$folder_id);
            if (is_null($folder)) {
                $this->response(array('error' => true, 'message' => 'Folder not found', 'data' => $data), 404);
            }

            if ($folder->getUser() != $this->rest->user)
                $this->response(array('error' => true, 'message' => "You can't share other user's folder", 'data' => $data), 400);
            $type = "folder";
            $entity = $folder;
        }

        if ($type == null) {
            $this->response(array('error' => true, 'message' => "Vous n'avez définie aucune entity (fichier ou dossier)", 'data' => $data), 400);
        }

        if (Entities\Share::entityAlreadyShared($entity->getId(), $user->getId(), $type)) {
            $this->response(array('error' => true, 'message' => "Vous partagez déjà cet entité avec cet utilisateur.", 'data' => $data), 400);
        }
        $share = new Entities\Share;
        $share->setOwner($this->rest->user);
        $share->setDate(new DateTime("now", new DateTimeZone("Europe/Berlin")));

        if ($type == "folder")
            $share->setFolder($folder);
        if ($type == "file")
            $share->setFile($file);

        $share->setIsWritable( ($write == "1" ? true : false) );
        $share->setUser($user);

        $this->doctrine->em->persist($share);
        $this->doctrine->em->flush();

        // TODO: Send email with template, etc...

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
            $this->response(array('error' => true, 'message' => 'Id not defined.', 'data' => $data), 400);
        }

        $share = $this->doctrine->em->find('Entities\Share', (int)$id);
        if (is_null($share)) {
            $this->response(array('error' => true, 'message' => 'Share not found.', 'data' => $data), 400);
        }

        $share->setIsWritable( ($this->put('write') == "1" ? true : false) );

        $this->doctrine->em->merge($share);
        $this->doctrine->em->flush();

        $data->share = $share;
        $this->response(array('error' => false, 'data' => $data), 200);
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
            $this->response(array('error' => true, 'message' => 'id not defined.', 'data' => $data), 400);
        }

        $share = $this->doctrine->em->find('Entities\Share', (int)$id);
        if (is_null($share)) {
            $this->response(array('error' => true, 'message' => 'Share not found.', 'data' => $data), 400);
        }

        $this->doctrine->em->remove($share);
        $this->doctrine->em->flush();

        $this->response(array('error' => false, 'message' => 'Share has been removed.', 'data' => $data), 200);
    }

    /**
     * @fn stats_get()
     * @brief Méthode pour recuperer des stats.\n
     * @URL{cubbyhole.name/api/share/stats}\n
     * @HTTPMethod{GET}
     * @return $data
     */
    public function stats_get() {

    }
}