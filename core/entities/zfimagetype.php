<?php

class ZfImageType extends ZfImageTypeCache
{

	const TYPE_JPG = 'jpg';
	const TYPE_PNG = 'png';
	const TYPE_GIF = 'gif';


	public static function get_default_type()
	{
		return ZPHP::get_config('image.default_type');
	}


	/**
	*
	* @return ZfImageType
	*
	*/
	public static function get_by_path($path)
	{
		$extension = FilesHelper::path_get_extension($path);
		$extension = ltrim($extension, '.');
		$extension = strtolower($extension);

		switch($extension)
		{
			case 'jpg':
			case 'jpeg':
				return self::get_by_id_image_type(self::TYPE_JPG);
			break;

			case 'png':
				return self::get_by_id_image_type(self::TYPE_PNG);
			break;

			case 'gif':
				return self::get_by_id_image_type(self::TYPE_GIF);
			break;

			default:
				return null;
			break;
		}
	}

	/**
	 *
	 * @return ZfImageType
	 *
	 */
	public static function get_by_upload_file($name, $index=null)
	{
		if(is_null($index))
		{
			$name = $_FILES[$name]['name'];
		}
		else
		{
			$name = $_FILES[$name]['name'][$index];
		}

		return self::get_by_path($name);
	}

	/* ZPHP Generated Code ------------------------------------------ */
	/* /ZPHP Generated Code ------------------------------------------ */

}

