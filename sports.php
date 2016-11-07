<?php if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) die(); ?>
<div class="nav_target"  id="sports">
<div id="intro">
<?php
if ($idpage != '') { foreach ($dogs as $d) { if ($idpage == $d['id']) $dog = $d; } }
else { $dog = $dogs[0]; }

$qlist = Array(4, 5, 6, 11, 12, 13, 16, 21, 22, 23, 24, 31, 32, 34, 42, 44, 46, 48, 49, 51, 52, 53, 54, 71, 72, 73, 74, 75, 77, 78, 79, 81, 82, 83, 85, 87, 88, 91, 101, 102, 104, 106, 107, 108, 109);
$stmt = $db->prepare('SELECT question, answer FROM answers WHERE dog = :id AND question in ( ' . implode(',',$qlist) . ' )');
$stmt->bindValue(':id', $dog['id'], PDO::PARAM_INT);
if (!$stmt->execute()) die('Query error: ' . $stmt->errorInfo());
$ans = $stmt->fetchAll(PDO::FETCH_ASSOC);
$rec = Array(1, 1, 1, 1, 1, 1, 1, 1, 1);
$mean = Array(
	Array(1.0,2.4,2.4,2.3,2.7,1.8,2.8,2.0,1.4,1.9,1.0,2.4,2.5,2.7,1.4,2.9,2.7,3.1,0.7,2.8,1.2,1.3,1.5,2.6,1.8,2.2,2.9,1.3,2.9,2.3,2.2,2.7,2.3,3.4,2.8,2.5,2.9,3.1,1.2,2.4,0.8,0.5,1.5,2.8),
	Array(1.0,2.4,2.4,2.3,2.7,1.8,2.8,2.0,1.4,1.9,1.0,2.4,2.5,2.7,1.4,2.9,2.7,3.1,0.7,2.8,1.2,1.3,1.5,2.6,1.8,2.2,2.9,1.3,2.9,2.3,2.2,2.7,2.3,3.4,2.8,2.5,2.9,3.1,1.2,2.4,0.8,0.5,1.5,2.8),
	Array(1.0,2.4,2.4,2.3,2.7,1.8,2.8,2.0,1.4,1.9,1.0,2.4,2.5,2.7,1.4,2.9,2.7,3.1,0.7,2.8,1.2,1.3,1.5,2.6,1.8,2.2,2.9,1.3,2.9,2.3,2.2,2.7,2.3,3.4,2.8,2.5,2.9,3.1,1.2,2.4,0.8,0.5,1.5,2.8),
	Array(1.0,2.4,2.4,2.3,2.7,1.8,2.8,2.0,1.4,1.9,1.0,2.4,2.5,2.7,1.4,2.9,2.7,3.1,0.7,2.8,1.2,1.3,1.5,2.6,1.8,2.2,2.9,1.3,2.9,2.3,2.2,2.7,2.3,3.4,2.8,2.5,2.9,3.1,1.2,2.4,0.8,0.5,1.5,2.8),
	Array(1.0,2.4,2.4,2.3,2.7,1.8,2.8,2.0,1.4,1.9,1.0,2.4,2.5,2.7,1.4,2.9,2.7,3.1,0.7,2.8,1.2,1.3,1.5,2.6,1.8,2.2,2.9,1.3,2.9,2.3,2.2,2.7,2.3,3.4,2.8,2.5,2.9,3.1,1.2,2.4,0.8,0.5,1.5,2.8),
	Array(1.0,2.4,2.4,2.3,2.7,1.8,2.8,2.0,1.4,1.9,1.0,2.4,2.5,2.7,1.4,2.9,2.7,3.1,0.7,2.8,1.2,1.3,1.5,2.6,1.8,2.2,2.9,1.3,2.9,2.3,2.2,2.7,2.3,3.4,2.8,2.5,2.9,3.1,1.2,2.4,0.8,0.5,1.5,2.8),
	Array(1.0,2.4,2.4,2.3,2.7,1.8,2.8,2.0,1.4,1.9,1.0,2.4,2.5,2.7,1.4,2.9,2.7,3.1,0.7,2.8,1.2,1.3,1.5,2.6,1.8,2.2,2.9,1.3,2.9,2.3,2.2,2.7,2.3,3.4,2.8,2.5,2.9,3.1,1.2,2.4,0.8,0.5,1.5,2.8),
	Array(1.0,2.4,2.4,2.3,2.7,1.8,2.8,2.0,1.4,1.9,1.0,2.4,2.5,2.7,1.4,2.9,2.7,3.1,0.7,2.8,1.2,1.3,1.5,2.6,1.8,2.2,2.9,1.3,2.9,2.3,2.2,2.7,2.3,3.4,2.8,2.5,2.9,3.1,1.2,2.4,0.8,0.5,1.5,2.8),
	Array(1.0,2.4,2.4,2.3,2.7,1.8,2.8,2.0,1.4,1.9,1.0,2.4,2.5,2.7,1.4,2.9,2.7,3.1,0.7,2.8,1.2,1.3,1.5,2.6,1.8,2.2,2.9,1.3,2.9,2.3,2.2,2.7,2.3,3.4,2.8,2.5,2.9,3.1,1.2,2.4,0.8,0.5,1.5,2.8),
);
$weight = Array(
	Array(-1, 1,-1,-1, 0,-1, 0,-1,-1,-1,-1, 1, 1, 1, 1, 1, 1, 1,-1,-1,-1,-1,-1, 1,-1, 0, 1,-1, 1, 1, 1,-1, 1, 0, 0, 1, 0, 0, 1,-1, 1,-1,-1, 0, 1),
	Array(-1,-1, 1,-1,-1, 0, 1, 0, 0, 0, 1, 0, 0, 0, 0, 0,-1,-1, 0, 1,-1,-1, 1,-1, 1, 0, 0, 0, 0, 0,-1, 1, 0, 0,-1,-1, 0,-1,-1, 0,-1,-1,-1,-1,-1),
	Array(-1, 1,-1, 1, 0,-1, 0,-1,-1,-1,-1, 1, 0, 1,-1, 1, 1, 1,-1, 1,-1, 0,-1, 1, 0, 0, 0,-1, 1, 1, 0,-1, 1, 0, 0, 1, 0, 0, 1, 0, 1,-1,-1, 0, 1),
	Array( 0, 1,-1, 0, 1,-1, 0,-1, 0,-1, 0, 1, 0, 1,-1, 0, 1, 1,-1, 0, 0,-1, 0, 0, 0, 0, 0,-1, 0, 0, 0,-1, 1, 0, 0, 1, 0, 0, 0,-1, 1,-1,-1,-1, 1),
	Array( 1,-1,-1, 0, 1,-1, 0,-1,-1,-1,-1, 1, 0, 1,-1, 1, 1, 1,-1,-1,-1,-1, 0, 1, 0, 0, 0,-1, 1, 1,-1,-1, 1, 0, 0,-1, 0, 1, 1,-1, 1,-1, 0, 1, 1),
	Array(-1, 0,-1,-1, 1,-1, 0,-1, 0,-1, 0, 1, 1, 1, 1,-1, 1,-1, 0, 1,-1,-1,-1, 0, 1,-1, 1, 0, 1, 0,-1,-1, 1, 1, 0,-1, 1, 1, 1,-1, 1, 0,-1, 1, 1),
	Array(-1, 1,-1, 0, 0,-1, 1, 1,-1,-1, 1, 1, 1, 1, 0, 0, 0, 0,-1,-1,-1, 1,-1, 1, 1, 0, 0,-1, 1, 0, 0, 0, 1, 0, 1, 1, 1, 0, 1,-1, 1,-1,-1, 1, 1),
	Array(-1, 1,-1, 0, 0,-1, 0,-1,-1,-1,-1, 1, 0, 1,-1, 1, 1, 1,-1,-1,-1, 1,-1, 1, 0, 0, 1, 0, 0, 1, 0,-1, 1, 0, 0, 1, 0, 1, 1,-1, 1,-1,-1,-1, 1),
	Array(-1, 1,-1, 1, 0,-1, 1, 0,-1,-1,-1, 1, 1, 1,-1, 0, 0, 0,-1, 1, 0,-1,-1, 1,-1, 0, 1,-1, 1, 0, 0,-1, 0, 1, 0, 1, 0, 0, 1,-1, 1,-1,-1, 1, 1),
);
foreach ($ans as $a) {
	$qn = array_search($a['question'],$qlist);
	for ($i = 0; $i < 9; $i++)
		$rec[$i] += ($a['answer'] - $mean[$i][$qn]) * $weight[$i][$qn];
}
arsort($rec);
$stmt = $db->prepare('SELECT id, question, answer FROM answers where dog = :id AND question in ( 156, 157, 158, 159, 160, 161, 162, 163, 164 )');
$stmt->bindValue(':id', $dog['id'], PDO::PARAM_INT);
$feedback_id = Array(0, 0, 0, 0, 0, 0, 0, 0, 0);
$feedback_answer = Array(1, 1, 1, 1, 1, 1, 1, 1, 1);
if ($stmt->execute()) {
	$ret = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach ($ret as $a) {
		$feedback_id[$a['question'] - 156] = $a['id'];
		$feedback_answer[$a['question'] - 156] = $a['answer'];
	}
}

