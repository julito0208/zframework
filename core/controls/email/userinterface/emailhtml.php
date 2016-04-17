<?php 

class EmailHTML extends Email {

	public function __construct() {
		parent::__construct();
	}
	
	//----------------------------------------
	
	protected function _get_parsed_content() {
		return $this->_get_parsed_html();
	}
	
	protected function _set_text_charset($charset) {
		$this->_set_text_html_charset($charset);
	}
	
	protected function _get_text_charset() {
		return $this->_get_text_html_charset();
	}
	
	protected function _get_content_text_plain() {
		return null;
	}
	
	protected function _get_content_text_html() {
		return $this->get_content();
	}
	
	//----------------------------------------
	
	public function prepare_params() {
		parent::prepare_params();
	}
}