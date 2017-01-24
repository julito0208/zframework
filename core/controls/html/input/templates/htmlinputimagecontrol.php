<div class="image-input<?=($for_modaldialog ? ' for-modaldialog' : '')?>" style="vertical-align: top; margin: 10px 0 0 10px;" id="<?=$id_uniq?>_container" data-input-id="<?=$id?>">

	<input class="image_contents_value" type="hidden" name="<?=HTMLHelper::escape($name)?>" id="<?=HTMLHelper::escape($id_uniq.'_value')?>" value="<?=($image_file ? $image_file->get_base64_contents(true) : '')?>" />


	<div class="image-container" style="display: inline-block; margin: 0 30px 0 0; max-width: 210px;">

		<div style="border-radius: 5px; background-color: #CCC; min-width: 200px; max-width: 200px; max-height: 200px; overflow: auto; min-height: 150px; background-image:  url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAIAAAACACAYAAADDPmHLAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEwAACxMBAJqcGAAABfhJREFUeJztnUtoXVUUhr+k6TM2ttJULdKX+MxEEBHBgRF0ID7AZ2OsDkQ6EenAqqgjlaoFBR+IilA7sKEBQXxMKhZFcCDWOrBiiyhSBWl9lCTY1DaJg51LQ5J79zpnn3PPY/8f/FCak7XW3fvPOfecve6+IIQQQgghhBBCCCGEEEIIIYQQQojYWQO8CHwPjANTUik0Pj0nzwPnN529QO4FRkvwYqXWGgHuaTKHqdkETJbgxUk2TQJ3zTuTs+gwHHMecBhYbgkoSsMIcBFwtNVBnYZAD6PJryI9uLlricUAt4bXIgriFt8BlkvAOLA4vBZRACeAZa0OsBhgKptaREFY5rgllnec28nx/lPM4QLgJWx3BMH4EryQRRKRilcpgQH0l18cl1MCA4jiWEXg/FhuA0WNkQEiRwaoNsGXYBkgcrqKLqDmbAQGgH6gD+id/v9jwEFgHzAE/FJIdUZ0F5CcDcAwMIF//CaAPcD6FHnOMcQPRgZIxmZgjORr+GPAYMJcMkDJeIzkEz9b2xLkkwFKxGbCJ78h65lgpSFWMDKAnw2kO+030yiwzpBXBigJw2Q3+Q0NGfLKACVgI7Z3+0k1gf/OINgAehAUzgD5jGMnrhu7FcF/gDJAOP05xr4+x9iADJAFfRWNDcgAWdDrPyQ1qz0/1yWg5uT+JlsGCOdYRWMDMkAWHCwwti4BJWBfjrE/yzG2GT0Ias0GinsQ1GOIE4wM4GcP2RtgtyHvckOcYGQAP+vJfjForSGvDDCLbmBRQbkHyc4AA8acMgDuOrgdOMKZa+d+4AEy+GBkQrYRPvmPJsgXvQHWAT/RvLZh2t/4Oki6vZRGsf/lNzjLEDeYshpgOW5nLF99u2j/mWAdbj3f2hS6G9s1fzbRGqAT+MhQW0MvF1Mm64EngL3A78Ap4L/pf+8FHiddN3CDaA2ww1DXbD1ZSKX50k2EBrjfUFMzbSmg3jyJzgDXELZD6QTG/fMqQlQGWAv8YajHp5PADW2sO0+WEYkBuoEDhlqsGgOublPteRKFATqA9w11JNVfuC1WqkwUBnjWUENa/YbtAxhlZSk1N8AmQ/5QHcbfe2ell3x7BGdTawNchdvpMm8DTOHWDnoCar0W+GZGvO+A6wLiWamtAdbgnpa1Y/Ib+hxYkqLWrcDpeeKdIv99lmtpgKXA14a8eegDYIGxzi7gTU+8E+R7JljiyV9JAwwZcuapnfgXj1YAnxrjjQBXph0MD7UzwNOGfO3QKy1q3Aj8kDDeMeDS1KPSnFoZ4HbK9bU0u3CrbTO5EzeZaeIdId2SbysWG/IG0w4DXEG2PXVZ6W9cw+dbuHf2ofEOke1tYi0McC7wqyFPXfQtYbecM6m8ARYDXxly1E1f4O52QllkyBVMngneNcSvqz4mvF+x0gbIooO26nqPsH7FyhrgZvL5OFUV9XrKMQRYaIgfTNYJ+nAPR4oe+DLpmRTjCBU0wCrgZ0PMGLU14VhCxQywELfgUvRAl1WTuE8zJaFSBnjbECt2nQZuSzCmXYaYwWSR4BFDHMlpHPv2cJUwwI3Mv14uNdcoriHGR+kNcAnwjyGGNFd/Apd5xneBIU4waROsxPXbFT2QVZavabW0BujC3jAhtVarptXSGuA1w+9Jdh0Azp5nnDsNvxtM0gRbEr44yaYvmbuCWDoD9OO6YYserLrqE9zDnwalMsCFuI9bFT1IddcQZzb47EgwP6mxJOjBbWta9ODEojemx70UBujEnZqKHpTY9BwZGCCLHbR2ADdlEEck4yncQ7YgLN0omZxGRC5M4Z/Dlj/XbuHVJnj7OxkgcmSAyJEBIkcGiBwZIHJkgMiRASJHBogcGSByZIDIkQEix2KAk7lXIfLiX98BFgP8mEEhohgO+Q6wGODDDAoRxeCdO8ty4mpcb/p8bcmivBwHLsbzFfSWM8BR4CHUGFIlJoEH8Ux+Uu5GO3tUQceBO5rM4RysGyOD6/rdifveuxXTave3cor5GcdtX/sOcB9u63ohhBBCCCGEEEIIIYQQQgghhBBCRMr/JWIY98/WGmEAAAAASUVORK5CYII='); background-position: center center; background-repeat: no-repeat;">
			<img alt="Image" class="main-img" id="<?=HTMLHelper::escape($id_uniq.'_img')?>" src="<?=HTMLHelper::escape(ZfImageFile::get_image_url($id_image_file))?>" style="width: 100%; border: solid 1px #777; border-radius: 5px; box-shadow: 1px 1px 1px rgba(0,0,0,0.4); visibility: hidden; cursor: pointer;" onmouseover="$(this).css({'border-color': '#00F'})" onmouseout="$(this).css({'border-color': ''})" />
		</div>

		<input class="image_title" type="hidden" name="<?=HTMLHelper::escape($name)?>_title" id="<?=HTMLHelper::escape($id_uniq.'_title')?>" value="<?=HTMLHelper::escape($image_file ? $image_file->get_title() : '')?>" />

		<?php if($enable_title) { ?>

			<div style="margin: 20px 0 0 0; font-weight: bold; " id="<?=HTMLHelper::escape($id_uniq.'_title_html')?>">
				<?=HTMLHelper::escape($image_file ? $image_file->get_title() : '')?>
			</div>

		<?php } ?>

	</div>

	<div style="display: inline-block; vertical-align: top; text-align: left;">

		<?php if($enable_select_local) { ?>
			<div class="local-select" style="font-weight: bold;">
				<a class="image-button local-link icon-link" href="javascript:void(0)" id="<?=$id_uniq?>_file_link">
					<span class="icon fa fa-desktop" style="display: inline-block; width: 20px; text-align: left;"></span>
					<span class="text"><?=LanguageHelper::get_text('select_image_local')?></span>
				</a>
				<input type="file" id="<?=$id?>" class="form-control" style="height: auto; padding: 10px 20px; background: #F5F5F5; display: none;" />
			</div>
		<?php } ?>

		<?php if($enable_select_url) { ?>
			<div style="margin-top: 20px; font-weight: bold;">
				<a class="image-button url-link icon-link" href="javascript:void(0)" id="<?=$id_uniq?>_url_link">
					<span class="icon fa fa-globe" style="display: inline-block; width: 20px; text-align: left;"></span>
					<span class="text"><?=LanguageHelper::get_text('select_image_url')?></span>
				</a>
			</div>
		<?php } ?>

		<?php if($enable_image_search) { ?>
			<div style="margin-top: 20px; font-weight: bold;">
				<a class="image-button search-link icon-link" href="javascript:void(0)" id="<?=$id_uniq?>_search_link">
					<span class="icon fa fa-search" style="display: inline-block; width: 20px; text-align: left;"></span>
					<span class="text"><?=LanguageHelper::get_text('search_images_online')?></span>
				</a>
			</div>
		<?php } ?>

		<?php if($enable_delete || ($enable_title && $enable_title_edit) || $enable_crop) { ?>

			<div style="margin: 20px 0 0 0; border-top: solid 1px #888; padding: 10px 0 0 0; display: none;" id="<?=$id_uniq?>_actions">

				<?php if($enable_title && $enable_title_edit) { ?>

					<div style="font-weight: bold; margin-top: 0px">
						<a href="javascript:void(0)" class="icon-link" id="<?=$id_uniq?>_edit_title_link">
							<span class="icon fa fa-info" style="display: inline-block; width: 20px; text-align: left;"></span>
							<span class="text">Cambiar T&iacute;tulo</span>
						</a>
					</div>

				<?php } ?>

				<?php if($enable_crop) { ?>

					<div style="font-weight: bold; margin-top: 0px">
						<a href="javascript:void(0)" class="icon-link" id="<?=$id_uniq?>_crop_link">
							<span class="icon fa fa-crop" style="display: inline-block; width: 20px; text-align: left;"></span>
							<span class="text">Recortar Imagen</span>
						</a>
					</div>

				<?php } ?>

				<?php if($enable_delete) { ?>

					<div style="font-weight: bold; margin: 10px 0 0 0; text-align: left;">
						<span class="checkbox input-checkbox">
							<input type="checkbox" id="<?=$id?>_delete" name="<?=$name?>_delete" value="<?=($id_image_file ? $id_image_file : '1')?>" class="align-middle" style="vertical-align: middle; position: relative; margin-right: 0; margin-left: 0;" <?=($delete_selected ? " checked='checked'" : "")?> />
							&nbsp;&nbsp;
							<label style="vertical-align: middle; width: auto !important;" class="align-middle underline" for="<?=$id?>_delete"><?=LanguageHelper::get_text('delete_image')?></label>
						</span>
					</div>

				<?php } ?>

			</div>

		<?php } ?>

	</div>


