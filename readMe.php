<?php
require 'openHtml.php';
?>
<h1>
<span class="ulmeFont"> Home </span>
</h1>
<hr class="divisor">
<?php

if (isset($_POST["back"]) and $_POST["back"]=="Working session"){
	header("Location: readinput.php");
	die();
} elseif (isset($_POST["back"]) and $_POST["back"]=="Graphical summary"){
	header("Location: readMeMoreDetails1.php");
	die();
} elseif (isset($_POST["back"]) and $_POST["back"]=="Scientific context"){
	header("Location: scientificContext.php");
	die();
}
?>
<br>
<form class="buttonRows" action="" method="post">
<input class="button" type="submit" name="back" value="Working session"/>
<input class="button" type="submit" name="back" value="Graphical summary"/>
<input class="button" type="submit" name="back" value="Scientific context"/>
</form>
<br>
<hr class="divisor">
<br>
<div id="top" class="pHome"> 
<h2>What is  ULME?</h2>
The <a href="http://rtweb.aer.com/lblrtm.html" target="_blank"> LBLRTM code </a>, 
is a sophisticated code that allows a realistic modelling of the earth atmosphere. It is an invaluable 
aid in several astrophysical fields involving infra-red spectroscopy, including EXO-planet detection and EXO-planet-atmosphere 
characterization (for a more detailed discussion of the scientific context, click on the left one of the three buttons above).  
However, the use of the LBLRTM code is extremely challenging because of the lack of clear instructions and the 
absence of a functional interface with the user. An easy access to realistic earth-atmosphere modelling could boost the positive spiral of an 
ever growing dataset being used to improve upon modelling and molecular data, which would encourage more members of the scientific 
community to fully exploit this potential. <br>
This was the rationale for building <b> ULME, <br> an interface between the user and the LBLRTM code.</b> <br>
It is a platform written in php that runs a python code. On the 8<sup>th</sup> of April 2015, a working version was made available on the internet to the
scientific and programming community not only for use, but also for improvements and updates in an open source setting.  We hoped to 
generate another upward spiral here, similar to that described above, in which  more and more scientists make ULME more and more user 
friendly and versatile, therefore more popular. <br>
</div><br><br><br><br>

<div id="top" class="pHome">
<h2>How it works</h2>

The input for the LBLRTM code has to be built by assigning a value, or a set of values, to some parameters. These parameters
are either control parameters, that tell LBLRTM what to do and how to do it, or data parameters, that store some physical quantities
necessary to build the model.
Examples among the many of the former category are <b> MODEL</b>, that tells LBLRTM what kind of atmospheric profile will be supplied, 
<b>IPLT</b>, which has to be set to 1 if we want LBLRTM to print an ouptut, to 0 otherwise, 
<b>NMOL</b>, which is the number of molecules whose density profile we want  to be used in to build an atmospheric profile model. 
Examples of data parameters are <b>VMOL</b>, in which molecular densities will be stored, <b>DENX</b>, same thing for molecular 
cross sections, <b>VLAS</b>, parameter of the laser wavenumber values, and so on.
<br>
The parameters are organized in sets called Records.  
<br>
When starting the working session, the user is presented with a page that has a box on the top, in 
which some instructions and a brief explanation are given. The box also contains a serious of control buttons, 
to take  some specific actions that will be explained in detail below.
<br>
Then the buttons relative to the parameters are shown record by record.
Clicking on one of those, the user is redirected to the parameter page, where a description of the parameter is provided, 
and where the user can edit or enter the value of the parameter. Thanks to ULME, the user does not have to worry about 
details such as the structure of the data and their format, and she can concentrate on their meaning and their role in 
building a model of the transmission spectrum of an earth atmosphere.  The descriptions of the parameters are
often taken directly from the LBLRTM website, they were occasionally made clearer. One aspect in which the feedback of the
user is strongly needes and encouraged, is the clarity and accuracy of the descriptions of the parameter.
<br>
The python code run by ULME, can also work on its own and produce a correct output for the LBLRTM and also aids the user with
an interface. You can freely download the python code and save it on your machine to use it off-line whenever necessary.
However, ULME is a much more efficient support.   
<br>
You are most welcome to contribute by sending your comments to <a href="mailto:gpace@astro.up.pt?subject=Comments on ULME">
gpace@astro.up.pt</a> or by editing directly the code and sending your updated version at the same address. Your help will be 
acknowledged. Both the python and the php source code is publicly 
available <a href="codes.txt" target="_blank"> here </a>. A pdf document with a detailed description of the codes will be briefly available.
For the moment, make the most of the comment lines.  
<br>

<br>
Let's go back to the command buttons in the box,  and let's explore in detail what they do. 
<ul>

<li><b>Reset</b> erases all parameter values. Notice that you can save at any point the current configuration of parameters
by clicking on the third button, as you will soon see. By clicking on "Reset" you will not erase the saved configurations, 
just the current one.
</li>

<li><b>Check the parameter values </b> allows you to know what parameters were correctly entered and which ones have some
kind of problems.  The buttons of the parameters you edited, before you click on this button, are yellow. It means that ULME
does not yet know whether they are valid or not. When you click on "check the parameter values", the parameter buttons either 
turn orange, which means that the value of the associated parameter is fine, or red, which on the contrary signals that the 
associated parameter, for some reasons, cannot be used to produce an input for TAPE5 because if the value of the other 
parameters remain the same.   
</li>

<li>
<b>Save current configuration of parameters</b> stores the current configuration of parameter values and gives it a number. 
If you are saving, by clicking this button, say, the 4<sup>th</sup> configuration, then you can eventually retrieve it 
in the way will be explained below.
</li>

<li>
<b>View configurations</b>
</li>

<li>
<b>Assign defaults</b> assigns fefault values to the parameters. It gives a starting point. After clicking on it, 
you can keep editing the current conifuration.</li>
</li>

<li>
<b>Display all values</b> redirects you to a page where you can see all the parameters values at one. 
</li>

<li>
<b>Home</b> redirects you to this page.
</li> 

<li>
<b>Scientific context</b> is the page where a scientific overview of the motivation for a realistic modeling of the earth atmosphere is given.
</li> 

</ul>
<a href="readMe.php#top"> To the top</a>
</div>
<br><br>


<div id="pythonCode" class="pHome"><h2> The python code </h2>
<br>

The python code <i>(writeinput.py)</i> can be used in two modalities: 
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

<br><br>
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

In interactive mode, writeinput asks the user to enter a value for each parameter, each time presenting an explanation for 
the current parameter. The order in which the parameters are shown, follows strictly the order in which they are described
in the instructions of the LBLRTM code. The user is forced to give an entry of the right type, for instance a number 
and not a string if the parameter is numeric, or a value within a range of allowed possibilities, when such a case applies.
The user is only prompted to give entries for the parameters that, according to the values entered up to that moment, should
should be written in the input for the LBLRTM code. The others are forcefully neglected, the code just skips them. 

For some Records (which, we remind, are subsets of parameters that control a specific aspect of the code), the input query sequence
is repeated several times, as requested by the code.
<br><a href="readMe.php#top"> To the top</a>
</div><br><br>



<?php
require 'closeHtml.php';
?>
