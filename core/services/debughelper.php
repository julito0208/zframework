<?php 

class DebugHelper
{
	public static function var_export_html($var, $return=false)
	{
		return VariableHelper::var_export_html($var, $return);
	}
	
	public static function var_dump_html($var, $return=false)
	{
		return VariableHelper::var_dump_html($var, $return);
	}
	
	public static function var_export_to_file($var, $path, $append=true, $use_app_dir=true)
	{
		$contents = var_export($var, true);
		
		if($use_app_dir)
		{
			$path = ZPHP::get_app_dir().'/'.ltrim($path, '/');
		}
		
		if($append) $contents.= "\n";
		
		@ file_put_contents($path, $contents, $append ? FILE_APPEND : 0);
		
	}
	
	public static function var_dump_to_file($var, $path, $append=true, $use_app_dir=true)
	{
		ob_start();
		var_dump($var);
		$contents = ob_get_clean();
		
		if($append) $contents.= "\n";
		
		if($use_app_dir)
		{
			$path = ZPHP::get_app_dir().'/'.ltrim($path, '/');
		}
		
		@ file_put_contents($path, $contents, $append ? FILE_APPEND : 0);
		
	}
	
	
}