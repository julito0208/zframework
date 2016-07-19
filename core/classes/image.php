<?php 

@ define('ZPHP_IMAGE_TTF_FONT_BOLD_FORMAT', '%s_b');
@ define('ZPHP_IMAGE_TTF_FONT_ITALIC_FORMAT', '%s_i');
@ define('ZPHP_IMAGE_TTF_FONT_BOLD_ITALIC_FORMAT', '%s_bi');

@ define('ZPHP_IMAGE_FONT', ZPHP::get_config('image_font'));
@ define('ZPHP_IMAGE_FONT_SIZE', ZPHP::get_config('image_font_size'));
@ define('ZPHP_IMAGE_TYPE', ZPHP::get_config('image_type'));
@ define('ZPHP_IMAGE_COLOR', ZPHP::get_config('image_default_color'));
@ define('ZPHP_IMAGE_LINE_COLOR', ZPHP::get_config('image_default_line_color'));
@ define('ZPHP_IMAGE_LINE_WIDTH', ZPHP::get_config('image_default_line_width'));
@ define('ZPHP_IMAGE_FILL_COLOR', ZPHP::get_config('image_default_fill_color'));
@ define('ZPHP_IMAGE_BACKGROUND', ZPHP::get_config('image_default_background'));
@ define('ZPHP_IMAGE_WIDTH', ZPHP::get_config('image_default_width'));
@ define('ZPHP_IMAGE_HEIGHT', ZPHP::get_config('image_default_height'));
@ define('ZPHP_IMAGE_QUALITY', ZPHP::get_config('image_default_quality'));
@ define('ZPHP_IMAGE_BOLD', false);
@ define('ZPHP_IMAGE_ITALIC', false);
@ define('ZPHP_IMAGE_TRANSPARENT_COLOR', null);
@ define('ZPHP_IMAGE_TEXT_ALIGN', 'left');
@ define('ZPHP_IMAGE_BORDER', true);
@ define('ZPHP_IMAGE_BORDER_ARC', true);
@ define('ZPHP_IMAGE_BORDER_START', true);
@ define('ZPHP_IMAGE_BORDER_END', true);
@ define('ZPHP_IMAGE_TEXT_ANGLE', 0);
@ define('ZPHP_IMAGE_PADDING', 0);
@ define('ZPHP_IMAGE_TRUECOLOR', true);
@ define('ZPHP_IMAGE_OUT_ALWAYS_SEND_LENGTH', false);
@ define('ZPHP_IMAGE_DEFAULT_OUT_FILENAME', 'image');


class Image extends Graphic implements MIMEControl {
	
	const TYPE_JPEG = 'jpeg';
	const TYPE_GIF = 'gif';
	const TYPE_PNG = 'png';
	const TYPE_BMP = 'bmp';

	const CROP_MODE_FILL = 100;
		
	//------------------------------------------------------------------------------------------------
			
	public static $styles = array();
	
	//------------------------------------------------------------------------------------------------
	
	protected static $_cache_fonts_files = array();
	protected static $_cache_fonts_files_size = 0;
	protected static $_cache_fonts_files_maxsize = 4000;
	
		
	protected static $_cache_texts_boxs = array();
	protected static $_cache_texts_boxs_size = 0;
	protected static $_cache_texts_boxs_maxsize = 4000;
	
	
	
	protected static $_style_attrs = array('font','size','bold','italic','color','fill_color', 'opacity', 'line_width', 'angle',
											'line_color', 'text_align', 'border', 'border_arc', 'border_start', 'border_end',
											'padding', 'padding_width', 'padding_height', 'padding_top', 'padding_left', 
											'padding_right', 'padding_bottom');
	
	protected static $_image_attrs = array('type', 'quality', 'width', 'height', 'transparent_color', 'size', 'file', 'background');
	
	
	protected static $_default_style_attrs = array(
										'font' => ZPHP_IMAGE_FONT,
										'size' => ZPHP_IMAGE_FONT_SIZE,
										'bold' => ZPHP_IMAGE_BOLD,
										'italic' => ZPHP_IMAGE_ITALIC,
										'color' => ZPHP_IMAGE_COLOR,
										'line_color' => ZPHP_IMAGE_LINE_COLOR,
										'line_width' => ZPHP_IMAGE_LINE_WIDTH,
										'fill_color' => ZPHP_IMAGE_FILL_COLOR,
										'border' => ZPHP_IMAGE_BORDER,
										'border_arc' => ZPHP_IMAGE_BORDER_ARC,
										'border_start' => ZPHP_IMAGE_BORDER_START,
										'border_end' => ZPHP_IMAGE_BORDER_END,
										'angle' => ZPHP_IMAGE_TEXT_ANGLE,
										'padding' => ZPHP_IMAGE_PADDING);
	
	
	protected static $_default_image_attrs = array(
										'width' => ZPHP_IMAGE_WIDTH,
										'height' => ZPHP_IMAGE_HEIGHT,
										'quality' => ZPHP_IMAGE_QUALITY,
										'type' => ZPHP_IMAGE_TYPE,
										'truecolor' => ZPHP_IMAGE_TRUECOLOR,
										'transparent_color' => ZPHP_IMAGE_TRANSPARENT_COLOR,
										'background' => ZPHP_IMAGE_BACKGROUND,
										'truecolor' => ZPHP_IMAGE_TRUECOLOR);
	
	
	
	
	
	protected static $_types_aliases = array(
											'jpg' => 'jpeg',
											'default' => ZPHP_IMAGE_TYPE);
	
	
	protected static $_valid_types = array('jpeg', 'gif', 'png', 'bmp');
	
	
	
	
	protected static $_angle_constants = array('horizontal' => 0, 'default' => ZPHP_IMAGE_TEXT_ANGLE, 'vertical' => 180);
	
	
	//------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------
	
	
	public static function get_font_file($style=null){
		
		$args = func_get_args();
		$style = self::_merge_attrs($args);
		return self::_get_font_file($style);
	}
	
	
	
	//------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------
	
	protected static function _get_font_file_path_format() {
		return ZPHP::get_config('zframework_dir').'/resources/fonts-ttf/%s.ttf';
	}
	
	protected static function _get_font_file($style){
		
		$style = array('font'=>strtolower(trim($style['font'])), 'bold'=>(boolean) $style['bold'], (boolean) 'italic'=>$style['italic']);
		$cache_key = var_export($style,true);
		
		if(array_key_exists($cache_key, self::$_cache_fonts_files)) return self::$_cache_fonts_files[$cache_key];
		else { 
			$font = $style['font'] ? $style['font'] :ZPHP_IMAGE_FONT;
				
			if($style['bold'] || $style['italic']){
				if($style['bold'] && $style['italic']) $file_basename=sprintf(ZPHP_IMAGE_TTF_FONT_BOLD_ITALIC_FORMAT, $font);
				else if($style['bold']) $file_basename=sprintf(ZPHP_IMAGE_TTF_FONT_BOLD_FORMAT, $font);
				else $file_basename=sprintf(ZPHP_IMAGE_TTF_FONT_ITALIC_FORMAT, $font);
				
				$font_file = sprintf(self::_get_font_file_path_format(), $file_basename);
				if(file_exists($font_file)) $valid_file = true;
			}
			
			if(!$valid_file) {
				$font_file = sprintf(self::_get_font_file_path_format(), $font);
				if(!file_exists($font_file)) $font_file = sprintf(self::_get_font_file_path_format(), ZPHP_IMAGE_FONT);
			}
			
			
			if(self::$_cache_fonts_files_maxsize > self::$_cache_fonts_files_size){
				self::$_cache_fonts_files_size += strlen($cache_key) + strlen($font_file);
				self::$_cache_fonts_files[$cache_key] = $font_file;
			}
			
			
			return $font_file;
			
		}
	}
	
	
	protected static function _get_text_box($text, $style, $key=null) {

		$style = array(
			'size' => self::_parse_font_size($style['size']),
			'font_file' => self::_get_font_file($style),
			'angle' => self::_parse_angle($style['angle']));
		
		$cache_key = var_export($style, true) . $text;
		
		if(array_key_exists($cache_key, self::$_cache_texts_boxs)) $text_box = self::$_cache_texts_boxs[$cache_key];
		else {
			
			// Guardamos en una variable '$pointsArray' informaci�n sobre las coordenadas
			// relativas del texto. El array $p tendr� la siguiente informaci�n:
			
			// $pointsArray[0] = esquina inferior izquierda, posici�n X
			// $pointsArray[1] = esquina inferior izquierda, posici�n Y
			// $pointsArray[2] = esquina inferior derecha, posici�n X
			// $pointsArray[3] = esquina inferior derecha, posici�n Y
			// $pointsArray[4] = esquina superior derecha, posici�n X
			// $pointsArray[5] = esquina superior derecha, posici�n Y
			// $pointsArray[6] = esquina superior izquierda, posici�n X
			// $pointsArray[7] = esquina superior izquierda, posici�n Y
			
			$points = imagettfbbox( $style['size'], $style['angle'], $style['font_file'], $text );
			$points = self::_group_coordenates_points($points);
			
			$dev_point = self::_normalize_polygon_points($points);
			$text_size = self::_get_polygon_size($points);
						
			$text_box = array();
			$text_box['font_size'] = $style['size'];
			$text_box['font_file'] = $style['font_file'];
			$text_box['text_size'] = $text_size;
			$text_box['angle'] = $style['angle'];
			$text_box['points'] = $points;
			$text_box['dev_point'] = $dev_point;
			
			if(self::$_cache_texts_boxs_maxsize > self::$_cache_texts_boxs_size){
				self::$_cache_texts_boxs_size += strlen($cache_key) + strlen(var_export($text_box, true));
				self::$_cache_texts_boxs[$cache_key] = $text_box;
			}
		}
		
		if(!$key) return $text_box;
		else return $text_box[$key];
	}
	
	
	
