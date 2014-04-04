<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Users extends REST_Controller {
	function __construct()
	{
		parent::__construct();
	}

	public function index_get() {
		// Get
	}

	public function index_post() {
		// Post
	}

	public function index_update($id) {
		// Update
	}

	public function index_delete($id) {
		// Delete
	}
}