<?php

ob_start();

;
require 'functions.php';
require 'openHtml.php';
$user=getUser();

##simplify this page now that you use the database.

?>
<h1>
<span class="ulmeFont"> Wavelength input </span>
</h1>
<hr class="divisor">
<?php

if (isset($_GET["value"])){
   $entryType=$_GET["value"];
   setcookie("entryType",$entryType);
}

if(isset($_COOKIE["suffix"])){
	$suffix=$_COOKIE["suffix"];
	if ($suffix=="blank"){
		$suffix="";
	}
} else $suffix="";

if (empty($_POST) and isset($_GET) and !empty($_GET)){
   $parName=$_GET['parName'];
   $suffix=substr($parName,2);
} else $suffix="";

$v1="V1" . $suffix;
$v2="V2" . $suffix;
$v1Value=getParField("value",$v1,$user);
$v2Value=getParField("value",$v2,$user);
$viValue=lastEntry($v1Value);
$v2Value=lastEntry($v2Value);
$set    =getParField("block",$v2);


$parDimension =getParField("dimension"      ,$v1);
$recordId     =getParField("records_id"     ,$v1);
$set          =getParField("block"          ,$v1);
$parDimension =getParField("dimension",      $v1);
$lineDimension=getRecordField("record_dimension",$recordId);
$recordName   =getRecordField("name",$recordId);
$repetition   =getLastRepetition($v1,$user);


$numericalParDimension =numericalValue($parDimension);
$numericalLineDimension=numericalValue($lineDimension);



if (empty($_POST) and isset($_GET) and !empty($_GET)){
   if ($suffix==""){
   	$suffix="blank";
   }
	setcookie("suffix",$suffix);




	if (validParValue($v1Value)){	
	   echo "<h2>" . "Both " . $v1 . " and " . $v2 . " must have a valid value.";
	   echo "<h2> Present values: " . $v1 . "= " . $v1Value . "cm<sup>-1</sup> , " . $v2 . "= " . $v2Value . "cm<sup>-1</sup></h2><hr class='divisor'>";
	} else {
	   echo "<h2> No value was given for " . $v1 . " and " . $v2 . "</h2>";
	   echo "<h2> You need to enter a value for both of them</h2> <hr class='divisor'>";
	   $v1Value="";
	   $v2Value="";
	}
	echo str_repeat("<br>",3);

   

    ?>
    <p class="pHome">Enter a value for <?php echo $v1 ?> in the box on the left and one for <?php echo $v2 ?> in the other, then confirm by clicking on
    either button below, according to the format you chose (wavenumber in cm<sup>-1</sup> or wavelength in &Aring ).
    <?php echo $v2 ?> must be a longer wavelength than <?php echo $v1 ?>.<br> </p><br>
    <form  class="buttonRows" action=""   method="post">
    <input type="text"     name="v1Value" value="<?php echo $v1Value; ?>" />
    <input type="text"     name="v2Value" value="<?php echo $v2Value; ?>" /> <br><br>
    click <input class="button" type="submit"   name="Unit" value="wavelength"> if you entered a wavelength in &Aring <br><br>
    click <input class="button" type="submit"   name="Unit" value="wavenumber"> if you entered a wavenumber in cm<sup>-1</sup> 
    </form> 

   <?php




   	

echo str_repeat("<br>",10);

} elseif(isset($_POST["Entered"])  and ($_POST["Entered"]=="Confirm")){
	$v1Value=$_COOKIE["v1Value"];
	$v2Value=$_COOKIE["v2Value"];

   $v1="V1" . $suffix;
   $v2="V2" . $suffix;
   $set            =getParField("block"      ,$v1);
	

   if (isset($_COOKIE["entryType"])){
   	if ($_COOKIE["entryType"]=="changeValue"){
   		resetParameter($v1,$user);
   		resetParameter($v2,$user);
   		if ($repetition!=0){
   			$repetition--;
   		}
   	}
	   editValue($v1,$v1Value,"not yet validated",$user,$repetition,0);
	   editValue($v2,$v2Value,"not yet validated",$user,$repetition,0);
	}	
   header("Location: readinput.php");
   die();
} elseif(isset($_POST["Entered"])  and ($_POST["Entered"]=="Retry")){

   header("Location: readWaveLength.php?parName=V1" . $suffix);
   die();
} elseif(isset($_POST["Entered"])  and ($_POST["Entered"]=="Discard")){
   header("Location: readinput.php");
   die();
} else {
	$v1Value=$_POST["v1Value"];
	$v2Value=$_POST["v2Value"];

   $v1="V1" . $suffix;
   $v2="V2" . $suffix;
   setcookie("v1Value",$v1Value);
   setcookie("v2Value",$v2Value);
  	setcookie("suffix",$suffix);
  	

	$error=true;
	$message="";
   $checkFurther=false;
	if (! (is_numeric($v1Value) and is_numeric($v2Value))){
		$message="Both values must be numeric";
	} elseif(isset($_POST["Unit"]) and $_POST["Unit"]=="wavelength"){
   	$v1Value=1E+12/$v1Value;
   	$v2Value=1E+12/$v2Value;
   	$checkFurther=true;
   } elseif(!(isset($_POST["Unit"]) and $_POST["Unit"]=="wavenumber")){
   	$message="Click on either 'wavelength' or 'wavenumber' button to confirm";
   } else{
   	$checkFurther=true;
   }
   
   if($checkFurther){
      if ($v1Value<="0" or $v2Value<=0){
		   $message="Both values must be strictly positive";
	   } elseif($v1Value>=$v2Value){
		   $message=$v2 . " must be a shorter wavelength or higher wavenumber than " . $v1;
	   } else {
		   $message="The values you entered are: " . $v1 . "=" . $v1Value . "cm<sup>-1</sup> and " . $v2 . "=" .  $v2Value. "cm<sup>-1</sup>";
		   $error=false;
	   }
	}
	?>
    <form  class="buttonRows" action=""   method="post">
	<?php

   echo str_repeat("<br>",2);
   echo "<h3>" . $message . "</h3><br>";

	if ($error){

 	   ?>
       <INPUT class="button" type="submit" name="Entered" value="Retry" />
	   <?php

	} else {
		//transform from angstrom into cm^-1
		?>
		 <INPUT class="button" type="submit" name="Entered" value="Confirm" />
		 <?php
	}
	?>
	 <INPUT class="button" type="submit" name="Entered" value="Discard" />
    </form> 
	<?php
	
	
} 
require 'closeHtml.php';
?>

