<?php //---------------------------------------------------------------------------------


@ define('ZPHP_GRAPHIC_COLOR_ALPHA_SEPARATOR', ':');


//---------------------------------------------------------------------------------

abstract class Graphic {
	
	const ALIGN_LEFT = 'left';
	const ALIGN_CENTER = 'center';
	const ALIGN_RIGHT = 'right';
	
	const ALIGN_TOP = 'top';
	const ALIGN_MIDDLE = 'middle';
	const ALIGN_BOTTOM = 'bottom';
	
	const ALIGN_START = 'start';
	const ALIGN_END = 'end';
	
	
	//------------------------------------------------------------------------------------------------
	
	public static $colors = array();
		
	//------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------
	
	
	protected static $_cache_colors_walk_maxsize = 262144;
	
	protected static $_cache_colors_constants = array();
	protected static $_cache_colors_constants_size = 0;
	protected static $_cache_colors_constants_maxsize = 4000;
		
	protected static $_cache_colors_info = array();
	protected static $_cache_colors_info_size = 0;
	protected static $_cache_colors_info_maxsize = 4000;
		
	
		
	
	protected static $_attrs_aliases = array(
											'fg' => 'color',
											'bg' => 'background',
											'alpha' => 'opacity',
											'font_size' => 'size',
											'fill' => 'fill_color',
											'align' => 'text_align',
											'line' => 'line_width',
											'rotation' => 'angle',
											'ln' => 'new_line',
											'text_angle' => 'angle');
											
	protected static $_align_aliases = array('left' => 'start', 'right' => 'end', 'center' => 'middle', 'top' => 'start', 'bottom' => 'end');
	
	
	
	protected static $_coordenates_constants = array( 'half' => '0.5', 'zero' => 0, 'full' => '1.0', 'middle' => '0.5', 'center' => '0.5', 'start' => 0,
												  'end' => '1.0', 'left' => '0.0', 'right' => '1.0', 'top' => '0.0', 'bottom' => '1.0');
	
	protected static $_coordenates_horizontal_constants = array('left','right');
	
	protected static $_coordenates_vertical_constants = array('top','bottom');
	
	
	protected static $_font_size_constants = array('normal' => '1.0', 'default' => '1.0', 
													'large' => '1.5', 'big' => '1.5', 'larger' => '2.0', 'bigger' => '2.0',
													'small' => '0.8', 'smaller' => '0.5');
	
	
	protected static $_hue_constants = array();
	
	//------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------
	
	
	
	public static function get_color_info($color, $input_type='rgb') {
		return self::_parse_color_rgb($color, strtolower($input_type));		
	}
	
	
	public static function get_color_rgb($color, $input_type='rgb') {
		return self::_parse_color_rgb($color, strtolower($input_type));		
	}
	
	
	public static function get_color_hsv($color, $input_type='rgb') {
		return self::_parse_color_hsv($color, strtolower($input_type));		
	}
	
	
	public static function get_color_hue($color, $input_type='rgb') {
		list($hue,$saturation,$value,$alpha) = self::get_color_hsv($color, $input_type);
		return $hue;
	}
	
		
	public static function convert_rgb2hsv($arg1, $arg2=null){
		$args = is_array($arg1) ? $arg1 : func_get_args();
		return self::_rgb2hsv($args); 
	}
	
	
	public static function convert_hsv2rgb($arg1, $arg2=null){
		$args = is_array($arg1) ? $arg1 : func_get_args();
		return self::_hsv2rgb($args); 
	}
	
	
	//------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------
	
	
	protected static function _rgb2hsv($rgb) {
		
		list($red, $green, $blue) = $rgb;
		
		$red /= 255;
		$green /= 255;
		$blue /= 255;

		$min = min( $red, $green, $blue );
		$max = max( $red, $green, $blue );
		
		$value = $max;
		$delta = $max-$min;
		
		
		if( $max != 0 && $delta != 0 ) {
			
			$saturation = $delta / $max;
			if( $red == $max ) $hue = (($green-$blue)/$delta);
			else if( $green == $max ) $hue = 2 + (($blue-$red)/$delta);
			else $hue = 4 + (($red-$green)/$delta);
	
			$hue *= 60;
			if( $hue<0 ) $hue += 360;
									
		} else {
			
			$saturation = 0;
			$hue = 0;
		}

		$hsv = $rgb;
		$hsv[0] = (integer) round($hue);
		$hsv[1] = (integer) round($saturation*255);
		$hsv[2] = (integer) round($value*255);
			
		return $hsv;
	}


