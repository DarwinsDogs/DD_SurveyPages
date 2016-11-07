<?php
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) die();
if ($idpage != '') { $dog = $dogs[0]; foreach ($dogs as $d) { if ($idpage == $d['id']) $dog = $d; } }
else { $dog = Array( 'id' => '0', 'name' => '', 'sex' => '', 'neutered' => '', 'age' => '', 'birthday' => '', 'breed1' => '', 'breed2' => '', 'breed3' => '', 'purebred' => '', 'image' => '0', 'dom_entry' => '', 'quirk' => '', 'fun_story' => '', 'does_best' => '', 'summary' => ''); }
if (strlen($dog['image']) == 0) $dog['image'] = '0';
?>
<div id="dog" class="nav_target profile">
	<form id="dog_data" action="lib/submit.php" method="POST" enctype="multipart/form-data">
	<div id="image_column">
		<input type="file" name="images" id="images" accept="image/*" onchange="preview.call(this);" hidden>
		<div id="new_image" title="Please select an image. Images with 3:4 aspect ratios work best"
			style="background-image: url(<?php echo $dd_surveys, 'res/dogs/' . $dog['image'] . '.png'; ?>);"
			onclick="document.getElementById('images').click();"></div>
		<div class="msg">Click to change image.</div>
	</div>
	<input type="hidden" name="type" value="dog">
	<input type="hidden" name="image" value="<?php echo $dog['image']; ?>">
	<input type="hidden" name="owner" value="<?php echo $user['id']; ?>">
	<input type="hidden" name="on_success" value="home">
	<input type="hidden" name="on_fail" value="dog&id=<?php echo $idpage; ?>">
	<input type="hidden" name="id" value="<?php echo $dog['id']; ?>">
<?php
if ($idpage == '') echo '<h3>Oh Boy, a New Dog!</h3>';
else echo '<h3>' . $dog['name'] . '\'s Profile</h3>';
?>
	<fieldset>
		<legend>Please tell us this dog's name</legend>
		<input type="text" name="name" id="new_dog_name" value="<?php echo $dog['name']; ?>" onchange="update()">
	</fieldset>
	<fieldset>
		<legend>Is <span class="dog_name">the dog</span> male or female</legend>
		<input type="radio" name="sex" value="male" <?php echo ($dog['sex'] == 'male' ? 'checked' : ''); ?>> Male
		<input type="radio" name="sex" value="female" <?php echo ($dog['sex'] == 'female' ? 'checked' : ''); ?>> Female
	</fieldset>
	<fieldset>
		<legend>Has <span class="dog_name">the dog</span> been <span id="spay_neuter">spayed or neutered</span>?</legend>
		<input type="radio" name="neutered" value="yes" <?php echo ($dog['neutered'] == 'yes' ? 'checked' : ''); ?>> Yes
		<input type="radio" name="neutered" value="yes" <?php echo ($dog['neutered'] == 'no' ? 'checked' : ''); ?>> No
	</fieldset>
	<fieldset>
		<legend>What is <span class="dog_name">the dog</span>'s approximate age?</legend>
		<input type="text" name="age" value="<?php echo $dog['age']; ?>">
	</fieldset>
	<fieldset>
		<legend>If known, enter <span class="dog_name">the dog</span>'s date of birth or birth year</legend>
		<input type="text" name="birthday" value="<?php echo $dog['birthday']; ?>">
	</fieldset>
	<fieldset id="breed">
	<legend>If known, please enter <span class="dog_name">the dog</span>'s breed, or mix of breeds (up to three)</legend>
		<input class="breed" type="text" name="breed1" value="<?php echo $dog['breed1']; ?>"><br/>
		<input class="breed" type="text" name="breed2" value="<?php echo $dog['breed2']; ?>"><br/>
		<input class="breed" type="text" name="breed3" value="<?php echo $dog['breed3']; ?>"><br/>
		<input type="checkbox" name="purebred" value="yes"<?php if ($dog['purebred'] == 'yes') echo ' checked'; ?>> Check here if <span class="dog_name">the dog</span> is a registered purebred.<br/>
		<a name="dom_entry"></a>
<?php if ($dog['id'] > 0): ?>
		<input type="checkbox" name="dom_entry" value="yes" onclick="dog_month();" <?php if ($dog['dom_entry'] == 'yes') echo 'checked'; ?>>
		<span <?php if (isset($_GET['dom_entry'])) echo 'style="color: #028; font-weight: 700;" '; ?>>
		Check here to enter <span class="dog_name">your dog</span> into the Dog of The Month.
		</span>
<?php endif; ?>
	</fieldset>
