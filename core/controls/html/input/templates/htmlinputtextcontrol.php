<?php //-----------------------------------------------------------------------

$tag_attrs = array();
$tag_attrs['onchange'] = $on_change;
$tag_attrs['onfocus'] = $on_focus;
$tag_attrs['onblur'] = $on_blur;
$tag_attrs['onmouseover'] = $on_mouse_over;
$tag_attrs['onmouseout'] = $on_mouse_out;
$tag_attrs['title'] = $title;
$tag_attrs['id'] = $id;
$tag_attrs['style'] = $style;
$tag_attrs['name'] = $name;
$tag_attrs['class'] = trim($class.' text input-border input-text');
$tag_attrs['disabled'] = $disabled ? 'disabled' : '';
$tag_attrs['readonly'] = $readonly ? 'readonly' : '';
$tag_attrs['placeholder'] = $placeholder ? $placeholder : '';

if(!$autocomplete) $tag_attrs['autocomplete'] = 'off';

$tag_attrs = array_filter($tag_attrs);

$tag_attrs['value'] = $value;

if($placeholder)
{
	$tag_attrs['placeholder'] = $placeholder;
}
//----------------------------------------------------------------------- ?>

<?=$label_html?>

<? if($prefix) { ?><span class="prefix input-text-prefix numeric-prefix"><?=$prefix?></span><? } ?>
<input type="text" <?php foreach($tag_attrs as $name => $attr_value) echo " {$name}=".HTMLHelper::quote ($attr_value); ?> />
<? if($sufix) { ?><span class="sufix input-text-sufix numeric-sufix"><?=$sufix?></span><? } ?>
