<?php

class ZfLanguageTextCache extends ZfLanguageTextDatabase
{

	/* ZPHP Generated Code ------------------------------------------ */


	private static $_CACHE_MANAGER_KEY_GET_PREFIX = "cache_zflanguagetextentity_get_by_";

	private static function _generate_cache_key_get_by_id_language_text_id_language_section_id_language($id_language_text, $id_language_section, $id_language) {
		$values = func_get_args();
		return self::$_CACHE_MANAGER_KEY_GET_PREFIX.'id_language_text_id_language_section_id_language_'.implode('_', $values);
	}

	private static function _generate_cache_key_list_all() {
		return self::$_CACHE_MANAGER_KEY_GET_PREFIX.'_list_all';
	}
	
	private static function _cache_save($entity) {
		if($entity) {
			$cache_key = self::_generate_cache_key_get_by_id_language_text_id_language_section_id_language($entity->get_id_language_text(), $entity->get_id_language_section(), $entity->get_id_language());
			CacheManager::save($cache_key, $entity);
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
			CacheManager::delete(self::_generate_cache_key_get_by_id_language_text_id_language_section_id_language($entity->get_id_language_text(), $entity->get_id_language_section(), $entity->get_id_language()));
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
	* @return ZfLanguageText
	*/
	public static function get_by_id_language_text_id_language_section_id_language($id_language_text, $id_language_section, $id_language, $column=null) {
		$entity = CacheManager::get(self::_generate_cache_key_get_by_id_language_text_id_language_section_id_language($id_language_text, $id_language_section, $id_language));

		if(!$entity) {
			$entity = parent::get_by_id_language_text_id_language_section_id_language($id_language_text, $id_language_section, $id_language);
			if($entity)
			{
				self::_cache_save($entity);
			}
		}

		return ($entity && $column) ? $entity->$column : $entity;
	}

						
						
	/**
	*
	* @return ZfLanguageText[]
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
	
	

	public static function saveEntity(ZfLanguageText $entity) {
		parent::saveEntity($entity);
		self::_cache_delete($entity);
		self::_cache_save($entity);
		self::_cache_delete_list_all();
	}
	
	
	public static function delete_by_id_language_text_id_language_section_id_language($id_language_text, $id_language_section, $id_language) {

		$conditions = array();
		$conditions['id_language_text'] = $id_language_text;
		$conditions['id_language_section'] = $id_language_section;
		$conditions['id_language'] = $id_language;
		return ZfLanguageText::delete_rows($conditions);

	}
	
	
	public static function delete_by_id_language_text($id_language_text) {

		$conditions = array();
		$conditions['id_language_text'] = $id_language_text;
		return ZfLanguageText::delete_rows($conditions);

	}
	
	
	public static function delete_by_id_language_section($id_language_section) {

		$conditions = array();
		$conditions['id_language_section'] = $id_language_section;
		return ZfLanguageText::delete_rows($conditions);

	}
	
	
	public static function delete_by_id_language($id_language) {

		$conditions = array();
		$conditions['id_language'] = $id_language;
		return ZfLanguageText::delete_rows($conditions);

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

}

