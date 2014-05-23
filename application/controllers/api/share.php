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

        if ($Share->getUser() != $this->rest->user && $this->rest->level != ADMIN_KEY_LEVEL)
            $this->response(array('error' => true, 'message' => "You are not allowed to do this.", 'data' => $data), 401);

        $data->plan_history = $data;
        $this->response(array('error' => false, 'data' => $data), 200);
    }

    public function create_post() {

    }

    public function update_put($id = null) {

    }

    public function delete_delete($id = null) {

    }

    public function stats_get() {

    }
}