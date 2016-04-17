<?php

class HTMLPageErrorException extends HTMLPageBlank 
{
	protected $_exception;
	protected $_block_error_exception;
	
	public function __construct(Exception $ex)
	{
		parent::__construct();
		$this->_exception = $ex;
		$this->_block_error_exception = new HTMLBlockErrorExceptionControl($ex);
		$this->set_title('Error');
	}
	
	public function prepare_params()
	{
		$this->set_param('block_error_exception', $this->_block_error_exception);
	}
}

