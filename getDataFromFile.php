<?php
ob_start();



;
require 'functions.php';
require 'openHtml.php';


if (isset($_COOKIE["requiredParName"])){
	$requiredParName=$_COOKIE["requiredParName"];
}

if((isset($_POST["homeOrPar"]) and $_POST["homeOrPar"]=="Go home first")or(isset($_POST["done"]) and $POST["done"]="Back to the working session")){
	   header("Location: readinput.php"); 
	   die();
}

if (isset($_POST["homeOrPar"]) and $_POST["homeOrPar"]=="Do it now"){
   header("Location: parameterPage.php?parName=$requiredParName");
   die();
}

$user=getUser();
$molecules=array('H2O','CO2','O3','N2O','CO','CH4','O2','NO','NO2','HNO3','ClO','OCS','HOCl','ClONO2','HNO4','CCl4','N2O5','HNO3','N2','HCN','H2O2','C2H2','C2H6','COF2','SF6');


if (isset($_GET["parName"])){
	$parName=$_GET["parName"];
	setcookie("parName",$parName);
} else $parName=$_COOKIE["parName"];

$block1=array("INMAX", "ZM", "PM", "TM", "JCHARP", "JCHART", "JLONG", "JCHAR", "VMOL");
$block2=array("LAYX","ZORP", "JCHARb", "DENX"); ##vedi IZORP che fa

if (in_array($parName,$block1)){
   $nMolsParName="NMOL";
   $IZORP='whatever';
} elseif (in_array($parName,$block2)) {
	$nMolsParName="IXMOLS";
	$IZORP=valueOf("IZORP");
}

