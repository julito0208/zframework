<?php

class ZfImageThumbType extends ZfImageThumbTypeCache
{

	const DEFAULT_THUMB_TYPE = 'default';
	const CROP_THUMB_TYPE = 'crop';

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

