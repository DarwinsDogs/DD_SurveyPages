<?php
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) die();
if ($idpage != '') { foreach ($dogs as $d) { if ($idpage == $d['id']) $dog = $d; } }
else { header('Location: ' . $dd_surveys); }

$counts = count_chars($dog['surveys'],1);
$nsurvey = (isset($counts[51]) ? $counts[51] : 0);

?>
<div id="feedback" class="nav_target">
<h3>Personalized Feedback for <?php echo $dog['name']; ?></h3>
<span style="color: blue;">This page is a work in progress.  We will gradually
be adding content here as it becomes available.  For now feel free to check out
our dog-sport pages:</span>
<ul>
<li>DNA sample status:
<?php echo
	(isset($dog['barcode']) ? 'barcode=' . $dog['barcode'] : ($dog['flags'] & 2 ? 'in queue for mailing' : 'no kit sent' )),
	($dog['flags'] & 16 ? '; kit received ' . date('M jS Y', $dog['kit_date']) . '; not yet genotyped' : '');
?>
</li>
<li><a href="?pg=sports&amp;id=<?php echo $dog['id']; ?>">Activity / Dog Sport Recommendations</a></li>
</ul>
</div>
