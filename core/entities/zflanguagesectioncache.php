<?php

class ZfLanguageSectionCache extends ZfLanguageSectionDatabase
{

	/* ZPHP Generated Code ------------------------------------------ */


	protected static $_CACHE_MANAGER_KEY_GET_PREFIX = "cache_zflanguagesectionentity_get_by_";

	protected static function _generate_cache_key_get_by_id_language_section($id_language_section) {
		$values = func_get_args();
		return self::$_CACHE_MANAGER_KEY_GET_PREFIX.'id_language_section_'.implode('_', $values);
	}

	protected static function _cache_save($entity) {
		if($entity) {
			CacheManager::save(self::_generate_cache_key_get_by_id_language_section($entity->get_id_language_section()), $entity);
		}
	}

	protected static function _cache_delete($entity) {
		if($entity) {
			CacheManager::delete(self::_generate_cache_key_get_by_id_language_section($entity->get_id_language_section()));
		}
	}

	
	/**
	* @return ZfLanguageSection
	*/
	public static function get_by_id_language_section($id_language_section, $column=null) {
		$entity = CacheManager::get(self::_generate_cache_key_get_by_id_language_section($id_language_section));

		if(!$entity) {
			$entity = parent::get_by_id_language_section($id_language_section);
			if($entity) self::_cache_save($entity);
		}

		return ($entity && $column) ? $entity->$column : $entity;
	}


	public static function saveEntity(ZfLanguageSection $entity) {
		parent::saveEntity($entity);
		self::_cache_save($entity);
	}
	
	
	public static function delete_by_id_language_section($id_language_section) {

		$conditions = array();
		$conditions['id_language_section'] = $id_language_section;
		return ZfLanguageSection::delete_rows($conditions);

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

