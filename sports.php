<div class="nav_target"  id="sports">
<div id="intro">
<?php
if ($idpage != '') { foreach ($dogs as $d) { if ($idpage == $d['id']) $dog = $d; } }
else { $dog = $dogs[0]; }
if (!isset($dog['sports'])) $dog['sports'] = '123456789';
if (!isset($dog['sports_answers'])) $dog['sports_answers'] = '111111111';
$name = Array(
	'0' => 'Sports',
	'1' => 'Course Sports',
	'2' => 'Doggie R&R',
	'3' => 'Fitness',
	'4' => 'Freestyle',
	'5' => 'Herding',
	'6' => 'Protection',
	'7' => 'Pulling',
	'8' => 'Tracking',
	'9' => 'Water Activities'
);
$title = Array(
	'0' => 'Intro needed here',
	'1' => 'Have a passion for competition and maybe want to showcase your talent? ' .
		'Try these well known sports! Great for all levels: those just looking for something fun to pass the time or those who are in it to win it!',
	'2' => 'Looking for a low key day? Just lay back and relax with these fun activities and toys to keep your dog occupied and entertained.',
	'3' => 'Have a dog with a lot of energy? If you are looking to burn that off and go for an adventure, why not get out there and try something new!',
	'4' => 'Have a sense of rhythm and want to teach your dog some dance moves? Explore these sports and move to the beat while improving teamwork.',
	'5' => 'Great way to burn off some energy or looking for a fun new activity? Dogs of any size or age can participate in herding sports.',
	'6' => 'These sports are intense and for serious trainers looking for a physically challenging activity that works on discipline and control. Feel up to the challenge?',
	'7' => 'Looking for an simple outlet and maybe test their strength? Challenge them to different pulling activities. All sizes of dogs welcome!',
	'8' => 'Does your dog have a knack for sniffing things out? Why not harness this talent and put him/her to the test with these fun exciting games.',
	'9' => 'Don’t mind getting a little wet? Acclimate your dog to water and get them started swimming. Then if you want, upgrade to a more competitive sport!'
);
$color = Array(
	'0' => '31,143,9',
	'1' => '122,56,4',
	'2' => '122,102,5',
	'3' => '168,157,5',
	'4' => '0,99,167',
	'5' => '101,142,10',
	'6' => '5,122,106',
	'7' => '220,122,15',
	'8' => '144,24,10',
	'9' => '90,58,121'
);
if ($argpage == '') $sn = 0;
else $sn = $argpage;
echo '<h3>', $name[$sn], '</h3>', PHP_EOL, $title[$sn], PHP_EOL, '</div>', PHP_EOL;
include './sports/sports' . $argpage . '.php';
if ($sn != '0'): ?>
<form id="survey">
<p><b>Have you tried any <?php echo $name[$sn]; ?> activities with <?php echo $dog['name']; ?>?</b></p>
<p>If you have, please let us know whether you think these activities would be good for dogs
with similar temperaments to <?php echo $dog['name']; ?>.</p>
<fieldset id="answers">
<span onclick="sport_feedback(0)" id="answer_0">Not Good</span>
<span onclick="sport_feedback(1)" id="answer_1">Neutral</span>
<span onclick="sport_feedback(2)" id="answer_2">Good</span>
<span onclick="sport_feedback(3)" id="answer_3">Great</span>
</fieldset>
</form>
<script type="text/javascript">
var answers = '<?php echo $dog['sports_answers']; ?>';
var sn = <?php echo $sn; ?>;
function sport_feedback(n) {
	for (i = 0; i < 4; i++) document.getElementById('answer_' + i).className = '';
	document.getElementById('answer_' + n).className = 'checked';
	answers = answers.substr(0,sn) + n + answers.substr(sn+1);
	var params = 'type=sports&id=<?php echo $dog['id']; ?>&answers=' + answers;
//	post_data(params, function() { questions[n].changed = false; });
}
function sub_load() {
	if (answers.length < 10) answers += '1111111111';
	document.getElementById('answer_' + answers.substr(sn,1)).className = 'checked';
}
</script>
<?php endif; ?>
</div>
</div>
