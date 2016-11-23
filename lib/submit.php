<?php require '/srv/http/lib/functions.php';
$user = get_cur_user();
if (!$user) die('{"success":false,"msg":"login error"}');

function log_err($type, $msg) {
	global $dd_surveys;
	file_put_contents('post.err', '[' . time() . '] TYPE=' . $type . ' POST=' . print_r($_POST, TRUE) . ' ERROR=' . $msg . PHP_EOL, FILE_APPEND);
	if (isset($_POST['on_fail'])) header('Location: ' . $dd_surveys . '?pg=' . $_POST['on_fail']); # why doesn't this work?
	else die('{"success":false,' . '"type":"' . $type . '", "msg":"' . $msg . '"}');
}

$db = get_db();
if (!$db) log_err('db', 'Connection Failed');

function submit_user() {
	global $db;
	$stmt = $db->prepare('UPDATE users SET first = :first, last = :last, address = :address, flags = :flags,
		image = :image, phoneDay = :phoneDay, phoneEve = :phoneEve, nick = :nick, tagline = :tagline, bio = :bio WHERE id = :id');
	$stmt->bindValue(':first', $_POST['first'], PDO::PARAM_STR);
	$stmt->bindValue(':last', $_POST['last'], PDO::PARAM_STR);
	$flags = $_POST['flags'] & ~ 8;
	if (isset($_POST['opt_out']) && $_POST['opt_out'] == 'yes') $flags = $flags | 8;
	if ($_POST['validated'] == 1) { $stmt->bindValue(':address', $_POST['address'], PDO::PARAM_STR); $flags = $flags | 2; }
	else { $stmt->bindValue(':address', $_POST['address_orig'], PDO::PARAM_STR); }
	$stmt->bindValue(':flags', $flags, PDO::PARAM_INT);
	if (submit_image('users', $_POST['id'])) $stmt->bindValue(':image', $_POST['id'], PDO::PARAM_INT);
	else $stmt->bindValue(':image', $_POST['image'], PDO::PARAM_INT);
	$stmt->bindValue(':phoneDay', $_POST['phoneDay'], PDO::PARAM_STR);
	$stmt->bindValue(':phoneEve', $_POST['phoneEve'], PDO::PARAM_STR);
	$stmt->bindValue(':nick', $_POST['nick'], PDO::PARAM_STR);
	$stmt->bindValue(':tagline', $_POST['tagline'], PDO::PARAM_STR);
	$txt = strip_tags($_POST['bio']);
	$txt = str_replace("\n",'<br>',$txt);
	$stmt->bindValue(':bio', $txt, PDO::PARAM_STR);
	$stmt->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
	if (!$stmt->execute()) log_err('user', print_r($stmt->errorInfo(), TRUE) . ' (' . $stmt->errorCode() . ')');
}

function submit_dog() {
	global $db;
	if ($_POST['id'] > 0) {
		$stmt = $db->prepare('UPDATE dogs SET name = :name, sex = :sex, neutered = :neutered, age = :age, birthday = :birthday,
				breed1 = :breed1, breed2 = :breed2, breed3 = :breed3, purebred = :purebred,
				summary = :summary, quirk = :quirk, does_best = :does_best, fun_story = :fun_story, dom_entry = :dom_entry WHERE id = :id');
		$stmt->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
	}
	else {
		$stmt = $db->prepare('INSERT INTO dogs ( name, sex, neutered, age, birthday, breed1, breed2, breed3, purebred, owner, consent_date, flags )
			VALUES ( :name, :sex, :neutered, :age, :birthday, :breed1, :breed2, :breed3, :purebred, :owner, :consent_date, 0 )');
		$stmt->bindValue(':owner', $_POST['owner'], PDO::PARAM_INT);
		$stmt->bindValue(':consent_date', time(), PDO::PARAM_INT);
	}
	$stmt->bindValue(':name', $_POST['name'], PDO::PARAM_STR);
	$stmt->bindValue(':sex', $_POST['sex'], PDO::PARAM_STR);
	$stmt->bindValue(':neutered', $_POST['neutered'], PDO::PARAM_STR);
	$stmt->bindValue(':age', $_POST['age'], PDO::PARAM_STR);
	$stmt->bindValue(':birthday', $_POST['birthday'], PDO::PARAM_STR);
	$stmt->bindValue(':breed1', $_POST['breed1'], PDO::PARAM_STR);
	$stmt->bindValue(':breed2', $_POST['breed2'], PDO::PARAM_STR);
	$stmt->bindValue(':breed3', $_POST['breed3'], PDO::PARAM_STR);
	if ($_POST['id'] > 0) {
		$stmt->bindValue(':summary', $_POST['summary'], PDO::PARAM_STR);
		$stmt->bindValue(':quirk', $_POST['quirk'], PDO::PARAM_STR);
		$stmt->bindValue(':does_best', $_POST['does_best'], PDO::PARAM_STR);
		$txt = strip_tags($_POST['fun_story']);
		$txt = str_replace("\n",'<br>',$txt);
		$stmt->bindValue(':fun_story', $txt, PDO::PARAM_STR);
		if (isset($_POST['dom_entry'])) $stmt->bindValue(':dom_entry', $_POST['dom_entry'], PDO::PARAM_STR);
		else $stmt->bindValue(':dom_entry', NULL, PDO::PARAM_NULL);
	}
	if (isset($_POST['purebred'])) $stmt->bindValue(':purebred', $_POST['purebred'], PDO::PARAM_STR);
	else $stmt->bindValue(':purebred', NULL, PDO::PARAM_NULL);
	if (!$stmt->execute()) log_err('dog', print_r($stmt->errorInfo(),TRUE) . ' (' . $stmt->errorCode() . ')');
	if (isset($_FILES)) {
		$id = ($_POST['id'] > 0 ? $_POST['id'] : $id = $db->lastInsertId());
		if (!submit_image('dogs', $id)) return;
		$stmt = $db->prepare('UPDATE dogs SET image = :image WHERE id = :id');
		$stmt->bindValue(':image', $id, PDO::PARAM_INT);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		if (!$stmt->execute()) log_err('dog', print_r($stmt->errorInfo(),TRUE) . ' (' . $stmt->errorCode() . ')');
	}
}

function submit_answer() {
	global $db;
	$query = 'INSERT INTO answers VALUES ( :id, :question, :dog, :answer, :notes, :time )
		ON DUPLICATE KEY UPDATE answer = :answer2, notes = :notes2, time = :time2';
	$stmt = $db->prepare($query);
	if ($_POST['id'] > 0) $stmt->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
	else $stmt->bindValue(':id', NULL, PDO::PARAM_NULL);
	$stmt->bindValue(':question', $_POST['question'], PDO::PARAM_INT);
	$stmt->bindValue(':dog', $_POST['dog'], PDO::PARAM_INT);
	$stmt->bindValue(':answer', urldecode($_POST['answer']), PDO::PARAM_STR);
	$stmt->bindValue(':answer2', urldecode($_POST['answer']), PDO::PARAM_STR);
	$stmt->bindValue(':notes', urldecode($_POST['notes']), PDO::PARAM_STR);
	$stmt->bindValue(':notes2', urldecode($_POST['notes']), PDO::PARAM_STR);
	$stmt->bindValue(':time', time(), PDO::PARAM_INT);
	$stmt->bindValue(':time2', time(), PDO::PARAM_INT);
	if (!$stmt->execute()) log_err('answer', print_r($stmt->errorInfo(),TRUE) . ' (' . $stmt->errorCode() . ')');
}

function submit_survey() {
	global $db;
	$stmt = $db->prepare('UPDATE dogs SET surveys = :surveys WHERE id = :dog');
	$stmt->bindValue(':dog', $_POST['id'], PDO::PARAM_INT);
	$stmt->bindValue(':surveys', urldecode($_POST['surveys']), PDO::PARAM_STR);
	if (!$stmt->execute()) log_err('survey', print_r($stmt->errorInfo(),TRUE) . ' (' . $stmt->errorCode() . ')');
	$stmt = $db->prepare('INSERT INTO fillouts ( dog, survey, timestamp ) VALUES ( :dog, :survey, :time )');
	$stmt->bindValue(':dog', $_POST['id'], PDO::PARAM_INT);
	$stmt->bindValue(':survey', $_POST['n'], PDO::PARAM_INT);
	$stmt->bindValue(':time', time(), PDO::PARAM_INT);
	if (!$stmt->execute()) log_err('survey2', print_r($stmt->errorInfo(),TRUE) . ' (' . $stmt->errorCode() . ')');
}

function submit_sports() {
	global $db;
	$query = 'UPDATE dogs SET sports_answer = :answer WHERE id = :dog';
	$stmt = $db->prepare($query);
	$stmt->bindValue(':dog', $_POST['id'], PDO::PARAM_INT);
	$stmt->bindValue(':answer', urldecode($_POST['answer']), PDO::PARAM_STR);
	if (!$stmt->execute()) log_err('sports', print_r($stmt->errorInfo(),TRUE) . ' (' . $stmt->errorCode() . ')');
}

function submit_image($type, $id) {
	global $post_img, $dd_path;
	if (!isset($_FILES) || !isset($_FILES['images']) || empty($_FILES['images']['tmp_name']) ) return false;
	$check = getimagesize($_FILES['images']['tmp_name']);
	if ($check == false  || $_FILES['images']['size'] > 8388608) return false;
	$p1 = $dd_path . 'res/' . $type . '/' . $id . '.tmp';
	$p2 = $dd_path . 'res/' . $type . '/' . $id . '.png';
	if (move_uploaded_file($_FILES['images']['tmp_name'], $p1)) {
		exec('convert ' . $p1 . ' -auto-orient -resize 600x800\> ' . $p2 . ' && rm ' . $p1);
		return true;
	}
	return false;
}

if (!isset($_POST['type'])) log_err('submit', 'missing submission type');
if (!isset($_POST['id'])) log_err('submit', 'missing id');
switch ($_POST['type']) {
	case 'user': submit_user(); break;
	case 'dog': submit_dog(); break;
	case 'answer': submit_answer(); break;
	case 'survey': submit_survey(); break;
	case 'sports': submit_sports(); break;
	default: log_err('submit', 'unknown submission type ' . $_POST['type']);
}

if (isset($_POST['on_success'])) header('Location: ' . $dd_surveys . '?pg=' . $_POST['on_success']);
else echo '{"success":true,"msg":"', $_POST['type'], '"}';
?>
