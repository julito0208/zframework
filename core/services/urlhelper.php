<?php

class URLHelper {
	
	public static function url_remove_protocol($url) {
		return preg_replace('#(?i)^\w+\:\/\/#', '', $url);
	}
	
	
	public static function url_put_protocol($url, $protocol, $replace=true) {
		
		if($replace || preg_match('#^(\w+)\:\/\/.*#', $url) === 0) {	
			return preg_replace('#^(\w+)\:\/\/\:?$#', '', trim($protocol, ':'))."://{$url}";
		} else {
			return $url;
		}
	}
	
	
	public static function get_zframework_static_url($filename) {
		return rtrim(ZPHP::get_config('zframework_static_url'), '/').'/'.ltrim($filename, '/');
	}

	public static function escape_url_string($string)
	{
		$string = strtolower($string);
		$string = preg_replace('#[^\w+]+#', '', $string);
		return $string;
	}
}