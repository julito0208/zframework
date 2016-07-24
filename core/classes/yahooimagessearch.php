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

//				$url = "https://ar.images.search.yahoo.com/search/images;_ylt=A2KLj9EfK5RXGDkAjR2s9Qt.;_ylc=X1MDMjExNDcxMzAwNARfcgMyBGJjawMyZnVldHQ5YnA4YW5rJTI2YiUzRDMlMjZzJTNEYmEEZnIDBGdwcmlkA0MwWlg0ajU1UndDcGxqcmF5WkpHRUEEbXRlc3RpZANudWxsBG5fc3VnZwMxMARvcmlnaW4DYXIuaW1hZ2VzLnNlYXJjaC55YWhvby5jb20EcG9zAzAEcHFzdHIDBHBxc3RybAMEcXN0cmwDMTgEcXVlcnkDdmlzYSBwcm9tb2Npb24gcG5nBHRfc3RtcAMxNDY5MzI4MTY3BHZ0ZXN0aWQDbnVsbA--?gprid=C0ZX4j55RwCpljrayZJGEA&pvid=ThXwwjcyLjMn.d3qV5Qq9A5pMTkwLgAAAACe_Mjz&fr2=sb-top-ar.images.search.yahoo.com&p={$search_encoded}&ei=UTF-8&iscqry=&fr=sfp";
				$url = "https://ar.images.search.yahoo.com/search/images;_ylt=AwrBTzCQv1ZXVrgA59er9Qt.;_ylu=X3oDMTB0N2Noc21lBGNvbG8DYmYxBHBvcwMxBHZ0aWQDBHNlYwNwaXZz?p={$search_encoded}&fr=yfp-t-726&fr2=piv-web&vm=p&b=" . ($i * 60);
				//$url = "https://ar.images.search.yahoo.com/search/images?ei=UTF-8&p={$search_encoded}&save=0";

				//			$url_request = new URLRequest($url);
				//			$contents = $url_request->request();
				$contents = file_get_contents($url);

				//	echo $contents;
				//			continue;

//				preg_match_all("#(?i)data\\-src\\=\\'(?P<url>.+?)\\'#", $contents, $matches);
//				preg_match_all("#(?i)href\\=\\'.*?\\&imgurl\\=(?P<url>.+?)\\&#", $contents, $matches);

				preg_match_all("#(?i)\\<a.+?href\\=\\'.*?\\&imgurl\\=(?P<url>.+?)\\&.+?'.*?\\<img.+?data\\-src\\=\\'(?P<thumb_url>.+?)\\'#", $contents, $matches);

				$match_urls = (array)$matches['url'];

				foreach ($match_urls as $index => $url)
				{
					$url = StringHelper::put_prefix(urldecode($url), 'http://', true);
					$thumb = str_replace('w=300&h=300', 'w=600&h=600', $matches['thumb_url'][$index]);

					$urls[] = array(
						'url' => $url,
						'thumb' => $thumb,
					);
//					$urls[] = str_replace('w=300&h=300', 'w=600&h=600', $url);
//					$urls[] = StringHelper::put_prefix(urldecode($url), 'http://', true);
				}

				//			preg_match_all('#imgurl\=(?P<url>.*?)\&#', $contents, $matches);
				//			foreach((array) $matches['url'] as $url)
				//			{
				//				$urls[] = 'http://'.urldecode($url);
				//			}
			}

		}
//		die();
//		$urls = array_unique($urls);
		return $urls;
	}

}