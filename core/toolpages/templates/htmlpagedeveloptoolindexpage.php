<ul style='margin: 20px 0 0 20px;'>
	<?php foreach($urls as $url): ?>

		<li style='padding: 5px 0;list-style-type: disc;'>
			<a style="font-weight: bold;" href='<?=$url['url_pattern']->format_url()?>'><?=$url['title']?></a>
		</li>

	<?php endforeach; ?>
</ul>