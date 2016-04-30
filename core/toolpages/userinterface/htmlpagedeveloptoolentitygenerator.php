<?php

class HTMLPageDevelopToolEntityGenerator extends HTMLPageDevelopTool  {

	const URL_SCRIPT_PATTERN = '/entitygenerator(?:\.php)?';
	
	const METHOD_DELETE = 'delete';
	const METHOD_GENERATE = 'generate';

	protected static $_tables_classnames = array();

	public static function table_classname($table)
	{
		if(array_key_exists('classnames', $_POST) && is_array($_POST['classnames']) && array_key_exists($table, $_POST['classnames']))
		{
			return $_POST['classnames'][$table];
		}
		else if(array_key_exists($table, self::$_tables_classnames))
		{
			return self::$_tables_classnames[$table];
		}
		else
		{
			return self::table_to_class($table);
		}

	}

	public static function table_to_class($table) {
		
		$classname = '';
		
		foreach(explode('_', $table) as $part) {
			
			$classname.= strtoupper(substr($part, 0, 1)) . strtolower(substr($part, 1));
					
		}
		
		return $classname;
	}
	
	public static function table_to_entity($table) {
		return self::table_to_class($table);
	}
	
	/*-----------------------------------------*/

	protected static $_protected_tables = array(
		'images_groups',
		'images_types',
		'images_crop_types',
		'images_thumbs_types',
		'images_files',
		'images_files_thumbs',
		'languages_codes',
		'languages_regions',
		'languages',
		'languages_sections',
		'languages_texts',
		'location_country',
		'location_state',
		'location_city',
		'location_zone',
		'permissions',
		'roles',
		'roles_permissions',
		'users',
		'users_roles',
	);

	protected static function _get_title()
	{
		return 'Entity Generator';
	}
	
	protected static function _get_show_index()
	{
		return true;
	}
	
	
	//-------------------------------------------------------------------
	
	protected $_tables;
	protected $_tables_columns;
	protected $_selected_tables;
	protected $_generated_code_delimiter_start;
	protected $_generated_code_delimiter_end;
	protected $_method;
	protected $_entities_dir;

	protected $_generate_data = array(
		'generate_entities' => true,
		'generate_entities_database' => true,
		'generate_services' => true,
		'generate_services_cache' => true,
		'generate_services_database' => true,
		'generate_services_get' => true,
		'generate_services_list' => true,
		'generate_services_save' => true,
		'generate_services_delete' => true,
		'overwrite' => null,
	);
	
	protected $_submit;
	
	public function __construct() {
		parent::__construct();

		$this->_tables_columns = 2;
		$this->_entities_dir = ZPHP::get_config('db_entities_dir');
		$this->_submit = $_POST['submit'];
		self::$_tables_classnames = DBEntity::get_tables_classnames();

		$this->_tables = array();
		
		$tables = DBConnection::get_default_connection()->select_rows("SHOW TABLES");
		
		foreach($tables as $table)
		{
			if(!in_array($table, self::$_protected_tables) && strpos($table, 'zf') !== 0)
			{
				$this->_tables[] = $table;
			}
		}
		
		if($this->_submit) {
			
			$this->_selected_tables = (array) $_POST['tables'];
			$this->_method = $_POST['method'];

			$this->_generated_code_delimiter_start = "/* ZPHP Generated Code ------------------------------------------ */";
			$this->_generated_code_delimiter_end = "/* /ZPHP Generated Code ------------------------------------------ */";
			
			$this->set_param('submit', 1);
			
			foreach($this->_generate_data as $key => $value) {
				if(array_key_exists($key, $_POST)) {
					$this->_generate_data[$key] = (bool) $_POST[$key];
				}
			}
			
			if($this->_method == self::METHOD_DELETE) {
				$this->delete_tables($this->_selected_tables);
			} else if($this->_method == self::METHOD_GENERATE) {
				$this->generate_entities($this->_selected_tables);
			}
			
		} else {
			
			$this->_method = $_GET['method'];
			
			$this->_selected_tables = (array) $_GET['tables'];
			
			foreach($this->_generate_data as $key => $value) {
				if(array_key_exists($key, $_GET)) {
					$this->_generate_data[$key] = (bool) $_GET[$key];
				}
			}
			
			$this->set_param('submit', 0);
		}

	}

	public function get_service_path($table, $sufix='') {
		$classname = self::table_classname($table);
		return $this->_entities_dir.'/'.strtolower(str_replace('_', '', $classname)).($sufix ? strtolower($sufix) : '').'.php';
	}
	
