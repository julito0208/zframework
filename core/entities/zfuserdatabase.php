<?php

class ZfUserDatabase extends DBEntity
{

	/* ZPHP Generated Code ------------------------------------------ */

	const ENTITY_TABLE = '`zf_user`';

	protected static $_table_sql = self::ENTITY_TABLE;

	protected static $_entity_fields = array(
		'id_user',
		'username',
		'password',
		'date_added',
		'last_login',
		'is_active',
		'token_restore_pass',
		'token_activation',
	);

	protected static $_primary_keys = array(
		'id_user',
	);

	//-----------------------------------------------------------------------

	
	/**
	* @return ZfUser[]
	*/	
	protected static function _entity_collection_from_array($array_rows) {

		$entities = array();

		foreach((array) $array_rows as $row) $entities[] = self::_entity_from_array($row);

		return $entities;

	}

	/**
	* @return ZfUser
	*/
	protected static function _entity_from_array($array) {

		if($array && is_array($array))
		{
			$entity = new ZfUser();
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
	* @return ZfUser
	*/
	public static function get_by_id_user($id_user, $column=null) {

		return ZfUser::get_row(array('id_user' => $id_user), $column);

	}
	
	/**
	* @return ZfUser
	*/
	public static function get_by_username($username, $column=null) {

		return ZfUser::get_row(array('username' => $username), $column);

	}
	
	/**
	* @return ZfUser
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
	* @return ZfUser[]
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
	* @return ZfUser
	*/
	public static function saveEntity(ZfUser $entity) {

		$entity_row = $entity->to_array(false);

		$primary_keys_values = array();
		$primary_keys_values['id_user'] = $entity_row['id_user'];

		if(DBConnection::get_default_connection()->select_value('SELECT COUNT(*) FROM '.self::ENTITY_TABLE.' '.SQLHelper::prepare_conditions($primary_keys_values, true)) == 1) {

			DBConnection::get_default_connection()->query_update('zf_user', $entity_row, $primary_keys_values);

		} else {

			DBConnection::get_default_connection()->query_insert('zf_user', $entity_row, true);
			$primary_keys_values['id_user'] = DBConnection::get_default_connection()->get_insert_id();
			$entity->id_user = $primary_keys_values['id_user'];

			$newEntity = self::_entity_from_array(DBConnection::get_default_connection()->select_row('SELECT * FROM '.self::ENTITY_TABLE.' '.SQLHelper::prepare_conditions($primary_keys_values, true)));

			if(is_null($entity->get_date_added())) {

				$entity->set_date_added($newEntity->get_date_added());

			}

			if(is_null($entity->get_is_active())) {

				$entity->set_is_active($newEntity->get_is_active());

			}
		}  

	}

	public static function __callStatic($method, $args)
	{
		if($method == 'save')
		{
			call_user_func_array(array('ZfUser', 'saveEntity'), $args);
		}
		else
		{
			call_user_func_array(array(get_parent_class(self), $method), $args);
		}
	}


	public static function update_rows($values, $conditions=null) {

		return DBConnection::get_default_connection()->query_update("zf_user", $values, $conditions);
	}
	
	
	public static function delete_by_id_user($id_user) {

		$conditions = array();
		$conditions['id_user'] = $id_user;
		return ZfUser::delete_rows($conditions);

	}
	
	
	public static function delete_by_username($username) {

		$conditions = array();
		$conditions['username'] = $username;
		return ZfUser::delete_rows($conditions);

	}
	
	
	public static function delete_rows($conditions=array()) {	
	
		$conditions = (array) $conditions;	
	
		if(count($conditions) == 0) return;
		return DBConnection::get_default_connection()->query('DELETE FROM '.self::ENTITY_TABLE.' '.SQLHelper::prepare_conditions($conditions));

	}

	//-----------------------------------------------------------------------


	protected $_id_user;
	protected $_username;
	protected $_password;
	protected $_date_added;
	protected $_last_login;
	protected $_is_active;
	protected $_token_restore_pass;
	protected $_token_activation;


	public function __construct($data = null) {

		parent::__construct();

		if($data !== null) {

			if(is_array($data) || (is_object($data) && $data instanceof DBEntity)) {

				$this->update_fields($data);

			} else if(func_num_args() == count(self::$_primary_keys)) { 

				$args = func_get_args();
				$conditions = array_combine(self::$_primary_keys, $args);
				$entity = ZfUser::get_row($conditions);
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
			ZfUser::saveEntity($this);
		}
		else
		{
			throw new Exception('Method '.get_class(self).'::'.$method.' does not exists');
		}
	}

	//-----------------------------------------------------------------------



	public function get_id_user() {
		return $this->_id_user;
	}


	/**
	* @return ZfUser
	*/
	public function set_id_user($value) {
		$this->_id_user = $value;
		return $this;
	}


	public function get_username() {
		return $this->_username;
	}


	/**
	* @return ZfUser
	*/
	public function set_username($value) {
		$this->_username = $value;
		return $this;
	}


	public function get_password() {
		return $this->_password;
	}


	/**
	* @return ZfUser
	*/
	public function set_password($value) {
		$this->_password = $value;
		return $this;
	}


	/**
	* @return Date
	*/
	public function get_date_added() {
		return $this->_date_added;
	}


	/**
	* @return ZfUser
	*/
	public function set_date_added($value) {
		$this->_date_added = is_null($value) ? null : Date::parse($value);
		return $this;
	}


	/**
	* @return Date
	*/
	public function get_last_login() {
		return $this->_last_login;
	}


	/**
	* @return ZfUser
	*/
	public function set_last_login($value) {
		$this->_last_login = is_null($value) ? null : Date::parse($value);
		return $this;
	}


	public function get_is_active() {
		return $this->_is_active;
	}


	/**
	* @return ZfUser
	*/
	public function set_is_active($value) {
		$this->_is_active = $value;
		return $this;
	}


	public function get_token_restore_pass() {
		return $this->_token_restore_pass;
	}


	/**
	* @return ZfUser
	*/
	public function set_token_restore_pass($value) {
		$this->_token_restore_pass = $value;
		return $this;
	}


	public function get_token_activation() {
		return $this->_token_activation;
	}


	/**
	* @return ZfUser
	*/
	public function set_token_activation($value) {
		$this->_token_activation = $value;
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

			return ZfUser::delete_rows($conditions);
		}
		else
		{
			return false;
		}
	}


	/* /ZPHP Generated Code ------------------------------------------ */

}

