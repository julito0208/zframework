<?php

class ZfRolesPermissionsCache extends ZfRolesPermissionsDatabase
{

	/* ZPHP Generated Code ------------------------------------------ */


	private static $_CACHE_MANAGER_KEY_GET_PREFIX = "cache_zfrolespermissionsentity_get_by_";

	private static function _generate_cache_key_get_by_id_role_id_permission($id_role, $id_permission) {
		$values = func_get_args();
		return self::$_CACHE_MANAGER_KEY_GET_PREFIX.'id_role_id_permission_'.implode('_', $values);
	}

	private static function _generate_cache_key_list_all() {
		return self::$_CACHE_MANAGER_KEY_GET_PREFIX.'_list_all';
	}
	
	private static function _cache_save($entity) {
		if($entity) {
			CacheManager::save(self::_generate_cache_key_get_by_id_role_id_permission($entity->get_id_role(), $entity->get_id_permission()), $entity);
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
			CacheManager::delete(self::_generate_cache_key_get_by_id_role_id_permission($entity->get_id_role(), $entity->get_id_permission()));
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
	* @return ZfRolesPermissions
	*/
	public static function get_by_id_role_id_permission($id_role, $id_permission, $column=null) {
		$entity = CacheManager::get(self::_generate_cache_key_get_by_id_role_id_permission($id_role, $id_permission));

		if(!$entity) {
			$entity = parent::get_by_id_role_id_permission($id_role, $id_permission);
			if($entity) self::_cache_save($entity);
		}

		return ($entity && $column) ? $entity->$column : $entity;
	}

						
						
	/**
	*
	* @return ZfRolesPermissions[]
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
	
	

	public static function saveEntity(ZfRolesPermissions $entity) {
		parent::saveEntity($entity);
		self::_cache_delete($entity);
		self::_cache_save($entity);
		self::_cache_delete_list_all();
	}
	
	
	public static function delete_by_id_role_id_permission($id_role, $id_permission) {

		$conditions = array();
		$conditions['id_role'] = $id_role;
		$conditions['id_permission'] = $id_permission;
		return ZfRolesPermissions::delete_rows($conditions);

	}
	
	
	public static function delete_by_id_role($id_role) {

		$conditions = array();
		$conditions['id_role'] = $id_role;
		return ZfRolesPermissions::delete_rows($conditions);

	}
	
	
	public static function delete_by_id_permission($id_permission) {

		$conditions = array();
		$conditions['id_permission'] = $id_permission;
		return ZfRolesPermissions::delete_rows($conditions);

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

