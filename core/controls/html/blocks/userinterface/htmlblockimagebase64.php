<?php 


class HTMLBlockImageBase64 extends HTMLControl {
	
	protected $_image;
	protected $_title;
	protected $_width;
	protected $_height;
	
	public function __construct(Image $image) {
		
		parent::__construct();
		
		$this->_image = $image;
		$this->set_title('Image');
	}
	
	public function get_title() {
		return $this->_title;
	}

	public function set_title($value) {
		$this->_title = $value;
	}

	public function get_width() {
		return $this->_width;
	}

	public function set_width($value) {
		$this->_width = $value;
	}

	public function get_height() {
		return $this->_height;
	}

	public function set_height($value) {
		$this->_height = $value;
	}

	
	public function prepare_params() {
		
		$this->set_param('image', $this->_image);
		$this->set_param('title', $this->_title);
		$this->set_param('width', $this->_width);
		$this->set_param('height', $this->_height);
		parent::prepare_params();
		
	}
	
}