<?php
if ($idpage != '') { foreach ($dogs as $d) { if ($idpage == $d['id']) $dog = $d; } }
else { $dog = Array( "name" => "" ); }
if (isset($_POST) && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['message'])) {
	$to      = 'jmcclure@darwinsdogs.org'; # TODO: change to info address when done testing
	$subject = '[Contact Us] Message from ' . $_POST['name'];
	$message = wordwrap($_POST['message'], 70, "\r\n");
	$headers = 'From: ' . $_POST['email'] . "\r\n" . 'Reply-To: ' . $_POST['email'] . "\r\n" . 'X-Mailer: PHP/' . phpversion();
	mail($to, $subject, $message, $headers);
	header('Location: ' . $dd_root . '?pg=contact&arg=posted');
}
?>
<div id="contact" class="nav_target">
<?php if ($argpage == 'posted'): ?>
	<form id="sent" style="display: none;">
	<h3>Thank you for your comments</h3>
	<fieldset>
	<legend>Your message has been sent</legend>
	<span>Thank you for contacting us.  We will reply shortly.  You can also
	always contact us directly at
	<a href="mailto:info@darwinsdogs.org">info@darwinsdogs.org</a></span>
	</fieldset>
	</form>
<?php else: ?>
	<form id="compose" method="POST" action="./contact.php">
	<h3>Leave us feedback or ask a question</h3>
	<p style="width: 44rem;">Please first check our <a href="http://darwinsdogs.org/?page_id=604" target="_blank">frequently asked questions</a> to see if your question has already been addressed there.
	Additionally, you may find further information in our <a href="http://darwinsdogs.org/?page_id=30" target="_blank">discussion forums</a>.</p>
	<fieldset>
		<legend>What's on your mind?</legend>
		<textarea id="message" name="message"><?php if ($argpage == 'retire') echo 'Please retire my dog ', $dog['name'], ' (id=', $dog['id'], ').', PHP_EOL, PHP_EOL; ?></textarea>
	</fieldset>
	<fieldset>
		<legend>Where would you like us to reply?</legend>
		<input type="email" id="reply_to" name="email" value="<?php echo $user['email']; ?>">
	</fieldset>
	<fieldset>
	<input id="user_info" name="name" value="<?php echo $user['first'] . ' ' . $user['last'] . '(id=' . $user['id'] .')'; ?>" hidden>
	<input style="width: 10em;" type="button" value="Cancel" class="button nav_button" id="home">
	<input style="width: 10em;" type="button" value="Submit" class="button" id="submit">
	</fieldset>
	</form>
<?php endif; ?>
</div>
