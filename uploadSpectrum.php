<?php


;
require 'functions.php';
require 'openHtml.php';

if (isset($_POST["back"])  and $_POST["back"]=="Working session"){
	header("Location: readinput.php");
	die();
} elseif (isset($_POST["back"]) and $_POST["back"]=="Try again"){
	header("Location: compare.php");
	die();
} elseif (isset($_POST["seeResult"]) and $_POST["seeResult"]=="Check the match"){
	$uploadedFile_name=$_COOKIE["spectrumName"];
	header("Location: compare.php?spectrumFile=$uploadedFile_name");
	die();
}


$target_dir = "uploads/";
$target_dir = $target_dir . basename( $_FILES["uploadFile"]["name"]);
$uploadFile_size=$_FILES['uploadFile']['size'];
$uploadFile_type=$_FILES['uploadFile']['type'];
$uploadFile_name=$_FILES['uploadFile']['name'];
$uploadOk=false;
// Check if file has been selected
if (!isset($_FILES["uploadFile"])or $_FILES["uploadFile"]["name"]=="") {
    $message= "Error, no file was selected.";
// Check file size
} elseif ($uploadFile_size > 500000) {
    $message= "Sorry, your file was not uploaded, it exceeds 500MB.";
// Only a certain kind of files is allowed, modify this later and introduce more check
// Like the number of rows
} elseif (!($uploadFile_type == "application/octet-stream")) {
    $message= "Sorry, your file was not uploaded, only application/octet-stream files are allowed.";
} elseif (move_uploaded_file($_FILES["uploadFile"]["tmp_name"], $target_dir)) {
    $uploadOk=true;
    $message= "The file ". basename( $uploadFile_name). " has been uploaded.";
} else {
    $message= "Sorry, there was an error uploading your file.";
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
<?php


if ($uploadOk){
	setcookie("spectrumName",$uploadFile_name);
	?>
   <br>
   <form  class="buttonRows" action="" method="post">
   <input class="button" type="submit" name="seeResult" value="Check the match"/>
   <br><br>
	<?php
} else {
?>
<br>
<form  class="buttonRows" action="" method="post">
<input class="button" type="submit" name="back" value="Try again"/>
<input class="button" type="submit" name="back" value="Working session"/>
</form>
<br>
<hr class="divisor">
<br>
<?php
}
require 'closeHtml.php';
?>


