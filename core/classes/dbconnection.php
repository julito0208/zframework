<?php //-----------------------------------------------------------------------------------------------------------------------


//abstract class DBConnection extends SQLHelper {
abstract class DBConnection {
	
	const ENGINE_MYSQL = 'mysql';
	const ENGINE_SQLITE = 'sqlite';
	
	//-----------------------------------------------------------------------------------------------------------------------
	
	private static $_DEFAULT_CONNECTION = null;
	
	/* @return DBConnection */
	public static function get_default_connection() {
		
		if(is_null(self::$_DEFAULT_CONNECTION)) {
			
			self::$_DEFAULT_CONNECTION = false;
			
			switch(strtolower(ZPHP::get_config('db_connection_default_engine'))) {
				
				case self::ENGINE_MYSQL:
					
					$server = ZPHP::get_config('db_connection_mysql_server');
					$user = ZPHP::get_config('db_connection_mysql_user');
					$pass = ZPHP::get_config('db_connection_mysql_pass');
					$dbname = ZPHP::get_config('db_connection_mysql_dbname');
					$port = ZPHP::get_config('db_connection_mysql_port');

					$mysql = new MySQL($server, $user, $pass, $dbname, $port);
					
					$charset = ZPHP::get_config('db_connection_mysql_charset');
					
					if($charset) {
						$mysql->set_charset($charset);
					}
					
					self::$_DEFAULT_CONNECTION = $mysql;
					
				break;
			}
			
			if(self::$_DEFAULT_CONNECTION) {
				self::$_DEFAULT_CONNECTION->set_debug(ZPHP::get_config('db_connection_debug'));
			}
		}
		
		return self::$_DEFAULT_CONNECTION;
	}

	
	//-----------------------------------------------------------------------------------------------------------------------

	protected static $_DEFAULT_TEMPLATES_FORMATS = array('%s.sql');
	protected static $_DEFAULT_TEMPLATES_PATHS = array();
	
	
	private static function _get_template_path($template) {
		
		if(file_exists($template)) return $template;
		
		$paths = array_merge((array) ZPHP::get_config('db_sql_templates_dir'), self::$_DEFAULT_TEMPLATES_PATHS);
		$formats = array_merge((array) ZPHP::get_config('db_sql_templates_format'), self::$_DEFAULT_TEMPLATES_FORMATS);
		
		$test_paths = array();
		
		foreach($paths as $path) {
		
			foreach($formats as $format) {
				$test_paths[] = $path.'/'.sprintf($format, $template);
				$test_paths[] = $path.'/'.strtolower(sprintf($format, $template));
			}
		}
		
		foreach($test_paths as $path) {
			if(file_exists($path)) {
				return $path;
			}
		}
		
	}
	
	
	private static function _parse_template_file($template, $vars=array()) {
		
		$vars = CastHelper::to_array($vars);
		$template_path = self::_get_template_path($template);
		
		$__TEMPLATE_VARS = $vars;
		$__TEMPLATE_PATH = $template_path;
		
		unset($vars);
		unset($template_path);
		
		extract($__TEMPLATE_VARS);
		
		try {
			ob_start();
			@ include($__TEMPLATE_PATH);
			$__TEMPLATE_CONTENT = ob_get_clean();
		} catch(Exception $ex) {
			ob_get_clean();
			throw $ex;
		}
		
		return $__TEMPLATE_CONTENT;
		
	}
	
	//private static $_table_pattern = "#^(?i)(?P<sql>\((\n|\s|.|\t)+\)\s+(?:AS\s+)?)?(?P<table_name>(?:(?:`.+`)|(?:\".+\")|(?:'.+')|(?:[\w\.]+)))\$#";
	private static $_table_pattern = "#^(?i)(?P<sql>\((\n|\s|.|\t)+\)\s+(?:AS\s+)?)(?P<table_name>(?:(?:`.+`)|(?:\".+\")|(?:'.+')|(?:[\w\.]+)))\$#";
	private static $_prepare_sql_code_vars = array();
	private static $_table_template_pattern = '/(?i)(?:(?:\[\@(?P<name1>.+?)(?:\:(?P<alias1>.+?))?\])|(?:\{\@(?P<name2>.+?)(?:\:(?P<alias2>.+?))?\}))/';
	private static $_multi_query_pattern;
	private static $_template_query_pattern = '/^\s*\&(?P<template>\S+)\s*$/';
	private static $_comment_pattern = "#(?i)(?s)\\/\\*(?:.|\n)*?\\*\\/#";
	
	private static $_query_delimiter = ';';
	
	private static function _replace_tables_templates_callback($match) {
		
		$template = $match[1] ? $match[1] : $match[3];
		
		if(array_key_exists('alias1', $match)) $alias = $match['alias1'];
		else if(array_key_exists('alias2', $match)) $alias = $match['alias2'];
		else if(array_key_exists('name1', $match)) $alias = $match['name1'];
		else $alias = $match['name2'];
		
		$sql = self::_parse_template_file($template, self::$_prepare_sql_code_vars);
		
		return @ "(".self::_prepare_sql_code($sql, self::$_prepare_sql_code_vars, true).") AS `{$alias}`";
	}
	
	
	
