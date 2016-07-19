<?php

class ZfFile extends ZfFileCache
{

	/* ZPHP Generated Code ------------------------------------------ */
	/* /ZPHP Generated Code ------------------------------------------ */

	public static function get_type_icon($type)
	{
		switch(strtolower($type))
		{
			case 'image/jpeg':
			case 'image/jpg':
			case 'image/gif':
			case 'image/png':
			case 'image/bmp':
				return 'fa fa-file-photo-o';
			break;

			case 'application/pdf':
				return 'fa fa-file-pdf-o';
			break;

			default:
				return 'fa fa-file-archive-o';
			break;


		}
	}

	/*-------------------------------------------------------------*/

	public static function delete_rows($conditions=array())
	{
		$conditions = (array) $conditions;
		$rows = self::list_all($conditions);

		foreach($rows as $row)
		{
			@ unlink($row->get_full_path());
//			ZfFile::delete_rows(array('id_file' => $row->get_id_file()));
		}

		parent::delete_rows($conditions);
	}

	/*-------------------------------------------------------------*/


	/**
	 *
	 * @return ZfFile
	 *
	 */
	public static function create_from_post($varname, $index=null, $title=null)
	{
		$id_file = uniqid($prefix);

		$file = new ZfFile();
		$file->set_id_file($id_file);
		$file->save();

		if(is_null($index))
		{
			$path = $_FILES[$varname]['tmp_name'];

			if(!$title)
			{
				$title = $_FILES[$varname]['name'];
			}
		}
		else
		{
			$path = $_FILES[$varname]['tmp_name'][$index];

			if(!$title)
			{
				$title = $_FILES[$varname]['name'][$index];
			}
		}

		$extension = FilesHelper::path_get_extension($title);

		$title = trim($title);
		$title = str_replace('\\', '/', $title);
		$title = preg_replace('#^.*\/#', '', $title);
		$title = preg_replace('#\..*?$#', '', $title);

		$file->set_title($title);

		$npath = '/'.sprintf(ZPHP::get_config('files.path_format'), $id_file);

		if($extension)
		{
			$npath.= '.'.ltrim('.'.$extension, '.');
		}

		$file->set_path($npath);

		move_uploaded_file($path, $file->get_full_path());

		$mimeType = MimeTypeHelper::from_extension(ltrim('.'.$extension, '.'));
		$file->set_mimetype($mimeType);

		$size = filesize($file->get_full_path());
		$file->set_file_size($size);
		$file->save();

		return $file;
	}


	/*------------------------------------------------------------------------------------------------------------*/


	public function get_full_path()
	{
		return ZPHP::get_www_dir().$this->get_path();
	}
}

