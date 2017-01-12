<?php if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) die(); ?>
<div id="reset" class="nav_target">
<h3>Change <?php echo $user['first']; ?>'s password</h3>
<?php if (isset($_GET['confirm'])): ?>
<p class="err">New password entries do not match.</p>
<?php elseif (isset($_GET['database'])): ?>
<p class="err">Database Connection Error: Please report this to <a href="mailto:jmcclure@darwinsdogs.org">jmcclure@darwinsdogs.org</a>.</p>
<?php elseif (isset($_GET['query'])): ?>
<p class="err">Query Error: Please report this to <a href="mailto:jmcclure@darwinsdogs.org">jmcclure@darwinsdogs.org</a>.</p>
<?php elseif (isset($_GET['pw'])): ?>
<p class="err">Old password does not match.</p>
<?php elseif (isset($_GET['change'])): ?>
<p class="err">Error setting new password. Please report this to <a href="mailto:jmcclure@darwinsdogs.org">jmcclure@darwinsdogs.org</a>.</p>
<?php endif; ?>
<?php if (isset($_GET['success'])): ?>
<p>Your password has been reset.</p>
<?php else: ?>
<p>Note: if you initially enrolled with a wordpress "username" once you reset
your password you will no longer be able to sign in with that name: we are
moving to email and password login only.</p>
<form method="POST" action="lib/change.php">
	<input type="hidden" name="id" value="<?php echo $user['id']; ?>">
	<input type="hidden" name="email" value="<?php echo $user['email']; ?>">
	<fieldset>
		<legend>Old Password</legend>
		<input type="password" name="old" required>
	</fieldset>
	<fieldset>
		<legend>New Password</legend>
		<input type="password" name="new" required>
	</fieldset>
	<fieldset>
		<legend>Confirm New Password</legend>
		<input type="password" name="confirm" required>
	</fieldset>
	<fieldset>
		<input style="width: 10em;" type="submit" value="Reset Password" id="submit">
		<input style="width: 10em;" type="button" value="Cancel" id="cancel" onclick="window.location='?pg=home';">
	</fieldset>
<?php endif; ?>
</form>
</div>
