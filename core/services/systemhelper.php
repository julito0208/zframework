<?php 

class SystemHelper
{

	protected static $_allowed_unquoted_system_params = array('<', '>');

	protected static function _get_php_command()
	{
		return ZPHP::get_config('crons_php_command');
	}

	protected static function _escape_arg($arg)
	{
		$arg = trim($arg);
		
		if(in_array($arg, self::$_allowed_unquoted_system_params))
		{
			return $arg;
		}
		else
		{
//			return '"'.var_export($arg, true).'"';
			return '"'.StringHelper::escape($arg).'"';
		}
		
		return $arg;
	}
	
	
	protected static function _quote_params($arg1, $arg2=null)
	{
		$params = array();
		$args = func_get_args();
		
		foreach($args as $arg)
		{
			if(is_array($arg))
			{
				$params = array_merge($params, call_user_func_array(array('SystemHelper', '_quote_params'), $arg));
			}
			else
			{
				$params[] = self::_escape_arg($arg);
			}
		}
		
		return $params;
	}
	
	/*------------------------------------------------------------------------------------------------------------*/
	
	public static function execute($arg1, $arg2=null)
	{
		$args = func_get_args();
		$params = self::_quote_params($args);
		
		$exec = implode(' ', $params);

		return @ exec($exec);
	}
	
	public static function execute_background($arg1, $arg2=null)
	{
		$args = func_get_args();
		$params = self::_quote_params($args);
		
		$exec = implode(' ', $params);
		$exec.= " > /dev/null &";
		
		return @ exec($exec);
	}
	
	public static function execute_cron($cron_name, $arg1, $arg2=null)
	{
		$args = func_get_args();
		$params = self::_quote_params($args);
		
		array_unshift($params, self::_escape_arg(ZPHP::get_app_dir()));
		array_unshift($params, self::_escape_arg(ZPHP::get_config('crons_command')));
		array_unshift($params, self::_escape_arg(ZPHP::get_zframework_dir().'/init.php'));
		array_unshift($params, self::_escape_arg(self::_get_php_command()));
		
		$exec = implode(' ', $params);
		
		return @ exec($exec);
	}
	
	public static function execute_cron_background($cron_name, $arg1, $arg2=null)
	{
		$args = func_get_args();
		$params = self::_quote_params($args);

		array_unshift($params, self::_escape_arg(ZPHP::get_app_dir()));
		array_unshift($params, self::_escape_arg(ZPHP::get_config('crons_command')));
		array_unshift($params, self::_escape_arg(ZPHP::get_zframework_dir().'/init.php'));
		array_unshift($params, self::_escape_arg(self::_get_php_command()));
		
		$exec = implode(' ', $params);
		$exec.= " > /dev/null &";
		return exec($exec);
	}
	
	
}