<?php //-----------------------------------------------------------------------


class HTMLInputStarsControl extends HTMLInputControl {

	const DEFAULT_STARS = 5;
	const DEFAULT_DECIMAL = false;

	protected $_stars;
	protected $_decimal;

	public function __construct($id=null, $name=null, $value=null) {
		parent::__construct();
		$this->set_id($id);
		$this->set_name($name);
		$this->set_value($value);
		self::add_global_static_library(self::STATIC_LIBRARY_FONTS_AWESOME);
		$this->set_decimal(self::DEFAULT_DECIMAL);
		$this->set_stars(self::DEFAULT_STARS);
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_stars($value)
	{
		$this->_stars = $value;
		return $this;
	}

	public function get_stars()
	{
		return $this->_stars;
	}

	/**
	*
	* @return $this
	*
	*/
	public function set_decimal($value)
	{
		$this->_decimal = $value;
		return $this;
	}

	public function get_decimal()
	{
		return $this->_decimal;
	}



	public function prepare_params() {

		parent::prepare_params();

		$this->set_param('stars', $this->_stars);
		$this->set_param('decimal', $this->_decimal);
	}

}


//----------------------------------------------------------------------- ?>