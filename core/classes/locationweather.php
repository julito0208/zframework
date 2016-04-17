<?php 

class LocationWeather
{
	
	const UNIT_TEMP_CELSIUS = 'C';
	const UNIT_TEMP_FAHRENHEIT = 'F';
	
	const UNIT_DISTANCE_KM = 'kmh';
	const UNIT_DISTANCE_MP = 'mph';
	
	const COND_TORNADO = 0;
	const COND_TROPICAL_STORM = 1;
	const COND_HURRICANE = 2;
	const COND_SEVERE_THUNDERSTORMS = 3;
	const COND_THUNDERSTORMS = 4;
	const COND_MIXED_RAIN_AND_SNOW = 5;
	const COND_MIXED_RAIN_AND_SLEET = 6;
	const COND_MIXED_SNOW_AND_SLEET = 7;
	const COND_FREEZING_DRIZZLE = 8;
	const COND_DRIZZLE = 9;
	const COND_FREEZING_RAIN = 10;
	const COND_SHOWERS = 11;
	const COND_SNOW_FLURRIES = 13;
	const COND_LIGHT_SNOW_SHOWERS = 14;
	const COND_BLOWING_SNOW = 15;
	const COND_SNOW = 16;
	const COND_HAIL = 17;
	const COND_SLEET = 18;
	const COND_DUST = 19;
	const COND_FOGGY = 20;
	const COND_HAZE = 21;
	const COND_SMOKY = 22;
	const COND_BLUSTERY = 23;
	const COND_WINDY = 24;
	const COND_COLD = 25;
	const COND_CLOUDY = 26;
	const COND_MOSTLY_CLOUDY_NIGHT = 27;
	const COND_MOSTLY_CLOUDY_DAY = 28;
	const COND_PARTLY_CLOUDY_NIGHT = 29;
	const COND_PARTLY_CLOUDY_DAY = 30;
	const COND_CLEAR_NIGHT = 31;
	const COND_SUNNY = 32;
	const COND_FAIR_NIGHT = 33;
	const COND_FAIR_DAY = 34;
	const COND_MIXED_RAIN_AND_HAIL = 35;
	const COND_HOT = 36;
	const COND_ISOLATED_THUNDERSTORMS = 37;
	const COND_SCATTERED_THUNDERSTORMS = 38;
	const COND_SCATTERED_SHOWERS = 40;
	const COND_HEAVY_SNOW = 41;
	const COND_SCATTERED_SNOW_SHOWERS = 42;
	const COND_PARTLY_CLOUDY = 44;
	const COND_SNOW_SHOWERS = 46;
	const COND_ISOLATED_THUNDERSHOWERS = 47;
	const COND_NOT_AVAILABLE = 3200;

	const DEFAULT_TEMP_UNIT = self::UNIT_TEMP_CELSIUS;
	const DEFAULT_DISTANCE_UNIT = self::UNIT_DISTANCE_KM;
	const DEFAULT_COND_CODE = self::COND_NOT_AVAILABLE;
	
	const POWERED_BY_IMG_URL = 'https://poweredby.yahoo.com/purple.png';
	
	/*------------------------------------------------------------------------------------------------------*/

	protected static $_months_numbers = array(
		'Jan' => 1,
		'Feb' => 2,
		'Mar' => 3,
		'Apr' => 4,
		'May' => 5,
		'Jun' => 6,
		'Jul' => 7,
		'Aug' => 8,
		'Sep' => 9,
		'Oct' => 10,
		'Nov' => 11,
		'Dec' => 12,
	);
	
	protected static $_weather_conditions = array (
		0 => 'tornado',
		1 => 'tropicalstorm',
		2 => 'hurricane',
		3 => 'severethunderstorms',
		4 => 'thunderstorms',
		5 => 'mixedrainandsnow',
		6 => 'mixedrainandsleet',
		7 => 'mixedsnowandsleet',
		8 => 'freezingdrizzle',
		9 => 'drizzle',
		10 => 'freezingrain',
		11 => 'showers',
		12 => 'showers',
		13 => 'snowflurries',
		14 => 'lightsnowshowers',
		15 => 'blowingsnow',
		16 => 'snow',
		17 => 'hail',
		18 => 'sleet',
		19 => 'dust',
		20 => 'foggy',
		21 => 'haze',
		22 => 'smoky',
		23 => 'blustery',
		24 => 'windy',
		25 => 'cold',
		26 => 'cloudy',
		27 => 'mostlycloudynight',
		28 => 'mostlycloudyday',
		29 => 'partlycloudynight',
		30 => 'partlycloudyday',
		31 => 'clearnight',
		32 => 'sunny',
		33 => 'fairnight',
		34 => 'fairday',
		35 => 'mixedrainandhail',
		36 => 'hot',
		37 => 'isolatedthunderstorms',
		38 => 'scatteredthunderstorms',
		39 => 'scatteredthunderstorms',
		40 => 'scatteredshowers',
		41 => 'heavysnow',
		42 => 'scatteredsnowshowers',
		43 => 'heavysnow',
		44 => 'partlycloudy',
		45 => 'thundershowers',
		46 => 'snowshowers',
		47 => 'isolatedthundershowers',
		3200 => 'notavailable',
	);
	
