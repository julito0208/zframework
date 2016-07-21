<?php

class HTMLBlockBXSliderControl extends HTMLControl
{
	protected $_id;
	protected $_width;
	protected $_height;
	protected $_images = array();
	protected $_thumb_type;
	protected $_auto_slide = true;
	protected $_pager = true;
	protected $_captions = true;
	protected $_json_params = array();

	public function __construct($thumb_type=null, $id=null, $images=array())
	{
		parent::__construct();
		self::add_static_library(self::STATIC_LIBRARY_BX_SLIDER);
		$this->set_id($id ? $id : uniqid('slider'));
		$this->set_thumb_type($thumb_type);
		$this->add_images($images);
	}

	protected function _update_json_params()
	{
		$this->_json_params = array();

		if(!is_null($this->_width))
		{
			$this->_json_params['width'] = $this->_width;
		}

		if(!is_null($this->_height))
		{
			$this->_json_params['height'] = $this->_height;
		}

		$this->_json_params['auto'] = (bool) $this->_auto_slide;
		$this->_json_params['pager'] = (bool) $this->_pager;


	}

	/**
	*
	* @return $this
	*
	*/
	public function add_images($images)
	{
		if(is_array($images))
		{
			foreach((array) $images as $image)
			{
				$this->add_image($image);
			}
		}
		
		return $this;
	}
	
	/**
	*
	* @return $this
	*
	*/
	public function add_image(Imageable $image, $title=null, $link=null)
	{

		$id_image_file = $image->get_id_image_file();
		$image_file = ZfImageFile::get_by_id_image_file($id_image_file);

		$this->_images[] = array(
			'image' => $image_file,
			'title' => $title ? $title : $image_file->get_title(),
			'href' => $link
		);
		
		return $this;
	}
	
	/**
	*
	* @return $this
	*
	*/
	public function set_id($value)
	{
		$this->_id = $value;
		return $this;
	}

	public function get_id()
	{
		return $this->_id;
	}


	/**
	 *
	 * @return $this
	 * 
	 */
	public function set_auto_slide($value)
	{
		$this->_auto_slide = $value;
		return $this;
	}

	public function get_auto_slide()
	{
		return $this->_auto_slide;
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_width($value)
	{
		$this->_width = $value;
		return $this;
	}

	public function get_width()
	{
		return $this->_width;
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_height($value)
	{
		$this->_height = $value;
		return $this;
	}

	public function get_height()
	{
		return $this->_height;
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_thumb_type($value)
	{
		$this->_thumb_type = $value;
		return $this;
	}
	
	public function get_thumb_type()
	{
		return $this->_thumb_type;
	}

	public function get_images()
	{
		return $this->_images;
	}

	public function count_images()
	{
		return count($this->_images);
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_pager($value)
	{
		$this->_pager = $value;
		return $this;
	}

	public function get_pager()
	{
		return $this->_pager;
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_captions($value)
	{
		$this->_captions = $value;
		return $this;
	}

	public function get_captions()
	{
		return $this->_captions;
	}



	public function prepare_params()
	{
		parent::prepare_params();
		$this->set_param('id', $this->_id); 
		$this->set_param('width', $this->_width); 
		$this->set_param('height', $this->_height); 
		$this->set_param('auto_slide', $this->_auto_slide);
		$this->set_param('thumb_type', $this->_thumb_type);
		$this->set_param('images', $this->_images);
		$this->set_param('pager', $this->_pager);

		$this->_update_json_params();
		$this->set_param('json_params', $this->_json_params);
	}
}