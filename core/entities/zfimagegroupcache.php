<?php

class ZfImageGroupCache extends ZfImageGroupDatabase
{

	/* ZPHP Generated Code ------------------------------------------ */


	protected static $_CACHE_MANAGER_KEY_GET_PREFIX = "cache_zfimagegroupentity_get_by_";

	protected static function _generate_cache_key_get_by_id_group($id_group) {
		$values = func_get_args();
		return self::$_CACHE_MANAGER_KEY_GET_PREFIX.'id_group_'.implode('_', $values);
	}

	protected static function _cache_save($entity) {
		if($entity) {
			CacheManager::save(self::_generate_cache_key_get_by_id_group($entity->get_id_group()), $entity);
		}
	}

	protected static function _cache_delete($entity) {
		if($entity) {
			CacheManager::delete(self::_generate_cache_key_get_by_id_group($entity->get_id_group()));
		}
	}

	
	/**
	* @return ZfImageGroup
	*/
	public static function get_by_id_group($id_group, $column=null) {
		$entity = CacheManager::get(self::_generate_cache_key_get_by_id_group($id_group));

		if(!$entity) {
			$entity = parent::get_by_id_group($id_group);
			if($entity) self::_cache_save($entity);
		}

		return ($entity && $column) ? $entity->$column : $entity;
	}


	public static function saveEntity(ZfImageGroup $entity) {
		parent::saveEntity($entity);
		self::_cache_save($entity);
	}
	
	
	public static function delete_by_id_group($id_group) {

		$conditions = array();
		$conditions['id_group'] = $id_group;
		return ZfImageGroup::delete_rows($conditions);

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

