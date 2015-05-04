<?php
require 'openHtml.php';

if (isset($_POST) and !empty($_POST) and $_POST["back"]=="Working session"){
	header("Location: readinput.php");
	die();
} elseif (isset($_POST) and !empty($_POST) and $_POST["back"]=="Scientific context"){
	header("Location: scientificContext.php");
	die();
} elseif (isset($_POST) and !empty($_POST) and $_POST["back"]=="Home"){
	header("Location: readMe.php");
	die();
}
?>
<br>
<form class="buttonRows" action="" method="post">
<input class="button" type="submit" name="back" value="Home"/>
<input class="button" type="submit" name="back" value="Working session"/>
<input class="button" type="submit" name="back" value="Scientific context"/>
</form>
<br>
<hr class="divisor">
<br>

<p id="top" class="pHome"> 
ULME is a platform written in php that runs a python code. 
It is an interface between the user and the <a href="http://rtweb.aer.com/lblrtm.html" target="_blank">LBLRTM code</a>, 
a code for modelling the earth atmosphere. ULME facilitates the use of the LBLRTM code by allowing the user to enter its control 
parameters while being instructed on the meaning  and function of the parameter at hand. The explications of such 
meanings and functions are taken from the manual of the LBLRTM code, sometimes reformulating the text to adapt it to the different context.  
<br>
More work needs to be  done before we can really call this platform "user friendly", and we are doing it. You are most welcome to contribute by 
sending your comments to <a href="mailto:gpace@astro.up.pt?subject=Comments on ULME">
gpace@astro.up.pt</a> 
<br>
The python code can also work on its own and it achieves alone the main goal of this platform. 
The php-based platform makes its use faster and more intuitive.
</p><br><br><br><br>

<p class="pHome"><b>The php-based platform <br></b>

The php part of the code does not add any scientifically significant feature to <i>writeinput.py</i> 
<a href="readMe.php#pythonCode"> (see below)</a>
but it makes the platform more practical to use by adding possibilities difficult to implement with python. 
Contrary to the python code <i>writeinput.py</i>, in the php platform the parameter values can be entered in any 
order. Parameter names are displayed in the <a href="readinput.php"> home page of the platform</a>, each on its button.
When the user clicks on one of them, she is directed to the page that will allow her to entry the value of the relative parameter and she will be  
presented with the relative information.
While for most parameters the user is just prompted to enter the value in a box or choose from a menu, some parameters require a specific modality, 
such as, for instance, the wavelength. The wavelength values can be entered either directly in Angstroms or in the form of wave number.
The user decides when her choices of the parameter-values can be fed to the LBLRTM code. At this point, such choices are stored in 
an ini file which is then fed to <i>writeinput.py</i>, which creates the input for LBLRTM which, on user demand, can also be run and have its
output displayed. More details in the section below.

</p><br><br>
<div class="pHome"> 
<b>Main functionalities of the php-based platform<br> </b>
The first page the user is presented with, contains several buttons displayed in rows.
The buttons in the first set of rows are special commands for the platform. 
You must have already guessed that the last of such buttons is the link to this page, which otherwise you would not be reading.
<br>
The parameters in the LBLRTM code are organized in Records, i.e. subsets of parameters that control a specific aspect of the code.
Each parameter has its button displayed in the page, and the buttons are grouped in Records just like the parameters to which they refer.
You just have to click on one button to edit the value of the relative parameter.
<br>
Let's go back to the first rows and let's see in details what its buttons do, one by one. 
<ul>
<li><b>Reset</b> erases any change you might have done to the parameters, and returns to the initial configuration. Notice that the initial 
configuration, just like the default sets of parameters, is a pre-chosen set of parameter values that the user can start with.
</li>
<li><b>Save changes</b> saves the present configuration of the parameters, and it uses it to create an input for the LBLRTM code (the TAPE5 file) 
</li>
<li>
<b>Save changes as one chunk</b> adds the present parameter configuration to the input file for the LBLRTM code, i.e. TAPE5. 
If you want to build  an input for LBLRTM to make it run several times in one go, set the first configuration of paramters, save it with the usual button "Save 
changes", then move to the next configuration and save it with the button "Save changes as one chunk". Keep repeating the latter 
step until you saved all the configurations you intend to save. Then you can run the LBLRTM code by clicking on the apposite button,  
which is described below after the next one. Be reminded that 
Every time you click on "Save changes", you start this process over, and you loose the information saved up to that point.</li>
<li>
<b>Assign defaults (1 and 2)</b> creates an input for the LBLRTM code with the indicated set of default values, which can be edited afterwards.
These two sets of defaults are stored in a file, and have nothing to do with the choices that the users entered before clicking on
"Assign defaults". Note that, by clicking this button, you lose the changes that you have done to the last configuration.</li>
<li>
<b>Run example: [example name]</b> runs the LBLRTM code with the TAPE5 corresponding to an example. The value of the parameters will not be changed.</li>
<li>
<b>Run LBLRTM and display results</b>. The output shown is the file TAPE6.
</li>
<li><b>Back to last save</b> restores the configuration saved last time the user clicked on "Save changes".</li>
<li>
<b>Display all values</b> redirects you to a page where you can see all the parameters values at one. </li>
<li>
<b>Help</b> redirects you to this page.</li> 
</ul>
<a href="readMe.php#top"> To the top</a>
</div>
<br><br>


