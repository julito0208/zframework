<?php 

class ImageCaptchaControl implements MIMEControl, RedirectURLPattern {

	const IMAGE_DEFAULT_SIZE_WIDTH = 120;
	const IMAGE_DEFAULT_SIZE_HEIGHT = 30;
	const IMAGE_DEFAULT_BACKGROUND_COLOR = '#F8F8F8';
	const IMAGE_DEFAULT_BORDER_COLOR = 'black';
	const IMAGE_DEFAULT_BORDER_WIDTH = 1;
	const IMAGE_DEFAULT_CODE_LENGTH = 4;
	const IMAGE_DEFAULT_TEXT_CHARS = 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,P,Q,R,T,U,V,W,X,Z';
	const IMAGE_DEFAULT_TEXT_SIZE_MIN = 15;
	const IMAGE_DEFAULT_TEXT_SIZE_MAX = 20;
	const IMAGE_DEFAULT_TEXT_ANGLE_MIN = -40;
	const IMAGE_DEFAULT_TEXT_ANGLE_MAX = 40;
	const IMAGE_DEFAULT_TEXT_ALPHA_MIN = 20;
	const IMAGE_DEFAULT_TEXT_ALPHA_MAX = 30;
	const IMAGE_DEFAULT_BACKGROUND_TEXT_SIZE_MIN = 8;
	const IMAGE_DEFAULT_BACKGROUND_TEXT_SIZE_MAX = 12;
	const IMAGE_DEFAULT_BACKGROUND_TEXT_ANGLE_MIN = -90;
	const IMAGE_DEFAULT_BACKGROUND_TEXT_ANGLE_MAX = 90;
	const IMAGE_DEFAULT_BACKGROUND_TEXT_ALPHA_MIN = 95;
	const IMAGE_DEFAULT_BACKGROUND_TEXT_ALPHA_MAX = 100;
	const IMAGE_DEFAULT_BACKGROUND_TEXT_DENSITY = 0.09;
	const IMAGE_DEFAULT_CODE_DURATION_SECS = 600;
	
	const SESSION_DATA_VARNAME = '__captcha__';
	
	const DEFAULT_IMAGE_URL = '_captcha';
	const URL_CAPTCHA_IMAGE = 'captchaImage';
	
	/*--------------------------------------------------------------*/
	
	/* @return URLPattern */
	public static function get_url_pattern() {
		
		$url_pattern = ZPHP::get_config('captcha_url', self::DEFAULT_IMAGE_URL);
		
		return new URLPattern(preg_quote($url_pattern), self::URL_CAPTCHA_IMAGE, 'ImageCaptchaControl');
	}
	
