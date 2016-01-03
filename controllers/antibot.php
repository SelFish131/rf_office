<?php

class antibot extends Controller {

var $data = array ();

	function antibot()
	{
		parent::Controller();
        $this->load->library('captcha');
        $this->load->library('session');
	}
	function index()
	{
		$this->captcha->genimage();
	}
	function rnd($what)
	{
		$this->captcha->genimage();
	}	
}
?>