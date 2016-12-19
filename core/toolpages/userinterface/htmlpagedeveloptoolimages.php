<?php 

class HTMLPageDevelopToolImages extends HTMLPageDevelopTool  {

	const URL_SCRIPT_PATTERN = '/images';


	public static function ajax_add_image()
	{
		$json = new AjaxJSONFormResponse();
		$json->set_success(false);

		$image_file = ZfImageFile::create_from_request_file('image');

		if($image_file)
		{
			$json->set_success(true);
			$json->set_item('id_image_file', $image_file->get_id_image_file());
		}
		else
		{
			$json->set_error("Imagen inválida");
		}

		$json->out();

	}

	protected static function _get_title()
	{
		return 'Images';
	}
	
	protected static function _get_show_index()
	{
		return true;
	}
	
	/*-----------------------------------------*/

	public function __construct() {
		
		parent::__construct();

	}

	public function prepare_params() {
		
		parent::prepare_params();

		$input = new HTMLInputImageControl('image');
		$input->set_enable_delete(false);
		$input->set_enable_title_edit(false);
		$this->set_param('input', $input);
	}
}
