<?php

class HTMLInputGmapControl extends HTMLInputControl {
	
	const GMAPS_JS_URL = "http://maps.googleapis.com/maps/api/js?key=%s";
	
	const GMAP_TYPE_HYBRID = 'HYBRID';
	const GMAP_TYPE_ROADMAP = 'ROADMAP';
	const GMAP_TYPE_SATELLITE = 'SATELLITE';
	const GMAP_TYPE_TERRAIN = 'TERRAIN';

	protected static $_DEFAULT_VARNAME = 'oGMap';
	protected static $_DEFAULT_NAME = 'gmap';
	protected static $_INCLUDE_JS = true;
	
	/*---------------------------------------------------------------------*/
	
	protected $_varname;
	protected $_init_pos;
	protected $_markers = array();
	protected $_width;
	protected $_height;
	protected $_navigation_control = true;
	protected $_scale_control = true;
	protected $_scroll_wheel = false;
	protected $_double_click_zoom = true;
	protected $_street_view_control = false;
	protected $_default_marker_key;
	protected $_map_type = self::GMAP_TYPE_ROADMAP;
	protected $_polygons = array();
	protected $_enable_change_type = false;
	protected $_auto_center_marker = false;
	protected $_listen_map_pos = false;
	protected $_load_on_ready = false;
	
	public function __construct($init_pos = null, $varname = null, $name = null) {
		
		parent::__construct();

		HTMLControl::add_global_js_files_zframework('/zframework/static/js/controls/gmapcontrol.js');
		HTMLControl::add_global_css_files_zframework('/zframework/static/css/controls/gmapcontrol.css');
		HTMLControl::add_global_static_library(self::STATIC_LIBRARY_FOLLOW_MOUSE_TITLE);

		$gmap_js = sprintf(self::GMAPS_JS_URL, ZPHP::get_config('gmap.key'));
		self::add_global_js_files($gmap_js);
		
		$this->set_init_pos($init_pos ? $init_pos : GMapPos::get_default_pos());
		$this->set_varname($varname ? $varname : (self::$_DEFAULT_VARNAME.uniqid()));
		$this->set_name($name ? $name : self::$_DEFAULT_NAME);
	}
	
	
	public function add_marker_with_key($key, GMapPoint $pos, $draggable=false, $icon=null, $float_html=null, $cursor=null, $onclick=null, $float_html_classname='') {
		
		if(is_null($key) || $key === true) {
			
			$is_default = $key === true;
			
			$key = count($this->_markers);
			
			if($is_default) $this->set_default_marker_key($key);
			
		}
		
		$this->_markers[$key] = array('position' => array('lat' => $pos->get_gmap_lat(), 'lng' => $pos->get_gmap_lng(), 'zoom' => $pos->get_gmap_zoom()), 'draggable' => $draggable, 'icon' => $icon, 'float_html' => (string) $float_html, 'cursor' => $cursor, 'onclick' => $onclick, 'float_html_classname' => $float_html_classname);
		
	}
	
	
	
	public function add_marker(GMapPoint $pos, $draggable=false, $icon=null, $float_html=null, $cursor=null, $onclick=null) {
		return $this->add_marker_with_key(null, $pos, $draggable, $icon, $float_html, $cursor, $onclick);
	}

	
	public function add_polygon(array $points, $options=array()) {
		
		if(count($points) < 3) return;
		
		$polygon_points = array();
		
		foreach($points as $point) {
			
			if(is_object($point) && $point instanceof GMapPoint) {
				
				$polygon_point = array();
				$polygon_point['lat'] = $point->get_gmap_lat();
				$polygon_point['lng'] = $point->get_gmap_lng();
				$polygon_points[] = $polygon_point;
				
			} else if(is_array($point)) {
				
				$polygon_point = array();
				$polygon_point['lat'] = $point['lat'];
				$polygon_point['lng'] = $point['lng'];
				$polygon_points[] = $polygon_point;
				
			}
			
		}
		
		
		if(count($polygon_points) < 3) return;
		
		$this->_polygons[] = array('points' => $polygon_points, 'options' => $options);
		
	}
	
	
	public function set_default_marker_key($key) {
		$this->_default_marker_key = $key;
	}

	/* @return GMapPoint */
	public function get_init_pos() {
		return $this->_init_pos;
	}

