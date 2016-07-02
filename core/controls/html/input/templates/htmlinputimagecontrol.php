<div class="image-input" style="vertical-align: top; margin: 10px 0 0 10px;">

	<input type="hidden" name="<?=HTMLHelper::escape($name)?>" id="<?=HTMLHelper::escape($id_uniq.'_value')?>" />

	<div class="image-container" style="display: inline-block; margin: 0 20px 0 0;">

		<img alt="Image" class="main-img" id="<?=HTMLHelper::escape($id_uniq.'_img')?>" src="<?=HTMLHelper::escape(ZfImageFile::get_image_url($value, ZfImageThumbType::get_thumb(200)))?>" style="max-width: <?=$image_width?>px; max-height: <?=$image_height?>px; border: solid 1px #777; border-radius: 5px; box-shadow: 1px 1px 1px rgba(0,0,0,0.4); " />

	</div>

	<div style="display: inline-block; vertical-align: top;">

		<div style="">
			<input type="file" id="<?=$id?>" class="form-control" style="height: auto; padding: 10px 20px; background: #F5F5F5;" />
		</div>

		<div style="margin-top: 20px">
			<a href="javascript:void(0)" id="<?=$id_uniq?>_search_link"><?=LanguageHelper::get_text('search_images_online')?></a>
		</div>


	</div>


</div>

<script type="text/javascript">

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

		}, 'mayonesa');

	});

</script>