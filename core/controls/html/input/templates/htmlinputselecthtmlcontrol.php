<?php //-----------------------------------------------------------------------

$tag_attrs = array();
$tag_attrs['onchange'] = $on_change;
$tag_attrs['onfocus'] = $on_focus;
$tag_attrs['onblur'] = $on_blur;
$tag_attrs['onmouseover'] = $on_mouse_over;
$tag_attrs['onmouseout'] = $on_mouse_out;
$tag_attrs['title'] = $title;
$tag_attrs['id'] = $id;
$tag_attrs['name'] = $name.($multiple ? '[]' : '');
$tag_attrs['class'] = trim($class.' input-border');
$tag_attrs['disabled'] = $disabled ? 'disabled' : '';
$tag_attrs['multiple'] = $multiple ? 'multiple' : '';
$tag_attrs = array_filter($tag_attrs);

$select2_options = array();

if($default_option_enabled) {
	$select2_options[] = array('id' => $default_option_value, 'text' => $default_option_text);
}

if($placeholder)
{
	$select2_options[] = array('id' => null, 'text' => '');
}

foreach((array) $options as $option) {
	$select2_options[] = array('id' => $option['value'], 'text' => $option['html'] ? $option['html'] : $option['text'], 'selected' => false);
}

foreach($select2_options as $index => $option)
{
	if(in_array($option['id'], $selected_values))
	{
		$select2_options[$index]['selected'] = true;
	}
}

//----------------------------------------------------------------------- ?>

<?=$label_html?>

<select <?php foreach($tag_attrs as $name => $attr_value) echo " {$name}=".HTMLHelper::quote ($attr_value); ?>>
	<?php foreach($select2_options as $option) { ?>
	<option label="<?=HTMLHelper::escape_quotes($option['text'])?>" value="<?=HTMLHelper::escape($option['id'])?>"<?=($option['selected'] ? " selected='selected'" : '')?>><?=HTMLHelper::escape_tags($option['text'])?></option>
	<?php } ?>
</select>

<script type="text/javascript">
	
	$(document).ready(function () {
		(function(){
			$(<?=JSHelper::cast_str('#'.$id)?>).select2({
				containerCssClass: <?=JSHelper::cast_str($container_class)?>,
				formatSearching: function() {
					return <?=JSHelper::cast_str(String::get('searching').'...')?>;
				},
				formatNoMatches: function() {
					return <?=JSHelper::cast_str(String::get('no_matches_found'))?>;
				},
				<? if($width) { ?>
					width: <?=JSHelper::cast($width)?>,
				<? } ?>
				dropdownAutoWidth: true,
				minimumResultsForSearch: <?=($allow_search ? 3 : -1)?>,
				escapeMarkup: function(s) { return s; },
				placeholder: <?=JSHelper::cast_str($placeholder)?>,
				allowClear: <?=JSHelper::cast_bool($allow_clear)?>
			});

			<? if($tag_attrs['onchange']) { ?>
				$(<?=JSHelper::cast_str("#{$id}")?>).on('change', function() { <?=$tag_attrs['onchange']?> });
			<? } ?>
		})();
	});	

</script>
