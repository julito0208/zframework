

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

<div id="zphp-debug-block" style="width: 100%; margin: 0 0 30px 0; font-family: sans; font-size: 9pt;z-index: 100000; top: 0;">

	<div id="zphp-debug-block" style="width: 100%; border: solid 1px #000; position: absolute; margin: 0 0 30px 0; top: 0; left: 0; z-index: 9999">

		<div style="padding: 4px 10px;background: #333; ">

			<div style="color: #1285C5; float: left; font-weight: bold;">
				Zframework Debug
				&nbsp;&nbsp;
			</div>

			<a data-list-target='debug-data-list' class="data-debug-button loaded" style="display: block; text-decoration: underline; font-weight: bold; float:left; margin-left: 15px;" onclick1="$('#debug-data-list-container').css({'max-height': $(window).height() - 100}); $('#debug-data-list').slideToggle(); $(this).toggleClass('opened'); " id="debug-data-sql-button">Debug Data (<span class="count"><?=$count_debug_data?></span>)</a>

			<span class="checkbox" style="display: inline; padding-left: 20px;">
				<input type="checkbox" id="debug-data-remember-checkbox" value="1" <?=(ZPHP::get_debug_data_remember() ? ' checked="checked"' : '')?> style="margin: 0; vertical-align: middle; position: static; " />
				<label style="vertical-align: middle; display: inline; color: #FFF; cursor: pointer; text-decoration: underline;" for="debug-data-remember-checkbox">Recordar Debug</label>
			</span>

			&nbsp;&nbsp;
			<a id="debug-data-clear-button" href="javascript:void(0)" style="color:#FFF; text-decoration: underline;">Limpiar Debug</a>

			<div style="float: right; color: #FFF;">

				<a data-list-target='debug-scripts-list' class="data-debug-button" style="display: block; text-decoration: underline; font-weight: bold; float:left; margin-left: 15px;" onclick1="if($('#debug-scripts-list').children().length > 0) { $('#debug-data-list-container').css({'max-height': $(window).height() - 100}); $('#debug-scripts-list').slideToggle(); $(this).toggleClass('opened');}" id="debug-data-js-button">JS Scripts <span class="length">(0)</span></a>
				<a data-list-target='debug-styles-list' class="data-debug-button" style="display: block; text-decoration: underline; font-weight: bold; float:left; margin-left: 15px;" onclick1="if($('#debug-styles-list').children().length > 0) { $('#debug-data-list-container').css({'max-height': $(window).height() - 100}); $('#debug-styles-list').slideToggle();$(this).toggleClass('opened'); }" id="debug-data-css-button">Styles <span class="length">(0)</span></a>
				<a data-list-target='debug-images-list' class="data-debug-button" style="display: block; text-decoration: underline; font-weight: bold; float:left; margin-left: 15px;" onclick1="if($('#debug-images-list').children().length > 0) { $('#debug-data-list-container').css({'max-height': $(window).height() - 100}); $('#debug-images-list').slideToggle(); $(this).toggleClass('opened');}" id="debug-data-img-button">Images <span class="length">(0)</span></a>

				<label style="padding-left: 30px;">Size: </label>
				<span data-size="0" id="debug-data-total-size">0</span>
			</div>

			<div style="clear:both"></div>

		</div>

		<div style="padding: 0 30px; position: absolute; width: 100%">

			<div style="overflow: auto; margin: 0 0px; box-shadow: 0 9px 9px 10px rgba(0, 0, 0, 0.8); display: none " id="debug-data-list-container">
				<ul class='debug-list' id="debug-data-list" style="display:none; margin: 0;border: solid 1px #777; list-style: none; margin: 0 !important; padding: 0 !important;">

					<?php foreach ($debug_data as $index => $debug_data_request): ?>

						<?php if(empty($debug_data_request['items'])) continue; ?>

						<li class="debug-request-title" data-request-id="<?=$debug_data_request['request']?>" style="list-style: none; border-top: solid 1px #888; font-weight: bold; ">
							<a href="javascript:void(0)" style="display: block; padding: 5px 10px;"  onmouseover="$(this).css({'background': '#CCC'}); $(this).find('.url').css({'text-decoration': 'underline'});" onmouseout="$(this).css({'background': ''}); $(this).find('.url').css({'text-decoration': 'none'});">
								<div style="float: left">
									<span class="sign" style="color: #111">
										<span class="fa-stack">
										  <i class="fa fa-square fa-stack-2x"></i>
										  <i class="fa fa-plus fa-stack-1x fa-inverse"></i>
										</span>
									</span>
									&nbsp;
									<span class="url" style="text-decoration: none">
										<?=$debug_data_request['url']?>
									</span>
								</div>
								<div style="float:right; color: #888">
								 	<?=$debug_data_request['request']?>
								</div>
								<div style="clear:both"></div>
							</a>
						</li>

						<?php foreach($debug_data_request['items'] as $debug_item) { ?>

							<li class="debug-request-item" data-request-id="<?=$debug_data_request['request']?>" style="list-style: none; padding: 0 20px; border-top: solid 1px #888; display: none;">
								<div style="padding: 2px 5px;">
									<div style="float:left; font-weight: bold;">
										<?=$debug_item['title']?>
									</div>
									<div style="float:right; font-weight: bold;">
										<?=strftime('%d/%m/%Y %H:%M:%S', $debug_item['time'])?>
									</div>
								</div>
								<div style="clear:both"></div>
								<div style="padding: 2px 8px;"><?=HTMLHelper::escape($debug_item['data'])?></div>
							</li>

						<?php } ?>

					<?php endforeach; ?>

				</ul>

				<ul class='debug-list' id="debug-scripts-list" style="display:none; margin: 0;border: solid 1px #777; padding: 0;"></ul>

				<ul class='debug-list' id="debug-styles-list" style="display:none; margin: 0;border: solid 1px #777;padding: 0;"></ul>

				<ul class='debug-list' id="debug-images-list" style="display:none; margin: 0;border: solid 1px #777;padding: 0;"></ul>

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
						css({'border-top': 'solid 1px #888', 'background': (index % 2 == 0) ? '#F2F2F2' : '#FEFEFE', 'list-style': 'none'}).
						appendTo(list);

						var containerDiv = $('<a />').
						attr({'href': url, 'target': '_blank'}).
						appendTo(listItem).
						css({'padding': '5px 5px', 'display': 'block'}).
						bind('mouseover', function() {
							$(this).css('background', '#CCC');
						}).bind('mouseout', function() {
							$(this).css('background', '');
						});

						var containerLeft = $('<div />').
						html(url).
						appendTo(containerDiv).
						css({'float': 'left', 'font-weight': 'bold'});

						var containerRight = $('<div />').
						appendTo(containerDiv).
						css({'float': 'right', 'font-weight': 'bold'}).
						html(formattedSize);

						containerDiv.append("<div style='clear:both'></div>");


					});

					addDebugDataSize(data['total']);

				}
			});
		}

		$('#zphp-debug-block .data-debug-button').css({'cursor': 'pointer'});
		$('#zphp-debug-block .data-debug-button').bind('click', function() {

			var $this = $(this);

			if($this.hasClass('loaded'))
			{
				var listTargetId = $this.attr('data-list-target');
				var listTarget = $('#' + listTargetId);
				var isOpened = listTarget.is(':visible');

				$('#debug-data-list-container').hide();
				$('#zphp-debug-block .debug-list').hide();
				$('#zphp-debug-block .data-debug-button').removeClass('opened');

				if(!isOpened && listTarget.children().length > 0)
				{
					$('#debug-data-list-container').show().css({'max-height': $(window).height() - 300});
					listTarget.show();
					$this.addClass('opened');
				}
			}

		});

		$('#debug-data-remember-checkbox').bind('click', function() {
			var checked = $(this).is(':checked');
			$.ajax({
				'url': '!HTMLBlockDebugData(set_remember_debug)',
				'type': 'post',
				'data': {remember: checked ? 1 : 0}
			});
		});

		$('#debug-data-clear-button').bind('click', function() {
			$.ajax({
				'url': '!HTMLBlockDebugData(clear_debug)',
				'type': 'post'
			});
			$('#debug-data-list-container').hide();
			$('#zphp-debug-block .debug-list').hide();
			$('#zphp-debug-block .data-debug-button').removeClass('opened');
			$('#debug-data-list').empty();
			$('#debug-data-sql-button .count').html('0');
		});

		$('#debug-data-list li').each(function(index, item) {

			var $item = $(this);
			var background = (index % 2 == 0) ? '#F2F2F2' : '#FEFEFE';

			$item.css({'background': background});
		});

		$('#zphp-debug-block .debug-request-title a').bind('click', function() {

			var $item = $(this);
			var requestId = $item.getParent().attr('data-request-id');
			var isOpened = $item.hasClass('opened');

			$('#zphp-debug-block .debug-request-item').hide();
			$('#zphp-debug-block .debug-request-title').find('.sign').html('<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-plus fa-stack-1x fa-inverse"></i></span>');
			$('#zphp-debug-block .debug-request-title a').removeClass('opened');

			if(!isOpened)
			{
				$item.scrollView();
				$item.addClass('opened');
				$item.find('.sign').html('<span class="fa-stack"><i class="fa fa-square fa-stack-2x"></i><i class="fa fa-minus fa-stack-1x fa-inverse"></i></span>');
				$('#zphp-debug-block .debug-request-item[data-request-id="'+requestId+'"]').show();
			}

		});

		if($('#zphp-debug-block .debug-request-title a').length == 1)
		{
			$('#zphp-debug-block .debug-request-title a').click();
		}

		loadDebugList(scripts, 'debug-data-js-button', 'debug-scripts-list');
		loadDebugList(styles, 'debug-data-css-button', 'debug-styles-list');
		loadDebugList(images, 'debug-data-img-button', 'debug-images-list');

	});

</script>