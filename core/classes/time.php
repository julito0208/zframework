<?php

class Time {
	
	const FORMAT_SQL_TIME = '%H:%M';
	const FORMAT_DEFAULT_TIME = '%H:%M';
	const FORMAT_SQL_TIME_SECS = '%H:%M:%S';
	const FORMAT_DEFAULT_TIME_SECS = '%H:%M:%S';
	
	const DEFAULT_FORMAT = self::FORMAT_DEFAULT_TIME;

	/*-----------------------------------------------------------*/
	
	protected static $_sql_pattern = '#^\s*(?P<hour>\d+)\s*(?:\.|\-|\:)\s*(?P<minutes>\d+)\s*(?:\s*(?:\.|\-|\:)\s*(?P<seconds>\d+))?\s*$#';
	
	/*-----------------------------------------------------------*/
	
	
	protected static function _set_time_value(Time $date, $value) {
		
		
		if(!$value) {
			
			$current_datetime = Date::now();
			
			$date->set_hour($current_datetime->get_hour(), false);
			$date->set_minutes($current_datetime->get_minutes(), false);
			$date->set_seconds($current_datetime->get_seconds(), false);
			
		} else if(is_object($value) && CastHelper::is_instance_of($value, 'Time')) {
			
			$date->set_hour($value->_hour, false);
			$date->set_minutes($value->_min, false);
			$date->set_seconds($value->_secs, false);
			
		} else {

			$value = trim($value);
			
			if(is_array($value)) {
				
				if(array_keys($value, 'hour')) $date->set_hour ($value['hour'], false);
				if(array_keys($value, 'minutes')) $date->set_minutes ($value['minutes'], false);
				if(array_keys($value, 'seconds')) $date->set_seconds ($value['seconds'], false);
				
				
			} else if(preg_match(self::$_sql_pattern, $value, $match)) {
				
				$date->set_hour($match['hour'], false);
				$date->set_minutes($match['minutes'], false);

				if(isset($match['seconds'])) {

					$date->set_seconds($match['seconds'], false);

				}
				
				
			} else {
				
				$date->set_hour($value, false);
				
			} 
			
		}

		$date->validate();
		
	}
	
	
	
	protected static function _compare(Time $date1, Time $date2, $use_secs=true) {
		
		if($date1->get_hour() > $date2->get_hour()) return 1;
							
		else if($date1->get_hour() < $date2->get_hour()) return -1;

		else {

			if($date1->get_minutes() > $date2->get_minutes()) return 1;

			else if($date1->get_minutes() < $date2->get_minutes()) return -1;

			else if(!$use_secs) return 0;

			else {

				if($date1->get_seconds() > $date2->get_seconds()) return 1;

				else if($date1->get_seconds() < $date2->get_seconds()) return -1;

				else return 0;

			}
			
		}

			
	}	
		
	/*-----------------------------------------------------------*/
	
	
	
	/* @return Time */
	public static function now() {
		$time = time();
		return new Time(strftime('%H', $time),strftime('%M', $time),strftime('%S', $time));			
	}
	
	
	/* @return Time */
	public static function current_time() {
		return self::now();
	}
	
	
	
	/* @return Time */
	public static function parse($value=null) {
		
		if(!$value) {
			
			return self::now ();
			
		} else if(is_object($value) && CastHelper::is_instance_of($value, 'Time')) {
			
			return $value;
			
		} else {
			
			$date = new Time();
			self::_set_time_value($date, $value);
			return $date;
			
		}

	}
	
	
	
	
	public static function parse_sql_datetime($value=null) {

		$date = self::parse($value);
		return $date->format_sql_time();
		
	}
	
	
	
	
	public static function time_format($value) {
		
		$value = trim($value);
			
		if(preg_match(self::$_sql_pattern, $value, $match)) {
			
			return true;

		} 
		
		
		return false;
		
	}
	
	
	/*-----------------------------------------------------------*/
	
	
	protected $_hour;
	protected $_min;
	protected $_secs;
	protected $_format;
	
	
	/*-----------------------------------------------------------*/
	
