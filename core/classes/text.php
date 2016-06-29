<?php 

class Text 
{
	public static function get($text, $arg1=null, $arg2=null)
	{
		$args = func_get_args();
		$text = array_shift($args);
		
		$string = LanguageHelper::get_text($text);
		
		if(!empty($args))
		{
			try 
			{
				$string = call_user_func_array('sprintf', array_merge(array($string), $args));
			}
			catch(Exception $ex)
			{
				$string = '';
			}
			
		}
		
		return $string;
	}

	public static function get_language($language, $text, $arg1=null, $arg2=null)
	{
		$args = func_get_args();
		$text = array_shift($args);

		$string = LanguageHelper::get_text($text, $language);

		if(!empty($args))
		{
			try
			{
				$string = call_user_func_array('sprintf', array_merge(array($string), $args));
			}
			catch(Exception $ex)
			{
				$string = '';
			}

		}

		return $string;
	}

	public static function get_html($text, $arg1=null, $arg2=null)
	{
		$args = func_get_args();
		$text = call_user_func_array(array(self, 'get'), $args);
		return HTMLHelper::escape($text);
	}

	public static function get_js($text, $arg1=null, $arg2=null)
	{
		$args = func_get_args();
		$text = call_user_func_array(array(self, 'get'), $args);
		return JSHelper::cast_str($text);
	}

	public static function get_language_html($language, $text, $arg1=null, $arg2=null)
	{
		$args = func_get_args();
		$text = call_user_func_array(array(self, 'get_language'), $args);
		return HTMLHelper::escape($text);
	}

	public static function get_language_js($language, $text, $arg1=null, $arg2=null)
	{
		$args = func_get_args();
		$text = call_user_func_array(array(self, 'get_language'), $args);
		return JSHelper::cast_str($text);
	}

}