	//------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------
	
	protected static function _get_polygon_size($points) {
		
		$x_pos = array();
		$y_pos = array();
		
		foreach($points as $point) {
			$x_pos[] = $point[0];
			$y_pos[] = $point[1];
		}
		
		return array(abs(max($x_pos)-min($x_pos)), abs(max($y_pos)-min($y_pos)));
		
	}
	
	
	
	protected static function _normalize_polygon_points(&$points) {
		
		$pos_x = array();
		$pos_y = array();
		
		foreach($points as $point) {
			$pos_x[] = $point[0];
			$pos_y[] = $point[1];
		}
		
		$min_x = min($pos_x); $max_x = max($pos_x);
		$min_y = min($pos_y); $max_y = max($pos_y);
		
		$height = abs($max_y - $min_y);
		
		$dev_point = array($min_x < 0 ? -$min_x : 0, $min_y < 0 ? -$min_y : 0);
		
		foreach($points as $index => $point) 
			$points[$index] = array($point[0] + $dev_point[0], $point[1] + $dev_point[1]);
		
		return $dev_point;
	}
	
	
	
	protected static function _rotate_coordenates($angle, $points){
		if($angle > 0) {
			
			$angle_rad = deg2rad($angle);
			$angle_cos = cos($angle_rad);
			$angle_sin = sin($angle_rad);
			$rotated_points = array();
			
			foreach($points as $point) 
				$rotated_points[] = array(
						(integer) round(($point[0]*$angle_cos)-($point[1]*$angle_sin)),
						(integer) round(($point[0]*$angle_sin)+($point[1]*$angle_cos)));
			
			
			return $rotated_points;
			
		} else return $points;		
	}
	
	
	
	
	protected static function _get_coordenates_points($points){
		$x_points = array(); 
		$y_points = array();
		
		foreach($points as $point) {
			$x_points[] = $point[0];
			$y_points[] = $point[1];
		}
		
		return array($x_points, $y_points);
	}
	
	
	protected static function _get_coordenates_limits($points){
		list($x_points, $y_points) = self::_get_coordenates_points($points);
		return array( array(min($x_points), min($y_points)), array(max($x_points), max($y_points)));
	}
	
	
	protected static function _group_coordenates_points($points) {
		$grouped = array();
		for($i=0; $i<count($points); $i+=2) $grouped[] = array($points[$i], $points[$i+1]);
		return $grouped;
	}
	
	
	protected static function _ungroup_coordenates_prgboints($points) {
		$ungrouped = array();
		
		foreach($points as $point){
			$ungrouped[] = $point[0];
			$ungrouped[] = $point[1];
		}
		return $ungrouped;
	}
	
	
	protected static function _get_coordenates_size($points){
		list( list($min_x,$min_y), list($max_x, $max_y) ) = self::_get_coordenates_limits($points);
		return array($max_x-$min_x, $max_y-$min_y);
	}
	
	
	protected static function _expand_coordenates($points, $val){
		$val = array_values((array) $val);
		
		if(count($val) == 0) return $points;
		else while(count($val) < 4) $val = array_merge($val, $val);
		list($inc_top, $inc_right, $inc_bottom, $inc_left) = $val;
			
		if($inc_top || $inc_right || $inc_bottom || $inc_left) {
		
			if(!$cpoint) {
				list( list($min_x,$min_y), list($max_x, $max_y) ) = self::_get_coordenates_limits($points);
				$points_width = $max_x-$min_x;
				$points_height = $max_y-$min_y;
			
				$cpoint = array($min_x + ($points_width/2), $min_y + ($points_height/2)); 
				
			}
			
			foreach($points as $index=>$point){
				
				list($x,$y) = $point;
	
				if($x > $cpoint[0]) $x += $inc_right; 
				else if($x < $cpoint[0]) $x -= $inc_left;
							
				if($y > $cpoint[1]) $y += $inc_bottom; 
				else if($y < $cpoint[1]) $y -= $inc_top;
				
				$points[$index] = array($x,$y);
				
			}	
		}
		
		return $points;
	}
	
	
	
	//------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------
	
	
	
	protected static function _parse_font_size($val){
		$size = self::_parse_numeric_value($val, ZPHP_IMAGE_FONT_SIZE, true, false, self::$_font_size_constants);
		if(!$size) return ZPHP_IMAGE_FONT_SIZE;
		else return $size;
	}
	
	
	protected static function _parse_angle($val){
		$angle = self::_parse_numeric_value($val, 360, true, true, self::$_angle_constants);
		while($angle < 0) $angle += 360;
		while($angle > 360) $angle -= 360;
		return $angle;
	}
	
	
	
	
	//------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------
	
	
	protected static function _prepare_attrs($args1=null,$args2=null){
		
		$prepared_attrs=array(); 
		foreach(func_get_args() as $arg) {
						
			if(is_array($arg)) $attrs = $arg;
			else {
				
				$attrs = array();
				$attrs_parts = explode(';', (string) $arg);
				foreach($attrs_parts as $attr_str){
					list($attr_name, $attr_value) = explode(':', $attr_str, 2);
					
					if(is_null($attr_value)){
						if(array_key_exists(($prepared_attr_name = self::_prepare_attr_name($attr_name)), self::$styles))
							$attrs = array_merge($attrs, self::$styles[$prepared_attr_name]);
						
					} else $attrs[$attr_name] = trim($attr_value) == 'null' ? null : $attr_value;
				}
			
			}
			
			foreach($attrs as $key=>$value ) 
				if(is_numeric($key)) $prepared_attrs = array_merge($prepared_attrs, self::_prepare_attrs($value));
				else $prepared_attrs[self::_prepare_attr_name($key)] = $value;				
			
		}
		
		return $prepared_attrs;
	}

	
	protected static function _merge_attrs($args1=null,$args2=null){
		
		$attrs = array();
		foreach(func_get_args() as $args)
			$attrs = array_merge($attrs, call_user_func_array(array(self,'_prepare_attrs'), (array) $args));
		
		return $attrs;
	}
	
	
		
	protected static function _parse_image_type($type) {
		$type = str_replace( array('image/', '.',' '), '', strtolower(trim($type)));
		if(array_key_exists($type, self::$_types_aliases)) $type = self::$_types_aliases[$type];
		return in_array($type, self::$_valid_types) ? $type : null;
	}
	
	

	protected static function _get_image($image) {
		
		if($image && ($image instanceof Image)) return $image->_image;
			
		else if(is_string($image)) {
			
			if(is_file($image)) return @ imagecreatefromstring(file_get_contents($image)); 
			else return @ imagecreatefromstring($image);
									
		} else if(is_resource($image)) return $image;		
		
		else return null;
		
	}
	
	//------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------
	
	/**
	 * 
	 * @return Image
	 */
	public static function from_uploaded_file( $fileName, $index = null ) {

		if($_FILES[$fileName])
		{
			$tmp_name = $index === null ? $_FILES[ $fileName ]['tmp_name'] : $_FILES[ $fileName ]['tmp_name'][ $index ];
			if(is_uploaded_file( $tmp_name )) return new Image( $tmp_name );
		}
		else if($_POST[$fileName])
		{
			if(is_null($index))
			{
				$text = $_POST[$fileName];
			}
			else
			{
				$text = $_POST[$fileName][$index];
			}

			$text = preg_replace('#(?i)^data\:image\/.+?\;base64\,#', '', $text);
			$text = base64_decode($text);

			$image = new Image($text);
			return $image;

		}


		return false;
	}
	
	
	/**
	 * 
	 * @return Image
	 */
	public static function create($image=null, $style=null){
		
		return new Image($image, $style);
	}
	
	//------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------
	
	protected $_image;
	protected $_type;
	protected $_transparent_color;
	protected $_width;
	protected $_height;
	protected $_aspect;
	protected $_file;
	protected $_style;
	protected $_background;
	protected $_is_truecolor;
	protected $_quality = 0;
	
