<?php //-----------------------------------------------------------------------

$tag_attrs = array();
$tag_attrs['onchange'] = $on_change;
$tag_attrs['onfocus'] = $on_focus;
$tag_attrs['onblur'] = $on_blur;
$tag_attrs['onmouseover'] = $on_mouse_over;
$tag_attrs['onmouseout'] = $on_mouse_out;
$tag_attrs['title'] = $title;
$tag_attrs['id'] = $id;
$tag_attrs['name'] = $name;
$tag_attrs['value'] = $value;
$tag_attrs['class'] = trim($class.' input-border');
$tag_attrs['disabled'] = $disabled ? 'disabled' : '';
$tag_attrs['checked'] = $checked ? 'checked' : '';


$tag_attrs = array_filter($tag_attrs);
//----------------------------------------------------------------------- ?>

<span class="htmlinputcheckboxcontrol input-checkbox">

	<input type="checkbox" <?php foreach((array) $tag_attrs as $attr_name => $attr_value) { ?> <?=" {$attr_name}=".HTMLHelper::quote($attr_value)?> <? } ?> />
	
	<label for="<?=HTMLHelper::escape($id)?>" id="<?=HTMLHelper::escape("{$id}_label")?>"><?=($label ? $label : $title)?></label>
	
</span>	
