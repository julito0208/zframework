<?php //-----------------------------------------------------------------------


class HTMLInputImageControl extends HTMLInputControl {

	const DEFAULT_IMAGE_WIDTH = 150;
	const DEFAULT_IMAGE_HEIGHT = 150;

	/*-------------------------------------------------------------*/

	/**
	*
	* @return ZfImageFile
	*
	*/
	public static function get_image_file($varname, $prefix='')
	{
		if(!$_POST[$varname])
		{
			return null;
		}

		$image = Image::from_uploaded_file($varname);

		if(!$image)
		{
			return null;
		}

		$image_file = ZfImageFile::create_from_image($image, $prefix);
		return $image_file;

	}


	/*-------------------------------------------------------------*/

	public static function ajax_url_base64()
	{
		$json = new AjaxJSONResponse();
		
		$url = $_POST['url'];
		$temp_file = tempnam(null, 'img');

		file_put_contents($temp_file, file_get_contents($url));

		$image = new Image($temp_file);

		$json->set_item('content', $image->get_base64_contents(true));
		$json->out();
	}
	
	/*-------------------------------------------------------------*/
	
	protected $_id_image_file = null;
	protected $_image_width = self::DEFAULT_IMAGE_WIDTH;
	protected $_image_height = self::DEFAULT_IMAGE_HEIGHT;
	protected $_enable_delete = false;
	protected $_delete_selected = false;

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
	public function set_image(Imageable $image)
	{
		return $this->set_value($image);
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_value($value)
	{
		if(ClassHelper::is_instance_of($value, 'Imageable'))
		{
			$this->_id_image_file = $value->get_id_image_file();
			return parent::set_value($this->_id_image_file);
		}

		return $this;
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
	public function set_delete_selected($value)
	{
		$this->_delete_selected = $value;
		return $this;
	}
	
	public function get_delete_selected()
	{
		return $this->_delete_selected;
	}
	
	
	public function prepare_params() {

		parent::prepare_params();

		$this->set_param('id_uniq', uniqid('image'.$this->get_id()));
		$this->set_param('image_width', $this->_image_width);
		$this->set_param('image_height', $this->_image_height);
		$this->set_param('enable_delete', $this->_enable_delete); 
		$this->set_param('delete_selected', $this->_delete_selected);
		$this->set_param('id_image_file', $this->_id_image_file);
	}

}


//----------------------------------------------------------------------- ?>