<?php

class ZfLocationCityCache extends ZfLocationCityDatabase
{

	/* ZPHP Generated Code ------------------------------------------ */


	protected static $_CACHE_MANAGER_KEY_GET_PREFIX = "cache_zflocationcityentity_get_by_";

	protected static function _generate_cache_key_get_by_id_city($id_city) {
		$values = func_get_args();
		return self::$_CACHE_MANAGER_KEY_GET_PREFIX.'id_city_'.implode('_', $values);
	}

	protected static function _cache_save($entity) {
		if($entity) {
			CacheManager::save(self::_generate_cache_key_get_by_id_city($entity->get_id_city()), $entity);
		}
	}

	protected static function _cache_delete($entity) {
		if($entity) {
			CacheManager::delete(self::_generate_cache_key_get_by_id_city($entity->get_id_city()));
		}
	}

	
	/**
	* @return ZfLocationCity
	*/
	public static function get_by_id_city($id_city, $column=null) {
		$entity = CacheManager::get(self::_generate_cache_key_get_by_id_city($id_city));

		if(!$entity) {
			$entity = parent::get_by_id_city($id_city);
			if($entity) self::_cache_save($entity);
		}

		return ($entity && $column) ? $entity->$column : $entity;
	}


	public static function saveEntity(ZfLocationCity $entity) {
		parent::saveEntity($entity);
		self::_cache_save($entity);
	}
	
	
	public static function delete_by_id_city($id_city) {

		$conditions = array();
		$conditions['id_city'] = $id_city;
		return ZfLocationCity::delete_rows($conditions);

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

