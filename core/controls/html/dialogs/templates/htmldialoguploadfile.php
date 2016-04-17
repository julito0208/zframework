<div id="dialog-upload-file">

	<form id="<?=HTMLHelper::escape($form_id)?>" action="<?=HTMLHelper::escape($form_action)?>" method='post' enctype='multipart/form-data'>

		<div class="data-block">
		
			<fieldset>

				<div class="row">
					<?=$file_input?>
				</div>
				
				<? if($help_bottom): ?>
				<div class="help-bottom"><?=$help_bottom?></div>
				<? endif;?>

			</fieldset>
			
		</div>
		
		<div class="buttons form-buttons">
			<div class="float-right">
				<button type="submit" class="btn btn-success"> <span class="text"><?=$accept_label?></span> </button>
				&nbsp;&nbsp;&nbsp;
				<button type="button" class="btn btn-default" onclick="$.modalDialog.close()"> <span class="text"><?=$cancel_label?></span> </button>
			</div>
			<div class="clear"></div>
		</div>	

		<? foreach($data as $name => $value) { ?>
			<input type="hidden" name="<?=HTMLHelper::escape($name)?>" value="<?=HTMLHelper::escape($value)?>" />
		<? } ?>
		
	</form>
</div>


<script type="text/javascript">
		
	$(document).ready(function() {

		var formOptions = {};

		formOptions['beforeSend'] = function() {
			$.modalDialog.loading(<?=JSHelper::cast_str($loading_label)?>, function() {});
		};
		
		<? if($callback) { ?>
		formOptions['success'] = function(data) {
			<?=$callback?>.call(this, data);
		};
		<? } ?>
		
		$(<?=JSHelper::cast_str("#{$form_id}")?>).ajaxForm(formOptions);
	
	});
	
</script>