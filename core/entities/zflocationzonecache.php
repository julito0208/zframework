<?php

class ZfLocationZoneCache extends ZfLocationZoneDatabase
{

	/* ZPHP Generated Code ------------------------------------------ */


	protected static $_CACHE_MANAGER_KEY_GET_PREFIX = "cache_zflocationzoneentity_get_by_";

	protected static function _generate_cache_key_get_by_id_zone($id_zone) {
		$values = func_get_args();
		return self::$_CACHE_MANAGER_KEY_GET_PREFIX.'id_zone_'.implode('_', $values);
	}

	protected static function _cache_save($entity) {
		if($entity) {
			CacheManager::save(self::_generate_cache_key_get_by_id_zone($entity->get_id_zone()), $entity);
		}
	}

	protected static function _cache_delete($entity) {
		if($entity) {
			CacheManager::delete(self::_generate_cache_key_get_by_id_zone($entity->get_id_zone()));
		}
	}

	
	/**
	* @return ZfLocationZone
	*/
	public static function get_by_id_zone($id_zone, $column=null) {
		$entity = CacheManager::get(self::_generate_cache_key_get_by_id_zone($id_zone));

		if(!$entity) {
			$entity = parent::get_by_id_zone($id_zone);
			if($entity) self::_cache_save($entity);
		}

		return ($entity && $column) ? $entity->$column : $entity;
	}


	public static function saveEntity(ZfLocationZone $entity) {
		parent::saveEntity($entity);
		self::_cache_save($entity);
	}
	
	
	public static function delete_by_id_zone($id_zone) {

		$conditions = array();
		$conditions['id_zone'] = $id_zone;
		return ZfLocationZone::delete_rows($conditions);

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

