
<div id="zphp-debug-block" style="width: 100%; margin: 0 0 30px 0;">

	<div id="zphp-debug-block" style="width: 100%; border: solid 1px #000; position: absolute; margin: 0 0 30px 0; top: 0; left: 0; z-index: 9999">

		<div style="padding: 5px 10px;background: #FFF; ">
			<a style="display: block; text-decoration: underline; font-weight: bold;" onclick="$('#debug-data-list').slideToggle()">Debug Data (<?=count($debug_data)?>) +</a>
		</div>

		<div style="max-height: 100%; overflow: auto; margin: 0 20px;">
			<ul id="debug-data-list" style="display:none; margin: 0;">
				<?php foreach ($debug_data as $debug_item): ?>
					<li style="border-top: solid 1px #888; background: #F2F2F2;">
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