	public function set_init_pos(GMapPoint $pos) {
		$this->_init_pos = $pos;
		return $this;
	}

	
	public function get_varname() {
		return $this->_varname;
	}

	public function set_varname($value) {
		$this->_varname = $value;
		return $this;
	}

	public function get_width() {
		return $this->_width;
	}

	public function set_width($value) {
		$this->_width = $value;
		return $this;
	}

	public function get_height() {
		return $this->_height;
	}

	public function set_height($value) {
		$this->_height = $value;
		return $this;
	}

	public function get_navigation_control() {
		return $this->_navigation_control;
	}

	public function set_navigation_control($value) {
		$this->_navigation_control = $value;
		return $this;
	}

	public function get_scale_control() {
		return $this->_scale_control;
	}

	public function set_scale_control($value) {
		$this->_scale_control = $value;
		return $this;
	}

	public function get_scroll_wheel() {
		return $this->_scroll_wheel;
	}

	public function set_scroll_wheel($value) {
		$this->_scroll_wheel = $value;
		return $this;
	}

	public function get_double_click_zoom() {
		return $this->_double_click_zoom;
	}

	public function set_double_click_zoom($value) {
		$this->_double_click_zoom = $value;
		return $this;
	}

	public function get_street_view_control() {
		return $this->_street_view_control;
	}

	public function set_street_view_control($value) {
		$this->_street_view_control = $value;
		return $this;
	}


	public function get_map_type() {
		return $this->_map_type;
	}

	public function set_map_type($value) {
		$this->_map_type = strtoupper($value);
		return $this;
	}

	public function get_enable_change_type() {
		return $this->_enable_change_type;
	}

	public function set_enable_change_type($value) {
		$this->_enable_change_type = $value;
		return $this;
	}

	
	public function get_auto_center_marker() {
		return $this->_auto_center_marker;
	}

	public function set_auto_center_marker($value) {
		$this->_auto_center_marker = $value;
		return $this;
	}


	public function get_markers_count() {
		return count($this->_markers);
	}

	
	public function set_value($pos) {
		if($pos && is_object($pos) && $pos instanceof GMapPoint) {
			$this->set_init_pos($pos);
		}
	}
	
	public function get_value() {
		return $this->get_init_pos();
	}

	
	public function get_listen_map_pos() {
		return $this->_listen_map_pos;
	}

	public function set_listen_map_pos($value) {
		$this->_listen_map_pos = $value;
		return $this;
	}

	public function get_load_on_ready() 
	{
		return $this->_load_on_ready;
	}

	public function set_load_on_ready($value)

	{
		$this->_load_on_ready = $value;
	}

	public function prepare_params() {

		parent::prepare_params();
		
		$mapOptions = array();
		$mapOptions['navigationControl'] = (bool) $this->_navigation_control;
		$mapOptions['scaleControl'] = (bool) $this->_scale_control;
		$mapOptions['scrollwheel'] = (bool) $this->_scroll_wheel;
		$mapOptions['disableDoubleClickZoom'] = !((bool) $this->_double_click_zoom);
		$mapOptions['streetViewControl'] = (bool) $this->_street_view_control;
		$mapOptions['initialPosition'] = array('lat' => $this->_init_pos->get_gmap_lat(), 'lng' => $this->_init_pos->get_gmap_lng(), 'zoom' => $this->_init_pos->get_zoom());
		$mapOptions['mapType'] = $this->_map_type;
		
		$this->set_param('varname', HTMLHelper::escape($this->_varname));
		$this->set_param('init_pos', $this->_init_pos);
		$this->set_param('markers', $this->_markers);
		$this->set_param('width', $this->_width);
		$this->set_param('height', $this->_height);
		$this->set_param('default_marker_key', in_array($this->_default_marker_key, $this->_markers) ? $this->_default_marker_key : null);
		$this->set_param('map_type', $this->_map_type);
		$this->set_param('polygons', $this->_polygons);
		$this->set_param('enable_change_type', $this->_enable_change_type);
		$this->set_param('map_options', $mapOptions);
		$this->set_param('auto_center_marker', $this->_auto_center_marker);
		$this->set_param('listen_map_pos', $this->_listen_map_pos);
		$this->set_param('load_on_ready', $this->_load_on_ready);
		
		$this->set_param('include_js', self::$_INCLUDE_JS);
		
		self::$_INCLUDE_JS = false;
		
	}
	
}