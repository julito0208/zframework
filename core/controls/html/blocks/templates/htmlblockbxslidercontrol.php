<?php if(!empty($images)) { ?>

	<div class="bxslider-container">

		<ul id="<?=$id?>" class="bxslider zbxslider">

			<?php foreach($images as $image) { ?>

				<li style="">
					<a href="<?=($image['href'] ? HTMLHelper::escape($image['href']) : 'javascript:void(0)')?>" title="<?=$image['title']?>">
						<img alt="<?=$image['title']?>" src="<?=HTMLHelper::escape(ZfImageFile::get_image_url($image['image'], $thumb_type))?>" />
					</a>
				</li>

			<?php } ?>

		</ul>
	</div>

	<script type="text/javascript">

		$(document).ready(function() {
			$('#<?=$id?>').bxSlider($.extend(
				{},
				<?=JSONMap::serialize($json_params)?>,
				{
					'onSliderLoad': function()
					{
						var slider = $('#<?=$id?>').addClass('loaded');
						var sliderParent = slider.getParent();
						var sliderImages = slider.find('li img');

						<?php if($height): ?>
							sliderParent.height(<?=$height?>);
							sliderParent.getParent().height(<?=$height?>);

							$(window).bind('resize', function() {
								setTimeout(function() {
									sliderParent.height(<?=$height?>);
									sliderParent.getParent().height(<?=$height?>);
								}, 100);
							});
						<?php endif; ?>

						var sliderHeight = sliderParent.height() + 2;

						sliderImages.each(function(index, image) {

							var $image = $(image);
							var imageHeight = $image.height();

							if(imageHeight > sliderHeight)
							{
								$image.css({'position': 'relative', 'top': -((imageHeight-sliderHeight)/2)});
							}
							else if(imageHeight < sliderHeight)
							{
								$image.css({'height': sliderHeight});
							}



						});

						$('#<?=$id?>').triggerHandler('bxslider-load');
					}
				}
			));
		});

	</script>

<?php } ?>