	public function delete_tables(array $tables) {

		foreach((array) $tables as $table) {
			
			$path = $this->get_service_path($table);
			@ unlink($path);
			
			$path = $this->get_service_path($table, 'cache');
			@ unlink($path);
			
			$path = $this->get_service_path($table, 'database');
			@ unlink($path);

			DBConnection::get_default_connection()->query("DROP TABLE `{$table}`");

		}
		
	}
	
	protected function preparePHPCode($code) {
		
		$code = StringHelper::put_prefix($code, "<?php //----------------------------------------------------------- \n\n");
		$code = StringHelper::put_sufix($code, "\n\n");
		$code = str_replace('\n', "\n", $code);
		$code = str_replace('\t', "\t", $code);
		return $code;
	}
	
	protected function file_unlink($path)
	{
		@ unlink($path);
	}
	
	protected function get_service_custom_code($path, $unlink=true)
	{
		$code = '';
		
		if(file_exists($path))
		{
			$contents = file_get_contents($path);
			$code = $contents;
			
			if($unlink)
			{
				$this->file_unlink($path);
			}
		}
		
		return trim($code);
	}
	
	protected function get_custom_code_paths($path)
	{
		$path_basename = basename($path);
		$path_dirname = dirname($path);
		$path_dirname_dirname = dirname($path_dirname);
		
		$test_custom_code_paths = array();
		
		if(file_exists($path))
		{
			$test_custom_code_paths[] = $path;
		}
		else
		{
			$test_custom_code_paths[] = $path_dirname_dirname.'/services/'.$path_basename;
			$test_custom_code_paths[] = $path_dirname_dirname.'/business/'.$path_basename;
			$test_custom_code_paths[] = $path_dirname_dirname.'/services/service'.$path_basename;
			$test_custom_code_paths[] = $path_dirname_dirname.'/business/service'.$path_basename;
		}
		
		return $test_custom_code_paths;
	}
	
	protected function save_service_file($path, $classname, $extends_classname, $inner_code)
	{
		$test_custom_code_paths = $this->get_custom_code_paths($path);
		$interfaces = array();
		$custom_code = '';
		
		foreach($test_custom_code_paths as $custom_code_path)
		{
			$path_custom_code = $this->get_service_custom_code($custom_code_path, true);
			
			if($path_custom_code)
			{
				$path_custom_code = preg_replace('#(?i)(?s)(?m)'.preg_quote($this->_generated_code_delimiter_start).'.+?'.preg_quote($this->_generated_code_delimiter_end).'#', '', $path_custom_code);
				
				if(preg_match('#(?i)(?s)(?m)class\s+.+?(?:\s+extends.+?)\s+implements(?P<interfaces>.+?)\{#', $path_custom_code, $class_match))
				{
					$interfaces[] = trim($class_match['interfaces']);
				}
				
				if(preg_match('#(?i)(?s)(?m)class\s+(?P<classname>\S+).*?\{.*?(?P<custom_code>\S.*\S)\s*\}#', $path_custom_code, $custom_code_match))
				{
					if(!($custom_code_match['classname'] == $classname && StringHelper::ends_with($classname, 'Database')))
					{
						$custom_code.= "\t".$custom_code_match['custom_code']."\n\n";
					}
				}
			}
		}
		
		$interfaces = implode(',', $interfaces);
		$interfaces = str_replace(' ', '', $interfaces);
		$interfaces = explode(',', $interfaces);
		$interfaces = array_filter(array_unique($interfaces));

		$code = "<?php\n\nclass {$classname} extends {$extends_classname}".(empty($interfaces) ? '' : ' implements '.implode(', ', $interfaces))."\n{\n\n";
		
		if($custom_code)
		{
			$code.= $custom_code."\n";
		}

		if($inner_code != '')
		{
			$code.= "\t".$this->_generated_code_delimiter_start."\n\n".$inner_code;
			$code.= "\n\t".$this->_generated_code_delimiter_end."\n";
			$code.= "\n}\n\n";
		}
		else
		{
			$code.= "\t".$this->_generated_code_delimiter_start."\n\t".$this->_generated_code_delimiter_end;
			$code.= "\n\n}\n\n";
		}


		
		FilesHelper::file_write($path, $code);
		chmod($path, 0777);
	}
	
