<?php

class ZfLocationStateDatabase extends DBEntity
{

	/* ZPHP Generated Code ------------------------------------------ */

	const ENTITY_TABLE = '`zf_location_state`';

	protected static $_table_sql = self::ENTITY_TABLE;

	protected static $_entity_fields = array(
		'id_state',
		'id_country',
		'abbr',
		'name',
	);

	protected static $_primary_keys = array(
		'id_state',
	);

	//-----------------------------------------------------------------------

	
	/**
	* @return ZfLocationState[]
	*/	
	protected static function _entity_collection_from_array($array_rows) {

		$entities = array();

		foreach((array) $array_rows as $row) $entities[] = self::_entity_from_array($row);

		return $entities;

	}

	/**
	* @return ZfLocationState
	*/
	protected static function _entity_from_array($array) {

		if($array && is_array($array))
		{
			$entity = new ZfLocationState();
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
	* @return ZfLocationState
	*/
	public static function get_by_id_state($id_state, $column=null) {

		return ZfLocationState::get_row(array('id_state' => $id_state), $column);

	}
	
	/**
	* @return ZfLocationState
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
	* @return ZfLocationState[]
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
	
	public static function list_by_id_country($id_country, $conditions=null, $order=null, $limit=null) {


		return ZfLocationState::list_all(array_merge(array('id_country' => $id_country), (array) $conditions), $order, $limit);

	}
	
	public static function count_by_id_country($id_country, $conditions=null) {


		return ZfLocationState::count_all(array_merge(array('id_country' => $id_country), (array) $conditions));

	}
	
	/**
	* @return ZfLocationState
	*/
	public static function saveEntity(ZfLocationState $entity) {

		$entity_row = $entity->to_array(false);

		$primary_keys_values = array();
		$primary_keys_values['id_state'] = $entity_row['id_state'];

		if(DBConnection::get_default_connection()->select_value('SELECT COUNT(*) FROM '.self::ENTITY_TABLE.' '.SQLHelper::prepare_conditions($primary_keys_values, true)) == 1) {

			DBConnection::get_default_connection()->query_update('zf_location_state', $entity_row, $primary_keys_values);

		} else {

			DBConnection::get_default_connection()->query_insert('zf_location_state', $entity_row, true);
			$primary_keys_values['id_state'] = DBConnection::get_default_connection()->get_insert_id();
			$entity->id_state = $primary_keys_values['id_state'];

			$newEntity = self::_entity_from_array(DBConnection::get_default_connection()->select_row('SELECT * FROM '.self::ENTITY_TABLE.' '.SQLHelper::prepare_conditions($primary_keys_values, true)));
		}  

	}

	public static function __callStatic($method, $args)
	{
		if($method == 'save')
		{
			call_user_func_array(array('ZfLocationState', 'saveEntity'), $args);
		}
		else
		{
			call_user_func_array(array(get_parent_class(self), $method), $args);
		}
	}

	
	
	public static function delete_by_id_state($id_state) {

		$conditions = array();
		$conditions['id_state'] = $id_state;
		return ZfLocationState::delete_rows($conditions);

	}
	
	
	public static function delete_rows($conditions=array()) {	
	
		$conditions = (array) $conditions;	
	
		if(count($conditions) == 0) return;
		return DBConnection::get_default_connection()->query('DELETE FROM '.self::ENTITY_TABLE.' '.SQLHelper::prepare_conditions($conditions));

	}

	//-----------------------------------------------------------------------


	protected $_id_state;
	protected $_id_country;
	protected $_abbr;
	protected $_name;


	public function __construct($data = null) {

		parent::__construct();

		if($data !== null) {

			if(is_array($data) || (is_object($data) && $data instanceof DBEntity)) {

				$this->update_fields($data);

			} else if(func_num_args() == count(self::$_primary_keys)) { 

				$args = func_get_args();
				$conditions = array_combine(self::$_primary_keys, $args);
				$entity = ZfLocationState::get_row($conditions);
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
			ZfLocationState::saveEntity($this);
		}
		else
		{
			throw new Exception('Method '.get_class(self).'::'.$method.' does not exists');
		}
	}

	//-----------------------------------------------------------------------



	public function get_id_state() {
		return $this->_id_state;
	}


	/**
	* @return ZfLocationState
	*/
	public function set_id_state($value) {
		$this->_id_state = $value;
		return $this;
	}


	public function get_id_country() {
		return $this->_id_country;
	}


	/**
	* @return ZfLocationState
	*/
	public function set_id_country($value) {
		$this->_id_country = $value;
		return $this;
	}


	public function get_abbr() {
		return $this->_abbr;
	}


	/**
	* @return ZfLocationState
	*/
	public function set_abbr($value) {
		$this->_abbr = $value;
		return $this;
	}


	public function get_name() {
		return $this->_name;
	}


	/**
	* @return ZfLocationState
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

			return ZfLocationState::delete_rows($conditions);
		}
		else
		{
			return false;
		}
	}


	/* /ZPHP Generated Code ------------------------------------------ */

}

