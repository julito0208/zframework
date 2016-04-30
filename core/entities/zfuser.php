<?php

class ZfUser extends ZfUserCache
{

	/* ZPHP Generated Code ------------------------------------------ */
	/* /ZPHP Generated Code ------------------------------------------ */

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


}

