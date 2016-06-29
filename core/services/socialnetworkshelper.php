<?php

class SocialNetworksHelper
{
	public static function generate_facebook_url($url=null)
	{
		if(!$url)
		{
			$url = ZPHP::get_absolute_actual_uri();
		}

		return "http://www.facebook.com/sharer.php?u=".urlencode($url);
	}

	public static function generate_twitter_url($title,$url=null)
	{
		if(!$url)
		{
			$url = ZPHP::get_absolute_actual_uri();
		}

		return "https://twitter.com/share?url=" . urlencode($url) . "&text={$title}";
	}

	public static function generate_google_url($url=null)
	{
		if(!$url)
		{
			$url = ZPHP::get_absolute_actual_uri();
		}

		return "https://plus.google.com/share?url=" . urlencode($url);
	}

	public static function generate_linkedin_url($title,$url=null)
	{
		if(!$url)
		{
			$url = ZPHP::get_absolute_actual_uri();
		}

		return "http://www.linkedin.com/shareArticle?url=". urlencode($url) . "&title={$title}";
	}
}