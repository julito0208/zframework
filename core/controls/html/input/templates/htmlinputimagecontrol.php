<div class="image-input<?=($for_modaldialog ? ' for-modaldialog' : '')?>" style="vertical-align: top; margin: 10px 0 0 10px;" id="<?=$id_uniq?>_container">

	<input class="image_contents_value" type="hidden" name="<?=HTMLHelper::escape($name)?>" id="<?=HTMLHelper::escape($id_uniq.'_value')?>" value="<?=($image_file ? $image_file->get_base64_contents(true) : '')?>" />


	<div class="image-container" style="display: inline-block; margin: 0 30px 0 0; height: <?=$image_height?>px;">

		<div style="background: #CCC; min-width: 200px; max-width: 200px;">
			<img alt="Image" class="main-img" id="<?=HTMLHelper::escape($id_uniq.'_img')?>" src="<?=HTMLHelper::escape(ZfImageFile::get_image_url($id_image_file))?>" style="width: 100%; max-height: <?=$image_height?>px; border: solid 1px #777; border-radius: 5px; box-shadow: 1px 1px 1px rgba(0,0,0,0.4); visibility: hidden; cursor: pointer;" onmouseover="$(this).css({'border-color': '#00F'})" onmouseout="$(this).css({'border-color': ''})" />
		</div>

		<?php if($enable_title) { ?>

			<div style="margin: 20px 0 0 0;">
				<textarea style="" <?=(!$enable_title_edit ? ' readonly="readonly"' : '')?> name="<?=HTMLHelper::escape($name)?>_title" id="<?=HTMLHelper::escape($id_uniq.'_title')?>"><?=HTMLHelper::escape($image_file ? $image_file->get_title() : '')?></textarea>
			</div>

		<?php } else { ?>

			<input name="<?=HTMLHelper::escape($name)?>_title" id="<?=HTMLHelper::escape($id_uniq.'_title')?>" value="<?=HTMLHelper::escape($image_file ? $image_file->get_title() : '')?>" />

		<?php } ?>

	</div>

	<div style="display: inline-block; vertical-align: top;">

		<?php if($enable_select_local) { ?>
			<div class="local-select" style="font-weight: bold;">
				<a class="underline local-link" href="javascript:void(0)" id="<?=$id_uniq?>_file_link" onclick="$('#<?=$id?>').click()"><?=LanguageHelper::get_text('select_image_local')?></a>
				<input type="file" id="<?=$id?>" class="form-control" style="height: auto; padding: 10px 20px; background: #F5F5F5; display: none;" />
			</div>
		<?php } ?>

		<?php if($enable_select_url) { ?>
			<div style="margin-top: 20px; font-weight: bold;">
				<a class="underline url-link" href="javascript:void(0)" id="<?=$id_uniq?>_url_link"><?=LanguageHelper::get_text('select_image_url')?></a>
			</div>
		<?php } ?>

		<?php if($enable_image_search) { ?>
			<div style="margin-top: 20px; font-weight: bold;">
				<a class="underline search-link" href="javascript:void(0)" id="<?=$id_uniq?>_search_link"><?=LanguageHelper::get_text('search_images_online')?></a>
			</div>
		<?php } ?>

		<?php if($enable_delete) { ?>

			<div style="margin: 20px 0 0 0px; font-weight: bold;">
				<span class="checkbox input-checkbox">
					<input type="checkbox" id="<?=$id?>_delete" name="<?=$name?>_delete" value="<?=($id_image_file ? $id_image_file : '1')?>" class="align-middle" style="vertical-align: bottom; position: relative; margin-right: 0; margin-left: 0;" <?=($delete_selected ? " checked='checked'" : "")?> />
					<label style="vertical-align: bottom" class="align-middle underline" for="<?=$id?>_delete"><?=LanguageHelper::get_text('delete_image')?></label>
				</span>
			</div>

		<?php } ?>

	</div>


</div>

