<?php if(!empty($images)) { ?>

	<ul id="<?=$id?>">

		<?php foreach($images as $image) { ?>

			<li>
				<a href="<?=($image['href'] ? HTMLHelper::escape($image['href']) : 'javascript:void(0)')?>" title="<?=$image['title']?>"><img alt="<?=$image['title']?>" src="<?=HTMLHelper::escape(ZfImageFile::get_image_url($image['image'], $thumb_size))?>" /></a>
			</li>

		<?php } ?>

	</ul>

	<script type="text/javascript">

		$('#<?=$id?>').bxSlider({
			auto: true
		});

		$('#<?=$id?> .bx-viewport').height(500);
//		$('#<?//=$id?>// .bx-viewport a img').css({'width': '100%', 'height': <?//=$height?>//});
		$('#<?=$id?> .bx-viewport a img').css({'width': '100%'});

		$(window).bind('resize', function() {
			$('#<?=$id?> .bx-viewport').height(<?=$height?>);
		});


	</script>

<?php } ?>