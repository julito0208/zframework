<?php 

class HTMLBlockErrorExceptionControl extends HTMLControl implements ErrorExceptionBlock{

	const FILE_OUT_PADDING = 5;
	const STACK_TRACE_REMOVE_FILES_COMMON_DIR = true;
	const REVERSE_STACK_TRACE = false;

	protected static $_debug_vars = array(
		'_SERVER', 
		'_POST', 
		'_GET', 
		'_SESSION', 
		'_COOKIES', 
		'_FILES'
	);
	
	protected static $_error_types_names = array (
		30719 => 'E_ALL',
		64 => 'E_COMPILE_ERROR',
		128 => 'E_COMPILE_WARNING',
		16 => 'E_CORE_ERROR',
		32 => 'E_CORE_WARNING',
		8192 => 'E_DEPRECATED',
		1 => 'E_ERROR',
		8 => 'E_NOTICE',
		4 => 'E_PARSE',
		4096 => 'E_RECOVERABLE_ERROR',
		2048 => 'E_STRICT',
		16384 => 'E_USER_DEPRECATED',
		256 => 'E_USER_ERROR',
		1024 => 'E_USER_NOTICE',
		512 => 'E_USER_WARNING',
		2 => 'E_WARNING',
	);
	
	protected $_file;
	protected $_error_line;
	protected $_message;
	protected $_id_type = null;
	protected $_stack_trace = array();	
	
	public function __construct(Exception $ex) {
		
		parent::__construct();
		
		$ex = ZException::create_from_exception($ex);
		
		$this->_message = $ex->getMessage();
		$this->_error_line = $ex->getLine();
		$this->_file = $ex->getFile();
		$this->_id_type = $ex->getIdType();
		$this->_stack_trace = $ex->getZTrace();			
	}
	
	public function prepare_params() {
	
		$file_lines = file($this->_file);

		foreach($file_lines as $index => $line) {
			$file_lines[$index] = rtrim($line);
		}
		
		$file_lines_count = count($file_lines);

		if($file_lines_count > (self::FILE_OUT_PADDING*2) + 1) {
			
			$start_line = $this->_error_line-self::FILE_OUT_PADDING;
			$end_line = $this->_error_line+self::FILE_OUT_PADDING;
			
			if($start_line < 0) {
				
				$start_line = 0;
				
			} else if($end_line >= $file_lines_count) {
				
				$start_line = $file_lines_count - $end_line;
				
			} 
			
			$new_file_lines = array();
			
			for($i=0; $i<=((self::FILE_OUT_PADDING*2)+1); $i++) {
				$new_file_lines[$i+$start_line] = $file_lines[$start_line+$i-1];
			}
			
			$file_lines = $new_file_lines;
			 
			
		}
		
		$this->set_param('file', $this->_file);
		$this->set_param('error_line', $this->_error_line);
		$this->set_param('message', $this->_message);
		$this->set_param('file_lines', $file_lines);
		$this->set_param('debug_vars', self::$_debug_vars);
		$this->set_param('stack_trace', $this->_stack_trace);
		$this->set_param('id_type', $this->_id_type);
		$this->set_param('type_name', array_key_exists($this->_id_type, self::$_error_types_names) ? self::$_error_types_names[$this->_id_type] : null);
		
		parent::prepare_params();
		
	}
	
}