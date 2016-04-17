<?php //-----------------------------------------------------------------------


class HTMLInputControl extends HTMLControl {

	const CLASS_READONLY = 'input-readonly';
	const CLASS_ERROR = 'input-error';
	const CLASS_OPTIONAL = 'input-optional';
	const CLASS_DISABLED = 'input-disabled';
	
	
	//-----------------------------------------------

	public static function parse_classes_array($arg1, $arg2=null)
	{
		$classes = array();

		$args = func_get_args();

		foreach($args as $arg)
		{
			if(is_array($arg))
			{
				$classes = array_merge($classes, call_user_func_array(array(self,'parse_classes_array'),$arg));
			}
			else
			{
				$arg = preg_replace('#(?i)(?m)(?s)\s+#', ' ', $arg);

				foreach(explode(' ', $arg) as $classname)
				{
					$classes[] = $classname;
				}
			}
		}

		return array_unique($classes);
	}

	public static function parse_classes($arg1, $arg2=null)
	{
		$args = func_get_args();
		$classes = self::parse_classes_array($args);
		return implode(' ', $classes);
	}

	protected static $_default_classes = array();

	public static function set_default_class($class)
	{
		$args = func_get_args();
		$classes = self::parse_classes_array($args);
		self::$_default_classes = $classes;
	}

	public static function add_default_class($class)
	{
		$args = func_get_args();
		$classes = self::parse_classes_array($args);
		self::$_default_classes = array_unique(array_merge(self::$_default_classes, $classes));
	}

	public static function remove_default_class($class)
	{
		$args = func_get_args();
		$classes = self::parse_classes_array($args);
		self::$_default_classes = array_diff(self::$_default_classes, $classes);
	}

	public static function get_default_class()
	{
		return implode(' ', self::$_default_classes);
	}

	public static function clear_default_class()
	{
		self::$_default_classes = array();
	}

	//-----------------------------------------------
	
	protected $_default_id;
	
	protected $_onChange;
	protected $_onFocus;
	protected $_onBlur;
	protected $_onMouseOver;
	protected $_onMouseOut;
	protected $_onClick;
	protected $_title;
	protected $_class;
	protected $_name;
	protected $_id;
	protected $_value;
	protected $_titleClass;
	protected $_style;
	protected $_disabled;
	protected $_readonly;
	protected $_label;
	
	protected $_error = false;
	protected $_optional;
	
	protected $_defaultClass = array();

	protected $_show_label_title = false;
	
	protected $_custom_attrs = array();
	
	//-----------------------------------------------
	
	public function __construct($attrs = array()) {
				
		$this->_default_id = StringHelper::uniqid('input_');
		
		parent::__construct();

		$this->_defaultClass = self::parse_classes_array(self::get_default_class());

		if(!$attrs['id']) $attrs['id'] = StringHelper::uniqid ();
		
		self::add_global_css_files_zframework('css/controls/htmlinputcontrol.css');
		HTMLControl::add_global_static_library(HTMLControlStaticLibrary::STATIC_LIBRARY_PLACEHOLDER);
	}
	
	
	
	public function __get($name) {

		if($name == 'label') {
			
			return $this->get_label_html();
			
		} else {
			
			if(array_key_exists($name, $this->_custom_attrs)) {
				
				return $this->_custom_attrs[$name];
				
			} else {
				
				return null;
			}
			
		}
		
	}
	
	
	
	public function __set($name, $value) {

		$method_name = 'set_'. strtolower($name);
		
		
		if(method_exists($this, $method_name)) {
			
			call_user_func(array($this, $method_name), $value);
			
		} else {
			
			$this->_custom_attrs[$name] = $value;
		}
		
		
	}
	
	//-----------------------------------------------------------------------
	
	
	public function get_label_html() {
		
		$label = "<label for=".HTMLHelper::quote($this->get_id());
		$label.= " id=".HTMLHelper::quote($this->get_id() . '_label');
		
		if($this->get_title_class()) $label.= " class=".HTMLHelper::quote($this->get_title_class());
		
		$label.= ">".$this->get_title()."</label>";
		
		return $label;
		
	}
	
	//-----------------------------------------------------------------------

	public function set_attrs($attrs) {
		
		foreach((array) $attrs as $name => $value) {
			
			$this->$name = $value;
		}
		
	}

	//-----------------------------------------------------------------------

	public function get_on_change() {
		return $this->_onChange;
	}

	public function set_on_change($var) {
		$this->_onChange = $var;
	}

	public function get_on_click() {
		return $this->_onClick;
	}

	public function set_on_click($var) {
		$this->_onClick = $var;
	}

	public function get_on_focus() {
		return $this->_onFocus;
	}

	public function set_on_focus($var) {
		$this->_onFocus = $var;
	}

	public function get_on_blur() {
		return $this->_onBlur;
	}

	public function set_on_blur($var) {
		$this->_onBlur = $var;
	}



	public function get_on_mouse_over() {
		return $this->_onMouseOver;
	}

	public function set_on_mouse_over($var) {
		$this->_onMouseOver = $var;
	}



