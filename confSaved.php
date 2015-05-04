<?php

ob_start();



;
require 'functions.php';
require 'openHtml.php';
$user=getUser();

if(isset($_POST["goOn"]) and $_POST["goOn"]=="Back to working session"){
	header("Location: readinput.php");
	die();
} else {
	##you can easily check here whether the configuration was already saved.
	##Not urgent
	$number=lastConfiguration($user,"save");
	?>
	<hr class="divisor">
	<br>
	<p class="pHome"> 
	The present configuration of parameters was saved. It is the 
	<?php echo ordinal($number);?> 
	 configuration
	</p>
	<br>
	<hr class="divisor">
	<br>
	<?php
} 

?>
<br>
<hr class="divisor">
<br>
<form class="buttonRows" action="" method="post">
<input class="button"  type="submit" name="goOn" value="Back to working session"/>	
</form>
<br>
<hr class="divisor">
<br>


<?php
require 'closeHtml.php';
?>
