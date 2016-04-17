<?php

class TextControl extends MVParamsContentControl implements MIMEControl {

	protected $_charset;
	protected $_mimetype;
	
	public function __construct($params) {
		parent::__construct($params);
		$this->set_charset(ZPHP::get_config('text_charset'));
		$this->set_mimetype(ZPHP::get_config('text_mimetype'));
	}
	
	//------------------------------------------------------------------------------------------------------------------------------------
	
	public function __set($name, $value=null) { 
		if($name == 'html') {
			$this->set_html($value);
		} else {
			return parent::__set($name, $value);
		}
	}
	
	public function __get($name) { 
		if($name == 'html') {
			return $this->get_html();
		} else {
			return parent::__get($name);
		}
	}
	
	public function __unset($name) { 
		if($name == 'html') {
			$this->clear_html();
		} else {
			return parent::__unset($name);
		}
	}
	
	//------------------------------------------------------------------------------------------------------------------------------------
	
	public function add_text($text) {
		$this->add_content($text);
	}
	
	public function get_text() {
		return $this->get_content();
	}

	public function set_text($text) {
		$this->set_content($text);
	}
	
	public function clear_text() {
		$this->clear_content();
	}

	public function get_charset() {
		return $this->_charset;
	}

	public function set_charset($value) {
		$this->_charset = $value;
		return $this;
	}

	public function get_mimetype() {
		return $this->_mimetype;
	}

	public function set_mimetype($value) {
		$this->_mimetype = $value;
		return $this;
	}

	
	//------------------------------------------------------------------------------------------------------------------------------------
	
	public function out() {

		if(!self::$_is_parsing) {
			@ header("Content-Type: {$this->_mimetype}; charset=\"{$this->_charset}\"");
		}
		
		$content = $this->to_string();
		echo $content;
		
		if(!self::$_is_parsing) {
			exit;
		}
		
	}
	
	public function save_to($filename) {
		@ file_put_contents($filename, $this->to_string());
	}

	public function out_attachment($filename=null) {
		NavigationHelper::header_content_attachment($filename ? $filename : 'html.html');
		$this->out();
	}
}