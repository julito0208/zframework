<?php //------------------------------------------------------------------------------------------------------------------------------

class NumbersHelper {
	
	protected static $_ROMAN_NUMBER_SYMBOLS = array('M'=>1000, 'CM'=>900, 'D'=>500, 'CD'=>400, 'C'=>100, 'XC'=>90,
													   'L'=>50, 'XL'=>40, 'X'=>10, 'IX'=>9, 'V'=>5, 'IV'=>4, 'I'=>1);

	protected static $_NUMBER_SIZE_UNITS = array('bytes','Kb','Mb','Gb','Tb','Pb');
	
	//------------------------------------------------------------------------------------------------------------------------------

	const FORMAT_MIN_DECIMALS = 2;
	const FORMAT_MAX_DECIMALS = 5;
	const FORMAT_CURRENCY_DECIMALS = 2;

	const SIZE_UNIT_BYTES = 0;
	const SIZE_UNIT_KB = 1;
	const SIZE_UNIT_MB = 2;
	const SIZE_UNIT_GB = 3;
	const SIZE_UNIT_TB = 4;
	const SIZE_UNIT_PT = 5;

	public static function decimal_split($number) {
		
		$number = (float) $number;
		$abs_number = abs($number);
		
		$number_str = (string) $number;
		$num_decimals = strlen($number_str) - strpos($number_str, '.') - 1;
		
		$integer_part = (integer) floor($abs_number);
		$decimal_part = $abs_number - $integer_part;
		
		return array( $number<0 ? -$integer_part : $integer_part, round($decimal_part, $num_decimals) );
	}
	
	
	public static function decimal_floor($number) {
		list($integer_part, $decimal_part) = NumbersHelper::decimal_split($number);
		return $integer_part;
	}
	
	
	public static function decimal_ceil($number) {
		$number = (float) $number;
		$integer = (integer) ceil(abs($number));
		return $number<0 ? -$integer : $integer;
		
	}
	
	
	public static function decimal_part($number) {
		list($integer_part, $decimal_part) = NumbersHelper::decimal_split($number);
		return $decimal_part;
	}
	
	
	public static function decimal_round($number, $decimals=0){
		return round($number, $decimals);
	}
	
	//------------------------------------------------------------------------------------------------------------------------------

	public static function get_default_thousands_separator()
	{
		$locale = localeconv();
		return $locale['thousands_sep'];
	}

	public static function get_default_decimals_separator()
	{
		$locale = localeconv();
		return $locale['decimal_point'];
	}

	public static function get_default_currency_symbol()
	{
		$locale = localeconv();
		return $locale['currency_symbol'];
	}

	public static function integer_format($number, $thousands_separator=null){
		return number_format($number, 0, '', $thousands_separator ? $thousands_separator : self::get_default_thousands_separator());
	}
	
	
	public static function decimal_format($number, $decimals=null, $thousands_separator=null, $decimals_separator=null) {
	
		list($integer_part, $decimal_part) = NumbersHelper::decimal_split($number);
		$integer_formatted = NumbersHelper::integer_format($integer_part, $thousands_separator);

		if(is_null($decimals))
		{
			$max_decimals = self::FORMAT_MAX_DECIMALS;
			$min_decimals = self::FORMAT_MIN_DECIMALS;
		}
		else if(!is_array($decimals))
		{
			$max_decimals = $decimals;
			$min_decimals = $decimals;
		}
		else
		{
			if(ArrayHelper::is_numeric($decimals))
			{
				$min_decimals = $decimals[0];
				$max_decimals = $decimals[1];
			}
			else
			{
				$min_decimals = $decimals['min'];
				$max_decimals = $decimals['max'];
			}
		}

		if($max_decimals < $min_decimals) $min_decimals = $max_decimals;

		ob_start();
		echo var_export($decimal_part, true);
		$decimal_part = ob_get_clean();

		list($decimal_int_digits, $decimal_digits) = explode('.', $decimal_part);

		$decimal_digits = str_pad(substr((string) $decimal_digits, 0, $max_decimals), $min_decimals, '0', STR_PAD_RIGHT);
		$decimals_separator = $decimals_separator ? $decimals_separator : self::get_default_decimals_separator();

		return $integer_formatted.($decimal_digits ? $decimals_separator.$decimal_digits : '');
	}
	
	
	public static function currency_format($number, $symbol=null, $decimals=null, $thousands_separator=null, $decimals_separator=null) {

		$symbol = $symbol ? $symbol : self::get_default_currency_symbol();
		$decimals = is_null($decimals) ? self::FORMAT_CURRENCY_DECIMALS : $decimals;
		return $symbol.' '.NumbersHelper::decimal_format($number, $decimals, $thousands_separator, $decimals_separator);
	}
	
	
	//------------------------------------------------------------------------------------------------------------------------------ 
	
	
	public static function roman_number_format($number, $lower=false){
		$roman = '';
		$number = (integer) $number;
		
		if($number>0 && $number<4000){
			
			$rest = $number;
			foreach(self::$_ROMAN_NUMBER_SYMBOLS as $roman_symbol => $value){
				
				while($rest >= $value){
					$roman.= $roman_symbol;
					$rest-= $value;
				}
				
				if($rest == 0) break;
			}
			
			if($lower) $roman = strtolower($roman);
		}
		
		return $roman;
		
	}
	
	
	public static function roman_number_parse($roman){
		
		$number = 0;
		$last_value = 0;
		
		foreach(str_split(preg_replace('/[^MDCLXVI]+/','',strtoupper($roman))) as $roman_symbol){
			$value = self::$_ROMAN_NUMBER_SYMBOLS[$roman_symbol];
			$number+= $value;
			
			if($value > $last_value) $number-= ($last_value*2);
			
			$last_value = $value;
		}
		
		return $number;
	}
	
	
	
	//------------------------------------------------------------------------------------------------------------------------------ 
	
	
	
	public static function number_get_optimal_size_unit($bytes) {
	
		$size_unit = 0;
		
		while($size_unit <= self::SIZE_UNIT_PT && ($bytes / pow(1024, $size_unit)) > 1024)
			$size_unit++;
		
		return $size_unit;
	}
	
	
	public static function number_format_size($bytes, $num_decimals=null, $size_unit=null) {
		
		if(is_null($size_unit)) $size_unit = NumbersHelper::number_get_optimal_size_unit($bytes);
		
		return NumbersHelper::decimal_format(NumbersHelper::decimal_round($bytes/pow(1024, $size_unit), (integer) $num_decimals), is_null($num_decimals) ? 0 : $num_decimals) . ' ' . self::$_NUMBER_SIZE_UNITS[$size_unit];
	}
	
	
	public static function number_parse_size($expresion) {
		
		if(preg_match('#^(?i)(?P<number>\d+(?:\.\d+)?)\s*(?P<unit_prefix>B|K|M|G|T|P)(?:b(?:it|s)?)?$#', trim($expresion), $match)) {
			
			switch(strtoupper($match['unit_prefix'])) {
				case 'B': $exp = 0; break;
				case 'K': $exp = 1; break;
				case 'M': $exp = 2; break;
				case 'G': $exp = 3; break;
				case 'T': $exp = 4; break;
				case 'P': $exp = 5; break;
			}
			
			return pow(1024, $exp) * ((float) $match['number']);
			
		} else return 0;
	}
	
	
	
}
