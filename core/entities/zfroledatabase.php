<?php

class ZfRoleDatabase extends DBEntity
{

	/* ZPHP Generated Code ------------------------------------------ */

	const ENTITY_TABLE = '`zf_role`';

	protected static $_table_sql = self::ENTITY_TABLE;

	protected static $_entity_fields = array(
		'id_role',
		'is_admin',
		'name',
	);

	protected static $_primary_keys = array(
		'id_role',
	);

	//-----------------------------------------------------------------------

	
	/**
	* @return ZfRole[]
	*/	
	protected static function _entity_collection_from_array($array_rows) {

		$entities = array();

		foreach((array) $array_rows as $row) $entities[] = self::_entity_from_array($row);

		return $entities;

	}

	/**
	* @return ZfRole
	*/
	protected static function _entity_from_array($array) {

		if($array && is_array($array))
		{
			$entity = new ZfRole();
			$entity->__fromDatabase = true;
			$entity->update_fields($array);
			$entity->__fromDatabase = false;
			return $entity;
		}
		else
		{
			return null;
		}
	}
	
	//-----------------------------------------------------------------------

	
	/**
	* @return ZfRole
	*/
	public static function get_by_id_role($id_role, $column=null) {

		return ZfRole::get_row(array('id_role' => $id_role), $column);

	}
	
	/**
	* @return ZfRole
	*/
	public static function get_row($conditions, $column=null) {


		$row = DBConnection::get_default_connection()->select_row('SELECT * FROM '.self::$_table_sql, $conditions);

		if($row) {

			$entity = self::_entity_from_array($row);

			if($column) return $entity->$column;

			else return $entity;

		} else {

			return null;

		}

	}

	
	/**
	* @return ZfRole[]
	*/	
	public static function list_all($conditions=null, $order=null, $limit=null) {

		$rows = DBConnection::get_default_connection()->select_rows('SELECT * FROM '.self::$_table_sql, $limit, $conditions, $order);

		return self::_entity_collection_from_array($rows);

	}

	public static function list_all_column($column, $conditions=null, $order=null, $limit=null) {

		$rows = self::list_all($conditions, $order, $limit);
		$column_data = array();

		foreach($rows as $row)
		{
			$column_data[] = $row->$column;
		}

		return $column_data;

	}

		
	public static function search_all(&$rows=null, $conditions=null, $order=null, $limit=null, $group_by=null) {

		$count = DBConnection::get_default_connection()->search_rows('SELECT * FROM '.self::$_table_sql, $rows, $limit, $conditions, $order, $group_by);

		$rows = self::_entity_collection_from_array($rows);

		return $count;

	}
	
	public static function count_all($conditions=null) {

		return DBConnection::get_default_connection()->count_rows('SELECT * FROM '.self::$_table_sql, $conditions);

	}
	
	/**
	* @return ZfRole
	*/
	public static function saveEntity(ZfRole $entity) {

		$entity_row = $entity->to_array(false);

		$primary_keys_values = array();
		$primary_keys_values['id_role'] = $entity_row['id_role'];

		if(DBConnection::get_default_connection()->select_value('SELECT COUNT(*) FROM '.self::ENTITY_TABLE.' '.SQLHelper::prepare_conditions($primary_keys_values, true)) == 1) {

			DBConnection::get_default_connection()->query_update('zf_role', $entity_row, $primary_keys_values);

		} else {

			DBConnection::get_default_connection()->query_insert('zf_role', $entity_row, true);

			$newEntity = self::_entity_from_array(DBConnection::get_default_connection()->select_row('SELECT * FROM '.self::ENTITY_TABLE.' '.SQLHelper::prepare_conditions($primary_keys_values, true)));
		}  

	}

	public static function __callStatic($method, $args)
	{
		if($method == 'save')
		{
			call_user_func_array(array('ZfRole', 'saveEntity'), $args);
		}
		else
		{
			call_user_func_array(array(get_parent_class(self), $method), $args);
		}
	}


	public static function update_rows($values, $conditions=null) {

		return DBConnection::get_default_connection()->query_update("zf_role", $values, $conditions);
	}
	
	
	public static function delete_by_id_role($id_role) {

		$conditions = array();
		$conditions['id_role'] = $id_role;
		return ZfRole::delete_rows($conditions);

	}
	
	
	public static function delete_rows($conditions=array()) {	
	
		$conditions = (array) $conditions;	
	
		if(count($conditions) == 0) return;
		return DBConnection::get_default_connection()->query('DELETE FROM '.self::ENTITY_TABLE.' '.SQLHelper::prepare_conditions($conditions));

	}

	//-----------------------------------------------------------------------


	protected $_id_role;
	protected $_is_admin;
	protected $_name;


	public function __construct($data = null) {

		parent::__construct();

		if($data !== null) {

			if(is_array($data) || (is_object($data) && $data instanceof DBEntity)) {

				$this->update_fields($data);

			} else if(func_num_args() == count(self::$_primary_keys)) { 

				$args = func_get_args();
				$conditions = array_combine(self::$_primary_keys, $args);
				$entity = ZfRole::get_row($conditions);
				$this->update_fields($entity);

			}

		}
	}

	public function __toString() {
		return var_export($this, true);
	}


	protected function _get_entity_fields() {
		return self::$_entity_fields;
	}


	protected function _get_primary_keys() {
		return self::$_primary_keys;
	}

	public function __call($method, $args)
	{
		if($method == 'save')
		{
			ZfRole::saveEntity($this);
		}
		else
		{
			throw new Exception('Method '.get_class(self).'::'.$method.' does not exists');
		}
	}

	//-----------------------------------------------------------------------



	public function get_id_role() {
		return $this->_id_role;
	}


	/**
	* @return ZfRole
	*/
	public function set_id_role($value) {
		$this->_id_role = $value;
		return $this;
	}


	public function get_is_admin() {
		return $this->_is_admin;
	}


	/**
	* @return ZfRole
	*/
	public function set_is_admin($value) {
		$this->_is_admin = $value;
		return $this;
	}


	public function get_name() {
		return $this->_name;
	}


	/**
	* @return ZfRole
	*/
	public function set_name($value) {
		$this->_name = $value;
		return $this;
	}

	//-----------------------------------------------------------------------

	public function delete()
	{
		$keys = array_merge(self::$_primary_keys, array());

		if(!empty($keys))
		{
			$conditions = [];

			foreach($keys as $key)
			{
				$conditions[$key] = $this->$key;
			}

			return ZfRole::delete_rows($conditions);
		}
		else
		{
			return false;
		}
	}


	/* /ZPHP Generated Code ------------------------------------------ */

}

