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
$tag_attrs['class'] = trim($class.' text-numeric text input-border input-text');
$tag_attrs['disabled'] = $disabled ? 'disabled' : '';
$tag_attrs['readonly'] = $readonly ? 'readonly' : '';

$tag_attrs = array_filter($tag_attrs);

$tag_attrs['value'] = $value;

//----------------------------------------------------------------------- ?>

<?=$label_html?>

<?php if($prefix) { ?><span class="prefix input-text-prefix numeric-prefix"><?=$prefix?></span><? } ?>
<input type="text" <?php foreach($tag_attrs as $name => $value) echo " {$name}=".HTMLHelper::quote ($value); ?> placeholder="<?=HTMLHelper::escape($placeholder)?>" />
<?php if($sufix) { ?><span class="sufix input-text-sufix numeric-sufix"><?=$sufix?></span><? } ?>

<script type="text/javascript">
	$(<?=JSHelper::cast_str("#{$tag_attrs['id']}")?>).numericFieldControl({'signed': <?=JSHelper::cast_bool($signed)?>, 'zero': <?=JSHelper::cast_bool($zero)?>, 'float': <?=JSHelper::cast_bool($float)?>});
	<?php if($placeholder && false) { //----------------------------------------------------------- ?>
		$(<?=JSHelper::cast_str("#{$tag_attrs['id']}")?>).placeHolder(<?=JSHelper::cast_str($placeholder)?>);
	<?php } //---------------------------------------------------------------------------- ?>
</script>

