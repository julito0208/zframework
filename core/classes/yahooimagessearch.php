<?php

class YahooImagesSearch extends ImagesSearch
{
	protected function _search_images($search, $pages)
	{

		$urls = array();
//		$pages = ZPHP::get_config('image.search_pages');
		//header('Content-type: text/html');
		for($i=0; $i<$pages; $i++)
		{

			foreach((array) $search as $string)
			{

				$search_encoded = urlencode($string);

				$url = "https://ar.images.search.yahoo.com/search/images;_ylt=AwrBTzCQv1ZXVrgA59er9Qt.;_ylu=X3oDMTB0N2Noc21lBGNvbG8DYmYxBHBvcwMxBHZ0aWQDBHNlYwNwaXZz?p={$search_encoded}&fr=yfp-t-726&fr2=piv-web&vm=p&b=" . ($i * 60);
				//$url = "https://ar.images.search.yahoo.com/search/images?ei=UTF-8&p={$search_encoded}&save=0";

				//			$url_request = new URLRequest($url);
				//			$contents = $url_request->request();
				$contents = file_get_contents($url);

				//	echo $contents;
				//			continue;

				preg_match_all("#(?i)data\\-src\\=\\'(?P<url>.+?)\\'#", $contents, $matches);

				$match_urls = (array)$matches['url'];

				foreach ($match_urls as $index => $url)
				{
					if ($index < count($match_urls) - 4)
					{
						$urls[] = str_replace('w=300&h=300', 'w=600&h=600', $url);
					}

				}

				//			preg_match_all('#imgurl\=(?P<url>.*?)\&#', $contents, $matches);
				//			foreach((array) $matches['url'] as $url)
				//			{
				//				$urls[] = 'http://'.urldecode($url);
				//			}
			}

		}
//		die();
		$urls = array_unique($urls);
		return $urls;
	}

}