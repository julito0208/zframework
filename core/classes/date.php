<?php


/*
Formats:
	%Y:				full year (4-digit)
	%y:				year (2-digit)
	%d:				day (2-digit)
	%m:				month (2-digit)
	%H:				hour (2-digit)
	%M:				minutes (2-digit)
	%S:				seconds (2-digit)
	%A				am - pm
	%K				hour in 12 hour mode
	%r				unix time
	%O				month name (long)
	%o				month name (short)
	%W				day of the week (number)
	%P				name of the day of the week (long)
	%p				name of the day of the week (short)
	%T				literal day (hoy, ayer, etc)
  
 */

class Date {
	
	
	const FORMAT_SQL_DATE = '%Y-%m-%d';
	const FORMAT_SQL_DATETIME = '%Y-%m-%d %H:%M';
	const FORMAT_SQL_DATETIME_SECS = '%Y-%m-%d %H:%M:%S';
	const FORMAT_DEFAULT_DATE = '%d/%m/%Y';
	const FORMAT_DEFAULT_DATETIME = '%d/%m/%Y %H:%M';
	const FORMAT_DEFAULT_DATETIME_SECS = '%d/%m/%Y %H:%M:%S';
	const FORMAT_DATE_LITERAL = '%T';
	const FORMAT_DATE_LITERAL_TIME = '%T - %H:%M hs';
	const FORMAT_DATE_LITERAL_TIME_SECS = '%T - %H:%M:%S hs';
	const FORMAT_JSON = '%Y-%m-%d %H:%M:%S';
	
	const DEFAULT_FORMAT = self::FORMAT_DEFAULT_DATETIME_SECS;

	/*-----------------------------------------------------------*/

	protected static $_language_text_prefix = 'date_';
	
	protected static $_months_names_long = array(
		'month_name_long_jan',
		'month_name_long_feb',
		'month_name_long_mar',
		'month_name_long_apr',
		'month_name_long_may',
		'month_name_long_jun',
		'month_name_long_jul',
		'month_name_long_aug',
		'month_name_long_sep',
		'month_name_long_oct',
		'month_name_long_nov',
		'month_name_long_dec',
	);
	
	protected static $_months_names_short = array(
		'month_name_short_jan',
		'month_name_short_feb',
		'month_name_short_mar',
		'month_name_short_apr',
		'month_name_short_may',
		'month_name_short_jun',
		'month_name_short_jul',
		'month_name_short_aug',
		'month_name_short_sep',
		'month_name_short_oct',
		'month_name_short_nov',
		'month_name_short_dec',
	);

	protected static $_days_names_long = array(
		'day_name_long_mon',
		'day_name_long_tue',
		'day_name_long_wed',
		'day_name_long_thu',
		'day_name_long_fri',
		'day_name_long_sat',
		'day_name_long_sun',
	);
	
	protected static $_days_names_short = array(
		'day_name_short_mon',
		'day_name_short_tue',
		'day_name_short_wed',
		'day_name_short_thu',
		'day_name_short_fri',
		'day_name_short_sat',
		'day_name_short_sun',
	);

	protected static $_days_diff_labels = array(
		'yesterday',
		'today',
		'tomorrow',
	);

	protected static $_days_diff_default_format = '%d/%m/%Y';
	
	protected static $_sql_pattern = '#^\s*(?P<year>\d+)\s*(?:\.|\-|\:)\s*(?P<month>\d+)\s*(?:\.|\-|\:)\s*(?P<day>\d+)(?:\s+(?P<hour>\d+)\s*(?:\.|\-|\:)\s*(?P<minutes>\d+)\s*(?:\s*(?:\.|\-|\:)\s*(?P<seconds>\d+))?)?\s*$#';
	
	protected static $_default_pattern = '#^\s*(?P<day>\d+)\s*(?:\.|\-|\:|\/)\s*(?P<month>\d+)\s*(?:\.|\-|\:|\/)\s*(?P<year>\d+)(?:\s+(?P<hour>\d+)\s*(?:\.|\-|\:)\s*(?P<minutes>\d+)\s*(?:\s*(?:\.|\-|\:)\s*(?P<seconds>\d+))?)?\s*$#';


