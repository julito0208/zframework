<?php

class ZfLocationCountryCache extends ZfLocationCountryDatabase
{

	/* ZPHP Generated Code ------------------------------------------ */


	protected static $_CACHE_MANAGER_KEY_GET_PREFIX = "cache_zflocationcountryentity_get_by_";

	protected static function _generate_cache_key_get_by_id_country($id_country) {
		$values = func_get_args();
		return self::$_CACHE_MANAGER_KEY_GET_PREFIX.'id_country_'.implode('_', $values);
	}

	protected static function _cache_save($entity) {
		if($entity) {
			CacheManager::save(self::_generate_cache_key_get_by_id_country($entity->get_id_country()), $entity);
		}
	}

	protected static function _cache_delete($entity) {
		if($entity) {
			CacheManager::delete(self::_generate_cache_key_get_by_id_country($entity->get_id_country()));
		}
	}

	
	/**
	* @return ZfLocationCountry
	*/
	public static function get_by_id_country($id_country, $column=null) {
		$entity = CacheManager::get(self::_generate_cache_key_get_by_id_country($id_country));

		if(!$entity) {
			$entity = parent::get_by_id_country($id_country);
			if($entity) self::_cache_save($entity);
		}

		return ($entity && $column) ? $entity->$column : $entity;
	}


	public static function saveEntity(ZfLocationCountry $entity) {
		parent::saveEntity($entity);
		self::_cache_save($entity);
	}
	
	
	public static function delete_by_id_country($id_country) {

		$conditions = array();
		$conditions['id_country'] = $id_country;
		return ZfLocationCountry::delete_rows($conditions);

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

