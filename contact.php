<?php
if ($idpage != '') { foreach ($dogs as $d) { if ($idpage == $d['id']) $dog = $d; } }
else { $dog = Array( "name" => "" ); }
?>
<div id="contact" class="nav_target">
	<form id="compose">
	<h3>Leave us feedback or ask a question</h3>
	<p style="width: 44rem;">Please first check our <a href="http://darwinsdogs.org/?page_id=604" target="_blank">frequently asked questions</a> to see if your question has already been addressed there.
	Additionally, you may find further information in our <a href="http://darwinsdogs.org/?page_id=30" target="_blank">discussion forums</a>.</p>
	<fieldset>
		<legend>What's on your mind?</legend>
		<textarea id="message"><?php if ($argpage == 'retire') echo 'Please retire my dog ', $dog['name'], ' (id=', $dog['id'], ').', PHP_EOL, PHP_EOL; ?></textarea>
	</fieldset>
	<fieldset>
		<legend>Where would you like us to reply?</legend>
		<input type="email" id="reply_to" name="reply_to" value="<?php echo $user['email']; ?>">
	</fieldset>
	<input id="user_info" value="<?php echo $user['first'] . ' ' . $user['last'] . '(id=' . $user['id'] .')'; ?>" hidden>
	<input style="width: 10em;" type="button" value="Cancel" class="button nav_button" id="home">
	<input style="width: 10em;" type="button" value="Submit" class="button" id="submit">
	</form>
	<form id="sent" style="display: none;">
	<h3>Thank you for your comments</h3>
	<fieldset>
	<legend>Your message has been sent</legend>
	<span>Thank you for contacting us.  We will reply shortly.  You can also
	always contact us directly at
	<a href="mailto:info@darwinsdogs.org">info@darwinsdogs.org</a></span>
	</fieldset>
	<fieldset>
	<legend>Your message:</legend>
	<textarea id="sent_message" readonly></textarea>
	</fieldset>
	</form>
</div>
