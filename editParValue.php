<?php

ob_start();


;
require 'functions.php';
require 'openHtml.php';
$user=getUser();
?>
<h1>
<span class="ulmeFont"> Edit parameter value </span>
</h1>
<hr class="divisor">
<?php
$parName=$_COOKIE["parName"];

if (isset($_POST["Home"]) and $_POST["Home"]=="Choose another parameter to edit"){
	   header("Location: readinput.php"); 
	   die();
} elseif (isset($_POST["Home"]) and $_POST["Home"]=="Edit next parameter"){
   	$parName=nextPar($parName);
   	if ($parName===false){
		   header("Location: readinput.php"); 
		   die();   		
   	}
   	setcookie("parName",$parName);
	   header("Location: parameterPage.php?parName=$parName"); 
	   die();
} elseif (isset($_POST["Home"]) and $_POST["Home"]=="Try to edit this parameter once again"){
	   header("Location: parameterPage.php?parName=$parName"); 
	   die();
}


if (isset($_COOKIE["requiredParName"])){
	$requiredParName=$_COOKIE["requiredParName"];
}

if (isset($_POST["homeOrPar"]) and $_POST["homeOrPar"]=="Go home first"){
	   header("Location: readinput.php"); 
	   die();
}

if (isset($_POST["homeOrPar"]) and $_POST["homeOrPar"]=="Do it now"){
   header("Location: parameterPage.php?parName=$requiredParName");
   die();
}




//variables that we might use in any case
$possValString=getParField("possibleValues" ,$parName);
$wholeDescr   =getParField("description"    ,$parName);
$parDimension =getParField("dimension"      ,$parName);
$recordId     =getParField("records_id"     ,$parName);
$set          =getParField("block"          ,$parName);
$parDimension =getParField("dimension",      $parName);
$lineDimension=getRecordField("record_dimension",$recordId);
$recordName   =getRecordField("name",$recordId);
$repetition   =getLastRepetition($parName,$user);
$parDescr="";

$numericalParDimension =numericalValue($parDimension);
$numericalLineDimension=numericalValue($lineDimension);

$format=parameterFormat($parName);

if (isset($_GET["value"])){
   $entryType=$_GET["value"];
   setcookie("entryType",$entryType);
} else{
	unset($_COOKIE["entryType"]);
	$entryType="";
}

if (isset($_GET["value"]) and $_GET["value"]=="edit" or isset($_GET["noRedirect"])){
	$functionTR="editParValue";
} else {
	$functionTR=getParField("function",$parName);
}


if ($functionTR !== "editParValue"  and strpos($entryType,"erase")===false){
	header("Location: " . $functionTR . ".php?parName=" . $parName ."&value=" . $entryType);
	die();
}


$homeOrPar=false;
$failedPars1=failedPars($parDimension);
if ($failedPars1){
	$firstFailedPar=$failedPars1[0];
	$failedExpression=$parDimension;
	$homeOrPar=true;
}

