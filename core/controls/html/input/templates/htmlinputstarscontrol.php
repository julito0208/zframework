
<input type="hidden" id="<?=$id?>" name="<?=$name?>" />

<span class="stars-input" data-input="<?=$id?>" style="cursor: pointer;color: #B8B644;text-shadow: 1px 1px 1px rgba(0,0,0,0.2)">

	<?php for($i=0; $i<$stars; $i++) { ?>

		<span class="star fa fa-star-o"></span>

	<?php } ?>

</span>

<script type="text/javascript">

	(function() {

		var stars = <?=$stars?>;
		var decimal = <?=JSHelper::cast_bool($decimal)?>;
		var input = $('#<?=$id?>');
		var container = $('[data-input=<?=$id?>]');
		var containerPosition = null;
		var containerSize = null;


		function drawStars(value) {

			value = parseFloat(value);

			var stars = container.find('.star');

			stars.removeClass('fa-star').addClass('fa-star-o');

			if(value > 0.3) {
				stars.slice(0, Math.ceil(value)).removeClass('fa-star-o').addClass('fa-star');
			}

		}

		function setValue(value) {

			drawStars(value);
			input.val(Math.round(value));
		}

		function resetValue() {

			drawStars(input.val());

		}

		container.on('mouseover', function(evt) {

			if(!containerPosition) {
				return;
			}

			var mouseX = evt.pageX;
			var containerLeft = containerPosition.left;
			var containerWidth = containerSize.width;

			var posX = mouseX - containerLeft;
			var percent = (posX / parseFloat(containerWidth)) * 100;

			var value = (percent * parseFloat(stars)) / 100;

			drawStars(value);
		});

		container.on('mouseenter', function(evt) {
			containerPosition = container.offset();
			containerSize = container.size();
		});

		container.on('mouseleave', function(evt) {
			resetValue();
		});

		container.on('click', function(evt) {

			var count = container.find('.fa.fa-star').length;
			setValue(count);
		});

		setStarsValue(<?=JSHelper::cast_number($value)?>);

	})();

</script>