	protected static function _hsv2rgb($hsv) {
	
		list($hue, $saturation, $value) = $hsv;
		
		$saturation /= 255;
		$value /= 255;
		
		if($saturation == 0) {
			$red = $value;
			$green = $value;
			$blue = $value;
		
		} else {
			
			$hue /= 60;
			$i = floor($hue);
			$f = $hue-$i;
			$p = $value * (1-$saturation);
			$q = $value * (1-$saturation*$f);
			$t = $value * (1-$saturation*(1-$f));

			switch( $i ) {
				case 0: $red = $value; $green = $t; $blue = $p; break;
					
				case 1: $red = $q; $green = $value; $blue = $p; break; 
					
				case 2: $red = $p; $green = $value; $blue = $t; break;
					
				case 3: $red = $p; $green = $q; $blue = $value; break;
					
				case 4: $red = $t; $green = $p; $blue = $p; break;
					
				default: $red = $value; $green = $p; $blue = $p; break;
			}
		}
	
		
		$rgb = $hsv;
		$rgb[0] = (integer) round($red*255);
		$rgb[1] = (integer) round($green*255);
		$rgb[2] = (integer) round($blue*255);
		
		return $rgb;
		
	
	}
	
	
	protected static function _parse_color_value($val){
		return self::_parse_numeric_range($val, array(0,255));
	}
	
	
	protected static function _parse_hue_value($val){
		return self::_parse_numeric_range($val, array(0,360), self::$_hue_constants);
	}
	
	
	protected static function _parse_rgb_array($values, $input_type='rgb'){
		
		if($input_type == 'hsv') {
			list($hue, $saturation, $value) = array_values($values);
			$hsv = array(
					self::_parse_hue_value($hue),
					self::_parse_color_value($saturation),
					self::_parse_color_value($value));
					
			$rgb = self::_hsv2rgb($hsv);
			
		} else {
			list($red, $green, $blue) = array_values($values);
			$rgb = array(
					self::_parse_color_value($red),
					self::_parse_color_value($green),
					self::_parse_color_value($blue));
		}
		
		return $rgb;
		
	}
	

	protected static function _parse_rgb_str($str, $default_type='rgb', $force_type=false) {
		$str = strtolower(preg_replace('/[^A-Za-z0-9\.\#\%\-\,\=]+/', '', $str));
		
		if(array_key_exists($str, self::$_cache_colors_constants)) return self::$_cache_colors_constants[$str];
		else {
			@ $is_constant = array_key_exists(($constant_key = self::_prepare_attr_name($str)), self::$colors);
			$color_str = $is_constant ? self::$colors[$constant_key] : $str;

			if(preg_match('/^\#(?i)(?P<red>[0-9a-f]{2})(?P<green>[0-9a-f]{2})(?P<blue>[0-9a-f]{2})$/', $color_str, $match))
				$rgb = array(hexdec($match['red']), hexdec($match['green']), hexdec($match['blue']) );
			
			else if(preg_match('/^\#(?i)(?P<red>[0-9a-f])(?P<green>[0-9a-f])(?P<blue>[0-9a-f])$/', $color_str, $match))
				$rgb = array(hexdec(str_repeat($match['red'],2)), hexdec(str_repeat($match['green'],2)), hexdec(str_repeat($match['blue'],2)) );
			
			else if( preg_match('/^(?:(?P<type>hsv|rgb)\=)?(?P<v1>.+?)\,(?P<v2>.+?)\,(?P<v3>.+?)/i', $str, $color_match) ) {
				
				$input_type = strtolower( $force_type ? $default_type : ($color_match['type'] ? $color_match['type'] : $default_type));
				$rgb = self::_parse_rgb_array(array($color_match['v1'], $color_match['v2'], $color_match['v3']), $input_type);
				
			} else $rgb = array(0,0,0);
				
						
			if($is_constant && self::$_cache_colors_constants_maxsize > self::$_cache_colors_constants_size){
				self::$_cache_colors_constants_size += strlen($str) + strlen(var_export($rgb,true));
				self::$_cache_colors_constants[$str] = $rgb;
			}
			
			return $rgb;
		}
	}
	

