<div id="user_form" class="nav_target">
	<form id="user_form_data" action="./submit.php" method="POST" enctype="multipart/form-data">
	<div id="image_column">
		<input type="file" name="images" id="images" accept="image/*" onchange="preview.call(this);" hidden>
		<div id="new_image" title="Please select an image. Images with 3:4 aspect ratios work best"
			style="background-image: url(<?php echo 'http://darwinsdogs.org/' . $respath . 'users/' . $user['id'] . '.png'; ?>);"
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
	<fieldset>
		<legend>Email Address</legend>
		<input type="email" name="email" required value="<?php echo $user['email']; ?>">
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
	<input style="width: 10em;" type="button" value="Cancel" id="cancel" onclick="window.location='?pg=home';">
	<input style="width: 8em;" type="submit" value="Update Profile" id="submit">
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
function preview() {
	var reader = new FileReader();
	reader.onload = function (e) { document.getElementById("new_image").style.backgroundImage = "url(" + e.target.result + ")"; }
	reader.readAsDataURL(this.files[0]);
}
function sub_load() { if (<?php echo ($user['flags'] & 2 ? 'false' : 'true'); ?>) address_change(); }
</script>