	/*--------------------------------------------------------------*/
	
	
	public static function test_code($code, &$code_incorrect=null, &$code_expired=null) {

		SessionHelper::init();
		$captcha = (array) SessionHelper::get_var(self::SESSION_DATA_VARNAME);

		if($captcha['case_insensitive']) $code_incorrect = strtolower($code) != strtolower($captcha['code']);
		else $code_incorrect = $code != $captcha['code'];

		$code_expired = time() >= $captcha['expiration_time'];

		return !$code_incorrect && !$code_expired;
	}


	
	public static function get_config_array() {
		
		return array(
			'size' => array(ZPHP::get_config('captcha_image_size_width', self::IMAGE_DEFAULT_SIZE_WIDTH),ZPHP::get_config('captcha_image_size_height', self::IMAGE_DEFAULT_SIZE_HEIGHT)),
			'width' => ZPHP::get_config('captcha_image_size_width', self::IMAGE_DEFAULT_SIZE_WIDTH),
			'height' => ZPHP::get_config('captcha_image_size_height', self::IMAGE_DEFAULT_SIZE_HEIGHT),
			'background_color' => ZPHP::get_config('captcha_image_background_color', self::IMAGE_DEFAULT_BACKGROUND_COLOR),
			'quality' => 90,
			'type' => 'gif',
			'border_color' => ZPHP::get_config('captcha_image_border_color', self::IMAGE_DEFAULT_BORDER_COLOR),
			'border_width' => ZPHP::get_config('captcha_image_border_width', self::IMAGE_DEFAULT_BORDER_WIDTH),
			'code_length' => ZPHP::get_config('captcha_image_code_length', self::IMAGE_DEFAULT_CODE_LENGTH),
			'text_colors' => array('black','blue','navy','red','brown','darkgreen','indigo'),
			'text_chars' => explode(',', ZPHP::get_config('captcha_image_text_chars', self::IMAGE_DEFAULT_TEXT_CHARS)),
			'text_fonts' => array('arial','comic','courier','georgia','pala','times','verdana'),
			'text_sizes' => range(ZPHP::get_config('captcha_image_text_size_min', self::IMAGE_DEFAULT_TEXT_SIZE_MIN), ZPHP::get_config('captcha_image_text_size_max', self::IMAGE_DEFAULT_TEXT_SIZE_MAX)),
			'text_angles' => range(ZPHP::get_config('captcha_image_text_angle_min', self::IMAGE_DEFAULT_TEXT_ANGLE_MIN), ZPHP::get_config('captcha_image_text_angle_max', self::IMAGE_DEFAULT_TEXT_ANGLE_MAX)),
			'text_alpha_levels' => range(ZPHP::get_config('captcha_image_text_alpha_min', self::IMAGE_DEFAULT_TEXT_ALPHA_MIN),ZPHP::get_config('captcha_image_text_alpha_max', self::IMAGE_DEFAULT_TEXT_ALPHA_MAX)),
			'text_default_style' => array('bold'=>true),
			'background_text_sizes' => range(ZPHP::get_config('captcha_image_background_text_size_min', self::IMAGE_DEFAULT_BACKGROUND_TEXT_SIZE_MIN), ZPHP::get_config('captcha_image_background_text_size_max', self::IMAGE_DEFAULT_BACKGROUND_TEXT_SIZE_MIN)),
			'background_text_angles' => range(ZPHP::get_config('captcha_image_background_text_angle_min', self::IMAGE_DEFAULT_BACKGROUND_TEXT_ANGLE_MIN), ZPHP::get_config('captcha_image_background_text_angle_max', self::IMAGE_DEFAULT_BACKGROUND_TEXT_ANGLE_MAX)),
			'background_text_alpha_levels' => range(ZPHP::get_config('captcha_image_background_text_alpha_min', self::IMAGE_DEFAULT_BACKGROUND_TEXT_ALPHA_MIN),ZPHP::get_config('captcha_image_background_text_alpha_max', self::IMAGE_DEFAULT_BACKGROUND_TEXT_ALPHA_MAX)),
			'background_text_colors' => array('gray','navy','green','blue'),
			'background_text_default_style' => array('bold'=>true),
			'background_text_density' => ZPHP::get_config('captcha_image_background_text_density', self::IMAGE_DEFAULT_BACKGROUND_TEXT_DENSITY),
			'case_insensitive' => true,
			'code_duration' => ZPHP::get_config('captcha_image_code_duration_secs', self::IMAGE_DEFAULT_CODE_DURATION_SECS),
		);
	}
	
