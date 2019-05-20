<?php 


class MySQL extends DBConnection {
	
	const DEFAULT_SERVER = 'localhost';
	const DEFAULT_USER = null;
	const DEFAULT_PASS = null;
	const DEFAULT_DBNAME = null;
	const DEFAULT_PORT = 3306;
	const DEFAULT_CHARSET = 'latin9';
	
	const LOG_NAME = 'mysql';

	//----------------------------------------------------------------------------------

	protected static $_rows_column_void_pattern = '/^(?i)\@\w+\s*\:?\s*\=/';
	protected static $_rows_column_pattern = '/^\s*(?i)(?P<is_key>\#key|\$)|(?:(?:\s*\(\s*(?P<functions>\S[ \w\:\-\|\,\;]+?)\s*\)\s*)?(?P<name>.+?)(?P<is_array>\[\])?)\s*$/';
	protected static $_select_column_name = '#value';
	
	protected static function _prepare_select_rows($id_result, $select_column=null) {

		$rows = array();

		if($id_result && $id_result instanceof mysqli_result && (@ $num_rows = mysqli_num_rows($id_result)) > 0){
			
			$row_code = '$row = array();';
			
			$frow = mysqli_fetch_assoc($id_result);
			$columns = array_keys($frow);
			$valid_columns = array();
			
			$add_row_key = '';
			
			foreach($columns as $column) {
								
				if(preg_match(self::$_rows_column_void_pattern, $column)) continue;

				else if(preg_match(self::$_rows_column_pattern, $column, $column_match)) {

					if($column_match['is_key']) $add_row_key = '$frow['.var_export($column,True).']';
					
					else if($column_match['name']) {
						
						$valid_columns[] = $column;
												
						$column_name = $column_match['name'];
						$frow_column_code = '($frow['. var_export($column,True) .'])';
							
						if($column_match['functions']){
							
							foreach(preg_split('/(\s|\;|\,|\|)/', preg_replace('/\s+/', ' ', $column_match['functions'])) as $column_function) {
							
								$prepared_column_function = str_replace(array(' ', '-'), '_', strtolower(trim($column_function)));
								
								if(in_array($prepared_column_function, array('bool','string','integer','float'))) 
									$frow_column_code = "(({$prepared_column_function}) {$frow_column_code})";
									
							}
						}
											
						$row_code .= '$row';
					
						$column_keys = explode('[', str_replace(']', '', $column_name));
						foreach($column_keys as $column_key) $row_code .= '['. var_export($column_key,True) .']';
						
						$row_code .= " = {$frow_column_code};";
					
						
						if(is_null($select_column) && $column_name == self::$_select_column_name) $select_column = $column_name;
					}
				}
					
			}
			
			$num_columns = count($valid_columns);
					
			if(is_null($select_column) && $num_columns == 1) $row_code .= ' $row = array_shift($row); ';
			elseif($select_column === true) $row_code .= $num_columns > 0 ? ' $row = array_shift($row); ' : ' $row = null; ';
			elseif($select_column) $row_code .= ' $row = $row[' . var_export($select_column, true) . ']; ';
				
			
			$row_code .= '$rows['.$add_row_key.'] = $row; ';
			
			eval($row_code);
			while($frow = mysqli_fetch_assoc($id_result)) {
				
				eval($row_code);
				
			}
		}
		
		
		
		return $rows;
	}
	
	
	
	//------------------------------------------------------------------------------------------------------------------------------------------
	
	private $_server, $_user, $_pass, $_dbname, $_port, $_charset;
	private $_connection_resource;
	
	public function __construct($server=null, $user=null, $pass=null, $dbname=null, $port=null, $charset=null){
		
		parent::__construct();

		$this->_server = $server === null ? self::DEFAULT_SERVER : $server;		
		$this->_user = $user === null ? self::DEFAULT_USER : $user;		
		$this->_pass = $pass === null ? self::DEFAULT_PASS : $pass;		
		$this->_port  = (integer) ($port === null ? self::DEFAULT_PORT : $port);		
		$this->_dbname  = $dbname === null ? self::DEFAULT_DBNAME : $dbname;		
		$this->_charset  = $charset === null ? self::DEFAULT_CHARSET : $charset;

		$this->connect();
	}
	
	
	
