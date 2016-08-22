<?php
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) die();
if ($idpage != '') { foreach ($dogs as $d) { if ($idpage == $d['id']) $dog = $d; } }
else { header('Location: ' . $dd_root); }

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
	$opts = explode('|', $question['options']);
	$w = 100 / count($opts);
	$a = ($question['answer'] != '' ? $question['answer'] : -1);
	for ($i = 0; $i < count($opts); $i++) echo
		"\t\t", '<div class="answer_likert" style="width: ', $w, '%;"><div class="text">', $opts[$i], '</div>',
		'<div id="button_', $n, '_', $i ,'" class="button', ($a == $i ? ' checked"' : '"'), ' onclick="answer_likert(', $n, ',', $i, ');"></div></div>', PHP_EOL;
}

function choices($question, $n, $multi) {
	$opts = explode('|', $question['options']);
	if ($multi) $opts[] = 'none';
	$a = explode('|', $question['answer']);
	$ai = 0;
	for ($i = 0; $i < count($opts); $i++) {
		$checked = '';
		if (isset($a[$ai]) && $a[$ai] === (string) $i) { $checked = ' checked'; $ai++; }
		echo "\t\t", '<div class="answer_choice"><div class="check', $checked, '" id="check_', $n, '_', $i,
		'" onclick="answer_choices(', $n, ',', $i, ',', ($multi ? 'true' : 'false'), ');"></div><span>', $opts[$i], '</span></div>', PHP_EOL;
	}
}

function multi($question, $n, $text) {
	$opts = explode('|', $question['options']);
	$a = explode('|', $question['answer']);
	for ($i = 0; $i < count($opts); $i++) echo
		"\t\t", '<div class="answer_multi"><input type="', ($text ? 'text' : 'number'), '" class="tinput" id="tinput_', $n, '_', $i,
		'" onchange="answer_multi(', $n, ',', ($text ? 'true': 'false'), ');" ', 'value="', $a[$i], '"/><span>', $opts[$i], '</span></div>', PHP_EOL; // TODO $a[$i] may not be defined
}

function text_numeric($question, $n, $text) {
	if ($text) echo '<div class="answer_text"><textarea id="tinput_', $n, '_0" onfocus="answer_empty(', $n, ');" onchange="answer_multi(', $n, ',true);">', $question['answer'], '</textarea>', PHP_EOL;
	else echo '<div class="answer_text"><input type="number" id="tinput_', $n, '_0" value="', $question['answer'], '" onchange="answer_multi(', $n, ',false);"/>', PHP_EOL;
}

function show_question($question, $n, $count) {
	global $dog, $pnoun, $ppnoun, $dd_root;
	$qstr = str_replace('DOG', $dog['name'], $question['question']);
	$qstr = str_replace('HE', $pnoun, $qstr); $qstr = str_replace('HIS', $ppnoun, $qstr);
	echo '<!-- ', $question['id'], '. ', $question['question'], ' -->', PHP_EOL,
		'<form class="question" id="question_', $n, '" style="display: none;">', PHP_EOL,
		"\t", '<fieldset class="qstring"><div class="photo" style="background-image: url(', $dd_root, 'res/dogs/', $dog['image'], '.png);"></div>',
		'<span class="text">', $qstr, '</span></fieldset>', PHP_EOL;
	if ($question['image'] == $question['id']) echo
		"\t", '<fieldset class="example"><img class="image" src="', $dd_root, 'res/examples/', $question['id'], '.png"></fieldset>', PHP_EOL,
		"\t", '<fieldset class="answer">', PHP_EOL;
	else echo
		"\t", '<fieldset class="answer">', PHP_EOL;
	switch ($question['format']) {
		case 'Likert': likert($question, $n); break;
		case 'Text': text_numeric($question, $n, true); break;
		case 'Numeric': text_numeric($question, $n, false); break;
		case 'MultiNumeric': multi($question, $n, false); break;
		case 'Choices': choices($question, $n, false); break;
		case 'MultiChoices': choices($question, $n, true); break; // TODO needs testing
		// case 'MutliText': multi($question, $n, true); break; // MultiText isn't used yet
		default:
	}
	echo "\t", '</fieldset>', PHP_EOL,
		"\t", '<fieldset class="controls">',
		"\t\t", '<div class="comment_button" id="comment_button_', $n, '" onclick="toggle_comment(', $n, ');">Add a comment<br/>',
			'<span id="comment_label_', $n, '" style="display: ', (strlen($question['notes']) > 1 ? 'inline' : 'none'), ';">comment saved</span></div>', PHP_EOL,
		"\t\t", ($n < $count - 1 ?
			'<div class="next" id="next_' . $n . '" onclick="show_question(' . ($n + 1) . ');"></div>' :
			'<div class="finish" style="display: none;">SUBMIT</div>' ),
			($n > 0 ? '<div class="back" onclick="show_question(' . ($n - 1) . ');"></div>' : '' ), PHP_EOL,
		"\t", '</fieldset>', PHP_EOL,
		"\t", '<div class="comment hidden" id="comment_', $n, '"><textarea placeholder="Enter comments here" id="comment_text_', $n, '" onchange="save_comment(', $n, ');">',
			$question['notes'], '</textarea></div>', PHP_EOL,
		'</form>', PHP_EOL;
}
?>