	protected $_cache_colors_walk_indexs = array();
	protected $_cache_colors_walk_count = 0;
	protected $_cache_colors_walk_callback;
	protected $_cache_colors_walk_parse = true;
	protected $_cache_colors_walk_additional_params = array();
	
	
	public function __construct($image=null, $style=null) {
				
		
		$attrs = self::$_default_image_attrs;
				
		if($image && ($image instanceof Image)) {
			
			$tmp_file = tempnam(sys_get_temp_dir(), 'image');
			@ imagepng($image->_image, $tmp_file);
			@ $string = file_get_contents($tmp_file); 
			@ unlink($tmp_file);
			
			@ $this->_image = imagecreatefromstring($string);
			$attrs['quality'] = $image->get_quality();
			$attrs['type'] = $image->get_type();
			$attrs['transparent_color'] = $image->get_transparent_color();
			$attrs['background'] = $image->get_background();
			
		} else if(is_string($image) && !is_numeric($image)) {
			
			if(is_file($image)) {
				
				@ $this->_image = imagecreatefromstring(file_get_contents($image)); 
				$image_info = getimagesize($image);
				$attrs['file'] = $image;
				$attrs['type'] = self::_parse_image_type($image_info['mime']);
				
			} else {
				
				@ $this->_image = imagecreatefromstring($image);
								
				$tmp_file = tempnam(sys_get_temp_dir(), 'image');
				@ file_put_contents($tmp_file, $image);
				$image_info = getimagesize($tmp_file);
				@ unlink($tmp_file);
			
				$attrs['type'] = self::_parse_image_type($image_info['mime']);
			}
			
		} else if(is_resource($image)) {

			
			$tmp_file = tempnam(sys_get_temp_dir(), 'image');
			@ imagepng($image, $tmp_file);
			@ $image_info = getimagesize($tmp_file);
			@ $string = file_get_contents($tmp_file);
			@ unlink($tmp_file);
			
			@ $this->_image = imagecreatefromstring($string); 
			$attrs['type'] = self::_parse_image_type($image_info['mime']);
						
			$this->_image = $image;
		
		} else {
			
			if(is_array($image)) $attrs = self::_prepare_attrs($attrs, $image);
			
			else if(is_numeric($image)) {
				
				$attrs['width'] = (integer) $image;
				$attrs['height'] = !is_null($style) ? ((integer) $style) : ((integer) $image);				
			}
			
			
			
			if(array_key_exists('size', $attrs)) {
				$size = $attrs['size'];
				if(is_array($size)){
					if(array_key_exists('width', $size) && array_key_exists('height', $size)) {
						$width = $arg1['width'];
						$height = $arg1['height'];
					} else list($width, $height) = array_values($size);
				
				} else {
					$width = $size;
					$height = $size;
				}
			} else {
				$width = $attrs['width'];
				$height = $attrs['height'];
			}
			
			$width = self::_parse_coordenate_value($width, 0);
			$height = self::_parse_coordenate_value($height, 0);
		
			$image_func = $attrs['truecolor'] ? 'imagecreatetruecolor' : 'imagecreate';
			@ $this->_image = $image_func($width,$height);
			
			if($attrs['background'] || $attrs['transparent_color'])
			@ imagefilledrectangle($this->_image,0,0,$width-1,$height-1,$this->_get_color_resource($attrs['background'] ? $attrs['background'] : $attrs['transparent_color']));
			
		}
		
		@ $this->_is_truecolor = imageistruecolor($this->_image);
		
		if(array_key_exists('file',$attrs)) $this->set_file($attrs['file']);
		if(array_key_exists('quality',$attrs)) $this->set_quality($attrs['quality']);
		if(array_key_exists('type',$attrs)) $this->set_type($attrs['type']);
		if(array_key_exists('transparent_color',$attrs)) $this->set_transparent_color($attrs['transparent_color']);
		if(array_key_exists('background',$attrs)) $this->set_background($attrs['background']);
		
		$this->_update_image_size();
		$this->_style = array();
		
		$args = func_get_args();
		$style_attrs = self::_merge_attrs(array_slice($args,1));			
		
		$this->set_style(self::$_default_style_attrs, $style_attrs);
		
	}
	
	
	//---------------------------------------------------------------------------
	
	public function __destruct() { $this->destroy(); }
	
	public function __toString() { return $this->get_string(); }
	
	//---------------------------------------------------------------------------
	//---------------------------------------------------------------------------
	
	protected function _destroy(){
		if($this->_image) @ imagedestroy($this->_image);
	}
	
	protected function _create($width, $height) {
		
		$image_func = $this->_is_truecolor ? 'imagecreatetruecolor' : 'imagecreate';
		@ $this->_image = $image_func($width,$height);
		
		$this->_width = $width;
		$this->_height = $height;
		$this->_aspect = $height>0 ? ($width/$height) : 0;
		
		$this->fill($this->_background);
		$this->_update_transparent_color();
		
	}
	
	//---------------------------------------------------------------------------
	//---------------------------------------------------------------------------
	
	
	protected function _get_color_resource( $color, $parse=true, $input_type='rgb' ) {
		if($parse) $color = self::_parse_color_rgb($color, $input_type);
		return @ imagecolorresolvealpha( $this->_image, $color[0], $color[1], $color[2], $color[3] );
		
	}
	
	
	protected function _update_image_size(){
		
		if($this->_image) {
			@ $this->_width = imagesx($this->_image);
			@ $this->_height = imagesy($this->_image);
			@ $this->_aspect = $this->_height>0 ? ($this->_width/$this->_height) : 0;
		} else {
			$this->_width = 0;
			$this->_height = 0;
			$this->_aspect = 0;
		}
	}
	
	
	protected function _update_transparent_color(){
		
		if($this->_image && $this->_transparent_color)
			@ imagecolortransparent( $this->_image, $this->_get_color_resource($this->_transparent_color, false) );
	}
	
	//---------------------------------------------------------------------------
	//---------------------------------------------------------------------------
	
	protected function _out( $file=null, $type=null ) {
		if(!$type) $type = $this->_type;
		if(((boolean) $type) && ((boolean) $this->_image)) {
			if($type == self::TYPE_JPEG) return @ imagejpeg( $this->_image, $file, $this->_quality);
			else if( $type == self::TYPE_GIF ) return @ imagegif( $this->_image, $file );
			else if( $type == self::TYPE_PNG ) return @ imagepng( $this->_image, $file );
			else if( $type == self::TYPE_BMP ) return @ imagewbmp( $this->_image, $file );
		} else return false;
	}
	
	
	//---------------------------------------------------------------------------
	//---------------------------------------------------------------------------
	
	
	protected function _prepare_bounds($pos, $size, $allow_overhead=false ){
		$size = self::_parse_coordenates($size, $this->get_size(), array('width','height'), $allow_overhead);
		$pos = self::_parse_coordenates($pos, array($this->get_width()-$size[0], $this->get_height()-$size[1]), array('x','y','left','top'), $allow_overhead);
		return array($pos,$size);
	}
	
	
	protected function _prepare_position($arg, $size_overhead=array(0,0), $allow_overhead=false) {
		return self::_parse_coordenates($arg, array($this->get_width()-$size_overhead[0], $this->get_height()-$size_overhead[1]), array('x','y','left','top'), $allow_overhead);
	}
	
	
	protected function _prepare_size_args($arg1,$arg2=null, $allow_overhead=true) {
		
		$arg = is_null($arg2) ? $arg1 : array('width'=>$arg1, 'height'=>$arg2);
		return self::_parse_coordenates($arg, $this->get_size(), array('width','height'), $allow_overhead);
	}
	
	
	protected function _size_resample($size, $dest_size=null, $src_size=null, $dest_pos=array(0,0), $src_pos=array(0,0)) {
				
		if(!$src_size) $src_size = $this->get_size();
		if(!$dest_size) $dest_size = $size;
				
		$image = $this->_image;
		$this->_create($size[0], $size[1]);
						
		@ imagecopyresampled( $this->_image, $image,$dest_pos[0],$dest_pos[1],$src_pos[0],$src_pos[1],$dest_size[0], $dest_size[1], $src_size[0], $src_size[1]);
		@ imagedestroy($image);
		return $this;
	}
	
	
	//---------------------------------------------------------------------------
	//---------------------------------------------------------------------------
	
	
	protected function _clear_colors_transform_callback(){
		$this->_cache_colors_walk_callback = null;
		$this->_cache_colors_walk_count = 0;
		$this->_cache_colors_walk_indexs = array();
		$this->_cache_colors_walk_parse = true;
		$this->_cache_colors_walk_additional_params = array();
	}
	
	
	protected function _prepare_colors_transform_callback($callback, $additional_params=array()){
		$this->_clear_colors_transform_callback();
		$this->_cache_colors_walk_callback = $callback;
		$this->_cache_colors_walk_additional_params = array_values($additional_params);
	}
	
	
	protected function _color_rgb_walk($x, $y){
	
		$color_index = imagecolorat($this->_image, $x, $y);

		if($this->_cache_colors_walk_count > self::$_cache_colors_walk_maxsize || !$this->_cache_colors_walk_indexs[$color_index]){
				
			$color = array_values(@ imagecolorsforindex($this->_image, $color_index));
			$new_color = call_user_func_array($this->_cache_colors_walk_callback, array_merge($color, $this->_cache_colors_walk_additional_params));
			
			
			if($this->_cache_colors_walk_parse){
				if(is_null($new_color)) return;
				
				$new_color[0] = self::_parse_color_value($new_color[0]);
				$new_color[1] = self::_parse_color_value($new_color[1]);
				$new_color[2] = self::_parse_color_value($new_color[2]);
				
				if(!isset($new_color[3])) $new_color[3] = $color[3];
				else $new_color[3] = self::_parse_color_value($new_color[3]);
			}
						
			$new_color_index = $this->_get_color_resource($new_color, false);
				
			if($this->_cache_colors_walk_count < self::$_cache_colors_walk_maxsize) {
				$this->_cache_colors_walk_count++;
				$this->_cache_colors_walk_indexs[$color_index] = $new_color_index;
			}
			
		} else $new_color_index = $this->_cache_colors_walk_indexs[$color_index];
								
		@ imagesetpixel($this->_image, $x, $y, $new_color_index);
	}
		
	
	protected function _color_hsv_walk($x, $y){
	
		$color_index = imagecolorat($this->_image, $x, $y);

		if($this->_cache_colors_walk_count > self::$_cache_colors_walk_maxsize || !$this->_cache_colors_walk_indexs[$color_index]){
				
			$color = self::_rgb2hsv(array_values(@ imagecolorsforindex($this->_image, $color_index)));
			$new_color = call_user_func_array($this->_cache_colors_walk_callback, array_merge($color, $this->_cache_colors_walk_additional_params));
		
			if($this->_cache_colors_walk_parse){
				
				if(is_null($new_color)) return;
				
				$new_color[0] = self::_parse_hue_value($new_color[0]);
				$new_color[1] = self::_parse_color_value($new_color[1]);
				$new_color[2] = self::_parse_color_value($new_color[2]);
				
				if(!isset($new_color[3])) $new_color[3] = $color[3];
				else $new_color[3] = self::_parse_color_value($new_color[3]);
			}
							
			$new_color_index = $this->_get_color_resource(self::_hsv2rgb($new_color), false);
				
			if($this->_cache_colors_walk_count < self::$_cache_colors_walk_maxsize) {
				$this->_cache_colors_walk_count++;
				$this->_cache_colors_walk_indexs[$color_index] = $new_color_index;
			}
			
		} else $new_color_index = $this->_cache_colors_walk_indexs[$color_index];
								
		@ imagesetpixel($this->_image, $x, $y, $new_color_index);
	}
	
