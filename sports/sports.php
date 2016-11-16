<?php
$mult = (max($rec) >= 5 ? 100 : max($rec) * 20) / max($rec);
foreach($rec as $s => $val) {
	if ($val < 0) $val = 0;
	else $val = round($val * $mult);
	$stars = '<div class="paws"><div class="pawback"></div><div class="pawfront" style="width: ' . $val . '%;" ></div></div>';
	$s++;
	echo '<a title="', $title[$s], '" href="?pg=sports&arg=', $s, '&id=', $dog['id'], '">',
		'<div class="sport"><div class="img" style="background-image: url(', $dd_surveys, '/res/ui/sports', $s, '.jpg);"></div>',
		'<div class="name" style="background: rgba(', $color[$s], ',1);">', $name[$s], $stars, '</div></div></a>', PHP_EOL;
}
?>


