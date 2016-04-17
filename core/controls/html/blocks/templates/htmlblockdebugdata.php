
<div id="zphp-debug-block" style="width: 100%; margin: 0 0 30px 0; font-family: sans;">

	<div id="zphp-debug-block" style="width: 100%; border: solid 1px #000; position: absolute; margin: 0 0 30px 0; top: 0; left: 0; z-index: 9999">

		<div style="padding: 5px 10px;background: #333; ">

			<div style="color: #1285C5; float: left; font-weight: bold;">
				Zframework Debug
				&nbsp;&nbsp;
			</div>

			<a style="color: #FFF; display: block; text-decoration: underline; font-weight: bold; float:left" onclick="$('#debug-data-list-container').css({'max-height': $(window).height() - 100}); $('#debug-data-list').slideToggle()">Debug Data (<?=count($debug_data)?>) +</a>

			<?php if($content_len) { ?>
				<div style="float: right; color: #FFF;">
					<label>Size: </label>
					<?=NumbersHelper::number_format_size($content_len)?>
				</div>
			<?php } ?>

			<div style="clear:both"></div>

		</div>

		<div style="padding: 0 30px; position: absolute; width: 100%">
			<div style="overflow: auto; margin: 0 0px; box-shadow: 7px 7px 9px rgba(0,0,0,0.8); border: solid 1px #777; " id="debug-data-list-container">
				<ul id="debug-data-list" style="display:none; margin: 0;">
					<?php foreach ($debug_data as $index => $debug_item): ?>
						<li style="border-top: solid 1px #888; background: <?($index % 2 == 0 ? '#F2F2F2' : '#FEFEFE')?>;">
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
			</div>
		</div>

	</div>

</div>

