<?php if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) die();
/* get surveys */
$stmt = $db->prepare('SELECT dogs.id AS dog, dogs.surveys AS surveys, surveys.id AS id, title,
		intro, color, surveys.image AS image, status, dogs.groups AS dgroup, surveys.groups AS sgroup
	FROM dogs, surveys WHERE dogs.owner = :uid AND NOT dogs.flags & 1
	ORDER BY isnull(priority), priority, isnull(status), id, dog');
$stmt->bindValue(':uid', $user['id'], PDO::PARAM_INT);
if (!$stmt->execute()) die('Query error: ' . $stmt->errorInfo());
$surveys = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div id="home" class="nav_target">
<?php if (!$sidebar) echo '<div id="pad"></div>'; ?>
<div id="surveys_up_next">
<h3>Questionnaires</h3>
<h5>Up next for <?php echo $user['first']; ?></span></h5>
<?php
$complete_column = '';
$redos = '';
$nsurveys = 0;
$nshowing = 0;
$ndone = 0;
for ($i = 0; $i < count($surveys); $i += count($dogs)) {
	$survey = $surveys[$i];
	if ($survey['status'] > $user['status']) continue; // TODO add testing icon
	$ndogs = count($dogs); $started = 0; $complete = 0;
	for ($j = 0; $j < count($dogs); $j++) {
		$surveys[$i + $j]['state'] = substr($surveys[$i + $j]['surveys'], $survey['id'] - 1, 1);
		if (!($surveys[$i + $j]['dgroup'] & $surveys[$i + $j]['sgroup'])) { $ndogs--; }
		else if ($surveys[$i + $j]['state'] == '3') { $complete++; $started++; }
		else if ($surveys[$i + $j]['state'] == '1') { $started++; }
	}
	if ($ndogs < 1) continue;
	$nsurveys++;
	if ($complete < $ndogs) {
		if ($nshowing > 2) continue;
		echo '<!--', $survey['title'], '-->', PHP_EOL; ?>
<div class="survey_token" id="survey_token_1" onclick="survey_popup(event, '<?php echo $i; ?>');"
		style="background: url(<?php echo $dd_surveys . 'res/banners/survey' . $survey['id']; ?>.jpg); background-size: 100% 100%; background-position: -5.5em 0em;">
	<div style="background: rgba(<?php echo $survey['color']; ?>,1);"><h5 id="survey_name"><?php echo $survey['title']; ?></h5><a><?php echo ($started ? 'Resume' : 'Begin'); ?></a></div>
</div>
<div class="survey_selector" id="survey_selector_<?php echo $i; ?>" onclick="hide_popups();">
<?php
		for ($j = 0; $j < count($dogs); $j++) {
			if ($surveys[$i + $j]['state'] == 0)
				echo "\t", '<div class="begin" style="background: rgba(', $survey['color'], ',1);" onclick="window.location=\'?pg=survey&n=',
					$survey['id'], '&id=',  $surveys[$i + $j]['dog'], '\'">Start for ', $dogs[$j]['name'], '</div>';
			else if ($surveys[$i + $j]['state'] == 1)
				echo "\t", '<div class="begin" style="background: rgba(', $survey['color'], ',1);" onclick="window.location=\'?pg=survey&n=',
					$survey['id'], '&id=',  $surveys[$i + $j]['dog'], '\'">Resume for ', $dogs[$j]['name'], '</div>';
			else if ($surveys[$i + $j]['state'] == 3)
				echo "\t", '<div class="begin disabled" style="background: rgba(', $survey['color'], ',1);">', $dogs[$j]['name'], ' is done!</div>';
		}
		$nshowing++;
		echo '</div>', PHP_EOL;
	}
	else if ($complete == $ndogs) {
		$complete_column .= "\t" . '<div class="survey_token" onclick="window.location=\'?pg=review&n=' . $survey['id'] . '\'" style="background: rgb(' . $survey['color'] . ');">' .
			'<h6 id="survey_name">' . $survey['title'] . '</h6><a>Review</a></div>' . PHP_EOL;
		for ($j = 0; $j < count($dogs); $j++)
			$redos .= '<div class="begin" onclick="window.location=\'?pg=contact&arg=redo_' . urlencode($survey['title']) . '_' . $survey['id'] . '&id=' . $dogs[$j]['id'] . '\'" ' .
					'style="background: rgba(' . $survey['color'] . ',1);">Redo "' . $survey['title'] . '" for ' . $dogs[$j]['name'] . '</div>' . PHP_EOL;
		$ndone++;
	}
}
if (count($dogs) == 0): ?>
	<div class="survey_token_thanks">
	<p>Welcome to the survey website.  If this is your first visit, you may want
	to <a href="?pg=reset">reset your password</a> which can be done anytime by
	following the link in the footer of your profile page.</p>
	<p>Otherwise, get started by following one of the links to
	<a href="?pg=dog">Add A Dog</a>.  Your added dog(s) will show up in the left
	sidebar.  Feel free to add all your dogs.</p>
	<p>You can fill out each dog's profile information, then return here to the
	survey home page where you will see big survey token buttons.  Click on each
	survey button to start that survey.</p>
	<p>You will be only be given 3 surveys to chose from at a time, but there are
	currently just over a dozen separate surveys.  Each one is short (about 10
	questions).  We encourage you to fill out a couple, log out, and come back
	here later.  Even if you opt to finish all the surveys at once, please
	regularly check back as we will occasionally add new surveys here.</p>
	<p>Also feel free to update your own <a href="?pg=user">profile</a>.  Please
	be sure to at least enter and validate a mailing address so we can send out a
	DNA sample kit once the surveys are filled out.</p>
	</div>
<?php elseif ($nshowing == 0): ?>
	<div class="survey_token_thanks">
	Thanks for your hard work. You have completed all currently available surveys.  Feel free to join us on the
	<a href="<?php echo $dd_home; ?>?pg=forum">forums</a> and please check back here for new surveys that may be added.
	You can review your answers and see how other owners are answering by clicking on the survey tokens on the right.
	You can also download a copy of the raw data for your answers <a href="<?php echo $dd_surveys; ?>lib/spreadsheet.php">here</a>.
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
	<a class="fontlink" id="request_redo" onclick="redo_popup(event);">REQUEST A REDO</a>
<?php if (empty($redos)): ?>
	<div class="survey_selector" id="redo_list" style="display: none; font-size: 80%;">Nothing to Redo.  Please begin the surveys.</div>
<?php else: ?>
	<div class="survey_selector" id="redo_list" style="display: none; font-size: 80%;"><?php echo $redos; ?></div>
<?php endif; ?>
</div>
</div>
<script type="text/javascript">
function m() { alert("hello"); }
function hide_popups() { var pops = document.getElementsByClassName('survey_selector'); for (i = 0; i < pops.length; i++) { pops[i].style.display = 'none'; } }
function survey_popup(e, n) { hide_popups(); var pop = document.getElementById('survey_selector_' + n); pop.style.left = e.clientX + 'px'; pop.style.top = e.clientY + 'px'; pop.style.display = 'block'; }
function redo_popup(e) { hide_popups(); var pop = document.getElementById('redo_list'); pop.style.display = 'block'; pop.style.top = (e.clientY - pop.clientHeight) + 'px'; pop.style.left = (e.clientX - pop.clientWidth) + 'px'; }
window.document.onmouseup = function () { hide_popups(); }
</script>
