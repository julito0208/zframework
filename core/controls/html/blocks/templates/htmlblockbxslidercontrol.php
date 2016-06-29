<?php if(!empty($images)) { ?>

	<ul id="<?=$id?>">

		<?php foreach($images as $image) { ?>

			<li>
				<a href="<?=($image['href'] ? HTMLHelper::escape($image['href']) : 'javascript:void(0)')?>" title="<?=$image['title']?>"><img alt="<?=$image['title']?>" src="<?=HTMLHelper::escape(ZfImageFile::get_image_url($image['image'], $thumb_type))?>" /></a>
			</li>

		<?php } ?>

	</ul>

	<script type="text/javascript">

		$(document).ready(function() {
			$('#<?=$id?>').bxSlider(<?=JSONMap::serialize($json_params)?>);
		});

	</script>

<?php } ?>