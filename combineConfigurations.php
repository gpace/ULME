<?php

ob_start();

require 'functions.php';
require 'openHtml.php';

$user=getUser();

if (isset($_POST["produceInputFile"]) and ($_POST["produceInputFile"]=="Produce input file")){
	header("Location: displayResults.php");
	die();
}

if (isset($_POST["confirmOrDiscard"]) and ($_POST["confirmOrDiscard"])=="Back to the working session"){
	header("Location: readinput.php");
   die();
}

if (isset($_POST["confirmOrDiscard"]) and ($_POST["confirmOrDiscard"])=="Back to the parameter configurations"){
	header("Location: editConfigurations.php");
	die();
}

if (isset($_POST["discard"]) and ($_POST["discard"])=="Discard and go back to the working session"){
	header("Location: readinput.php");
	die();
}


if (isset($_POST["confSubmitted"]) and ($_POST["confSubmitted"])=="Combine checked configurations"){
	$configurations=array();
	foreach($_POST as $key=>$value){
		if(strpos($key,"Configuration")!==false){
			$configurations[]=substr($key,13);
		}
	}
	$configurationsStr=implode(", ",$configurations);
	if ($configurationsStr==""){
		header("Location: combineConfigurations.php");
		die();
	}
	setcookie("configurations",$configurationsStr);
	?>
	<br>
	<br>
	<div class="pHome">
   By clicking on of the buttons below, you will choose whether to produce the input for LBLRTM, go back to the initial
   page of the working session, or view again the configurations. In the first case, you will eventually choose whether 
   to run the LBLRTM code on your machine or on this platform.  
   </div>
	<br><br>
   <hr class="divisor">
   <br><br>
	<form class="buttonRows" action="" method="post">
   <input class="button"  type="submit" name="produceInputFile"       value="Produce input file"/>	
   <input class="button"  type="submit" name="confirmOrDiscard" value="Back to the working session"/>	
   <br><br>
   <input class="button"  type="submit" name="confirmOrDiscard" value="Back to the parameter configurations"/>	
	</form>
	<br>
   <hr class="divisor">
   <br><br>
	<?php
} else {

	?>
	<h1>
	<span class="ulmeFont">  Combine configurations </span>
	</h1>
	<hr class="divisor">
	<br><br>
	<div class="pHome">
	<form class="buttonRows" action="" method="post">
	Check the boxes relative to the configurations to be saved, if you saved them in the proper order.
	If they were not saved in the proper order, do it before using this page.<br><br>
	<?php
	$numConfigurations=lastConfiguration($user,"do not save");
	for ($configuration=1;$configuration<=$numConfigurations;$configuration++){
	   ?>
	   <input  type="checkBox" name="Configuration<?php echo $configuration;?>"  value="1"> <?php echo $configuration; ?>   
	   <?php
	}
	?>
	<br><br>
	<input class="button" type="submit" name="confSubmitted" value="Combine checked configurations">
	<input class="button" type="submit" name="discard" value="Discard and go back to the working session">
	</form>
	</div>
	<?php
}
require 'closeHtml.php';
?>

