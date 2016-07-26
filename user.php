<div id="user_form" class="nav_target">
	<iframe name="user_form_frame" style="display: none;"></iframe>
	<form id="user_form_image" target="user_form_frame" enctype="multipart/form-data" action="#" method="POST">
		<input type="file" name="user_image" accept="image/*" hidden>
		<input type="text" name="user_str" hidden>
		<input type="text" name="user" hidden>
		<div id="new_image" title="Please select an image file under 4MB.&#10;Images with 3:4 aspect ratios work best"></div>
		<div class="msg">Click to change image</div>
	</form>
	<form>
	<form>
	<h3><?php echo $user['first']; ?>'s profile</h3>
	<fieldset>
		<legend>Name</legend>
		<input type="text" name="first" placeholder="first" required value="<?php echo $user['first']; ?>">
		<input type="text" name="last" placeholder="last" required value="<?php echo $user['last']; ?>">
	</fieldset>
	<fieldset>
		<legend>Contact Numbers</legend>
		<input type="tel" name="phone1" placeholder="Day" required value="<?php echo $user['phoneDay']; ?>">
		<input type="tel" name="phone2" placeholder="Evening (optional)" value="<?php echo $user['phoneEve']; ?>">
	</fieldset>
	<fieldset>
		<legend>Email Address</legend>
		<input type="email" name="user_email" required value="<?php echo $user['email']; ?>">
	</fieldset>
	<fieldset id="mailing_address">
		<legend>Mailing Address</legend>
		<textarea id="input_address" name="user_address" required style="width: 100%;" onkeydown='address_change();'><?php echo $user['address']; ?></textarea><br/>
		<input style="width: 100%; margin-left: 0; display: none;" type="button" value="Validate Address" id="validate_button" onclick='address_validate();'>
		<input type="text" name="validated" value="0" hidden>
		<div id="confirmed" style="width: 100%; display: none;" align="right">Address validated.  Update profile to save.</div>
		<div id="validating" style="width: 100%; display: none;" align="right">Validating.  Please Wait ...</div>
		<div id="validation_container"></div>
	</fieldset>
	<input style="width: 10em;" type="button" value="Cancel" id="cancel" onclick="window.location='?pg=home';">
	<input style="width: 8em;" type="button" value="Update Profile" id="submit" onclick='user_form_submit();'>
	</form>
</div>
<script type="text/javascript">
function address_change() {
	document.getElementById('validation_container').innerHTML = '';
	document.getElementById('validate_button').style.display = "block";
	document.getElementById('confirmed').style.display = "none";
	document.getElementById('validated').value = "0";
	update_height();
}
function address_validate() {
	document.getElementById('validate_button').style.display = "none";
	document.getElementById('validating').style.display = "block";
	var url = "http://darwinsdogs.org/~jmcclure/draft/validate_address.php?" + encodeURIComponent(document.getElementById('input_address').value);
	xmlhttp=new XMLHttpRequest();
	xmlhttp.open("GET", url, false);
	xmlhttp.send();
	document.getElementById('validating').style.display = "none";
	document.getElementById('validation_container').innerHTML = xmlhttp.responseText;
	update_height();
}
function update_address(sel) {
	document.getElementById('input_address').value = sel.value.replace(', ', "\n");
	document.getElementById('validation_container').innerHTML = '';
	document.getElementById('confirmed').style.display = "block";
	document.getElementById('validated').value = "1";
	update_height();
}
</script>
