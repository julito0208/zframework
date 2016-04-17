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
$tag_attrs['style'] = $style;
$tag_attrs['class'] = trim($class.' text input-border input-text');
$tag_attrs['disabled'] = $disabled ? 'disabled' : '';
$tag_attrs['readonly'] = $readonly ? 'readonly' : '';

$tag_attrs = array_filter($tag_attrs);

//----------------------------------------------------------------------- ?>

<?=$label_html?>

<textarea <?php foreach($tag_attrs as $name => $attr_value) echo " {$name}=".HTMLHelper::quote ($attr_value); ?><?=$custom_attrs_html?>><?=HTMLHelper::escape($value)?></textarea>

<?php if($placeholder) { //----------------------------------------------------------- ?>
<script type="text/javascript">
	$(<?=JSHelper::cast_str("#{$tag_attrs['id']}")?>).placeHolder(<?=JSHelper::cast_str($placeholder)?>);
</script>
<?php } //---------------------------------------------------------------------------- ?>