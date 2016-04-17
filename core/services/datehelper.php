<?php 

class DateHelper {

	private static $_MONTHS_NAMES_LONG = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
	private static $_MONTHS_NAMES_SHORT = array('ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic');
	private static $_DAYS_NAMES_LONG = array('Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo');
	private static $_DAYS_NAMES_SHORT = array('Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom');



	public static function date_get_month_name($month) {
		return self::$_MONTHS_NAMES_LONG[((integer) $month) - 1];
	}
	
	
	public static function date_get_month_name_short($month) {
		return self::$_MONTHS_NAMES_SHORT[((integer) $month) - 1];
	}
	
	
	public static function date_get_day_name($day) {
		return self::$_DAYS_NAMES_LONG[((integer) $day) - 1];
	}
	
	
	public static function date_get_day_name_short($day) {
		return self::$_DAYS_NAMES_SHORT[((integer) $day) - 1];
	}
	
	
	//--------------------------------------------------------------------------------
	
	
	public static function date_get_month_days($month=null, $year=null) {
		
		$num_args = func_num_args();
		
		if($num_args < 2) {
			
			$time = $num_args > 0 ? $month : time();
			
			$month = strftime('%m', $time);
			$year = strftime('%Y', $time);
		}
		
		$month = (integer) $month;
		$year = (integer) $year;
		
		if($month == 2) return $year % 4 == 0 ? 29 : 28;	
		else if($month == 1 || $month == 3 || $month == 5 || $month == 7 || $month == 8 || $month == 10 || $month == 12) return 31;
		else return 30;
		
	}
	
	
	
	public static function date_get_year_days($year=null) {
		
		$num_args = func_num_args();
		
		if($num_args == 0) $year = strftime('%Y', time());
		
		$year = (integer) $year;
		
		return $year % 4 == 0 ? 366 : 365;
	}
	
	
	//--------------------------------------------------------------------------------
	
	public static function time_format($format, $time=null) {
		
		$time = is_null($time) ? time() : $time;
		
		foreach(array(
			array('B', 'm', 'months_names_long'),
			array('b', 'm', 'months_names_short'),
			array('A', 'u', 'days_names_long'),
			array('a', 'u', 'days_names_short')) as $wrap_data) {
				
				list($find_char, $array_char, $array_name) = $wrap_data;
				
				if(strpos($format, "%{$find_char}") !== false) {
					
					$replace_str = $GLOBALS[ZPHP_VARNAME]['dates'][$array_name][((integer) strftime("%{$array_char}", $time))-1];
					$format = preg_replace("#(?<!\\%)\\%{$find_char}#", $replace_str, $format);
					
				}
			}
		
			
		return strftime($format, $time);
	}
	
	
	public static function time_parse($format, $expression) {
		
		$time_data = strptime($expression, $format);
		return mktime($time_data['tm_hour'], $time_data['tm_min'], $time_data['tm_sec'], $time_data['tm_mon']+1, $time_data['tm_mday'], $time_data['tm_year'] + 1900);
		
	}
	
	
	
	//--------------------------------------------------------------------------------
	
	
	public static function time_split($time=null) {
		
		if(is_null($time)) $time = time();
		
		$month = (integer) strftime('%m', $time);
		$year = (integer) strftime('%Y', $time);
		$hour = (integer) strftime('%H', $time);
		$min = (integer) strftime('%M', $time);
		$sec = (integer) strftime('%S', $time);
		$day = (integer) strftime('%d', $time);
		
		return array($year, $month, $day, $hour, $min, $sec);
	}
	
	
	public static function time_join($arg1, $arg2=null) {
		
		if(!is_array($arg1)) {
			
			$args = func_get_args();
			return DateHelper::time_join($args);
			
		} else {
			
			list($year, $month, $day, $hour, $min, $sec) = array_values($arg1);
					
			if($month && $day && $year && $day > ($max_days = DateHelper::date_get_month_days($month, $year))) $day = $max_days;
			
			return mktime($hour, $min, $sec, $month, $day, $year);
			
		}
	}
	
	
	//--------------------------------------------------------------------------------
	
	
	public static function time_add_days($days=0, $time=null) {
		
		if(is_null($time)) $time = time();
		
		return $time + (3600*24*$days);
	}
	
	
	public static function time_add_months($months=0, $time=null) {
		
		list($year, $month, $day, $hour, $min, $sec) = DateHelper::time_split($time);
		
		$count_months = abs($months);
		$add_sign = $months < 0 ? -1 : 1;
		
		for($i=0; $i<$count_months; $i++) {
			
			$month += $add_sign;
			
			if($month == 0) { $month = 12; $year--; } 
			else if($month == 13) { $month = 1; $year++; }
		}
		
		return DateHelper::time_join($year, $month, $day, $hour, $min, $sec);
	}
	
	
	
	public static function time_add_years($years=0, $time=null) {
		
		list($year, $month, $day, $hour, $min, $sec) = DateHelper::time_split($time);
		
		return DateHelper::time_join($year + $years, $month, $day, $hour, $min, $sec);
	}
	
	
	
	//--------------------------------------------------------------------------------
	
	
	public static function time_diff_days($time1, $time2=null) {
		
		
		if(is_null($time2)) $time2 = time();
		
		$num_days1 = floor($time1 / (3600*24));
		$num_days2 = floor($time2 / (3600*24));
		
		return $num_days1 - $num_days2;
	}
	
	
	
	public static function time_diff_months($time1, $time2=null) {
		
		list($year1, $month1, $day1, $hour1, $min1, $sec1) = DateHelper::time_split($time1);
		list($year2, $month2, $day2, $hour2, $min2, $sec2) = DateHelper::time_split($time2);
		
		$num_months1 = ($year1 * 12) + $month1;
		$num_months2 = ($year2 * 12) + $month2;
		
		return $num_months1 - $num_months2;
	}
	
	
	
	public static function time_diff_years($time1, $time2=null) {
		
		list($year1, $month1, $day1, $hour1, $min1, $sec1) = DateHelper::time_split($time1);
		list($year2, $month2, $day2, $hour2, $min2, $sec2) = DateHelper::time_split($time2);
		
		return $year1 - $year2;
	}
	
	
	//--------------------------------------------------------------------------------
	
	/* @return Date */
	public static function now() {
		return Date::now();
	}
	
	/* @return Date */
	public static function current_datetime() {
		return Date::current_datetime();
	}
	
	/* @return Date */
	public static function current_date() {
		return Date::current_date();
	}
	
	/* @return Time */
	public static function current_time() {
		return Time::current_time();
	}
	
	
}
