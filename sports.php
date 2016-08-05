<?php
if ($idpage != '') { foreach ($dogs as $d) { if ($idpage == $d['id']) $dog = $d; } }
else { $dog = Array( "name" => "" ); }
?>
<div id="sports">

<div id="intro">
<h3>Sports</h3>
Some text here
</div>


<div class="sport"><div class="img"></div><div class="name"></div></div>
<div class="sport"><div class="img"></div><div class="name"></div></div>
<div class="sport"><div class="img"></div><div class="name"></div></div>
<div class="sport"><div class="img"></div><div class="name"></div></div>
<div class="sport"><div class="img"></div><div class="name"></div></div>
<div class="sport"><div class="img"></div><div class="name"></div></div>
<div class="sport"><div class="img"></div><div class="name"></div></div>
<div class="sport"><div class="img"></div><div class="name"></div></div>
<div class="sport"><div class="img"></div><div class="name"></div></div>


</div>
