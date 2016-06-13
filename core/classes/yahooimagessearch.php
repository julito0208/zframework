<?php

class YahooImagesSearch extends ImagesSearch
{
	protected function _search_images($search)
	{
		$search_encoded = urlencode($search);
		$urls = array();
		$pages = ZPHP::get_config('image.search_pages');

		for($i=0; $i<$pages; $i++)
		{
			$url = "https://ar.images.search.yahoo.com/search/images;_ylt=AwrBTzCQv1ZXVrgA59er9Qt.;_ylu=X3oDMTB0N2Noc21lBGNvbG8DYmYxBHBvcwMxBHZ0aWQDBHNlYwNwaXZz?p={$search_encoded}&fr=yfp-t-726&fr2=piv-web&vm=p&b=".($i*60);
			$contents = file_get_contents($url);

			preg_match_all('#imgurl\=(?P<url>.*?)\&#', $contents, $matches);

			foreach((array) $matches['url'] as $url)
			{
				$urls[] = 'http://'.urldecode($url);
			}

		}

		$urls = array_unique($urls);
		return $urls;
	}

}