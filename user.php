<?php if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) die(); ?>
<div id="user" class="nav_target profile">
	<form id="user_data" action="lib/submit.php" method="POST" enctype="multipart/form-data">
	<div id="image_column">
		<input type="file" name="images" id="images" accept="image/*" onchange="preview.call(this);" hidden>
		<div id="new_image" title="Please select an image. Images with 3:4 aspect ratios work best"
			style="background-image: url(<?php cache_check('res/users/' . $user['image'] . '.png'); ?>);"
			onclick="document.getElementById('images').click();"></div>
		<div class="msg">Click to change image.</div>
	</div>
	<h3><?php echo $user['first']; ?>'s profile</h3>
	<input type="hidden" name="type" value="user">
	<input type="hidden" name="id" value="<?php echo $user['id']; ?>">
	<input type="hidden" name="image" value="<?php echo $user['image']; ?>">
	<input type="hidden" name="flags" value="<?php echo $user['flags']; ?>">
	<input type="hidden" name="on_success" value="home">
	<input type="hidden" name="on_fail" value="user">
	<fieldset>
		<legend>Name</legend>
		<input type="text" class="txtfield" name="first" placeholder="first" required value="<?php echo $user['first']; ?>">
		<input type="text" class="txtfield" name="last" placeholder="last" required value="<?php echo $user['last']; ?>">
	</fieldset>
	<fieldset>
		<legend>Contact Numbers</legend>
		<input type="tel" class="txtfield" name="phoneDay" placeholder="Day" required value="<?php echo $user['phoneDay']; ?>">
		<input type="tel" class="txtfield" name="phoneEve" placeholder="Evening (optional)" value="<?php echo $user['phoneEve']; ?>">
	</fieldset>
	<fieldset id="mailing_address">
		<legend>Mailing Address</legend>
		<textarea id="input_address" name="address" required style="width: 100%;" onkeydown='address_change();'><?php echo $user['address']; ?></textarea><br/>
		<input type="hidden" name="address_orig" value="<?php echo $user['address']; ?>">
		<input style="width: 100%; margin-left: 0; display: none;" type="button" value="Validate Address" id="validate_button" onclick='address_validate();'>
		<input type="text" name="validated" id="validated" value="0" hidden>
		<div id="confirmed" style="width: 100%; display: none;" align="right">Address validated.  Update profile to save.</div>
		<div id="validating" style="width: 100%; display: none;" align="right">Validating.  Please Wait ...</div>
		<div id="validation_container"></div>
	</fieldset>
		<input type="checkbox" name="opt_out" value="yes"<?php if ($user['flags'] & 8) echo ' checked'; ?>> Check here to opt out of monthly newsletter emails.
	<fieldset id="opt_out">
	</fieldset>
	<h3>Social profile (Public)</h3>
	<fieldset class="social">
		<legend>Display Name</legend>
		<input type="text" name="nick" maxlength="12" placeholder="Shown on your forum posts" value="<?php echo $user['nick']; ?>">
	</fieldset>
	<fieldset class="social">
		<legend>Profile Title</legend>
		<input type="text" name="tagline" maxlength="100" placeholder="One sentence personal intro" value="<?php echo $user['tagline']; ?>">
	</fieldset>
	<fieldset class="social">
		<legend>Profile Content / Biography</legend>
		<textarea name="bio" maxlength="4096" placeholder="Tell the community about yourself and your dogs"><?php echo $user['bio']; ?></textarea>
	</fieldset class="social">
	<fieldset id="buttons">
		<input style="width: 10em;" type="submit" value="Update Profile" id="submit">
		<input style="width: 10em;" type="button" value="Cancel" id="cancel" onclick="window.location='?pg=home';">
	</fieldset>
	</form>
</div>
<script type="text/javascript">
function address_change() {
	document.getElementById('validation_container').innerHTML = '';
	document.getElementById('validate_button').style.display = 'block';
	document.getElementById('confirmed').style.display = 'none';
	document.getElementById('validated').value = '0';
	update_height();
}
function address_validate() {
	document.getElementById('validate_button').style.display = 'none';
	document.getElementById('validating').style.display = 'block';
	var url = dd_root + 'lib/validate_address.php?' + encodeURIComponent(document.getElementById('input_address').value);
	xmlhttp=new XMLHttpRequest();
	xmlhttp.open('GET', url, false);
	xmlhttp.send();
	document.getElementById('validating').style.display = 'none';
	document.getElementById('validation_container').innerHTML = xmlhttp.responseText;
	update_height();
}
function update_address(sel) {
	document.getElementById('input_address').value = sel.value.replace(', ', "\n");
	document.getElementById('validation_container').innerHTML = '';
	document.getElementById('confirmed').style.display = 'block';
	document.getElementById('validated').value = '1';
	update_height();
}
function preview() {
	if (this.files[0].size > 8192000) { alert('Please select an image under 8MB'); return; }
	var reader = new FileReader();
	reader.onload = function (e) { document.getElementById('new_image').style.backgroundImage = 'url(' + e.target.result + ')'; }
	reader.readAsDataURL(this.files[0]);
}
function sub_load() { if (<?php echo ($user['flags'] & 2 ? 'false' : 'true'); ?>) address_change(); }
</script>
