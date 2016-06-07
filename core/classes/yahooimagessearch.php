<?php

class YahooImagesSearch extends ImagesSearch
{
	protected function _search_images($search)
	{
		$search_encoded = urlencode($search);

//		$url = "https://ajax.googleapis.com/ajax/services/search/images?v=1.0&q={$search_encoded}";
//		$url = "https://www.google.com.ar/search?q={$search_encoded}&safe=off&biw=1920&bih=921&source=lnms&tbm=isch&sa=X&ved=0ahUKEwjX_quKy_3MAhWLfpAKHWr_CgUQ_AUIBigB";
//		$url = "https://www.google.com.ar/search?q={$search_encoded}&biw=1920&bih=640";
//		$url = "https://www.googleapis.com/customsearch/v1?searchType=image&key=AIzaSyD3abkHwyZilWVehzXnyOBvGa_Jm-zS1_k&cx=A&q=hola";
		$url = "https://ar.images.search.yahoo.com/search/images;_ylt=AwrBTzCQv1ZXVrgA59er9Qt.;_ylu=X3oDMTB0N2Noc21lBGNvbG8DYmYxBHBvcwMxBHZ0aWQDBHNlYwNwaXZz?p={$search_encoded}&fr=yfp-t-726&fr2=piv-web";

		$contents = file_get_contents($url);
		preg_match_all('#imgurl\=(?P<url>.*?)\&#', $contents, $matches);

		$urls = array();

		foreach((array) $matches['url'] as $url)
		{
			$urls[] = 'http://'.urldecode($url);
		}

		return $urls;
	}

}