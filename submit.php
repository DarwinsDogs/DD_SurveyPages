<?php
if (isset($_COOKIE['dd_logged_in'])) { $uid = $_COOKIE['dd_logged_in']; setcookie('dd_logged_in', $uid, time() + 3600, '/', '.darwinsdogs.org'); }
else { die('{"success":false,"msg":"login error"}'); }

require '/srv/secure/dogdb.conf';
try { $db = new PDO($dsn, $user, $password); }
catch (PDOException $e) {
	file_put_contents('submissions.err', 'TYPE=db, data=' . print_r($_GET) .
		', ERROR=' . $e->getMessage() . PHP_EOL, FILE_APPEND);
	die('{"success":false,"msg":"database error"}'); }
}

function submit_answer() {
	global $db;
	$query_submit_answer = 'INSERT INTO answers VALUES ( :id, :question, :dog, :answer, :notes, :time )
		ON DUPLICATE KEY UPDATE  answer = :answer, notes = :notes, time = :time';
	if (!isset($_GET['id'])) return;
	$id = $_GET['id'];
	$stmt = $db->prepare($query_submit_answer);
	if ($id > 0) $stmt->bindValue(':id', $id, PDO::PARAM_INT);
	else $stmt->bindValue(':id', NULL, PDO::PARAM_NULL);
	$stmt->bindValue(':question', $_GET['question'], PDO::PARAM_INT);
	$stmt->bindValue(':dog', $_GET['dog'], PDO::PARAM_INT);
	$stmt->bindValue(':answer', urldecode($_GET['answer']), PDO::PARAM_STR);
	$stmt->bindValue(':notes', urldecode($_GET['notes']), PDO::PARAM_STR);
	$stmt->bindValue(':time', time(), PDO::PARAM_INT);
	if (!$stmt->execute()) {
		file_put_contents('submissions.err', 'TYPE=answer, ' .
			'dog=' . $_GET['dog'] . ', qn=' . $_GET['qn'] . ', ans=' . $_GET['ans'] .
			'ERROR=' . $stmt->errorCode() . ', info=' . $stmt->errorInfo() . PHP_EOL,
			FILE_APPEND);
		die('{"success":false,"msg":"PDO ERROR ' . $stmt->errorCode() . '"}');
	}
}

if (!isset($_GET['type'])) die('{"success":false,"msg":"missing submission type"}');
switch ($_GET['type']) {
	case 'answer': submit_answer(); break;
	default: die('{"success":false,"msg":"unknown submission type"}');
}

echo '{"success":true,"msg":"', $_GET['type'], '"}';
?>
