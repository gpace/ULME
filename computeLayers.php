<?php

ob_start();


;
require 'functions.php';
require 'openHtml.php';

$parName  ='ZorP_BND';
$requiredParameters="";
$H1       =valueOf("H1");
$H2       =valueOf("H2");
$IBMAX    =valueOf("IBMAX");
if (intval($IBMAX)<0){
	$sign="-";
} else {$sign="";}

?>
<h1>
<span class="ulmeFont"> Wavelength input </span>
</h1>
<hr class="divisor">
<?php

$user=getUser();
if (isset($_POST["editOtherPar"]) and ! empty($_POST["editOtherPar"])){
	if ($_POST["editOtherPar"]=="Edit another parameter"){
		header("Location: readinput.php");
		die();
	}
	$parName=$_POST["editOtherPar"];
	setcookie("parName",$parName);
	header("Location: editParValue.php?value=newValue");
	die();
}


if (isset($_POST["confirm"]) and ($_POST["confirm"]=="Continue session" or $_POST["confirm"]=="Discard" )){
	header("Location: readinput.php");
	die();
}


if (isset($_POST["confirm"]) and ($_POST["confirm"])=="Update value"){
   $entryType=$_COOKIE["entryType"];
   $parName  ='ZorP_BND';
  
	if ($_COOKIE["entryType"]=="changeValue"){
		deleteLastValue($parName,$user);
	}



  	if (abs($IBMAX)<3){
  		$ZorP_BND=array($H1,$H2);
  	} else {
  		$step=($H2-$H1)/abs($IBMAX);
     	$ZorP_BND=array();
     	$lastEl=$H1;
     	for($i=1;$i< abs($IBMAX);$i++){
     		$ZorP_BND[]=$lastEl;
     		$lastEl=$lastEl+$step;
     	}
     	$ZorP_BND[]=$H2;
   }
   $value=implode(",",$ZorP_BND);

   editValue("ZorP_BND",$value,"not yet validated",$user,'0','0');

   ?>
   <br>
   <p class="pHome">ZoRP_BND has been successfully updated. Click the button below to edit more parameters <br> </p><br>
   <form  class="buttonRows" action=""  method="post">   	
   <input class="button" type="submit"  name="confirm" value="Continue session" />   	
   </form>
   <?php
}

if (isset($_GET["parName"]) and isset($_GET["value"]) and $_GET["parName"]=="ZorP_BND" and !isset($_POST["confirm"])){

	$entryType=$_GET["value"];
	setcookie("entryType",$entryType);

  	
  
   $allValidValues=(validParValue($H1) && validParValue($H2) && validParValue($IBMAX));
   $allnumericValues=(is_numeric($H1) && is_numeric($H2) && is_numeric($IBMAX));
   if ($allValidValues and $allnumericValues){
   	unset($_POST);
   	?>
   	<br>
      <p class="pHome"> If you confirm by clicking on the buttom "Update value", ZoRP_BND will be set 
      to  an array of <?php echo $sign ?> IBMAX (i.e. <?php echo abs($IBMAX) ?>) elements
      ranging from H1 (i.e. <?php echo $H1 ?>) to H2 (i.e. <?php echo $H2 ?>).
      <br> </p><br>
      <form  class="buttonRows" action=""  method="post">
      <input class="button" type="submit"  name="confirm" value="Update value" />   	
      <input class="button" type="submit"  name="confirm" value="Discard" />   	
      </form>
      <?php
   } else {
   	?>
   	<br>
      <p class="pHome">ZoRP_BND is not entered directly by the user. It is an array of IBMAX elements from H1 to H2. 
      It cannot be computed with the present set of parameters because, among the aforementioned ones, some are undefined
      or not correctly entered. 
      Chose by clicking a button below whether you want to enter a value for one of them, or go on with the working session <br> </p><br>
      <form  class="buttonRows" action=""   method="post">
      <?php
      
      if (! validParValue($H1) or ! is_numeric($H1)){
         ?>
         <input class="button" type="submit"    name="editOtherPar" value="H1" />
         <?php
      }

      if (! validParValue($H2) or ! is_numeric($H2)){
         ?>
         <input class="button" type="submit"    name="editOtherPar" value="H2" />
         <?php
      }

      if (! validParValue($IBMAX) or ! is_numeric($IBMAX)){
         ?>
         <input class="button" type="submit"    name="editOtherPar" value="IBMAX" />
         <?php
      }

      ?>
      <input class="button" type="submit"     name="editOtherPar"    value="Edit another parameter" /> <br><br>
      </form> 
      <?php
   }
   

}

require 'closeHtml.php';
?>