	public function __construct($hour_value=null, $min=null, $secs=null) {
		
		if($hour_value) {
			
			self::_set_time_value($this, $hour_value);

		} else {
		
			$now = self::now();
			$this->set_hour($now->get_hour(), false);
			$min = $now->get_minutes();
			$secs = $now->get_seconds();		
			
		}
		
		if(!is_null($min)) $this->set_minutes ($min, false);
		if(!is_null($secs)) $this->set_seconds ($secs, false);
		
		if(is_null($this->_secs)){
			$this->set_format(self::FORMAT_DEFAULT_TIME);
		} else {
			$this->set_format(self::FORMAT_DEFAULT_TIME_SECS);
		}
		
		if(func_num_args() > 0) $this->validate();
	}
	
	
	public function __toArray() {
		$array = array();
		$array['hour'] = $this->get_hour();
		$array['min'] = $this->get_min();
		$array['secs'] = $this->get_secs();
		return $array;
	}
	
	
	public function __toString() {
		return $this->to_string($this->get_format());
	}

	
	public function __get($name) {
		switch(strtolower($name)) {
			case 'hour': case 'hours': return $this->get_hour(); break;
			case 'min': case 'minutes': return $this->get_minutes(); break;
			case 'seconds': case 'secs': return $this->get_seconds(); break;
		}
	}
	
	
	public function __set($name, $value) {
		switch(strtolower($name)) {
			case 'hour': case 'hours': $this->set_hour($value); break;
			case 'min': case 'minutes': $this->set_minutes($value); break;
			case 'seconds': case 'secs': $this->set_seconds($value); break;
		}
	}
	

	protected function _format_replace_callback($match) {
		
		switch($match['letter']) {
			
		
			
			case 'H': 
				return str_pad($this->get_hour(), 2, '0', STR_PAD_LEFT); 
			break;
			
			case 'M': 
				return str_pad($this->get_minutes(), 2, '0', STR_PAD_LEFT); 
			break;
			
			case 'S': 
				return str_pad($this->get_seconds(), 2, '0', STR_PAD_LEFT); 
			break;
			
			case 'A': 
				return $this->get_hour() >= 12 ? 'pm' : 'am'; 
			break;
			
			case 'K': 
				
				$hour = $this->get_hour();
				
				if($hour == 0) {
					
					$hour = 12;
					
				} else if($hour > 12) {
					
					$hour = $hour - 12;
					
				}
				
				return str_pad($hour, 2, '0', STR_PAD_LEFT); 
				
			break;
			
			
			default: return ''; break;
		}
	}
	
	/*-----------------------------------------------------------*/

	
	protected function _control_hours() {

		if(is_null($this->_hour)) return;
		
		while($this->_hour >= 24) {
			
			$this->_hour -= 24;
			
		}
		
		while($this->_hour < 0) {
			
			$this->_hour += 24;
		}


		
	}
	
	
	protected function _control_minutes($control_hours=true) {

		if(is_null($this->_min)) return;
		
		while($this->_min >= 60) {
			
			$this->_min -= 60;
			$this->_hour += 1;
			
		}
		
		while($this->_min < 0) {
			
			$this->_min += 60;
			$this->_hour -= 1;
		}
		
		if($control_hours) $this->_control_hours (true);
		
	}
	
	
	protected function _control_secs($control_minutes=true) {

		if(is_null($this->_secs)) return;
		
		
		while($this->_secs >= 60) {
			
			$this->_secs -= 60;
			$this->_min += 1;
			
		}
		
		while($this->_secs < 0) {
			
			$this->_secs += 60;
			$this->_min -= 1;
		}
		
		
		if($control_minutes) $this->_control_minutes (true);
		
	}
	
	
	/*-----------------------------------------------------------*/
	
	
	public function validate() {
		
		if(!is_null($this->_hour) && !is_null($this->_min)) {
			$this->_control_secs(false);
			$this->_control_minutes(false);
			$this->_control_hours(false);
		}
		
	}
	
	
	/*-----------------------------------------------------------*/
	
	
	public function get_seconds() {
		return (int) $this->_secs;
	}

	/* @return Time */
	public function set_seconds($value, $validate=true) {
		$this->_secs = $value;
		if($validate) $this->_control_secs ();
		return $this;
	}

	public function get_minutes() {
		return (int) $this->_min;
	}

	/* @return Time */
	public function set_minutes($value, $validate=true) {
		$this->_min = $value;
		if($validate) $this->_control_minutes();
		return $this;
	}
	

	public function get_hour() {
		return (int)  $this->_hour;
	}

	/* @return Time */
	public function set_hour($value, $validate=true) {
		$this->_hour = $value;
		if($validate) $this->_control_hours();
		return $this;
	}

	/*-----------------------------------------------------------*/

