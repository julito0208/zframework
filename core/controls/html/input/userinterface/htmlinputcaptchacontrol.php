<?php

class HTMLInputCaptchaControl extends HTMLInputControl {

	const DEFAULT_NAME = 'captcha';

	public static function test_code($code, &$error=null)
	{
		if(ImageCaptchaControl::test_code($code, $code_incorrect, $code_expired))
		{
			return true;
		}
		else
		{
			if($code_incorrect)
			{
				$error = class_exists('ZfLanguageText') ? String::get('html_input_control_captcha_error_code') : 'El código de seguridad es incorrecto';
			}

			if($code_expired)
			{
				$error = class_exists('ZfLanguageText') ? String::get('html_input_control_captcha_error_expired') : 'El código de seguridad ha expirado';
			}

			return false;
		}

	}
	
	protected $_id_sufix;
	protected $_id_prefix;
	protected $_label;

	public function __construct($name=null, $id_sufix=null, $id_prefix=null) {
		
		parent::__construct();
		
		$this->set_name($name ? $name : self::DEFAULT_NAME);
		$this->set_id_sufix($id_sufix);
		$this->set_id_prefix($id_prefix);
	}

	public function get_id_sufix() {
		return $this->_id_sufix;
	}

	public function set_id_sufix($value) {
		$this->_id_sufix = $value;
		return $this;
	}
	
	public function get_id_prefix() {
		return $this->_id_prefix;
	}

	public function set_id_prefix($value) {
		$this->_id_prefix = $value;
		return $this;
	}
	
	public function get_text_id() {
		return ($this->_id_prefix ? $this->_id_prefix.'_' : '')."{$this->_name}_text".($this->_id_sufix ? '_'.$this->_id_sufix : '');
	}

	public function get_img_id() {
		return ($this->_id_prefix ? $this->_id_prefix.'_' : '')."{$this->_name}_img".($this->_id_sufix ? '_'.$this->_id_sufix : '');
		return $this;
	}



	public function prepare_params() {

		$captcha_config = ImageCaptchaControl::get_config_array();
		
		$this->set_param('id_sufix', $this->_id_sufix);
		$this->set_param('text_id', $this->get_text_id());
		$this->set_param('img_id', $this->get_img_id());
		$this->set_param('img_url', URLPattern::reverse(ImageCaptchaControl::URL_CAPTCHA_IMAGE));
		$this->set_param('width', $captcha_config['width']);
		$this->set_param('height', $captcha_config['height']);
		$this->set_param('label', $this->_label);
		
		parent::prepare_params();
		
	}

}