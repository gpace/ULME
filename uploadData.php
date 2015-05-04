<?php


;
require 'functions.php';
require 'openHtml.php';

if (isset($_POST["proceed"]) and $_POST["proceed"]=="Click here to proceed"){
	$sender=$_COOKIE["sender"]."?uploaded=OK";
	unset($_COOKIE["sender"]);
	header("Location: $sender");
	die();
} elseif (isset($_POST["proceed"]) and $_POST["proceed"]=="Try again"){
	$sender=$_COOKIE["sender"]."?uploaded=NO";
	unset($_COOKIE["sender"]);
	header("Location: $sender");
	die();
} elseif (isset($_POST["proceed"]) and $_POST["proceed"]=="Back to working session"){
	unset($_COOKIE["sender"]);
	header("Location: readinput.php");
	die();
}
	$uploadOk=false;

// Check if file has been selected
if (!isset($_FILES["uploadDataFile"])or $_FILES["uploadDataFile"]["name"]=="") {
   $message= "Error, no file was selected.";
// Check file size
} else {
   
	$target_dir = "uploads/";
	$fileNameAndPath = $target_dir . basename( $_FILES["uploadDataFile"]["name"]);
	$uploadFile_size=$_FILES['uploadDataFile']['size'];
	$uploadFile_type=$_FILES['uploadDataFile']['type'];
	$uploadFile_name=$_FILES['uploadDataFile']['name'];

	if ($uploadFile_size > 500000) {
	   $message= "Sorry, your file was not uploaded, it exceeds 500MB.";
	// only the kind of file suited to contain the data we need is allowed.
	} elseif (!($uploadFile_type == "application/octet-stream")) { 
	    $message= "Sorry, your file was not uploaded, only application/octet-stream files are allowed.";
	} elseif (move_uploaded_file($_FILES["uploadDataFile"]["tmp_name"], $fileNameAndPath)) {
	   $uploadOk=true;
	   setcookie("dataFileName",$fileNameAndPath);
	   $message= "The file ". basename( $uploadFile_name). " has been uploaded.";
	} else {
		$message= "Sorry, there was an error uploading your file.";
   }
}

?>
<br>
<hr class="divisor">
<br>
<h2 class="pHome">
<?php echo $message ?>
</h2>
<br>
<hr class="divisor">
<br>
<?php

if ($uploadOk){
	?>
	<form class="buttonRows" action="" method="post">
	<input class="button"  type="submit" name="proceed" value="Click here to proceed"/>
	</form>
	
	<?php
} else {
	?>
	<form class="buttonRows" action="" method="post">
	<input class="button"  type="submit" name="proceed" value="Try again"/>
	<input class="button"  type="submit" name="proceed" value="Back to working session"/>
	</form>
	
	<?php
}

require 'closeHtml.php';
?>