$name = Array(
	0 => 'Sports',
	1 => 'Course Sports',
	2 => 'Doggie R&R',
	3 => 'Fitness',
	4 => 'Freestyle',
	5 => 'Herding',
	6 => 'Protection',
	7 => 'Pulling',
	8 => 'Tracking',
	9 => 'Water Activities'
);
$title = Array(
	0 => 'Please see your personalized activity recommendations below for you and ' . $dog['name'] . '. ' .
		'Getting involved in any of these can be good for any dog, but we\'ve highlighted ' .
		'those most well suited for ' . $dog['name'] . ' based on the survey answers you\'ve provided. ' .
		'Please note that the scores for these recommendations (the number of "paws") will change ' .
		'as we gather more data on which to base the recommendations. ' .
		'For now these are quite preliminary - feel free to check out all the categories: ' .
		'click on a category emblem for information on how to get started.',
	1 => 'Have a passion for competition and maybe want to showcase your dog\'s talent? ' .
		'Try these well known sports! Great for all levels: those just looking for something fun to pass the time or those who are in it to win it!',
	2 => 'Looking for a low key day? Just lay back and relax with these fun activities and toys to keep your dog occupied and entertained.',
	3 => 'Have a dog with a lot of energy? If you are looking to burn that off and go for an adventure, why not get out there and try something new!',
	4 => 'Have a sense of rhythm and want to teach your dog some dance moves? Explore these sports and move to the beat while improving teamwork.',
	5 => 'Great way to burn off some energy or looking for a fun new activity? Dogs of any size or age can participate in herding sports.',
	6 => 'These sports are intense and for trainers looking for a physically challenging activity that works on discipline and control. Feel up to the challenge?',
	7 => 'Looking for an simple outlet and maybe test their strength? Challenge them to different pulling activities. All sizes of dogs welcome!',
	8 => 'Does your dog have a knack for sniffing things out? Why not harness this talent and put him/her to the test with these fun exciting games.',
	9 => 'Don’t mind getting a little wet? Acclimate your dog to water and get them started swimming. Then if you want, upgrade to a more competitive sport!'
);
$color = Array(
	0 => '31,143,9',
	1 => '122,56,4',
	2 => '122,102,5',
	3 => '168,157,5',
	4 => '0,99,167',
	5 => '101,142,10',
	6 => '5,122,106',
	7 => '220,122,15',
	8 => '144,24,10',
	9 => '90,58,121'
);
if ($argpage == '') $sn = 0;
else $sn = $argpage;
if ($sn == 0) echo '<h1>', $dog['name'],'\'s Activity Recommendations</h1>', PHP_EOL, $title[$sn], PHP_EOL, '</div>', PHP_EOL;
else echo '<h1>', $name[$sn], '</h1>', PHP_EOL, $title[$sn], PHP_EOL, '</div>', PHP_EOL;
include './sports/sports' . $argpage . '.php';
if ($sn != '0'): ?>
<form id="survey">
<p><b>Have you ever tried <?php echo $name[$sn]; ?> activities with <?php echo $dog['name']; ?>? Help us improve our activity recommendations for other dogs.</b></p>
<p>
Are <i><?php echo $name[$sn]; ?></i> activities good for dogs with temperaments like <?php echo $dog['name']; ?>'s?<br>
If <?php echo $dog['name']; ?> hasn't tried any of these activities please leave "neutral" selected.</p>
</p>
<fieldset id="answers">
<span onclick="sport_feedback(0)" id="answer_0">Not Good</span>
<span onclick="sport_feedback(1)" id="answer_1">Neutral</span>
<span onclick="sport_feedback(2)" id="answer_2">Good</span>
<span onclick="sport_feedback(3)" id="answer_3">Great</span>
</fieldset>
</form>
<script type="text/javascript">
var sn = <?php echo $sn; ?>;
var ids = <?php echo json_encode($feedback_id); ?>;
var answers = <?php echo json_encode($feedback_answer); ?>;
function sport_feedback(n) {
	for (i = 0; i < 4; i++) document.getElementById('answer_' + i).className = '';
	document.getElementById('answer_' + n).className = 'checked';
	var params = 'type=answer';
	params += '&id=' + ids[sn];
	params += '&question=' + (sn + 156);
	params += '&dog=' + <?php echo $dog['id']; ?>;
	params += '&answer=' + n;
	params += '&notes=';
	post_data(params, function() { });
}
function sub_load() {
	document.getElementById('answer_' + answers[sn]).className = 'checked';
}
</script>
<?php endif; ?>
</div>
</div>