<div id="survey" class="nav_target">
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
	<fieldset class="qstring"><div class="photo" style="background-image: url(<?php echo $dd_root, 'res/dogs/', $dog['image']; ?>.png);"></div></fieldset>
	<fieldset class="answer"><div class="intro"><?php echo $survey['intro']; ?></div></fieldset>
	<fieldset class="controls"><div class="start" onclick="show_question(0);"></div></fieldset>
</form>
<?php for ($n = 0; $n < count($questions); $n++) show_question($questions[$n], $n, count($questions)); ?>
</div>

<?php
$jsq = Array();
foreach ($questions as $q)
	$jsq[] = Array('answer_id' => $q['answer_id'], 'id' => $q['id'], 'answer' => $q['answer'], 'notes' => $q['notes'], 'changed' => false, 'nopts' => count(explode('|',$q['options'])));
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
		post_data(params, function() { window.location = '?pg=thanks&n=' + (sn + 1); });
		return;
	}
	if (n < 0 || n > i) return;
	if (i > n) document.getElementById('next_' + n).style.opacity = 1.0;
	else document.getElementById('next_' + n).style.opacity = 0.65;
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
	update_height();
}
function answer_generic(n, a) {
	if (questions[n].answer === a) return;
	document.getElementById('next_' + n).style.opacity = 1.0;
	questions[n].answer = a;
	questions[n].changed = true;
	document.getElementById('qball_' + n).className += ' qball_filled';
}
function answer_likert(n, a) {
	for (var i = 0; i < questions[n].nopts; i++)
		document.getElementById('button_' + n + '_' + i).className = 'button';
	document.getElementById('button_' + n + '_' + a).className += ' checked';
	answer_generic(n, a);
}
function answer_choices(n, a, multi) {
	if (multi) {
		var str = '';
		if (a < questions[n].nopts) {
			document.getElementById('check_' + n + '_' + a).classList.toggle('checked');
			document.getElementById('check_' + n + '_' + questions[n].nopts).className = 'check';
			for (var i = 0; i < questions[n].nopts; i++) {
				if (!document.getElementById('check_' + n + '_' + i).classList.contains('checked')) continue;
				if (str.length < 1) str += i;
				else str += '|' + i;
			}
		}
		else {
			str = questions[n].nopts;
			for (var i = 0; i < questions[n].nopts; i++)
				document.getElementById('check_' + n + '_' + i).className = 'check';
			document.getElementById('check_' + n + '_' + questions[n].nopts).className = 'check checked';
		}
		answer_generic(n, str);
	}
	else {
		for (var i = 0; i < questions[n].nopts; i++)
			document.getElementById('check_' + n + '_' + i).className = 'check';
		document.getElementById('check_' + n + '_' + a).className += ' checked';
		answer_generic(n, a);
	}
}
function answer_empty(n) {
	answer_generic(n, ' ');
}
function answer_multi(n, text) {
	var str = toNumber(document.getElementById('tinput_' + n + '_0').value, text);
	for (var i = 1; i < questions[n].nopts; i++)
		str += '|' + toNumber(document.getElementById('tinput_' + n + '_' + i).value, text);
	if (questions[n].answer === str) return;
	answer_generic(n, str);
}
function toNumber(val, text) {
	if (text) return val;
	else return (isNaN(val) ? '' : Number(val));
}
function submit_answer(n) {
	var params = 'type=answer';
	params += '&id=' + (questions[n].answer_id ? questions[n].answer_id : 0);
	params += '&question=' + questions[n].id;
	params += '&dog=' + <?php echo $dog['id']; ?>;
	params += '&answer=' + encodeURIComponent(questions[n].answer);
	params += '&notes=' + (questions[n].notes ? encodeURIComponent(questions[n].notes) : '');
	post_data(params, function() { questions[n].changed = false; });
	if (!started) {
		surveys = surveys.substr(0, sn) + '1' + surveys.substr(sn + 1);
		params = 'type=survey&id=' + dn + '&surveys=' + surveys;
		post_data(params, function() { started = true; });
	}
}
function toggle_comment(n) {
	document.getElementById('comment_' + n).classList.toggle('hidden');
	update_height();
}
function save_comment(n) {
	questions[n].notes = document.getElementById('comment_text_' + n).value;
	document.getElementById('comment_label_' + n).style.display = 'inline';
	questions[n].changed = true;
}
function sub_load() { show_question(cur); }
</script>
