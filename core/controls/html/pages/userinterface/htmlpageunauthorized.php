<?php 


class HTMLPageUnauthorized extends HTMLPage {

	protected $_url;
	
	public function __construct() {
		
		parent::__construct();

		@ header('HTTP/1.0 401 Unauthorized');
		@ header('Status: 401 Unauthorized');
			
		$this->_url = ZPHP::get_absolute_actual_uri();
		
	}

	public function prepare_params() {
		
		parent::prepare_params();
		
		$this->set_param('url', $this->_url);
		
	}
	
	public function out()
	{
		header('HTTP/1.0 401 Unauthorized');
		parent::out();
	}		
			
}