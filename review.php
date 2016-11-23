<?php if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) die(); ?>
<div id="review" class="nav_target">
<?php
$colors = Array(
	Array( '216,179,101' ),
	Array( '90,180,172', '216,179,101' ),
	Array( '90,180,172', '220,220,220', '216,179,101' ),
	Array( '1,133,113', '128,205,193', '223,194,125', '166,97,26' ),
	Array( '1,133,113', '128,205,193', '220,220,220', '223,194,125', '166,97,26' ),
	Array( '1,102,94', '90,180,172', '199,234,229', '246,232,195', '216,179,101', '140,81,10' ),
	Array( '1,102,94', '90,180,172', '199,234,229', '220,220,220', '246,232,195', '216,179,101', '140,81,10' ),
	Array( '1,102,94', '53,151,143', '128,205,193', '199,234,229', '246,232,195', '223,194,125', '191,129,45', '140,81,10' ),
	Array( '1,102,94', '53,151,143', '128,205,193', '199,234,229', '220,220,220', '246,232,195', '223,194,125', '191,129,45', '140,81,10' )
);

/* get overview */
$stmt = $db->prepare('SELECT title FROM surveys WHERE id = :sn');
$stmt->bindValue(':sn', $npage, PDO::PARAM_INT);
if (!$stmt->execute()) die('Query error: ' . $stmt->errorInfo());
$review = $stmt->fetch(PDO::FETCH_ASSOC);
$title = $review['title'];

