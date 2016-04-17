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
$tag_attrs['name'] = $multiple ? StringHelper::put_sufix($name, '[]') : $name;
$tag_attrs['class'] = trim($class.' text input-border input-text');
$tag_attrs['disabled'] = $disabled ? 'disabled' : '';
$tag_attrs['readonly'] = $readonly ? 'readonly' : '';

if($multiple) $tag_attrs['multiple'] = 'multiple';

$tag_attrs = array_filter($tag_attrs);

$tag_attrs['value'] = $value;

//----------------------------------------------------------------------- ?>

<?=$label_html?>

<input type="file" <?php foreach($tag_attrs as $name => $attr_value) echo " {$name}=".HTMLHelper::quote ($attr_value); ?> />