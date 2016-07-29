<div id="<?=$id?>_container" class="image-crop" style="visibility: hidden">

	<div class="float-left crop-image ">
		<img src="<?=HTMLHelper::escape($image_url)?>" id="<?=$id?>_img" class="crop image-crop" alt="Image Crop" />
	</div>
	<div class="clear"></div>

	<? if($enable_preview) { ?>
		<div class="float-left left-desc" style="display: none">
			
			<div class="float-left image-crop-preview" style="width:<?=$preview_width?>px;height:<?=$preview_height?>px;overflow:hidden;margin-left:5px;">
				<img src="<?=HTMLHelper::escape($image_url)?>" id="<?=$id?>_img_preview" class="crop image-crop-preview" alt="Image Crop Preview" />
			</div>
			
			<div class="help float-left">
				<p>Seleccione el área que se mostrar de la imagen. </p>
				<p>Arriba puede ver una vista previa de la selección</p>
				
				<? if($enable_button_optimal_crop) { ?>
					<div class="optimal-crop-button-container">
						<a class="optimal-crop-button" href="javascript:void(0)" title="Recorte Óptimo" onclick="$('#<?=$id?>_img').data('doOptimalCrop')()">Recorte Óptimo</a>
					</div>
				<? } ?>
				
			</div>	

		</div>	
	<? } else if($enable_button_optimal_crop) { ?>

		<div class="help">

			<br />
			<p>Seleccione el área que se mostrar de la imagen. </p>

			<div class="optimal-crop-button-container">
				<a class="optimal-crop-button underline" href="javascript:void(0)" title="Recorte Óptimo" onclick="$('#<?=$id?>_img').data('doOptimalCrop')()">Recorte Óptimo</a>
			</div>
			<br />
		</div>

	<? } ?>
	

	<input type="hidden" id="<?=$id?>_select_x" name="crop[x]" value="0" >
	<input type="hidden" id="<?=$id?>_select_y" name="crop[y]" value="0" >
	<input type="hidden" id="<?=$id?>_select_width" name="crop[w]" value="0" >
	<input type="hidden" id="<?=$id?>_select_height" name="crop[h]" value="0" >

</div>