	private static function _prepare_sql_code($sql, $vars=array(), $mantain_vars=false) {
		
		self::$_prepare_sql_code_vars = $vars;
		
		$sql = preg_replace(self::$_comment_pattern, '', $sql);
		
		if(preg_match(self::$_table_pattern, $sql)) {
			$sql = "SELECT * FROM {$sql}";
		}
		
		$new_sql = preg_replace_callback(self::$_table_template_pattern, array('self', '_replace_tables_templates_callback'), $sql);		
		
		if($mantain_vars) self::$_prepare_sql_code_vars = array();
		
		return $new_sql;
	}
	
	
	protected static function _prepare_querys($sql, $vars=array()){
		
		if(!self::$_multi_query_pattern) {
			
			$quoted_str_double = '\"(?:\\\\\\\\|\\\\\"|.|\s)*?\"';
			$quoted_str_simple = "\'(?:\\\\\\\\|\\\\\'|.|\s)*?\'";
			self::$_multi_query_pattern = "/(?s)\s*(?P<sql>(?:(?:{$quoted_str_double})|(?:{$quoted_str_simple})|.)+?)\s*(?:".preg_quote(self::$_query_delimiter)."|\$)/";

		}

		$vars = CastHelper::to_array($vars);
		
		if(($is_array = is_array($sql)) || preg_match(self::$_template_query_pattern, $sql, $template_name_match)) {
			
			if($is_array) {
				$template_name = array_shift($sql);
				if(count($sql) == 1 && array_key_exists(0, $sql)) $template_vars = $sql[0];
				else $template_vars = $sql;
			
			} else {
				$template_vars = array();
				$template_name = $template_name_match['template'];
				
			}
			
			$vars = array_merge($vars, CastHelper::to_array($template_vars));
			
			$sql = self::_parse_template_file($template_name, $vars);
					
		}
		
			
		$querys = array();
		
		if(preg_match_all(self::$_multi_query_pattern, trim($sql), $querys_matches)){
			
			$querys_strs = $querys_matches['sql'];
			
			foreach($querys_strs as $query_str) {
				
				$query_str = trim($query_str);
				
				if($query_str) {

					$prepared_query = self::_prepare_sql_code($query_str, $vars, true);
					if($prepared_query) $querys[] = $prepared_query;
				}
				
			}
					
		}
		
		return $querys;
		
	}

	
	//-----------------------------------------------------------------------------------------------------------------------
	
	
	protected $_debug = false;
	
	public function get_debug() {
		return $this->_debug;
	}

	public function set_debug($value) {
		$this->_debug = $value;
		return $this;
	}

	
	//-----------------------------------------------------------------------------------------------------------------------
	
	public function __construct(){
		
	}
	
	public function __destruct(){
		$this->disconnect();
	}
	
	
	protected $_connected = false, $_error_msg, $_error_query;

	
	protected function _clear_errors() {
		$this->_error_msg = null;
		$this->_error_query = null;
	}
	
	protected function _set_error($error_msg, $error_query=null) {
		
		$this->_error_msg = "[".$this->_get_engine()."] ".$error_msg;
		$this->_error_query = $error_query;
		
		if(ZPHP::get_config('db_log_errors')) {
			LogFile::log_error_file($this->_get_log_name(), $this->_error_msg." | SQL: ".$error_query);
		}

		if(ZPHP::is_debug_mode() || ZPHP::is_development_mode()) {
			throw new Exception($error_msg);
		}
	}
	
	public function has_errors() {
		return is_null($this->_error_msg);
	}
	
	public function get_error_html($show_query=true) {
		
		if($this->has_errors()) {
			
			$html = "<div style='text-align:left; margin: 5px auto; background: #F7F7F7; border: solid 1px #000; padding: 10px;'>
					<strong class='error'>Error:</strong><br />
					<span style='font-weight:normal'>".HTMLHelper::escape($this->get_error_msg())."</span>";
			
			if($show_query && $this->_error_query) {
			
				$query_html_array = array();
				
				foreach(explode("\n", (array) $this->get_error_query()) as $index => $line) {
					$query_html_array[] = "<div style='padding: 0; margin: 0;'><span style='text-align: right; display: inline-block; width: 25px; padding: 2px 5px 2px 0;margin: 0 10px 0 0; font-weight: bold; background: #CCC; '>".($index+1)."</span>".HTMLHelper::escape($line)."</div>";
				}
				
				$html.= "<br /><br /><strong class='error'>SQL:</strong> <br />
									 <div style='font-weight: normal; font-size: 9pt; background: #EDEDED; padding: 5px 5px'>".implode("\n", $query_html_array)."</div>";
				
			}
			
			
			$html.= "</div>";
						
		} 
		
		return $html;
	}
	