	private static $_conditions_language_text_prefix = 'location_weather_cond_';

	private static $_condition_code_image_url_format = 'http://l.yimg.com/a/i/us/we/52/%1$s.gif';
	
	private static $_fetch_weather_url = 'https://query.yahooapis.com/v1/public/yql';
		
	private static $_fetch_weather_defaults_params = array(
		'env' => 'store://datatables.org/alltableswithkeys',
		'format' => 'json'
	);
	
	private static $_fetch_weather_search_param_format = 'select * from weather.forecast where woeid in (select woeid from geo.places(1) where text="%s")';

	private static $_params_key = array('location_weather', 'data');
	
	protected static function _fetch_weather_data(array $args)
	{
		foreach($args as $key => $arg)
		{
			$args[$key] = trim($arg);
		}

		$url_params = array_merge(self::$_fetch_weather_defaults_params, array());
		$url_params['q'] = sprintf(self::$_fetch_weather_search_param_format, implode(' ', $args));
		
		foreach($url_params as $key => $url_param)
		{
			$url_params[$key] = $key.'='.urlencode($url_param);
		}
		
		$url = self::$_fetch_weather_url.'?'.implode('&', $url_params);

		$params_key = array_merge(self::$_params_key, array($url));
		$fetch_url = true;
		
		if(ParamsHelper::has_param($params_key))
		{
			$params_value = ParamsHelper::get_param($params_key);
			$fetch_time = $params_value['time'];
			
			if($fetch_time)
			{
				$difference = (time() - $fetch_time) / 60;
				$max_difference = ZPHP::get_config('location_weather_update_interval_minutes');
				
				if($difference < $max_difference)
				{
					$weather_data = $params_value['weather_data'];
					$fetch_url = false;
				}
			}
		}
		
		if($fetch_url)
		{
			@ $contents = file_get_contents($url);
			$json = JSONMap::parse($contents);

			$weather_data = array();

			if($json && $json->has_key('query'))
			{
				$query = $json->get_item('query');

				if($query['results'])
				{
					$results = $query['results'];

					if($results['channel'])
					{
						$weather_data = $results['channel'];
					}
				}
			}		
			
			$params_value = array(
				'weather_data' => $weather_data,
				'time' => time()
			);
			
			ParamsHelper::set_param($params_key, $params_value);
		}

		return $weather_data;
	}
	
	protected static function _convert_km_to_mp($km)
	{
		return $km * 0.621371192;
	}
	
	protected static function _convert_mp_to_km($mp)
	{
		return $mp * 1.609344;
	}
	
	protected static function _convert_c_to_f($c)
	{
		return ($c*1.8) + 32;
	}
	
	protected static function _convert_f_to_c($f)
	{
		return ($f-32) / 1.8;
	}
	
	protected static function _parse_condition_str($condition)
	{
		return preg_replace('#[^a-z0-9]+#', '', strtolower(trim($condition)));
	}
	
	protected static function _get_condition_code_from_key($key)
	{
		$key = self::_parse_condition_str($key);
		
		foreach(self::$_weather_conditions as $code => $value)
		{
			if($value == $key)
			{
				return $code;
			}
		}
		
		return null;
	}
	
	protected static function _parse_condition_code($code)
	{
		$code = self::_parse_condition_str($code);
		
		if(array_key_exists($code, self::$_weather_conditions))
		{
			return (integer) $code;
		}
		else if(in_array($code, self::$_weather_conditions))
		{
			return self::_get_condition_code_from_key($code);
		}
		else
		{
			return null;
		}
		
	}
	