	/* @return Image */
	protected static function _get_image(&$data=null) {
		
		$configuration = self::get_config_array();

		$image = new Image(array('size'=>$configuration['size']));
		$image->fill($configuration['background_color']);
		$image->set_quality($configuration['quality']);
		$image->set_type($configuration['type']);

		//---------------------------------------------------------------------------------------------

		$code = '';
		$code_length = (integer) $configuration['code_length'];

		$text_colors = (array) $configuration['text_colors'];
		$text_chars = (array) $configuration['text_chars'];
		$text_fonts = (array) $configuration['text_fonts'];
		$text_sizes = (array) $configuration['text_sizes'];
		$text_angles = (array) $configuration['text_angles'];
		$text_alpha_levels = (array) $configuration['text_alpha_levels'];
		$text_default_style = (array) $configuration['text_default_style'];


		//---------------------------------------------------------------------------------------------


		$background_text_density = (float) $configuration['background_text_density'];

		if($background_text_density > 0) {

			if($background_text_density > 1) $background_text_density = 1.0;

			$background_text_colors = (array) $configuration['background_text_colors'];
			$background_text_sizes = (array) $configuration['background_text_sizes'];
			$background_text_angles = (array) $configuration['background_text_angles'];
			$background_text_alpha_levels = (array) $configuration['background_text_alpha_levels'];
			$background_text_default_style = (array) $configuration['background_text_default_style'];

			$background_text_length_width = $image->get_width() * $background_text_density;
			$background_text_length_height = $image->get_height() * $background_text_density;

			$background_chars_percent_width_space = 1/($background_text_length_width+1);
			$background_chars_percent_height_space = 1/($background_text_length_height+1);

			for($width_index=0; $width_index<=$background_text_length_width+1; $width_index++) {

				$pos_x = $background_chars_percent_width_space*($width_index);

				for($height_index=0; $height_index<=$background_text_length_height+1; $height_index++) {

					$pos_y = $background_chars_percent_height_space*($height_index);

					$char_style = $background_text_default_style;
					$char_style['color'] = array('color'=>ArrayHelper::rand_value($background_text_colors),'alpha'=>ArrayHelper::rand_value($background_text_alpha_levels));
					$char_style['size'] = ArrayHelper::rand_value($background_text_sizes);
					$char_style['angle'] = ArrayHelper::rand_value($background_text_angles);
					$char_style['font'] = ArrayHelper::rand_value($text_fonts);

					$image->draw_text(array(($pos_x*100).'%',($pos_y*100).'%'),ArrayHelper::rand_value($text_chars),$char_style,array('point'=>'center'));
				}
			}
		}

		//---------------------------------------------------------------------------------------------


		$chars_percent_space = 1/($code_length+1);

		for($char_index=0; $char_index<$code_length; $char_index++) {

			$char = ArrayHelper::rand_value($text_chars);
			$code.= $char;

			$text_style = $text_default_style;
			$text_style['color'] = array('color'=>ArrayHelper::rand_value($text_colors), 'alpha'=>ArrayHelper::rand_value($text_alpha_levels));
			$text_style['font'] = ArrayHelper::rand_value($text_fonts);
			$text_style['size'] = ArrayHelper::rand_value($text_sizes);
			$text_style['angle'] = ArrayHelper::rand_value($text_angles);

			$image->draw_text(array((($chars_percent_space*($char_index+1))*100).'%','center'),$char, $text_style, array('point'=>'center'));

		}

		//---------------------------------------------------------------------------------------------

		$image->draw_border($configuration['border_color'], $configuration['border_width']);


		$data = array(
			'code'=>$code, 
			'generation_time' => time(), 
			'code_duration'=>$configuration['code_duration'], 
			'case_insensitive'=>$configuration['case_insensitive'],
			'expiration_time'=>time()+$configuration['code_duration']);

		return $image;
	}
	
	//---------------------------------------------------------------------------------------------

	protected $_image;
	protected $_data;
	
	public function __construct() {
		
		$this->_image = self::_get_image($data);
		$this->_data = $data;
		
	}
	
	
	//------------------------------------------------------------------------------------------------------------------------------------
	
	public function out() {
		SessionHelper::init();
		SessionHelper::add_var(self::SESSION_DATA_VARNAME, $this->_data);
		$this->_image->out();
	}
	
	public function save_to($filename) {
		$this->_image->save_to($filename);
	}

	public function out_attachment($filename=null) {
		$this->_image->out_attachment($filename);
	}
}