	public function generate_entities(array $tables) {
		
		@ ini_set('max_execution_time', 0);
	
		foreach((array) $tables as $table) {
			
			$fields = array();
			$fields_primary_key = array();
			$fields_unique = array();
			$fields_delete = array();
			$fields_list = array();
			$fields_datetime = array();
			$fields_time = array();
			$fields_default = array();

			$field_autoincrement = null;

			$classname = self::table_classname($table);

			foreach(DBConnection::get_default_connection()->select_rows("DESCRIBE {$table}") as $key => $field) {

				$field['attrname'] = $field['Field'];
				$field['name'] = $field['Field'];
				$field['get_method'] = 'get_'.$field['Field'];
				$field['set_method'] = 'set_'.$field['Field'];

				$fields[$field['Field']] = $field;

				if(strpos(strtolower($field['Extra']), 'increment') !== false) {

					$field_autoincrement = $field['Field'];
				}

				if($field['Default']) {
					$fields_default[] = $field['Field'];
				}

				if(strpos(strtolower($field['Key']), 'pri') !== false) {

					$fields_primary_key[] = $field['Field'];
					$fields_delete[] = $field['Field'];

				} else if(strpos(strtolower($field['Key']), 'mul') !== false) {	

					$fields_list[] = $field['Field'];

				} else if(strpos(strtolower($field['Key']), 'uni') !== false) {

					$fields_unique[] = $field['Field'];
					$fields_delete[] = $field['Field'];

				} else if((strpos(strtolower($field['Type']), 'varchar') !== false || strpos(strtolower($field['Type']), 'int') !== false) && false){

					$fields_delete[] = $field['Field'];

				} else if(preg_match('#^(?i).*timestamp.*$#', $field['Type']) || preg_match('#^(?i)date.*$#', $field['Type'])) {

					$fields_datetime[] = $field['Field'];

				} else if(preg_match('#^(?i).*time.*$#', $field['Type'])) {

					$fields_time[] = $field['Field'];

				}


			}

			$entity_classname = $classname;
			$entity_db_classname = $entity_classname.'Database';
			$service_classname = $classname;
			$service_db_classname = $service_classname.'Database';
			$service_cache_classname = $service_classname.'Cache';

			$path = $this->get_service_path($table);
			
			/* Service ------------------------------------------------------------------------------------------------------------------------*/
			
			if($this->_generate_data['generate_services']) {
				
				if(!file_exists($path) || $this->_generate_data['overwrite'])
				{
										
					$this->save_service_file($path, $classname, $classname.'Cache', '');
				}
				
			}

			/* Service CACHE ------------------------------------------------------------------------------------------------------------------------*/

			if($this->_generate_data['generate_services_cache']) {

				$path = $this->get_service_path($table, 'cache');

				$get_fields_array = array();

				if(count($fields_primary_key) > 0) $get_fields_array[] = implode(',',$fields_primary_key);

				foreach($fields_unique as $field) 
					$get_fields_array[] = $field;

				$get_fields_array = array_unique($get_fields_array);

				foreach($get_fields_array as $index => $get_field_array) {
					$get_fields_array[$index] = explode(',', $get_field_array);
				}


				if($this->_generate_data['overwrite'] || !file_exists($path)) {
//				if(!file_exists($path)) {

					$code = "\n";

					$code.= "\tprivate static \$_CACHE_MANAGER_KEY_GET_PREFIX = \"cache_".strtolower($classname).'entity_get_by_";';
					$code.= "\n\n";

					foreach($get_fields_array as $get_field_array) {
						$code.="\tprivate static function _generate_cache_key_get_by_".implode('_', $get_field_array)."(\$".  implode(', $', $get_field_array).") {\n";
						$code.="\t\t\$values = func_get_args();\n";
						$code.= "\t\treturn self::\$_CACHE_MANAGER_KEY_GET_PREFIX.'".implode('_', $get_field_array)."_'.implode('_', \$values);";
						$code.= "\n\t}\n\n";
					}

					$code.= "	private static function _generate_cache_key_list_all() {
		return self::\$_CACHE_MANAGER_KEY_GET_PREFIX.'_list_all';
	}
	
";


					$code.="\tprivate static function _cache_save(\$entity) {\n";
					$code.= "\t\tif(\$entity) {\n";

					foreach($get_fields_array as $get_field_array) {

						$arguments = array();
						foreach($get_field_array as $key) {
							$arguments[] = "\$entity->get_{$key}()";
						}

						$code.= "\t\t\tCacheManager::save(self::_generate_cache_key_get_by_".implode('_', $get_field_array)."(".implode(', ', $arguments)."), \$entity);\n";

					}

					$code.= "\t\t}\n";

					$code.= "
		if(isset(self::\$_CACHE_SAVE_ENTITY_CALLBACKS))
		{
			foreach((array) self::\$_CACHE_SAVE_ENTITY_CALLBACKS as \$save_callback)
			{
				call_user_func(array(self, \$save_callback), \$entity);
			}
		}
";

					$code.= "\t}\n\n";

					$code.="\tprivate static function _cache_delete(\$entity) {\n";
					$code.= "\t\tif(\$entity) {\n";

					foreach($get_fields_array as $get_field_array) {

						$arguments = array();
						foreach($get_field_array as $key) {
							$arguments[] = "\$entity->get_{$key}()";
						}


						$code.= "\t\t\tCacheManager::delete(self::_generate_cache_key_get_by_".implode('_', $get_field_array)."(".implode(', ', $arguments)."));\n";

					}


					$code.= "\t\t}\n";

					$code.= "
		if(isset(self::\$_CACHE_DELETE_ENTITY_CALLBACKS))
		{
			foreach((array) self::\$_CACHE_DELETE_ENTITY_CALLBACKS as \$delete_callback)
			{
				call_user_func(array(self, \$delete_callback), \$entity);
			}
		}
";

					$code.= "\t}\n\n";

					$code.= "	private static function _cache_delete_list_all()
	{
		CacheManager::delete(self::_generate_cache_key_list_all());
	}
	
	/*------------------------------------------------------------------------*/ 
	
	";

					if($this->_generate_data['generate_services_get']) {

						foreach($get_fields_array as $get_fields) {

							$get_fields_key = implode(',', $get_fields);

							$conditions_array = array();

							foreach($get_fields as $field) $conditions_array[]= "'{$field}' => \${$field}";

							$code .= "\t\n\t/**\n\t* @return " . $classname . "\n\t*/\n\tpublic static function get_by_".implode('_', $get_fields)."(\$".  implode(', $', $get_fields).", \$column=null) {\n";

							$code .= "\t\t\$entity = CacheManager::get(self::_generate_cache_key_get_by_".implode('_', $get_fields)."(\$".  implode(', $', $get_fields)."));";
							$code .= "\n\n\t\tif(!\$entity) {";
							$code .= "\n\t\t\t\$entity = parent::get_by_".implode('_', $get_fields)."(\$".  implode(', $', $get_fields).");";
							$code .= "\n\t\t\tif(\$entity) self::_cache_save(\$entity);";
							$code .= "\n\t\t}\n\n";
							$code .= "\t\treturn (\$entity && \$column) ? \$entity->\$column : \$entity;";
							$code .= "\n\t}\n";

						}
						
						$code.= "
						
						
	/**
	*
	* @return {$classname}[]
	*
	*/
	public static function list_all(\$conditions=null, \$order=null, \$limit=null)
	{
		if(is_null(\$conditions) && is_null(\$order) && is_null(\$limit))
		{
			\$cache_key = self::_generate_cache_key_list_all();

			\$rows = CacheManager::get(\$cache_key);

			if(is_null(\$rows))
			{
				\$rows = parent::list_all(\$conditions, \$order, \$limit);
				CacheManager::save(\$cache_key, \$rows);
			}

			return \$rows;
		}
		else
		{
			return parent::list_all(\$conditions, \$order, \$limit);
		}
	}
	
	";
					}


					if($this->_generate_data['generate_services_save']) {

						$code .= "\n\n\tpublic static function saveEntity({$entity_classname} \$entity) {\n";
						$code .= "\t\tparent::saveEntity(\$entity);";
						$code .= "\n\t\tself::_cache_delete(\$entity);";
						$code .= "\n\t\tself::_cache_save(\$entity);";
						$code .= "\n\t\tself::_cache_delete_list_all();";
						$code .= "\n\t}\n";

					}


					if($this->_generate_data['generate_services_delete']) {

						$delete_fields_array = array();
						$processed_fields = array();

						if(count($fields_primary_key) > 0) 
							$delete_fields_array[] = $fields_primary_key;

						foreach($fields_delete as $field) 
							$delete_fields_array[] = array($field);

						foreach($delete_fields_array as $delete_fields) {

							$delete_fields_key = implode(',', $delete_fields);

							if(!in_array($delete_fields_key, $processed_fields)) {

								$processed_fields[] = $delete_fields_key;

								$array_conditions = array();

								foreach($delete_fields as $field) $array_conditions[]= var_export($field, true). " => \" . SQLHelper::quote(\${$field})";

								$code .= "\t\n\t\n\tpublic static function delete_by_".implode('_', $delete_fields).'($'.  implode(', $', $delete_fields).") {\n";
								$code .= "\n\t\t\$conditions = array();";
								
								foreach($delete_fields as $field)
								{
									$code .= "\n\t\t\$conditions[".var_export($field, true)."] = \${$field};";
								}
								
								$code .= "\n\t\treturn {$classname}::delete_rows(\$conditions);\n";
								$code .= "\n\t}\n";

							}

						}


						$code .= "\t\n\t\n\tpublic static function delete_rows(\$conditions=array()) {\n";
						$code .= "\t\t\$rows = self::list_all(\$conditions);\n";
						$code .= "\t\tforeach(\$rows as \$row){\n";
						$code .= "\t\t\tself::_cache_delete(\$row);";
						$code .= "\n\t\t}";
						$code .= "\n\t\tself::_cache_delete_list_all();";
						$code .= "\n\t\tparent::delete_rows(\$conditions);";
						$code .= "\n\t}\n";


						$code .= "\t\n\t\n\tpublic static function update_rows(\$values, \$conditions=array()) {
	
		\$return = parent::update_rows(\$values, \$conditions);

		\$rows = parent::list_all(\$conditions);

		foreach(\$rows as \$row)
		{
			self::_cache_save(\$row);
		}

		self::_cache_delete_list_all();

		return \$return; 
		
	}
	
	";

					}
					$this->save_service_file($path, $classname.'Cache', $classname.'Database', $code);

				}

			}
			
