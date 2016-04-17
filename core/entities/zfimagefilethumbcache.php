<?php

class ZfImageFileThumbCache extends ZfImageFileThumbDatabase
{

	/* ZPHP Generated Code ------------------------------------------ */


	protected static $_CACHE_MANAGER_KEY_GET_PREFIX = "cache_zfimagefilethumbentity_get_by_";

	protected static function _generate_cache_key_get_by_id_image_file_id_image_thumb_type($id_image_file, $id_image_thumb_type) {
		$values = func_get_args();
		return self::$_CACHE_MANAGER_KEY_GET_PREFIX.'id_image_file_id_image_thumb_type_'.implode('_', $values);
	}

	protected static function _cache_save($entity) {
		if($entity) {
			CacheManager::save(self::_generate_cache_key_get_by_id_image_file_id_image_thumb_type($entity->get_id_image_file(), $entity->get_id_image_thumb_type()), $entity);
		}
	}

	protected static function _cache_delete($entity) {
		if($entity) {
			CacheManager::delete(self::_generate_cache_key_get_by_id_image_file_id_image_thumb_type($entity->get_id_image_file(), $entity->get_id_image_thumb_type()));
		}
	}

	
	/**
	* @return ZfImageFileThumb
	*/
	public static function get_by_id_image_file_id_image_thumb_type($id_image_file, $id_image_thumb_type, $column=null) {
		$entity = CacheManager::get(self::_generate_cache_key_get_by_id_image_file_id_image_thumb_type($id_image_file, $id_image_thumb_type));

		if(!$entity) {
			$entity = parent::get_by_id_image_file_id_image_thumb_type($id_image_file, $id_image_thumb_type);
			if($entity) self::_cache_save($entity);
		}

		return ($entity && $column) ? $entity->$column : $entity;
	}


	public static function saveEntity(ZfImageFileThumb $entity) {
		parent::saveEntity($entity);
		self::_cache_save($entity);
	}
	
	
	public static function delete_by_id_image_file_id_image_thumb_type($id_image_file, $id_image_thumb_type) {

		$conditions = array();
		$conditions['id_image_file'] = $id_image_file;
		$conditions['id_image_thumb_type'] = $id_image_thumb_type;
		return ZfImageFileThumb::delete_rows($conditions);

	}
	
	
	public static function delete_by_id_image_file($id_image_file) {

		$conditions = array();
		$conditions['id_image_file'] = $id_image_file;
		return ZfImageFileThumb::delete_rows($conditions);

	}
	
	
	public static function delete_by_id_image_thumb_type($id_image_thumb_type) {

		$conditions = array();
		$conditions['id_image_thumb_type'] = $id_image_thumb_type;
		return ZfImageFileThumb::delete_rows($conditions);

	}
	
	
	public static function delete_rows($conditions=array()) {
		$rows = self::list_all($conditions);
		foreach($rows as $row){
			self::_cache_delete($row);
		}
		parent::delete_rows($conditions);
	}

	/* /ZPHP Generated Code ------------------------------------------ */

}