<div id="dog_month" <?php if ($dog['dom_entry'] != 'yes') echo ' class="hidden"'; ?>>
	<h3>Enter <span class="dog_name">your dog</span> into "Dog of the Month"</h3>
	<p class="warn">Entering your dog in "Dog of the Month" implies consent for
	us to share your dog's name, photo, breed, age, and the information provided
	below publicly on our website, social media sites, and in print.  Note that
	you must have a validated postal mailing address in your own profile in order
	to be selected for dog of the month (We need to know where to send the
	prize!)</p>
	<fieldset>
		<legend>One sentence introduction for <span class="dog_name">your dog</span>:</legend>
		<input type="text" name="summary" value="<?php echo $dog['summary']; ?>"
			placeholder="e.g., <?php echo ($dog['age'] ? $dog['age'] : '4'); ?> year old <?php echo ($dog['breed1'] ? $dog['breed1'] : 'chocolate lab'); ?> who loves to swim in the water and play all day!"
			maxlength="100">
	</fieldset>
	<fieldset>
		<legend>Share a fun story about <span class="dog_name">your dog</span>:</legend>
		<textarea name="fun_story" maxlength="2048"><?php echo $dog['fun_story']; ?></textarea>
	</fieldset>
	<fieldset>
		<legend>One interesting quirk about <span class="dog_name">your dog</span>:</legend>
		<input type="text" name="quirk" value="<?php echo $dog['quirk']; ?>" maxlength="250"><br/>
	</fieldset>
	<fieldset>
		<legend>Something that <span class="dog_name">your dog</span> does best:</legend>
		<input type="text" name="does_best" value="<?php echo $dog['does_best']; ?>" maxlength="250"><br/>
	</fieldset>
</div>
	</fieldset>
	<div id="consent">
	<fieldset id="buttons">
<?php if ($dog['id'] == 0): ?>
	<p style="margin-right: 1em;">I, <b><?php echo $user['first'] . ' ' . $user['last']; ?></b>, have
	read the <a href="<?php echo $dd_surveys; ?>/consent.pdf" target="_blank">Consent Form</a>
	and the nature of the research has been made clear to me. I have been given a copy of this form.
	I have had an opportunity to ask questions about the project and understand that I can ask
	questions at any time.  I agree to allow my dog, <b><span class="dog_name">________</span></b>,
	to participate in this study.  Furthermore, I agree to donate to Dr. Elinor Karlsson, of the
	University of Massachusetts Medical School, saliva, blood, hair and/or fecal samples obtained
	from my dog. I understand that these specimens will be used to study canine genetics and health.
	I understand that, if I chose to collect samples from my dog, I do so at my own risk and that the
	principal investigator and the University of Massachusetts Medical School are not responsible for
	any injuries or expenses incurred as a result.  I also understand that by signing this Consent
	Form, I give up all future claims to these specimens and any experimental results that may be
	derived from their investigational use. I agree to be contacted by the Principal Investigator,
	or her agent, periodically to inquire about my dog through questionnaires posted at the
	www.darwinsdogs.org website.  If I have any questions or problems that I feel are related to
	the study, I can contact the Principal Investigator whose name is on this
	form at <a href="mailto:info@darwinsdogs.org">info@darwinsdogs.org</a>.</p>
	<input style="width: 10em;" type="button" value="Cancel" id="cancel" onclick="window.location='?pg=home';">
	<input style="width: 10em;" type="submit" value="Add Dog" id="submit">
<?php else: ?>
	<input style="width: 10em;" type="button" value="Cancel" id="cancel" onclick="window.location='?pg=home';">
	<input style="width: 10em;" type="submit" value="Save Updates">
	<a href="?pg=contact&arg=retire&id=<?php echo $dog['id']; ?>" id="retire"
		title="Please use the Contact Us form to let us know if you would like to remove a dog">Retire <?php echo $dog['name']; ?></a>
<?php endif; ?>
	</fieldset>
	</div>
	</form>
</div>
<script type="text/javascript">
function update() {
	var n = document.getElementsByClassName('dog_name');
	var name = document.getElementById("new_dog_name").value
	for (i = 0; i < n.length; i++) { n[i].innerHTML = name; }
}
function preview() {
	var reader = new FileReader();
	reader.onload = function (e) { document.getElementById("new_image").style.backgroundImage = "url(" + e.target.result + ")"; }
	reader.readAsDataURL(this.files[0]);
}
function dog_month() {
	document.getElementById('dog_month').classList.toggle('hidden');
	update_height();
}
function sub_load() { if (<?php echo ($idpage != '' ? 'true' : 'false'); ?>) update(); }
</script>
