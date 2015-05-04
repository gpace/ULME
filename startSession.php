<?php

ob_start();

;
require 'functions.php';
require 'openHtml.php';

?> 
<h1>
<span class="ulmeFont"> Register or recover session </span>
</h1>
<hr class="divisor">
<?php


$sessionId=session_id();
                        
if (isset($_POST['WorkingSession']) and $_POST['WorkingSession']=="Go to the working session"){	
   header("Location: readinput.php");
   die();
}

if (isset($_POST['submitEmail']) and !empty($_POST['submitEmail'])){	
   if (isset($_POST['email']) and filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) !== false) {
   	$email=$_POST['email'];
   	if (newUser($email)){
	   	register($email);
	      ?>
	      <br><br>
	      <div class="pHome">
	      You now successfully registered. Please click below to start the working session
	      </div>
			<br><br>
			<?php 
      } else {
      	restoreSession($email);
	      ?>
	      <br><br>
	      <div class="pHome">
	      Your email was already registered. Please click below to recover your last working session
	      </div>
			<br><br>
			<?php       	
      }
      ?>
   	<form class="buttonRows" action="" method="post">
		<input class="button"  type="submit" name="WorkingSession" value="Go to the working session" />
		</form>
		<br>
		<?php
   } 
} else {		
	?>	
   <br>
	<div class="pHome">
   Your starting a new session. Enter your email in the box below. Since the system deals with no private information,
   no password is needed.  If it is the first time you use ULME, you will be registered. Otherwise your last session 
   will be restored.
   </div>
 	<br>
	<form class="buttonRows" action="" method="post">
	<input type='text'     name='email' value='' /> 
	<input class="button"  type="submit" name="submitEmail" value="enter" />
	</form>
	<br>
	<br>
	<!-- <input class="button"  type="submit" name="homeButton" value="Home page"/> -->	
 	<?php
}
require 'closeHtml.php';
?>


 
