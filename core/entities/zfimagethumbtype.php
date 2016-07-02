<?php

class ZfImageThumbType extends ZfImageThumbTypeCache
{

	const DEFAULT_THUMB_TYPE = 'default';
	const CROP_THUMB_TYPE = 'crop';

	/*-------------------------------------------------------------*/

	/**
	*
	* @return ZfImageThumbType
	*
	*/
	public static function get_thumb($width=null, $height=null, $crop=false, $image_type=ZfImageType::TYPE_PNG)
	{
		$id_keys = array();

		if($width)
		{
			$id_keys[] = $width;
		}
		else
		{
			$id_keys[] = 'null';
		}

		if($height)
		{
			$id_keys[] = $height;
		}
		else
		{
			$id_keys[] = 'null';
		}

		$id_keys[] = $crop ? '0' : '1';
		$id_keys[] = $image_type;

		$id = implode('-', $id_keys);

		$thumb_type = ZfImageThumbType::get_by_id_image_thumb_type($id);

		if(!$thumb_type)
		{
			$thumb_type = new ZfImageThumbType();
			$thumb_type->set_thumb_width($width);
			$thumb_type->set_thumb_height($height);
			$thumb_type->set_use_image_crop($crop);
			$thumb_type->set_id_image_type($image_type);
			$thumb_type->set_id_image_thumb_type($id);
			$thumb_type->save();
		}

		return $thumb_type;
	}


	/**
	 *
	 * @return ZfImageThumbType
	 *
	 */
	public static function get_thumb_crop($width=null, $height=null, $image_type=ZfImageType::TYPE_PNG)
	{
		return self::get_thumb($width, $height, true, $image_type);
	}

	/*-------------------------------------------------------------*/

	public function resize_image(Image $image, ZfImageFile $image_file)
	{
		if($this->get_use_image_crop() && $image_file->crop_width && $image_file->crop_height)
		{
//			$image_file->get_crop_height()

			$crop_pos = array(
				$image_file->crop_x,
				$image_file->crop_y,
			);

			$crop_size = array(
				$image_file->crop_width,
				$image_file->crop_height,
			);

			$image->crop($crop_pos, $crop_size);
		}

		if($this->get_thumb_width() && $this->get_thumb_height())
		{
			$image->fill_canvas_size($this->get_thumb_width(), $this->get_thumb_height());
		}
		else if($this->get_thumb_width())
		{
			$image->set_width($this->get_thumb_width(), true);
//				$image->fill_canvas_width($this->get_thumb_width());
		}
		else if($this->get_thumb_height())
		{
//				$image->fill_canvas_height($this->get_thumb_height());
			$image->set_height($this->get_thumb_height(), true);
		}
	}

	/* ZPHP Generated Code ------------------------------------------ */
	/* /ZPHP Generated Code ------------------------------------------ */

}