	protected static function _parse_color_rgb($value, $input_type='rgb') {

		$cache_key = var_export($value,true);
		if(array_key_exists($cache_key, self::$_cache_colors_info)) return self::$_cache_colors_info[$cache_key];
		else {
		
			if(is_array($value)){
				
				if(array_key_exists('red',$value) && array_key_exists('green',$value) && array_key_exists('blue',$value)) 
					$color = self::_parse_rgb_array(array($value['red'],$value['green'],$value['blue']), 'rgb');

				else if(array_key_exists('hue',$value) && array_key_exists('saturation',$value) && array_key_exists('value',$value)) 
					$color = self::_parse_rgb_array(array($value['hue'],$value['saturation'],$value['value']), 'hsv');
					
				else if(array_key_exists(0, $value) && array_key_exists(1, $value) && array_key_exists(2, $value)) {
					$color = self::_parse_rgb_array(array($value[0],$value[1],$value[2]), $input_type);
					if(array_key_exists(3, $value)) $alpha = $value[3];
				
				} else if(array_key_exists('color',$value)) $color = self::_parse_color_rgb($value['color'], $input_type);
	
				else if(array_key_exists('rgb',$value)) $color = self::_parse_color_rgb($value['rgb'], 'rgb');
				
				else if(array_key_exists('hsv',$value)) $color = self::_parse_color_rgb($value['hsv'], 'hsv');
				
				else $color = array(0,0,0);
				
				if(array_key_exists('alpha', $value)) $alpha = $value['alpha'];
				else if(array_key_exists('opacity', $value)) $alpha = $value['opacity'];
							
			} else {
				
				@ list($value,$alpha) = explode(ZPHP_GRAPHIC_COLOR_ALPHA_SEPARATOR, (string) $value);
				$color = self::_parse_rgb_str($value, $input_type);
				
			}
			
			$color = array($color[0],$color[1],$color[2],self::_parse_color_value($alpha));
			
			if(self::$_cache_colors_info_maxsize > self::$_cache_colors_info_size){
				self::$_cache_colors_info_size += strlen($cache_key) + strlen(var_export($color, true));
				self::$_cache_colors_info[$cache_key] = $color;
			}
						
			return $color;
		}
	}
	
	
	protected static function _parse_color_hsv($value, $input_type='rgb') {
		$rgb = self::_parse_color_rgb($value, $input_type);
		return self::_rgb2hsv($rgb);
	}
	
	
	protected static function _parse_color($value, $output_type='rgb', $input_type='rgb') {
		if($output_type == 'hsv') return self::_parse_color_hsv($value, $input_type);
		else return self::_parse_color_rgb($value, $input_type);
	}
	
	
	//------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------
	
	
	protected static function _move_coordenates($offset, $points){
		
		if($offset[0] == 0 && $offset[1] == 0) return $points;
		
		$moved_points = array();
		foreach($points as $point)
			$moved_points[] = array($point[0]+$offset[0], $point[1]+$offset[1]);
		
		return $moved_points;		
	}
	
	
	