<script type="text/javascript">

	$('#<?=$id_uniq?>_img').bind('load error', function(evt) {

		var $this = $(this);
		var $parent = $this.getParent();

		if($this.attr('src') && evt.type != 'error')
		{
			$this.css({'visibility': 'visible'});
			$parent.css({'background': 'transparent', 'border': '0', 'box-shadow': '0 0 0 rgba(0,0,0,0)', 'opacity': '1', 'min-height': ''});
			$this.getParent().next().show();
		}
		else
		{
			$this.css({'visibility': 'hidden'});
			$parent.css({'background': '#777 url(/zframework/static/icons/photo.png) center center no-repeat', 'border-radius': '5px', 'border': 'solid 1px #222', 'box-shadow': '1px 1px 1px rgba(0,0,0,0.7)', 'opacity': '0.4', 'min-height': 110});
			$this.getParent().next().hide();
		}

	});

	$('#<?=$id?>').bind('clear', function(title) {

		$('#<?=$id_uniq?>_container').find('p.image-title').html('');
		$('#<?=$id_uniq?>_title').val('');

		$('#<?=$id_uniq?>_img').triggerHandler('error');

		$('#<?=$id_uniq . '_value'?>').val('');
	});

	$('#<?=$id?>').data('set_title', function(parent, title) {

		console.log(title);
		if(title)
		{
			title = String(title).replace(/\//g, '/');
			title = title.replace(/^.*\//g, '');
			title = title.replace(/\?.*$/g, '');
			title = title.replace(/\..*$/g, '');
		}
		else
		{
			title = '';
		}

		parent.find('#<?=$id_uniq?>_title').val(title);

	});

	$('#<?=$id?>').bind('change', function() {

		var $this = $(this);
		var parent = $('body');

		if($(this).data('image_dialog'))
		{
			parent = $.modalDialog($(this).data('image_dialog')).body();
		}

		var img = $('#<?=$id_uniq.'_img'?>').get(0);
		var file = $(this).get(0).files[0];
		var reader  = new FileReader();

		reader.addEventListener("load", function () {
			img.src = reader.result;
			$('#<?=$id_uniq.'_value'?>').val(reader.result);
			$('#<?=$id?>').data('set_title').call(this, parent, file.name);
		}, false);

		if (file) {
			reader.readAsDataURL(file);
		}
	});

	$('#<?=$id_uniq?>_search_link').bind('click', function() {

		if($('#<?=$id?>_delete').is(':checked'))
		{
			return;
		}


		var $this = $(this);
		var parent = $('body');

		if($(this).data('image_dialog'))
		{
			parent = $.modalDialog($(this).data('image_dialog')).body();
		}


		$.modalDialog.imagesSearch(function(url){

			$.modalDialog.loading('Cargando...', function () {
				$.ajax({
					'url': '!HTMLInputImageControl(url_base64)',
					'type': 'post',
					'data': {'url': url},
					'success': function (data) {

						if(data && data['success'])
						{
							parent.find('#<?=$id_uniq . '_img'?>').attr('src', data['content']);
							parent.find('#<?=$id_uniq . '_value'?>').val(data['content']);
							parent.find('#<?=$id?>').data('set_title').call($this, parent, url);
							$.modalDialog.close();
							$.modalDialog.close();

						}

					}
				});
			});

		});

	});

	$('#<?=$id_uniq?>_url_link').bind('click', function() {

		var $this = $(this);
		var parent = $('body');

		if($(this).data('image_dialog'))
		{
			parent = $.modalDialog($(this).data('image_dialog')).body();
		}

		$.modalDialog.prompt('URL', '', function(value) {

			$.modalDialog.loading('Cargando', function() {

				$.ajax({
					'url': '!HTMLInputImageControl(url_base64)',
					'type': 'post',
					'data': {'url': value},
					'success': function(data)
					{
						if(data && data['success'])
						{
							parent.find('#<?=$id_uniq . '_img'?>').attr('src', data['content']);
							parent.find('#<?=$id_uniq . '_value'?>').val(data['content']);
							parent.find('#<?=$id?>').data('set_title').call($this, parent, value);
							$.modalDialog.close();
						}
						else
						{
							$.zmodal.alert('No se pudo leer la imagen', function() {
								$.zmodal.closeAll();
								$.modalDialog.close();
							});
						}

					}
				});

			}, {'mode': jQuery.modalDialog.modeReplace});

		}, null, {'title': null}, {'width': 600});

	});

	$('#<?=$id?>_delete').bind('click', function() {

		var checked = $(this).is(':checked');

		if(checked)
		{
			$('#<?=$id?>').attr('disabled', true);
			$('#<?=$id?>').css({'opacity': 0.5});
			$('#<?=$id_uniq?>_search_link').css({'opacity': 0.5});
		}
		else
		{
			$('#<?=$id?>').attr('disabled', false);
			$('#<?=$id?>').css({'opacity': 1});
			$('#<?=$id_uniq?>_search_link').css({'opacity': 1});
		}

	});

	$('#<?=$id_uniq?>_img').bind('click', function() {

		var src = $(this).attr('src');
		$.modalDialog.image({'src': src, 'options': {'fill-window': true}});
	});

	//$('#<?=$id_uniq?>_url_link').triggerHandler('click');

</script>