<style type="text/css">

	.right-html
	{
		width: 500px;

	}

	.icon-block-html
	{
		border: solid 1px #333;
		margin: 0 35px 35px 0;
		background: #FFF;
		border-radius: 5px;
		width: 90px;
		height: 90px;
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

	#keys
	{
		margin: 10px 10px 0;
		padding: 0 0 10px 0;
		border-bottom: solid 1px #333;
	}

	.title
	{
		/*border-bottom: solid 1px #666;*/
		font-size: 14pt;
		font-weight: bold;
		border: solid 1px #333;
		margin: 20px 0 30px;
		background: #DDD;
		padding: 10px;
	}

</style>


<div id="keys">

	<?php foreach ($keys as $key): ?>

		<span style='margin: 2px 10px 0 0px; line-height: 25px; text-align: right; '>

			<? if($selected_category == $key) { ?>
				<span style='color: #777; font-weight: bold; white-space: nowrap;'><?=$key?></span>
			<? } else { ?>
				<a href='<?=NavigationHelper::make_url_query(array($key_varname => $key))?>' class='color-link' style=''><?=$key?></a>
			<? } ?>

		</span>

	<?php endforeach; ?>

</div>

<div id="container">

	<?php foreach ($categories as $key => $category): ?>

		<?php if($selected_category != 'all' && $selected_category != $key) continue; ?>

		<div class="title"><?=$category['title']?></div>

		<?php foreach ($category['icons'] as $icon): ?>

			<div class='<?=($show_icon_block ? 'icon-block icon-block-html icon-block-white' : 'icon-block')?>' onclick='selectIcon(this)' onmouseover='showIcon(this)' onmouseout='showIcon(null)'>
				<span style='font-size: 16pt; margin-top: 10px;' class='fa fa-<?=$icon?>'></span>
				<?php if($show_icon_block) { ?><div style="padding-top: 10px; font-size: 10pt;">fa fa-<?=$icon?></div><?php } ?>
			</div>

		<?php endforeach; ?>

	<?php endforeach; ?>

</div>