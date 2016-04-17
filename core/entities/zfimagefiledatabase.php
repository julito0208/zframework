<?php

class ZfImageFileDatabase extends DBEntity
{

	/* ZPHP Generated Code ------------------------------------------ */

	const ENTITY_TABLE = '`zf_image_file`';

	protected static $_table_sql = self::ENTITY_TABLE;

	protected static $_entity_fields = array(
		'id_image_file',
		'id_group',
		'path',
		'pos',
		'title',
		'date_added',
		'crop_x',
		'crop_y',
		'crop_width',
		'crop_height',
		'width',
		'height',
	);

	protected static $_primary_keys = array(
		'id_image_file',
	);

	//-----------------------------------------------------------------------

	
	/**
	* @return ZfImageFile[]
	*/	
	protected static function _entity_collection_from_array($array_rows) {

		$entities = array();

		foreach((array) $array_rows as $row) $entities[] = self::_entity_from_array($row);

		return $entities;

	}

	/**
	* @return ZfImageFile
	*/
	protected static function _entity_from_array($array) {

		if($array && is_array($array))
		{
			$entity = new ZfImageFile();
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
	* @return ZfImageFile
	*/
	public static function get_by_id_image_file($id_image_file, $column=null) {

		return ZfImageFile::get_row(array('id_image_file' => $id_image_file), $column);

	}
	
	/**
	* @return ZfImageFile
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
	* @return ZfImageFile[]
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
	
	public static function list_by_id_group($id_group, $conditions=null, $order=null, $limit=null) {


		return ZfImageFile::list_all(array_merge(array('id_group' => $id_group), (array) $conditions), $order, $limit);

	}
	
	public static function count_by_id_group($id_group, $conditions=null) {


		return ZfImageFile::count_all(array_merge(array('id_group' => $id_group), (array) $conditions));

	}
	
	/**
	* @return ZfImageFile
	*/
	public static function saveEntity(ZfImageFile $entity) {

		$entity_row = $entity->to_array(false);

		$primary_keys_values = array();
		$primary_keys_values['id_image_file'] = $entity_row['id_image_file'];

		if(DBConnection::get_default_connection()->select_value('SELECT COUNT(*) FROM '.self::ENTITY_TABLE.' '.SQLHelper::prepare_conditions($primary_keys_values, true)) == 1) {

			DBConnection::get_default_connection()->query_update('zf_image_file', $entity_row, $primary_keys_values);

		} else {

			DBConnection::get_default_connection()->query_insert('zf_image_file', $entity_row, true);

			$newEntity = self::_entity_from_array(DBConnection::get_default_connection()->select_row('SELECT * FROM '.self::ENTITY_TABLE.' '.SQLHelper::prepare_conditions($primary_keys_values, true)));

			if(is_null($entity->get_date_added())) {

				$entity->set_date_added($newEntity->get_date_added());

			}
		}  

	}

	public static function __callStatic($method, $args)
	{
		if($method == 'save')
		{
			call_user_func_array(array('ZfImageFile', 'saveEntity'), $args);
		}
		else
		{
			call_user_func_array(array(get_parent_class(self), $method), $args);
		}
	}

	
	
	public static function delete_by_id_image_file($id_image_file) {

		$conditions = array();
		$conditions['id_image_file'] = $id_image_file;
		return ZfImageFile::delete_rows($conditions);

	}
	
	
	public static function delete_rows($conditions=array()) {	
	
		$conditions = (array) $conditions;	
	
		if(count($conditions) == 0) return;
		return DBConnection::get_default_connection()->query('DELETE FROM '.self::ENTITY_TABLE.' '.SQLHelper::prepare_conditions($conditions));

	}

	//-----------------------------------------------------------------------


	protected $_id_image_file;
	protected $_id_group;
	protected $_path;
	protected $_pos;
	protected $_title;
	protected $_date_added;
	protected $_crop_x;
	protected $_crop_y;
	protected $_crop_width;
	protected $_crop_height;
	protected $_width;
	protected $_height;


	public function __construct($data = null) {

		parent::__construct();

		if($data !== null) {

			if(is_array($data) || (is_object($data) && $data instanceof DBEntity)) {

				$this->update_fields($data);

			} else if(func_num_args() == count(self::$_primary_keys)) { 

				$args = func_get_args();
				$conditions = array_combine(self::$_primary_keys, $args);
				$entity = ZfImageFile::get_row($conditions);
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
			ZfImageFile::saveEntity($this);
		}
		else
		{
			throw new Exception('Method '.get_class(self).'::'.$method.' does not exists');
		}
	}

	//-----------------------------------------------------------------------



	public function get_id_image_file() {
		return $this->_id_image_file;
	}


	/**
	* @return ZfImageFile
	*/
	public function set_id_image_file($value) {
		$this->_id_image_file = $value;
		return $this;
	}


	public function get_id_group() {
		return $this->_id_group;
	}


	/**
	* @return ZfImageFile
	*/
	public function set_id_group($value) {
		$this->_id_group = $value;
		return $this;
	}


	public function get_path() {
		return $this->_path;
	}


	/**
	* @return ZfImageFile
	*/
	public function set_path($value) {
		$this->_path = $value;
		return $this;
	}


	public function get_pos() {
		return $this->_pos;
	}


	/**
	* @return ZfImageFile
	*/
	public function set_pos($value) {
		$this->_pos = $value;
		return $this;
	}


	public function get_title() {
		return $this->_title;
	}


	/**
	* @return ZfImageFile
	*/
	public function set_title($value) {
		$this->_title = $value;
		return $this;
	}


	/**
	* @return Date
	*/
	public function get_date_added() {
		return $this->_date_added;
	}


	/**
	* @return ZfImageFile
	*/
	public function set_date_added($value) {
		$this->_date_added = is_null($value) ? null : Date::parse($value);
		return $this;
	}


	public function get_crop_x() {
		return $this->_crop_x;
	}


	/**
	* @return ZfImageFile
	*/
	public function set_crop_x($value) {
		$this->_crop_x = $value;
		return $this;
	}


	public function get_crop_y() {
		return $this->_crop_y;
	}


	/**
	* @return ZfImageFile
	*/
	public function set_crop_y($value) {
		$this->_crop_y = $value;
		return $this;
	}


	public function get_crop_width() {
		return $this->_crop_width;
	}


	/**
	* @return ZfImageFile
	*/
	public function set_crop_width($value) {
		$this->_crop_width = $value;
		return $this;
	}


	public function get_crop_height() {
		return $this->_crop_height;
	}


	/**
	* @return ZfImageFile
	*/
	public function set_crop_height($value) {
		$this->_crop_height = $value;
		return $this;
	}


	public function get_width() {
		return $this->_width;
	}


	/**
	* @return ZfImageFile
	*/
	public function set_width($value) {
		$this->_width = $value;
		return $this;
	}


	public function get_height() {
		return $this->_height;
	}


	/**
	* @return ZfImageFile
	*/
	public function set_height($value) {
		$this->_height = $value;
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

			return ZfImageFile::delete_rows($conditions);
		}
		else
		{
			return false;
		}
	}


	/* /ZPHP Generated Code ------------------------------------------ */

}