	//------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------
	
	
	protected static function _parse_numeric_value($val, $max, $allow_overhead, $allow_negative, $constants=array()){
		if(is_null($val)) return 0;
		else {
						
			if(is_string($val) && array_key_exists(($constant_key=self::_prepare_attr_name($val)), $constants)) 
				$val = $constants[$constant_key];
			
			$relative = false;			
						
			if(!is_float($val) && !is_int($val)){
				if(strpos($val, '%') === strlen($val)-1) { 
					$relative = true;
					$val = ((float) $val) / 100;
				} else $relative = (boolean) preg_match('/\d*\.\d+/', $val);
			}
			
			if($relative) $val = round(((float) $val)*$max);
			$val = (integer) $val;			
			
			if($max>0) {
				if(!$allow_overhead && $val>$max) $val=$max;
				if(!$allow_negative) while($val<0) $val+=$max;
			
			} else if(!$allow_negative && $val < 0) $val = 0;
						
			return $val;
		}
	}
	
	
	protected static function _parse_numeric_range($val, $range, $constants=array()){
		$val = self::_parse_numeric_value($val, $range[1], true, true, $constants);
		if($val > $range[1]) $val = $range[1];
		else if($val < $range[0]) $val = $range[0];
		return $val;
	}
	
	
	protected static function _parse_integer_value($val, $allow_negative=true){
		return self::_parse_numeric_value($val, 0, false, $allow_negative);
	}
	
	
	protected static function _parse_coordenate_value($val, $max=0, $allow_overhead=false, $allow_negative=false){
		return self::_parse_numeric_value($val, $max, $allow_overhead, $allow_negative, self::$_coordenates_constants);
	}
		
	
	
	
	//------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------
	
	
	protected static function _prepare_attr_name($name) {
		$name = str_replace( array('-',' '), '_', strtolower(trim((string) $name)));
		return array_key_exists($name,self::$_attrs_aliases) ? self::$_attrs_aliases[$name] : $name;
	}
	
	
	
	protected static function _parse_align_str($str){
		$str = self::_prepare_attr_name($str);
		if(array_key_exists($str, self::$_align_aliases)) $str = self::$_align_aliases[$str];
		return $str;
	}
	
	
	protected static function _prepare_align_offset($avalsize, $size, $align_str, $parse_align_str=false) {
		if($parse_align_str) $align_str = self::_parse_align_str($align_str);
		
		if($align_str == 'end') return $avalsize-$size;
		else if($align_str == 'middle') return (integer) (($avalsize-$size)/2);
		else return 0;
		
	}
	
	
	