	//------------------------------------------------------------------------------------------------------------------------------------------
	
	
	protected function _get_engine() {
		return self::ENGINE_MYSQL;
	}
	
	protected function _get_log_name() {
		return self::LOG_NAME.'_'.$this->_dbname;
	}
	
	protected function _get_connection_string() {
		return "Server: {$this->_server} | User: {$this->_user} | Pass: {$this->_pass} | DBName: {$this->_dbname} | Port: {$this->_port} ";
	}
	

	protected function _test_connection(){
		return $this->_connected && (@ mysqli_ping($this->_connection_resource));
	}
	
	protected function _ping(){
		return $this->_test_connection();
	}
	


	protected function _updateConnectionDatabase(){
		if($this->_dbname && $this->_connection_resource)
			$this->_execute_single_query("USE {$this->_dbname}");
		
	}
	
	protected function _updateConnectionCharset(){
		if($this->_charset && $this->_connection_resource)
			@ mysqli_set_charset($this->_connection_resource, $this->_charset);
	}
	
	

	
	protected function _connect() {

		@ $this->_connection_resource = mysqli_connect($this->_server, $this->_user, $this->_pass, null, $this->_port);

		if($this->_connection_resource) {
			$this->_updateConnectionCharset();
			$this->_updateConnectionDatabase();
			return true;
		}
		
		return false;
	
	}

	
	protected function _disconnect(){
		
		if($this->_connection_resource) {
			@ mysqli_close($this->_connection_resource);
		}
	}
	
	
	protected function _execute_single_query($sql){

		$query_return = @ mysqli_query($this->_connection_resource, $sql);

		if(mysqli_error($this->_connection_resource)) {
			$this->_set_error(mysqli_error($this->_connection_resource), $sql);
		}
		
		return $query_return;
	}
	
	
	protected function _query_single_value($sql, $default=null) {
		$id_result = $this->_execute_single_query($sql);
		if($id_result && $id_result instanceof mysqli_result && (@ $num_rows = mysqli_num_rows($id_result)) > 0){
			$row = mysqli_fetch_row($id_result);
			return $row[0];
			
		} else return $default;
	}

	
	
	//------------------------------------------------------------------------------------------------------------------------------------------
	
	
	
	public function set_database($dbname){
		$this->_dbname = $dbname;
		$this->_updateConnectionDatabase();
	}
	
	
	
	public function get_database() {
		return $this->_dbname;
	}
	
	public function set_charset($charset){
		$this->_charset = $charset;
		$this->_updateConnectionCharset();
	}

	
	//---------------------------------------------------------------------
	
	public function select_rows($sql, $limit=null, $conditions=null, $order=null, $group_by=null){
		
		$querys = self::_prepare_querys($sql, $conditions);

		$sql = array_pop($querys);
		$this->_multi_query($querys);
		
		$sql = trim($sql);
		if(StringHelper::starts_with($sql, 'SELECT', true)) {
			$sql = "SELECT select_rows.* FROM ({$sql}) AS select_rows";
		} 
		
		$sql.= SQLHelper::prepare_conditions($conditions, true);

		if(!is_null($order)) {
			$order = array_filter((array) $order);
			$order_columns = preg_replace('#(\()|(\))#', '', SQLHelper::prepare_conditions($order, false, null, ', '));
			if($order_columns) $sql.= " ORDER BY {$order_columns}";
			
		}
		
		if(!is_null($limit)) {
			
			if(!is_array($limit)) $sql.= " LIMIT ".((integer) $limit);
			
			else {
				
				if(array_key_exists('page', $limit)) {
					
					$page = ArrayHelper::pop_value($limit, 'page');
					
					$length = ArrayHelper::pop_value($limit, array('length', 0, 1));
					$start = ((integer) $page) * $length;
				
				} else {
					
					$start = ArrayHelper::pop_value($limit, array('start', 'pos', 0, 1));
					$length = ArrayHelper::pop_value($limit, array('length', 0, 1));
					
				}
				
				$sql.= " LIMIT ".((integer) $start).", ".((integer) $length);
				
			}
		}
		
		if($group_by) {
			$sql.= " GROUP BY {$group_by}";
		}
		
		
		$id_result = $this->_query($sql);
		$rows = self::_prepare_select_rows($id_result);

		return $rows;
	}

