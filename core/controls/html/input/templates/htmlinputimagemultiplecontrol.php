<div class="image-input" style="vertical-align: top; margin: 10px 0 0 10px;" id="<?=$id_uniq?>_container">

	<div style="display: none" class="inputs"></div>

	<div class="images-empty-container" style="display: none; margin: 0 20px 40px 0; width: <?=$image_width?>px; height: <?=$image_height?>px; background: #CCC; background: #777 url(/zframework/static/icons/photo.png) center center no-repeat; border-radius: 5px; border: solid 1px #222; box-shadow: 1px 1px 1px rgba(0,0,0,0.7); opacity: 0.7;"></div>

	<div class="images-container" style="display: none; margin: 0 20px 20px 0;  "></div>

	<div style="display: inline-block; vertical-align: top;">

		<div style="">
			<input type="file" multiple="multiple" id="<?=$id?>" class="form-control" style="height: auto; padding: 10px 20px; background: #F5F5F5;" />
		</div>

		<div style="margin-top: 20px; font-weight: bold;">
			<a href="javascript:void(0)" id="<?=$id_uniq?>_search_link"><?=LanguageHelper::get_text('search_images_online')?></a>
		</div>

		<?php if(false && $enable_delete) { ?>

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

//	$('#<?//=$id_uniq?>//_img').bind('load error', function(evt) {
//
//		var $this = $(this);
//		var $parent = $this.getParent();
//
//		if($this.attr('src') && evt.type != 'error')
//		{
//			$this.css({'visibility': 'visible'});
//			$parent.css({'background': 'transparent'});
//			$parent.css({'background': 'transparent', 'border': '0', 'box-shadow': '0 0 0 rgba(0,0,0,0)', 'opacity': '1'});
//		}
//		else
//		{
//			$this.css({'visibility': 'hidden'});
//			$parent.css({'background': '#777 url(/zframework/static/icons/photo.png) center center no-repeat', 'border-radius': '5px', 'border': 'solid 1px #222', 'box-shadow': '1px 1px 1px rgba(0,0,0,0.7)', 'opacity': '0.7'});
//		}
//
//	});

	$('#<?=$id?>').bind('update-images', function() {

		if($('#<?=$id_uniq?>_container .inputs input').length > 0)
		{
			$('#<?=$id_uniq?>_container .images-empty-container').hide();
			$('#<?=$id_uniq?>_container .images-container').show();
		}
		else
		{
			$('#<?=$id_uniq?>_container .images-empty-container').show();
			$('#<?=$id_uniq?>_container .images-container').hide();
		}

	});

	var prepareImage = function(node) {

		var $node = $(node);

		$node.css({
			'cursor': 'pointer',
			'border': 'solid 1px #888',
			'border-radius': '5px',
			'box-shadow': '1px 1px 1px rgba(0,0,0,0.4)',
			'max-width': '<?=$image_width?>px',
			'height': 150,
			'display': 'inline-block',
			'margin': '0 20px 0px 0'
		});

		$node.bind('mouseover', function() {
			$(this).css({'border-color': '#00F'});
		});

		$node.bind('mouseout', function() {
			$(this).css({'border-color': ''});
		});

		$node.bind('click', function() {
			var src = $(this).attr('src');
			$.modalDialog.image({'src': src, 'options': {'fill-window': true}});
		});
	};

	function addInputImage(content, idImageFile)
	{
		var add = true;

		$('#<?=$id_uniq?>_container .images-container img').each(function(index, item) {

			if($(this).attr('src') == content)
			{
				add = false;
				return false;
			}

		});

		if(add) {

			if(arguments.length > 1)
			{
				var block;

				if($('#<?=$id_uniq?>_container .images-container .block').length > 0)
				{
					block = $('#<?=$id_uniq?>_container .images-container .block');
				}
				else
				{
					block = $('<div />').css({'display': 'inline-block', 'margin': '0 20px 0px 0'}).addClass('block').appendTo($('#<?=$id_uniq?>_container .images-container'));
				}


				var imageBlock = $('<div />').css({'padding': '5px 25px 0 0', 'text-align': 'right', 'display': 'inline-block', 'margin': '0 0 30px 0'}).appendTo(block);
				var image = $('<img />').attr({'src': content}).appendTo(imageBlock);
				image.data('id_image_file', idImageFile);


				<?php if($enable_delete) { ?>

					var buttonsBlock = $('<div />').css({'padding': '5px 25px 0 0', 'text-align': 'right', 'display': 'block'}).appendTo(imageBlock);
					var buttonDelete = $('<a />').html("<span class='text text-danger fa fa-remove'></span> Eliminar").appendTo(buttonsBlock);

					buttonDelete.bind('click', function() {

						$.zmodal.confirm('Est&acute; seguro que desea eliminar esta imagen?', function() {

							$.zmodal.loading(function() {

								<?php if($remove_url) { ?>

									$.ajax({
										'url': <?=JSHelper::cast_str($remove_url)?>,
										'type': <?=JSHelper::cast_str($remove_method)?>,
										'data': $.extend({}, {'id_image_file': idImageFile}, <?=JSHelper::cast_obj($remove_data)?>),
										'success': function(data)
										{
											imageBlock.remove();
											$('#<?=$id_uniq?>_container .inputs input[data-id-image-file='+idImageFile+']').remove();
											$('#<?=$id?>').triggerHandler('update-images');
											$.zmodal.closeAll();

										}
									});


								<?php } else { ?>

									imageBlock.remove();
									$('#<?=$id_uniq?>_container .inputs input[data-id-image-file='+idImageFile+']').remove();
									$('#<?=$id?>').triggerHandler('update-images');
									$.zmodal.closeAll();

								<?php } ?>

							});

						});

					});

				<?php } ?>

				var input = $('<input type="hidden" />').appendTo($('#<?=$id_uniq?>_container .inputs')).attr({'data-id-image-file': idImageFile}).val(content);

				$('#<?=$id?>').triggerHandler('update-images');

				prepareImage(image);
			}
			else
			{
				<?php if($add_url) { ?>
					$.ajax({
						'url': <?=JSHelper::cast_str($add_url)?>,
						'type': <?=JSHelper::cast_str($add_method)?>,
						'data': $.extend({}, {'src': content}, <?=JSHelper::cast_obj($add_data)?>),
						'success': function(data)
						{
							addInputImage(content, data['id_image_file']);
						}
					});
				<?php } else { ?>
					addInputImage(content, 0);
				<?php } ?>
			}

		}
	};

	$('#<?=$id?>').bind('change', function() {

		var img = $('#<?=$id_uniq.'_img'?>').get(0);
		var files = $(this).get(0).files;

//		$('#<?//=$id_uniq?>//_container .inputs').empty();
//		$('#<?//=$id_uniq?>//_container .images-container').empty();

		if(files.length > 0)
		{
			for(var i=0; i<files.length; i++) {

				var file = files[i];
				var reader = new FileReader();

				reader.addEventListener("load", function () { addInputImage(this.result); }, false);

				if (file) {
					reader.readAsDataURL(file);
				}
			}
		}

		$('#<?=$id?>').triggerHandler('update-images');

	});

	$('#<?=$id_uniq?>_search_link').bind('click', function() {

		$.modalDialog.imagesSearch(function(url){

//			$.modalDialog.loading('Cargando...', function () {
				$.ajax({
					'url': '!HTMLInputImageControl(url_base64)',
					'type': 'post',
					'data': {'url': url},
					'success': function (data) {
						addInputImage(data['content']);
					}
				});
//			});

		});

	});


	<?php foreach($images as $image) { ?>

		addInputImage(<?=JSHelper::cast_str(ZfImageFile::get_image_url($image))?>, <?=JSHelper::cast_str($image->get_id_image_file())?>);

	<?php } ?>

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


	$('#<?=$id?>').triggerHandler('update-images');

</script>