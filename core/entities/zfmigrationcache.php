<?php

class ZfMigrationCache extends ZfMigrationDatabase
{

	/* ZPHP Generated Code ------------------------------------------ */


	protected static $_CACHE_MANAGER_KEY_GET_PREFIX = "cache_zfmigrationentity_get_by_";

	protected static function _generate_cache_key_get_by_id_migration($id_migration) {
		$values = func_get_args();
		return self::$_CACHE_MANAGER_KEY_GET_PREFIX.'id_migration_'.implode('_', $values);
	}

	protected static function _cache_save($entity) {
		if($entity) {
			CacheManager::save(self::_generate_cache_key_get_by_id_migration($entity->get_id_migration()), $entity);
		}
	}

	protected static function _cache_delete($entity) {
		if($entity) {
			CacheManager::delete(self::_generate_cache_key_get_by_id_migration($entity->get_id_migration()));
		}
	}

	
	/**
	* @return ZfMigration
	*/
	public static function get_by_id_migration($id_migration, $column=null) {
		$entity = CacheManager::get(self::_generate_cache_key_get_by_id_migration($id_migration));

		if(!$entity) {
			$entity = parent::get_by_id_migration($id_migration);
			if($entity) self::_cache_save($entity);
		}

		return ($entity && $column) ? $entity->$column : $entity;
	}


	public static function saveEntity(ZfMigration $entity) {
		parent::saveEntity($entity);
		self::_cache_save($entity);
	}
	
	
	public static function delete_by_id_migration($id_migration) {

		$conditions = array();
		$conditions['id_migration'] = $id_migration;
		return ZfMigration::delete_rows($conditions);

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

