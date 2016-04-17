<?php

class HTMLBlockBreadCrumbControl extends HTMLControl {

	
	protected $_items;
	
	public function __construct() {
		
		parent::__construct();
		
		$this->_items = array();
	}

	
	/* @return BreadCrumbControl */
	public function add_item($text, $href=null) {
		$this->_items[] = array('text' => $text, 'href' => $href);
		return $this;
	}
	
	
	public function get_items_count() {
		return count($this->_items);
	}


	public function prepare_params() {
		parent::prepare_params();
		$this->set_param('items', $this->_items);
	}
	
}