	protected static function _get_attr_value($names, $arg1=null, $arg2=null){
		$args = func_get_args();
		$attrs_arrays = array_slice($args, 1);
		$names = (array) $names;
		
		foreach($attrs_arrays as $attr_array)
			foreach($names as $name)
				if(array_key_exists($name, (array) $attr_array)) return $attr_array[$name];
				
		return null;
		
	}
	
	
	protected static function _parse_coordenates($arg, $max=array(0,0), $str_keys=array(), $allow_overhead=false, $allow_negative=false){
	
		if(is_array($arg)){
		
			$found = false;
			
			for($i=0; $i<count($str_keys); $i=$i+2)
				if(array_key_exists($str_keys[$i], $arg) && array_key_exists($str_keys[$i+1], $arg)) {
					$p1 = $arg[$str_keys[$i]];
					$p2 = $arg[$str_keys[$i+1]];
					$found=true;
					break;
				}
			
			if(!$found) list($p1, $p2) = array_values($arg);
			
			if(in_array(self::_prepare_attr_name($p1),self::$_coordenates_vertical_constants) || in_array(self::_prepare_attr_name($p2),self::$_coordenates_horizontal_constants)) {
				$aux = $p1;
				$p1 = $p2;
				$p2 = $aux;
			}
				
		} else if(is_int($arg) || is_float($arg)) {
				
				$p1 = $arg;
				$p2 = $arg;
				
		} else {
				
			$arg = trim(preg_replace('/(?:\s+|x|X|\;|\,)/', ' ', (string) $arg));
			
			if(strpos($arg,' ') !== false) {
				
				list($p1,$p2) = explode(' ', $arg, 2);
				
				if(in_array(self::_prepare_attr_name($p1),self::$_coordenates_vertical_constants) || in_array(self::_prepare_attr_name($p2),self::$_coordenates_horizontal_constants)) {
					$aux = $p1;
					$p1 = $p2;
					$p2 = $aux;
				}		
			
			} else {
			
				if(in_array(self::_prepare_attr_name($arg),self::$_coordenates_horizontal_constants)){
					$p1 = $arg;
					$p2 = 0;
				
				} else if(in_array(self::_prepare_attr_name($arg),self::$_coordenates_vertical_constants)){
					$p2 = $arg;
					$p1 = 0;
				
				} else {
					$p1 = $arg;
					$p2 = $arg;	
				
				}
					
			}
			
		}
		
		
		return array(
				self::_parse_coordenate_value($p1, $max[0], $allow_overhead, $allow_negative), 
				self::_parse_coordenate_value($p2, $max[1], $allow_overhead, $allow_negative));
	
	}
	
	
	protected static function _parse_point($arg, $max=array(0,0), $allow_overhead=false, $allow_negative=true){
		return self::_parse_coordenates($arg, $max, array('x','y','left','top'), $allow_overhead, $allow_negative);
	}
	
	
	
	
	protected static function _get_box_value($name, $arg1=null, $arg2=null){
		
		$top = 0; $left = 0; $bottom = 0; $right = 0;
		$single_names = array($name.'_top' => 'top', $name.'_left' => 'left', $name.'_right' => 'right', $name.'_bottom' => 'bottom');
		$multiple_names = array($name.'_width' => array('left','right'), $name.'_height'=>array('top','bottom'), $name => array('top','right','bottom','left'));		
		
		$attrs_array = array();
		
		$args = func_get_args(); 
		foreach(array_reverse(array_slice($args,1)) as $arg) 
			$attrs_array = array_merge($attrs_array, (array) $arg);
			
		foreach($attrs_array as $attr_name=>$value)	
			if(array_key_exists($attr_name, $single_names)) ${$single_names[$attr_name]} = $value;
			
			else if(array_key_exists($attr_name, $multiple_names)) {
				$names = $multiple_names[$attr_name];
								
				if(!is_array($value)) $value = preg_split('/\s+/', trim($value));
				else {
					$found_names = true;
					foreach($names as $var_name)
						if(!array_key_exists($var_name, $value)){
							$found_names = false;
							break;
						}
					
					if($found_names) {
						foreach($names as $var_name) ${$var_name} = $value[$var_name];
						continue;
					
					} else $value = array_values($value); 
				}
				
				
				
				if(count($value) > 0) {
					
					
					$count_names = count($names);
					while(count($value) < $count_names) $value = array_merge($value, $value);
					 
					foreach($names as $var_name) ${$var_name} = array_shift($value);
				}
				
			}
						
		
		return array_map(array(self, '_parse_integer_value'),array($top, $right, $bottom, $left));
	}
	
	
	