	public function search_rows($sql, &$rows=null, $limit=null, $conditions=null, $order=null, $group_by=null) {
		
		$querys = self::_prepare_querys($sql, $conditions);
		$sql = array_pop($querys);
		$this->_multi_query($querys);
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS search_rows.* FROM ({$sql}) AS search_rows";
		
		$sql.= SQLHelper::prepare_conditions($conditions, true);

		if(!is_null($order)) {
			$order = array_filter((array) $order);
			$order_columns = preg_replace('#(\()|(\))#', '', SQLHelper::prepare_conditions($order, false, null, ', '));
			if($order_columns) $sql.= " ORDER BY {$order_columns}";
		}

		if(!is_null($limit)) {
			
			if(!is_array($limit)) $sql.= " LIMIT ".((integer) $limit);
			
			else {
				
				if(array_key_exists('page', $limit)) {
					
					$page = ArrayHelper::pop_value($limit, 'page');
					
					$length = ArrayHelper::pop_value($limit, array('length', 0, 1));
					$start = ((integer) $page) * $length;
				
				} else {
					
					$start = ArrayHelper::pop_value($limit, array('start', 'pos', 0, 1));
					$length = ArrayHelper::pop_value($limit, array('length', 0, 1));
					
				}
				
				$sql.= " LIMIT ".((integer) $start).", ".((integer) $length);
				
			}
		}
		
		if($group_by) {
			
			$sql.= " GROUP BY {$group_by}";
			
		}
		
		$id_result = $this->_query($sql);
		$rows = self::_prepare_select_rows($id_result);
		
		return $this->get_found_rows();
	}
	
	
	public function count_rows($sql, $conditions=null) {
		
		$querys = self::_prepare_querys($sql, $conditions);
		$sql = array_pop($querys);
		$this->_multi_query($querys);
		
		$sql = "SELECT * FROM ({$sql}) AS rows";
		
		$row = $this->select_row("{$sql} LIMIT 1");
		
		if(!$row) {
			
			$rows = array();
			return 0;
			
		}
		
		$keys = array_keys($row);
		
		$sql.= SQLHelper::prepare_conditions($conditions, true, $keys);

		return $this->select_value("SELECT COUNT(*) FROM ({$sql}) rows");

	}
	
	
	//------------------------------------------------------------------------------------------------------------------------------------------
	
	
	public function get_found_rows() { 
		return $this->_query_single_value('SELECT FOUND_ROWS()', 0); 
	}
	
	public function get_insert_id() { 
		return $this->_query_single_value('SELECT LAST_INSERT_ID()', 0);
	}
	
	public function get_affected_rows() { 
		$this->ping();
		return mysqli_affected_rows($this->_connection_resource);
	}
	
	
	public function query($sql) {
		$sql = self::_prepare_querys($sql);
		$result = $this->_query($sql[0]);
		if($result instanceof mysqli_result || $result instanceof mysqli_stmt) return true;
		else if(is_null($result)) return false;
		else return $result;
	}


    //----------------------------------------------------------------------------------

    public function start_transaction()
    {
        $this->query("SET AUTOCOMMIT=0");
        $this->query("BEGIN");
    }

    public function commit()
    {
        $this->query("COMMIT");
    }

    public function rollback()
    {
        $this->query("ROLLBACK");
    }

	
}
