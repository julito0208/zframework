<?php //----------------------------------------------------------------------



class SessionHelper {


	const COOKIE_VARNAME = '__session__';
	const FIRST_TIME_VARNAME = '__ft__';
	
	protected static $_first_time = false;
	protected static $_initialized = false;
	protected static $_last_time = false;
	
	//----------------------------------------------------------------------
	
	
	public static function init( $session_id = null, $destroy = false ){
	
			
		If( SessionHelper::is_initialized() ) return false;
	
		if( !isset( $_COOKIE[ self::COOKIE_VARNAME ] ) || $destroy ) {
			
			self::$_first_time = true;
			setcookie( self::COOKIE_VARNAME, true, 0 );
			
		
		} else {
			
			self::$_first_time = false;
			
		}
		
		If( $session_id != null ) session_id( $session_id );
		
		session_start();
		
		if( $destroy ) session_unset();
		
		self::$_last_time = time();
		self::$_initialized = true;
		
		if(self::$_first_time) {
			self::add_var(self::FIRST_TIME_VARNAME, time());
		}
	}
	
	
	public static function get_id() { return session_id(); }
	
	
	public static function is_initialized() {
		
		return self::$_initialized;
	}
	
	
	
	public static function is_firsttime() {
		
		
		return (boolean) self::$_first_time;
	}
	 
	
	
	public static function get_firsttime() { return $_SESSION[ self::FIRST_TIME_VARNAME ]; }
		
		
	public static function get_lasttime() { return self::$_last_time; }
		
	//----------------------------------------------------------------
		
	
	public static function add_var( $name, $value = false ) { 
		@ $_SESSION[ $name ] = serialize($value);
	}
		
	
	public static function remove_var( $name ) { 
		unset( $_SESSION[ $name ] ); 
	}
	
	
	public static function get_var($name, $delete = false) {
		
		$var = unserialize( $_SESSION[ $name ] );
		
		If( $delete ) SessionHelper::remove_var( $name );
		
		return $var;
	}
	
	
	public static function has_var($name) {
		return array_key_exists($name, (array) $_SESSION);
	}

	
	
	
}
