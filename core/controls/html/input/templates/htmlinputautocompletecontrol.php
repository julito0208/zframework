<?php //-----------------------------------------------------------------------

$selected_item = $value && is_array($value) ? $value : null;

$tag_attrs = array();
$tag_attrs['onfocus'] = $on_focus;
$tag_attrs['onblur'] = $on_blur;
$tag_attrs['onmouseover'] = $on_mouse_over;
$tag_attrs['onmouseout'] = $on_mouse_out;
$tag_attrs['title'] = $title;
$tag_attrs['id'] = $text_input_id;
$tag_attrs['value'] = $selected_item ? $selected_item['text'] : '';
$tag_attrs['class'] = trim($class.' autocomplete text input-border input-text text-medium');
$tag_attrs['disabled'] = $disabled ? 'disabled' : '';
$tag_attrs['readonly'] = $readonly ? 'readonly' : '';

$tag_attrs = array_filter($tag_attrs);

//----------------------------------------------------------------------- ?>
<?=$label_html?>

<input type="hidden" id="<?=HTMLHelper::escape($hidden_input_id)?>" name="<?=HTMLHelper::escape($name)?>" />

<script type="text/javascript">

	if(!window[<?=JSHelper::cast_str('#s2id_'.$hidden_input_id)?>]) {

		$(<?=JSHelper::cast_str('#'.$hidden_input_id)?>).select2({
			width: <?=JSHelper::cast($width)?>,
			multiple: false,
//			formatSearching: function() {
//				return 'Buscando...';
//			},
			minimumInputLength: <?=JSHelper::cast_number($min_length)?>,

			<? if(!$value || !is_array($value)) { ?>
				placeholder: <?=JSHelper::cast_str($placeholder)?>,
			<? } ?>

			escapeMarkup: function(s) { return s; },
			ajax: { 
			   url: <?=JSHelper::cast_str($search_url)?>,
				type: <?=JSHelper::cast_str($search_method)?>,
			   dataType: 'json',
			   data: function (term, page) {

				   var searchData = <?=JSHelper::cast_obj($search_data)?>;

				   var itemData = $(<?=JSHelper::cast_str('#'.$hidden_input_id)?>).data('autocompletionSearchData');
				   if(!itemData) itemData = {};

					return $.extend({<?=JSHelper::cast_str(HTMLInputAutoCompleteControl::SEARCH_VARNAME)?>: term, <?=JSHelper::cast_str(HTMLInputAutoCompleteControl::ENABLE_HTML_VARNAME)?>: <?=JSHelper::cast_number($enable_html)?>}, searchData, itemData);
			   },
			   results: function (data, page) {

				   var results = data[<?=JSHelper::cast_str(HTMLInputAutoCompleteControl::AJAX_RESULTS_VARNAME)?>];

				   for(var i=0; i<results.length; i++)
				   {
					   results[i]['id'] = results[i][<?=JSHelper::cast_str(OptionItem::VALUE_NAME)?>];
				   }

				   return {results: results};
			   }
			},
			formatNoMatches: function(term) {
				return <?=JSHelper::cast_str($nomatches)?>;
			},
			<? if($value && is_array($value) && $value['id'] && $value['text']) { ?>
				initSelection : function (element, callback) {
					element.val(<?=JSHelper::cast($value['id'])?>);
					callback(<?=JSHelper::cast_obj($value)?>);
				}
			<? } ?>
		});

		<? if($tag_attrs['onchange']) { ?>
			$(<?=JSHelper::cast_str("#{$id}")?>).on('change', <?=$tag_attrs['onchange']?>);
		<? } ?>

		window[<?=JSHelper::cast_str('#s2id_'.$hidden_input_id)?>] = true;
	}

</script>
