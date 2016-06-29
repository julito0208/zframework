<?php

class ZfUser extends ZfUserCache
{

	/* ZPHP Generated Code ------------------------------------------ */
	/* /ZPHP Generated Code ------------------------------------------ */

	public static function encode_pass($pass)
	{
		return md5($pass);
	}

	public function generate_token_restore_password()
	{
		$token = md5($this->get_id_user()).sha1('pass').sha1($this->get_username());
		$this->set_token_restore_pass($token);
		$this->save();
		return $token;
	}

	public function generate_token_activation()
	{
		$token = md5($this->get_id_user()).sha1('activation').sha1($this->get_username());
		$this->set_token_activation($token);
		$this->save();
		return $token;
	}

	public function generate_token_cookie()
	{
		$token = md5($this->get_id_user()).sha1('cookie').sha1($this->get_username());
		$this->set_token_cookie($token);
		$this->save();
		return $token;
	}

	/**
	*
	* @return ZfUser
	*
	*/
	public static function get_by_token_restore_password($token)
	{
		return self::get_row(array('token_restore_pass' => $token));
	}

	/**
	*
	* @return ZfUser
	*
	*/
	public static function get_by_token_activation($token)
	{
		return self::get_row(array('token_activation' => $token));
	}


	/**
	*
	* @return ZfUser
	*
	*/
	public static function get_by_token_cookie($token)
	{
		return self::get_row(array('token_cookie' => $token));
	}


	private static $_COOKIE_DAYS = 600;
	private static $_SESSION_VARNAME;
	private static $_SESSION_LOGIN_VARNAME = 'usuariosession';
	private static $_SESSION_LOGOUT_VARNAME = 'usuariosessionl';
	private static $_COOKIE_VARNAME = 'usuario';

	/**
	 *
	 * @return ZfUser
	 *
	 */
	public static function get_logged_user()
	{
		$id_user = SessionHelper::get_var(self::$_SESSION_LOGIN_VARNAME);
		$usuario = ZfUser::get_by_id_user($id_user);

		if($usuario)
		{
			return $usuario;
		}

		if(!SessionHelper::get_var(self::$_SESSION_LOGOUT_VARNAME))
		{
			$hash = $_COOKIE[self::$_COOKIE_VARNAME];

			if ($hash)
			{
				$usuario = ZfUser::get_by_token_cookie($hash);

				if ($usuario && $usuario->get_is_active())
				{
					$usuario->log_in();
					return $usuario;
				}
			}
		}
	}

	public function log_in()
	{
		SessionHelper::add_var(self::$_SESSION_LOGIN_VARNAME, $this->get_id_user());

		$this->set_last_login(DateHelper::current_datetime());
		$this->save();
	}

	public static function log_out()
	{
		SessionHelper::add_var(self::$_SESSION_LOGOUT_VARNAME, 1);
		SessionHelper::remove_var(self::$_SESSION_LOGIN_VARNAME);
	}

	public function remember_login()
	{
		$hash = $this->generate_token_cookie();
		$days = self::$_COOKIE_DAYS;

		NavigationHelper::cookie_add_days(self::$_COOKIE_VARNAME, $hash, $days);

	}

}

