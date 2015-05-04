<?php
ob_start();

;
require 'functions.php';
require 'openHtml.php';
?>
<hr class="divisor">
<h1>
<span class="ulmeFont"> Display of both spectra: <br> the earth-transmission  model <br> and the observed spectrum
</span>
</h1>
<hr class="divisor">
<br>
<?php
$displayed=false;
if(isset($_GET["spectrumFile"])){
	$spectrumFile=$_GET["spectrumFile"];
	exec("python TAPE28toCSV.py",$out);
	echo "<br> for the moment it does not show " .$spectrumFile . " but it will <br>";
	?>
   <form class="buttonRows" action="" method="post">
   <input class="button" type="submit" name="alreadyUploaded" value="Show only the synthetic earth-transmission spectrum"/>
   <br><br>
   <a href="TAPE28fake" target="_blank"> <div class="button">Download the output file of the LBLRTM code</div> </a>
   <br><br>
   <input class="button" type="submit" name="alreadyUploaded" value="Working session"/>
   <input class="button" type="submit" name="alreadyUploaded" value="Refresh this page"/>
   </form>
   <br>
   <hr class="divisor">
   <br>
   <!-- 
   Look here how to do plots with JavaScripts.
   http://dygraphs.com/tutorial.html
   -->
   <script type="text/javascript"
   src="dygraph-combined.js"></script>
   <div id="graphdiv"  class="centralImage" style="width:700px;height:300px"></div>

   <script type="text/javascript">
   g2 = new Dygraph(
   document.getElementById("graphdiv"),
   "dataToPlot2D.csv", // path to CSV file
   {}          // options
   );
   </script>
   <?php
   $displayed=true;
}

unset($_GET["spectrumFile"]);


if (!$displayed) {
  ?>
  <form class="buttonRows" action="uploadSpectrum.php" method="post" enctype="multipart/form-data">
  Browse button and upload   the file with the observed spectrum. 
  <input type="file"  name="uploadFile">
  <br><br>
  <input class="button" name="compare" type="submit" value="Upload File">
  </form>
  <br>
  <hr class="divisor">
  <br>
  <?php
}
if (isset($_POST["alreadyUploaded"]) and $_POST["alreadyUploaded"]=="Working session"){
   header("Location: readinput.php");
   die();
} elseif (isset($_POST["alreadyUploaded"]) and $_POST["alreadyUploaded"]=="Show only the synthetic earth-transmission spectrum"){
   header("Location: displayResults.php");
   die();
} elseif (isset($_POST["alreadyUploaded"]) and $_POST["alreadyUploaded"]=="Refresh this page"){
   echo '<meta http-equiv="refresh" content="5">';
   die();
}

require 'closeHtml.php';
?>
