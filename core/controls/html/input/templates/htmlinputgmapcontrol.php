<div class="gmap-control-block" style="margin: 0 auto; <? if($width) echo 'width: '.(is_numeric($width) ? ($width+2).'px' : "{$width}").'; '; ?>">

	<div class="gmap-container" style="<? if($width) echo 'width: '.(is_numeric($width) ? "{$width}px" : "{$width}").'; '; ?><? if($height) echo 'height: '.(is_numeric($height) ? "{$height}px" : "{$height}").'; '; ?>">
		<div id="<?=$varname?>-canvas" class="gmap" style="<? if($width) echo 'width: '.(is_numeric($width) ? "{$width}px" : "{$width}").'; '; ?><? if($height) echo 'height: '.(is_numeric($height) ? "{$height}px" : "{$height}").'; '; ?>"></div>
	</div>

	<? if($enable_change_type) { ?>

		<div class="gmap-type-controls">
			<a href="javascript: void(0)" title="<?=HTMLHelper::escape(ZString::get('gmap_view_type_roadmap'))?>" onclick="<?=$varname?>.setMapType('<?=HTMLInputGmapControl::GMAP_TYPE_ROADMAP?>'); $(this).parent().children().removeClass('selected'); $(this).addClass('selected');"<? if($map_type == HTMLInputGmapControl::GMAP_TYPE_ROADMAP) echo " class='selected'";?>><?=HTMLHelper::escape(ZString::get('gmap_view_type_roadmap'))?></a>
			<a href="javascript: void(0)" title="<?=HTMLHelper::escape(ZString::get('gmap_view_type_satellite'))?>" onclick="<?=$varname?>.setMapType('<?=HTMLInputGmapControl::GMAP_TYPE_SATELLITE?>'); $(this).parent().children().removeClass('selected'); $(this).addClass('selected');"<? if($map_type == HTMLInputGmapControl::GMAP_TYPE_SATELLITE) echo " class='selected'";?>><?=HTMLHelper::escape(ZString::get('gmap_view_type_satellite'))?></a>
			<a href="javascript: void(0)" title="<?=HTMLHelper::escape(ZString::get('gmap_view_type_hybrid'))?>" onclick="<?=$varname?>.setMapType('<?=HTMLInputGmapControl::GMAP_TYPE_HYBRID?>'); $(this).parent().children().removeClass('selected'); $(this).addClass('selected');"<? if($map_type == HTMLInputGmapControl::GMAP_TYPE_HYBRID) echo " class='selected'";?>><?=HTMLHelper::escape(ZString::get('gmap_view_type_hybrid'))?></a>
			<a href="javascript: void(0)" title="<?=HTMLHelper::escape(ZString::get('gmap_view_type_terrain'))?>" onclick="<?=$varname?>.setMapType('<?=HTMLInputGmapControl::GMAP_TYPE_TERRAIN?>'); $(this).parent().children().removeClass('selected'); $(this).addClass('selected');"<? if($map_type == HTMLInputGmapControl::GMAP_TYPE_TERRAIN) echo " class='selected'";?>><?=HTMLHelper::escape(ZString::get('gmap_view_type_terrain'))?></a>
		</div>

	<? } ?>
	
	<? if($listen_map_pos && $name) { ?>
	
		<input type="hidden" id="<?=HTMLHelper::escape("{$id}_pos_lat")?>" name="<?=HTMLHelper::escape("{$name}[pos][lat]")?>" value="<?=HTMLHelper::escape($init_pos->get_gmap_lat())?>" />
		<input type="hidden" id="<?=HTMLHelper::escape("{$id}_pos_lng")?>" name="<?=HTMLHelper::escape("{$name}[pos][lng]")?>" value="<?=HTMLHelper::escape($init_pos->get_gmap_lng())?>" />
		<input type="hidden" id="<?=HTMLHelper::escape("{$id}_pos_zoom")?>" name="<?=HTMLHelper::escape("{$name}[pos][zoom]")?>" value="<?=HTMLHelper::escape($init_pos->get_gmap_zoom())?>" />
	
	<? } ?>
	
</div>

<script type="text/javascript">

	var <?=$varname?> = function() {
		
		if(jQuery.fn['GMapWrapper'])
		{
			var map = $('#<?=$varname?>-canvas').GMapWrapper(<?=JSHelper::cast_obj($map_options)?>);
			<?php foreach($markers as $key => $marker) { ?> map.addMarker(<?=JSHelper::cast_str($key)?>, <?=JSHelper::cast_obj($marker)?>); <?php } ?>
			<?php if($auto_center_marker) { ?> map.centerMarker(); <?php } ?> <?php foreach($polygons as $polygon) { ?> map.addPolygon(<?=JSHelper::cast_array($polygon['points'])?>, <?=JSHelper::cast_obj($polygon['options'])?>); <?php } ?>		
			<?php if($listen_map_pos && $name) { ?> map.bindMapEventHandler('center_changed zoom_changed idle', function(evt) { var latLng = this.getCenter(); $(<?=JSHelper::cast_str("#{$id}_pos_lat")?>).val(latLng.lat()); $(<?=JSHelper::cast_str("#{$id}_pos_lng")?>).val(latLng.lng()); $(<?=JSHelper::cast_str("#{$id}_pos_zoom")?>).val(this.getZoom()); }); <?php } ?>
			return map;
		}
		else
		{
			setTimeout(function() { <?=$varname?>(); }, 500);
		}
	};		
	
	<?php if($load_on_ready): ?>
		$(document).ready(function() { <?=$varname?>();	});
	<?php else: ?>
		<?=$varname?>();		
	<?php endif; ?>
	
</script>