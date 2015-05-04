<?php

ob_start();


;
require 'functions.php';
require 'openHtml.php';

$parName=$_GET["parName"];

if (isset($_GET["value"]) and $_GET["value"]=="newValue"){
   $value="DD/MM";
} else {
	$user=getUser();
   $value=getParField("value",$parName,$user);
   if (trim($value)=="no scaling"){
   	$value="DD/MM";
   } else {
   	$value=fromNumberToDate($value);
   }
}


//variables that we might use in any case
$wholeDescr   =getParField("description"    ,$parName);


if (isset($_POST) && empty($_POST)){
  echo "<h2>" . "choose a value for " . $parName . "</h2>";
   ?>
   <form  class="buttonRows" action=""   method="post">
   <input type="text" id="calendar" name="value" value="<?php echo $value;?>"/>
   <input class="button"  type="submit"  value="enter"/>	

   <?php   
   setcookie("parName",$parName);
} elseif (isset($_POST) and empty($_POST["WS"]) and (isset($_POST["value"]) || $_POST["value"]=="0")){
	if (isset($_POST["value"]) and empty($_POST["value"]) and ($_POST["value"]!='0')){
		$message="No valid value was inserted, no action was taken";
		$parDescr="";
	}
	if (!isset($_POST["value"]) or empty($_POST["value"]) and $_POST["value"]!="0"){
	   $newValue=$value;
	} else {
	 	$newValue=$_POST["value"];
	 	$valueForDB=fromDateToNumber($newValue);
	 	if ($valueForDB==0){
	 		$message="You did not enter a correct date, thus " . $parName;
	 		$message=$message . " was set to 0, which means that no scaling of the source  
	 		                          function will be applied according to solar distance";
	 	} else {
	 		$message="You entered the date:  " . $newValue .". Thus ". $parName . " will be set to: " . $valueForDB;
	 		$message=$message . " which is the number of days after the beginning of the year.";
	 	}   
	   if (lowerLimit($wholeDescr)!== false and lowerLimit($wholeDescr)<$newValue) {
	   	$startParDescr=">" . trim(lowerLimit($wholeDescr)) . ":";
	   	$lastDescription=true;
	   } else {
	   	$lastDescription=false;
	   	$startParDescr=trim($newValue) . ":";
	   }
	   $noDescription=strpos($wholeDescr,$startParDescr)===false;
	}
	editValue($parName,clean($valueForDB),"not yet validated",$user,'0','0');
	//also in the case the paramater value was not yet set before, there is the entry in the database with the description.
	?>
   <form  class="buttonRows" action="" method="post">
   <?php 
   echo "<br><p class='pHome'>" . $message . "</p>";
   echo "<br>";
   ?>
   <input class="button"  type="submit" name="WS" value="Continue working session"/>
   </form>
   <?php
} else if (isset($_POST) and !empty($_POST["WS"])){
	header("Location: readinput.php"); 
	die();
}

require 'closeHtml.php';
?>$line
