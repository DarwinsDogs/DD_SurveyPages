<?php
if ($idpage != '') { foreach ($dogs as $d) { if ($idpage == $d['id']) $dog = $d; } } // TODO check that dog exists
else { $dog = Array( "name" => "" ); }

if ($dog['sex'] == 'male') { $pnoun = 'he'; $ppnoun = 'his'; }
else if ($dog['sex'] == 'female') { $pnoun = 'she'; $ppnoun = 'her'; }
else { $pnoun = 'he/she'; $ppnoun = 'his/her'; }
/* get survey info */
$stmt = $db->prepare('SELECT * FROM surveys WHERE id = :id');
$stmt->bindValue(':id', $npage, PDO::PARAM_INT);
if (!$stmt->execute()) die('Query error: ' . $stmt->errorInfo());
$survey = $stmt->fetch(PDO::FETCH_ASSOC);
/* get questons / answers */
$stmt = $db->prepare('
	SELECT questions.id AS id, questions.string AS question, questions.position AS position, questions.image AS image,
			formats.style AS format, formats.options AS options, NULL AS answer_id, NULL AS answer, NULL AS notes
	FROM questions, formats
	WHERE questions.survey = :id AND formats.id = questions.format AND questions.id NOT IN
		( SELECT answers.question FROM answers WHERE answers.dog = :dog )
	UNION
	SELECT questions.id AS id, questions.string AS question, questions.position AS position, questions.image AS image,
			formats.style AS format, formats.options AS options, answers.id AS answer_id, answers.answer AS answer, answers.notes AS notes
	FROM questions, formats, answers
	WHERE questions.survey = :id AND formats.id = questions.format AND
			answers.dog = :dog AND answers.question = questions.id
	GROUP BY id');
$stmt->bindValue(':id', $npage, PDO::PARAM_INT);
$stmt->bindValue(':dog', $idpage, PDO::PARAM_INT);
if (!$stmt->execute()) die('Query error: ' . $stmt->errorInfo());

/* get next question number, and decremement if first for title page */
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
$nextq = 0;
foreach ($questions as $question) {
	if ($question['answer'] == '') break;
	$nextq++;
}
if ($nextq == 0) $nextq--;

function likert($question, $n) {
	echo "\t", '<fieldset id="answer">', PHP_EOL;
	$opts = explode('|', $question['options']);
	$w = 42 / count($opts);
	$a = ($question['answer'] != '' ? $question['answer'] : -1);
	for ($i = 0; $i < count($opts); $i++) echo
		"\t\t", '<div class="answer_likert" style="width: ', $w, 'em;"><div id="text">', $opts[$i], '</div>',
		'<div id="button"', ($a == $i ? ' class="checked"' : ''), ' onclick="answer_likert(', $n, ',', $i, ');"></div></div>', PHP_EOL; // TODO answer == 0?
	echo "\t", '</fieldset>', PHP_EOL;
}

function show_question($question, $n, $dog) {
	global $respath;
	$qstr = str_replace('DOG', $dog['name'], $question['question']);
	$qstr = str_replace('HE', $pnoun, $qstr); $qstr = str_replace('HIS', $ppnoun, $qstr);
	echo '<!-- ', $question['id'], '. ', $question['question'], ' -->', PHP_EOL,
		'<form class="question" id="question_', $n, '" style="display: none;">', PHP_EOL,
		"\t", '<fieldset id="photo_question"><div id="dog_photo" style="background-image: url(http://darwinsdogs.org/', $respath, 'dogs/', $dog['image'], '.png);"></div>',
		'<span id="question">', $qstr, '</span></fieldset>', PHP_EOL,
		"\t", '<fieldset id="example"><div id="example_image"></div></fieldset>', PHP_EOL;
	likert($question, $n); // TODO other question types
	echo "\t", '<fieldset id="controls">',
		'<div class="comment">Add a comment<br/><span style="display: ', (strlen($question['notes']) > 1 ? 'inline' : 'none'), ';">comment saved</span></div>',
		'<div class="finish" style="display: none;">SUBMIT</div>',
		'<div class="next" onclick="show_question(', $n + 1, ');"></div>',
		'<div class="back" onclick="show_question(', $n - 1, ');"></div>',
		'</fieldset>', PHP_EOL, '</form>', PHP_EOL;
}
?>

<div id="survey_page" class="nav_target">
<fieldset id="question_banner" style="background: rgba(<?php echo $survey['color']; ?>,1);">
	<span id="title"><?php echo $survey['title']; ?></span>
	<span id="balls">
<?php for ($i = 0; $i < count($questions); $i++): ?>
		<div class="qball" id="qball_<?php echo $i; ?>" onclick="show_question(<?php echo $i; ?>);"></div>
<?php endfor; ?>
	</span>
</fieldset>
<!-- Intro -->
<form class="question" id="survey_<?php echo $survey['id']; ?>">
	<fieldset id="photo_question"><div id="dog_photo" style="background-image: url(http://darwinsdogs.org/<?php echo $respath, 'dogs/', $dog['image']; ?>.png);"></div></fieldset>
	<fieldset id="answer"><div class="intro"><?php echo $survey['intro']; ?></div></fieldset>
	<fieldset id="controls"><div id="start" onclick="show_question(0);"></div></fieldset>
</form>
<?php $n = 0; foreach ($questions as $question) {
	show_question($question, $n, $dog);
	$n++;
} ?>
</div>

<?php
$jsq = Array();
foreach ($questions as $q)
	$jsq[] = Array('id' => $q['id'], 'answer' => $q['answer'], 'notes' => $q['notes'], 'changed' => false);
?>
<script type="text/javascript">
var questions = <?php echo json_encode($jsq); ?>;
var cur = <?php echo $nextq; ?>;
function show_question(n) {
	/* check if we are allowed to see this question yet */
	var i;
	for (i = 0; i < questions.length && !(questions[i].answer === null || questions[i].answer === ''); i++);
	if (n < 0 || n > i) return;
	/* submit current question if needed */
	if (questions[cur].changed) submit_question(cur);
	/* draw qballs, hide all questions and intro */
	document.getElementById('survey_<?php echo $survey['id']; ?>').style.display = 'none';
	for (i = 0; i < questions.length; i++) {
		var q = document.getElementById('qball_' + i);
		q.className = 'qball';
		if (i == n) q.className += ' qball_selected';
		if (!(questions[i].answer === null || questions[i].answer === '')) q.className += ' qball_filled';
		document.getElementById('question_' + i).style.display = 'none';
	}
	/* show only selected question */
	document.getElementById('question_' + n).style.display = 'block';
}
function answer_generic(n, a) {
	questions[n].answer = a;
	questions[n].changed = true;
	// TODO change nav button classes
}
function answer_likert(n, a) {
	// TODO check if changed and call answer_generic(n, a)
}
function submit_question(n) {
	var url = 'http://darwinsdogs.org/~jmcclure/draft/submit.php?type=question';
	url += '&id=' + (questions[n].question_id ? questions[n].question_id : 0);
	url += '&dog=' + questions[n].dog;
	url += '&qn=' + questions[n].id;
	url += '&ans=' + encodeURIComponent(questions[n].answer);
	url += '&notes=' + encodeURIComponent(questions[n].notes);
	push = new XMLHttpRequest();
	push.open("GET", url, true);
	push.onreadystatechange = function () {
		var ret = JSON.parse(this.responseText);
		if (ret.success) questions[n].changed = false;
		else alert(ret.msg); // TODO move to conditional debugging
	}
	push.send();
}
window.onload = show_question(cur);
</script>