	/*-----------------------------------------------------------*/
	
	
	protected static function _set_date_value(Date $date, $value) {
		
		
		if(!$value) {
			
			$date->set_unix_time(time());
			
		} else if(is_object($value) && CastHelper::is_instance_of($value, 'Date')) {
			
			$date->set_year($value->_year, false);
			$date->set_month($value->_month, false);
			$date->set_day($value->_day, false);
			$date->set_hour($value->_hour, false);
			$date->set_minutes($value->_min, false);
			$date->set_seconds($value->_secs, false);
			
		} else {

			$value = trim($value);
			
			if(is_array($value)) {
				
				if(array_keys($value, 'year')) $date->set_year ($value['year'], false);
				if(array_keys($value, 'month')) $date->set_month ($value['month'], false);
				if(array_keys($value, 'day')) $date->set_day ($value['day'], false);
				if(array_keys($value, 'hour')) $date->set_hour ($value['hour'], false);
				if(array_keys($value, 'minutes')) $date->set_minutes ($value['minutes'], false);
				if(array_keys($value, 'seconds')) $date->set_seconds ($value['seconds'], false);
				
			} else if(preg_match('#^\d{4}$#', $value)) {
				
				$date->set_year($value, false);
				
			} else if(preg_match('#^\d{5,}$#', $value)) {	
				
				$date->set_unix_time($value);
				
			} else if(preg_match(self::$_sql_pattern, $value, $match)) {
				
				$date->set_year($match['year'], false);
				$date->set_month($match['month'], false);
				$date->set_day($match['day'], false);
				
				if(isset($match['hour'])) {
					
					$date->set_hour($match['hour'], false);
					$date->set_minutes($match['minutes'], false);
					
					if(isset($match['seconds'])) {
					
						$date->set_seconds($match['seconds'], false);
						
					}
				}
				
			} else if(preg_match(self::$_default_pattern, $value, $match)) {
				
				$date->set_year($match['year'], false);
				$date->set_month($match['month'], false);
				$date->set_day($match['day'], false);
				
				if(isset($match['hour'])) {
					
					$date->set_hour($match['hour'], false);
					$date->set_minutes($match['minutes'], false);
					
					if(isset($match['seconds'])) {
					
						$date->set_seconds($match['seconds'], false);
						
					}
				}
				
			}
			
		}

		$date->validate();
		
	}
	
	
	