	protected static function _get_box_array_value($name, $arg1=null, $arg2=null){
		
		$top = 0; $left = 0; $bottom = 0; $right = 0;
		$single_names = array($name.'_top' => 'top', $name.'_left' => 'left', $name.'_right' => 'right', $name.'_bottom' => 'bottom');
		$multiple_names = array($name.'_width' => array('left','right'), $name.'_height'=>array('top','bottom'), $name => array('top','right','bottom','left'));		
		
		$attrs_array = array();
		
		$args = func_get_args(); 
		foreach(array_reverse(array_slice($args,1)) as $arg) 
			$attrs_array = array_merge($attrs_array, (array) $arg);
			
		foreach($attrs_array as $attr_name=>$value)	{
		
			
			
			if(array_key_exists($attr_name, $single_names)) ${$single_names[$attr_name]} = $value;
			
			else if(array_key_exists($attr_name, $multiple_names)) {
				$names = $multiple_names[$attr_name];
								
				$found_names = true;
				foreach($names as $var_name)
					if(!array_key_exists($var_name, (array) $value)){
						$found_names = false;
						break;
					}
				
				if($found_names) {
					foreach($names as $var_name) ${$var_name} = $value[$var_name];
					continue;
				
				} else $value = array_values((array) $value); 
				
				if(count($value) > 0) {
					
					
					$count_names = count($names);
					while(count($value) < $count_names) $value = array_merge($value, $value);
					 
					foreach($names as $var_name) ${$var_name} = array_shift($value);
				}
				
			}
			
		}
						
		
		return array($top, $right, $bottom, $left);
	}
	
	
	
	protected static function _parse_box_value($arg1=null, $arg2=null){
		
		$array = array();
		
		foreach(func_get_args() as $value)
			$array = array_merge($array, is_array($value) ? $value : explode(' ', preg_replace('#\s+#', ' ', trim((string) $value))));
		
		if(array_key_exists('top', $array) || array_key_exists('left', $array) || array_key_exists('right', $array) || array_key_exists('bottom', $array) || array_key_exists('height', $array) || array_key_exists('width', $array)) {
			
			if(array_key_exists('width', $array)) { $left = $array['width']; $right = $array['width']; } 
			else { $left = $array['left']; $right = $array['right']; }
						
			if(array_key_exists('height', $array)) { $top = $array['height']; $bottom = $array['height']; } 
			else { $top = $array['top']; $bottom = $array['bottom']; }
		
		} else {
			
			$array = array_values($array);
			
			if(count($array) > 3) { $top = $array[0]; $bottom = $array[2]; $left = $array[3]; $right = $array[1]; } 
			else if(count($array) == 3) { $top = $array[0]; $bottom = $array[2]; $right = $array[1]; $left = $array[1]; } 
			else if(count($array) == 2) { $top = $array[0]; $bottom = $array[0]; $right = $array[1]; $left = $array[1]; }
			else { $top = $array[0]; $bottom = $array[0]; $right = $array[0]; $left = $array[0]; }
			
		}
		
		return array_map(array(self, '_parse_integer_value'),array($top, $right, $bottom, $left));
		
	}
	
	
	//------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------
	
	abstract public function get_width();
	
	abstract public function get_height();
	
	public function get_size() {
		return array($this->get_width(), $this->get_height());
	}
	
	
}


//------------------------------------------------------------------------------------------------------------
// Constantes de Colores
//------------------------------------------------------------------------------------------------------------

