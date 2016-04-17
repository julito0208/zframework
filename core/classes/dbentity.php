<?php //----------------------------------------------------------------------


abstract class DBEntity implements Params, OptionRow {

	
	const ENTITY_CLASSNAME = '';
	const ENTITY_TABLE = '';

	//---------------------------------------------------------------------------

	public static function get_tables_classnames()
	{
		$entities_dir = ZPHP::get_config('db_entities_dir');
		$tables_classnames = [];
		$entities_files = FilesHelper::dir_list($entities_dir, true);

		foreach($entities_files as $entity_file)
		{
			$entity_file_basename = basename($entity_file);

			if(!(StringHelper::ends_with($entity_file_basename, 'cache.php', true) || StringHelper::ends_with($entity, 'database.php', true)))
			{
				$contents = file_get_contents($entity_file);

				if(preg_match('#(?i)(?s)(?m)class\s+(?P<classname>.+?)\s+#', $contents, $entity_match))
				{
					try
					{
						$classname = $entity_match['classname'];
						$classname = StringHelper::remove_sufix($classname, 'Database');
						$classname = StringHelper::remove_sufix($classname, 'Cache');

						@ include_once $entity_file;
						eval("\$table = {$classname}::ENTITY_TABLE;");

						$table = StringHelper::remove_prefix($table, '`');
						$table = StringHelper::remove_sufix($table, '`');

						$tables_classnames[$table] = $classname;
					}
					catch(Exception $ex){}
				}
			}
		}

		return $tables_classnames;
	}

	//---------------------------------------------------------------------------
	
	
	public static function entity_list_get_column(array $list, $column) {
		
		$values = array();
		
		foreach((array) $list as $entity) $values[] = $entity->$column;
		
		return $values;
	}


	public static function parse_entity($value)
	{
		if($value && is_object($value) && $value instanceof DBEntity)
		{
			return $value;
		}
		else if($value)
		{
			try {
				$args = func_get_args();
				$keys = static::$_primary_keys;
				$conditions = array();

				foreach ($keys as $key) {
					if (count($args) > 0) {
						$value = array_shift($args);
					} else {
						$value = null;
					}

					$conditions[$key] = $value;
				}

				if (!empty($conditions)) {
					return static::get_row($conditions);
				} else {
					return null;
				}
			}
			catch(Exception $ex) {}
		}
		else
		{
			return null;
		}
	}
	
	//---------------------------------------------------------------------------
	
	protected static function _entity_collection_from_array($array_rows) {}
	
	/* @return DBEntity */
	protected static function _entity_from_array($array) {}
	
	/*------------------------------------------------------------------------*/

	protected $__fromDatabase = false;
	protected $__othersFields = array();

	public function __construct() {}
	
	/*------------------------------------------------------------------------*/

	abstract protected function _get_entity_fields();

	abstract protected function _get_primary_keys();
	
	/*------------------------------------------------------------------------*/

	protected function _setAttribute($name, $value)
	{
		if(in_array($name, $this->_get_entity_fields())) {

			$method_name = "set_{$name}";

			if(method_exists($this, $method_name)) {
				return $this->$method_name($value);
			}
		}
		else if($this->__fromDatabase)
		{
			$this->__othersFields[$name] = $value;
		}
	}

	protected function _getAttribute($name)
	{
		if($name == 'primary_keys') {

			return $this->_get_primary_keys();

		} else if($name == 'fields') {

			return $this->_get_entity_fields();

		} else if(($method_name = "get_{$name}") && method_exists(get_class($this), $method_name)) {

			return $this->$method_name();

		}
		else if(array_key_exists($name, $this->__othersFields))
		{
			return $this->__othersFields[$name];
		}
	}


	/*-------------------------------------------------------------*/

	
	public function __toArray() {
		return $this->to_array();
	}


	public function __get($name) {
		return $this->_getAttribute($name);
	}


	public function __set($name, $value) {
		return $this->_setAttribute($name, $value);
	}


	//-----------------------------------------------------------------------


	public function to_array($all_fields=true) {

		if($all_fields)
		{
			$row = array_merge(array(), $this->__othersFields);
		}
		else
		{
			$row = array();
		}

		foreach($this->_get_entity_fields() as $field) {
			$row[$field] = $this->$field;
		}

		return $row;

	}
	

	public function update_fields($data) {

		if(!$data) return $this;
		else if(is_object($data) && $data instanceof DBEntity) $data = $data->to_array();
		else $data = (array) $data;

		foreach($data as $name => $value) {
			$this->_setAttribute($name, $value);
		}
		
	}

	/*-------------------------------------------------------------*/

	public function set_param($name, $value=null)
	{
		$this->_setAttribute($name, $value);
		return $this;
	}

	public function has_param($name)
	{
		if($name == 'primary_keys') {

			return true;

		} else if($name == 'fields') {

			return true;

		} else if(($method_name = "get_{$name}") && method_exists(get_class($this), $method_name)) {

			return true;

		}
		else if(array_key_exists($name, $this->__othersFields))
		{
			return true;
		}

		return false;
	}

	public function remove_param($name)
	{
		return $this->set_param($name, null);
	}

	public function get_params_array()
	{
		return $this->__toArray();
	}

	public function get_row_id()
	{
		$values = [];

		foreach($this->primary_keys as $primary_key)
		{
			$values[$primary_key] = $this->get_param($primary_key);
		}

		return implode('-', $values);
	}

	public function get_param($name, $default=null)
	{
		if($this->has_param($name))
		{
			return $this->_getAttribute($name);
		}
		else
		{
			return $default;
		}
	}
}


//--------------------------------------------------------------------------- ?>