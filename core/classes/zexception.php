<?php

class ZException extends Exception
{
	/* @return ZException */
	public static function create_from_exception(Exception $ex)
	{
		if(!ClassHelper::is_instance_of($ex, 'ZException'))
		{
			$zex = new ZException();
			$zex->setLine($ex->getLine());
			$zex->setFile($ex->getFile());
			$zex->setMessage($ex->getMessage());
			$zex->setZTraceAsString($ex->getTraceAsString());
			return $zex;
		}
		else
		{
			return $ex;
		}
	}
	
	/* @return ZException */
	public static function create_from_error(array $error)
	{
		$message = $error['message'];
		$file = $error['file'];
		$line = $error['line'];
		$id_type = $error['type'];
		$ztrace_string = null;

		if(preg_match('#(?i)(?m)(?s)(?P<stack_trace_prefix>stack\s+trace\:\s*)(?P<stack_trace>.*)#', $message, $message_match, PREG_OFFSET_CAPTURE))
		{
			$ztrace_string = $message_match['stack_trace'][0];
			$message = substr($message, 0, $message_match['stack_trace_prefix'][1]);
		}
		
		$zex = new ZException();
		$zex->setMessage($message);
		$zex->setFile($file);
		$zex->setLine($line);
		$zex->setIdType($id_type);
		$zex->setZTraceAsString($ztrace_string);
		return $zex;
	}
	
	/*---------------------------------------------------------------------------------------------*/
	
	protected static $_reverse_stack = true;
	
	protected static $_trace_void_first_rows_prefixs = array(
		
		array(
			'RedirectControl::redirect_process()',
			'RedirectControl::_test_uri_redirects',
			'ClassHelper::create_instance_array',
			'eval()',
			'eval()\'d code(1):',
		),
		
		array(
			'RedirectControl::redirect_process()',
			'RedirectControl::_test_uri_redirects',
			'HTMLControl->out()',
			'MVControl->to_string()',
			'MVControl->_parse_mv_content()',
			'MVParamsControl->_get_parse_vars()',
		),
		
		array(
			'RedirectControl::redirect_process()',
			'RedirectControl::_test_uri_redirects',
		),
		
		array(
			'RedirectControl::redirect_process()',
		),
	);

	/*---------------------------------------------------------------------------------------------*/
	
	protected $_ztrace;
	protected $_id_type;
	
	public function __construct($message=null, $file=null, $line=null, $code=null)
	{
		parent::__construct();
		$this->setMessage($message);
		$this->setFile($file);
		$this->setLine($line);
		$this->setCode($code);
	}
	
	protected function setZTrace($trace)
	{
		$this->_ztrace = (array) $trace;
		$this->_ztrace = array_reverse($this->_ztrace);
					
		foreach(self::$_trace_void_first_rows_prefixs as $remove_first_rows_prefixs)
		{
			if(count($this->_ztrace) < count($remove_first_rows_prefixs))
			{
				continue;
			}
		
			$remove_trace_first_rows = true;
			$trace_index = 0;	

			foreach($remove_first_rows_prefixs as $test_code)
			{
				if(stripos(trim($this->_ztrace[$trace_index]['code']), trim($test_code)) !== 0)
				{
					$remove_trace_first_rows = false;
					break;
				}

				$trace_index++;
			}

			if($remove_trace_first_rows)
			{
				$this->_ztrace = array_slice($this->_ztrace, count($remove_first_rows_prefixs));
				break;
			}
		}
				
		if(!self::$_reverse_stack)
		{
			$this->_ztrace = array_reverse($this->_ztrace);
		}
	}
	
	protected function setZTraceAsString($trace_string)
	{
		$ztrace = array();
		
		if(($count_matches = preg_match_all("#(?i)\#\d+\s+(?P<file>.+?)\((?P<line_number>.+?)\)\s*\:\s*(?P<code>.+)\n#", $trace_string, $stack_trace_matches, PREG_OFFSET_CAPTURE)))
		{
			$files = array();
			
			for($i=0; $i<$count_matches; $i++)
			{
				$ztrace[] = array(
					'line' => $stack_trace_matches['line_number'][$i][0],
					'code' => $stack_trace_matches['code'][$i][0],
					'file' => $stack_trace_matches['file'][$i][0],
				);
			}
		}
		
		$this->setZTrace($ztrace);
	}
	
	protected function setLine($line)
	{
		$this->line = $line;
	}
	
	protected function setFile($file)
	{
		$this->file = $file;
	}
	
	protected function setCode($code)
	{
		$this->code = (integer) $code;
	}
	
	protected function setMessage($message)
	{
		$this->message = (string) $message;
	}
	
	protected function setIdType($id_type)
	{
		$this->_id_type = (integer) $id_type;
	}

	public function getZTrace()
	{
		return array_merge((array) $this->_ztrace, array());
	}
	
	public function getIdType()
	{
		return (integer) $this->_id_type;
	}
}

