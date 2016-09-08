<?php
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) die();
if ($idpage != '') { foreach ($dogs as $d) { if ($idpage == $d['id']) $dog = $d; } }
else { $dog = Array( 'id' => '0', 'name' => '' ); }
if (isset($_POST) && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['message']) && isset($_POST['to'])) {
	$to = $_POST['to'];
	$subject = '[Contact Us] Message from ' . $_POST['name'];
	$message = wordwrap($_POST['message'], 70, "\r\n");
	$headers = 'From: ' . $_POST['email'] . "\r\n" . 'Reply-To: ' . $_POST['email'] . "\r\n" . 'X-Mailer: PHP/' . phpversion();
	mail($to, $subject, $message, $headers);
	header('Location: ' . $dd_root . '?pg=contact&arg=posted');
}
$top = '<legend>What\'s on your mind?</legend>
<p>Please first check our <a href="' . $dd_home . '?page_id=604" target="_blank">frequently asked questions</a> to see if your question has already been addressed there.
Additionally, you may find further information in our <a href="' . $dd_home . '?page_id=30" target="_blank">discussion forums</a>.</p>';
$text = '';
$addressee = 'info@darwinsdogs.org';
if (strlen($argpage) > 1) {
	$argspage = explode('_',$argpage);
	if ($argspage[0] == 'retire') {
		$text = '
> Please retire my dog ' . $dog['name'] . ' (id=' . $dog['id'] . ').' . '
> Feel free to let us know why we\'re retiring ' . $dog['name'] . ' (optional):

';
	}
	else if ($argspage[0] == 'redo') {
	$top = '<legend>Uh oh, need a redo?</legend>
<p>As noted <a href="http://darwinsdogs.org/?topic=edit-answers" target="_blank">here</a> we generally prefer to not have participants go back to edit answers.  We want your first answers.  But if there has been a clear mistake that needs to be revised, we can let you redo a survey for a dog.  Please let us know why you think you need to redo a survey and we <b>may</b> reset it for you.</p>';
		$text = '
> Please let me redo the "' . urldecode($argspage[1]) . '" survey (id=' . $argspage[2] . ') for ' . $dog['name'] . ' (id=' . $dog['id'] . ')
> Why do you need to redo the survey? (required):

';
	}
	else if ($argspage[0] == 'bug') {
		$addressee = 'jmcclure@darwinsdogs.org';
		$top = '<legend style="color: red;">BUG REPORT:</legend><p>Please carefully read <a href="http://www.chiark.greenend.org.uk/~sgtatham/bugs.html" target="_blank">this page</a> on how to write good bug reports <b>before</b> submitting the form below.</p>';
		$text = '
> This is a bug report for the "' . $argspage[1] . '" page.
> Please describe *in detail* exactly what the problem is:

';
	}
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
	<fieldset>
		<?php echo $top; ?>
		<textarea id="message" name="message" placeholder="Type your message here"><?php echo $text; ?></textarea>
	</fieldset>
	<fieldset>
		<legend>Where would you like us to reply?</legend>
		<input type="email" id="reply_to" name="email" value="<?php echo $user['email']; ?>">
	</fieldset>
	<fieldset>
	<input id="user_info" name="name" value="<?php echo $user['first'] . ' ' . $user['last'] . '(id=' . $user['id'] .')'; ?>" hidden>
	<input id="addressee" name="to" value="<?php echo $addressee; ?>" hidden>
	<input style="width: 10em;" type="button" value="Cancel" class="button nav_button" id="home">
	<input style="width: 10em;" type="button" value="Submit" class="button" id="submit">
	</fieldset>
	</form>
<?php endif; ?>
</div>
