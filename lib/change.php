<?php require '/srv/http/lib/functions.php';

if (!isset($_POST) || !isset($_POST['id']) || !isset($_POST['email']) || !isset($_POST['old']) || !isset($_POST['new']) || !isset($_POST['confirm'])) {
	header('Location: ' . $dd_surveys . '?pg=reset');
	die();
}
$id = $_POST['id'];
$email = $_POST['email'];
$old = $_POST['old'];
$new = $_POST['new'];
$confirm = $_POST['confirm'];

if (strcmp($new,$confirm) != 0) { header('Location: ' . $dd_surveys . '?pg=reset&confirm'); die(); }

$ret = '';
$uid = check_pw($email, $old, $ret);
if (!$uid || $uid != $id) { header('Location: ' . $dd_surveys . '?pg=reset' . $ret); die(); }

if (!set_pw($id, $email, $new)) { header('Location: ' . $dd_surveys . '?pg=reset&change'); die(); }

header('Location: ' . $dd_surveys . '?pg=reset&success');
?>