	//---------------------------------------------------------------------------
	//---------------------------------------------------------------------------

	/**
	 * 
	 * @return Image
	 */
	public function set_quality($quality) {
		$this->_quality = abs((integer) $quality);
		return $this;
	}
	
	
	public function get_quality() { return $this->_quality; }
	
	/**
	 * 
	 * @return Image
	 */
	public function quality($quality=null){
		if(func_num_args()>0) return $this->set_quality($quality);
		else return $this->get_quality();
	}

	//---------------------------------------------------------------------------
	
	/**
	 * 
	 * @return Image
	 */
	public function set_type($type) {
		$this->_type = self::_parse_image_type($type);
		return $this;
	}
		
	
	public function get_type() { return $this->_type; }

	/**
	 * 
	 * @return Image
	 */
	public function type($type=null){
		if(func_num_args()>0) return $this->set_type($type);
		else return $this->get_type();
	}
	
	
	public function get_mimetype(){ return "image/{$this->_type}"; }
	
	//---------------------------------------------------------------------------
	
	/**
	 * 
	 * @return Image
	 */
	public function set_background($color) { 
		$this->_background = $color;
		return $this; 
	}
	
	
	public function get_background() { return $this->_background; }
	

	/**
	 * 
	 * @return Image
	 */
	public function background($color=null){
		if(func_num_args()>0) return $this->set_background($color);
		else return $this->get_background();
	}	
	//---------------------------------------------------------------------------
	
	/**
	 * 
	 * @return Image
	 */
	public function set_transparent_color($color) { 
		if($color) {
			$this->_transparent_color = self::_parse_color_rgb($color);
			$this->_update_transparent_color();
		}
		return $this; 
	}
	
	
	public function get_transparent_color() { return $this->_transparent_color; }
	
	/**
	 * 
	 * @return Image
	 */
	public function transparent_color($color=null){
		if(func_num_args()>0) return $this->set_transparent_color($color);
		else return $this->get_transparent_color();
	}
	
	/**
	 * 
	 * @return Image
	 */
	public function make_background_transparent(){
		return $this->set_transparent_color($this->_background);
	}
	
	//---------------------------------------------------------------------------
	
	/**
	 * 
	 * @return Image
	 */
	public function set_width($width, $mantain_aspect=false) {
		$width = self::_parse_coordenate_value($width, $this->_width, true);
		$height = (integer) (($mantain_aspect && $this->_aspect>0) ? ($width/$this->_aspect) : $this->_height);
		return $this->_size_resample(array($width, $height));
	}
	
	
	public function get_width(){ return $this->_width; }
	
	/**
	 * 
	 * @return Image
	 */
	public function width($width=null){
		if(func_num_args()>0) return $this->set_width($width);
		else return $this->get_width();
	}
	
	/**
	 * 
	 * @return Image
	 */
	public function set_height($height, $mantain_aspect=false) {
		$height = self::_parse_coordenate_value($height, $this->_height, true);
		$width = (integer) (($mantain_aspect && $this->_aspect>0) ? ($height*$this->_aspect) : $this->_width);
		return $this->_size_resample(array($width, $height));
	}
	
	
	public function get_height(){ return $this->_height; }
	
	/**
	 * 
	 * @return Image
	 */
	public function height($height=null){
		if(func_num_args()>0) return $this->set_height($height);
		else return $this->get_height();
	}
	
	/**
	 * 
	 * @return Image
	 */
	public function set_size($arg1,$arg2=null) {
			
		list($width,$height) = $this->_prepare_size_args($arg1,$arg2);
		return $this->_size_resample(array($width, $height));
	}
	
	
	/**
	 * 
	 * @return Image
	 */
	public function size($arg1=null,$arg2=null){
		if(func_num_args()>0) {
			$args = func_get_args();
		 	return call_user_func_array(array($this,'set_size'), $args);
		} else return $this->get_size();
	}
	
	
	public function get_aspect(){ return $this->_aspect; }
	
	//---------------------------------------------------------------------------
	
	/**
	 * 
	 * @return Image
	 */
	public function set_file($file, $save_now=false){
		$this->_file = $file;
		if($file && $save_now) $this->save();
		return $this;
	}
	
	
	public function get_file(){ return $this->_file; }
	
	/**
	 * 
	 * @return Image
	 */
	public function file($file=null){
		if(func_num_args()>0) return $this->set_file($file);
		else return $this->get_file();
	}
	
	//---------------------------------------------------------------------------
	//---------------------------------------------------------------------------
	
	/**
	 * 
	 * @return Image
	 */
	public function set_attr($name, $val=null) {
		if(is_array($name)) {
			$args = func_get_args();
			$attrs = self::_merge_attrs($args);
			
		} else $attrs = array(self::_prepare_attr_name($name)=>$val);
		
		foreach($attrs as $name=>$val)
			if(in_array($name, self::$_image_attrs))
				call_user_func(array($this, 'set_' . $name), $val);			
				
		return $this;
	}
	
	
	public function get_attr($name) {
		
		$name = self::_prepare_attr_name($name);
		if(in_array($name, self::$_image_attrs)){
			$args = func_get_args();
			return call_user_func_array(array($this, 'get_' . $name), array_slice($args,1));			
		} else return null;
		
	}
	
	/**
	 * 
	 * @return Image
	 */
	public function attr($name, $val=null) {
		if(func_num_args() == 1 && !is_array($name)) return $this->get_attr($name);
		else {
			$args = func_get_args();
			return call_user_func_array(array($this,'set_attr'), $args);
		}
	}
	
	
	//---------------------------------------------------------------------------
	//---------------------------------------------------------------------------
	
	/**
	 * 
	 * @return Image
	 */
	public function set_style($name, $val=null) {
		
		if(is_array($name)) {
			$args = func_get_args();
			$attrs = self::_merge_attrs($args);
			
		} else $attrs = array(self::_prepare_attr_name($name)=>$val);
		
		foreach($attrs as $name=>$val)
			if(in_array($name, self::$_style_attrs)){
				if(is_null($val)) unset($this->_style[$name]);
				else $this->_style[$name] = $val;
				
			}
				
		return $this;
	}
	
	
	public function get_style($name) {
		return $this->_style[self::_prepare_attr_name($name)];		
	}
	
	/**
	 * 
	 * @return Image
	 */
	public function style($name, $val=null) {
		if(func_num_args() == 1 && !is_array($name)) return $this->get_style($name);
		else {
			$args = func_get_args();
			return call_user_func_array(array($this,'set_style'), $args);
		}
	}
	
	
	//---------------------------------------------------------------------------
	//---------------------------------------------------------------------------
	
	public function is_truecolor() { return $this->_is_truecolor; }
	
	
	public function is_palette_based() { return !$this->is_truecolor();  }
	
	
	public function convert_truecolor() {
		
		if(!$this->is_truecolor()) {
			
			list($width, $height) = $this->get_size();
			$image = $this->_image;
			$this->_is_truecolor = true;
			$this->_create($width, $height);
						
			@ imagecopyresampled( $this->_image, $image, 0, 0, 0, 0, $width, $height, $width, $height);
			@ imagedestroy($image);
		
		}
		
		return $this;
	}
	
	
	public function convert_palette_based($colors=10240, $dithering=true) {
		if($this->is_truecolor()) {
			$this->_is_truecolor = false;
			@ imagetruecolortopalette($this->_image, $dithering, $colors);
		}
		
		return $this;
	}
	
	
	//---------------------------------------------------------------------------
	//---------------------------------------------------------------------------
	
	public function is_valid(){
		return ((boolean) $this->_type) && ((boolean) $this->_image);
	}
	
	public function get_string($type=null) {
				
		$type = $type ? self::_parse_image_type($type) : $this->_type;	
		
		$tmp_file = tempnam(sys_get_temp_dir(), 'image');
		$this->_out($tmp_file, $type);
		@ $string = file_get_contents($tmp_file);
		@ unlink($tmp_file);
				
		return $string;		
	}
	
