<?php

class HTMLInputAutoCompleteControl extends HTMLInputTextControl {

	
	const DEFAULT_MIN_LENGTH = 2;
	const DEFAULT_DELAY = 200;
	const DEFAULT_AUTO_FOCUS = true;
	const DEFAULT_AUTO_SELECT = true;
	const DEFAULT_STRICT = true;
	const DEFAULT_SEARCH_METHOD = 'post';

	const SEARCH_VARNAME = 'q';
	
	/*--------------------------------------------------------------*/

//	protected static $_init_timeout = 300;

	protected static $_text_empty_class = 'autocompletion_empty';
	protected static $_text_strict_class = 'autocompletion_strict';
	protected static $_text_value_selected_class = 'autocompletion_value_selected value_selected';
	protected static $_text_loading_class = 'autocompletion_loading loading';
	
	protected static $_data_key_select_item_function = 'autocomplete_select';
	protected static $_data_key_update_search_data_function = 'autocomplete_set_search_data';

	protected static $_default_width = 180;
	
	/*--------------------------------------------------------------*/
	
	protected $_min_length = self::DEFAULT_MIN_LENGTH;
	protected $_strict = self::DEFAULT_STRICT;
	protected $_delay = self::DEFAULT_DELAY;
	protected $_auto_focus = self::DEFAULT_AUTO_FOCUS;
	protected $_auto_select = self::DEFAULT_AUTO_SELECT;
	protected $_selected_item;
	protected $_search_url;
	protected $_search_method = self::DEFAULT_SEARCH_METHOD;
	protected $_search_data = array();
	protected $_width;
	protected $_nomatches = '';

	public function __construct($id=null, $name=null, $selected_item=null) {
		
		parent::__construct();
		
		$this->add_static_library(HTMLControlStaticLibrary::STATIC_LIBRARY_SELECT2);

		$this->set_id($id);
		$this->set_name($name);
		$this->set_selected_item($selected_item);
		
	}

	public function get_search_url() {
		return $this->_search_url;
	}

	public function set_search_url($value) {
		$this->_search_url = $value;
		return $this;
	}

	public function get_search_method() {
		return $this->_search_method;
	}

	public function set_search_method($value) {
		$this->_search_method = $value;
		return $this;
	}

	

	public function get_min_length() {
		return $this->_min_length;
	}

	public function set_min_length($value) {
		$this->_min_length = $value;
		return $this;
	}


	public function get_strict() {
		return $this->_strict;
	}

	public function set_strict($value) {
		$this->_strict = $value;
		return $this;
	}


	public function get_delay() {
		return $this->_delay;
	}

	public function set_delay($value) {
		$this->_delay = $value;
		return $this;
	}



	public function get_auto_focus() {
		return $this->_auto_focus;
	}

	public function set_auto_focus($value) {
		$this->_auto_focus = $value;
		return $this;
	}


	public function set_selected_item($item) {
		return $this->set_value($item);
	}
	
	public function get_selected_item() {
		return $this->get_value();
	}


	public function get_text_input_id() {
		return $this->get_id().'_text';
	}
	
	public function get_hidden_value_input_id() {
		return $this->get_id();
	}
	
	
	public function get_auto_select() {
		return $this->_auto_select;
	}

	public function set_auto_select($value) {
		$this->_auto_select = $value;
		return $this;
	}
	
	
	/* @return HTMLInputAutoCompleteControl */
	public function set_search_data($data_name, $value=null) {
		
		if(!is_array($data_name)) {
			
			return $this->set_search_data(array($data_name => $value));
			
		} else {
			
			$this->_search_data = array_merge($this->_search_data, $data_name);
			return $this;
			
		}
		
	}
	
	/* @return HTMLInputAutoCompleteControl */
	public function clear_search_data() {
		$this->_search_data = array();
		return $this;
	}
	
	
	public function remove_search_data($name) {
		
		$args = func_get_args();
		
		foreach($args as $arg) {
			
			if(!is_array($arg)) $names = array($arg);
			else $names = $arg;
			
			foreach($names as $name) {
				unset($this->_search_data[$name]);				
			}
			
		}
		
		return $this;
	}


	public function get_search_data($key=null) {
		
		if(func_num_args() > 0) {
			
			return $this->_search_data[$key];
			
		} else {
			
			return array_merge(array(), $this->_search_data);
			
		}
		
	}
	
	
	public function set_text($text) {
		
		$this->set_value(array('text' => $text, 'item_text' => $text));
	}
	
	
	public function get_width() {
		return $this->_width;
	}

	public function set_width($value) {
		$this->_width = $value;
	}

	public function get_nomatches() {
		return $this->_nomatches;
	}

	public function set_nomatches($value) {
		$this->_nomatches = $value;
	}

	
	public function prepare_params() {

		$this->set_param('strict', $this->_strict);
		$this->set_param('auto_focus', $this->_auto_focus);
		$this->set_param('auto_select', $this->_auto_select);
		$this->set_param('delay', $this->_delay);
		$this->set_param('min_length', $this->_min_length);
		$this->set_param('search_url', $this->_search_url);
		$this->set_param('search_method', $this->_search_method);
		$this->set_param('text_empty_class', self::$_text_empty_class);
		$this->set_param('text_strict_class', self::$_text_strict_class);
		$this->set_param('text_value_selected_class', self::$_text_value_selected_class);
		$this->set_param('text_loading_class', self::$_text_loading_class);
		$this->set_param('text_input_id', $this->get_text_input_id());
		$this->set_param('hidden_input_id', $this->get_hidden_value_input_id());
		$this->set_param('data_key_select_item_function', self::$_data_key_select_item_function);
		$this->set_param('data_key_set_search_data_function', self::$_data_key_update_search_data_function);
		$this->set_param('search_data', $this->_search_data);
		$this->set_param('width', $this->_width ? $this->_width : self::$_default_width);
		$this->set_param('nomatches', $this->_nomatches);
		
		parent::prepare_params();
		
		
	}


}