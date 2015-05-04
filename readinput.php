<?php


ob_start();

require 'functions.php';
require 'openHtml.php';
?> 
<h1>
<span class="ulmeFont"> Working session </span>
</h1>
<hr class="divisor">
<?php

if (newSession()){
	header("Location: startSession.php");
	die();
}

$user=getUser();

if (isset($_POST) and !empty($_POST)){

	switch($_POST["homeButton"]){	
		case "Assign default values":
		   assignDefaults('1',$user);##make the user choose the default, and add many more.
			header("Location: readinput.php");
			die();
			break;		
		case "Check the parameter values":
		   checkParameterValues($user);
			header("Location: readinput.php");
			die();
			break;				
		case "Reset":
		   ## ask for a confirmation, suggest to save the current configuration if it is different.
		   resetValues($user);
			header("Location: readinput.php");
			die();
			break;		
		case "Display all values":
			unset($_POST);
			header("Location: displayAllValues.php");
			die();
			break;
		case "Save current confirguration of parameters": 
		   checkParameterValues($user);
			header("Location: confSaved.php");
			die();
			break;		
		case "Run the LBLRTM code":
		case "View configurations":
			unset($_POST);
			header("Location: editConfigurations.php");
			die();
			break;				  
		case "Show all Records":
		   setcookie("showAll","YES");
			unset($_POST);
			header("Location: readinput.php");
	  	   die();
		   break;	   
		case "Hide non allowed Records":
		   setcookie("showAll","NO");
			unset($_POST);
			header("Location: readinput.php");
		   die();
		   break;	   
	}
} else {
		
	if (( ! isset($_COOKIE["showAll"])) or $_COOKIE["showAll"]=="YES"){
		$showButton="Hide non allowed Records";
	} else {
		$showButton="Show all Records";
	}
			
	?>
	
   <br>
	<div class="pHome">
	Before using ULME, we recommend you to get familiar with it by reading operative and scientific information.
	( you can start clicking on either of the buttons two lines below ) <br> If you are familiar with ULME, 
	proceed. We just remind you that 
	<b> you have to save a configuration of parameters before using it to run LBLRTM</b>.
	<br>
	<p align="center">
	<a     class="button"  href="readMe.php"            >Home Page          </a>
   <a     class="button"  href="scientificContext.php" >Scientific context </a>
   </p>
   <br>
	If you are ready to use this interface, you want to take an action of two kinds. You might either want to use one of the buttons below:
	<br>
	<br>
	<form class="buttonRows" action="" method="post">
   <input class="button"  type="submit" name="homeButton" value="Reset"/>
   <input class="button"  type="submit" name="homeButton" value="Check the parameter values"/>
   <br>	
   <br>
   <input class="button"  type="submit" name="homeButton" value="Save current confirguration of parameters"/>
   <br>	
   <br>
   <input class="button"  type="submit" name="homeButton" value="View configurations"/>
	<input class="button"  type="submit" name="homeButton" value="Assign default values"/>	
	<br>
	<br>
	<input class="button"  type="submit" name="homeButton" value="Run the LBLRTM code"/>	
	<br>
	<br>	
	<input class="button"  type="submit" name="homeButton" value="Display all values"/>	
	<input class="button"  type="submit" name="homeButton" value="<?php echo $showButton; ?>"/>	
	<br>
	</div>
	<br>
	<br>
	<div class="pHome">
   Alternatively, you might need to edit a parameter value. In the latter case find, among the many links below, 
   the one that carries the name of the parameter you need to edit.
	Below each parameter's name (and link) you will read a two-word description of the status of its value, which can be:
	'valid value', 'warning', 'not checked', 'no value'. The first 3 are bold faced. 
	If the status is 'warning', the parameter value will not be used to produce the input for LBLRTM, if no change will
	be made. You can click on the parameter's link to see a more detailed message.
   Click on "save changes" to validate the parameters and use their value to build the input for the LBLRTM code.
   </div>
   <br><br>
	<!-- <input class="button"  type="submit" name="homeButton" value="Home page"/> -->
	
   <table class="pHome">
	<?php
   $emptyRow="<tr><th colspan='6'>    </th></tr>";
  	$query="SELECT * FROM `records`";
   $result=mysql_query($query);
   while($row=mysql_fetch_assoc($result)){
      $lineKey=$row['ID'];
      $file=$row['file'];
		$recordName="Record ". $row['name'];
		if ($file!='TAPE5'){
			$recordName=$recordName.' for file '.$file;
		}
		$recordDimension=$row['record_dimension'];
		$lineParIDs=$row['parameter_list'];
		$linePars=explode(", ",$lineParIDs);
		$showAll=((! isset($_COOKIE["showAll"])) or $_COOKIE["showAll"]=="YES");
	   $show= ((! toBeSkipped($lineKey)) or $showAll);
	   $show=($show and controlRecord($lineKey)===false);
		$numericalRecordDimension=numericalValue($recordDimension);

		if ($show){
			echo "<tr><th colspan='6'>".$recordName."</th></tr>";
			echo $emptyRow;
	    	if ($numericalRecordDimension>1){
	    		echo "<tr>";
	    		echo "<td colspan='6'> This record must be entered <?php echo $numericalRecordDimension;?>  times </td>";
	    		echo "</tr>";
		   }
 	      if (toBeSkipped($lineKey)){
	    		?>
		    	<tr><td colspan='6'> With the present set of parameters, this record will not be used </td></tr>
		    	<?php
	 	   }
         echo $emptyRow;
	 	   echo "<tr>";
	 	   echo "<td> parameters </td>";
		   $i=0;
		   $flags=array();
		   foreach($linePars as $parId){
		   	$queryPar="SELECT * from `parameters` where ID='$parId'";
		   	$parFields=mysql_fetch_assoc(mysql_query($queryPar));
		   	$parName=$parFields['name'];
			   $i+=1;
			
			   if ($i ==5){
			   	$i=1;
				   echo "</tr>";
				   echo "<tr>";
				   echo "<td> Status </td>";
				   foreach ($flags as $F){
				   	echo "<td>".$F."</td>";
				   }
				   echo "</tr>";
				   echo $emptyRow;
   	 	      echo "<tr>";
	      	   echo "<td> parameters </td>";
				   $flags=array();
			   }
			   $message=getParField("message",$parName,$user);
			   $values=getParField("value",$parName,$user);

			   $value=lastEntry($values);
			   if ($message=="not yet validated" ){
				   $flags[]="<b>Not checked</b>";
			   }  elseif (trim($message)!='OK' and trim($message)!=""){
				   $flags[]="<b>Warning</b>";
			   } elseif (! validParValue($value)){
				   $flags[]="No value";
			   } else 
			      $flags[]="<b>Valid value<b>";
			   ?>		
			   <td>
         	<a  href="parameterPage.php?parName=<?php echo $parName;?>">  <?php echo $parName;?>  </a>
         	</td>
			   <?php
 		   }
		   ?>
		   </tr>
		   <tr>
		   <td> Status </td>
		   <?php
		   foreach ($flags as $F){
		   	echo "<td>".$F."</td>";
		   }
		   echo "</tr>";
		   echo str_repeat($emptyRow,8);
		}
	}
	?>
	</table>
	<?php
}
require 'closeHtml.php';
?>


 
