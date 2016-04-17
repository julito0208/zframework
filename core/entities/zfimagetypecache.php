<?php

class ZfImageTypeCache extends ZfImageTypeDatabase
{

	/* ZPHP Generated Code ------------------------------------------ */


	protected static $_CACHE_MANAGER_KEY_GET_PREFIX = "cache_zfimagetypeentity_get_by_";

	protected static function _generate_cache_key_get_by_id_image_type($id_image_type) {
		$values = func_get_args();
		return self::$_CACHE_MANAGER_KEY_GET_PREFIX.'id_image_type_'.implode('_', $values);
	}

	protected static function _cache_save($entity) {
		if($entity) {
			CacheManager::save(self::_generate_cache_key_get_by_id_image_type($entity->get_id_image_type()), $entity);
		}
	}

	protected static function _cache_delete($entity) {
		if($entity) {
			CacheManager::delete(self::_generate_cache_key_get_by_id_image_type($entity->get_id_image_type()));
		}
	}

	
	/**
	* @return ZfImageType
	*/
	public static function get_by_id_image_type($id_image_type, $column=null) {
		$entity = CacheManager::get(self::_generate_cache_key_get_by_id_image_type($id_image_type));

		if(!$entity) {
			$entity = parent::get_by_id_image_type($id_image_type);
			if($entity) self::_cache_save($entity);
		}

		return ($entity && $column) ? $entity->$column : $entity;
	}


	public static function saveEntity(ZfImageType $entity) {
		parent::saveEntity($entity);
		self::_cache_save($entity);
	}
	
	
	public static function delete_by_id_image_type($id_image_type) {

		$conditions = array();
		$conditions['id_image_type'] = $id_image_type;
		return ZfImageType::delete_rows($conditions);

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

