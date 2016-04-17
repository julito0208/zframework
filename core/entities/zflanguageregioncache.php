<?php

class ZfLanguageRegionCache extends ZfLanguageRegionDatabase
{

	/* ZPHP Generated Code ------------------------------------------ */


	protected static $_CACHE_MANAGER_KEY_GET_PREFIX = "cache_zflanguageregionentity_get_by_";

	protected static function _generate_cache_key_get_by_id_language_region($id_language_region) {
		$values = func_get_args();
		return self::$_CACHE_MANAGER_KEY_GET_PREFIX.'id_language_region_'.implode('_', $values);
	}

	protected static function _cache_save($entity) {
		if($entity) {
			CacheManager::save(self::_generate_cache_key_get_by_id_language_region($entity->get_id_language_region()), $entity);
		}
	}

	protected static function _cache_delete($entity) {
		if($entity) {
			CacheManager::delete(self::_generate_cache_key_get_by_id_language_region($entity->get_id_language_region()));
		}
	}

	
	/**
	* @return ZfLanguageRegion
	*/
	public static function get_by_id_language_region($id_language_region, $column=null) {
		$entity = CacheManager::get(self::_generate_cache_key_get_by_id_language_region($id_language_region));

		if(!$entity) {
			$entity = parent::get_by_id_language_region($id_language_region);
			if($entity) self::_cache_save($entity);
		}

		return ($entity && $column) ? $entity->$column : $entity;
	}


	public static function saveEntity(ZfLanguageRegion $entity) {
		parent::saveEntity($entity);
		self::_cache_save($entity);
	}
	
	
	public static function delete_by_id_language_region($id_language_region) {

		$conditions = array();
		$conditions['id_language_region'] = $id_language_region;
		return ZfLanguageRegion::delete_rows($conditions);

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