	protected static function _parse_condition_key($key)
	{
		$key = self::_parse_condition_str($key);
		
		if(array_key_exists($key, self::$_weather_conditions))
		{
			return self::$_weather_conditions[$key];
		}
		else if(in_array($code, self::$_weather_conditions))
		{
			return $key;
		}
		else
		{
			return null;
		}
		
	}
	
	protected static function _get_condition_language_text_key($cond)
	{
		$code = self::_parse_condition_code($cond);
		$key = self::$_weather_conditions[$code];
		
		return self::$_conditions_language_text_prefix.$key;
		
	}
	
	protected static function _get_condition_image_url($cond)
	{
		$code = self::_parse_condition_code($cond);
		
		return sprintf(self::$_condition_code_image_url_format, $code);
		
	}
	
	/*------------------------------------------------------------------------------------------------------*/
	
	public static function get_condition_image_url($cond)
	{
		return self::_get_condition_image_url($cond);
	}
	
	
	public static function get_condition_text($cond, $language=null)
	{
		$language_text = self::_get_condition_language_text_key($cond);
		
		return LanguageHelper::get_text($language_text, $language);
	}
	
	
	/*------------------------------------------------------------------------------------------------------*/

	protected $_data;
	
	protected $_temp_unit = self::DEFAULT_TEMP_UNIT;
	protected $_distance_unit = self::DEFAULT_DISTANCE_UNIT;
	
	protected $_location = array();
	protected $_wind = array();
	protected $_atmosphere = array();
	protected $_astronomy = array();
	protected $_units = array();
	protected $_actual_weather = array();
	protected $_forecast = array();
	
	public function __construct($country=null, $state=null, $city=null)
	{
		$args = func_get_args();
		$this->_data = (array) self::_fetch_weather_data($args);
				
		$this->_update_location();
		$this->_update_units();
		$this->_update_atmosphere();
		$this->_update_astronomy();
		$this->_update_wind();
		$this->_update_weather();
	}
	
	/*------------------------------------------------------------------------------------------------------*/
	
	protected function _update_location()
	{
		$this->_location = (array) $this->_data['location'];
	}
	
	protected function _update_units()
	{
		$this->_units = (array) $this->_data['units'];
	}
	
	protected function _update_atmosphere()
	{
		$this->_atmosphere = (array) $this->_data['atmosphere'];
	}
	
	protected function _update_astronomy()
	{
		$this->_astronomy = (array) $this->_data['astronomy'];
	}
	
	protected function _update_wind()
	{
		$this->_wind = (array) $this->_data['wind'];
		
		if($this->_units['distance'] != $this->_distance_unit)
		{
			if($this->_distance_unit == self::UNIT_DISTANCE_KM)
			{
				$this->_wind['speed'] = self::_convert_mp_to_km($this->_wind['speed']);
			}
			else
			{
				$this->_wind['speed'] = self::_convert_km_to_mp($this->_wind['speed']);
			}
		}
	}
	
	protected function _update_weather()
	{
		$this->_update_forecast();
		$this->_update_actual_weather();
	}
	
	protected function _update_actual_weather()
	{
		$this->_actual_weather = array();
		$condition = $this->_data['item']['condition'];
		
		if($this->_units['temperature'] != $this->_temp_unit)
		{
			if($this->_temp_unit == self::UNIT_TEMP_CELSIUS)
			{
				$this->_actual_weather['temp'] = self::_convert_f_to_c($condition['temp']);
			}
			else
			{
				$this->_actual_weather['temp'] = self::_convert_c_to_f($condition['temp']);
			}
		}
		else
		{
			$this->_actual_weather['temp'] = $condition['temp'];
		}
		
		list($day_name, $day_number, $month_name, $year, $hour, $minute, $am_pm, $other) = sscanf($condition['date'], '%s %d %s %d %d:%d %s %s');
		
		if(strtolower($am_pm) == 'pm')
		{
			$hour = $hour + 12;
		}
		else
		{
			if($hour == 12)
			{
				$hour = 0;
			}
		}

		$date = new Date();
		$date->set_day($day_number);
		$date->set_year($year);
		$date->set_month(self::$_months_numbers[$month_name]);
		$date->set_minutes($minute);
		$date->set_hour($hour);
		
		$this->_actual_weather['date'] = $date;
		$this->_actual_weather['code'] = $condition['code'];
		$this->_actual_weather['unit'] = $this->_temp_unit;
		$this->_actual_weather['temp_formatted'] = round($this->_actual_weather['temp'], 1).' '.$this->_temp_unit.'�';
		$this->_actual_weather['text'] = self::get_condition_text($this->_actual_weather['code']);
		$this->_actual_weather['img_url'] = self::get_condition_image_url($this->_actual_weather['code']);
		$this->_actual_weather['max'] = $this->_forecast[0]['max'];
		$this->_actual_weather['min'] = $this->_forecast[0]['min'];
		$this->_actual_weather['max_formatted'] = $this->_forecast[0]['max_formatted'];
		$this->_actual_weather['min_formatted'] = $this->_forecast[0]['min_formatted'];
	}
	
