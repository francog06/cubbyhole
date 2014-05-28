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

        $Share = $this->doctrine->em->find('Entities\Share', (int)$id);
        if (is_null($Share)) {
            $this->response(array('error' => true, 'message' => 'Share not found.', 'data' => $data), 400);
        }

        $data->share = $data;
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
        $user_id = $this->mandatory_value('user', 'post');
        $write = $this->mandatory_value('write', 'post');

        $user = $this->doctrine->em->find('Entities\User', (int)$user_id);

        if (is_null($user)) {
            $this->response(array('error' => true, 'message' => 'User not found', 'data' => $data), 404);
        }

        if ( ($file_id = $this->post('file')) !== false ) {
            $type = "type";
            $file = $this->doctrine->em->find('Entities\File', (int)$file_id);
            if (is_null($file)) {
                $this->response(array('error' => true, 'message' => 'File not found', 'data' => $data), 404);
            }
        }

        if ( ($folder_id = $this->post('folder')) !== false && is_null($fileOrFolder) ) {
            $folder = $this->doctrine->em->find('Entities\Folder', (int)$folder_id);
            if (is_null($folder)) {
                $this->response(array('error' => true, 'message' => 'Folder not found', 'data' => $data), 404);
            }
            $type = "folder";
        }

        $share = new Entities\Share;
        $share->setOwner($this->rest->user);
        $share->setDate(new DateTime("now", new DateTimeZone("Europe/Berlin")));

        if ($type == "folder")
            $share->setFolder($folder);
        if ($type == "file")
            $share->setFile($file);

        $share->setRead(true);
        $share->setWrite( ($write == "1" ? true : false) );
        $share->setUser($user);

        $this->doctrine->em->persist($share);
        $this->doctrine->em->flush();

        $data->share = $share;
        $this->response(array('error' => false, 'data' => $data), 200);
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

        if (isset($this->put('write'))) {
            $share->setWrite( ($this->put('write') == "1" ? true : false) );
        }

        $this->doctrine->em->merge($share);
        $this->doctrine->em->flush();
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