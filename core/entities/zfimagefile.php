<?php

class ZfImageFile extends ZfImageFileCache
{
	const DEFAULT_ID_IMAGE_FILE = 'default';

	/*------------------------------------------------------------------------------------------------------------*/

	/**
	*
	* @return ZfImageFile
	*
	*/
	public static function get_by_imageable(Imageable $imageable)
	{
		return self::get_by_id_image_file($imageable->get_id_image_file());
	}

	public static function delete_rows($conditions=array())
	{
		$conditions = (array) $conditions;
		$rows = self::list_all($conditions);

		foreach($rows as $row)
		{
			@ unlink($row->get_full_path());
			ZfImageFileThumb::delete_rows(array('id_image_file' => $row->get_id_image_file()));
		}

		parent::delete_rows($conditions);
	}

	/**
	*
	* @return ZfImageFile[]
	*
	*/
	public static function list_by_imageables(array $imageables)
	{
		$image_files = array();

		foreach($imageables as $imageable)
		{
			if($imageable instanceof Imageable)
			{
				$image_files[] = self::get_by_id_image_file($imageable->get_id_image_file());
			}
		}

		return $image_files;
	}


	/*-------------------------------------------------------------*/

	/**
	*
	* @return ZfImageFile
	*
	*/
	public static function create_from_image(Image $image, $prefix=null, $pos=null, $title=null, $id_group=null)
	{
		$id_image_file = uniqid($prefix);

		$type = ZfImageType::get_by_id_image_type(ZfImageType::get_default_type());
		$path = '/'.sprintf(ZPHP::get_config('image.path_format'), $id_image_file, $type->get_extension());

		$image_file = new ZfImageFile();
		$image_file->set_width($image->get_width());
		$image_file->set_height($image->get_height());
		$image_file->set_id_group($id_group);
		$image_file->set_pos($pos);
		$image_file->set_title($title);
		$image_file->set_id_image_file($id_image_file);
		$image_file->set_path($path);
		$image_file->set_date_added(time());
		$image_file->save();

		$image_thumb = new ZfImageFileThumb();
		$image_thumb->set_id_image_file($image_file->get_id_image_file());
		$image_thumb->set_id_image_thumb_type(ZfImageThumbType::DEFAULT_THUMB_TYPE);
		$image_thumb->set_path($image_file->get_path());
		$image_thumb->save();

		$image->save_copy($image_file->get_full_path(), $type->get_extension());

		return $image_file;
	}


	/**
	 *
	 * @return ZfImageFile
	 *
	 */
	public static function create_from_request_file($name, $prefix=null, $pos=null, $title=null, $id_group=null, $index=null)
	{
		$image = Image::from_uploaded_file($name, $index);

		if(is_null($title))
		{
			if(is_null($index))
			{
				$title = $_FILES[$name]['name'];
			}
			else
			{
				$title = $_FILES[$name]['name'][$index];
			}

			$title = preg_replace('#\.\w+$#', '', $title);
		}

		return self::create_from_image($image, $prefix, $pos, $title, $id_group);
	}


	/**
	 *
	 * @return ZfImageFile
	 *
	 */
	public static function create_from_request_file_index($name, $index, $prefix=null, $pos=null, $id_group=null, $title=null)
	{
		return self::create_from_request_file($name, $prefix, $pos, $title, $id_group, $index);
	}

	/**
	 *
	 * @return ZfImageFile
	 *
	 */
	public static function create_from_path($path, $prefix=null, $pos=null, $id_group=null, $title=null)
	{
		$image = new Image($path);
		return self::create_from_image($image, $prefix, $pos, $id_group, $title);
	}

	/*------------------------------------------------------------------------------------------------------------*/


	public function get_full_path()
	{
		return ZPHP::get_www_dir().$this->get_path();
	}

	public function get_thumb_path($image_thumb_type=null, $full_path=true)
	{
		if($image_thumb_type)
		{
			if(ClassHelper::is_instance_of($image_thumb_type, 'ZfImageFileThumb'))
			{
				$image_thumb_type = ZfImageThumbType::get_by_id_image_thumb_type($image_thumb_type->get_id_image_thumb_type());
			}

			if(!ClassHelper::is_instance_of($image_thumb_type, 'ZfImageThumbType'))
			{
				$image_thumb_type = ZfImageThumbType::get_by_id_image_thumb_type($image_thumb_type);
			}

			$id_image_file = $this->get_id_image_file();

			$type = ZfImageType::get_by_id_image_type($image_thumb_type->get_id_image_type());
			$path = sprintf(ZPHP::get_config('image.path_format'), $id_image_file.'-'.$image_thumb_type->get_id_image_thumb_type(), $type->get_extension());
			$path = StringHelper::put_prefix($path, '/');

			if($full_path)
			{
				$path = ZPHP::get_www_dir().$path;
			}

			return $path;

		}
		else
		{
			return $this->get_full_path();
		}
	}

	public function get_thumb_url($image_thumb_type=null)
	{
		if($image_thumb_type)
		{
			if(ClassHelper::is_instance_of($image_thumb_type, 'ZfImageFileThumb'))
			{
				$image_thumb_type = ZfImageThumbType::get_by_id_image_thumb_type($image_thumb_type->get_id_image_thumb_type());
			}

			if(!ClassHelper::is_instance_of($image_thumb_type, 'ZfImageThumbType'))
			{
				$image_thumb_type = ZfImageThumbType::get_by_id_image_thumb_type($image_thumb_type);
			}

			$id_image_file = $this->get_id_image_file();

			if(!$image_thumb_type)
			{
				$image_thumb_type = ZfImageThumbType::get_by_id_image_thumb_type(ZPHP::get_config('image.default_thumb_type'));
			}

			$type = ZfImageType::get_by_id_image_type($image_thumb_type->get_id_image_type());
//			var_export($type->get_extension());
//			die();
			$url = sprintf(ZPHP::get_config('image.url_format'), $id_image_file.'-'.$image_thumb_type->get_id_image_thumb_type(), $type->get_extension());

			return $url;

		}
		else
		{

			$id_image_file = $this->get_id_image_file();
			$type = ZfImageType::get_by_id_image_type(ZfImageType::get_default_type());

			$url = sprintf(ZPHP::get_config('image.url_format'), $id_image_file, $type->get_extension());
			return $url;
		}
	}

	/*-------------------------------------------------------------*/

	public static function get_image_url($object, $image_thumb_type=null, $use_default=true)
	{
		if(!$object)
		{
			return null;
		}

		if(ClassHelper::is_instance_of($object, 'Imageable'))
		{
			$id_image_file = $object->get_id_image_file();

			if(!$id_image_file)
			{
				if($use_default)
				{
					$id_image_file = self::DEFAULT_ID_IMAGE_FILE;
				}
				else
				{
					return null;
				}
			}

			$image_file = ZfImageFile::get_by_id_image_file($id_image_file);

			if(!$image_file)
			{
				return null;
			}

			return $image_file->get_thumb_url($image_thumb_type);
		}
		else
		{

			return self::get_image_url(ZfImageFile::get_by_id_image_file($object), $image_thumb_type, $use_default);
		}
	}

	/* ZPHP Generated Code ------------------------------------------ */
	/* /ZPHP Generated Code ------------------------------------------ */

}

