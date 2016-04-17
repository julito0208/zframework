<?php 

class LogFile {

	public static function log_file($file, $string) {
		
		if(ZPHP::get_config()->has_value('logs_dir') && ZPHP::get_config()->has_value('logs_file_formats_log_file')) {
			$file = rtrim(ZPHP::get_config('logs_dir'), '/').'/'.ltrim(sprintf(ZPHP::get_config('logs_file_formats_log_file'), $file), '/');
		}
		
		$log = new LogFile($file);
		$log->add_log($string);
		
	}
	
	public static function log_error_file($file, $string) {
		
		if(ZPHP::get_config()->has_value('logs_dir') && ZPHP::get_config()->has_value('logs_file_formats_error_file')) {
			$file = rtrim(ZPHP::get_config('logs_dir'), '/').'/'.ltrim(sprintf(ZPHP::get_config('logs_file_formats_error_file'), $file), '/');
		}
		
		$log = new LogFile($file);
		$log->add_log($string);
		
	}
	
	/*----------------------------------------------------------------------------*/
	
	const DEFAULT_DATE_FORMAT = '[%d/%m/%Y %H:%M:%S]';
	
	protected $_log_file;
	protected $_date_format;
	
	public function __construct($log_file) {
		$this->_log_file = $log_file;
		$this->set_date_format(self::DEFAULT_DATE_FORMAT);
	}
	
	public function get_date_format() {
		return $this->_date_format;
	}

	public function set_date_format($value) {
		$this->_date_format = $value;
		return $this;
	}

	
	public function add_log($string) {
			
		$date_string = Date::now()->format($this->get_date_format());
		
		$dirname = dirname($this->_log_file);
		
		if(!file_exists($dirname))
		{
			@ mkdir($dirname, 0777, true);
		}
		
		@ file_put_contents($this->_log_file, $date_string.' '.$string."\n", FILE_APPEND);
	}
	
	
}