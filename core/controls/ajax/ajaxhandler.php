<?php

class AjaxHandler extends AjaxJSONFormResponse {
	
	const URL_HANDLER_CALL = 'AjaxHandlerHandler';
	const URL_STATIC_METHOD_CALL = 'AjaxHandlerStaticMethod';
	
	/* @return URLPattern */
	public static function get_handler_url_pattern() {
		return new URLPattern(ZPHP::get_config('redirect_control_ajax_handlers_call_pattern'), self::URL_HANDLER_CALL);
	}
	
	/* @return URLPattern */
	public static function get_static_method_url_pattern() {
		return new URLPattern(ZPHP::get_config('redirect_control_ajax_static_methods_call_pattern'), self::URL_STATIC_METHOD_CALL);
	}
	
	/*--------------------------------------------------------------------*/
	
	public function __construct() {
		parent::__construct();
	}
	
}