	public function get_on_mouse_out() {
		return $this->_onMouseOut;
	}

	public function set_on_mouse_out($var) {
		$this->_onMouseOut = $var;
	}



	public function get_title() {
		return $this->_title;
	}

	public function set_title($var) {
		$this->_title = $var;
	}



	public function get_class() {
		return $this->_class;
	}

	public function set_class($var) {

		$args = func_get_args();
		$classes = self::parse_classes_array($args);
		$this->_class = implode(' ', $classes);
	}

	public function add_class($class)
	{
		$args = func_get_args();
		$this->_class = implode(' ', array_unique(array_merge(self::parse_classes_array($this->_class), self::parse_classes_array($args))));
	}

	public function remove_class($class)
	{
		$args = func_get_args();
		$this->_class = implode(' ', array_unique(array_diff(self::parse_classes_array($this->_class), self::parse_classes_array($args))));
	}

	public function clear_class()
	{
		$this->_class = '';
	}


	public function get_name() {
		return $this->_name;
	}

	public function set_name($var) {
		$this->_name = $var;
	}



	public function get_id() {
		return $this->_id ? $this->_id : $this->_default_id;
	}

	public function set_id($var) {
		$this->_id = $var;
	}



	public function get_value() {
		return $this->_value;
	}

	public function set_value($var) {
		$this->_value = $var;
	}



	public function get_title_class() {
		return $this->_titleClass;
	}

	public function set_title_class($var) {
		$this->_titleClass = $var;
	}



	public function get_style() {
		return $this->_style;
	}

	public function set_style($var) {
		$this->_style = $var;
	}



	public function get_disabled() {
		return $this->_disabled;
	}

	public function set_disabled($var) {
		$this->_disabled = $var;
	}



	public function get_readonly() {
		return $this->_readonly;
	}

	public function set_readonly($var) {
		$this->_readonly = $var;
	}

	
	
	
	public function get_optional() {
		return $this->_optional;
	}

	public function set_optional($var) {
		$this->_optional = $var;
	}
	
	
	public function get_show_label_title() {
		return $this->_show_label_title;
	}

	public function set_show_label_title($value) {
		$this->_show_label_title = $value;
		return $this;
	}


	public function get_label() {
		return $this->_label;
	}

	public function set_label($value) {
		$this->_label = $value;
	}

	
	//-----------------------------------------------

	
	public function validate() {
		
		$this->_error = false;
		
		return $this->_error;
	}

	
	
	
	
	public function get_error() {
		return $this->_error;
	}

	public function set_error($var) {
		$this->_error = $var;
	}


	//-----------------------------------------------------------------------
	
	
	public function prepare_params() {
		
		$class = self::parse_classes_array($this->_class);
		$class = array_unique(array_merge($class, $this->_defaultClass));

		if($this->get_disabled()) $class[] = self::CLASS_DISABLED;
		if($this->get_error()) $class[] = self::CLASS_ERROR;
		if($this->get_optional()) $class[] = self::CLASS_OPTIONAL;
		if($this->get_readonly()) $class[] = self::CLASS_READONLY;

		if($this->_label) {
			
			$label_html = $this->_label;
			
		} else if($this->_show_label_title && $this->_title) {
			
			$label_html = $this->_title;
			
		} else {
			
			$label_html = '';
			
		}
		
		if($label_html) {
			
			$label_html = "<label class='htmlinputcontrol-label' for='".HTMLHelper::escape($this->_id)."'>".$label_html."</label>";
			
		} 
		
		$custom_attrs_html = "";
		
		foreach($this->_custom_attrs as $name => $value) {
			$custom_attrs_html.= ' '.HTMLHelper::escape($name).'="'.HTMLHelper::escape($value).'"';
		}
		
		$this->set_param('on_change', $this->get_on_change());
		$this->set_param('on_focus', $this->get_on_focus());
		$this->set_param('on_blur', $this->get_on_blur());
		$this->set_param('on_mouse_over', $this->get_on_mouse_over());
		$this->set_param('on_mouse_out', $this->get_on_mouse_out());
		$this->set_param('on_click', $this->get_on_click());
		$this->set_param('title', $this->get_title());
		$this->set_param('class', implode(' ', array_filter($class)));
		$this->set_param('name', $this->get_name() ? $this->get_name() : $this->get_id());
		$this->set_param('id', $this->get_id());
		$this->set_param('value', $this->get_value());
		$this->set_param('title_class', $this->get_title_class());
		$this->set_param('style', $this->get_style());
		$this->set_param('disabled', $this->get_disabled());
		$this->set_param('readonly', $this->get_readonly());
		$this->set_param('optional', $this->get_optional());
		$this->set_param('show_label_title', $this->_show_label_title);
		$this->set_param('label_html', $label_html);
		$this->set_param('label', $this->_label);
		$this->set_param('custom_attrs_html', $custom_attrs_html);
		$this->set_param('custom_attrs', $this->_custom_attrs);

		parent::prepare_params();
	}


	
}
