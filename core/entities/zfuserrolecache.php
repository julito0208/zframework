<?php

class ZfUserRoleCache extends ZfUserRoleDatabase
{

	/* ZPHP Generated Code ------------------------------------------ */


	private static $_CACHE_MANAGER_KEY_GET_PREFIX = "cache_zfuserroleentity_get_by_";

	private static function _generate_cache_key_get_by_id_user_id_role($id_user, $id_role) {
		$values = func_get_args();
		return self::$_CACHE_MANAGER_KEY_GET_PREFIX.'id_user_id_role_'.implode('_', $values);
	}

	private static function _generate_cache_key_list_all() {
		return self::$_CACHE_MANAGER_KEY_GET_PREFIX.'_list_all';
	}
	
	private static function _cache_save($entity) {
		if($entity) {
			CacheManager::save(self::_generate_cache_key_get_by_id_user_id_role($entity->get_id_user(), $entity->get_id_role()), $entity);
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
			CacheManager::delete(self::_generate_cache_key_get_by_id_user_id_role($entity->get_id_user(), $entity->get_id_role()));
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
	* @return ZfUserRole
	*/
	public static function get_by_id_user_id_role($id_user, $id_role, $column=null) {
		$entity = CacheManager::get(self::_generate_cache_key_get_by_id_user_id_role($id_user, $id_role));

		if(!$entity) {
			$entity = parent::get_by_id_user_id_role($id_user, $id_role);
			if($entity) self::_cache_save($entity);
		}

		return ($entity && $column) ? $entity->$column : $entity;
	}

						
						
	/**
	*
	* @return ZfUserRole[]
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
	
	

	public static function saveEntity(ZfUserRole $entity) {
		parent::saveEntity($entity);
		self::_cache_delete($entity);
		self::_cache_save($entity);
		self::_cache_delete_list_all();
	}
	
	
	public static function delete_by_id_user_id_role($id_user, $id_role) {

		$conditions = array();
		$conditions['id_user'] = $id_user;
		$conditions['id_role'] = $id_role;
		return ZfUserRole::delete_rows($conditions);

	}
	
	
	public static function delete_by_id_user($id_user) {

		$conditions = array();
		$conditions['id_user'] = $id_user;
		return ZfUserRole::delete_rows($conditions);

	}
	
	
	public static function delete_by_id_role($id_role) {

		$conditions = array();
		$conditions['id_role'] = $id_role;
		return ZfUserRole::delete_rows($conditions);

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

