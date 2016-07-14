<?php //-----------------------------------------------------------------------


class HTMLInputImageMultipleControl extends HTMLInputControl {

	const DEFAULT_IMAGE_WIDTH = 150;
	const DEFAULT_IMAGE_HEIGHT = 150;

	const DEFAULT_METHOD = 'post';

	/*-------------------------------------------------------------*/

	protected $_image_width = self::DEFAULT_IMAGE_WIDTH;
	protected $_image_height = self::DEFAULT_IMAGE_HEIGHT;
	protected $_enable_delete = true;
	protected $_images = array();
	protected $_add_url;
	protected $_remove_url;
	protected $_add_method;
	protected $_remove_method;
	protected $_add_data = array();
	protected $_remove_data = array();

	public function __construct($id=null, $name=null) {
		parent::__construct();
		$this->set_id($id);
		$this->set_name($name);
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_image_width($value)
	{
		$this->_image_width = $value;
		return $this;
	}
	
	public function get_image_width()
	{
		return $this->_image_width;
	}
	
	/**
	*
	* @return $this
	*
	*/
	public function set_image_height($value)
	{
		$this->_image_height = $value;
		return $this;
	}

	public function get_image_height()
	{
		return $this->_image_height;
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_enable_delete($value)
	{
		$this->_enable_delete = $value;
		return $this;
	}
	
	public function get_enable_delete()
	{
		return $this->_enable_delete;
	}
	

	/**
	*
	* @return $this
	*
	*/
	public function add_image(Imageable $image)
	{
		$this->_images[] = $image;
		return $this;
	}

	/**
	*
	* @return $this
	*
	*/
	public function add_images(array $images)
	{
		foreach($images as $image)
		{
			$this->add_image($image);
		}

		return $this;
	}


	/**
	*
	* @return $this
	*
	*/
	public function set_add_url($value)
	{
		$this->_add_url = $value;
		return $this;
	}

	public function get_add_url()
	{
		return $this->_add_url;
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_remove_url($value)
	{
		$this->_remove_url = $value;
		return $this;
	}

	public function get_remove_url()
	{
		return $this->_remove_url;
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_add_method($value)
	{
		$this->_add_method = $value;
		return $this;
	}

	public function get_add_method()
	{
		return $this->_add_method;
	}


	/**
	*
	* @return $this
	*
	*/
	public function set_remove_method($value)
	{
		$this->_remove_method = $value;
		return $this;
	}

	public function get_remove_method()
	{
		return $this->_remove_method;
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_add_data($value)
	{
		$this->_add_data = $value;
		return $this;
	}

	public function get_add_data()
	{
		return $this->_add_data;
	}


	/**
	*
	* @return $this
	*
	*/
	public function set_remove_data($value)
	{
		$this->_remove_data = $value;
		return $this;
	}

	public function get_remove_data()
	{
		return $this->_remove_data;
	}


	/**
	*
	* @return $this
	*
	*/
	public function set_method($value)
	{
		$this->set_add_method($value);
		$this->set_remove_method($value);
		return $this;
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_data_item_add($key, $value)
	{
		$this->_add_data[$key] = $value;
		return $this;
	}


	/**
	*
	* @return $this
	*
	*/
	public function set_data_item_remove($key, $value)
	{
		$this->_remove_data[$key] = $value;
		return $this;
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_data_item($key, $value)
	{
		$this->set_data_item_add($key, $value);
		$this->set_data_item_remove($key, $value);
		return $this;
	}


	public function prepare_params() {

		parent::prepare_params();

		$this->set_param('id_uniq', uniqid('image'.$this->get_id()));
		$this->set_param('image_width', $this->_image_width);
		$this->set_param('image_height', $this->_image_height);
		$this->set_param('enable_delete', $this->_enable_delete); 
		$this->set_param('images', $this->_images);
		$this->set_param('add_url', $this->_add_url);
		$this->set_param('remove_url', $this->_remove_url);
		$this->set_param('add_method', $this->_add_method);
		$this->set_param('remove_method', $this->_remove_method);
		$this->set_param('add_data', $this->_add_data);
		$this->set_param('remove_data', $this->_remove_data);
	}

}


//----------------------------------------------------------------------- ?>