<div id="pythonCode" class="pHome"><b> The python code <i>writeinput.py</i> : what it does </b>
<br>

The python code <i>writeinput.py</i> can be used in two modalities: 
<br>
<i>the interactive one</i>, in which the users entry a value for each parameter and the user choices are restricted to avoid errors; <br> 
<i>the non interactive </i>one, in which the parameters are fed through a file. In this mode, <i>writeinput.py</i> edits the input file 
to validate the parameters.<br>
Some specific errors cannot be avoided by the restrictions in the interactive mode and will not be emended by the parameter-file 
editing in the non interactive mode. In such cases, the python code <i>writeinput.py</i> will generate a warning message. 
The main output of <i>writeinput.py</i> consists in the input file for the LBLRTM code. 
If the set of parameter values matches the conditions imposed by the LBLRTM code, i.e. if <i>writeinput.py</i> does not generate any warning message,
then feeding the file generated by <i>writeinput.py</i> to the LBLRTM code will result in a successful run.
<br><a href="readMe.php#top"> To the top</a>
</div><br><br>

<p class="pHome"><b>The python code: how it works<br></b>

In interactive mode, the python code reads the choices of the user and transforms them into two files containing basically the same information
in different formats: TAPE5 and detTAPE.ini. TAPE5 is the input for the LBLRTM code, while detTAPE.ini is an ini file that can be read 
as input in the non interactive mode. <br>


The main purpose of the ini file is to be the interface between the <i>writeinput.py</i> and the php platform, but it can also be useful when using 
the python code alone. For instance, if the user wants to slightly change a set of  parameter values after having entered them in interactive mode,
the fastest way is to edit detTAPE.ini and then feed it into <i>writeinput.py</i> in non-interactive mode.
<br>
In non-interactive mode <i>writeinput.py</i> basically edits detTAPE.ini to avoid or signal errors and values incompatible with the rules of the LBLRTL code,
and it translates it into the TAPE5 format, ready to be used by the LBLRTM code.
<br>
<i>writeinput.py</i> always reads also two files. They contain the instructions of the LBLRTM code, modified with respect to the original version
to allow their authomatic reading.
The information stored in this file is used by  <i>writeinput.py</i> to validate the parameter and, in interactive mode, to show to the 
user the relavant information about the parameter he is entering. <i>writeinput.py</i> also transforms them,  at  the beginning of each run, 
into the file detTAPEall.ini, which is an ini file containing the same fields as detTAPE.ini, one for each parameter.  
While detTAPE.ini contains the values for the parameters and possibly the warning message, 
detTAPEall.ini only contains the permanent information, such as the description of the parameter, 
whether their value must be a variable of a list, and its dimension in the latter case.
<br><a href="readMe.php#top"> To the top</a>
</p><br><br>
<p class="pHome"><b> More on the interactive use of <i>writeinput.py</i> <br> </b>

In interactive mode, writeinput asks the user to enter a value for each parameter, each time presenting an explanation for 
the current parameter. The order in which the parameters are shown, follows strictly the order in which they are described
in the instructions of the LBLRTM code. The user is forced to give an entry of the right type, for instance a number 
and not a string if the parameter is numeric, or a value within a range of allowed possibilities, when such a case applies.
The user is only prompted to give entries for the parameters that, according to the values entered up to that moment, should
should be written in the input for the LBLRTM code. The others are forcefully neglected, the code just skips them. 

For some Records (which, we remind, are subsets of parameters that control a specific aspect of the code), the input query sequence
is repeated several times, as requested by the code.
<br><a href="readMe.php#top"> To the top</a>
</p><br><br>



<?php
require 'closeHtml.php';
?>
