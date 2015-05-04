<?php

ob_start();


;
require 'functions.php';
require 'openHtml.php';

//rifare da capo!!!



if (empty($_POST) and isset($_GET) and !empty($_GET)){

   $user=getUser();
   $parName=$_GET['parName'];
   $suffix=substr($parName,3);
   $v1="V1" . $suffix;
   $v2="V2" . $suffix;
   die($v1 . "   " . $v2);
   $value=getParField("value",$parName,$user);
	$message=getParField("message",$parName,$user);
?> 
	<form class="buttonRows" action="" method="post">
	<?php 
	if (validParValue($value)){	
	   echo "<h2>" . $parName . " actual value: " . $value . "</h2>";
	   $buttonText="Change value";
	} else {
	   echo "<h2>" . " No value was given for " . $parName . "</h2>";
	   $buttonText="Enter a value";
	}
	?>
	<input class="button" type="submit" name="action" value="<?php echo $buttonText; ?>"/>
	<?php
	setcookie("parName",$parName);
	if (validParValue($value)){
		?>
		<input class="button" type="submit" name="action" value="Erase entry"/>	
		<?php
	}
	?>	
   <input class="button" type="submit" name="action" value="Edit another parameter"/>	
	
	</form>
	<br>
	<h3>Description</h3>
	<pre>
   <?php

 
	$parDimension =getParField("dimension"      ,$v1);
	$recordId     =getParField("records_id"     ,$v1);
	$set          =getParField("block"          ,$v1);
	$parDimension =getParField("dimension",      $v1);
	$description  =getParField("description",      $v1);
	$lineDimension=getRecordField("record_dimension",$recordId);
	$recordName   =getRecordField("name",$recordId);
	$repetition   =getLastRepetition($v1,$user);
	
	
	$numericalParDimension =numericalValue($parDimension);
	$numericalLineDimension=numericalValue($lineDimension);

 
 
 
 
   echo "<h3>";
  	echo "Enter the value of " . $parName . " " . $parDimension . "times";

   if (trim($message)!="OK"){
   	echo "<br>";
   	echo $message;
   }

  	echo ".</h3>";

   echo $description;
   ?>
   </pre>
   <?php
} elseif(isset($_POST["action"])) switch($_POST["action"]){
	case "Edit another parameter":
	   header("Location: readinput.php");
	   die();
	   break;
	case "Change value": 
	   header("Location: editParValue.php?value=changeValue");
	   die();
	   break;
	case "Enter a value": 
	   header("Location: editParValue.php?value=newValue");
	   die();
	   break;
	case "Erase entry": 
	   header("Location: editParValue.php?value=erase");
	   die();
	   break;
}
require 'closeHtml.php';
?>