	protected function _update_forecast()
	{
		$this->_forecast = array();
		$forecast = $this->_data['item']['forecast'];
		
		$forecast = array_slice($forecast, 1);
		
		foreach($forecast as $data)
		{
			$day_data = array();
			
			if($this->_units['temperature'] != $this->_temp_unit)
			{
				if($this->_temp_unit == self::UNIT_TEMP_CELSIUS)
				{
					$day_data['max'] = self::_convert_f_to_c($data['high']);
					$day_data['min'] = self::_convert_f_to_c($data['low']);
				}
				else
				{
					$day_data['max'] = self::_convert_c_to_f($data['high']);
					$day_data['min'] = self::_convert_c_to_f($data['low']);
				}
			}
			else
			{
				$day_data['max'] = $data['high'];
				$day_data['min'] = $data['low'];
			}

			list($day_number, $month_name, $year) = sscanf($data['date'], '%d %s %d');

			$date = new Date();
			$date->set_day($day_number);
			$date->set_year($year);
			$date->set_month(self::$_months_numbers[$month_name]);
			
			$day_data['date'] = $date;
			$day_data['code'] = $data['code'];
			$day_data['unit'] = $this->_temp_unit;
			$day_data['max_formatted'] = round($day_data['max'], 1).' '.$this->_temp_unit.'�';
			$day_data['min_formatted'] = round($day_data['min'], 1).' '.$this->_temp_unit.'�';
			$day_data['text'] = self::get_condition_text($day_data['code']);
			$day_data['img_url'] = self::get_condition_image_url($day_data['code']);

			$this->_forecast[] = $day_data;
		}
	}
	
	/*------------------------------------------------------------------------------------------------------*/
	
	public function get_temp_unit()
	{
		return $this->_temp_unit;
	}
	
	public function set_temp_unit($unit)
	{
		$unit = strtolower(trim($unit));
		
		if($unit == self::UNIT_TEMP_CELSIUS)
		{
			$this->_temp_unit = self::UNIT_TEMP_CELSIUS;
		}
		else
		{
			$this->_temp_unit = self::UNIT_TEMP_FAHRENHEIT;
		}
		
		$this->_update_weather();
	}
	
	public function get_distance_unit()
	{
		return $this->_distance_unit;
	}
	
	public function set_distance_unit($unit)
	{
		$unit = strtolower(trim($unit));
		
		if($unit == self::UNIT_DISTANCE_MP)
		{
			$this->_distance_unit = self::UNIT_DISTANCE_MP;
		}
		else
		{
			$this->_distance_unit = self::UNIT_DISTANCE_KM;
		}
		
		$this->_update_wind();
	}
	
	public function get_location_city()
	{
		return $this->_location['city'];
	}
	
	public function get_location_country()
	{
		return $this->_location['country'];
	}
	
	public function get_location()
	{
		return $this->get_location_city().', '.$this->get_location_country();
	}
	
	public function get_wind_speed()
	{
		return $this->_wind['speed'];
	}
	
	public function get_wind_speed_formatted()
	{
		return round($this->get_wind_speed()).' '.$this->_distance_unit;
	}
	
	public function get_humidity()
	{
		return $this->_atmosphere['humidity'];
	}
	
	public function get_humidity_formatted()
	{
		return $this->get_humidity().'%';
	}
	
	public function get_pressure()
	{
		return $this->_atmosphere['pressure'];
	}
	
	public function get_pressure_formatted()
	{
		return $this->get_pressure().' '.$this->_units['pressure'];
	}
	
	public function get_sunrise()
	{
		return $this->_astronomy['sunrise'];
	}
	
	public function get_sunset()
	{
		return $this->_astronomy['sunset'];
	}
	
	public function get_actual_weather($key=null)
	{
		$actual_weather = array_merge($this->_actual_weather, array());
		
		if($key)
		{
			return $actual_weather[$key];
		}
		else
		{
			return $actual_weather;
		}
	}
	
	public function get_forecast()
	{
		return array_merge($this->_forecast, array());
	}
	
}