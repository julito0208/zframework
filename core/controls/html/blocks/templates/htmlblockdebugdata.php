
<style type="text/css">

	.data-debug-button
	{
		color: #777;
	}

	.data-debug-button.loaded
	{
		color: #007500;
	}

	.data-debug-button.loaded.opened
	{
		color: #00AC00;
	}

</style>

<div id="zphp-debug-block" style="width: 100%; margin: 0 0 30px 0; font-family: sans; position: absolute; opacity: 0.7; font-size: 9pt;z-index: 999999999999999; top: 0;" onmouseenter="$(this).css({'opacity': 1})" onmouseleave="$(this).css({'opacity': '0.7'})">

	<div id="zphp-debug-block" style="width: 100%; border: solid 1px #000; position: absolute; margin: 0 0 30px 0; top: 0; left: 0; z-index: 9999">

		<div style="padding: 4px 10px;background: #333; ">

			<div style="color: #1285C5; float: left; font-weight: bold;">
				Zframework Debug
				&nbsp;&nbsp;
			</div>

			<a class="data-debug-button loaded" style="display: block; text-decoration: underline; font-weight: bold; float:left; margin-left: 15px;" onclick="$('#debug-data-list-container').css({'max-height': $(window).height() - 100}); $('#debug-data-list').slideToggle(); $(this).toggleClass('opened'); " id="debug-data-sql-button">Debug Data (<?=count($debug_data)?>)</a>
			<a class="data-debug-button" style="display: block; text-decoration: underline; font-weight: bold; float:left; margin-left: 15px;" onclick="if($('#debug-scripts-list').children().length > 0) { $('#debug-data-list-container').css({'max-height': $(window).height() - 100}); $('#debug-scripts-list').slideToggle(); $(this).toggleClass('opened');}" id="debug-data-js-button">JS Scripts <span class="length">(0)</span></a>
			<a class="data-debug-button" style="display: block; text-decoration: underline; font-weight: bold; float:left; margin-left: 15px;" onclick="if($('#debug-styles-list').children().length > 0) { $('#debug-data-list-container').css({'max-height': $(window).height() - 100}); $('#debug-styles-list').slideToggle();$(this).toggleClass('opened'); }" id="debug-data-css-button">Styles <span class="length">(0)</span></a>
			<a class="data-debug-button" style="display: block; text-decoration: underline; font-weight: bold; float:left; margin-left: 15px;" onclick="if($('#debug-images-list').children().length > 0) { $('#debug-data-list-container').css({'max-height': $(window).height() - 100}); $('#debug-images-list').slideToggle(); $(this).toggleClass('opened');}" id="debug-data-img-button">Images <span class="length">(0)</span></a>

			<div style="float: right; color: #FFF;">
				<label>Size: </label>
				<span data-size="0" id="debug-data-total-size">0</span>
			</div>

			<div style="clear:both"></div>

		</div>

		<div style="padding: 0 30px; position: absolute; width: 100%">

			<div style="overflow: auto; margin: 0 0px; box-shadow: 7px 7px 9px rgba(0,0,0,0.8);  " id="debug-data-list-container">
				<ul id="debug-data-list" style="display:none; margin: 0;border: solid 1px #777; list-style: none; margin: 0 !important; padding: 0 !important;">
					<?php foreach ($debug_data as $index => $debug_item): ?>
						<li style="list-style: none; border-top: solid 1px #888; background: <?=($index % 2 == 0 ? '#F2F2F2' : '#FEFEFE')?>;">
							<div style="padding: 2px 5px;">
								<div style="float:left; font-weight: bold;">
									<?=$debug_item['title']?>
								</div>
								<div style="float:right; font-weight: bold;">
									<?=strftime('%d/%m/%Y %H-%M-%I', $debug_item['time'])?>
								</div>
							</div>
							<div style="clear:both"></div>
							<div style="padding: 2px 8px;"><?=$debug_item['data']?></div>
						</li>
					<?php endforeach; ?>

				</ul>

				<ul id="debug-scripts-list" style="display:none; margin: 0;border: solid 1px #777;"></ul>

				<ul id="debug-styles-list" style="display:none; margin: 0;border: solid 1px #777;"></ul>

				<ul id="debug-images-list" style="display:none; margin: 0;border: solid 1px #777;"></ul>

			</div>

		</div>

	</div>

</div>

<script type="text/javascript">

	function addDebugDataSize(size)
	{
		var node = $('#debug-data-total-size');

		var total = parseInt(node.attr('data-size'));
		total+=size;

		node.html(Number.formatSize(total, 2));
		node.attr('data-size', total);
	}

	$(document).ready(function() {

		var scripts = [];

		$('script[src]').each(function(index, item) {
			scripts.push($(item).attr('src'));
		});

		var styles = [];

		$('link[rel="stylesheet"][href]').each(function(index, item) {
			styles.push($(item).attr('href'));
		});

		var images = [];

		$('img[src]').each(function(index, item) {
			images.push($(item).attr('src'));
		});

		function loadDebugList(urls, buttonId, listId)
		{
			$.ajax({
				'url': '!HTMLBlockDebugData(get_resources_sizes)',
				'type': 'post',
				'data': {'urls': urls},
				'success': function(data)
				{
					$('#' + buttonId).addClass('loaded').find('.length').html('('+data['total_formatted']+')');

					var list = $('#' + listId);

					$.each(data['sizes'], function(index, item) {

						var url = item['url'];
						var formattedSize = item['size_formatted'];

						var listItem = $('<li />').
						css({'border-top': 'solid 1px #888', 'background': (index % 2 == 0) ? '#F2F2F2' : '#FEFEFE', 'padding': '5px 5px'}).
						appendTo(list);

						var containerDiv = $('<div />').
						appendTo(listItem).
						css({'padding': '5px 5px'});

						var containerLeft = $('<div />').
						appendTo(containerDiv).
						css({'float': 'left', 'font-weight': 'bold'}).
						html($('<a />').html(url).attr({'href': url, 'target': '_blank'}));

						var containerRight = $('<div />').
						appendTo(containerDiv).
						css({'float': 'right', 'font-weight': 'bold'}).
						html(formattedSize);

						listItem.append("<div style='clear:both'></div>");

					});

					addDebugDataSize(data['total']);

				}
			});
		}

		loadDebugList(scripts, 'debug-data-js-button', 'debug-scripts-list');
		loadDebugList(styles, 'debug-data-css-button', 'debug-styles-list');
		loadDebugList(images, 'debug-data-img-button', 'debug-images-list');

	});

</script>