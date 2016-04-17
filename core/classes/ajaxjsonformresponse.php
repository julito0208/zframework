<?php

class AjaxJSONFormResponse extends AjaxJSONResponse implements AjaxResponse {
	
	const RESPONSE_SUCCESS_VARNAME = 'success';
	const RESPONSE_ERROR_VARNAME = 'error';
	
	public function __construct($data=array()) {
		parent::__construct($data);
		$this->set_success(false);
		$this->set_error(null);
	}
	
	public function set_success($success) {
		return $this->set_item_boolean(self::RESPONSE_SUCCESS_VARNAME, $success);
	}
	
	public function get_success() {
		return $this->get_item_boolean(self::RESPONSE_SUCCESS_VARNAME);
	}
	
	public function success($success=null)
	{
		return $this->set_success(func_num_args() > 0 ? $success : true);
	}
	
	public function set_error($error, $input=null, $group=null) {
		
		if(!$error) 
		{
			$this->set_item(self::RESPONSE_ERROR_VARNAME, null);
		}
		else if(func_num_args() > 1)
		{
			$this->set_item_array(self::RESPONSE_ERROR_VARNAME, array('error' => $error, 'input' => $input, 'group' => $group));
		}
		else
		{
			$this->set_item_string(self::RESPONSE_ERROR_VARNAME, $error);
		}
		
		return $this;
	}
	
	public function add_error($error, $input=null, $group=null)
	{
		if(func_num_args() > 1)
		{
			$error = array('error' => $error, 'input' => $input, 'group' => $group);
		}
		
		$existing_error = $this->get_item(self::RESPONSE_ERROR_VARNAME);
		
		if($existing_error && is_array($existing_error) && isset($existing_error['error']) && isset($existing_error['input']))
		{
			$new_error = array($existing_error, $error);
		}
		else if($existing_error && is_array($existing_error))
		{
			$new_error = $existing_error;
			$existing_error[] = $error;
		}
		else if($existing_error)
		{
			$new_error = array($existing_error, $error);
		}
		else
		{
			$new_error = $error;
		}
		
		$this->set_item(self::RESPONSE_ERROR_VARNAME, $new_error);
	}

	public function get_error() {
		return $this->get_item(self::RESPONSE_ERROR_VARNAME);
	}
	
	public function has_errors()
	{
		return (bool) $this->get_item(self::RESPONSE_ERROR_VARNAME);
	}

	public function out_error($error, $input=null, $group=null)
	{
		$args = func_get_args();
		call_user_func_array(array($this, 'set_error'), $args);
		$this->set_success(false);
		$this->out();
	}
	
}