	protected static function _compare(Date $date1, Date $date2, $use_time=true, $use_secs=true) {
		
		if($date1->get_year() > $date2->get_year()) return 1;
			
		else if($date1->get_year() < $date2->get_year()) return -1;
			
		else {
			
			if($date1->get_month() > $date2->get_month()) return 1;
			
			else if($date1->get_month() < $date2->get_month()) return -1;
				
			else {
				
				if($date1->get_day() > $date2->get_day()) return 1;
					
				else if($date1->get_day() < $date2->get_day()) return -1;
					
				else if(!$use_time) return 0;
						
				else {
						
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
				
			}
			
		}
								
	}	
		
	/*-----------------------------------------------------------*/
	
	
	public static function get_month_name_long($month) {
		
		$key = self::$_language_text_prefix.self::$_months_names_long[$month-1];
		
		return LanguageHelper::get_text($key);
	}
	
	
	public static function get_month_name_short($month) {
		
		$key = self::$_language_text_prefix.self::$_months_names_short[$month-1];
		
		return LanguageHelper::get_text($key);
	}
	
	
	public static function get_day_name_long($day_of_week) {
		
		$key = self::$_language_text_prefix.self::$_days_names_long[$day_of_week-1];
		
		return LanguageHelper::get_text($key);
	}
	
	
	public static function get_day_name_short($day_of_week) {
		
		$key = self::$_language_text_prefix.self::$_days_names_short[$day_of_week-1];
		
		return LanguageHelper::get_text($key);
	}
	
	
	public static function count_month_days($year, $month) {
		
		if($month == 2) return $year % 4 == 0 ? 29 : 28;
		else return in_array($month, array(1,3,5,7,8,10,12)) ? 31 : 30;
	}
	
	
	public static function count_year_days($year) {
		
		return $year % 4 == 0 ? 366 : 365;
	}
	
	
	/* @return Date */
	public static function now() {
		$time = time();
		return new Date(strftime('%Y', $time),strftime('%m', $time),strftime('%d', $time),strftime('%H', $time),strftime('%M', $time),strftime('%S', $time));			
	}
	
	
	/* @return Date */
	public static function current_datetime() {
		$time = time();
		return new Date(strftime('%Y', $time),strftime('%m', $time),strftime('%d', $time),strftime('%H', $time),strftime('%M', $time),strftime('%S', $time));			
	}
	
	
	/* @return Date */
	public static function current_date() {
		$time = time();
		return new Date(strftime('%Y', $time),strftime('%m', $time),strftime('%d', $time));			
	}
	
	
	/* @return Date */
	public static function parse($value=null) {
		
		if(!$value) {
			
			return self::now();
			
		} else if(is_object($value) && CastHelper::is_instance_of($value, 'Date')) {

			return $value->copy();
			
		} else {
			
			$date = new Date();
			self::_set_date_value($date, $value);
			return $date;
			
		}

	}
	
	
	
	public static function parse_to_unix_time($value=null) {

		$date = self::parse($value);
		return $date->get_unix_time();
		
	}
	
	
	public static function parse_sql_datetime($value=null) {

		$date = self::parse($value);
		return $date->format_sql_datetime();
		
	}
	
	
	public static function parse_sql_date($value=null) {

		$date = self::parse($value);
		return $date->format_sql_date();
		
	}
	
	
	public static function test_date_format($value) {
		
		$value = trim($value);
			
		if(preg_match('#^\d{5,}$#', $value)) {

			return true;

		} else if(preg_match(self::$_sql_pattern, $value, $match)) {
			
			return true;

		} else if(preg_match(self::$_default_pattern, $value, $match)) {
			
			return true;

		}
		
		
		return false;
		
	}
	
	
	/*-----------------------------------------------------------*/
	
	protected $_year;
	protected $_month;
	protected $_day;
	protected $_hour;
	protected $_min;
	protected $_secs;
	protected $_format;
	
	
	/*-----------------------------------------------------------*/
	
	public function __construct($year_value=null, $month=null, $day=null, $hour=null, $min=null, $secs=null) {
		
		if($year_value) {
			
			self::_set_date_value($this, $year_value);

		} else {
		
			$now = self::now();
			$this->set_year($now->get_year(), false);
			$month = $now->get_month();
			$day = $now->get_day();		
			
		}
		
		if(!is_null($month)) $this->set_month ($month, false);
		if(!is_null($day)) $this->set_day ($day, false);
		if(!is_null($hour)) $this->set_hour ($hour, false);
		if(!is_null($min)) $this->set_minutes ($min, false);
		if(!is_null($secs)) $this->set_seconds ($secs, false);
		
		if(is_null($this->_hour) && is_null($this->_min)) {
			$this->set_format(self::FORMAT_DEFAULT_DATE);
		} else if(is_null($this->_secs)){
			$this->set_format(self::FORMAT_DEFAULT_DATETIME);
		} else {
			$this->set_format(self::FORMAT_DEFAULT_DATETIME_SECS);
		}
		
		if(func_num_args() > 0) $this->validate();
	}
	
	
	public function __toArray() {
		$array = array();
		$array['year'] = $this->get_year();
		$array['month'] = $this->get_month();
		$array['day'] = $this->get_day();
		$array['hour'] = $this->get_hour();
		$array['min'] = $this->get_minutes();
		$array['secs'] = $this->get_seconds();
		return $array;
	}
	
	
	public function __toJSON() {
		return $this->format_json();
	}
	
	
	public function __toString() {
		return $this->to_string($this->get_format());
	}

	
	public function __get($name) {
		switch(strtolower($name)) {
			case 'year': return $this->get_year(); break;
			case 'month': return $this->get_month(); break;
			case 'day': return $this->get_day(); break;
			case 'hour': case 'hours': return $this->get_hour(); break;
			case 'min': case 'minutes': return $this->get_minutes(); break;
			case 'seconds': case 'secs': return $this->get_seconds(); break;
			case 'unix_time': case 'time': case 'unixtime': return $this->get_unix_time(); break;
			case 'count_days': case 'total_days': case 'count_total_days': return $this->get_total_days(); break;
			case 'day_week': case 'day_of_week': return $this->get_day_of_week(); break;
		}
	}
	
	
	public function __set($name, $value) {
		switch(strtolower($name)) {
			case 'year': $this->set_year($value); break;
			case 'month': $this->set_month($value); break;
			case 'day': $this->set_day($value); break;
			case 'hour': case 'hours': $this->set_hour($value); break;
			case 'min': case 'minutes': $this->set_minutes($value); break;
			case 'seconds': case 'secs': $this->set_seconds($value); break;
			case 'unix_time': case 'time': case 'unixtime': $this->set_unix_time($value); break;
		}
	}
	

	protected function _format_replace_callback($match) {
		
		switch($match['letter']) {
			
			case 'Y': 
				return $this->get_year(); 
			break;
			
			case 'y': 
				return substr((string) $this->get_year(), 2); 
			break;
		
			case 'm': 
				return str_pad($this->get_month(), 2, '0', STR_PAD_LEFT); 
			break;
		
			case 'd': 
				return str_pad($this->get_day(), 2, '0', STR_PAD_LEFT); 
			break;
			
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
			
			case 'r': 
				return mktime($this->get_hour(), $this->get_minutes(), $this->get_seconds(), $this->get_month(), $this->get_day(), $this->get_year()); 
			break;
		
			case 'O':
				return self::get_month_name_long($this->get_month());
			break;
		
			case 'o':
				return self::get_month_name_short($this->get_month());
			break;
		
			case 'W':
				return $this->get_day_of_week();
			break;
		
			case 'p':
				return self::get_day_name_short($this->get_day_of_week());
			break;
		
			case 'P':
				return self::get_day_name_long($this->get_day_of_week());
			break;
		
			case 'T':
				
				$days_diff = self::now()->diff_days($this);
				
				if(isset(self::$_days_diff_labels[$days_diff])) {
					$key = self::$_language_text_prefix.self::$_days_diff_labels[$days_diff];
					return LanguageHelper::get_text($key);
				} else {
					return $this->format(self::$_days_diff_default_format);
				}
				
			break;
			
			default: return ''; break;
		}
	}
	
	/*-----------------------------------------------------------*/

	protected function _control_days() {
		
		while($this->_day > $this->get_month_days()) {

			$this->_day -= $this->get_month_days();
			$this->_month += 1;

			if($this->_month > 12) {

				$this->_month = 1;
				$this->_year += 1;
			}

		}
		
		while($this->_day <= 0) {

			$this->_month -= 1;

			if($this->_month <= 0) {

				$this->_month = 12;
				$this->_year -= 1;
			}
			
			$this->_day += $this->get_month_days();
		}
		
	}
	
	
	protected function _control_months($control_days=true) {

		while($this->_month > 12) {
			
			$this->_month -= 12;
			$this->_year += 1;
			
		}
		
		while($this->_month <= 0) {
			
			$this->_month += 12;
			$this->_year -= 1;
		}

		
		if($control_days) $this->_control_days();
			
	}
	
	
	protected function _control_hours($control_days=true) {

		if(is_null($this->_hour)) return;
		
		while($this->_hour >= 24) {
			
			$this->_hour -= 24;
			$this->_day += 1;
			
		}
		
		while($this->_hour < 0) {
			
			$this->_hour += 24;
			$this->_day -= 1;
		}

		if($control_days) $this->_control_days();

		
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
		
		if(!is_null($this->_year) && !is_null($this->_month) && !is_null($this->_day)) {
			$this->_control_secs(false);
			$this->_control_minutes(false);
			$this->_control_hours(false);
			$this->_control_months(false);
			$this->_control_days();
			
		}
		
	}
	
	
	/*-----------------------------------------------------------*/
	
	
	public function get_seconds() {
		return (int) $this->_secs;
	}

	/* @return Date */
	public function set_seconds($value, $validate=true) {
		$this->_secs = $value;
		if($validate) $this->_control_secs ();
		return $this;
	}

	public function get_minutes() {
		return (int) $this->_min;
	}

	/* @return Date */
	public function set_minutes($value, $validate=true) {
		$this->_min = $value;
		if($validate) $this->_control_minutes();
		return $this;
	}
	

	public function get_hour() {
		return (int)  $this->_hour;
	}

	/* @return Date */
	public function set_hour($value, $validate=true) {
		$this->_hour = $value;
		if($validate) $this->_control_hours();
		return $this;
	}


	public function get_day() {
		return (int) $this->_day;
	}

	/* @return Date */
	public function set_day($value, $validate=true) {
		$this->_day = $value;
		if($validate) $this->_control_days();
		return $this;
	}


	public function get_month() {
		return (int) $this->_month;
	}

	/* @return Date */
	public function set_month($value, $validate=true) {
		$this->_month = $value;
		if($validate) $this->_control_months();
		return $this;
	}


	public function get_year() {
		return (int) $this->_year;
	}

	/* @return Date */
	public function set_year($value, $validate=true) {
		$this->_year = $value;
		if($validate) $this->_control_months();
		return $this;
	}
	
	
	/*-----------------------------------------------------------*/
	
	/* @return Date */
	public function set_date($year=null, $month=null, $day=null, $validate=true) {
		
		if(!is_null($year)) $this->set_year ($year);
		if(!is_null($month)) $this->set_month ($month, false);
		if(!is_null($day)) $this->set_day ($day, false);
		
		if($validate) $this->validate ();
		return $this;
	}
	
	/* @return Date */
	public function set_time($hour=null, $min=null, $secs=null, $validate=true) {
		
		if(!is_null($hour)) $this->set_hour ($hour, false);
		if(!is_null($min)) $this->set_minutes ($min, false);
		if(!is_null($secs)) $this->set_seconds ($secs, false);
		
		if($validate) $this->validate ();
		return $this;
	}
	
	
	/* @return Date */
	public function set_datetime($year=null, $month=null, $day=null, $hour=null, $min=null, $secs=null, $validate=true) {
		
		if(!is_null($year)) $this->set_year ($year);
		if(!is_null($month)) $this->set_month ($month, false);
		if(!is_null($day)) $this->set_day ($day, false);
		if(!is_null($hour)) $this->set_hour ($hour, false);
		if(!is_null($min)) $this->set_minutes ($min, false);
		if(!is_null($secs)) $this->set_seconds ($secs, false);
				
		if($validate) $this->validate ();
		return $this;
	}
	
	
	
	/*-----------------------------------------------------------*/
	
	/* @return Time */
	public function get_time() {
		return new Time($this->get_hour(), $this->get_minutes(), $this->get_seconds());
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

	
	public function get_unix_time() {
		return $this->format('%r');
	}

	
	/* @return Date */
	public function set_unix_time($time=null) {
		
		if(!$time) $time = time();
		
		$this->set_year(strftime('%Y', $time), false);
		$this->set_month(strftime('%m', $time), false);
		$this->set_day(strftime('%d', $time), false);
		$this->set_hour(strftime('%H', $time), false);
		$this->set_minutes(strftime('%M', $time), false);
		$this->set_seconds(strftime('%S', $time), false);
	}

	/*-----------------------------------------------------------*/
	
	public function get_total_months() {
		
		return ($this->get_year() * 12) + $this->get_month();
	}
	
	
	public function get_total_days() {
		
		$days = 0;
		
		for($year=1; $year<$this->get_year(); $year++)
			$days+= self::count_year_days($year);
		
		for($month=1; $month<$this->get_month(); $month++)
			$days+= self::count_month_days ($this->get_year (), $month);
		
		$days+= $this->get_day();
		
		return $days;
		
	}
	
	
	public function get_total_hours() {
		
		return ($this->get_total_days() * 24) + (is_null($this->_hour) ? 0 : $this->get_hour());
		
	}
	
	
	public function get_total_minutes() {
		
		return ($this->get_total_hours() * 60) + (is_null($this->_min) ? 0 : $this->get_minutes());
		
	}
	
	
	public function get_total_seconds() {
		
		return ($this->get_total_minutes() * 60) + (is_null($this->_secs) ? 0 : $this->get_seconds());
		
	}
	
	
	public function get_year_days() {
		return self::count_year_days($this->get_year());
	}
	
	
	public function get_year_day() {
		$days = 0;
		
		for($month=1; $month<$this->get_month(); $month++)
			$days+= self::count_month_days ($this->get_year (), $month);
		
		$days+= $this->get_day();
		
		return $days;
	}
	
	
	public function get_month_days() {
		return self::count_month_days($this->get_year(), $this->get_month());
	}
	
	
	public function get_day_of_week() {
		
		return $this->get_total_days() % 7;
		
	}
	
	


	/*-----------------------------------------------------------*/
	
	
	
	/* @return Date */
	public function add_years($years) {
		
		$this->_year += $years;
		$this->_control_days();

		return $this;
		
	}
	
	/* @return Date */
	public function add_months($months) {
		
		$this->_month += $months;
		$this->_control_months();

		return $this;
	}
	
	/* @return Date */
	public function add_days($days) {

		$this->_day += $days;
		$this->_control_days();

		return $this;
		
	}
	
	
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
	
	public function diff_years($date=null, $first_this=false) {

		$date = self::parse($date);
		
		return $first_this ? ($this->get_year()-$date->get_year()) : ($date->get_year()-$this->get_year());
		
	}
	
	
	public function diff_months($date=null, $first_this=false) {

		$date = self::parse($date);
		
		return $first_this ? ($this->get_total_months()-$date->get_total_months()) : ($date->get_total_months()-$this->get_total_months());
		
	}
	
	
	public function diff_days($date=null, $first_this=false) {

		$date = self::parse($date);
		
		return $first_this ? ($this->get_total_days()-$date->get_total_days()) : ($date->get_total_days()-$this->get_total_days());
		
	}
	
	
	public function diff_hours($date=null, $first_this=false) {

		$date = self::parse($date);
		
		return $first_this ? ($this->get_total_hours()-$date->get_total_hours()) : ($date->get_total_hours()-$this->get_total_hours());
		
	}
	
	
	public function diff_minutes($date=null, $first_this=false) {

		$date = self::parse($date);
		
		return $first_this ? ($this->get_total_minutes()-$date->get_total_minutes()) : ($date->get_total_minutes()-$this->get_total_minutes());
		
	}
	
	
	public function diff_seconds($date=null, $first_this=false) {

		$date = self::parse($date);
		
		return $first_this ? ($this->get_total_seconds()-$date->get_total_seconds()) : ($date->get_total_seconds()-$this->get_total_seconds());
		
	}
	
	/*-----------------------------------------------------------*/
	

	public function compare_datetime($date=null, $use_time=true, $use_secs=true) {
		
		$date = self::parse($date);
		
		return self::_compare($this, $date, $use_time, $use_secs);
	}
	
	
	public function is_less_than_datetime($date=null, $use_time=true, $use_secs=true) {
		
		return $this->compare_datetime($date, $use_time, $use_secs) == -1;
		
	}
	
	
	public function is_greater_than_datetime($date=null, $use_time=true, $use_secs=true) {
		
		return $this->compare_datetime($date, $use_time, $use_secs) == 1;
		
	}
	
	
	public function is_less_or_equal_than_datetime($date=null, $use_time=true, $use_secs=true) {
		
		return $this->compare_datetime($date, $use_time, $use_secs) <= 0;
		
	}
	
	
	public function is_greater_or_equal_than_datetime($date=null, $use_time=true, $use_secs=true) {
		
		return $this->compare_datetime($date, $use_time, $use_secs) >= 0;
		
	}
	
	
	public function is_equal_to_datetime($date=null, $use_time=true, $use_secs=true) {
		
		return $this->compare_datetime($date, $use_time, $use_secs) == 0;
		
	}
	
	
	public function compare_date($date=null) {
		
		$date = self::parse($date);
		
		return self::_compare($this, $date, false, false);
	}
	
	
	public function is_less_than_date($date=null) {
		
		return $this->compare_datetime($date, false, false) == -1;
		
	}
	
	
	public function is_greater_than_date($date=null) {
		
		return $this->compare_datetime($date, false, false) == 1;
		
	}
	
	
	public function is_less_or_equal_than_date($date=null) {
		
		return $this->compare_datetime($date, false, false) <= 0;
		
	}
	
	
	public function is_greater_or_equal_than_date($date=null) {
		
		return $this->compare_datetime($date, false, false) >= 0;
		
	}
	
	
	public function is_equal_to_date($date=null) {
		
		return $this->compare_datetime($date, false, false) == 0;
		
	}
	
	
	
	public function compare_to($date=null) {
		$date = self::parse($date);
		$use_time = !is_null($this->_hour) && !is_null($date->_hour);
		return self::_compare($this, $date, $use_time, $use_time);
	}
	
	
	public function is_less_than($date=null) {
		$date = self::parse($date);
		$use_time = !is_null($this->_hour) && !is_null($date->_hour);
		return $this->compare_datetime($date, $use_time, $use_time) == -1;
		
	}
	
	
	public function is_greater_than($date=null) {
		$date = self::parse($date);
		$use_time = !is_null($this->_hour) && !is_null($date->_hour);
		return $this->compare_datetime($date, $use_time, $use_time) == 1;
		
	}
	
	
	public function is_less_or_equal_than($date=null) {
		$date = self::parse($date);
		$use_time = !is_null($this->_hour) && !is_null($date->_hour);
		return $this->compare_datetime($date, $use_time, $use_time) <= 0;
		
	}
	
	
	public function is_greater_or_equal_than($date=null) {
		$date = self::parse($date);
		$use_time = !is_null($this->_hour) && !is_null($date->_hour);
		return $this->compare_datetime($date, $use_time, $use_time) >= 0;
		
	}
	
	
	public function is_equal_to($date=null) {
		$date = self::parse($date);
		$use_time = !is_null($this->_hour) && !is_null($date->_hour);
		return $this->compare_datetime($date, $use_time, $use_time) == 0;
		
	}
	
	
	/*-----------------------------------------------------------*/
	
	
	public function to_string($format=null) {
		if(!$format) $format = $this->get_format();
		return preg_replace_callback('#(?!=\%)\%(?P<letter>[A-Za-z])#', array($this, '_format_replace_callback'), $format);
	}
	
	
	public function format($format=null) {
		return $this->to_string($format);
	}

	
	public function format_sql_date() {
		return $this->to_string(self::FORMAT_SQL_DATE);
	}
	
	
	public function format_sql_datetime() {
		return $this->to_string(self::FORMAT_SQL_DATETIME_SECS);
	}
	
	
	public function format_literal() {
		return $this->to_string(self::FORMAT_DATE_LITERAL);
	}

	
	public function format_literal_time() {
		return $this->to_string(self::FORMAT_DATE_LITERAL_TIME);
	}
	
	
	public function format_json() {
		return $this->to_string(self::FORMAT_JSON);
	}
	
	
	public function format_default_date() {
		return $this->to_string(self::FORMAT_DEFAULT_DATE);
	}
	
	
	public function format_default_datetime($secs=true) {
		return $this->to_string($secs ? self::FORMAT_DEFAULT_DATETIME_SECS : self::FORMAT_DEFAULT_DATETIME);
	}

	/*-----------------------------------------------------------*/
	
	public function copy() {
		
		$date = new Date();

		$date->set_year($this->_year, false);
		$date->set_month($this->_month, false);
		$date->set_day($this->_day, false);
		$date->set_hour($this->_hour, false);
		$date->set_minutes($this->_min, false);
		$date->set_seconds($this->_secs, false);
		
		return $date;
	}
	
}
