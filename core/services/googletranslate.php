<?php 

class GoogleTranslate {

	private static $_GOOGLE_TRANSLATE_URL_FORMAT = 'http://www.google.com/translate_t?hl=en&ie=%1$s&text=%2$s&langpair=%3$s|%4$s';
	private static $_DEFAULT_CHARSET = 'UTF8';
	private static $_TRANSLATED_TEXT_PATTERN = '#result_box.*?\>.*?\<span.*?\>(?P<text>.+?)\<#';

	
	protected static function _translate_text_plain($text, $language_from, $language_to, $is_utf8=false) {
		
		if(!trim($text)) return $text;
		
		$language_from_key = LanguageHelper::parse_language_code($language_from, false);
		
		if($language_from_key) {
			
			$language_from = $language_from_key;
			
		} 
		
		$language_to_key = LanguageHelper::parse_language_code($language_to, false);
		
		if($language_to_key) {
			
			$language_to = $language_to_key;
			
		} 
		
		$language_from = preg_replace('#[^a-z]+#', '', strtolower(trim($language_from)));
		$language_to = preg_replace('#[^a-z]+#', '', strtolower(trim($language_to)));
	
		$charset = self::$_DEFAULT_CHARSET;
		
		if(!$is_utf8) {
			$text = utf8_encode($text);
		}
		
		$text_encoded = urlencode($text);
				
		$url = sprintf(self::$_GOOGLE_TRANSLATE_URL_FORMAT, $charset, $text_encoded, $language_from, $language_to);
		@ $url_contents = file_get_contents($url);
		
		if($url_contents) {

			if(preg_match(self::$_TRANSLATED_TEXT_PATTERN, $url_contents, $translate_match)) {
			
				$translated_text = $translate_match['text'];
				
				return $translated_text;
			} 
			
		} 
		
		return null;
		
	}
	
	protected static function _translate_text_html($html, $language_from, $language_to, $is_utf8=false) {
		
		return preg_replace_callback('#(?s)(?m)\>(?P<content>.*?)\<#', create_function('$match', 'return ">".GoogleTranslate::translate_text(HTMLHelper::unescape($match["content"]), '.var_export($language_from, true).', '.var_export($language_to, true).', '.var_export($is_utf8, true).')."<";'), $html);
	}

	/*--------------------------------------------------------------------------------------------------------*/
	
	public static function translate_text($text, $language_from, $language_to, $is_utf8=false, $search_html=true) {
		
		if($search_html && stripos($text, '>') !== false)
		{
			return self::_translate_text_html($text, $language_from, $language_to, $is_utf8);
		}
		else
		{
			return self::_translate_text_plain($text, $language_from, $language_to, $is_utf8);
		}
		
	}
}