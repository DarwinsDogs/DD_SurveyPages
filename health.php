<?php
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) die();
if ($idpage != '') { foreach ($dogs as $d) { if ($idpage == $d['id']) $dog = $d; } }
else { header('Location: ' . $dd_root); }
?>
<div id="health" class="nav_target">
<h3><?php echo $dog['name']; ?>'s Health Information</h3>
Coming soon ...
</div>