	public function get_file_size($type=null) { 
		
		$type = $type ? self::_parse_image_type($type) : $this->_type;	
		
		$tmp_file = tempnam(sys_get_temp_dir(), 'image');
		$this->_out($tmp_file, $type);
		@ $file_size = filesize($tmp_file);
		@ unlink($tmp_file);
				
		return $file_size;		
	}

	//---------------------------------------------------------------------------
	
	public function save($type=null) { 
		return $this->save_copy($this->_file,$type);	
	}
	
	
	public function save_copy($file, $type=null) {
		$type = $type ? self::_parse_image_type($type) : $this->_type;	
		
		if($file) return $this->_out($file, $type);
		else return false;
	}
	
	
	public function save_as($type, $file=null) {
		return $this->save_copy($file ? $file : $this->_file,$type);
	}
	
	
	
	public function unlink_file(){
		if($this->_file) return @ unlink($this->_file);
		else return false;
	}
	
	/**
	 * 
	 * @return Image
	 */
	public function destroy($unlink_file=false) { 
		$this->_destroy();
		if($unlink_file) $this->unlink_file();
		return $this; 
	}
	
	/**
	 * 
	 * @return Image
	 */
	public function duplicate() {
		return new Image($this, $this->_style);		
	}
	
	//-------------------------------------------------------------------------
	//---------------------------------------------------------------------------
	
	/**
	 * 
	 * @return Image
	 */
	public function scale($arg1,$arg2=null){
		return $this->set_size($arg1,$arg2);
	}
	
	/**
	 * 
	 * @return Image
	 */
	public function scale_width($width,$mantain_aspect=true){
		return $this->set_width($width, $mantain_aspect);
	}
	
	/**
	 * 
	 * @return Image
	 */
	public function scale_height($height,$mantain_aspect=true){
		return $this->set_height($height, $mantain_aspect);
	}
	
	
	/**
	 * 
	 * @return Image
	 */
	public function scale_max_width($val){
		$val = (integer) $val;
		if($val>0 && $val<$this->_width) $this->set_width($val, true);
		return $this;
	}
	
	/**
	 * 
	 * @return Image
	 */
	public function scale_max_height($val){
		$val = (integer) $val;
		if($val>0 && $val<$this->_height) $this->set_height($val, true);
		return $this;
	}
	

	/**
	 * 
	 * @return Image
	 */
	public function scale_min_width($val){
		$val = (integer) $val;
		if($val>0 && $val>$this->_width) $this->set_width($val, true);
		return $this;
	}
	
	/**
	 * 
	 * @return Image
	 */
	public function scale_min_height($val){
		$val = (integer) $val;
		if($val>0 && $val>$this->_height) $this->set_height($val, true);
		return $this;
	}
	
	/**
	 * 
	 * @return Image
	 */
	public function scale_max_size($arg1, $arg2=null){
		list($width,$height) = $this->_prepare_size_args($arg1,$arg2);
		$this->scale_max_width($width);
		$this->scale_max_height($height);
		return $this;
	}
	
	/**
	 * 
	 * @return Image
	 */
	public function scale_min_size($arg1, $arg2=null){
		list($width,$height) = $this->_prepare_size_args($arg1,$arg2);
		$this->scale_min_width($width);
		$this->scale_min_height($height);
		return $this;
	}
	
	/**
	 * 
	 * @return Image
	 */
	public function set_canvas_size($arg1, $arg2=null){
		$canvas_size = $this->_prepare_size_args($arg1,$arg2);
		$dest_size=array(); $dest_pos=array(); $src_size=array(); $src_pos=array();
		$original_size = $this->size();
		
		foreach( array(0,1) as $index) 
			if($canvas_size[$index]<$original_size[$index]) {
				$dest_pos[$index] = 0;
				$dest_size[$index] = $canvas_size[$index];
				$src_pos[$index] = (integer) (($original_size[$index]-$canvas_size[$index]) / 2);	
				$src_size[$index] = $canvas_size[$index];
			} else {
				$src_pos[$index] = 0;
				$dest_pos[$index] = (integer) (($canvas_size[$index]-$original_size[$index]) / 2);
				$src_size[$index] = $original_size[$index];
				$dest_size[$index] = $original_size[$index];	
			}
			
		return $this->_size_resample($canvas_size, $dest_size, $src_size, $dest_pos, $src_pos);	
	}
	
	/**
	 * 
	 * @return Image
	 */
	public function set_canvas_width($val, $mantain_aspect=false){
		return $this->set_canvas_size($val, (integer) (($mantain_aspect && $this->_aspect>0) ? ($val/$this->_aspect) : $this->_height));
	}
	
	/**
	 * 
	 * @return Image
	 */
	public function set_canvas_height($val, $mantain_aspect=false){
		return $this->set_canvas_size((integer)($mantain_aspect ? ($val*$this->_aspect) : $this->_width), $val);
	}
	
	
	/**
	 * 
	 * @return Image
	 */
	public function fill_canvas_size($arg1, $arg2=null){
		
		$canvas_size = $this->_prepare_size_args($arg1,$arg2);
		$original_size = $this->size();
		
		$diffs = array( abs($canvas_size[0]-$original_size[0]), abs($canvas_size[1]-$original_size[1]) );
		
		$pivot_index = 0;
		$dest_size = array();
		$dest_size[$pivot_index] = floor($canvas_size[$pivot_index]);
		$dest_size[!$pivot_index] = floor($dest_size[$pivot_index] * ($pivot_index == 0 ? ( $this->_aspect > 0 ? (1/$this->_aspect) : 0 ) : $this->_aspect));
		
		if($dest_size[0] < $canvas_size[0] || $dest_size[1] < $canvas_size[1]) {
			
			$pivot_index = 1;
			$dest_size = array();
			$dest_size[$pivot_index] = floor($canvas_size[$pivot_index]);
			$dest_size[!$pivot_index] = floor($dest_size[$pivot_index] * ($pivot_index == 0 ? ( $this->_aspect > 0 ? (1/$this->_aspect) : 0 ) : $this->_aspect));
			
		}

		
		$dest_pos = array();
		$dest_pos[$pivot_index] = 0;
		$dest_pos[!$pivot_index] = floor((integer) (($canvas_size[!$pivot_index]-$dest_size[!$pivot_index])/2));
		//$dest_pos[!$pivot_index] = 0;
				
		return $this->_size_resample($canvas_size, $dest_size, null, $dest_pos);	
	}
	
	
	/**
	 * 
	 * @return Image
	 */
	public function fill_canvas_width($val, $mantain_aspect=false){
		return $this->fill_canvas_size($val, (integer) (($mantain_aspect && $this->_aspect>0) ? ($val/$this->_aspect) : $this->_height));
	}
	
	/**
	 * 
	 * @return Image
	 */
	public function fill_canvas_height($val, $mantain_aspect=false){
		return $this->fill_canvas_size((integer)($mantain_aspect ? ($val*$this->_aspect) : $this->_width), $val);
	}
	
	
	/**
	 * 
	 * @return Image
	 */
	public function fit_canvas_size($arg1, $arg2=null){
		$canvas_size = $this->_prepare_size_args($arg1,$arg2);
		$original_size = $this->size();
		
		$diffs = array( abs($canvas_size[0]-$original_size[0]), abs($canvas_size[1]-$original_size[1]) );
		$pivot_index = $diffs[0]>$diffs[1] ? 0 : 1;
		if($original_size[0] < $original_size[1]) $pivot_index = !$pivot_index;
		
		$src_size = array();
		$src_size[$pivot_index] = $original_size[$pivot_index];
		$src_size[!$pivot_index] = $src_size[$pivot_index] * ($pivot_index == 0 ? ( $canvas_size[1] > 0 ? ($canvas_size[0]/$canvas_size[1]) : 0 ) : ($canvas_size[1]/$canvas_size[0]));
		
		$src_pos = array();
		$src_pos[$pivot_index] = 0;
		$src_pos[!$pivot_index] = (integer) (($original_size[!$pivot_index]-$src_size[!$pivot_index])/2);
				
		return $this->_size_resample($canvas_size, null, $src_size, array(0,0), $src_pos);	
	}
	
	
	/**
	 * 
	 * @return Image
	 */
	public function fit_canvas_width($val, $mantain_aspect=false){
		return $this->fit_canvas_size($val, (integer) (($mantain_aspect && $this->_aspect>0) ? ($val/$this->_aspect) : $this->_height));
	}
	
	/**
	 * 
	 * @return Image
	 */
	public function fit_canvas_height($val, $mantain_aspect=false){
		return $this->fit_canvas_size((integer)($mantain_aspect ? ($val*$this->_aspect) : $this->_width), $val);
	}
	
	/**
	 * 
	 * @return Image
	 */
	public function trim( $pos, $size ) {
		
		list($pos,$size) = $this->_prepare_bounds($pos, $size, false);
		return $this->_size_resample($size, $size, $size, array(0,0), $pos);
		
	}

	/**
	 *
	 * @return Image
	 */
	public function crop($pos, $size) {

		$args = func_get_args();
		return call_user_func_array(array($this, 'trim'), $args);

	}
	
	
	//---------------------------------------------------------------------------
	//---------------------------------------------------------------------------
	
	/**
	 * 
	 * @return Image
	 */
	public function copy( $pos=0, $size='full' ) {
		
		$new_image = $this->duplicate();
		$new_image->trim($pos, $size);
		return $new_image;
	}
	
	
	
