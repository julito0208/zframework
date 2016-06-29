<?php

class GMapPos implements GMapPoint {

	protected static $_default_pos_lat = -40.160317;
	protected static $_default_pos_lng = -60.470703;
	protected static $_default_pos_zoom = 5;
	
	/* @return GMapPos */
	public static function get_default_pos() {
		return new GMapPos(self::$_default_pos_lat, self::$_default_pos_lng, self::$_default_pos_zoom);
	}

	/**
	*
	* @return GMapPos
	*
	*/
	public static function parse_pos($str)
	{
		if(preg_match('#^(?i)(.*\@)?(?P<lat>\-?.+?)\,(?P<lng>\-?.+?)\,(?P<zoom>\d+)(z)?(\?.*?)?$#', $str, $match))
		{
			return new GMapPos($match['lat'], $match['lng'], $match['zoom']);
		}
		else
		{
			return new GMapPos();
		}

	}
	
	
	/*--------------------------------------------------------*/
	
	protected $_zoom;
	protected $_lng;
	protected $_lat;

	/*--------------------------------------------------------*/
	
	public function __construct($data_lat=null, $lng=null, $zoom=null) {
		
		if($data_lat && is_array($data_lat)) {
			
			$this->update_fields($data_lat);
			
		} else {
			
			$this->set_lat($data_lat);
			$this->set_lng($lng);
			$this->set_zoom($zoom);
			
		}
		
	}
	
	
	

	public function __get($name) {

		switch($name) {
			case "lat": return $this->get_lat(); break;
			case "lng": return $this->get_lng(); break;
			case "zoom": return $this->get_zoom(); break;
			default: return null; break;
		}

	}

	public function __set($name, $value) {

		switch($name) {
			case "lat": $this->set_lat($value); break;
			case "lng": $this->set_lng($value); break;
			case "zoom": $this->set_zoom($value); break;
		}

	}
	

	public function __toArray() {
		return array(
			'lat' => $this->get_lat(),
			'lng' => $this->get_lng(),
			'zoom' => $this->get_zoom(),
		);
	}
	
	/*--------------------------------------------------------*/

	
	public function update_fields(array $data) {

		foreach((array) $data as $name => $value) {
			$this->$name = $value;
		}

	}
	
	public function get_zoom() {
		return $this->_zoom;
	}

	public function set_zoom($value) {
		$this->_zoom = (integer) $value;
		return $this;
	}

	public function get_lng() {
		return $this->_lng;
	}

	public function set_lng($value) {
		$this->_lng = (float) $value;
		return $this;
	}

	public function get_lat() {
		return $this->_lat;
	}

	public function set_lat($value) {
		$this->_lat = (float) $value;
		return $this;
	}

	/*--------------------------------------------------------*/
	
	public function get_gmap_lat() {
		return $this->get_lat();
	}

	public function get_gmap_lng() {
		return $this->get_lng();
	}

	public function get_gmap_zoom() {
		return $this->get_zoom();
	}
}