			/* Service DATABASE ------------------------------------------------------------------------------------------------------------------------*/

			if($this->_generate_data['generate_services_database']) {

				$path = $this->get_service_path($table, 'database');
				
				$code = '';
				$code.= "\tconst ENTITY_TABLE = '`" . $table . "`';\n\n";

//				$get_row_table_code = null;
//				$get_list_table_code = null;

				$table_sql = null;
				
				$custom_code_paths = $this->get_custom_code_paths($path);
				
				foreach($custom_code_paths as $custom_code_path)
				{
					if(file_exists($custom_code_path))
					{
						$custom_code = file_get_contents($custom_code_path);
						
						if(preg_match('#(?i)(?:(?:protected\s+static)|(?:static\s+protected))\s+\$(_get_row_table|_get_list_table|_table_sql)\s*\=\s*(?P<var>.+?)\;#', $custom_code, $table_sql_match))
						{
							$table_sql = $table_sql_match['var'];
						}

					}
					
					if($table_sql)
					{
						break;
					}
				}
				
				if(is_null($table_sql))
				{
					$table_sql = "self::ENTITY_TABLE";
				}
				
				if($this->_generate_data['overwrite'] || !file_exists($path)) {

//					$code.= "\n\n\t".$this->_generated_code_delimiter_start."\n\n";
//					$code .= "\tprotected static \$_get_row_table = ".$get_row_table_code.";\n";
//					$code .= "\tprotected static \$_get_list_table = ".$get_list_table_code.";\n";

					$code .= "\tprotected static \$_table_sql = ".$table_sql.";\n";

					$code .= "\n\tprotected static \$_entity_fields = array(\n";
					foreach($fields as $field)  $code .= "\t\t'" . $field['name'] . "',\n";
					$code .= "\t);\n\n";

					$code .= "\tprotected static \$_primary_keys = array(\n";
					foreach($fields_primary_key as $field)  $code .= "\t\t'" . $field . "',\n";
					$code .= "\t);\n";

					$code .= "\n\t//-----------------------------------------------------------------------\n\n";
					
					#$code .= "\n\t//-----------------------------------------------------------------------\n\n\n";

					$code .= "\t\n\t/**\n\t* @return " . $classname . "[]\n\t*/\t\n";
					$code .= "\tprotected static function _entity_collection_from_array(\$array_rows) {\n\n";
					$code .= "\t\t\$entities = array();\n\n\t\tforeach((array) \$array_rows as \$row) \$entities[] = self::_entity_from_array(\$row);\n\n";
					$code .= "\t\treturn \$entities;\n\n\t}\n\n";

					$code .= "	/**
	* @return {$classname}
	*/
	protected static function _entity_from_array(\$array) {

		if(\$array && is_array(\$array))
		{
			\$entity = new {$classname}();
			\$entity->__fromDatabase = true;
			\$entity->update_fields(\$array);
			\$entity->__fromDatabase = false;
			return \$entity;
		}
		else
		{
			return null;
		}
	}
	";
//					$code .= "\t\n\t/**\n\t* @return " . $entity_classname . "\n\t*/\n\tprotected static function _entity_from_array(\$array) {\n\n";
//
//					$code .= "\t\treturn (\$array && is_array(\$array)) ? new ".$entity_classname ."(\$array) : null;\n\n\t}\n";

					$code .= "\n\t//-----------------------------------------------------------------------\n\n";


					if($this->_generate_data['generate_services_get']) {

						$get_fields_array = array();
						$processed_fields = array();

						if(count($fields_primary_key) > 0) $get_fields_array[] = $fields_primary_key;

						foreach($fields_unique as $field) 
							$get_fields_array[] = array($field);

						foreach($get_fields_array as $get_fields) {

							$get_fields_key = implode(',', $get_fields);

							if(!in_array($get_fields_key, $processed_fields)) {

								$processed_fields[] = $get_fields_key;

								$conditions_array = array();

								foreach($get_fields as $field) $conditions_array[]= "'{$field}' => \${$field}";

								$code .= "\t\n\t/**\n\t* @return " . $classname . "\n\t*/\n\tpublic static function get_by_".implode('_', $get_fields)."(\$".  implode(', $', $get_fields).", \$column=null) {\n";
								$code .= "\n\t\treturn {$classname}::get_row(array(".implode(', ', $conditions_array)."), \$column);\n";
								$code .= "\n\t}\n";


							}

						}


						$code .= "\t\n\t/**\n\t* @return " . $classname . "\n\t*/\n\tpublic static function get_row(\$conditions, \$column=null) {\n\n";
						$code .= "\n\t\t\$row = DBConnection::get_default_connection()->select_row('SELECT * FROM '.self::\$_table_sql, \$conditions);\n";
						$code .= "\n\t\tif(\$row) {\n\n\t\t\t\$entity = self::_entity_from_array(\$row);\n\n\t\t\tif(\$column) return \$entity->\$column;\n\n\t\t\telse return \$entity;\n\n\t\t} else {\n\n\t\t\treturn null;\n\n\t\t}";
						$code .= "\n\n\t}\n\n";


					}

					if($this->_generate_data['generate_services_list']) {

						$code .= "\t\n\t/**\n\t* @return " . $classname . "[]\n\t*/\t\n";
						$code .= "\tpublic static function list_all(\$conditions=null, \$order=null, \$limit=null) {\n";
						$code .= "\n\t\t\$rows = DBConnection::get_default_connection()->select_rows('SELECT * FROM '.self::\$_table_sql, \$limit, \$conditions, \$order);\n";
						$code .= "\n\t\treturn self::_entity_collection_from_array(\$rows);\n";
						$code .= "\n\t}\n\n";

						$code .= "\tpublic static function list_all_column(\$column, \$conditions=null, \$order=null, \$limit=null) {

		\$rows = self::list_all(\$conditions, \$order, \$limit);
		\$column_data = array();

		foreach(\$rows as \$row)
		{
			\$column_data[] = \$row->\$column;
		}

		return \$column_data;

	}

	";


						$code .= "\t\n\tpublic static function search_all(&\$rows=null, \$conditions=null, \$order=null, \$limit=null, \$group_by=null) {\n";
						$code .= "\n\t\t\$count = DBConnection::get_default_connection()->search_rows('SELECT * FROM '.self::\$_table_sql, \$rows, \$limit, \$conditions, \$order, \$group_by);\n";
						$code .= "\n\t\t\$rows = self::_entity_collection_from_array(\$rows);\n";
						$code .= "\n\t\treturn \$count;\n";
						$code .= "\n\t}\n";


						$code .= "\t\n\tpublic static function count_all(\$conditions=null) {\n";
						$code .= "\n\t\treturn DBConnection::get_default_connection()->count_rows('SELECT * FROM '.self::\$_table_sql, \$conditions);\n";
						$code .= "\n\t}\n";


						if(count($fields_primary_key) > 1) {

							foreach($fields_primary_key as $field)
								$fields_list[] = $field;

						}

						foreach($fields_list as $field) {

							$code .= "\t\n\tpublic static function list_by_".$field."(\$".$field.", \$conditions=null, \$order=null, \$limit=null) {\n\n";
							$code .= "\n\t\treturn {$classname}::list_all(array_merge(array('".$field."' => \$".$field."), (array) \$conditions), \$order, \$limit);\n";
							$code .= "\n\t}\n";

							$code .= "\t\n\tpublic static function count_by_".$field."(\$".$field.", \$conditions=null) {\n\n";
							$code .= "\n\t\treturn {$classname}::count_all(array_merge(array('".$field."' => \$".$field."), (array) \$conditions));\n";
							$code .= "\n\t}\n";

						}

					}

					if($this->_generate_data['generate_services_save']) {


						$code .= "\t\n\t/**\n\t* @return " . $classname . "\n\t*/\n\tpublic static function saveEntity(".$entity_classname ." \$entity) {\n\n";
						$code .= "\t\t\$entity_row = \$entity->to_array(false);\n\n";


						if(count($fields_primary_key) > 0) {

							$code.= "\t\t\$primary_keys_values = array();\n";

							foreach($fields_primary_key as $field)
								$code.= "\t\t\$primary_keys_values['{$field}'] = \$entity_row['{$field}'];\n";

							$code.= "\n\t\tif(DBConnection::get_default_connection()->select_value('SELECT COUNT(*) FROM '.self::ENTITY_TABLE.' '.SQLHelper::prepare_conditions(\$primary_keys_values, true)) == 1) {\n";

							$code.= "\n\t\t\tDBConnection::get_default_connection()->query_update('{$table}', \$entity_row, \$primary_keys_values);\n\n";

							$code.= "\t\t} else {\n\n";

							$code.= "\t\t\tDBConnection::get_default_connection()->query_insert('{$table}', \$entity_row, true);\n";

							if($field_autoincrement) {

								$code.= "\t\t\t\$primary_keys_values['{$field_autoincrement}'] = DBConnection::get_default_connection()->get_insert_id();\n";
								$code.= "\t\t\t\$entity->{$field_autoincrement} = \$primary_keys_values['{$field_autoincrement}'];\n";

							}

							$code.= "\n\t\t\t\$newEntity = self::_entity_from_array(DBConnection::get_default_connection()->select_row('SELECT * FROM '.self::ENTITY_TABLE.' '.SQLHelper::prepare_conditions(\$primary_keys_values, true)));";

							foreach($fields_default as $field_default) {

								$code.= "\n\n\t\t\tif(is_null(\$entity->get_{$field_default}())) {";
								$code.= "\n\n\t\t\t\t\$entity->set_{$field_default}(\$newEntity->get_{$field_default}());";
								$code.= "\n\n\t\t\t}";
							}

							$code.= "\n\t\t}  \n";

						} else {

							$code.= "\t\tDBConnection::get_default_connection()->query_insert('{$table}', \$entity_row, true);\n";

						}

						$code .= "\n\t}\n";

						$code .= "\n\tpublic static function __callStatic(\$method, \$args)
	{
		if(\$method == 'save')
		{
			call_user_func_array(array('{$classname}', 'saveEntity'), \$args);
		}
		else
		{
			call_user_func_array(array(get_parent_class(self), \$method), \$args);
		}
	}

";

						$code.= "\n\tpublic static function update_rows(\$values, \$conditions=null) {

		return DBConnection::get_default_connection()->query_update(\"{$table}\", \$values, \$conditions);
	}\n";
					}

					if($this->_generate_data['generate_services_delete']) {

						$delete_fields_array = array();
						$processed_fields = array();

						if(count($fields_primary_key) > 0) 
							$delete_fields_array[] = $fields_primary_key;

						foreach($fields_delete as $field) 
							$delete_fields_array[] = array($field);

						foreach($delete_fields_array as $delete_fields) {

							$delete_fields_key = implode(',', $delete_fields);

							if(!in_array($delete_fields_key, $processed_fields)) {

								$processed_fields[] = $delete_fields_key;

								$array_conditions = array();

								foreach($delete_fields as $field) $array_conditions[]= var_export($field, true). " => \" . SQLHelper::quote(\${$field})";

								$code .= "\t\n\t\n\tpublic static function delete_by_".implode('_', $delete_fields)."(\$".  implode(', $', $delete_fields).") {\n";
								$code .= "\n\t\t\$conditions = array();";
								
								foreach($delete_fields as $field)
								{
									$code .= "\n\t\t\$conditions[".var_export($field, true)."] = \${$field};";
								}
								
								$code .= "\n\t\treturn {$classname}::delete_rows(\$conditions);\n";
								$code .= "\n\t}\n";

							}

						}

						$code .= "\t\n\t\n\tpublic static function delete_rows(\$conditions=array()) {";
						$code .= "\t\n\t\n\t\t\$conditions = (array) \$conditions;";
						$code .= "\t\n\t\n\t\tif(count(\$conditions) == 0) return;";
						$code .= "\n\t\treturn DBConnection::get_default_connection()->query('DELETE FROM '.self::ENTITY_TABLE.' '.SQLHelper::prepare_conditions(\$conditions));\n";
						$code .= "\n\t}\n";

					}

					$code .= "\n\t//-----------------------------------------------------------------------\n\n\n";
					
					foreach($fields as $field)  $code .= "\tprotected \$_" . $field["attrname"] . ";\n";

					$code .= "\n\n\tpublic function __construct(\$data = null) {\n\n";
					$code .= "\t\tparent::__construct();\n\n";
					$code .= "\t\tif(\$data !== null) {\n\n";
					$code .= "\t\t\tif(is_array(\$data) || (is_object(\$data) && \$data instanceof DBEntity)) {\n\n";
					$code .= "\t\t\t\t\$this->update_fields(\$data);\n\n";
					$code .= "\t\t\t} else if(func_num_args() == count(self::\$_primary_keys)) { \n\n";
					$code .= "\t\t\t\t\$args = func_get_args();\n";
					$code .= "\t\t\t\t\$conditions = array_combine(self::\$_primary_keys, \$args);\n";
					$code .= "\t\t\t\t\$entity = {$classname}::get_row(\$conditions);\n";
					$code .= "\t\t\t\t\$this->update_fields(\$entity);\n\n";
					$code .= "\t\t\t}\n\n";
					$code .= "\t\t}\n";
					$code .= "\t}\n";

					$code .= "\n\tpublic function __toString() {\n";
					$code .= "\t\treturn var_export(\$this, true);\n";
					$code .= "\t}\n\n";
					
					$code .= "\n\tprotected function _get_entity_fields() {\n";
					$code .= "\t\treturn self::\$_entity_fields;\n";
					$code .= "\t}\n\n";

					$code .= "\n\tprotected function _get_primary_keys() {\n";
					$code .= "\t\treturn self::\$_primary_keys;\n";
					$code .= "\t}\n\n";

					$code .= "\tpublic function __call(\$method, \$args)
	{
		if(\$method == 'save')
		{
			{$classname}::saveEntity(\$this);
		}
		else
		{
			throw new Exception('Method '.get_class(self).'::'.\$method.' does not exists');
		}
	}
";
					$code .= "\n\t//-----------------------------------------------------------------------\n\n\n";


					foreach($fields as $field)  {

						$code .= "\n\t".(in_array($field["name"], $fields_datetime) ? "/**\n\t* @return Date\n\t*/\n\t" : (in_array($field["name"], $fields_time) ? "/**\n\t* @return Time\n\t*/\n\t" : ""))."public function " . $field["get_method"] . "() {\n\t\treturn \$this->_" . $field["attrname"] . ";\n\t}\n\n";
						$code .= "\n\t/**\n\t* @return " . $classname . "\n\t*/\n\tpublic function " . $field["set_method"]  . "(\$value) {\n\t\t\$this->_" . $field["attrname"] . " = ".(in_array($field["name"], $fields_datetime) ? "is_null(\$value) ? null : Date::parse(\$value)" : (in_array($field["name"], $fields_time) ? "is_null(\$value) ? null : Time::parse(\$value)" : "\$value")).";\n\t\treturn \$this;\n\t}\n\n";
					}


					$code .= "\t//-----------------------------------------------------------------------

	public function delete()
	{
		\$keys = array_merge(self::\$_primary_keys, array());

		if(!empty(\$keys))
		{
			\$conditions = [];

			foreach(\$keys as \$key)
			{
				\$conditions[\$key] = \$this->\$key;
			}

			return {$classname}::delete_rows(\$conditions);
		}
		else
		{
			return false;
		}
	}

";

					$this->save_service_file($path, $classname.'Database', 'DBEntity', $code);
					
				}
			}
			
			$dirname = ZPHP::get_app_dir();
			$this->replace_service_classname($dirname, 'Service'.$classname, $classname);
		}
		
	}
	
	protected function replace_service_classname($dirname, $service_classname, $classname)
	{
		$contents = FilesHelper::dir_list($dirname, true);
		
		foreach($contents as $content)
		{
			if(is_dir($content))
			{
				$this->replace_service_classname($content, $service_classname, $classname);
			}
			else if(FilesHelper::path_get_extension($content) == '.php')
			{
				$file_contents = file_get_contents($content);
				$file_contents = str_replace($service_classname, $classname, $file_contents);
				file_put_contents($content, $file_contents);
			}
		}
				
		
	}
	
	public function prepare_params() {

		$tables_classnames = array();

		foreach($this->_tables as $table)
		{
			$tables_classnames[$table] = self::table_classname($table);
		}
		
		$this->set_param('tables', $this->_tables);
		$this->set_param('tables_columns', $this->_tables_columns);
		$this->set_param('selected_tables', $this->_selected_tables);
		$this->set_param('generated_code_delimiter_start', $this->_generated_code_delimiter_start);
		$this->set_param('generated_code_delimiter_end', $this->_generated_code_delimiter_end);
		$this->set_param('method', $this->_method);
		$this->set_param('submit', $this->_submit);
		$this->set_param('generate_data', $this->_generate_data);
		$this->set_param('tables_classnames', $tables_classnames);
		
		parent::prepare_params();
		
	}
}
