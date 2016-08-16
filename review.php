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

function review_likert($q) {
	global $db, $user, $colors;
	$stmt = $db->prepare('SELECT answer, COUNT(answer) AS count, GROUP_CONCAT( CASE WHEN dog IN ( SELECT id FROM dogs WHERE owner = :uid )
		THEN ( SELECT name FROM dogs WHERE id = dog ) ELSE NULL END SEPARATOR ", " ) AS dogs FROM answers WHERE question = :qn GROUP BY answer ORDER BY answer');
	$stmt->bindValue(':uid', $user['id'], PDO::PARAM_INT);
	$stmt->bindValue(':qn', $q['id'], PDO::PARAM_INT);
	if (!$stmt->execute()) die('Query error: ' . $stmt->errorInfo());
	$review = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$sum = 0;
	foreach ($review as $r) $sum += $r['count'];
	$opts = explode('|',$q['options']);
	for ($i = 0; $i < count($review); $i++) {
		$r = $review[$i];
		$w = 100 * $r['count'] / $sum;
		$class = ($i == '0' ? ' first' : ($i == count($review) - 1 ? ' last' : '' ));
		echo '<div class="likert" style="width:', $w, '%;">',
			'<div class="label">', $opts[$r['answer']], ' (', $r['count'], ')</div>',
			'<div class="bar', $class, '" style="background: rgba(', $colors[4][$r['answer']], ',1);">&nbsp;</div>',
			'<div class="dogs">', ($r['dogs'] ? $r['dogs'] : '&nbsp;'), '</div></div>';
	}
}

echo '<h3>Review for "', $title, '"</h3>', PHP_EOL,
	'<b>See how your ', (count($dogs) > 1 ? 'dogs compare' : 'dog compares'), ' to other dogs in the project:</b>', PHP_EOL;
foreach ($review as $q) {
	// TODO DOG -> your dog; HE -> he/she; HIS -> his/her
	echo '<!-- Question ', $q['id'], ' -->', PHP_EOL,
		'<div class="review_block"><p>', $q['string'], '</p>';
	switch ($q['format']) {
		case 'Likert': review_likert($q); break;
		default: echo '<p class="no_such">No review available for this format yet</p>', PHP_EOL;
	}
	echo '</div>', PHP_EOL;
}
?>
</div>