	public function get_error_msg() {
		return $this->_error_msg;
	}
	
	public function get_error_query() {
		return $this->_error_query;
	}
	
	
	public function connect() {
		
		if($this->_connected) {
			$this->_connected = $this->_test_connection();
		}

		if(!$this->_connected) {
			
			$this->_clear_errors();
			$this->_connected = $this->_connect();
			
			if(!$this->_connected) {
				
				$this->_set_error("Could not connect to database: ".$this->_get_connection_string());
			}
			
		}
		
	}
	
	public function is_connected() {
		return $this->_connected && $this->_test_connection();
	}
	
	public function disconnect() {
		if($this->_connected) {
			$this->_disconnect();
		}
	}
	
	public function ping() {
		return $this->_connected && $this->_ping();
	}
	
	
	protected function _query($sql) {
		
		$this->ping();
		$this->_clear_errors();
		
		if(ZPHP::get_config('db_log_querys')) {
			LogFile::log_file($this->_get_log_name(), $sql);
		}

		if(ZPHP::is_debug_mode() && ZPHP::get_config('db.debug'))
		{
			ZPHP::add_debug_data($sql, $this->_get_log_name(), ZPHP::DEBUG_TYPE_DB);
		}

		return $this->_execute_single_query($sql);
	}
	
	protected function _multi_query(array $queries) {
		
		$results = array();
		
		foreach($queries as $sql) {
			
			$results[] = $this->_query($sql);
			
			if($this->has_errors()) {
				break;
			}
			
		}
		
		return $results;
	}

	
	public function select_row($sql, $conditions=null){
		$rows = $this->select_rows($sql, 1, $conditions);
		if(count($rows) > 0) return array_shift($rows);
		else return null;
		
	}
	
	public function select_array($sql, $default=null) {
		return array_values($this->select_row($sql, $default));
	}
	
	
	public function select_value($sql, $default=null, $select_column=null){
		$row = $this->select_row($sql, array());
		if(count($row) > 0) return is_null($select_column) ? $row : $row[$select_column];
		else return $default;
		
	}
	

	public function query_insert($table, $values, $quote=true, $quote_columns=false) {
		return $this->_query("INSERT INTO `{$table}` ".SQLHelper::prepare_insert_values($values, $quote, $quote_columns));
	}
	
	
	public function query_update($table, $values, $conditions=array(), $quote=true, $quote_columns=false) {
		return $this->_query("UPDATE `{$table}` SET ".SQLHelper::prepare_columns_values($values, $quote, $quote_columns)." ".SQLHelper::prepare_conditions($conditions, true));
	}

	
		
	public function load_file($filename) {

		$fp = fopen($filename, 'rb');
		if(!$fp) return false;
						
		$sql_code = '';
		$newline = true;
		$success = true;
		
		while((@ $char = fgetc($fp)) !== false && $success) {

			if($char == '-' && $newline) {
			
				$next_char = fgetc($fp);
				
				if($next_char == '-') {
					
					FilesHelper::fgets_until($fp, "\n");
					$newline = true;
					continue;
				
				} else FilesHelper::fmove($fp, -1);
			}			
			
			if($char == '/') {
				
				$next_char = fgetc($fp);
				
				if($next_char == '*') {
					
					FilesHelper::fgets_until($fp, '*/');
					continue;
					
				} else FilesHelper::fmove($fp, -1);
			}
			
			
			if($char != ';') {
				
				$sql_code.= $char;
			
				if($char == '"' || $char == "'") $sql_code.= FilesHelper::fgets_until($fp, $char, true, true);
				else $newline = $char == "\n";
				
			} else {
			
				if(($sql_code = trim($sql_code))) $success = (bool) $this->_query(self::_prepare_sql_code($sql_code));
				
				$sql_code = '';				
			}
		}	
		
		
		if($success && ($sql_code = trim($sql_code))) $success = (bool) $this->_query(self::_prepare_sql_code($sql_code));
		
		fclose($fp);
			
		return $success;
		
		
	}
	
	//-----------------------------------------------------------------------------------------------------------------------

	abstract protected function _get_engine();
	
	abstract protected function _get_log_name();
	
	abstract protected function _get_connection_string();
	
	abstract protected function _connect();
	
	abstract protected function _disconnect();
	
	abstract protected function _test_connection();
	
	abstract protected function _ping();
	
	abstract protected function _execute_single_query($sql);
	
	abstract protected function _query_single_value($sql, $default=null);

	abstract public function query($sql);
			
	abstract public function select_rows($sql, $limit=null, $conditions=null, $order=null, $group_by=null);
	
	abstract public function search_rows($sql, &$rows=null, $limit=null, $conditions=null, $order=null, $group_by=null);
	
	abstract public function count_rows($sql, $conditions=null);
	
	abstract public function get_found_rows();
	
	abstract public function get_insert_id();
	
	abstract public function get_affected_rows();
	
	abstract public function start_transaction();
	
	abstract public function commit();
	
	abstract public function rollback();

}