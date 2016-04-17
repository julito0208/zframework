<?php

class ZfImageFileThumb extends ZfImageFileThumbCache
{

	public static function delete_rows($conditions=array())
	{
		$conditions = (array) $conditions;
		$rows = self::list_all($conditions);

		foreach($rows as $row)
		{
			$image_file = ZfImageFile::get_by_id_image_file($row->get_id_image_file());
			@ unlink($image_file->get_thumb_path($row->get_id_image_thumb_type(), true));
		}

		parent::delete_rows($conditions);
	}

	/* ZPHP Generated Code ------------------------------------------ */
	/* /ZPHP Generated Code ------------------------------------------ */

}

