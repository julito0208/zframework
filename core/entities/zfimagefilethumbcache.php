<?php

class ZfImageFileThumbCache extends ZfImageFileThumbDatabase
{

	/* ZPHP Generated Code ------------------------------------------ */


	private static $_CACHE_MANAGER_KEY_GET_PREFIX = "cache_zfimagefilethumbentity_get_by_";

	private static function _generate_cache_key_get_by_id_image_file_id_image_thumb_type($id_image_file, $id_image_thumb_type) {
		$values = func_get_args();
		return self::$_CACHE_MANAGER_KEY_GET_PREFIX.'id_image_file_id_image_thumb_type_'.implode('_', $values);
	}

	private static function _generate_cache_key_list_all() {
		return self::$_CACHE_MANAGER_KEY_GET_PREFIX.'_list_all';
	}
	
	private static function _cache_save($entity) {
		if($entity) {
			CacheManager::save(self::_generate_cache_key_get_by_id_image_file_id_image_thumb_type($entity->get_id_image_file(), $entity->get_id_image_thumb_type()), $entity);
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
			CacheManager::delete(self::_generate_cache_key_get_by_id_image_file_id_image_thumb_type($entity->get_id_image_file(), $entity->get_id_image_thumb_type()));
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
	* @return ZfImageFileThumb
	*/
	public static function get_by_id_image_file_id_image_thumb_type($id_image_file, $id_image_thumb_type, $column=null) {
		$entity = CacheManager::get(self::_generate_cache_key_get_by_id_image_file_id_image_thumb_type($id_image_file, $id_image_thumb_type));

		if(!$entity) {
			$entity = parent::get_by_id_image_file_id_image_thumb_type($id_image_file, $id_image_thumb_type);
			if($entity) self::_cache_save($entity);
		}

		return ($entity && $column) ? $entity->$column : $entity;
	}

						
						
	/**
	*
	* @return ZfImageFileThumb[]
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
	
	

	public static function saveEntity(ZfImageFileThumb $entity) {
		parent::saveEntity($entity);
		self::_cache_delete($entity);
		self::_cache_save($entity);
		self::_cache_delete_list_all();
	}
	
	
	public static function delete_by_id_image_file_id_image_thumb_type($id_image_file, $id_image_thumb_type) {

		$conditions = array();
		$conditions['id_image_file'] = $id_image_file;
		$conditions['id_image_thumb_type'] = $id_image_thumb_type;
		return ZfImageFileThumb::delete_rows($conditions);

	}
	
	
	public static function delete_by_id_image_file($id_image_file) {

		$conditions = array();
		$conditions['id_image_file'] = $id_image_file;
		return ZfImageFileThumb::delete_rows($conditions);

	}
	
	
	public static function delete_by_id_image_thumb_type($id_image_thumb_type) {

		$conditions = array();
		$conditions['id_image_thumb_type'] = $id_image_thumb_type;
		return ZfImageFileThumb::delete_rows($conditions);

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

