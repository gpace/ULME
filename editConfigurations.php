<?php

ob_start();

;
require 'functions.php';
require 'openHtml.php';

$user=getUser();

if (isset($_POST["confirmOrDiscard"]) and strpos($_POST["confirmOrDiscard"],"Delete configuration")!==false){
	$chosenConfNumber=substr($_POST["confirmOrDiscard"],21,2);
   $conf=intval(trim($chosenConfNumber));
   ##Add a form to check if the user really wants to delete the data.
	deleteConfiguration($user,$conf);
	header("Location: editConfigurations.php");
	die();
} elseif (isset($_POST["confirmOrDiscard"]) and strpos($_POST["confirmOrDiscard"],"Use configuration")!==false){
   $chosenConfNumber=substr($_POST["confirmOrDiscard"],18,2);
   $conf=trim($chosenConfNumber);
   setcookie("configurations",$conf);
 	header("Location: displayResults.php");
	die();
} elseif (isset($_POST["confirmOrDiscard"]) and strpos($_POST["confirmOrDiscard"],"working session")!==false){
	header("Location: readinput.php"); 
	die();
} elseif (isset($_POST["confirmOrDiscard"]) and strpos($_POST["confirmOrDiscard"],"Restore configuration ")!==false){
   $chosenConfNumber=substr($_POST["confirmOrDiscard"],22,2);
   $configuration=intval(trim($chosenConfNumber));
	restoreConfiguration($configuration,$user);
	header("Location: readinput.php"); 
	die();
} elseif (isset($_POST["confirmOrDiscard"]) and strpos($_POST["confirmOrDiscard"],"View parameter values of configuration ")!==false){
   $chosenConfNumber=substr($_POST["confirmOrDiscard"],39,2);
   $configuration=intval(trim($chosenConfNumber));
	$showConfiguration=showConfiguration($configuration,$user);
	?>
	<h1>
   <span class="ulmeFont">  Configuration number <?php echo strval($configuration); ?> </span>
   </h1>
   <hr class="divisor">
   <br><br>
   <div class="pHome">
   <?php echo $showConfiguration; ?>
   </div>
   <br><br>
	<form class="buttonRows" action="" method="post">
	<input class="button"  type="submit" name="confirmOrDiscard" value="Use configuration <?php echo $chosenConfNumber; ?> to run LBLRTM" />
	<input class="button"  type="submit" name="confirmOrDiscard" value="Restore configuration <?php echo $chosenConfNumber; ?> and continue editing" />
   <br><br>                                                            
   <input class="button"  type="submit" name="confirmOrDiscard" value="Delete configuration <?php echo $chosenConfNumber; ?>"/>	
   <br><br>                                                            
   <input class="button"  type="submit" name="confirmOrDiscard" value="Go back to the configuration outline"/>	
   <input class="button"  type="submit" name="confirmOrDiscard" value="Go back to the working session"/>	
	</form>
	<?php
	require 'closeHtml.php';
	die();
} else {
	?>
   <h1>
   <span class="ulmeFont"> Edit Configurations </span>
   </h1>
   <hr class="divisor">
   <br>
   <?php


	if (isset($_POST["chooseConfs"]) and $_POST["chooseConfs"]=="Go back to the working session"){
		header("Location: readinput.php"); 
		die();
	}
	
	if (isset($_POST["chooseConfs"]) and $_POST["chooseConfs"]=="Combine more configurations to run LBLRTM"){
		header("Location: combineConfigurations.php"); 
		die();
	}
	

   $numConf=	lastConfiguration($user,"do not save");
	if ($numConf==1){
		$suffix="";
		$button=" it ";
	} else {
	   $suffix="s";
	   $button=" one of them ";
	}
	if ($numConf==0){
		$quantity="no";
		$message2="";
	} else {
		$quantity=strval($numConf);
		$message2="Click on".$button."to restore them or to produce the input for LBLRTM and then run it.<br>";
	}
	$message="You have saved ".$quantity." configuration".$suffix."<br>";
	$message=$message. $message2."<br> If you want to combine more configurations or go back to the parameter page, <br>";
	$message=$message." click on either button below";
	   
	if (isset($_POST["chooseConfs"]) and strpos($_POST["chooseConfs"],"configuration")!==false){
	   $chosenConfNumber=substr($_POST["chooseConfs"],14);
	   ?>
	   <form class="buttonRows" action="" method="post">
		<input class="button"  type="submit" name="confirmOrDiscard" value="Use configuration <?php echo $chosenConfNumber; ?> to run LBLRTM" />
		<input class="button"  type="submit" name="confirmOrDiscard" value="Restore configuration <?php echo $chosenConfNumber; ?> and continue editing" />
	   <br><br>                                                            
		<input class="button"  type="submit" name="confirmOrDiscard" value="View parameter values of configuration <?php echo $chosenConfNumber; ?>" />
      <br><br>
	   <input class="button"  type="submit" name="confirmOrDiscard" value="Go back to the configuration outline"/>	
	   <input class="button"  type="submit" name="confirmOrDiscard" value="Go back to the working session"/>	
		</form>
		<br>
	   <hr class="divisor">
	   <br><br>

	   <?php
	} else {
	
	
	
	
	
	   ?> 
	
	   <form class="buttonRows" action="" name="chooseConfs" method="post">
	   <?php
	   $numConfigurations=lastConfiguration($user,"do not save");
	   for ($configuration=1;$configuration<=$numConfigurations;$configuration++){
		   ?>
		   <input class="button"  type="submit" name="chooseConfs" value="<?php echo 'configuration '.$configuration ?>" />
		   <?php
		   if ($configuration % 3==0 and $configuration!=$numConfigurations) {
		      echo str_repeat("<br>",2);
		   }
	   }
	   ?>
	   <br><br>
	   <div class='showRes'>
	   <?php echo $message;?>
	  	</div>
	  	<br>
	  	<br>
	   <input class="button"  type="submit" name="chooseConfs" value="Combine more configurations to run LBLRTM"/>
	   <input class="button"  type="submit" name="chooseConfs" value="Go back to the working session"/>
	   </form>
	   <br>
	   <hr class="divisor">
	   <br>
	<?php
	}
}
require 'closeHtml.php';
?>
