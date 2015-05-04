<?php

require 'openHtml.php';
?>
<h1>
<span class="ulmeFont"> Grafical summary </span>
</h1>
<hr class="divisor">
<br>
<?php

if (isset($_POST) and !empty($_POST) and $_POST["back"]=="Working session"){
	header("Location: readinput.php");
	die();
} elseif (isset($_POST) and !empty($_POST) and $_POST["back"]=="Scientific context"){
	header("Location: scientificContext.php");
	die();
} elseif (isset($_POST) and !empty($_POST) and $_POST["back"]=="Home"){
	header("Location: readMe.php");
	die();
}
?>
<form class="buttonRows" action="" method="post">
<input class="button" type="submit" name="back" value="Home"/>
<input class="button" type="submit" name="back" value="Working session"/>
<input class="button" type="submit" name="back" value="Scientific context"/>
</form>
<br>
<hr class="divisor">
<br>
<div class="pHome">
<img class="centralImage" src="images/mainGraph.jpg" alt="code scheme">
</div>
<br><br><br><br>

<?php
require 'closeHtml.php';
?>
