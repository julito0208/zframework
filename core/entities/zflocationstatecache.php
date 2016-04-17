<?php

class ZfLocationStateCache extends ZfLocationStateDatabase
{

	/* ZPHP Generated Code ------------------------------------------ */


	protected static $_CACHE_MANAGER_KEY_GET_PREFIX = "cache_zflocationstateentity_get_by_";

	protected static function _generate_cache_key_get_by_id_state($id_state) {
		$values = func_get_args();
		return self::$_CACHE_MANAGER_KEY_GET_PREFIX.'id_state_'.implode('_', $values);
	}

	protected static function _cache_save($entity) {
		if($entity) {
			CacheManager::save(self::_generate_cache_key_get_by_id_state($entity->get_id_state()), $entity);
		}
	}

	protected static function _cache_delete($entity) {
		if($entity) {
			CacheManager::delete(self::_generate_cache_key_get_by_id_state($entity->get_id_state()));
		}
	}

	
	/**
	* @return ZfLocationState
	*/
	public static function get_by_id_state($id_state, $column=null) {
		$entity = CacheManager::get(self::_generate_cache_key_get_by_id_state($id_state));

		if(!$entity) {
			$entity = parent::get_by_id_state($id_state);
			if($entity) self::_cache_save($entity);
		}

		return ($entity && $column) ? $entity->$column : $entity;
	}


	public static function saveEntity(ZfLocationState $entity) {
		parent::saveEntity($entity);
		self::_cache_save($entity);
	}
	
	
	public static function delete_by_id_state($id_state) {

		$conditions = array();
		$conditions['id_state'] = $id_state;
		return ZfLocationState::delete_rows($conditions);

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

