<?php

class HTMLDialogCropImage extends HTMLDialog implements RedirectURLPattern {
	
	const URL_ID = 'ImageCropDialog';
	const DEFAULT_NAME = 'crop';
	
	protected static $_URL_PATTERN = '/imagecropdialog';
	
	/* @return URLPattern */
	public static function get_url_pattern() {
		return new URLPattern(self::$_URL_PATTERN, self::URL_ID, get_class());
	}
	
	
	/*-----------------------------------------------------------------------------*/
	
	protected $_imagecropcontrol;
	protected $_mimeimage;
	protected $_image_url;
	protected $_form_values = array();
	protected $_callback;
	protected $_can_select_none = false;
	protected $_post_data = array();
	
	public function __construct($url=null, $data=array()) {
		
		parent::__construct();
		
		self::add_global_css_files_zframework('css/controls/imagecropdialog.css');
		
		if(!$url) $url = HTTPPost::get_item('image_url');
		$aspect = HTTPPost::get_item('aspect', $data['aspect']);
		$select_width = HTTPPost::get_item('select_width', $data['select_width']);
		$select_height = HTTPPost::get_item('select_height', $data['select_height']);
		$select_x = HTTPPost::get_item('select_x', $data['select_x']);
		$select_y = HTTPPost::get_item('select_y', $data['select_y']);
		$min_size_width = HTTPPost::get_item('min_size_width');
		$min_size_height = HTTPPost::get_item('min_size_height');
		$auto_select = HTTPPost::get_item('auto_select');
		$callback = HTTPPost::get_item('callback');
	
		$this->_imagecropcontrol = new HTMLBlockCropImageControl($url, uniqid(), self::DEFAULT_NAME);
		$this->_imagecropcontrol->set_auto_select(true);
		$this->_imagecropcontrol->set_center(true);
		$this->set_image_url ($url);
		
		if(!is_null($aspect)) $this->_imagecropcontrol->set_aspect($aspect);
		if(!is_null($select_width)) $this->_imagecropcontrol->set_select_width($select_width);
		if(!is_null($select_height)) $this->_imagecropcontrol->set_select_height($select_height);
		if(!is_null($select_x)) $this->_imagecropcontrol->set_select_x($select_x);
		if(!is_null($select_y)) $this->_imagecropcontrol->set_select_y($select_y);
		if(!is_null($min_size_height)) $this->_imagecropcontrol->set_min_size_height($min_size_height);
		if(!is_null($min_size_width)) $this->_imagecropcontrol->set_min_size_width($min_size_width);
		if(!is_null($auto_select)) $this->_imagecropcontrol->set_auto_select($auto_select);
		
		$this->set_callback($callback);
		
	}
	
	/* @return ImageCropControl */
	public function get_imagecropcontrol() {
		return $this->_imagecropcontrol;
	}
	
	
	/* @return ImageCropDialog */
	public function set_imagecropcontrol_id($id) {
		$this->_imagecropcontrol->set_id($id);
		return $this;
	}
	
	
	/* @return ImageCropDialog */
	public function set_imagecropcontrol_name($name) {
		$this->_imagecropcontrol->set_name($name);
		return $this;
	}
	
	
	/* @return ImageCropDialog */
	public function set_image_url($url) {
		$this->_image_url = $url;
		$this->_imagecropcontrol->set_image_url($url);
		return $this;
	}
	
	
	/* @return ImageCropDialog */
	public function set_aspect($aspect) {
		$this->_imagecropcontrol->set_aspect($aspect);
		return $this;
	}
	
	/* @return ImageCropDialog */
	public function set_min_size($width, $height) {
		$this->_imagecropcontrol->set_min_size_width($width);
		$this->_imagecropcontrol->set_min_size_height($height);
		if($height) $this->set_aspect($width/$height);
		return $this;
	}
	

	/* @return ImageCropDialog */
	public function add_form_value($name, $value=null) {
		$this->_form_values[$name] = $value;
		return $this;
	}
	
	
	/* @return ImageCropDialog */
	public function set_callback($callback) {
		$this->_callback = $callback;
		return $this;
	}

	public function get_callback() {
		return $this->_callback;
	}
	
	
	public function get_can_select_none() {
		return $this->_can_select_none;
	}

	public function set_can_select_none($value) {
		$this->_can_select_none = $value;
		return $this;
	}


	public function get_post_data() {
		return $this->_post_data;
	}

	public function set_post_data($value) {
		$this->_post_data = $value;
		return $this;
	}

	public function prepare_params() {
		
		parent::prepare_params();
		
		$this->set_param('imagecropcontrol', $this->_imagecropcontrol);
		$this->set_param('form_values', $this->_form_values);
		$this->set_param('callback', $this->_callback);
		$this->set_param('can_select_none', $this->_can_select_none);
		$this->set_param('post_data', $this->_post_data);
	}

	
}