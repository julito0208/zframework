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
$tag_attrs['class'] = trim($class.' input-border');
$tag_attrs['disabled'] = $disabled ? 'disabled' : '';


$tag_attrs = array_filter($tag_attrs);

//----------------------------------------------------------------------- ?>

<?=$label_html?>

<select <?php foreach($tag_attrs as $name => $attr_value) echo " {$name}=".HTMLHelper::quote ($attr_value); ?>>
	
	<?php if($default_option_enabled) { ?>
	
		<option value=<?php echo HTMLHelper::quote($default_option_value); ?>><?php echo $default_option_text; ?></option>
		<option value=<?php echo HTMLHelper::quote($default_option_value); ?>></option>
	
	<?php } ?>
		
		
	<?php foreach((array) $options as $item) { ?>
	
		<option value=<?php echo HTMLHelper::quote($item['value']); ?><?php if($value == $item['value']) echo " selected='selected'"; ?>><?php echo $item['label']; ?>&nbsp;</option>
			
	<?php } ?>
		
</select>