Graphic::$colors['white'] = '#FFFFFF';
Graphic::$colors['ivory'] = '#FFFFF0';
Graphic::$colors['lightyellow'] = '#FFFFE0';
Graphic::$colors['yellow'] = '#FFFF00';
Graphic::$colors['snow'] = '#FFFAFA';
Graphic::$colors['floralwhite'] = '#FFFAF0';
Graphic::$colors['lemonchiffon'] = '#FFFACD';
Graphic::$colors['cornsilk'] = '#FFF8DC';
Graphic::$colors['seashell'] = '#FFF5EE';
Graphic::$colors['lavenderblush'] = '#FFF0F5';
Graphic::$colors['papayawhip'] = '#FFEFD5';
Graphic::$colors['blanchedalmond'] = '#FFEBCD';
Graphic::$colors['mistyrose'] = '#F FE4E1';
Graphic::$colors['bisque'] = '#FFE4C4';
Graphic::$colors['moccasin'] = '#FFE4B5';
Graphic::$colors['navajowhite'] = '#FFDEAD';
Graphic::$colors['peachpuff'] = '#FFDAB9';
Graphic::$colors['gold'] = '#FFD700';
Graphic::$colors['pink'] = '#FFC0CB';
Graphic::$colors['lightpink '] = '#FFB6C1';
Graphic::$colors['orange'] = '#FFA500';
Graphic::$colors['lightsalmon'] = '#FFA07A';
Graphic::$colors['darkorange'] = '#FF8C00';
Graphic::$colors['coral'] = '#FF7F50';
Graphic::$colors['hotpink'] = '#FF69B4';
Graphic::$colors['tomato'] = '#FF6347';
Graphic::$colors['orangered'] = '#FF4500';
Graphic::$colors['deeppink'] = '#FF1493';
Graphic::$colors['magenta'] = '#FF00FF';
Graphic::$colors['fuchsia'] = '#FF00FF';
Graphic::$colors['red'] = '#FF0000';
Graphic::$colors['oldlace'] = '#FDF5E6';
Graphic::$colors['lightgoldenrodyellow'] = '#FAFAD2';
Graphic::$colors['linen'] = '#FAF0E6';
Graphic::$colors['antiquewhite'] = '#FAEBD7';
Graphic::$colors['salmon'] = '#FA8072';
Graphic::$colors['ghostwhite'] = '#F8F8FF';
Graphic::$colors['mintcream'] = '#F5FFFA';
Graphic::$colors['whitesmoke'] = '#F5F5F5';
Graphic::$colors['beige'] = '# F5F5DC';
Graphic::$colors['wheat'] = '#F5DEB3';
Graphic::$colors['sandybrown'] = '#F4A460';
Graphic::$colors['azure'] = '#F0FFFF';
Graphic::$colors['honeydew'] = '#F0FFF0';
Graphic::$colors['aliceblue'] = '#F0F8FF';
Graphic::$colors['khaki'] = '#F0E68C';
Graphic::$colors['lightcoral '] = '#F08080';
Graphic::$colors['palegoldenrod'] = '#EEE8AA';
Graphic::$colors['violet'] = '#EE82EE';
Graphic::$colors['darksalmon'] = '#E9967A';
Graphic::$colors['lavender'] = '#E6E6FA';
Graphic::$colors['lightcyan'] = '#E0FFFF';
Graphic::$colors['burlywood'] = '#DEB887';
Graphic::$colors['plum'] = '#DDA0DD';
Graphic::$colors['gainsboro'] = '#DCDCDC';
Graphic::$colors['crimson'] = '#DC143C';
Graphic::$colors['palevioletred'] = '#DB7093';
Graphic::$colors['goldenrod'] = '#DAA520';
Graphic::$colors['orchid'] = '#DA70D6';
Graphic::$colors['thistle'] = '#D8BFD8';
Graphic::$colors['lightgrey'] = '#D3D3D3';
Graphic::$colors['tan'] = '#D2B48C';
Graphic::$colors['chocolate'] = '#D2691E';
Graphic::$colors['peru'] = '#CD853F';
Graphic::$colors['indianred'] = '#CD5C5C';
Graphic::$colors['mediumvioletred'] = '#C71585';
Graphic::$colors['silver'] = '#C0C0C0';
Graphic::$colors['darkkhaki'] = '#BDB76B';
Graphic::$colors['rosybrown'] = '#BC8F8F';
Graphic::$colors['mediumorchid'] = '#BA55D3';
Graphic::$colors['darkgoldenrod'] = '#B8860B';
Graphic::$colors['firebrick'] = '#B22222';
Graphic::$colors['powderblue'] = '#B0E0E6';
Graphic::$colors['lightsteelblue'] = '#B0C4DE';
Graphic::$colors['paleturquoise'] = '#AFEEEE';
Graphic::$colors['greenyellow'] = '#ADFF2F';
Graphic::$colors['lightblue'] = '#ADD8E6';
Graphic::$colors['darkgray'] = '#A9A9A9';
Graphic::$colors['brown'] = '#A52A2A';
Graphic::$colors['sienna'] = '#A0522D';
Graphic::$colors['yellowgreen'] = '#9ACD32';
Graphic::$colors['darkorchid'] = '#9932CC';
Graphic::$colors['palegreen'] = '#98FB98';
Graphic::$colors['darkviolet'] = '#9400D3';
Graphic::$colors['mediumpurple'] = '#9370DB';
Graphic::$colors['lightgreen'] = '#90EE90';
Graphic::$colors['darkseagreen'] = '#8FBC8F';
Graphic::$colors['saddlebrown'] = '#8B4513';
Graphic::$colors['darkmagenta'] = '#8B008B';
Graphic::$colors['darkred'] = '#8B0000';
Graphic::$colors['blueviolet'] = '#8A2BE2';
Graphic::$colors['lightskyblue'] = '#87CEFA';
Graphic::$colors['skyblue'] = '#87CEEB';
Graphic::$colors['gray'] = '#808080';
Graphic::$colors['olive'] = '#808000';
Graphic::$colors['purple'] = '#800080';
Graphic::$colors['maroon'] = '#800000';
Graphic::$colors['aquamarine'] = '#7FFFD4';
Graphic::$colors['chartreuse'] = '#7FFF00';
Graphic::$colors['lawngreen'] = '#7CFC00';
Graphic::$colors['mediumslateblue'] = '#7B68EE';
Graphic::$colors['lightslategray'] = '#778899';
Graphic::$colors['slategray'] = '#708090';
Graphic::$colors['olivedrab'] = '#6B8E23';
Graphic::$colors['slateblue'] = '#6A5ACD';
Graphic::$colors['dimgray'] = '#696969';
Graphic::$colors['mediumaquamarine'] = '#66CDAA';
Graphic::$colors['cornflowerblue'] = '#6495ED';
Graphic::$colors['cadetblue'] = '#5F9EA0';
Graphic::$colors['darkolivegreen'] = '#556B2F';
Graphic::$colors['indigo'] = '#4B0082';
Graphic::$colors['mediumturquoise'] = '#48D1CC';
Graphic::$colors['darkslateblue'] = '#483D8B';
Graphic::$colors['steelblue'] = '#4682B4';
Graphic::$colors['royalblue'] = '#4169E1';
Graphic::$colors['turquoise'] = '#40E0D0';
Graphic::$colors['mediumseagreen'] = '#3CB371';
Graphic::$colors['limegreen'] = '#32CD32';
Graphic::$colors['darkslategray'] = '#2F4F4F';
Graphic::$colors['seagreen'] = '#2E8B57';
Graphic::$colors['forestgreen'] = '#228B22';
Graphic::$colors['lightseagreen'] = '#20B2AA';
Graphic::$colors['dodgerblue'] = '#1E90FF';
Graphic::$colors['midnightblue'] = '#191970';
Graphic::$colors['cyan'] = '#00FFFF';
Graphic::$colors['aqua'] = '#00FFFF';
Graphic::$colors['springgreen'] = '#00FF7F';
Graphic::$colors['lime'] = '#00FF00';
Graphic::$colors['mediumspringgreen'] = '#00FA9A';
Graphic::$colors['darkturquoise'] = '#00CED1';
Graphic::$colors['deepskyblue'] = '#00BFFF';
Graphic::$colors['darkcyan'] = '#008B8B';
Graphic::$colors['teal'] = '#008080';
Graphic::$colors['green'] = '#008000';
Graphic::$colors['darkgreen'] = '#006400';
Graphic::$colors['blue'] = '#0000FF';
Graphic::$colors['mediumblue'] = '#0000CD';
Graphic::$colors['darkblue'] = '#00008B';
Graphic::$colors['navy'] = '#000080';
Graphic::$colors['black'] = '#000000';


//-------------------------------------------------------------------------------------- ?>