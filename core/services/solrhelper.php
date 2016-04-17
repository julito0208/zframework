<?php

class SolrHelper
{
	public static function escape($arg1=null, $arg2=null)
	{
		if(func_num_args() == 0)
		{
			return null;
		}
		else if(func_num_args() == 1 && !is_array($arg1))
		{
			return urlencode($arg1);
		}
		else
		{
			$args = func_get_args();
			$args = ArrayHelper::plain($args);
			
			foreach($args as $key => $arg)
			{
				$args[$key] = self::escape($arg);
			}
			
			return $args;
		}
		
	}
	
}