</div>

<script type="text/javascript">


	$('#<?=$id_uniq?>_container a').css({'color': '#183956'});
	$('.image-button').css({'color': '#183956'});

	$('#<?=HTMLHelper::escape($id_uniq.'_title')?>').bind('change', function() {
		$('#<?=HTMLHelper::escape($id_uniq.'_title_html')?>').html($(this).val());
	});

	$('#<?=$id_uniq?>_img').bind('load error', function(evt) {

		var $this = $(this);
		var $parent = $this.getParent();

		if($this.attr('src') && evt.type != 'error')
		{
			$this.css({'visibility': 'visible'});
			$('#<?=$id_uniq?>_actions').show();
			$parent.css({'background': 'transparent', 'border': '0', 'box-shadow': '0 0 0 rgba(0,0,0,0)', 'opacity': '1', 'min-height': ''});
			$this.getParent().next().show();
		}
		else
		{
			$this.css({'visibility': 'hidden'});
			$('#<?=$id_uniq?>_actions').hide();
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

		$(parent).find('#<?=$id_uniq?>_title').val(title).triggerHandler('change');

	});

	$('#<?=$id_uniq?>_edit_title_link').bind('click', function() {

		var title = $('#<?=$id_uniq?>_title').val();

		var newTitle = prompt('Titulo', title);

		if(newTitle)
		{
			$('#<?=$id?>').data('set_title').call(this, 'body', newTitle);
		}
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
							$.modalDialog.close();

							setTimeout(function() {
								parent.find('#<?=$id_uniq . '_img'?>').attr('src', data['content']);
								parent.find('#<?=$id_uniq . '_value'?>').val(data['content']);
								parent.find('#<?=$id?>').data('set_title').call($this, parent, value);
							}, 500);
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


	$('#<?=$id_uniq?>_file_link').bind('click', function() {


		if($('#<?=$id?>_delete').is(':checked'))
		{
			return;
		}


		$('#<?=$id?>').click();
	});

	$('#<?=$id?>_delete').bind('click', function() {

		var checked = $(this).is(':checked');

		if(checked)
		{
			$('#<?=$id?>').attr('disabled', true);
			$('#<?=$id?>').css({'opacity': 0.5});
			$('#<?=$id_uniq?>_search_link').css({'opacity': 0.5});
			$('#<?=$id_uniq?>_url_link').css({'opacity': 0.5});
			$('#<?=$id_uniq?>_file_link').css({'opacity': 0.5});
		}
		else
		{
			$('#<?=$id?>').attr('disabled', false);
			$('#<?=$id?>').css({'opacity': 1});
			$('#<?=$id_uniq?>_search_link').css({'opacity': 1});
			$('#<?=$id_uniq?>_url_link').css({'opacity': 1});
			$('#<?=$id_uniq?>_file_link').css({'opacity': 1});
		}

	});

	$('#<?=$id_uniq?>_img').bind('click', function() {

		var src = $(this).attr('src');
		$.modalDialog.image({'src': src, 'options': {'fill-window': false}});
	});

	//$('#<?=$id_uniq?>_url_link').triggerHandler('click');

</script>