	/**
	 * 
	 * @return Image
	 */
	function paste($image, $pos, $size='full') {
		
		$image = self::_get_image($image);

		if($image) {
			$image_width = imagesx($image);
			$image_height = imagesy($image);
			
			list($dest_width, $dest_height) = self::_parse_coordenates($size, array($image_width, $image_height), array('width','height'), true);
			list($x, $y) = self::_parse_coordenates($pos, array($this->_width-$dest_width, $this->_height-$dest_height), array('x','y','left','top'), false);
						
			imagecopyresized($this->_image, $image, $x, $y, 0, 0, $dest_width, $dest_height, $image_width, $image_height);
		}

		return $this;
	}
	
	
	
	//---------------------------------------------------------------------------
	//---------------------------------------------------------------------------
	
	/**
	 * 
	 * @return Image
	 */
	public function fill($color){
		if($color) @ imagefilledrectangle($this->_image,0,0,$this->_width,$this->_height, $this->_get_color_resource($color));
		return $this;		
	}
	
	/**
	 * 
	 * @return Image
	 */
	public function draw_border($color=null, $line_width=1) {
		
		if($line_width <= 0) return $this;
		$line_color = self::_get_attr_value(array('line_color','color'),$color?array('line_color'=>$color):array(), $this->_style, array('line_color'=>ZPHP_IMAGE_COLOR) );
		@ imagesetthickness( $this->_image, $line_width );
		@ imagerectangle($this->_image,$line_width/2,$line_width/2,$this->_width-$line_width,$this->_height-$line_width, $this->_get_color_resource($line_color));
		
		return $this;
	}
	
	//---------------------------------------------------------------------------
	
	/**
	 * 
	 * @return Image
	 */
	public function draw_rectangle($pos, $size, $style=null) {
		
		list(list($x,$y), list($width,$height)) = $this->_prepare_bounds($pos, $size);
		
		$args = func_get_args();
		$user_style = self::_merge_attrs(array_slice($args,2));
		
		$line_width = (integer) self::_get_attr_value('line_width', $user_style, $this->_style);
		$background = self::_get_attr_value(array('fill_color','background'), $user_style, $this->_style);
		$line_color = self::_get_attr_value(array('line_color','color'), $user_style, $this->_style);
		
		@ imagesetthickness( $this->_image, $line_width );

		if($background) 
		@ imagefilledrectangle($this->_image,$x,$y,$x+$width,$y+$height, $this->_get_color_resource($background));
		

		if($line_width) 
		@ imagerectangle($this->_image,$x+$line_width-1,$y+$line_width-1,$x+$width-$line_width+1,$y+$height-$line_width+1, $this->_get_color_resource($line_color));
		
		return $this;
	}
	
	
	/**
	 * 
	 * @return Image
	 */
	public function draw_line( $pos1, $pos2, $style=null ) {
		
		$args = func_get_args();
		$user_style = self::_merge_attrs(array_slice($args,2));
		
		$line_width = (integer) self::_get_attr_value('line_width', $user_style, $this->_style);
		$line_color = self::_get_attr_value(array('line_color','color'), $user_style, $this->_style);
				
		list($x1,$y1) = $this->_prepare_position($pos1);
		list($x2,$y2) = $this->_prepare_position($pos2);
		
		@ imagesetthickness( $this->_image, $line_width );
		@ imageline( $this->_image, $x1, $y1, $x2, $y2, $this->_get_color_resource($line_color));
		return $this;		
	}
	
	
	/**
	 * 
	 * @return Image
	 */
	public function draw_line_polar( $pos, $angle, $width, $style=null ) {
		
		$args = func_get_args();
		$args = func_get_args();
		$user_style = self::_merge_attrs(array_slice($args,3));
		
		$line_width = (integer) self::_get_attr_value('line_width', $user_style, $this->_style);
		$line_color = self::_get_attr_value(array('line_color','color'), $user_style, $this->_style);
		
		$angle = deg2rad( $angle );
		$width = self::_parse_coordenate_value($width, max( $this->_width, $this->_height ));	
		
		$x_offset = $width * cos( $angle );
		$y_offset = sqrt( pow( $width, 2 ) - pow( $x_offset, 2 ) );
		
		list($x,$y) = $this->_prepare_position($pos);
		
		@ imagesetthickness( $this->_image, $line_width );
		@ imageline( $this->_image, $x, $y, $x_offset + $x, $y_offset + $y, $this->_get_color_resource($line_color));
		return $this;		
				
		
	}
	
	/**
	 * 
	 * @return Image
	 */
	public function draw_line_horizontal($y, $width='full', $style=null ) {
		
		$args = func_get_args();
		$user_style = self::_merge_attrs(array_slice($args,2));
		
		$line_width = (integer) self::_get_attr_value('line_width', $user_style, $this->_style);
		$line_color = self::_get_attr_value(array('line_color','color'), $user_style, $this->_style);
				
		$width = self::_parse_coordenate_value($width, $this->_width);
		$y = self::_parse_coordenate_value($y, $this->_height-$line_width);
		$x = ($this->_width-$width) / 2;
				
		@ imagesetthickness( $this->_image, $line_width );
		@ imageline( $this->_image, $x, $y, $x + $width, $y, $this->_get_color_resource($line_color));
		return $this;		
	}
	

	/**
	 * 
	 * @return Image
	 */
	public function draw_line_vertical($x, $height='full', $style=null ) {
		
		$args = func_get_args();
		$user_style = self::_merge_attrs(array_slice($args,2));
		
		$line_width = (integer) self::_get_attr_value('line_width', $user_style, $this->_style);
		$line_color = self::_get_attr_value(array('line_color','color'), $user_style, $this->_style);
				
		$height = self::_parse_coordenate_value($height, $this->_height);
		$x = self::_parse_coordenate_value($x, $this->_width-$line_width);
		$y = ($this->_height-$height) / 2;
				
		@ imagesetthickness( $this->_image, $line_width );
		@ imageline( $this->_image, $x, $y, $x, $y + $height, $this->_get_color_resource($line_color));
		return $this;		
	}
	
