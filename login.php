
<div id="login" class="nav_target">
<h5>Welcome To The Surveys - Please Sign In</h5>
<?php
if (isset($_GET['user'])) echo '<p>Member <b>', $_GET['user'], '</b> not found</p>';
else if (isset($_GET['pw'])) echo '<p>Incorrect password for <b>', $_GET['pw'], '</b></p>';
else if (isset($_GET['enroll'])) echo '<p><b>', $_GET['enroll'], '</b> is not enrolled in the surveys</p>';
else if (isset($_GET['login'])) echo '<p>You must login before continuing</p>';
if (isset($_GET['next'])) $next = urldecode($_GET['next']);
else if (isset($_GET['pg']) && $_GET['pg'] != 'login') $next = $_SERVER['QUERY_STRING'];
else $next = '';
?>
<form method="POST" action="lib/loginout.php">
User Name:<br/>
<input class="txt" type="text" name="user" placeholder="user name or email address"><br/>
Password:<br/>
<input class="txt" type="password" name="pw"><br/>
<input type="hidden" name="next" value="<?php echo $next; ?>">
<input class="btn" type="submit">
<input class="btn" type="button" value="Cancel" onclick="window.location='<?php echo $dd_home; ?>';">
</form>
<?php if (isset($_GET['pw'])): ?>
<div style="text-align: center; padding-bottom: 1rem;">Lost your password? Request a <a href="https://darwinsdogs.org?pg=reset">new password</a>.</div>
<?php endif; ?>
</div>

