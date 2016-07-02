<?php 

class FileImageThumbControl implements MIMEControl, RedirectURLPattern {

	public static function get_url_pattern() {
		$url_format = ZPHP::get_config('image_url_format');
		$url_pattern = preg_quote($url_format);
		$url_pattern = str_replace(preg_quote('%s'), '(.+)', $url_pattern);
		return new URLPattern($url_pattern, 'FileImageThumbControl', 'FileImageThumbControl');
	}
	
	//---------------------------------------------------------------------------------------------

	protected $_image;

	
	public function __construct($image_str, $image_type) {

		if(strpos($image_str, '-') !== false)
		{
			list($id_image_file, $id_image_thumb_type) = explode('-', $image_str, 2);

			$image_file = ZfImageFile::get_by_id_image_file($id_image_file);
			$image_thumb_type = ZfImageThumbType::get_by_id_image_thumb_type($id_image_thumb_type);
			$image_type = ZfImageType::get_by_id_image_type($image_thumb_type->get_id_image_type());

			if(!$image_file || !$image_thumb_type)
			{
				NavigationHelper::location_error_not_found();
			}

			$image_file_thumb = ZfImageFileThumb::get_by_id_image_file_id_image_thumb_type($id_image_file, $id_image_thumb_type);

			if(!$image_file_thumb)
			{
				$image_file_thumb = new ZfImageFileThumb();
				$image_file_thumb->set_id_image_file($id_image_file);
				$image_file_thumb->set_id_image_thumb_type($id_image_thumb_type);
				$image_file_thumb->set_path($image_file->get_thumb_path($image_file_thumb, false));
				$image_file_thumb->save();
			}

			$path = $image_file->get_thumb_path($image_thumb_type);

			if(!file_exists($path))
			{
				$this->_image = new Image($image_file->get_full_path());
				$image_thumb_type->resize_image($this->_image, $image_file);

				if(ZPHP::get_config('image_save_thumb'))
				{
					$this->_image->save_copy($path, $image_type->get_extension());
				}
			}
			else
			{
				$this->_image = new Image($path);
			}

			$this->_image->set_type($image_type->get_extension());

		}
		else
		{
			$image_file = ZfImageFile::get_by_id_image_file($image_str);

			if($image_file)
			{
				$this->_image = new Image($image_file->get_full_path());
			}
		}
	}
	
	
	//------------------------------------------------------------------------------------------------------------------------------------
	
	public function out() {

		if($this->_image)
		{
			$this->_image->out();
		}

	}
	
	public function save_to($filename) {
		if($this->_image)
		{
			$this->_image->save_to($filename);
		}
	}

	public function out_attachment($filename=null) {
		if($this->_image)
		{
			$this->_image->out_attachment($filename);
		}
	}
}