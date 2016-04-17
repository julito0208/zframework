<?php 

class ParamsHelper
{
	protected static function _get_params_file()
	{
		return ZPHP::get_config('params_file');
	}
	
	protected static function _update_params($params)
	{
		$path = self::_get_params_file();
		
		$contents = "<?php\n\$params = ".var_export((array) $params, true).";\n";
		
		$tmp_file = FilesHelper::file_create_site_temp($contents);
		
		@ unlink($path);
		@ rename($tmp_file, $path);
	}
	
	protected static function _get_params($update=true)
	{
		$path = self::_get_params_file();
		
		if(!file_exists($path))
		{
			if($update)
			{
				self::_update_params(array());
				return self::_get_params(false);
			}
			else
			{
				return array();
			}
		}
		else
		{
			@ include($path);
			
			if(!isset($params))
			{
				if($update)
				{
					self::_update_params(array());
					return self::_get_params(false);
				}
				else
				{
					return array();
				}
				
			}
			else
			{
				return $params;
			}
		}
	}
	
	/*--------------------------------------------------------------------------------------*/
	
	public static function set_param($key, $value)
	{
		$params = self::_get_params();
		ArrayHelper::extended_set_value($params, $key, $value);
		self::_update_params($params);
	}
	
	public static function get_param($key, $default=null)
	{
		$params = self::_get_params();
		return ArrayHelper::extended_get_value($params, $key, $default);
	}
	
	public static function has_param($key)
	{
		$params = self::_get_params();
		return ArrayHelper::extended_has_key($params, $key);
	}
	
	public static function remove_param($key)
	{
		$params = self::_get_params();
		return ArrayHelper::extended_remove_value($params, $key);
	}
}