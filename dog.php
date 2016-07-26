<?php
if ($idpage != '') { foreach ($dogs as $d) { if ($idpage == $d['id']) $dog = $d; } }
else { $dog = Array( "name" => "" ); }
?>
<div id="dog_form" class="nav_target">
	<iframe name="dog_form_frame" style="display: none;"></iframe>
	<form id="dog_form_image" target="dog_form_frame" enctype="multipart/form-data" action="" method="POST">
		<input type="file" name="dog_image" accept="image/*" hidden>
		<input type="text" name="dog_str" hidden>
		<input type="text" name="dog" hidden>
		<div id="new_image" title="Please select an image file under 4MB.&#10;Images with 3:4 aspect ratios work best"></div>
		<div class="msg">Click to change image.  If you upload images for more
		than one dog in the same session, please refresh your browser page for
		changes to take effect after updating the profile.</div>
	</form>
	<form>
<?php
if ($idpage == '') echo '<h3>Oh Boy, a New Dog!</h3>';
else echo '<h3><span class="dog_name">' . $dog['name'] . '</span>\'s Profile</h3>';
?>
	<fieldset>
		<legend>Please tell us this dog's name</legend>
		<input type="text" name="dog_name" id="new_dog_name" value="<?php echo $dog['name']; ?>" onchange="update()">
	</fieldset>
	<fieldset>
		<legend>Is <span class="dog_name">the dog</span> male or female</legend>
		<input type="radio" name="dog_sex" value="male" <?php echo ($dog['sex'] == 'male' ? 'checked' : ''); ?>> Male
		<input type="radio" name="dog_sex" value="female" <?php echo ($dog['sex'] == 'female' ? 'checked' : ''); ?>> Female
	</fieldset>
	<fieldset>
		<legend>Has <span class="dog_name">the dog</span> been <span id="spay_neuter">spayed or neutered</span>?</legend>
		<input type="radio" name="dog_neutered" value="yes" <?php echo ($dog['neutered'] == 'yes' ? 'checked' : ''); ?>> Yes
		<input type="radio" name="dog_neutered" value="yes" <?php echo ($dog['neutered'] == 'no' ? 'checked' : ''); ?>> No
	</fieldset>
	<fieldset>
		<legend>What is <span class="dog_name">the dog</span>'s approximate age?</legend>
		<input type="text" name="dog_age" value="<?php echo $dog['age']; ?>">
	</fieldset>
	<fieldset>
		<legend>If known, enter <span class="dog_name">the dog</span>'s date of birth or birth year</legend>
		<input type="text" name="dog_birthday" value="<?php echo $dog['birthday']; ?>">
	</fieldset>
	<fieldset id="breed">
	<legend>If known, please enter <span class="dog_name">the dog</span>'s breed, or mix of breeds (up to three)</legend>
		<input class="breed" type="text" name="dog_breed1" value="<?php echo $dog['breed1']; ?>"><br/>
		<input class="breed" type="text" name="dog_breed2" value="<?php echo $dog['breed2']; ?>"><br/>
		<input class="breed" type="text" name="dog_breed3" value="<?php echo $dog['breed3']; ?>"><br/>
		<input type="checkbox" name="dog_purebred" value="<?php echo $dog['purebred']; ?>"> Check here if <span class="dog_name">the dog</span> is a registered purebred.
	</fieldset>
	<div id="consent">
<?php if ($idpage == ''): ?>
	<p style="margin-right: 1em;">I, <b><?php echo $user['first'] . ' ' . $user['last']; ?></b>, have
	read the <a href="http://members.darwinsdogs.org/consent.pdf" target="_blank">Consent Form</a>
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
	<input style="width: 10em;" type="button" value="Add Dog" id="submit">
<?php else: ?>
	<input style="width: 10em;" type="button" value="Cancel" id="cancel" onclick="window.location='?pg=home';">
	<input style="width: 10em;" type="button" value="Save Updates" id="submit">
	<a href="?pg=contact&arg=retire&id=<?php echo $dog['id']; ?>" id="retire" title="Please use the Contact Us form to let us know if you would like to remove a dog">Retire <span class="dog_name"><?php echo $dog['name']; ?></span></a>
<?php endif; ?>
	</div>
	</form>
</div>
<script type="text/javascript">
update = function() {
	var n = document.getElementsByClassName('dog_name');
	var name = document.getElementById("new_dog_name").value
	for (i = 0; i < n.length; i++) { n[i].innerHTML = name; }
}
<?php if ($idpage != '') echo 'window.onload = update;'; ?>
</script>
