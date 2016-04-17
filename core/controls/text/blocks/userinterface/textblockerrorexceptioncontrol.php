<?php 

class TextBlockErrorExceptionControl extends TextControl implements ErrorExceptionBlock{

	public static function _format_block($title)
	{
		$horizontal_space_len = 2;
		$line = str_repeat('-', strlen($title)+($horizontal_space_len*2));
		$horizontal_space = str_repeat(' ', $horizontal_space_len);
		return "{$line}\n{$horizontal_space}{$title}{$horizontal_space}\n{$line}";
	}
	
	const FILE_OUT_PADDING = 5;
	const STACK_TRACE_REMOVE_FILES_COMMON_DIR = true;
	const REVERSE_STACK_TRACE = true;

	protected static $_debug_vars = array(
		'_SERVER', 
		'_POST', 
		'_GET', 
		'_SESSION', 
		'_COOKIES', 
		'_FILES'
	);

	protected $_file;
	protected $_error_line;
	protected $_message;
	protected $_stack_trace = array();	
	
	public function __construct(Exception $ex) {
		
		parent::__construct();
		
		$ex = ZException::create_from_exception($ex);
		
		$this->_message = $ex->getMessage();
		$this->_error_line = $ex->getLine();
		$this->_file = $ex->getFile();
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
		
		$file_title = StringHelper::remove_prefix($this->_file, ZPHP::get_site_dir());
		
		$this->set_param('file', $this->_file);
		$this->set_param('error_line', $this->_error_line);
		$this->set_param('message', $this->_message);
		$this->set_param('file_lines', $file_lines);
		$this->set_param('debug_vars', self::$_debug_vars);
		$this->set_param('stack_trace', $this->_stack_trace);
		$this->set_param('file_error_title', $file_title.' ['.$this->_error_line.']');
		
		parent::prepare_params();
		
	}
	
}