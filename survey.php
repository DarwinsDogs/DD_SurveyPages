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
	GROUP BY id ORDER BY id');
$stmt->bindValue(':id', $npage, PDO::PARAM_INT);
$stmt->bindValue(':dog', $dog['id'], PDO::PARAM_INT);
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
		'<div id="button_', $n, '_', $i ,'" class="button', ($a == $i ? ' checked"' : '"'), ' onclick="answer_likert(', $n, ',', $i, ');"></div></div>', PHP_EOL; // TODO answer == 0?
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
	if ($question['format'] == 'Likert')
		likert($question, $n); // TODO other question types: Text, Choices, Numeric, MultiNumeric, MultiChoices
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
	<span id="balls" style="display: none;">
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
	$jsq[] = Array('answer_id' => $q['answer_id'], 'id' => $q['id'], 'answer' => $q['answer'], 'notes' => $q['notes'], 'changed' => false);
?>
<script type="text/javascript">
var questions = <?php echo json_encode($jsq); ?>;
var cur = <?php echo $nextq; ?>;
var started = <?php echo ($nextq > 0 ? 'true' : 'false'); ?>;
var sn = <?php echo $npage - 1; ?>;
var dn = <?php echo $dog['id']; ?>;
var surveys = '<?php echo $dog['surveys']; ?>';
function show_question(n) {
	/* check if we are allowed to see this question yet */
	var i;
	for (i = 0; i < questions.length && !(questions[i].answer === null || questions[i].answer === ''); i++);
	/* if we finished the last question, submit survey, and go to thanks page */
	if (n == questions.length && i == n) {
		surveys = surveys.substr(0, sn) + '3' + surveys.substr(sn + 1);
		params = 'type=survey&id=' + dn + '&surveys=' + surveys;
		post_data(params, function() { started = true; window.location = '?pg=thanks&n=' + (sn + 1); });
	}
	if (n < 0 || n > i) return;
	/* submit current question if needed */
	if (cur >= 0 && cur < questions.length && questions[cur].changed) submit_answer(cur);
	cur = n;
	/* draw qballs, hide all questions and intro */
	document.getElementById('balls').style.display = 'block';
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
	document.getElementById('qball_' + n).className += ' qball_filled';
	// TODO change nav button classes
}
function answer_likert(n, a) {
	if (questions[n].answer === a) return;
	for (i = 0; i < 5; i++)
		document.getElementById('button_' + n + '_' + i).className = 'button';
	document.getElementById('button_' + n + '_' + a).className += ' checked';
	answer_generic(n, a);
}
function submit_answer(n) {
	var params = 'type=answer';
	params += '&id=' + (questions[n].answer_id ? questions[n].answer_id : 0);
	params += '&question=' + questions[n].id;
	params += '&dog=' + <?php echo $dog['id']; ?>;
	params += '&answer=' + encodeURIComponent(questions[n].answer);
	params += '&notes=' + encodeURIComponent(questions[n].notes);
	post_data(params, function() { questions[n].changed = false; });
	if (!started) {
		surveys = surveys.substr(0, sn) + '1' + surveys.substr(sn + 1);
		params = 'type=survey&id=' + dn + '&surveys=' + surveys;
		post_data(params, function() { started = true; });
	}
}
function sub_load() { show_question(cur); }
</script>