$failedPars2=failedPars($lineDimension);
if ($failedPars2){
	$homeOrPar=true;
	if (! $failedPars1){
		$firstFailedPar=$failedPars2[0];
   	$failedExpression=$lineDimension;
	} else $failedExpression=$failedExpression." and ".$parDimension;
}
if($homeOrPar){
	echo "<br><h2 class='pHome'>  $failedExpression needs to be known.<br>";
	echo " You should enter a value for " . $firstFailedPar . " before editing " . $parName; 
	echo "</h2> <br> <hr class='divisor'> <br><br>"; 
	setcookie("requiredParName",$firstFailedPar);
   ?> 
	<form class="buttonRows" action="" method="post">
	<input class="button"  type="submit" name="homeOrPar" value="Do it now"/>
   <input class="button"  type="submit" name="homeOrPar" value="Go home first"/>	
	</form>
	<br>
   <?php
} else {

   if (isset($_POST) && (empty($_POST) && strpos($entryType,"erase")===false)) {   	
 
   	if ($numericalLineDimension==1 and $numericalParDimension==1){
   		?>
         <h2> choose a value for <?php echo  $parName; ?> </h2>
         <?php
      } else {
   		?>
         <h2> choose a set of values for <?php echo  $parName; ?> </h2>
         <?php
      }

      echo '<form  class="buttonRows" action=""   method="post">';
      for($recordEntry=0;$recordEntry<$numericalLineDimension;$recordEntry++) {
      	if ($numericalLineDimension>1){
      		echo "<br><hr class='divisor'>";
      		echo ordinal($recordEntry+1) . " entry of the Record: <br>";
      	}
      	for($listEntry=0;$listEntry<$numericalParDimension;$listEntry++){
      		$postKey="value_" . $recordEntry . '_' . $listEntry;
            if (trim($possValString)=="no restrictions"){
               echo "<input type='text' name='$postKey' value='' />";
            } else {
      	      $possibleValues=turnIntoArray($possValString);
   	         echo '<select name="'.$postKey.'" id="value">';
   	         foreach($possibleValues as $val){
   	            echo '<option value=' . $val . '> ' .  $val .  ' </option>';
   	         }
   	         echo "</select>";
            }
         }
         echo "<br>";
      }
      if ($numericalLineDimension>1){
         echo "<br><hr class='divisor'>";
      }
   
      echo '<br><br><INPUT class="button"  type="submit" value="enter" />';
      echo "</form>";
   
   } elseif (isset($_POST) and empty($_POST["Home"]) and (enteredValues($_POST,$format) || strpos($entryType,"erase")!==false)){
	   if (missingValues($_POST,$format)){
		   $message="<h2> There is at least one missing value, no action was taken </h2>";
		   $parDescr="";
   		?>
	      <h2> There is at least one missing value, no action was taken </h2>
	      <hr class="divisor"><br>
  		         <form  class="buttonRows" action="" method="post">
         <input class="button"  type="submit" name="Home" value="Edit next parameter"/>
         <input class="button"  type="submit" name="Home" value="Try to edit this parameter once again"/>
         <br><br>
         <input class="button"  type="submit" name="Home" value="Choose another parameter to edit"/>
         </form>
	      <?php
	      require 'closeHtml.php';
	      die();
	   } else {
	   	switch($entryType){
	   		case "changeValue":
   	   	   if ($repetition>1){
   	   		   $repetition=$repetition-1; 
   	   	   } elseif($set!=0){
   	   	   	$repetition=1;
   	   	   }
   	   	   break;
   	   	case "addValue":
   	   	   $repetition++;
   	   	   break;
   	   	case "newValue":
   	   	   if ($set!='0'){
   	   	   	$repetition=1;
   	   	   }
   	   	   break;   	   	   
   	   	case "changeAllValues":
   	   	   resetParameter($parName,$user);
   	   	   if ($set!='0'){
   	   	   	$repetition=1;
   	   	   }
   	   	   break;
   	   	//In the changeLastValue case, nothing has to be done.
   	   	case "erase":
		   		resetParameter($parName,$user);
		   		?>
			      <h2> The value of the parameter  <?php echo $parName; ?> has been removed </h2>
			      <hr class="divisor"><br>
  		         <form  class="buttonRows" action="" method="post">
		         <input class="button"  type="submit" name="Home" value="Edit next parameter"/>
		         <input class="button"  type="submit" name="Home" value="Choose another parameter to edit"/>
		         </form>
			      <?php
			      require 'closeHtml.php';
			      die();
	   	   	break;
   	   	case "eraseAll":
	       		resetParameter($parName,$user);
 	       		?>
		         <h2> All entries of the parameter  <?php echo $parName; ?>  have been removed </h2>
		         <hr class="divisor"><br>
		         <form  class="buttonRows" action="" method="post">
		         <input class="button"  type="submit" name="Home" value="Edit next parameter"/>
		         <input class="button"  type="submit" name="Home" value="Choose another parameter to edit"/>
		         </form>
		         <?php
		         require 'closeHtml.php';
		         die();
   	   	   break;
   	   	case "eraseLast":
	       		deleteLastValue($parName,$user);
	       		?>
		         <h2> The value of the parameter  <?php echo $parName; ?>  entered last, has been removed </h2>
		         <hr class="divisor"><br>
		         <form  class="buttonRows" action="" method="post">
		         <input class="button"  type="submit" name="Home" value="Edit next parameter"/>
		         <input class="button"  type="submit" name="Home" value="Choose another parameter to edit"/>
		         </form>
		         <?php
		         require 'closeHtml.php';
		         die();
   	   	   break;
   	   }
	   	if (strpos($entryType,"erase")===false) {
		 	   $newValue=extractValue($_POST,$format);
		      if ($numericalLineDimension>1 and is_array($newValue)){
	            foreach($newValue as $thisRepetitionRow=>$entry){     
	              //die($parName." - ".$entry." - ".$user." - ".strval($repetition)." - ".strval($thisRepetitionRow)); 	
		      	  editValue($parName,$entry,"not yet validated",$user,$repetition,$thisRepetitionRow);
		      	}
	            $message= "<br><div class='pHome'>";
	            $time=1;
	            foreach ($newValue as $recordEntryValue){
	         	   if (trim($recordEntryValue)!=""){
	         	      $message=$message .  ordinal($time) . " entry of " . $recordName . ", ". $parName." set to: <br>";  
	   	            $message=$message .  $recordEntryValue . "<br><br>";
	   	         }
	   	         $time++;
	   	      }
	   	      $message=$message .  "</div><br>";
	   	   } else {
	   	   	if (is_array($newValue)){
	   	   		$newValue=$newValue[0];
	   	   	}
	   	   	$message="<h2> Parameter " . $parName . " set to :<br>" . $newValue . "</h2>";
	   	   	//die($parName." -1- ".$newValue." - ".$user." - ".$repetition);
	   	   	editValue($parName,$newValue,"not yet validated",$user,$repetition,'0');
	   	   }
		      if (lowerLimit($wholeDescr)!== false and lowerLimit($wholeDescr)<$newValue) {
		         $startParDescr=">" . trim(lowerLimit($wholeDescr)) . ":";
		   	   $lastDescription=true;
		      } else {
		         $lastDescription=false;
		         if (is_array($newValue) and array_key_exists(1,$newValue)){
		         	$currentValue=$newValue[1];
		         } elseif (isset($newValue)){
		         	$currentValue=$newValue[0];
		         } else $currentValue="no";
		         $startParDescr=trim($newValue[0]) . ":";
		      }
		      $noDescription=(strpos($wholeDescr,$startParDescr)===false);
		      if ($possValString=="no restrictions" or $noDescription or ($numericalLineDimension=="1" and $numericalParDimension=="1")) {
		         $parDescr="";
		      } else {
		         //here the program gets the piece of parameter description relative to the
		         //value chosen by the user. This, of course, only happens in the case the
		         //parameter has a discrete set of allowed parameters. We already know with 
		         //what string this piece of parameter description starts: $starParDescr.
		         $possibleValues=turnIntoArray($possValString);
		         $nextValue=getNext($possibleValues,$newValue,$isTheLast);
		         if (lowerLimit($wholeDescr)!== false and lowerLimit($wholeDescr)<$nextValue){
		   	      $stopParDescr=">" . $newValue . ":";
		   	      //$isTheLast=true;
		         } else {
		   	      $stopParDescr=trim($nextValue) . ":";
		         }  		  		   	
		         if ($isTheLast or $lastDescription){
		            $parDescr=substr($wholeDescr,strpos($wholeDescr,$startParDescr));
		         } else {
		   	      $length=strpos($wholeDescr,$stopParDescr)-strpos($wholeDescr,$startParDescr);
		   	      $parDescr=substr($wholeDescr,strpos($wholeDescr,$startParDescr),$length);
		       	}
		      }
         }
      }
      echo '<form  class="buttonRows" action="" method="post">';
      echo $message;
      if(trim($parDescr,' \n\t\r')!=""){
   	   echo "<pre class='pDescr'>";  
         $DescrLines   =explode('\n',$parDescr);
         foreach ($DescrLines as $DescrLine){
            echo trim($DescrLine,'\n'); 
         }
         echo "</pre>"; 
      }
      echo "<br>";
      echo '<input class="button"  type="submit" name="Home" value="Edit next parameter"/>';
      echo '<input class="button"  type="submit" name="Home" value="Choose another parameter to edit"/>';
      echo '</form>';
   }
}

require 'closeHtml.php';
?>
