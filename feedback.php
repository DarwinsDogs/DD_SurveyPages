<?php
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) die();
if ($idpage != '') { foreach ($dogs as $d) { if ($idpage == $d['id']) $dog = $d; } }
else { header('Location: ' . $dd_root); }

$counts = count_chars($dog['surveys'],1);
$nsurvey = (count($counts) > 51 ? $counts[51] : 0);

?>
<div id="feedback" class="nav_target">
<h3>Personalized Feedback for <?php echo $dog['name']; ?></h3>
Coming soon ...
<ul>
<li><?php echo ($nsurvey > 1 ? $nsurvey . ' surveys' : ($nsurvey == 1 ? '1 survey' : 'no surveys')); ?> complete</li>
<li><?php echo ($dog['barcode'] > 1 ? 'Kit Sent: ' . $dog['barcode'] : 'No Kit Sent' ) ?></li>
</ul>
</div>
