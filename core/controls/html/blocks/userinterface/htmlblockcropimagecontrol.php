<?php

class HTMLBlockCropImageControl extends HTMLControl {
	
	protected $_image_url;
	protected $_name;
	protected $_aspect;
	protected $_select_width;
	protected $_select_height;
	protected $_select_x;
	protected $_select_y;
	protected $_min_size_width;
	protected $_min_size_height;
	protected $_id;
	protected $_auto_select = true;
	protected $_original_height;
	protected $_original_width;
	protected $_enable_preview = true;
	protected $_preview_width = 150;
	protected $_preview_height = 150;
	protected $_center;
	protected $_enable_button_optimal_crop = true;


	public function __construct($image_url=null, $id=null, $name=null) {

		parent::__construct();
		
		$this->set_image_url($image_url);
		$this->set_id($id ? $id : uniqid('crop'));
		$this->set_name($name);
		
		self::add_global_js_files_zframework('thirdparty/jquery-jcrop/jquery.jcrop.js');
		self::add_global_js_files_zframework('thirdparty/jquery-jcrop/jquery.color.js');

		self::add_global_css_files_zframework('thirdparty/jquery-jcrop/jquery.jcrop.css');
		self::add_global_css_files_zframework('css/controls/imagecropcontrol.css');
	}

	public function get_image_url() {
		return $this->_image_url;
	}

	public function set_image_url($value) {

		$this->_image_url = NavigationHelper::conv_abs_url($value);
		
		@ $data = getimagesize($this->_image_url);

		if($data) {

			$this->_original_width = $data[0];
			$this->_original_height = $data[1];

		}
		
		return $this;
	}


	public function get_auto_select() {
		return $this->_auto_select;
	}

	public function set_auto_select($value) {
		$this->_auto_select = $value;
		return $this;
	}



	public function get_id() {
		return $this->_id;
	}

	public function set_id($value) {
		$this->_id = $value;
		return $this;
	}


	public function get_name() {
		return $this->_name;
	}

	public function set_name($value) {
		$this->_name = $value;
		return $this;
	}



	public function get_aspect() {
		return $this->_aspect;
	}

	public function set_aspect($value) {
		$this->_aspect = $value;
		return $this;
	}



	public function get_select_width() {
		return $this->_select_width;
	}

	public function set_select_width($value) {
		$this->_select_width = $value;
		return $this;
	}



	public function get_select_height() {
		return $this->_select_height;
	}

	public function set_select_height($value) {
		$this->_select_height = $value;
		return $this;
	}
	
	
	
	public function get_preview_width() {
		return $this->_preview_width;
	}

	public function set_preview_width($value) {
		$this->_preview_width = $value;
		return $this;
	}



	public function get_preview_height() {
		return $this->_preview_height;
	}

	public function set_preview_height($value) {
		$this->_preview_height = $value;
		return $this;
	}
	
	
	
	public function get_enable_preview() {
		return $this->_enable_preview;
	}

	public function set_enable_preview($value) {
		$this->_enable_preview = $value;
		return $this;
	}



	public function get_select_x() {
		return $this->_select_x;
	}

	public function set_select_x($value) {
		$this->_select_x = $value;
		return $this;
	}



	public function get_select_y() {
		return $this->_select_y;
	}

	public function set_select_y($value) {
		$this->_select_y = $value;
		return $this;
	}



	public function get_min_size_width() {
		return $this->_min_size_width;
	}

	public function set_min_size_width($value) {
		$this->_min_size_width = $value;
		return $this;
	}



	public function get_min_size_height() {
		return $this->_min_size_height;
	}

	public function set_min_size_height($value) {
		$this->_min_size_height = $value;
		return $this;
	}


	public function get_original_width() {
		return $this->_original_width;
	}

	public function set_original_width($value) {
		$this->_original_width = $value;
		return $this;
	}



	public function get_original_height() {
		return $this->_original_height;
	}

	public function set_original_height($value) {
		$this->_original_height = $value;
		return $this;
	}
	
	
	public function get_center() {
		return $this->_center;
	}

	public function set_center($value) {
		$this->_center = $value;
		return $this;
	}
	
	public function get_init_function_name() {
		return 'init_crop_'.($this->_id ? $this->_id : $this->_name);
	}
	
	
	public function get_enable_button_optimal_crop() {
		return $this->_enable_button_optimal_crop;
	}

	public function set_enable_button_optimal_crop($value) {
		$this->_enable_button_optimal_crop = $value;
		return $this;
	}


	public function prepare_params() {
		
		parent::prepare_params();
		
		
		if($this->_aspect) {

			$select_width = $this->_original_width;
			$select_height = $select_width / $this->_aspect;
			
			if($select_height > $this->_original_height) {
				
				$select_height = $this->_original_height;
				$select_width = $select_height * $this->_aspect;
			}
			
			$select_x = ($this->_original_width-$select_width)/2;
			$select_y = ($this->_original_height-$select_height)/2;
			
			$optimal_crop = array($select_x, $select_y, $select_width, $select_height);
			
		} else {
			
			$optimal_crop = array(0, 0, $this->_original_width, $this->_original_height);
			
		}
		
		$this->set_param('optimal_crop', $optimal_crop);
		$this->set_param('image_url', $this->_image_url);
		$this->set_param('id', $this->_id ? $this->_id : $this->_name);
		$this->set_param('name', $this->_name ? $this->_name : $this->_id);
		$this->set_param('select_width', $this->_select_width);
		$this->set_param('select_height', $this->_select_height);
		$this->set_param('select_x', $this->_select_x);
		$this->set_param('select_y', $this->_select_y);
		$this->set_param('aspect', $this->_aspect);
		$this->set_param('min_size_width', $this->_min_size_width);
		$this->set_param('min_size_height', $this->_min_size_height);
		$this->set_param('auto_select', $this->_auto_select);
		$this->set_param('original_width', $this->_original_width);
		$this->set_param('original_height', $this->_original_height);
		$this->set_param('preview_width', $this->_preview_width);
		$this->set_param('preview_height', $this->_preview_height);
		$this->set_param('enable_preview', $this->_enable_preview);
		$this->set_param('center', $this->_center);
		$this->set_param('init_function_name', $this->get_init_function_name());
		$this->set_param('enable_button_optimal_crop', $this->_enable_button_optimal_crop);
		
		
	}



}