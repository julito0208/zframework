<?php //-----------------------------------------------------------------------


class HTMLInputImageControl extends HTMLInputControl {

	const DEFAULT_IMAGE_WIDTH = 150;
	const DEFAULT_IMAGE_HEIGHT = 150;

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
	
	protected $_image = null;
	protected $_image_width = self::DEFAULT_IMAGE_WIDTH;
	protected $_image_height = self::DEFAULT_IMAGE_HEIGHT;

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
			return parent::set_value($value);
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



	public function prepare_params() {

		parent::prepare_params();
		$this->set_param('id_uniq', uniqid('image'.$this->get_id()));
		$this->set_param('image_width', $this->_image_width);
		$this->set_param('image_height', $this->_image_height);

	}

}


//----------------------------------------------------------------------- ?>