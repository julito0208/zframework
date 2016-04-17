<?php 

class EmailText extends Email {

	public function __construct() {
		parent::__construct();
	}
	
	//----------------------------------------
	
	protected function _get_parsed_content() {
		return $this->_get_parsed_text();
	}
	
	protected function _set_text_charset($charset) {
		$this->_set_text_plain_charset($charset);
	}
	
	protected function _get_text_charset() {
		return $this->_get_text_plain_charset();
	}
	
	protected function _get_content_text_plain() {
		return $this->get_content();
	}
	
	protected function _get_content_text_html() {
		return null;
	}
	
	//----------------------------------------
	
	public function prepare_params() {
		parent::prepare_params();
	}
}