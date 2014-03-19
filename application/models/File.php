<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class File extends CI_Model {
	public $id;
	public $name = "";
	public $creationDate = date(); //formattage? US? FR?
	public $lastModifificationDate = date();
	public $relativePath = "";	
	public $absolutePath = "";	
	public $publicLinkPath = "";
}
