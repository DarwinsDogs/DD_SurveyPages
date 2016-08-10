<?php
foreach(str_split($dog['sports']) as $s) {
	echo '<a title="', $title[$s], '" href="?pg=sports&n=', $s, '">',
		'<div class="sport"><div class="img" style="background-image: url(', $dd_root, '/res/banner/sports', $s, '.jpg);"></div>',
		'<div class="name" style="background: rgba(', $color[$s], ',1);">', $name[$s], '</div></div></a>', PHP_EOL;
}
?>


