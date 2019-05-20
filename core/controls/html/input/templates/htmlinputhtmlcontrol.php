<div id="<?=HTMLHelper::escape($id_container)?>" style="height: <?=($height+10)?>px; overflow-y: hidden !important;">
	<textarea id="<?=HTMLHelper::escape($id)?>" name="<?=HTMLHelper::escape($name)?>" style="visibility: hidden; width: <?=(is_numeric($width) ? "{$width}px" : HTMLHelper::escape($width))?>; height: <?=(is_numeric($height) ? "{$height}px" : HTMLHelper::escape($height))?>"><?=$value?></textarea>
</div>

<div id="<?=HTMLHelper::escape($id_container)?>_image_dialog" class="image-dialog">

	<div id="<?=HTMLHelper::escape($id_container)?>_image_dialog_body">
		<div style="width: 600px;" id="<?=HTMLHelper::escape($id_container)?>_main_block">
			<h4 style="text-decoration: underline; margin: 0 0 30px 0;">Agregar Imagen</h4>

			<div class="form-fieldset">
				<?=$image_input?>
			</div>

			<div class="form-fieldset buttons">
				<button type="submit" class="btn btn-success">Aceptar</button>
				&nbsp;&nbsp;
				<button type="button" class="btn btn-default" onclick="$.modalDialog.closeAll();">Cancelar</button>
			</div>
		</div>
	</div>
</div>

<div id="prueba" style="display: none">
	asas
</div>
<script type="text/javascript">

	$(<?=JSHelper::cast_str('#'.$id_container)?>).data('clear_image_dialog', true);

	$(<?=JSHelper::cast_str('#'.$id_container.'_image_dialog')?>).find('.url-link').data('image_dialog', <?=JSHelper::cast_str('#'.$id_container.'_image_dialog')?>);
	$(<?=JSHelper::cast_str('#'.$id_container.'_image_dialog')?>).find('.search-link').data('image_dialog', <?=JSHelper::cast_str('#'.$id_container.'_image_dialog')?>);
	$(<?=JSHelper::cast_str('#'.$id_container.'_image_dialog')?>).find('.local-link').data('image_dialog', <?=JSHelper::cast_str('#'.$id_container.'_image_dialog')?>);

//	$(<?//=JSHelper::cast_str('#'.$id_container.'_image_dialog')?>//).find('button[type=submit]').bind('click', function() {
//
//		var base64Contents = $(<?//=JSHelper::cast_str('#'.$id_container)?>//).data('image_dialog').body().find('.image_contents_value').val();
//
//		if(base64Contents)
//		{
//			var imageHtml = "<img src='" + base64Contents + "' alt='Image' />";
//			tinymce.get(<?//=JSHelper::cast_str($id)?>//).insertContent(imageHtml);
//			$.modalDialog.closeAll();
//		}
//	});

	$(<?=JSHelper::cast_str('#'.$id_container)?>).data('image_dialog', $('#<?=HTMLHelper::escape($id_container)?>_image_dialog').modalDialog({
		'onload': function()
		{
			if($(<?=JSHelper::cast_str('#'.$id_container)?>).data('clear_image_dialog'))
			{
				$(<?=JSHelper::cast_str('#'.$id_container)?>).data('image_dialog').body().find('#<?=$image_input->get_id()?>').triggerHandler('clear');
			}

			$(<?=JSHelper::cast_str('#'.$id_container)?>).data('clear_image_dialog', false);

		},
		'onunload': function()
		{
			$(<?=JSHelper::cast_str('#'.$id_container)?>).data('clear_image_dialog', true);
		}
	}));

	$(<?=JSHelper::cast_str('#'.$id_container)?>).
		data('image_dialog').
		body().
		find('#<?=HTMLHelper::escape($id_container)?>_main_block').
		wrap("<form class='form-ajax' id='<?=$id_container?>_image_form' method='post' action='!HTMLInputHTMLControl(get_image_html)' style='width: 600px' enctype='multipart/form-data'></form>");

	$('body').on('form.success', '#<?=$id_container?>_image_form', function(evt, data) {

		if(data && data['success'] && data['src']) {

			var imageHtml = "<img src='" + data['src'] + "' alt='Image' />";
			tinymce.get(<?=JSHelper::cast_str($id)?>).insertContent(imageHtml);
		}

		return false;
	});

	<? if($toolbar && stripos($toolbar, HTMLInputHTMLControl::TOOLBAR_ITEM_IMAGE) !== false) { ?>

		$(<?=JSHelper::cast_str('#'.$id_container)?>).data('html_load_function', function() {

			var container = $(<?=JSHelper::cast_str('#'.$id_container)?>);
			var imageItem = container.find('.mce-ico.mce-i-image');

			if(imageItem.length > 0) {

				var button = imageItem.parent('button');

				if(!button.data('image_listener_bind')) {

					button.data('image_listener_bind', true);

					button.bind('click', function(evt) {

						$(<?=JSHelper::cast_str('#'.$id_container)?>).data('image_dialog').open();
						evt.stopPropagation();
						return false;
					});
				}

			} else {

				setTimeout($(<?=JSHelper::cast_str('#'.$id_container)?>).data('html_load_function'), 100);

			}



		});

	<? } ?>

	tinymce.init({
		selector: <?=JSHelper::cast_str("#{$id}")?>,
		language: <?=JSHelper::cast_str($language)?>,
		width: <?=JSHelper::cast_str($width)?>,
		height: <?=JSHelper::cast_str($height)?>,
		content_css: <?=JSHelper::cast_str(NavigationHelper::conv_abs_url(URLHelper::get_zframework_static_url('thirdparty/tinymce/js/tinymce/tinymce.content.css')))?>,
		menubar: <?=JSHelper::cast_bool($show_menu)?>,
		paste_data_images: true,
		theme: "modern",
		plugins: [ "youtube advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker","searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking","save table contextmenu directionality emoticons template paste textcolor youtube"],
    	toolbar: <?=($toolbar ? JSHelper::cast_array($toolbar) : JSHelper::cast_bool(false))?>,
		onpageload: function() {

			var container = $(<?=JSHelper::cast_str('#'.$id_container)?>);

			setTimeout(function() { container.css({'height': '', 'overflow-y': 'visible'}); }, 300);

			var imageLoadFunction = $(<?=JSHelper::cast_str('#'.$id_container)?>).data('html_load_function');

			if(imageLoadFunction) {
				imageLoadFunction.call(this);
			}

		},
		setup: function(ed)
		{
			<?php if($paste_clear_font): ?>
			ed.on('paste', function(ed) {

				setTimeout(function() {

					var content = tinyMCE.get(<?=JSHelper::cast_str($id)?>).getContent();

					content = content.replace(/(\<\w+?.*?style\=\".*?)font.*?\:.*?;?(")/gi, '$1$2');
					content = content.replace(/(\<\w+?.*?style\=\'.*?)font.*?\:.*?;?(')/gi, '$1$2');

					tinyMCE.get(<?=JSHelper::cast_str($id)?>).setContent(content);

				}, 100);

			});
			<?php endif; ?>

			ed.on('change', function(ed) {
				$('#<?=HTMLHelper::escape($id)?>').val(tinyMCE.get(<?=JSHelper::cast_str($id)?>).getContent());
			});

			$(<?=JSHelper::cast_str("#{$id}")?>).getParent('table').width(<?=JSHelper::cast_str($width)?>);
		}
	 });

</script>
