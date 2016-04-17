<? foreach($sizes as $size) { ?>	
	
<div style="padding: 10px">
	
	<div id="tit-<?=$size?>" style="height: 20px; font-size: 11pt; font-weight: bold; border-bottom: solid 1px #DDD; padding: 0 0 5px 5px; margin: 20px 0 10px 0">Size <?=$size?></div>
	
	
<? foreach($names as $name) {
	$name = trim($name);
	echo "<div class='icon-block' onclick='selectIcon(this)' onmouseover='showIcon(this)' onmouseout='showIcon(null)'><span class='icon-social-network size-{$size} {$name}'></span></div>";
} ?> 
	
<? } ?>	
