<?	foreach($names as $name) {
	$name = trim($name);
	echo "<div class='icon-block' onclick='selectIcon(this)' onmouseover='showIcon(this)' onmouseout='showIcon(null)'><span class='icon-silk {$name}'></span></div>";
} ?> 