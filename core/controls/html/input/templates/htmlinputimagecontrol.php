<div class="image-input" style="vertical-align: top; margin: 10px 0 0 10px;" id="<?=$id_uniq?>_container">

	<input type="hidden" name="<?=HTMLHelper::escape($name)?>" id="<?=HTMLHelper::escape($id_uniq.'_value')?>" />

	<div class="image-container" style="display: inline-block; margin: 0 20px 0 0; width: <?=$image_width?>px; height: <?=$image_height?>px; background: #CCC;">

		<img alt="Image" class="main-img" id="<?=HTMLHelper::escape($id_uniq.'_img')?>" src="<?=HTMLHelper::escape(ZfImageFile::get_image_url($id_image_file))?>" style="max-width: <?=$image_width?>px; max-height: <?=$image_height?>px; border: solid 1px #777; border-radius: 5px; box-shadow: 1px 1px 1px rgba(0,0,0,0.4); visibility: hidden; cursor: pointer;" onmouseover="$(this).css({'border-color': '#00F'})" onmouseout="$(this).css({'border-color': ''})" />

	</div>

	<div style="display: inline-block; vertical-align: top;">

		<div style="">
			<input type="file" id="<?=$id?>" class="form-control" style="height: auto; padding: 10px 20px; background: #F5F5F5;" />
		</div>

		<div style="margin-top: 20px; font-weight: bold;">
			<a href="javascript:void(0)" id="<?=$id_uniq?>_search_link"><?=LanguageHelper::get_text('search_images_online')?></a>
		</div>

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
			$parent.css({'background': 'transparent'});
			$parent.css({'background': 'transparent', 'border': '0', 'box-shadow': '0 0 0 rgba(0,0,0,0)', 'opacity': '1'});
		}
		else
		{
			$this.css({'visibility': 'hidden'});
			$parent.css({'background': '#777 url(/zframework/static/icons/photo.png) center center no-repeat', 'border-radius': '5px', 'border': 'solid 1px #222', 'box-shadow': '1px 1px 1px rgba(0,0,0,0.7)', 'opacity': '0.7'});
		}

	});

	$('#<?=$id?>').bind('change', function() {

		var img = $('#<?=$id_uniq.'_img'?>').get(0);
		var file = $(this).get(0).files[0];
		var reader  = new FileReader();

		reader.addEventListener("load", function () {
			img.src = reader.result;
			$('#<?=$id_uniq.'_value'?>').val(reader.result);
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

		$.modalDialog.imagesSearch(function(url){

			$.modalDialog.loading('Cargando...', function () {
				$.ajax({
					'url': '!HTMLInputImageControl(url_base64)',
					'type': 'post',
					'data': {'url': url},
					'success': function (data) {
						$('#<?=$id_uniq . '_img'?>').attr('src', data['content']);
						$('#<?=$id_uniq . '_value'?>').val(data['content']);
						$.modalDialog.closeAll();
					}
				});
			});

		});

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

	$('#<?=$id?>_delete').triggerHandler('click');

</script>