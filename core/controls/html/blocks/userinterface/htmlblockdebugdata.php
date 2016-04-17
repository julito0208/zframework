<?php

class HTMLBlockDebugData extends HTMLControl
{
	protected $_debug_data;
	protected $_content_len;
	
	public function __construct($debug_data, $content_len=null)
	{
		parent::__construct();	
		$this->_debug_data = $debug_data;
		$this->_content_len = $content_len;
	}
	
	public function prepare_params()
	{
		parent::prepare_params();
		$this->set_param('debug_data', $this->_debug_data);
		$this->set_param('content_len', $this->_content_len);
	}
}