	/* @return Time */
	public function set_time($hour=null, $min=null, $secs=null, $validate=true) {
		
		if(!is_null($hour)) $this->set_hour ($hour, false);
		if(!is_null($min)) $this->set_minutes ($min, false);
		if(!is_null($secs)) $this->set_seconds ($secs, false);
		
		if($validate) $this->validate ();
		return $this;
	}
	
	
	/*-----------------------------------------------------------*/

	
	public function get_format() {
		return $this->_format;
	}

	
	/* @return Date */
	public function set_format($value) {
		$this->_format = $value;
		return $this;
	}

	/*-----------------------------------------------------------*/
	
	public function get_total_minutes() {
		
		return ($this->get_hour() * 60) + (is_null($this->_min) ? 0 : $this->get_minutes());
		
	}
	
	
	public function get_total_seconds() {
		
		return ($this->get_total_minutes() * 60) + (is_null($this->_secs) ? 0 : $this->get_seconds());
		
	}
	

	/*-----------------------------------------------------------*/
	
	
	public function add_hours($hours) {
		
		$this->_hour += $hours;
		$this->_control_hours();

		return $this;
	}
	
	
	public function add_minutes($minutes) {
		
		$this->_min += $minutes;
		$this->_control_minutes();

		return $this;
	}
	
	
	public function add_secs($secs) {
		
		$this->_secs += $secs;
		$this->_control_secs();

		return $this;
	}
	
	
	
	/*-----------------------------------------------------------*/
	
	public function diff_hours($date=null) {

		$date = self::parse($date);
		
		return $date->get_hour() - $this->get_hour();
		
	}
	
	
	public function diff_minutes($date=null) {

		$date = self::parse($date);
		
		return $date->get_total_minutes() - $this->get_total_minutes();
		
	}
	
	
	public function diff_seconds($date=null) {

		$date = self::parse($date);
		
		return $date->get_total_seconds() - $this->get_total_seconds();
		
	}
	
	/*-----------------------------------------------------------*/
	
	public function compare_to($date=null, $use_secs=null) {
		$date = self::parse($date);
		if(is_null($use_secs)) $use_secs = !is_null($this->_secs) && !is_null($date->_secs);
		return self::_compare($this, $date, $use_secs);
	}
	
	
	public function is_less_than($date=null, $use_secs=null) {
		$date = self::parse($date);
		if(is_null($use_secs)) $use_secs = !is_null($this->_secs) && !is_null($date->_secs);
		return $this->compare_to($date, $use_secs) == -1;
		
	}
	
	
	public function is_greater_than($date=null, $use_secs=null) {
		$date = self::parse($date);
		if(is_null($use_secs)) $use_secs = !is_null($this->_secs) && !is_null($date->_secs);
		return $this->compare_to($date, $use_secs) == 1;
		
	}
	
	
	public function is_less_or_equal_than($date=null, $use_secs=null) {
		$date = self::parse($date);
		if(is_null($use_secs)) $use_secs = !is_null($this->_secs) && !is_null($date->_secs);
		return $this->compare_to($date, $use_secs) <= 0;
		
	}
	
	
	public function is_greater_or_equal_than($date=null, $use_secs=null) {
		$date = self::parse($date);
		if(is_null($use_secs)) $use_secs = !is_null($this->_secs) && !is_null($date->_secs);
		return $this->compare_to($date, $use_secs) >= 0;
		
	}
	
	
	public function is_equal_to($date=null, $use_secs=null) {
		$date = self::parse($date);
		if(is_null($use_secs)) $use_secs = !is_null($this->_secs) && !is_null($date->_secs);
		return $this->compare_to($date, $use_secs) == 0;
		
	}
	
	
	/*-----------------------------------------------------------*/
	
	
	public function to_string($format=null) {
		if(!$format) $format = $this->get_format();
		return preg_replace_callback('#(?!=\%)\%(?P<letter>[A-Za-z])#', array($this, '_format_replace_callback'), $format);
	}
	
	
	public function format($format=null) {
		return $this->to_string($format);
	}

	
	public function format_sql_datetime() {
		return $this->to_string(self::FORMAT_SQL_TIME_SECS);
	}
	
	
	
	/*-----------------------------------------------------------*/
	
	public function copy() {
		
		$date = new Time();

		$date->set_hour($this->_hour, false);
		$date->set_minutes($this->_min, false);
		$date->set_seconds($this->_secs, false);
		
		return $date;
	}
	
}