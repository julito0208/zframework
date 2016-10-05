<?php //-----------------------------------------------------------------------


class HTMLInputSelectHTMLControl extends HTMLInputSelectControl {

	const DEFAULT_WIDTH = 'resolve';
	const DEFAULT_DEFAULT_OPTION_WIDTH = 0;
	const DEFAULT_CONTAINER_CLASS = 'input-border';

	/*-------------------------------------------------------------*/

	protected static function _parse_object_value($value)
	{
		if(is_object($value) && $value instanceof OptionItem)
		{
			return $value->get_option_item_value();
		}
		else if(is_object($value) && $value instanceof DBEntity)
		{
			$primary_keys = $value->primary_keys;
			$fields = $value->fields;

			if(count($primary_keys) == 1) {

				return $value->$primary_keys[0];
			}
		}
		else
		{
			return $value;
		}
	}

	/*-------------------------------------------------------------*/
	
	protected $_width = null;
	protected $_placeholder = '';
	protected $_container_class;
	protected $_allow_search = true;
	protected $_multiple = false;
	protected $_allow_clear = true;
	
	public function __construct($id=null, $name=null, array $options=array()) {
				
		parent::__construct($id, $name, $options);
		
		self::add_global_static_library(HTMLControlStaticLibrary::STATIC_LIBRARY_SELECT2);
		
		$this->set_width(self::DEFAULT_WIDTH);
		$this->set_container_class(self::DEFAULT_CONTAINER_CLASS);
		$this->set_default_option_enabled(false);
		
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_allow_search($value)
	{
		$this->_allow_search = $value;
		return $this;
	}

	public function get_allow_search()
	{
		return $this->_allow_search;
	}


	/* @return HTMLInputSelectControl */
	public function add_option($option_label, $value=null, $html=null) {

		$this->_options[] = JSONOptionItemHTML::parse_option_array($option_label, $value, $html);
		return $this;
		
	}
	
	public function get_width() {
		return $this->_width;
	}

	public function set_width($value) {
		$this->_width = $value;
		return $this;
	}

	public function get_placeholder() {
		return $this->_placeholder;
	}

	public function set_placeholder($value) {
		$this->_placeholder = $value;
	}

	public function get_container_class() {
		return $this->_container_class;
	}

	public function set_container_class($value) {
		$this->_container_class = $value;
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_multiple($value)
	{
		$this->_multiple = $value;
		return $this;
	}

	public function get_multiple()
	{
		return $this->_multiple;
	}
	
	/**
	*
	* @return $this
	*
	*/
	public function set_allow_clear($value)
	{
		$this->_allow_clear = $value;
		return $this;
	}
	
	public function get_allow_clear()
	{
		return $this->_allow_clear;
	}
	
	

	public function prepare_params() {
		
		parent::prepare_params();
		
		if($this->_width === null) {
			
			if($this->get_default_option_enabled()) {
				$this->_width = self::DEFAULT_DEFAULT_OPTION_WIDTH;
			} else {
				$this->_width = self::DEFAULT_WIDTH;
			}
			
		} else {
			
			$width = $this->_width;
		}
		
		$this->set_param('width', $this->_width);
		$this->set_param('multiple', $this->_multiple);
		$this->set_param('placeholder', $this->_placeholder);
		$this->set_param('container_class', $this->_container_class);
		$this->set_param('allow_search', $this->_allow_search);
		$this->set_param('allow_clear', $this->_allow_clear);

		$parsed_values = array();

		$value = $this->get_value();

		if(is_array($value))
		{
			foreach($value as $val)
			{
				$parsed_values[] = self::_parse_object_value($val);
			}
		}
		else
		{
			$parsed_values[] = self::_parse_object_value($value);
		}

		$this->set_param('selected_values', $parsed_values);

	}
	
}

