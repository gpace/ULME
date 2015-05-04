<?php



require 'openHtml.php';
?>
<h1>
<span class="ulmeFont"> Present parameter values </span>
</h1>
<hr class="divisor">
<?php
;
require 'functions.php';
$user=getUser();

if (isset($_POST) and !empty($_POST) and $_POST["back"]=="Go back to working session"){
	header("Location: readinput.php");
	die();
}
?>
<br>
<form class="buttonRows" action="" method="post">
<input class="button"  type="submit" name="back" value="Go back to working session"/>
</form>
<br>
<hr class="divisor">
<br>
<br>
<div class="pHome">
<br>
<?php
$parameterDescription=showConfiguration('0', $user,"descriptions");
echo $parameterDescription;
?>
</div>
<?php
require 'closeHtml.php';
?>
