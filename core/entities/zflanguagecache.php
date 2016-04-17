<?php

class ZfLanguageCache extends ZfLanguageDatabase
{

	/* ZPHP Generated Code ------------------------------------------ */


	protected static $_CACHE_MANAGER_KEY_GET_PREFIX = "cache_zflanguageentity_get_by_";

	protected static function _generate_cache_key_get_by_id_language($id_language) {
		$values = func_get_args();
		return self::$_CACHE_MANAGER_KEY_GET_PREFIX.'id_language_'.implode('_', $values);
	}

	protected static function _generate_cache_key_get_by_id_language_code($id_language_code) {
		$values = func_get_args();
		return self::$_CACHE_MANAGER_KEY_GET_PREFIX.'id_language_code'.implode('_', $values);
	}

	protected static function _cache_save($entity) {
		if($entity) {
			CacheManager::save(self::_generate_cache_key_get_by_id_language($entity->get_id_language()), $entity);
			CacheManager::save(self::_generate_cache_key_get_by_id_language_code($entity->get_id_language_code()), $entity);
		}
	}

	protected static function _cache_delete($entity) {
		if($entity) {
			CacheManager::delete(self::_generate_cache_key_get_by_id_language($entity->get_id_language()));
			CacheManager::delete(self::_generate_cache_key_get_by_id_language_code($entity->get_id_language_code()));
		}
	}


	/**
	 *
	 * @return ZfLanguage
	 *
	 */
	public static function get_by_id_language_code($language_code)
	{
		$entity = CacheManager::get(self::_generate_cache_key_get_by_id_language_code($language_code));

		if(!$entity) {
			$entity = parent::get_by_id_language_code($language_code);
			if($entity) self::_cache_save($entity);
		}

		return $entity;
	}

	/**
	* @return ZfLanguage
	*/
	public static function get_by_id_language($id_language, $column=null) {
		$entity = CacheManager::get(self::_generate_cache_key_get_by_id_language($id_language));

		if(!$entity) {
			$entity = parent::get_by_id_language($id_language);
			if($entity) self::_cache_save($entity);
		}

		return ($entity && $column) ? $entity->$column : $entity;
	}


	public static function saveEntity(ZfLanguage $entity) {
		parent::saveEntity($entity);
		self::_cache_save($entity);
	}
	
	
	public static function delete_by_id_language($id_language) {

		$conditions = array();
		$conditions['id_language'] = $id_language;
		return ZfLanguage::delete_rows($conditions);

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

