<?php

class ZfLanguageTextCache extends ZfLanguageTextDatabase
{

	/* ZPHP Generated Code ------------------------------------------ */


	protected static $_CACHE_MANAGER_KEY_GET_PREFIX = "cache_zflanguagetextentity_get_by_";

	protected static function _generate_cache_key_get_by_id_language_text_id_language_section_id_language($id_language_text, $id_language_section, $id_language) {
		return self::$_CACHE_MANAGER_KEY_GET_PREFIX.'id_language_text_id_language_section_id_language'.implode('_', array($id_language_text, $id_language_section, var_export($id_language, true)));
	}


	protected static function _cache_save($entity) {
		if($entity) {
			CacheManager::save(self::_generate_cache_key_get_by_id_language_text_id_language_section_id_language($entity->get_id_language_text(), $entity->get_id_language_section(), $entity->get_id_language()), $entity);
		}
	}

	protected static function _cache_delete($entity) {
		if($entity) {
			CacheManager::delete(self::_generate_cache_key_get_by_id_language_text_id_language_section_id_language($entity->get_id_language_text(), $entity->get_id_language_section(), $entity->get_id_language()));
		}
	}


	/**
	* @return ZfLanguageText
	*/
	public static function get_by_id_language_text_id_language_section_id_language($id_language_text, $id_language_section, $id_language, $column=null) {
		$entity = CacheManager::get(self::_generate_cache_key_get_by_id_language_text_id_language_section_id_language($id_language_text, $id_language_section, $id_language));

		if(!$entity) {
			$entity = parent::get_by_id_language_text_id_language_section_id_language($id_language_text, $id_language_section, $id_language);
			if($entity) self::_cache_save($entity);
		}

		return ($entity && $column) ? $entity->$column : $entity;
	}


	public static function saveEntity(ZfLanguageText $entity) {
		parent::saveEntity($entity);
		self::_cache_save($entity);
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
		parent::delete_rows($conditions);
	}

	/* /ZPHP Generated Code ------------------------------------------ */

}

