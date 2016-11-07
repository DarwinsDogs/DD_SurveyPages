<?php
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) die();
if ($idpage != '') { foreach ($dogs as $d) { if ($idpage == $d['id']) $dog = $d; } }
else { header('Location: ' . $dd_surveys); }
?>
<div class="nav_target" id="thanks">

<h3>Thank You!</h3>

<?php echo $user['first'], ' and ', $dog['name']; ?><br/>

<div class="avatar" id="user_avatar" style="background-image: url(<?php echo $dd_surveys . 'res/users/' . $user['image'] . '.png' . $post_img; ?>);"></div>
<div class="avatar" id="dog_avatar" style="background-image: url(<?php echo $dd_surveys . 'res/dogs/' . $dog['image'] . '.png' . $post_img; ?>);"></div>
<br/>

<span>for your contribution<br/>to genetics research!</span><br/>

<div id="home_button" onclick="window.location='?pg=home'"></div><br/>

<span style="font-size: 50%;">Back Home</span>

</div>

