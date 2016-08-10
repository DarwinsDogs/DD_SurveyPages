<?php
$respath = '~jmcclure/draft/res/';
$dd_root = 'http://darwinsdogs.org/~jmcclure/draft/';
if (isset($_COOKIE['dd_logged_in'])) { $uid = $_COOKIE['dd_logged_in']; setcookie('dd_logged_in', $uid, time() + 3600, '/', '.darwinsdogs.org'); }
else { header('Location: http://darwinsdogs.org'); die(); }
if (isset($_GET['pg'])) $page = $_GET['pg'];
else $page = 'home';
if (isset($_GET['n'])) $npage = $_GET['n'];
else $npage = '';
if (isset($_GET['id'])) $idpage = $_GET['id'];
else $idpage = '';
if (isset($_GET['arg'])) $argpage = $_GET['arg'];
else $argpage = '';
if (isset($_GET['post_img'])) $post_img = '?' . time();
else $post_img = '';
if ($page == 'sports') $sidebar = false;
else $sidebar = true;

$banner = $page;
if ($page == 'review' || $page == 'thanks') $banner = 'survey';

/* connect to db and get user */
require '../../credentials.php';
try { $db = new PDO($dsn, $user, $password); }
catch (PDOException $e) { die('Database error: ' . $e->getMessage()); }
$stmt = $db->prepare('SELECT * FROM users WHERE id = :id');
$stmt->bindValue(':id', $uid, PDO::PARAM_INT);
if (!$stmt->execute()) die('Query error: ' . $stmt->errorInfo());
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) { header('Location: http://darwinsdogs.org'); }

/* submit login */
////UNCOMMENT when not readonly
//$stmt = $db->prepare('INSERT INTO logins ( member, timestamp ) VALUES ( :id, :time )');
//$stmt->bindValue(':id', $uid, PDO::PARAM_INT);
//$stmt->bindValue(':time', time(), PDO::PARAM_INT);
//if (!$stmt->execute()) die('Login error: ' . $stmt->errorInfo());

/* get dogs */
$stmt = $db->prepare('SELECT * FROM dogs WHERE owner = :uid AND NOT dogs.flags & 1');
$stmt->bindValue(':uid', $uid, PDO::PARAM_INT);
if (!$stmt->execute()) die('Query error: ' . $stmt->errorInfo());
$dogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
<title>Darwin's Dogs | Survey Site</title>
<!--[if lt IE 9]>
  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<link rel="icon" type="image/x-icon" href="http://darwinsdogs.org/wp-content/themes/dogtheme/favicon.ico" />
<link rel="stylesheet" type="text/css" href="res/style.css">
<link href='https://fonts.googleapis.com/css?family=PT+Sans:400,700|Overlock|Overlock+SC' rel='stylesheet' type='text/css'>
</head>
<body>
<script type="text/javascript">
function sub_load() { /* do nothing, overriden by included pages */ }
</script>
<main>

<!-- MAIN NAVIGATION -->
<nav>
<div id="bar">
	<a href="http://darwinsdogs.org" title="return to the public site"><div id="logo"></div></a>
	<ul>
		<li class="nav_button"><a href="?pg=home">HOME</a></li>
		<li class="nav_button"><a href="?pg=user">MEMBER PROFILE</a></li>
		<li class="nav_button"><a href="?pg=dog">ADD A DOG</a></li>
		<li class="nav_button"><a href="?pg=contact">CONTACT</a></li>
	</ul>
</div>
<div class="banner" style="background-image: url(http://darwinsdogs.org/<?php echo $respath . 'banner/' . $banner . $npage . '.jpg'; ?>);"></div>
</nav>

<?php if ($sidebar === true): /* TODO tokens */ ?>
<!-- SIDE BAR -->
<div id="side_bar">
<h2 class="smallcap">Welcome, <?php echo $user['first'];?></h2>
<div id="user_block">
	<div id="user_avatar" style="background-image: url(http://darwinsdogs.org/<?php echo $respath . 'users/' . $user['id'] . '.png' . $post_img; ?>);"></div>
	<div id="user_name">
		<?php echo $user['first'] . ' ' . $user['last'] . '<br/>' .
		'<span class="sanscap">Member since ' . date('M Y', $user['start_date']) . '</span><br/>' . PHP_EOL; ?>
		<a class="sanscap fontlink" href="?pg=user">Update Profile</a></span>
	</div>
</div>
<div id="pre_dog_block"><span class="sanscap"><?php echo $user['first']; ?>'s Dogs</span><a class="sanscap fontlink" href="?pg=dog">Add Dog</a></div>
<?php foreach ($dogs as $dog) : ?>
<div class="dog_block" id="dog_block">
	<div id="dog_avatar" style="background-image: url(http://darwinsdogs.org/<?php echo $respath . 'dogs/' . $dog['id'] . '.png' . $post_img; ?>);"></div>
	<div id="dog_name">
		<div class="badges"></div>
		<span class="name"><?php echo $dog['name']; ?></span><br/>
		<a class="sanscap fontlink" href="?pg=dog&amp;id=<?php echo $dog['id']; ?>">Update Profile</a>
	</div>
</div>
<?php endforeach; ?>
</div>
<?php endif; /* Sidebar */ ?>

<!-- BODY -->
<?php include $page . '.php'; ?>

</main>
<!-- FOOTER -->
<footer>
<div id="copy">&copy; Copyright 2015 Darwin's Dogs
	<div id="contribute">Graphic design by Brian Prendergast<br/>
	Database design by Chris Hancock<br/>
	Website and database implementation by Jesse McClure</div>
</div>
</footer>

</body>
<script type="text/javascript">
var debug = <?php echo (isset($_GET['debug']) ? 'true' : 'false'); ?>;
function post_data(params, success) {
	http = new XMLHttpRequest();
	http.open('POST', 'http://darwinsdogs.org/~jmcclure/draft/submit.php' , true);
	http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	http.setRequestHeader('Content-length', params.length);
	http.setRequestHeader('Connection', 'close');
	http.onreadystatechange = function () {
		if (http.readyState == 4 && http.status == 200) {
			var ret = JSON.parse(this.responseText);
			if (ret.success) success(ret.msg);
			else if (debug) alert(ret.msg);
		}
	}
	http.send(params);
}
function update_height() {
	var sidebar = document.getElementById('side_bar');
	var complete = document.getElementById('surveys_completed');
	if (sidebar) sidebar.style.minHeight = document.getElementsByClassName('nav_target')[0].clientHeight + "px";
	if (complete) complete.style.minHeight = (document.getElementById('side_bar').clientHeight - 100) + "px";
}
window.onload = function() { update_height(); sub_load(); }
</script>
</html>