$stmt = $db->prepare('SELECT questions.id AS id, string, style AS format, options, image FROM questions, formats
	WHERE survey = :sn AND format = formats.id ORDER BY isnull(position), position, questions.id');
$stmt->bindValue(':sn', $npage, PDO::PARAM_INT);
if (!$stmt->execute()) die('Query error: ' . $stmt->errorInfo());
$review = $stmt->fetchAll(PDO::FETCH_ASSOC);

function pronouns($str) {
	$str = str_replace('DOG', 'your dog', $str);
	$str = str_replace('HIS', 'his/her', $str);
	$str = str_replace('HE', 'he/she', $str);
	return $str;
}

function review_likert($q) {
	global $db, $user, $colors;
	$stmt = $db->prepare('SELECT answer, COUNT(answer) AS count, GROUP_CONCAT( CASE WHEN dog IN ( SELECT id FROM dogs WHERE owner = :uid )
		THEN ( SELECT name FROM dogs WHERE id = dog ) ELSE NULL END SEPARATOR ", " ) AS dogs FROM answers WHERE question = :qn AND answer != "" GROUP BY answer ORDER BY answer');
	$stmt->bindValue(':uid', $user['id'], PDO::PARAM_INT);
	$stmt->bindValue(':qn', $q['id'], PDO::PARAM_INT);
	if (!$stmt->execute()) die('Query error: ' . $stmt->errorInfo());
	$review = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$sum = 0;
	foreach ($review as $r) $sum += $r['count'];
	$opts = explode('|', pronouns($q['options']));
	for ($i = 0; $i < count($review); $i++) {
		$r = $review[$i];
		$w = 100 * $r['count'] / $sum;
		$class = ($i == '0' ? ' first' : ($i == count($review) - 1 ? ' last' : '' ));
		$out = $opts[$r['answer']];
		echo '<div class="likert" style="width:', $w, '%;">',
			'<div class="label">', ($w > 10 ? $out : ''), '</div>',
			'<div class="bar', $class, '" style="background: rgba(', $colors[4][$r['answer']], ',1);" title="', $out, ' (', $r['count'], ')">', ($w > 3 ? $r['count'] : ''), '</div>',
			'<div class="dogs">', ($r['dogs'] ? $r['dogs'] : '&nbsp;'), '</div></div>';
	}
}

/*
function review_choices($q) {
	global $db, $user, $colors, $dogs;
	$stmt = $db->prepare('SELECT answer, COUNT(answer) AS count, GROUP_CONCAT( CASE WHEN dog IN ( SELECT id FROM dogs WHERE owner = :uid )
		THEN ( SELECT name FROM dogs WHERE id = dog ) ELSE NULL END SEPARATOR ", " ) AS dogs FROM answers WHERE question = :qn AND answer != "" GROUP BY answer ORDER BY answer');
	$stmt->bindValue(':uid', $user['id'], PDO::PARAM_INT);
	$stmt->bindValue(':qn', $q['id'], PDO::PARAM_INT);
	if (!$stmt->execute()) die('Query error: ' . $stmt->errorInfo());
	$review = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$sum = 0;
	foreach ($review as $r) $sum += $r['count'];
	$opts = explode('|', pronouns($q['options']));
	for ($i = 0; $i < count($review); $i++) {
		$r = $review[$i];
		$w = 100 * $r['count'] / $sum;
		echo '<div style="width: 100%; position: relative; margin-bottom: 0.25rem;">&nbsp;', $opts[$r['answer']], ' (', $r['count'], ')',
			'<div style="border: 1px solid black; position: absolute; top: 0px; left: 0px; width: ', $w, '%; background: rgba(', $colors[count($opts)][$r['answer']], ',1); color: white; overflow: hidden; white-space: nowrap;">&nbsp;',
			$opts[$r['answer']], ' (', $r['count'], ')</div></div>';
	}
}
*/

function prefix($attr, $val) { foreach (array('-moz-','-ms-','-o-','-webkit-') as $pre) echo $pre, $attr, ': ', $val, '; '; echo $attr, ': ', $val; }
function review_choices($q) {
	global $db, $user, $colors, $dogs;
	$stmt = $db->prepare('SELECT answer, COUNT(answer) AS count, GROUP_CONCAT( CASE WHEN dog IN ( SELECT id FROM dogs WHERE owner = :uid )
		THEN ( SELECT name FROM dogs WHERE id = dog ) ELSE NULL END SEPARATOR ", " ) AS dogs FROM answers WHERE question = :qn AND answer != "" GROUP BY answer ORDER BY answer');
	$stmt->bindValue(':uid', $user['id'], PDO::PARAM_INT);
	$stmt->bindValue(':qn', $q['id'], PDO::PARAM_INT);
	if (!$stmt->execute()) die('Query error: ' . $stmt->errorInfo());
	$review = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$sum = 0;

	$vals = Array();
	foreach ($review as $r) $vals[] = $r['count'];
	$opts = explode('|', pronouns($q['options']));

	$n = count($vals); $degrees[0] = 0;
	$col = $colors[$n];
	for ($i = 1; $i < $n; $i++) $vals[$i] += $vals[$i-1];
	for ($i = 1; $i < $n; $i++) $degrees[$i] = $vals[$i-1] * 360 / $vals[$n-1];
	for ($n = 0; $n < count($degrees) && $degrees[$n] < 180; $n++);
	echo '<div class="pie_half pie_left" style="background: rgba(', $col[$n-1], ',1);">', PHP_EOL;
	for ($n = 0; $n < count($degrees); $n++)
		echo "\t", '<div class="pie_slice" style="background: rgba(', $col[$n], ',1); ', prefix('transform', 'rotate(' . $degrees[$n] . 'deg)'), '"></div>', PHP_EOL;
	echo '</div><div class="pie_half pie_right">', PHP_EOL;
	for ($n = 0; $n < count($degrees) && $degrees[$n] < 180; $n++)
		echo "\t", '<div class="pie_slice" style="background: rgba(', $col[$n], ',1); ', prefix('transform', 'rotate(' . $degrees[$n] . 'deg)'), '"></div>', PHP_EOL;
	echo '</div>', PHP_EOL, '<div class="pie_labels">', PHP_EOL;
	for ($n = 0; $n < count($opts) ; $n++)
		echo "\t", '<div class="pie_label"><div style="background: rgba(', $col[$n], ',1);"></div><span>', ucfirst($opts[$n]), '</span><span class="num">(', $vals[$n], 
			($review[$n]['dogs'] ? ' including ' . $review[$n]['dogs'] : '' ), ')</span></div>', PHP_EOL;
	echo '&nbsp;</div>', PHP_EOL;

}

function review_multinumeric($q) {
	global $db, $user, $colors, $dogs;
	$opts = explode('|', preg_replace('/ ?\([^)]*\)/', '', $q['options']));
	$qstr = 'SELECT AVG(SUBSTRING_INDEX(SUBSTRING_INDEX(answer,"|",1),"|",-1)) AS opt0';
	for ($i = 1; $i < count($opts); $i++)
		$qstr .= ', AVG(SUBSTRING_INDEX(SUBSTRING_INDEX(answer,"|",' . ($i + 1) . '),"|",-1)) AS opt' . $i;
	$qstr .= ' FROM answers WHERE question = :qn';
	$stmt = $db->prepare($qstr);
	$stmt->bindValue(':qn', $q['id'], PDO::PARAM_INT);
	if (!$stmt->execute()) die('Query error: ' . print_r($stmt->errorInfo(),true));
	$means = $stmt->fetch(PDO::FETCH_ASSOC);
	$stmt = $db->prepare('SELECT name, answer FROM answers, dogs WHERE dog = dogs.id AND question = :qn AND owner = :uid AND NOT dogs.flags & 1');
	$stmt->bindValue(':qn', $q['id'], PDO::PARAM_INT);
	$stmt->bindValue(':uid', $user['id'], PDO::PARAM_INT);
	if (!$stmt->execute()) die('Query error: ' . print_r($stmt->errorInfo(),true));
	$review = $stmt->fetchAll(PDO::FETCH_ASSOC);
	echo '<table class="numeric"><tbody>', PHP_EOL, "\t", '<tr><th>Dog</th>';
	for ($i = 0; $i < count($opts); $i++)
		echo '<th title="', $opts[$i], '">', ($i+1), '</th>';
	echo '</tr>', PHP_EOL;
	foreach ($review as $r) {
		$ans = explode('|', $r['answer']);
		echo "\t", '<tr><td>', $r['name'], '</td>';
		foreach ($ans as $a) echo '<td>', $a, '</td>';
		echo '</tr>', PHP_EOL;
	}
	echo "\t", '<tr><td>Darwin\'s Dogs Average</td>';
		foreach ($means as $mean) echo '<td>', round($mean, 2), '</td>';
	echo '</tr>', PHP_EOL;
	echo '</tbody></table>', PHP_EOL;
	echo '<table class="footnotes"><tbody>', PHP_EOL,
		"\t", '<tr><th>Category</th><th>Description</th></tr>', PHP_EOL;
	for ($i = 0; $i < count($opts); $i++)
		echo '<tr><td>', ($i + 1), '</td><td>', $opts[$i], '</td></tr>', PHP_EOL;
	echo '</tbody></table>', PHP_EOL;
}




echo '<h3>Review for "', $title, '"</h3>', PHP_EOL,
	'<p style="color: red">The review pages here are not yet complete and in many
	cases will not display properly.  Please bear with us through the
	upgrades.</p>',
	'<b>See how your ', (count($dogs) > 2 ? 'dogs compare' : 'dog compares'), ' to other dogs in the project:</b>', PHP_EOL;
foreach ($review as $q) {
	echo '<!-- Question ', $q['id'], ' -->', PHP_EOL,
		'<div class="review_block"><p class="qstring">', ucfirst(pronouns($q['string'])), '</p>', PHP_EOL;
	switch ($q['format']) {
		case 'Likert': review_likert($q); break;
		case 'MultiNumeric': review_multinumeric($q); break;
		case 'Choices': review_choices($q); break;
		default: echo '<p class="no_such" title="',$q['format'],'">No review available for this format yet</p>', PHP_EOL;
	}
	echo '</div>', PHP_EOL;
}
?>
</div>
