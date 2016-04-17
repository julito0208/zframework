<?php if($show_icon_block): ?>

	<style type="text/css">
		.icon-block
		{
			border: solid 1px #333;
			margin: 0 35px 35px 0;
			background: #FFF;
			border-radius: 5px;
			width: 120px;
			height: 140px;
			text-align: center;
			vertical-align: top;
			box-shadow: 1px 1px 1px rgba(0,0,0,0.3);
		}

		#container
		{
			padding: 20px;
			text-align: left;
			text-justify: distribute;
		}

	</style>

	<div id="container">

		<?php foreach ($names as $index => $name): ?>

			<div class='icon-block icon-block-white' onclick='selectIcon(this)' onmouseover='showIcon(this)' onmouseout='showIcon(null)'>
				<span style='font-size: 16pt; margin-top: 10px' class='glyphicon <?=$name?>'></span>
				<div style="padding-top: 10px; font-size: 10pt;">glyphicon <?=$name?></div>
			</div>

		<?php endforeach; ?>

	</div>

<?php else: ?>

	<div id="container">

		<?php foreach ($names as $index => $name): ?>

			<div class='icon-block' onclick='selectIcon(this)' onmouseover='showIcon(this)' onmouseout='showIcon(null)'>
				<span style='font-size: 16pt; margin-top: 10px' class='glyphicon <?=$name?>'></span>
			</div>

		<?php endforeach; ?>

	</div>

<?php endif; ?>