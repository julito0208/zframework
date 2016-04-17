<?php 

class HTMLControlStaticLibraryScrewDefaultButtons extends HTMLControlStaticLibrary {

	const IMAGE_CHECKBOX = 'checkbox.jpg';
	const IMAGE_CHECKBOX_SMALL = 'checkboxSmall.jpg';
	const IMAGE_CHECKBOX_SMALL_BLUE = 'checkboxSmall_Blue.jpg';
	const IMAGE_RADIO = 'radio.jpg';
	const IMAGE_RADIO_ALT = 'radio-alt.png';
	const IMAGE_RADIO_SMALL = 'radioSmall.jpg';
	const IMAGE_RADIO_SMALL_ALT = 'radioSmall-alt.png';

	public static function get_image_url($image, $for_css=false)
	{
		$url = URLHelper::get_zframework_static_url('thirdparty/screw-default-buttons/images/'.$image);

		if($for_css)
		{
			return "url('{$url}')";
		}
		else
		{
			return $url;
		}
	}

	public function get_library_js_files() {
		return array(
			URLHelper::get_zframework_static_url('thirdparty/screw-default-buttons/js/jquery.screwdefaultbuttonsV2.min.js'),
		);
	}
	
	public function get_dependence_libraries() {
		return array(
			self::STATIC_LIBRARY_JQUERY,
		);
	}
}