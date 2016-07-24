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

		if(isset($_POST["{$varname}_title"]))
		{
			$image_file->set_title($_POST["{$varname}_title"]);
			$image_file->save();
		}

		return $image_file;

	}


	/*-------------------------------------------------------------*/

	public static function ajax_url_base64()
	{
		set_time_limit(0);

		$json = new AjaxJSONFormResponse();
		
		$url = $_REQUEST['url'];
		$contents = file_get_contents($url);

		if(!$contents)
		{
			$json->set_success(false);
		}
		else
		{
			$json->set_success(true);

			$image = new Image($contents);
			$json->set_item('content', $image->get_base64_contents(true));
		}


		$json->out();
	}
	
	/*-------------------------------------------------------------*/
	
	protected $_id_image_file = null;
	protected $_image_width = self::DEFAULT_IMAGE_WIDTH;
	protected $_image_height = self::DEFAULT_IMAGE_HEIGHT;
	protected $_enable_delete = true;
	protected $_delete_selected = false;

	protected $_enable_select_local = true;
	protected $_enable_image_search = true;
	protected $_enable_select_url = true;
	
	protected $_for_modaldialog = false;
	
	protected $_enable_title = true;
	protected $_enable_title_edit = true;
	protected $_enable_crop = true;

	public function __construct($id=null, $name=null) {

		parent::__construct();

		self::add_global_static_library(self::STATIC_LIBRARY_MODAL_DIALOG);
		self::add_global_static_library(self::STATIC_LIBRARY_BOOTSTRAP);
		self::add_global_static_library(self::STATIC_LIBRARY_MASONRY);

		$this->set_id($id);
		$this->set_name($name);
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_enable_select_url($value)
	{
		$this->_enable_select_url = $value;
		return $this;
	}

	public function get_enable_select_url()
	{
		return $this->_enable_select_url;
	}



	/**
	*
	* @return $this
	*
	*/
	public function set_enable_image_search($value)
	{
		$this->_enable_image_search = $value;
		return $this;
	}

	public function get_enable_image_search()
	{
		return $this->_enable_image_search;
	}



	/**
	*
	* @return $this
	*
	*/
	public function set_enable_select_local($value)
	{
		$this->_enable_select_local = $value;
		return $this;
	}

	public function get_enable_select_local()
	{
		return $this->_enable_select_local;
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
		else
		{
			$this->_id_image_file = $value;
			parent::set_value($value);
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
	
	/**
	*
	* @return $this
	*
	*/
	public function set_for_modaldialog($value)
	{
		$this->_for_modaldialog = $value;
		return $this;
	}
	
	public function get_for_modaldialog()
	{
		return $this->_for_modaldialog;
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_enable_title($value)
	{
		$this->_enable_title = $value;
		return $this;
	}

	public function get_enable_title()
	{
		return $this->_enable_title;
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_enable_title_edit($value)
	{
		$this->_enable_title_edit = $value;
		return $this;
	}

	public function get_enable_title_edit()
	{
		return $this->_enable_title_edit;
	}


	/**
	*
	* @return $this
	*
	*/
	public function set_enable_crop($value)
	{
		$this->_enable_crop = $value;
		return $this;
	}

	public function get_enable_crop()
	{
		return $this->_enable_crop;
	}


	
	
	public function prepare_params() {

		parent::prepare_params();

		$this->set_param('id_uniq', uniqid('image'.$this->get_id()));
		$this->set_param('image_width', $this->_image_width);
		$this->set_param('image_height', $this->_image_height);
		$this->set_param('enable_delete', $this->_enable_delete); 
		$this->set_param('delete_selected', $this->_delete_selected);
		$this->set_param('id_image_file', $this->_id_image_file);
		$this->set_param('image_file', $this->_id_image_file ? ZfImageFile::get_by_id_image_file($this->_id_image_file) : null);
		$this->set_param('enable_select_url', $this->_enable_select_url);
		$this->set_param('enable_select_local', $this->_enable_select_local);
		$this->set_param('enable_image_search', $this->_enable_image_search);
		$this->set_param('for_modaldialog', $this->_for_modaldialog);
		$this->set_param('enable_title', $this->_enable_title);
		$this->set_param('enable_title_edit', $this->_enable_title_edit);
		$this->set_param('enable_crop', $this->_enable_crop); 
	}

}


//----------------------------------------------------------------------- ?>