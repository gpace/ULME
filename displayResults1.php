<?php
ob_start();

require 'functions.php';
require 'openHtml.php';
?>
<hr class="divisor">
<h1>
<span class="ulmeFont"> Display of the computed <br> transmission spectrum </span>
</h1>
<hr class="divisor">
<br>
<?php
$user=getUser();
$configurationStr=$_COOKIE['configurations'];
$configurations=explode(", ",$configurationStr);
                          
if(isset($_POST["back"]) and $_POST["back"]=="Working session"){
	noLongerRunning($user);
	header("Location: readinput.php");
	die();
} 

if (isset($_POST["runTheCode"]) and $_POST["runTheCode"]=="Go on"){
	signalRunning($user);
	$TAPE5=fromDatabaseToTAPE5($user,$configurations);
	exec("rm -f TAPE27");
	//exec("rm -f TAPE28"); uncomment eventually
   file_put_contents("TAPE5", $TAPE5);
	exec("./lblrtm_v12.2_linux_gnu_dbl",$out);
   exec("ls TAPE27 > tmp27");
   exec("ls TAPE28 > tmp28");
   $TAPE27=file_get_contents("tmp27");
   $TAPE28=file_get_contents("tmp28");
   $output=true;
   $TAPE27=trim(str_replace("\n","",$TAPE27));
   $TAPE28=trim(str_replace("\n","",$TAPE28));
   if ($TAPE27!=''){
   	$LBLRTMoutput=$TAPE27;
   } elseif($TAPE28!='') {
   	$LBLRTMoutput=$TAPE28;
   } else 
      $output=false;
       
	if ($output){
		?>
		<form class="buttonRows" action="" method="post">
			<br><br>
		<a href="<?php echo $LBLRTMoutput;  ?>"     target="_blank"> <div class="button">Download the output file of the LBLRTM code</div></a>
		<br><br>
		<input class="button" type="submit" name="back" value="Working session"/>
		</form>
		
	   <?php
	} else {
		?>
		<div class="pHome">
		We are sorry, something went wrong in the process of running the LBLRTM code. Please, consider reporting to us the issue. 
		</div>
		<br><br>
		<form class="buttonRows" action="" method="post">
		<input class="button" type="submit" name="back" value="Working session"/>
	   </form>
		<?php 
   }
     
} elseif (runningStatus($user)=="allowed"){
	?>
	<br>
	<br>
	<div class="pHome">
	If you click  on the first button below, 'Go on', you will launch  LBLRTM on ULME server. 
	For the duration of the process, which, depending on your input,
	might take up to few minutes, you will prevent other users to do the same. You have a maximum of ten times per day to 
	do that. If another user is running LBLRTM in this very moment, you will be asked to wait until she has her results.
	Please use this tool 	responsibly.  
	</div>
	<br><br>
   <hr class="divisor">
   <br><br>
	<form class="buttonRows" action="" method="post">
   <input class="button"  type="submit" name="runTheCode" value="Go on"/>	
   <input class="button"  type="submit" name="back" value="Working session"/>		
	</form>
	<?php

} else {
	if (runningStatus($user)=="over limit"){
		$message='You already run out of allowed attempts. Wait until tomorrow or just download TAPE5';
	} else 
	   $message='Some other user is running LBLRTM. Go back to the	initial page of the working session and try again in few minutes.';
	?>
	<br>
	<br>
	<div class="pHome">
	<?php echo $message; ?>
	</div>
	<br><br>
   <hr class="divisor">
   <br><br>
	<form class="buttonRows" action="" method="post">
   <input class="button"  type="submit" name="back" value="Working session"/>		
	</form>
	<?php
}



require 'closeHtml.php';
?>
