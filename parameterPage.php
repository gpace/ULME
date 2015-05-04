<?php

ob_start();


;
require 'functions.php';
require 'openHtml.php';


##In case like JULDAT the value showed is not correct. Fix it
if (empty($_POST) and isset($_GET) and !empty($_GET)){

   $user=getUser();
   $parName=$_GET['parName'];
   $value=getParField("value",$parName,$user);
 	$message         =getParField("message"         ,$parName,$user);
   $description      =getParField("description"   ,$parName);
   $descriptionLines =explode("\n",$description);
 	$recordId         =getParField("records_id"    ,$parName);
 	$recordName       =getRecordField("name",$recordId);
 	$possibleValues   =getParField("possibleValues",$parName);
  	$set              =getParField("block"         ,$parName);

 	setcookie("parName",$parName);


	if (validParValue($value)){
		if (! is_array($value)){
			echo "<h2>" . $parName . " present value: </h2>";
		  	$buttonText="Change value";
		  	$buttonText1="";
		} else {
			echo "<h2>" . $parName . " present values: </h2>";
			$buttonText="Change last entered value";
			$buttonText1="Remove all values and add a new one";
		}
	  	$linesText="- change the last value that was entered last for this individual parameter<br>";
	  	$linesText=$linesText."- erase the last value that was entered last for this individual parameter<br> ";
	  	$linesText=$linesText."- add a new value to those already entered for this individual parameter <br>  ";
	   if (is_array($value) ){
         echo "<br><div class='pHome'>";
         $time=1;
         foreach ($value as $recordEntry){
         	if (! validParValue($recordEntry)) {continue;}
        	   echo ordinal($time) . " entry of Record " . $recordName . ": <br>";  
        	   if (is_array($recordEntry)){
        	   	$showEntry=implode(", ",$recordEntry);
        	   }
         	if (strlen($showEntry)<50){
    	         echo $showEntry . "<br><br>";
   	      } else echo substr($showEntry,0,45)." ...<br><br>";
   	      $time++;
   	   }
   	   echo "</div><br>";
   	} else {
   		if (! is_array($value)){
   		   echo "<h3>"  . $value . "</h3>";
   		   echo "<br>";
   		}
   	}  
   } else {
 	   echo "<h2>" . " No value was given for " . $parName . "</h2>";
	   $buttonText="Enter a value";
	   $buttonText1="";
	   $linesText="- enter a value for this individual parameter <br>  ";
	}

	?>   
	<form class="buttonRows" action="" method="post">
	<input class="button"  type="submit" name="action" value="<?php echo $buttonText; ?>"/>
	<?php
	if ($buttonText1!=""){
		?>
		<input class="button"  type="submit" name="action" value="<?php echo $buttonText1; ?>"/>
		<br><br>
	   <?php
	}

	if (validParValue($value)){
   	if ($set==0){
			?>
			<input class="button"  type="submit" name="action" value="Erase entry"/>
			<br><br>
			<?php	
   	} else {
   		?>
		   <input class="button"  type="submit" name="action" value="Erase all entries"/>
		   <br><br>
		   <input class="button"  type="submit" name="action" value="Erase last entry"/>
		   <input class="button"  type="submit" name="action" value="Add a new entry"/>
		   <br><br>
		   <?php	
		}
	}


	?>	
	<br><br>
	<input class="button"  type="submit" name="action" value="Edit next parameter"/>	
   <input class="button"  type="submit" name="action" value="Choose another parameter to edit"/>	
	
	</form>
	<br>
   <?php

   echo "<div class='pDescr'> <p class='pDescrText'>";
    if ($message!='' and $message!='OK'){
   	echo "<b> Warning </b> on the last entered value:<br>";
   	echo $message."<br><br>";
   }
   echo "<b> Description: </b><br>";
   $checkIfNewLine=($possibleValues!="no restrictions");
   if($checkIfNewLine){
   	$possibleValues=turnIntoArray($possibleValues);
   }
   foreach ($descriptionLines as  $descriptionLine){
   	$descriptionLine= " " . trim($descriptionLine,'\n');
   	$written=false;
   	if(substr($descriptionLine,0,2)=="--"){
   		$descriptionLine=substr($descriptionLine,2);
   		echo "<pre>" . $descriptionLine . "</pre>";
   		$written=true;
   	}
   	if ($checkIfNewLine){
   		if(strpos($descriptionLine,":")){
   		   $possibleLimit=explode(":",$descriptionLine);
   		   $possibleLimit=trim($possibleLimit[0]);
   		   if (in_array($possibleLimit,$possibleValues) or strpos($possibleLimit,">")){ 
   		      echo "<br><br>";
   		   } 
   		}
   	}
   	$possibleLoweLimitsStr=explode(":",$description);
   	if (!$written){ echo $descriptionLine;}
   }
   echo "</p></div>";
} else	switch($_POST["action"]){
	case "Choose another parameter to edit":
	   header("Location: readinput.php");
	   die();
	   break;
	case "Add a new entry": 
	   header("Location: editParValue.php?value=addValue");
	   die();
	   break;
	case "Change value":
	   header("Location: editParValue.php?value=changeValue");
	   die();
	   break;
	case "Remove all values and add a new one":
	   header("Location: editParValue.php?value=changeAllValues");
	   die();
	   break;
	case "Change last entered value":
	   header("Location: editParValue.php?value=changeLastValue");
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
	case "Erase all entries": 
	   header("Location: editParValue.php?value=eraseAll");
	   die();
	   break;
	case "Erase last entry": 
	   header("Location: editParValue.php?value=eraseLast");
	   die();
	   break;
	case "Edit next parameter": 
	   $parName=$_COOKIE["parName"];
	   $parName=nextPar($parName);
		if ($parName){
		   header("Location: parameterPage.php?parName=$parName");
		   die();
	   } else {
		   header("Location: readinput.php");
		   die();
	   }
	   break;
}
require 'closeHtml.php';
?>

