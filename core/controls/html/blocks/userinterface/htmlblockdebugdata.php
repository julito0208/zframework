<?php

class HTMLBlockDebugData extends HTMLControl
{
	protected $_debug_data;
	
	public function __construct($debug_data)
	{
		parent::__construct();	
		$this->_debug_data = $debug_data;
	}
	
	public function prepare_params()
	{
		parent::prepare_params();
		$this->set_param('debug_data', $this->_debug_data); 
	}
}