$nMols= valueOf($nMolsParName);
if ($nMols===NULL){
	setcookie('requiredParName',$nMolsParName);
	$requiredPar=$nMolsParName;
} elseif ($IZORP===NULL){
	setcookie('requiredParName',"IZORP");
	$requiredPar="IZORP";
} else {
	unset($_COOKIE['requiredParName']);
	$requiredPar=false;
}
if($requiredPar){
   ?> 
	<br><h2 class='pHome'> 
	You should enter a value for  <?php echo $nMolsParName; ?>  before editing  <?php echo $parName; ?> 
	</h2> <br> <hr class='divisor'> <br><br>"
   <form class="buttonRows" action="" method="post">
   <input class="button"  type="submit" name="homeOrPar" value="Do it now"/>
   <input class="button"  type="submit" name="homeOrPar" value="Go home first"/>	
   </form>
   <br>
   <?php
} else {

	if (isset($_COOKIE["dataFileName"]) and isset($_GET["uploaded"]) and $_GET["uploaded"]=='OK'){
	   $fileNameAndPath=$_COOKIE["dataFileName"];
	   unset($_COOKIE["dataFileName"]);
	   
		
		$usedMolecules=array_slice($molecules,0,$nMols);
		if (in_array($parName,$block1)){
			$parameters=implode(", ",$block1);
   		$physicalParameters=array('ZM'=>'HGT'  ,'PM'=>'PRE'   ,'TM'=>'TEM'   ,'VMOL'=>$usedMolecules);
			$JCHARpars         =array('ZM'=>'JLONG','PM'=>'JCHARP','TM'=>'JCHART','VMOL'=>'JCHAR');
			$nTimesParName="INMAX";
		} elseif (in_array($parName,$block2)) {
			$parameters=implode(", ",$block2);
			$physicalParameters=array('DENX'=>$usedMolecules);
			if ($IZORP==1){
   			$physicalParameters['ZORP']='HGT';##check what belongs here
   		} else{
   			$physicalParameters['ZORP']='PRE';##check what belongs here
   		}
			$JCHARpars=array('ZORP'=>'','DENX'=>'JCHARb');
			$nTimesParName="LAYX";
		}
		
	
		?>
		<br>
		<div class="pHome">
	   The file uploaded is being used to update the value of the following parameters: <?php echo $parameters; ?>
		</div> <br><br>
		<hr class="divisor"><br>
		<form class="buttonRows" action="" method="post">
	   <input class="button"  type="submit" name="done" value="Back to the working session"/>
		</form>
	   <br>
		<hr class="divisor"><br>
		<?php

		#$result['data']=$data;
	   #$result['flagString']=$JCHAR;   
	   #$result['times']=$nTimes;   
	   #$data=getParValuesFromFile($physicalParameters,$fileName)
	   foreach($JCHARpars as $dataParName=>$JCHARparName){
	   	$parValuesFromFile=getParValuesFromFile($physicalParameters[$dataParName],$fileNameAndPath);
		   $data  =$parValuesFromFile['data'];
		   $JCHAR =$parValuesFromFile['flagString'];
		   $nTimes=$parValuesFromFile['times'];
		   resetParameter($JCHARparName,$user);
		   resetParameter($dataParName,$user);
		   editValue($nTimesParName,$nTimes,"not yet validated",$user,'0','0');
		   //no need to reset LAYX or INMAX, they have dimension one 
		   foreach ($data as $repetition=>$thisValue){
		    	editValue($dataParName,$thisValue,"not yet validated",$user,$repetition,'0');
		    	if ($JCHAR!=""){
		    	   editValue($JCHARparName,$JCHAR,   "not yet validated",$user,$repetition,'0'); //always the same
		    	}
		   }
		}
	} else {
		if (isset($_GET["value"])){
		   $entryType=$_GET["value"];
		   setcookie("entryType",$entryType);
		   if ($entryType=="addValue"){
		   	$action=" add a value to the existing ones";
		   } elseif ($entryType=="changeValue"){
		   	$action=" change the last entered value";
		   } else $action=" enter a new value";
		   $parenthesis="(in this case, as you chose with the previous click, you will $action )";
		} else{
			unset($_COOKIE["entryType"]);
			$entryType="";
			$parenthesis="";
		}
		
		$set          =getParField("block"          ,$parName);
		
		
		if (! isset($_POST["chooseMode"]) or empty($_POST["chooseMode"])){
			$parName=$_COOKIE["parName"];
			?>
			<h1>
		   <span class="ulmeFont"> Edit parameter value </span>
		   </h1>
		   <?php
		   if ($set>0){
				$firstTwoLines=$parName." belongs to the ".ordinal($set)." set of parameters that has to be  repeated an arbitrary number of times.";
			} else $firstTwoLines=$parName." belongs to a set of parameters whose value can be uploaded from a file.";
			?>
			<br>
			<div class="pHome">
			<?php echo $firstTwoLines; ?>  
			You will enter data for <?php echo $nMolsParName." (i.e. ".strval($nMols).") molecules"; ?>  
			You can either edit <?php echo $parName; ?>   in the interactive way, as for 
			most parameters <?php echo $parenthesis; ?>   or by uploading its value. 
			In the latter case, you will change a whole set of parameters, including <?php echo $parName; ?>, and you will erase their old values.
			You can do it by either uploading an appropriate file of browsing the data from the web.<br>
			If you no longer intend to edit this parameter, just click on the last of the buttons below.<br>
			<br>
			<?php echo $extraLine; ?>  
			</div> <br><br>
			<hr class="divisor"><br>
			<form class="buttonRows" action="" method="post">
		   <input class="button"  type="submit" name="chooseMode" value="Input manually"/>
			<input class="button"  type="submit" name="chooseMode" value="From file"/>
			<input class="button"  type="submit" name="chooseMode" value="From website"/>
		   <input class="button"  type="submit" name="chooseMode" value="Edit another parameter"/>
			</form>
		   <br>
			<hr class="divisor"><br>
			<?php
		} elseif ($_POST["chooseMode"]=="Input manually"){
			header("Location: editParValue.php?noRedirect&parName=" . $parName ."&value=" . $entryType);
			die();
		} elseif ($_POST["chooseMode"]=="Edit another parameter"){
			header("Location: readinput.php");
			die();
		} elseif ($_POST["chooseMode"]=="From file"){
			setcookie("sender","getDataFromFile.php");
		   ?>
		   <hr class="divisor">
		   <h1>
		   <span class="ulmeFont"> Data file upload </span>
		   </h1>
		   <hr class="divisor">
		   <br>
		   <form class="buttonRows" action="uploadData.php" method="post" enctype="multipart/form-data">
		   Click on the browse button and pick the data file with molecular data. 
		   <input type="file"  name="uploadDataFile">
		   <br><br>
		   <input class="button" name="uploadData" type="submit" value="Upload File">
		   </form>
		   <br>
		   <hr class="divisor">
		   <br>
		   <?php
		   
		} elseif ($_POST["chooseMode"]=="From website"){ 
		   echo "Function not yet available";
		   die();
		}
	}
}
require 'closeHtml.php';
?>
