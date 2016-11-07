<?php require '/srv/http/lib/functions.php';

if (isset($_GET['out'])) { log_out(); header('Location: ' . $dd_home); die(); }

if (!(isset($_POST) && isset($_POST['user']) && isset($_POST['pw']))) { header('Location: ' . $dd_surveys . '?pg=login'); die(); }

$ret = '';
$uid = check_pw($_POST['user'], $_POST['pw'], $ret);
if (!$uid) { header('Location: ' . $dd_surveys . '?' . $ret); die(); }

log_in($uid);
header('Location: ' . $dd_surveys . (isset($_POST['next']) ? '?' . $_POST['next'] : '' ));
?>
