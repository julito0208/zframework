<?php 


class HTMLPageUserLogout extends HTMLPage {

	/* @return URLPattern */
	public static function get_url_pattern() {

		$url_pattern = ZPHP::get_config('user_session_logout_url');

		return new URLPattern(preg_quote($url_pattern), 'UserLogout', get_class());
	}

	public static function get_logout_url()
	{
		return self::get_url_pattern()->format_url();
	}

	public function __construct() {

		parent::__construct();
		User::logout_user();

		$url = ZPHP::get_config('user_session_logout_redirect');

		NavigationHelper::location_go($url);
	}

}