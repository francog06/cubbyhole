<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('_p'))
{
	function _p($var)
	{
		echo "<pre>";
		var_dump($var);
		echo "</pre>";
	}
}

if ( ! function_exists('_d'))
{
	function _d($var)
	{
		_p($var);
		die();
	}
}
