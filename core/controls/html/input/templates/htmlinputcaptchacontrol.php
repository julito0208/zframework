<div class="captcha-block">
	<div class="captcha-block-label" style="float: left">
		<label for=<?=HTMLHelper::quote($text_id)?>><?=(class_exists('LanguageText') ? String::get_html('html_input_control_captcha_label') : 'Código de Seguridad')?>:</label>
		<br />
		<a class="reload-captcha" href="javascript:void(0)" style="color: #555;font-size:9pt;text-decoration:underline;" onclick=<?=HTMLHelper::quote(JSHelper::call_quote("updateCaptcha_{$img_id}"))?>><?=(class_exists('LanguageText') ? String::get_html('html_input_control_captcha_load_code') : 'Recargar')?></a>
	</div>
	
	<div class="captcha-block-img" style="float: left; margin-left: 20px;">
		<img  alt='Captcha' style="display: block; width: <?=$width?>px; height: <?=$height?>px;" width="<?=$width?>" height="<?=$height?>" id=<?=HTMLHelper::quote($img_id)?> />
	</div>

	<div class="captcha-block-input" style="float: left; margin-left: 20px; padding-top: <?=($height/2)-13?>px">
		<input type='text' name='<?=$name?>' class='captcha text captcha-text input-border' style="width: 90px;" id=<?=HTMLHelper::quote($text_id)?> value="<?=HTMLHelper::escape($value)?>" placeholder="<?=(class_exists('LanguageText') ? String::get_html('code') : 'Código')?>" />
	</div>
</div>


<script type='text/javascript'>
	function updateCaptcha_<?=$img_id?>() { $(<?=JSHelper::cast_str("#{$img_id}")?>).attr('src', <?=JSHelper::cast_str($img_url)?>+'?out_captcha_img=1&id='+$.uniqID('captcha')); }
	$(document).ready(function() { setTimeout(function() { updateCaptcha_<?=$img_id?>(); }, 1); });
	updateCaptcha_<?=$img_id?>();
</script>