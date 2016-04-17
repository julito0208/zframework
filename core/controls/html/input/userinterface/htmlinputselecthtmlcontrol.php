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
	public function add_option($option_label, $value='') {
		
		if(func_num_args() == 2) {
			
			$this->_options[] = array('text' => $option_label, 'value' => $value);
			
		} else {
			
			if($option_label && $option_label instanceof OptionItem) {
				
				$option = array('text' => $option_label->get_option_item_label(), 'value' => $option_label->get_option_item_value());
				
				if($option_label instanceof OptionItemHTML) $option['html'] = $option_label->get_option_item_html();
				
				$this->_options[] = $option;
				
			} else {
				
				if($option_label instanceof DBEntity) {
					
					$primary_keys = $option_label->primary_keys;
					$fields = $option_label->fields;
					
					if(count($primary_keys) == 1 && in_array('nombre', $fields)) {
						
						$option = array('text' => $option_label->nombre, 'value' => $option_label->$primary_keys[0]);
						$this->_options[] = $option;
						
						return $this;
					}
					
				}
				
				$option = CastHelper::to_array($option_label);
				
				$option_value = $option['value'];
				$option_label = '';

				foreach(array('text', 'label', 'title', 'html') as $key) {

					if($option[$key]) {

						$option_label = $option[$key];
						break;
					}

				}

				$this->_options[] = array('text' => $option_label, 'value' => $option_value);
				
			}
			
		}

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

