<?php 

class GeolocatorHelper {

	const DEFAULT_COUNTRY = 'Argentina';
	
	private static $_lang = 'en';
	
	private static function _load_geolocator_class() {
		@ include_once(ZPHP::get_third_party_path('geolocator/GeoLocator.class.php'));
		@ include_once(ZPHP::get_third_party_path('geolocator/GoogleGeoLocator.class.php'));
	}
	
	public static function get_address_pos($address, $region, $country=null) {
	
		self::_load_geolocator_class();
		
		if(!$country) $country = self::DEFAULT_COUNTRY;
	
		$google_api_key = ZPHP::get_config('google_geolocator_api_key');
		
		$google = new GoogleGeoLocator($google_api_key, self::$_lang); 
		
		$res = $google->searchByAddress(trim($address), $region, $country);
		
		if($res['count'] > 0) return array('lat' => $res['results'][0]['lat'], 'lng' => $res['results'][0]['long']);
		else return null;
	
	}
	
	
	
	
}