<script type="text/javascript">
	

	function <?=$init_function_name?>(callback) {

		if(!jQuery.fn.Jcrop) {
			
			setTimeout(function() { <?=$init_function_name?>(callback); }, 100);
			
			return;
		}

		var cropImage = $('#<?=$id?>_img');
		var imageWidth = cropImage.width();
		var imageHeight = cropImage.height();

		var jcropParams = {};

		jcropParams['bgOpacity'] = 0.2;

		jcropParams['originalWidth'] = <?=JSHelper::cast_number($original_width)?>;
		jcropParams['originalHeight'] = <?=JSHelper::cast_number($original_height)?>;
		
		jcropParams['factorX'] = jcropParams['originalWidth'] ? (jcropParams['originalWidth'] / imageWidth) : 1;
		jcropParams['factorY'] = jcropParams['originalHeight'] ? (jcropParams['originalHeight'] / imageHeight) : 1;
		
		<? if($enable_preview) { ?>
		
			jcropParams['previewWidth'] = <?=  JSHelper::cast_number($preview_width)?>;
			jcropParams['previewHeight'] = <?=  JSHelper::cast_number($preview_height)?>;
			
		<? } ?>
		jcropParams['onSelect'] = function(coords) {
			$('#<?=$id?>_select_x').val(coords.x * jcropParams['factorX']);
			$('#<?=$id?>_select_y').val(coords.y * jcropParams['factorY']);
			$('#<?=$id?>_select_width').val(coords.w * jcropParams['factorX']);
			$('#<?=$id?>_select_height').val(coords.h * jcropParams['factorY']);
			
			<? if($enable_preview) { ?>

				jcropParams['previewHeight'] = <?=  JSHelper::cast_number($preview_height)?>;

				var rx = jcropParams['previewWidth'] / coords.w;
				var ry = jcropParams['previewHeight'] / coords.h;

				$('#<?=$id?>_img_preview').css({
					width: Math.round(rx * imageWidth) + 'px',
					height: Math.round(ry * imageHeight) + 'px',
					marginLeft: '-' + Math.round(rx * coords.x) + 'px',
					marginTop: '-' + Math.round(ry * coords.y) + 'px'
				});

			<? } ?>
			
		};
		
		jcropParams['onChange'] = jcropParams['onSelect'];
		
		<? if($min_size_width || $min_size_height) { ?>
		
			jcropParams['minSize'] = [<?=  JSHelper::cast_number($min_size_width) ?>, <?=  JSHelper::cast_number($min_size_height) ?>];
			jcropParams['minSize'][0] = jcropParams['minSize'][0] / jcropParams['factorX'];
			jcropParams['minSize'][1] = jcropParams['minSize'][1] / jcropParams['factorY'];
		
		<? } ?>	
			
			
		<? if($aspect) { ?>

			jcropParams['aspectRatio'] = <?=  JSHelper::cast_number($aspect) ?>;
		
			<? if($auto_select && (!$select_width && !$select_height)) { ?>

				var selectWidth = imageWidth;
				var selectHeight = imageWidth / jcropParams['aspectRatio'];
				
				if(selectHeight > imageHeight) {
					selectHeight = imageHeight;
					selectWidth = imageHeight * jcropParams['aspectRatio'];
				}
				
				var selectX = (imageWidth-selectWidth) / 2;
				var selectY = (imageHeight-selectHeight) / 2;

				
				jcropParams['setSelect'] = [selectX, selectY, selectWidth, selectHeight];

			<? } ?>

		
		<? } else if($auto_select && (!$select_width && !$select_height)) { ?>
		
			jcropParams['setSelect'] = [0, 0, imageWidth, imageHeight];
		
		<? } ?>
			
			
		<? if($select_width || $select_height) { ?>
			
			jcropParams['setSelect'] = [0,0,0,0];
			
			<? if(!is_null($select_width)) { ?>

				jcropParams['setSelect'][2] = <?=JSHelper::cast_number($select_width)?> / jcropParams['factorX'];
				
			<? } else { ?>
				
				jcropParams['setSelect'][2] = imageWidth;
				
			<? } ?>
				
			<? if($auto_select && is_null($select_x)) { ?>
				
				jcropParams['setSelect'][0] = (imageWidth-jcropParams['setSelect'][2])/2;
				
			<? } else { ?>
				
				jcropParams['setSelect'][0] = <?=JSHelper::cast_number($select_x)?> / jcropParams['factorX'];
				jcropParams['setSelect'][2] = jcropParams['setSelect'][2] + jcropParams['setSelect'][0];
				
			<? } ?>
				
				
			<? if(!is_null($select_height)) { ?>
				
				jcropParams['setSelect'][3] = <?=JSHelper::cast_number($select_height)?> / jcropParams['factorY'];
				
			<? } else { ?>
				
				jcropParams['setSelect'][3] = imageHeight;
				
			<? } ?>
				
			<? if($auto_select && is_null($select_y)) { ?>
				
				jcropParams['setSelect'][1] = (imageHeight-jcropParams['setSelect'][3])/2;
				
			<? } else { ?>
				
				jcropParams['setSelect'][1] = <?=JSHelper::cast_number($select_y)?> / jcropParams['factorY'];
				jcropParams['setSelect'][3] = jcropParams['setSelect'][3] + jcropParams['setSelect'][1];
				
			<? } ?>	
			
		<? } ?>	
		
		<? if($enable_button_optimal_crop) { ?>
		cropImage.data('doOptimalCrop', function() {
			
			var optimalCrop = [0,0,0,0];
			optimalCrop[0] = <?=JSHelper::cast_number($optimal_crop[0])?> / jcropParams['factorX'];
			optimalCrop[1] = <?=JSHelper::cast_number($optimal_crop[1])?> / jcropParams['factorY'];
			optimalCrop[2] = <?=JSHelper::cast_number($optimal_crop[2])?> / jcropParams['factorX'];
			optimalCrop[3] = <?=JSHelper::cast_number($optimal_crop[3])?> / jcropParams['factorY'];
			
			$('#<?=$id?>_img').Jcrop({'setSelect': optimalCrop});
		});
		<? } ?>

		cropImage.Jcrop(jcropParams);
	
		$('#<?=$id?>_container .left-desc').show();
		$('#<?=$id?>_container').css('visibility', 'visible');
		
	}
	
	
	$('#<?=$id?>_img').bind('load', <?=$init_function_name?>);


</script>

	