<?php
/* get surveys */
$stmt = $db->prepare('SELECT dogs.id AS dog, dogs.surveys AS surveys, surveys.id AS id, title,
		intro, color, surveys.image AS image, status, dogs.groups AS dgroup, surveys.groups AS sgroup
	FROM dogs, surveys WHERE dogs.owner = :uid AND NOT dogs.flags & 1
	ORDER BY isnull(priority), priority, isnull(status), id, dog');
$stmt->bindValue(':uid', $uid, PDO::PARAM_INT);
if (!$stmt->execute()) die('Query error: ' . $stmt->errorInfo());
$surveys = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div id="home" class="nav_target">
<div id="surveys_up_next">
<h3>Questionnaires</h3>
<h5>Up next for <?php echo $user['first']; ?></span></h5>
<?php
$complete_column = '';
$nsurveys = 0;
$nshowing = 0;
$ndone = 0;
for ($i = 0; $i < count($surveys); $i += count($dogs)) {
	$survey = $surveys[$i];
	if ($survey['status'] > $user['status']) continue; // TODO add testing icon
	$ndogs = count($dogs); $started = 0; $complete = 0;
	for ($j = 0; $j < count($dogs); $j++) {
		$surveys[$i + $j]['state'] = substr($surveys[$i + $j]['surveys'], $survey['id'] - 1, 1);
		if ($surveys[$i + $j]['state'] == '3') { $complete++; $started++; }
		if ($surveys[$i + $j]['state'] == '1') { $started++; }
		if (!($survey['dgroup'] & $survey['sgroup'])) {
			$survey['state'] = '9';
			$ndogs--;
		}
	}
	if ($survey['state'] != 9) $nsurveys++;
	if ($ndogs < 1) continue;
	else if ($complete < $ndogs) {
		if ($nshowing > 2) continue;
		echo '<!--', $survey['title'], '-->', PHP_EOL; ?>
<div class="survey_token" id="survey_token_1" onclick="survey_popup('<?php echo $i; ?>');"
		style="background: url(http://darwinsdogs.org/<?php echo $respath . 'banner/survey' . $survey['id']; ?>.jpg); background-size: 100% 100%; background-position: -5.5em 0em;">
	<div style="background: rgba(<?php echo $survey['color']; ?>,1);"><h5 id="survey_name"><?php echo $survey['title']; ?></h5><a href="#"><?php echo ($started ? 'Resume' : 'Begin'); ?></a></div>
</div>
<div class="survey_selector" id="survey_selector_<?php echo $i; ?>" onclick="hide_popups();">
<?php
		for ($j = 0; $j < count($dogs); $j++) {
			$class = 'begin';
			if ($surveys[$i + $j]['state'] == 0)
				echo "\t", '<div class="begin" style="background: rgba(', $survey['color'], ',1);" onclick="window.location=\'?pg=survey&n=',
					$survey['id'], '&id=',  $surveys[$i + $j]['dog'], '\'">Begin for ', $dogs[$j]['name'], '</div>';
			else if ($surveys[$i + $j]['state'] == 1)
				echo "\t", '<div class="begin" style="background: rgba(', $survey['color'], ',1);" onclick="window.location=\'?pg=survey&n=',
					$survey['id'], '&id=',  $surveys[$i + $j]['dog'], '\'">Resume for ', $dogs[$j]['name'], '</div>';
			else
				echo "\t", '<div class="begin disabled" style="background: rgba(', $survey['color'], ',1);">', $dogs[$j]['name'], ' is done</div>';
		}
		$nshowing++;
		echo '</div>', PHP_EOL;
	}
	else if ($complete == $ndogs) {
		$complete_column .= "\t" . '<div class="survey_token" onclick="window.location=\'?pg=review&n=' . $survey['id'] . '\'" style="background: rgb(' . $survey['color'] . ');">' .
			'<h6 id="survey_name">' . $survey['title'] . '</h6><a href="#">Review</a></div>' . PHP_EOL;
		$ndone++;
	}
}
if ($nshowing == 0): ?>
	<div class="survey_token_thanks">
	Thanks for your hard work. You have completed all currently available surveys.  Feel free to join us on the
	<a href="http://darwinsdogs.org/?page_id=30">forums</a> and please check back here for new surveys that may be added.
	Also please be sure your mailing address is validated and confirmed in your profile so you can receive a DNA sampling
	kit (<a href="http://darwinsdogs.org/?topic=confirm-your-mailing-addresss-step-by-step">instructions</a>).<br/><br/>
	You can review your answers and see how other owners are answering by clicking on the survey tokens on the right.
	You can also download a copy of the raw data for your answers <a href="http://members.darwinsdogs.org/spreadsheet.php">here</a>.
	</div>
<?php else: ?>
	<div class="ncompleted" style="color: #666; font-size: 0.8em;"
	title="You may take a break and log out at any time.  When you log back in, you will pick up where you left off.">
		You have completed <?php echo $ndone; ?> out of <?php echo $nsurveys; ?> currently available surveys.
	</div>
<?php endif; ?>
</div>
<div id="surveys_completed">
	<h5 title="See how your dogs compare to our population data">Completed</h5>
<?php echo $complete_column; ?>
</div>
</div>
<script type="text/javascript">
function m() { alert("hello"); }
function hide_popups() { var pops = document.getElementsByClassName('survey_selector'); for (i = 0; i < pops.length; i++) { pops[i].style.display = 'none'; } }
function survey_popup(n) { hide_popups(); var pop = document.getElementById('survey_selector_' + n); pop.style.left = event.clientX + 'px'; pop.style.top = event.clientY + 'px'; pop.style.display = 'block'; }
window.document.onmouseup = function () { hide_popups(); }
</script>
