<?php

class ZfLanguageCache extends ZfLanguageDatabase
{

	/* ZPHP Generated Code ------------------------------------------ */


	private static $_CACHE_MANAGER_KEY_GET_PREFIX = "cache_zflanguageentity_get_by_";

	private static function _generate_cache_key_get_by_id_language($id_language) {
		$values = func_get_args();
		return self::$_CACHE_MANAGER_KEY_GET_PREFIX.'id_language_'.implode('_', $values);
	}

	private static function _generate_cache_key_list_all() {
		return self::$_CACHE_MANAGER_KEY_GET_PREFIX.'_list_all';
	}
	
	private static function _cache_save($entity) {
		if($entity) {
			CacheManager::save(self::_generate_cache_key_get_by_id_language($entity->get_id_language()), $entity);
		}

		if(isset(self::$_CACHE_SAVE_ENTITY_CALLBACKS))
		{
			foreach((array) self::$_CACHE_SAVE_ENTITY_CALLBACKS as $save_callback)
			{
				call_user_func(array(self, $save_callback), $entity);
			}
		}
	}

	private static function _cache_delete($entity) {
		if($entity) {
			CacheManager::delete(self::_generate_cache_key_get_by_id_language($entity->get_id_language()));
		}

		if(isset(self::$_CACHE_DELETE_ENTITY_CALLBACKS))
		{
			foreach((array) self::$_CACHE_DELETE_ENTITY_CALLBACKS as $delete_callback)
			{
				call_user_func(array(self, $delete_callback), $entity);
			}
		}
	}

	private static function _cache_delete_list_all()
	{
		CacheManager::delete(self::_generate_cache_key_list_all());
	}
	
	/*------------------------------------------------------------------------*/ 
	
		
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

						
						
	/**
	*
	* @return ZfLanguage[]
	*
	*/
	public static function list_all($conditions=null, $order=null, $limit=null)
	{
		if(is_null($conditions) && is_null($order) && is_null($limit))
		{
			$cache_key = self::_generate_cache_key_list_all();

			$rows = CacheManager::get($cache_key);

			if(is_null($rows))
			{
				$rows = parent::list_all($conditions, $order, $limit);
				CacheManager::save($cache_key, $rows);
			}

			return $rows;
		}
		else
		{
			return parent::list_all($conditions, $order, $limit);
		}
	}
	
	

	public static function saveEntity(ZfLanguage $entity) {
		parent::saveEntity($entity);
		self::_cache_delete($entity);
		self::_cache_save($entity);
		self::_cache_delete_list_all();
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
		self::_cache_delete_list_all();
		parent::delete_rows($conditions);
	}
	
	
	public static function update_rows($values, $conditions=array()) {
	
		$return = parent::update_rows($values, $conditions);

		$rows = parent::list_all($conditions);

		foreach($rows as $row)
		{
			self::_cache_save($row);
		}

		self::_cache_delete_list_all();

		return $return; 
		
	}
	
	
	/* /ZPHP Generated Code ------------------------------------------ */

	private static $_CACHE_DELETE_ENTITY_CALLBACKS = array(
		'_cache_delete_custom'
	);

	private static $_CACHE_SAVE_ENTITY_CALLBACKS = array(
		'_cache_save_custom'
	);

	private static function _generate_cache_key_get_by_id_language_code($id_language_code) {
		return self::$_CACHE_MANAGER_KEY_GET_PREFIX.'id_language_code'.$id_language_code;
	}

	private static function _cache_delete_custom($entity)
	{
		if($entity)
		{
			$cache_key = self::_generate_cache_key_get_by_id_language_code($entity->id_language_code);
			CacheManager::delete($cache_key);
		}
	}

	private static function _cache_save_custom($entity)
	{
		if($entity)
		{
			$cache_key = self::_generate_cache_key_get_by_id_language_code($entity->id_language_code);
			CacheManager::save($cache_key, $entity);
		}
	}


	/**
	 *
	 * @return ZfLanguage
	 *
	 */
	public static function get_by_id_language_code($language_code)
	{
		$cache_key = self::_generate_cache_key_get_by_id_language_code($language_code);

		$row = CacheManager::get($cache_key);

		if(is_null($row))
		{
			$row = parent::get_by_id_language_code($language_code);
			CacheManager::save($cache_key, $row);
		}

		return $row;
	}
}

