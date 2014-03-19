<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Folder extends CI_Model {
	public $id;
	public $name = "";
	public $creationDate = date(); //formattage
	public $relativePath = "";	
	public $absolutePath = "";	
	public $publicLinkPath = "";
}
