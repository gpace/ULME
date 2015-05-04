<?php
ob_start();

require 'functions.php';
require 'openHtml.php';
?>
<hr class="divisor">
<h1>
<span class="ulmeFont"> Get the input file for LBLRTM </span>
</h1>
<hr class="divisor">
<br>
<?php

##First let the user downlaod the TAPE5. Then make him choose whether he wants 
##to run LBLRTM, with all the warning involved, or not.
##set runningLBLRTM= 1, the field of the table users that tells whether somebody
##is running LBLRTM. Also increase of 1 the number of times this user has
##run LBLRTM in a day.
##then set runningLBLRTM=0 back again.
##IT's a mess for the moment. For the moment, let it show the TAPE5. Clean up the rest later.


if      (isset($_POST['displayTAPE5']) and ! empty($_POST['displayTAPE5'])){
	header("Location: displayTAPE5.php");
	die();
}elseif (isset($_POST['runTheCode'])   and ! empty($_POST['runTheCode']))  {
	header("Location: displayResults1.php");
	die();
}elseif (isset($_POST['backToWS'])     and ! empty($_POST['backToWS']))    {
	header("Location: readinput.php");
	die();
} else {
	?>
	<br>
	<br>
	<div class="pHome">
	Chose among the options below. Be reminded that, if you launch LBLRTM, for the duration of the process, which, 
	depending on your input, might take up to few minutes, you will prevent other users to do the same. You have a 
	maximum of ten times per day to 
	do that. If another user is launching LBLRTM in this very moment, you will be asked to wait until she has her results.
	Please use this tool 	responsibly.  
	</div>
	<br><br>
   <hr class="divisor">
   <br><br>
	<form class="buttonRows" action="" method="post">
   <input class="button"  type="submit" name="displayTAPE5" value="Simply display the input file for the LBLRTM code"/>
   <br><br>	
   <input class="button"  type="submit" name="runTheCode"   value="Run the LBLRTM code"/>	
   <input class="button"  type="submit" name="backToWS"     value="Back to the working session"/>		
	</form>
	<?php

}

require 'closeHtml.php';
?>
