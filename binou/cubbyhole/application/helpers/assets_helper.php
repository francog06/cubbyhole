<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('css_url'))
{
	function css($nom)
	{
		return base_url() . 'assets/css/' . $nom . '.css';
	}
}

if ( ! function_exists('js_url'))
{
	function js($nom)
	{
		return base_url() . 'assets/js/' . $nom . '.js';
	}
}

if ( ! function_exists('img_url'))
{
	function img($nom)
	{
		return base_url() . 'assets/img/' . $nom;
	}
}