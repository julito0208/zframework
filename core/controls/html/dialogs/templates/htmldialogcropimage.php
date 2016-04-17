<div id="imagecropdialog" style="width: 600px;">
	
	<form id="imagecropdialog-form" action="javascript: void(0)" method="post">

		<div class="error-block" style="display: none"></div>
		
		<?=$imagecropcontrol?>
		
		<div class="buttons form-buttons">
			<button type="submit" class="modaldialog-button-icon button-submit"><span>Aceptar</span></button>
			<button type="button" class="modaldialog-button-icon button-cancel" onclick="$.modalDialog.close()"><span>Cancelar</span></button>
		</div>	

		<? foreach($form_values as $name => $value) { ?>
			<input type="hidden" name=<?=HTMLHelper::quote($name)?> value=<?=HTMLHelper::quote($value)?> />
		<? } ?>

		<? foreach(ArrayHelper::encode_html($post_data) as $param) { ?>
			<? if($param['name'] == 'id_image' || $param['name'] == 'crop') continue; ?>
			<input type="hidden" name="<?=HTMLHelper::escape($param['name'])?>" value="<?=HTMLHelper::escape($param['value'])?>" />
		<? } ?>

	</form>
	
</div>

<script type="text/javascript">

	var cropDialog = $.modalDialog.current();
	var cropForm = $('#imagecropdialog-form');
	
	cropDialog.title('Recortar Imagen');
	
	cropDialog.bind('load', function() {
		<?=$imagecropcontrol->get_init_function_name()?>();
	});

	$(document).ready(function() { 
		
		cropDialog.bind('load', function() {
			
			cropDialog.selectedData = null;
			
			cropForm.bind('submit', function() { 

				var data = cropForm.paramMapExtended();
				var errorBlock = cropForm.find('.error-block').css('display', 'none');
					
				<? if(!$can_select_none) { ?>

				if(data[<?=JSHelper::cast_str($imagecropcontrol->get_name())?>]['w'] <= 0 || data[<?=JSHelper::cast_str($imagecropcontrol->get_name())?>]['h'] <= 0) {

					errorBlock.html('Debe seleccionar un área').slideDown(100);
					return false;

				}

				<? } ?>
						
				cropDialog.selectedData = data;
				cropDialog.close();
					
			});
		});
		
		cropDialog.bind('terminate', function() {
			
			if(cropDialog.selectedData) {
				<? if($callback) { ?>
					<?=$callback?>(cropDialog.selectedData);
				<? } ?>	
			}

		});
		
	});
	
	
</script>