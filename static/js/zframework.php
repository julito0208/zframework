<?php /*-------------------------------------------------------------*/

if(!class_exists('ZPHP'))
{
	include_once('../../init.php');
}
/*-------------------------------------------------------------*/ ?>

<?php NavigationHelper::header_content_text_javascript(); ?>
_zphp = new Object();
_zphp['zframework_dir'] = <?=JSHelper::cast_str(rtrim(ZPHP::get_config('zframework_static_url'), '/'))?>;
_zphp['is_mobile'] = <?=JSHelper::cast_bool(ZPHP::is_mobile())?>;
_zphp['charset'] = <?=JSHelper::cast_str(ZPHP::get_config('html.charset'))?>;
ZPHP = new Object();
ZPHP.isMobile = function() { return _zphp['is_mobile']; };
<?php if(isset($_GET['language']) && $_GET['language'])
{
	LanguageHelper::set_current_language($_GET['language']);
} 
else
{
	LanguageHelper::set_current_language_from_url($_SERVER['HTTP_REFERER']);
}
?>
var LanguageHelper = new Object();
LanguageHelper.DefaultSection = <?=JSHelper::cast_str(ZPHP::get_config('multi_language_default_section'))?>;
LanguageHelper.DefaultText = <?=JSHelper::cast_str(LanguageHelper::DEFAULT_TEXT)?>;
LanguageHelper.SectionKeySeparator = <?=JSHelper::cast_str(LanguageHelper::SECTION_KEY_SEPARATOR)?>;
LanguageHelper.Texts = {};
<?php foreach(LanguageHelper::get_javascript_texts() as $id_language_section => $texts) { ?>
LanguageHelper.Texts[<?=JSHelper::cast_str($id_language_section)?>] = {};
<?php foreach($texts as $key => $text) { ?>
LanguageHelper.Texts[<?=JSHelper::cast_str($id_language_section)?>][<?=JSHelper::cast_str($key)?>] = <?=JSHelper::cast_str($text)?>;
<?php } ?>
<?php } ?>
LanguageHelper.ParseKey = function(key)
{
	var parsedKey = String(key);
	parsedKey = parsedKey.toLowerCase();
	parsedKey = parsedKey.replace(/(\s+|[^0-9a-zA-Z\_\-]+)+/g, '');
	return parsedKey;
};
LanguageHelper.GetText = function(section, key)
{
	if(arguments.length == 1 && section && Object.prototype.toString.call(section) === '[object Array]')
	{
		if(section.length > 1)
		{
			return LanguageHelper.GetText(LanguageHelper.ParseKey(section[0]), LanguageHelper.ParseKey(section[1]));
		}
		else
		{
			return LanguageHelper.GetText(section[0]);
		}
	}
	else if(arguments.length > 1)
	{
		return LanguageHelper.GetText(LanguageHelper.ParseKey(section) + LanguageHelper.SectionKeySeparator + LanguageHelper.ParseKey(key));
	}
	else
	{
		var key = String(section);
		if(key.indexOf(LanguageHelper.SectionKeySeparator) == -1)
		{
			return LanguageHelper.GetText(LanguageHelper.DefaultSection + LanguageHelper.SectionKeySeparator + key);
		}
		else
		{
			var keyParts = key.split('\.', 2);
			if(keyParts.length > 1)
			{
				var key = LanguageHelper.ParseKey(keyParts[1]);
				var section = LanguageHelper.ParseKey(keyParts[0]);
			}
			else
			{
				return LanguageHelper.GetText(keyParts[0]);
			}
		}
		if(LanguageHelper.Texts[section] && LanguageHelper.Texts[section][key])
		{
			return LanguageHelper.Texts[section][key];
		}
		else
		{
			return LanguageHelper.DefaultText;
		}
	}
};
var Strings = new Object();
Strings.Get = function() { return LanguageHelper.GetText.apply(LanguageHelper, arguments); };
var Text = function() { return LanguageHelper.GetText.apply(LanguageHelper, arguments); };