	/**
	 * 
	 * @return Image
	 */
	public function draw_ellipse($pos, $size, $style=null) {
		
		list(list($x,$y), list($width,$height)) = $this->_prepare_bounds($pos, $size);
		
		$args = func_get_args();
		$user_style = self::_merge_attrs(array_slice($args,2));
		
		$line_width = (integer) self::_get_attr_value('line_width', $user_style, $this->_style);
		$background = self::_get_attr_value(array('fill_color','background'), $user_style, $this->_style);
		$line_color = self::_get_attr_value(array('line_color','color'), $user_style, $this->_style);
		
		@ imagesetthickness( $this->_image, $line_width );
						
		if($background) 
		@ imagefilledellipse($this->_image,$x+($width/2),$y+($height/2),$width,$height, $this->_get_color_resource($background));
		
		if($line_width) 
		@ imageellipse($this->_image,$x+($width/2),$y+($height/2),$width,$height, $this->_get_color_resource($line_color));
		
		return $this;
	}
	
	
	/**
	 * 
	 * @return Image
	 */
	public function draw_point($pos, $diam=2, $color=null ) {
		
		if($diam <= 0) return $this;
		$line_color = self::_get_attr_value(array('line_color','color','fill_color'),$color?array('line_color'=>$color):array(), $this->_style, array('line_color'=>ZPHP_IMAGE_COLOR) );
		list($x,$y) = $this->_prepare_position($pos);
		@ imagefilledellipse($this->_image,(integer) $x+($diam/2),(integer) $y+($diam/2), $diam, $diam, $this->_get_color_resource($line_color));
		return $this;
		
	}
	
	
	
	
	/**
	* 
	* @return Image
	*/
	public function draw_arc($pos, $size, $start, $long, $style=null) {
		
		list(list($x,$y), list($width,$height)) = $this->_prepare_bounds($pos, $size);
		
		$args = func_get_args();
		$user_style = self::_merge_attrs(array_slice($args,4));
		
		$line_width = (integer) self::_get_attr_value('line_width', $user_style, $this->_style);
		$background = self::_get_attr_value(array('fill_color','background'), $user_style, $this->_style);
		$line_color = self::_get_attr_value(array('line_color','color'), $user_style, $this->_style);
		
		$border = (boolean) self::_get_attr_value('border', $user_style, $this->_style, array('border'=>true));
		$border_arc = $border && ((boolean) self::_get_attr_value('border_arc', $user_style, $this->_style, array('border_arc'=>true)));
		$border_start = $border && ((boolean) self::_get_attr_value('border_start', $user_style, $this->_style, array('border_start'=>true)));
		$border_end = $border && ((boolean) self::_get_attr_value('border_end', $user_style, $this->_style, array('border_end'=>true)));
		
		$angle_start = (self::_parse_coordenate_value($start, 360, true, true)) % 360;
		while($angle_start > 360) $angle_start -= 360;
		while($angle_start < 0) $angle_start += 360;
		
		$long = self::_parse_coordenate_value($long, 360, true, true);
		if($long > 360) $long = 360; 
		elseif($long < -360) $long = -360;
		
		if($long>=0) {
			$angle_end = ($angle_start+$long) % 360;
			$arc_start = 360 - $angle_end;
			$arc_end = 360 - $angle_start;
		}
		else {
			$arc_start = $angle_start;
			$arc_end = ($angle_start-$long) % 360;
			$angle_end = 360 - $angle_end;
		}
		
		@ imagesetthickness( $this->_image, $line_width );
		
		$cx = $x + (integer) ($width/2); 
		$cy = $y + (integer) ($height/2);
		
							
		if($background) 
		@ imagefilledarc($this->_image,$cx,$cy,$width,$height, $arc_start, $arc_end, $this->_get_color_resource($background), IMG_ARC_ROUNDED);
		
		if($line_width && ($border_arc || $border_end || $border_start)) {
			$line_color = $this->_get_color_resource($line_color);
			
			if($border_arc) @ imagearc($this->_image,$cx,$cy,$width,$height,$arc_start,$arc_end, $line_color);
			
			if($border_start || $border_end) {
				
				$r_width = ($width/2)-$line_width;
				$r_height = ($height/2)-$line_width;
				
				if($border_start) @ imageline($this->_image, $cx, $cy, $cx+(cos(deg2rad($angle_start))*$r_width), $cy-(sin(deg2rad($angle_start))*$r_height), $line_color);
				if($border_end) @ imageline($this->_image, $cx, $cy, $cx+(cos(deg2rad($angle_end))*$r_width), $cy-(sin(deg2rad($angle_end))*$r_height), $line_color);
				
			}
			
		}
		
		return $this;
	}
	
		
	/**
	 * 
	 * @return Image
	 */
	public function draw_polygon($pos, $points, $style=null) {
		
		$args = func_get_args();
		$user_style = self::_merge_attrs(array_slice($args,1));
		
		$line_width = (integer) self::_get_attr_value('line_width', $user_style, $this->_style);
		$background = self::_get_attr_value(array('fill_color','background'), $user_style, $this->_style);
		$line_color = self::_get_attr_value(array('line_color','color'), $user_style, $this->_style);
		list($padding_top,$padding_right,$padding_bottom,$padding_left) = self::_get_box_value('padding', $user_style, $this->_style);
		
		$num_points = count($points);
		$points = array_map(array(Graphic, '_parse_point'), $points);
		
		self::_expand_coordenates($points,array($padding_top + $line_width, $padding_right-$line_width, $padding_bottom-$line_width, $padding_left+$line_width));
		
		/*
		list(list($min_x,$min_y), list($max_x, $max_y)) = self::_get_coordenates_limits($points);
		
		$polygon_width = $max_x-$min_x;
		$polygon_height = $max_y-$min_y;
		
		$cx = $min_x + ($polygon_width/2); 
		$cy = $min_y + ($polygon_height/2);
		
		$points = self::_expand_coordenates(array($padding_top + $line_width, $padding_right-$line_width, $padding_bottom-$line_width, $padding_left+$line_width), $points, array($cx, $cy));

		$total_width = $polygon_width+$padding_left+$padding_right; 
		$total_height = $polygon_height+$padding_top+$padding_bottom;
		*/
		//list($x_offset,$y_offset) = self::_parse_coordenates($pos, array($this->_width-$total_width, $this->_height-$total_height), array('x','y','left','top'));
		
		$polygon_size = self::_get_polygon_size($points);
		$draw_pos = $this->_prepare_position($pos);
		$inner_dev = self::_parse_coordenates($style['point'], $polygon_size, array('x','y','left','top'), false);
		//$text_dev = $text_box['dev_point'];
		
		$points = self::_move_coordenates(array($draw_pos[0] - $inner_dev[0], $draw_pos[1] - $inner_dev[1]), $points);
		
		$draw_points = array();
		foreach($points as $point) {
			$draw_points[] = $point[0];
			$draw_points[] = $point[1];
		}
		
		@ imagesetthickness( $this->_image, $line_width );
						
		if($background) 
		@ imagefilledpolygon($this->_image, $draw_points, $num_points,$this->_get_color_resource($background));
		
		if($line_width) 
		@ imagepolygon($this->_image, $draw_points, $num_points,$this->_get_color_resource($line_color));
		
		return $this;
	}
	
	//---------------------------------------------------------------------------
	//---------------------------------------------------------------------------
	
	public function get_text_size($text, $style=null) {

		$args = func_get_args();
		$style = array_merge($this->_style, self::_merge_attrs(array_slice($args, 1)));
				
		return self::_get_text_box($text, $style, 'text_size');
	}
	
	
	public function get_text_width( $text, $style = null ) {
		$args = func_get_args();		
		$size = call_user_func_array(array($this,'get_text_size'), $args);
		return $size[0];
	}
	
	
	public function get_text_height( $text, $style = null ) {
		$args = func_get_args();		
		$size = call_user_func_array(array($this,'get_text_size'), $args);
		return $size[1];
	}
	
	
	public function get_text_box($text, $style=null) {

		$args = func_get_args();
		$user_style = self::_merge_attrs(array_slice($args, 1));
		$style = array_merge($this->_style, $user_style);
		$text_box = self::_get_text_box($text, $style);
		//$points = self::_expand_coordenates(self::_get_box_value('padding', $user_style, $this->_style), $text_box['points'], array($text_box['cx'], $text_box['cy']));
		$points = $text_box['points'];
		
		//if(($angle = self::_parse_angle($style['angle']))) 
		//	$points = self::_rotate_coordenates($angle, $points);
			
		return $points;
	}
	
	public function get_text_box_size($text, $style=null) {
		$args = func_get_args();		
		$points = call_user_func_array(array($this,'get_text_box'), $args);
		return self::_get_coordenates_size($points);
	}
	
	
	public function get_text_box_width( $text, $style = null ) {
		$args = func_get_args();		
		$size = call_user_func_array(array($this,'get_text_box_size'), $args);
		return $size[0];
	}
	
	
	public function get_text_box_height( $text, $style = null ) {
		$args = func_get_args();		
		$size = call_user_func_array(array($this,'get_text_box_size'), $args);
		return $size[1];
	}
	//---------------------------------------------------------------------------
	//---------------------------------------------------------------------------
	
	/**
	 * 
	 * @return Image
	 */
	public function draw_text($pos, $text, $style=null) {
		
		$args = func_get_args();
		$style = array_merge($this->_style,self::_merge_attrs(array_slice($args,2)));

		$text_box = self::_get_text_box($text, $style);
				
		$draw_pos = $this->_prepare_position($pos);
		$inner_dev = self::_parse_coordenates($style['point'], $text_box['text_size'], array('x','y','left','top'), false);
		$text_dev = $text_box['dev_point'];
				
		$pos = array();
		foreach(array(0,1) as $i) $pos[] = $draw_pos[$i] + $text_dev[$i] - $inner_dev[$i];
		
		$color = $this->_get_color_resource($style['color']);
		@ imagettftext( $this->_image, $text_box['font_size'], $text_box['angle'], $pos[0], $pos[1], $color, $text_box['font_file'], $text);
		return $this;
	}
	
	
	/**
	 * 
	 * @return Image
	 */
	public function draw_text_box($pos, $text, $style=null) {
		
		$args = func_get_args();
		$user_style = self::_merge_attrs(array_slice($args, 2));
		$style = array_merge($this->_style, $user_style);
		
		$line_width = (integer) self::_get_attr_value('line_width', $user_style, $this->_style);
		$background = self::_get_attr_value(array('fill_color','background'), $user_style, $this->_style);
		$line_color = self::_get_attr_value(array('line_color'), $user_style, $this->_style);
		list($padding_top,$padding_right,$padding_bottom,$padding_left) = self::_get_box_value('padding', $user_style, $this->_style);
				
		$text_box = self::_get_text_box($text, $style);
			
		$box_points = self::_expand_coordenates(array($padding_top-($line_width/2), $padding_right+$line_width, $padding_bottom+$line_width, $padding_left-($line_width)), $text_box['points'], array($text_box['cx'], $text_box['cy']));
		list(list($min_x,$min_y),list($max_x,$max_y)) = self::_get_coordenates_limits($box_points);		
		
		//if(($angle = self::_parse_angle($style['angle']))) {
		//	$box_points = self::_rotate_coordenates(360-$angle, $box_points);
		//	$text_points = self::_rotate_coordenates(360-$angle, $text_box['points']);
		//	$text_size = self::_get_coordenates_size($text_points);
			
		//} else $text_size = $text_box['text_size']; 

		$text_size = $text_box['text_size']; 
		
		
		list($box_width, $box_height) = self::_get_coordenates_size($box_points);
		$num_box_points = count($box_points);
		
				
		list($x_pos,$y_pos) = self::_parse_coordenates($pos, array($this->_width-$box_width, $this->_height-$box_height), array('x','y','left','top'), true, true);
		list($x_pos,$y_pos) = self::_parse_coordenates($pos, array((integer) $this->_width-($box_width+$line_width), (integer) $this->_height-($box_height+$line_width)), array('x','y','left','top'), true, true);
		
		list(list($min1_x,$min1_y),list($max1_x,$max1_y)) = self::_get_coordenates_limits($box_points);
		$cx1 = $min1_x + (($max1_x-$min1_x)/2);
		$cy1 = $min1_y + (($max1_y-$min1_y)/2);
		$cx = $min_x + (($max_x-$min_x)/2);
		$cy = $min_y + (($max_y-$min_y)/2);
				
		$box_points = self::_move_coordenates(array(($cx-$cx1)/2, ($cy1-$cy)/2), $box_points);
		
		list($padding_left_r, $padding_top_r) = self::_rotate_point($angle, array($padding_left, $padding_top));
		
		$box_points = self::_move_coordenates(array($x_pos+$padding_left_r-($line_width/2), $y_pos+$box_height-($line_width/2)-$padding_top_r), $box_points);

		$text_pos = $box_points[0];
		$text_pos[1] -= $line_width;
		
		$box_points = self::_ungroup_coordenates_points($box_points);
					
		@ imagesetthickness( $this->_image, $line_width );
						
		if($background) 
		@ imagefilledpolygon($this->_image, $box_points, $num_box_points,$this->_get_color_resource($background));
		
		if($line_width) 
		@ imagepolygon($this->_image, $box_points, $num_box_points,$this->_get_color_resource($line_color));
				
		list($padding_left_r, $padding_bottom_r) = self::_rotate_point($angle, array($padding_left, $padding_bottom));
		
		$color = $this->_get_color_resource($style['color']);
		@ imagettftext( $this->_image, $text_box['font_size'], $angle, $text_pos[0] + $padding_left_r, $text_pos[1] - $padding_bottom_r, $color, $text_box['font_file'], $text);
		return $this;
	}
	
	
	//------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------
	
	public function get_color_rgb_at($arg1, $arg2=null){
		
		list($x, $y) = $this->_prepare_position(is_null($arg2) ? $arg1 : array('x'=>$arg1, 'y'=>$arg2));

		@ $color_index = imagecolorat($this->_image, $x, $y);
		return @ array_values(imagecolorsforindex($this->_image, $color_index));
	}
	

	public function get_color_hsv_at($arg1, $arg2=null){
		$args = func_get_args();
		$rgb = call_user_func_array(array($this, 'get_color_rgb_at'), $args);
		return self::_rgb2hsv($rgb);
	}
	
	public function get_color_at($arg1, $arg2=null){
		$args = func_get_args();
		return call_user_func_array(array($this, 'get_color_rgb_at'), $args);
	}
	
	
	public function count_colors(){
		return @ imagecolorstotal($this->_image);
	}
	
	//---------------------------------------------------------------------
	
	public function set_color_rgb_points($points, $color){
		$color = $this->_get_color_resource($color);
		foreach((array) $points as $point){
			list($x,$y) = $this->_prepare_position($point);
			@ imagesetpixel($this->_image, $x, $y, $color);
		}
		
		return $this;
	}
	
	
	public function set_color_hsv_points($points, $color){
		$color = $this->_get_color_resource($color, 'hsv');
		foreach((array) $points as $point){
			list($x,$y) = $this->_prepare_position($point);
			@ imagesetpixel($this->_image, $x, $y, $color);
		}
		
		return $this;
	}
	
	
	public function set_color_points($points, $color){
		return $this->set_color_rgb_points($points, $color);
	}
	
	
	public function set_color_rgb_at($arg1,$arg2,$arg3=null){
		$num_args = func_num_args();
		
		if($num_args > 2) {
			$pos = array($arg1, $arg2);
			$color = $arg3;	
		} else {
			$pos = $arg1;
			$color = $arg2;
		}
		
		return $this->set_color_rgb_points(array($pos), $color);
	}
	
	
	public function set_color_hsv_at($arg1,$arg2,$arg3=null){
		$num_args = func_num_args();
		
		if($num_args > 2) {
			$pos = array($arg1, $arg2);
			$color = $arg3;	
		} else {
			$pos = $arg1;
			$color = $arg2;
		}
		
		return $this->set_color_hsv_points(array($pos), $color);
	}
	
	
	public function set_color_at($arg1,$arg2,$arg3=null){
		$args = func_get_args();
		return call_user_func_array(array($this, 'set_color_rgb_at'), $args);
	}
	
	
	//---------------------------------------------------------------------
	//---------------------------------------------------------------------
	
	public function walk($callback, $parameters=null) {
		
		$args = func_get_args();
		$additional_params = array_slice($args, 1);
		
		for($y=0; $y<$this->_height; $y++)
			for($x=0; $x<$this->_width; $x++)
				if(@ call_user_func_array($callback, array_merge(array($x, $y), $additional_params)) === false) break;
				
		return $this;		
	}
	
	
	public function walk_colors_rgb($callback, $parameters=null){
		$args = func_get_args();
		$this->_prepare_colors_transform_callback($callback, array_slice($args, 1));
		$this->walk(array($this, '_color_rgb_walk'));
		$this->_clear_colors_transform_callback();
		return $this; 
	}
	
	
	public function walk_colors_hsv($callback, $parameters=null){
		$args = func_get_args();
		$this->_prepare_colors_transform_callback($callback, array_slice($args, 1));
		$this->walk(array($this, '_color_hsv_walk'));
		$this->_clear_colors_transform_callback();
		return $this; 
	}
	
	
	public function walk_colors($callback, $parameters=null){
		$args = func_get_args();
		return call_user_method_array(array($this, 'walk_colors_rgb'), $args);
	}

	
	//------------------------------------------------------------------------------------------------------------
	//------------------------------------------------------------------------------------------------------------

	public function set_saturation($saturation){
		$this->_cache_colors_walk_parse = false;
		$saturation = self::_parse_color_value($saturation);
		$callback = create_function('$h,$s,$v,$a','return array($h,'.var_export($saturation, true).',$v,$a);');
		return $this->walk_colors_hsv($callback);
	}
		
	
	public function add_saturation($saturation){
		$saturation = self::_parse_numeric_value($saturation, 255, false, true);
		$callback = create_function('$h,$s,$v,$a','return array($h,$s+'.var_export($saturation, true).',$v,$a);');
		return $this->walk_colors_hsv($callback);
	}
	
	
	
	public function set_brightness($value){
		$this->_cache_colors_walk_parse = false;
		$value = self::_parse_color_value($value);
		$callback = create_function('$h,$s,$v,$a','return array($h,$s,'.var_export($value, true).',$a);');
		return $this->walk_colors_hsv($callback);
	}
		
	
	public function add_brightness($value){
		$value = self::_parse_numeric_value($value, 255, false, true);
		$callback = create_function('$h,$s,$v,$a','return array($h,$s,$v+'.var_export($value, true).',$a);');
		return $this->walk_colors_hsv($callback);
	}
	
	
	public function set_hue($hue){
		$this->_cache_colors_walk_parse = false;
		$hue = self::_parse_hue_value($hue);
		$callback = create_function('$h,$s,$v,$a','return array('.var_export($hue, true).',$s,$v,$a);');
		return $this->walk_colors_hsv($callback);
	}
	
		
	public function colorize($color){
		$this->_cache_colors_walk_parse = false;
		list($h,$s,$v) = self::get_color_hsv($color);
		$callback = create_function('$h,$s,$v,$a',"return array({$h},99,min({$v},\$v),\$a);");
		return $this->walk_colors_hsv($callback);
	}
	
	
	public function apply_threshold($max_value=0.5){
		
		$max_value = self::_parse_color_value($max_value);
		$this->_cache_colors_walk_parse = false;
		
		$callback = create_function('$h,$s,$v,$a',"if(\$v>{$max_value}) \$v= 255; return array(0,0,\$v,\$a);");
		return $this->walk_colors_hsv($callback);
	}
	
	
	public function convert_grayscale(){
		return $this->set_saturation(0);
	}

	
	
		public function out() {
	
		if($this->is_valid()) {
			
			@ header("Content-Type: image/{$this->_type}");
			@ header('Cache-Control: no-cache; must-revalidate');
		
			return $this->_out(null,$this->_type);
		} 
	}
	
	
	public function save_to($filename) {
		return $this->save_copy($filename);
	}
	
	public function out_attachment($filename=null) {
		
		if($this->is_valid()) {
			
			@ header("Content-Type: image/{$this->_type}");
				
			$filename = trim($filename);

			if(!$filename) {
				$filename = $this->_file;
			} 
			
			if(!$filename) {
				$filename = 'image.'.MimeTypeHelper::get_extension("image/{$this->_type}");
			} 

			NavigationHelper::header_content_attachment($filename);
			NavigationHelper::header_content_length($this->get_file_size($this->_type));

			@ header('Cache-Control: no-cache; must-revalidate'); 
		
			return $this->_out(null,$this->_type);
		} 
	}

	
	public function get_base64_contents($img_src=false) {

		if(!$img_src)
		{
			ob_start();

			$this->_out(null, $this->_type);

			$contents = ob_get_clean();

			return base64_encode($contents);
		}
		else
		{
			return 'data:image/'.$this->get_type().';base64,'.$this->get_base64_contents();
		}
		
	}

}

//------------------------------------------------------------------------------------------------------------
// Constantes de Estilos
//------------------------------------------------------------------------------------------------------------

Image::$styles['line_double'] = array('line_width' => 2);
Image::$styles['thin'] = array('line_width' => 1);
Image::$styles['wide'] = array('line_width' => 3);
Image::$styles['bold'] = array('bold' => true);
Image::$styles['italic'] = array('italic' => true);
Image::$styles['vertical'] = array('angle' => 'vertical');
Image::$styles['small'] = array('size' => 'small');
Image::$styles['smaller'] = array('size' => 'smaller');
Image::$styles['large'] = array('size' => 'large');
Image::$styles['larger'] = array('size' => 'larger');
Image::$styles['borderless'] = array('line_width' => 0);

