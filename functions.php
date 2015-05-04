<?php
//In this file we put all the function.

require 'connect.php';


function getUser(){
	//Function that returns the user identification code.
	//Change at will
   $sessionId=session_id();
   $query="SELECT * FROM `users` WHERE session_id='$sessionId'";
   $result=mysql_query($query);
   if (! $result){
   	die("your session id got lost");
   }
	$row=mysql_fetch_assoc($result);
	return ($row['ID']);
}

function newSession(){
	//function that checks whether it  user connects to ULME for the first time in this session;
	$sessionId=session_id();
	$query="SELECT COUNT(*) FROM `users` WHERE session_id='$sessionId'";
	$result=mysql_result(mysql_query($query),0);
	return ($result==0);
}

function newUser($email){
	//function that checks whether it  user connects to ULME for the first time ever with the given email;
	$query="SELECT COUNT(*) FROM `users` WHERE email='$email'";
	$result=mysql_result(mysql_query($query),0);
	return ($result==0);
}


function register($email){
	//This function writes in the database the first row relative to the user
	$sessionId=session_id();
	$query="INSERT INTO `users` (session_id,email) VALUES ('$sessionId','$email')";
	mysql_query($query);
}

function restoreSession($email){
	//Function that allows a user to restart a new session;
   $sessionId=session_id();
	$query="UPDATE `users` SET session_id='$sessionId' WHERE email='$email'";
	mysql_query($query);
}

function runningStatus($user){
	#Function that returns the boolean true if the conditions are proper for the user, whose ID 
	#in input is given, to run the python code and then LBLRTM. Such conditions are: she did not
	#do it yet 10 times, and nobody else is doing it at the moment.
	$limit=10;
	$query="SELECT COUNT(*) FROM `users` WHERE runningLBLRTM='1'";
	$result=mysql_result(mysql_query($query),0);
	if ($result==0){
		$status="allowed";
	} else {
		$status="conlfict";
	}
   date_default_timezone_set("Europe/Lisbon");
	$date= date("Y-m-d");
 	$query1="SELECT * FROM `users` WHERE ID='$user' and date='$date'";
 	$result1=mysql_query($query1);
 	if ($result1){
	   $row1=mysql_fetch_assoc($result1);
	   if (intval($row1['number_times'])>$limit){
		   $status= "over limit";
	   }   
	}
	return $status;
}

function writeCsvSpectrum($filename,$spectrum) {
	//File that extracts the spectrum from a file like TAPE27 or TAPE28;
	//the format of the output is a csv file, wavelegtnh, transmission.
	//In the end this function was not used because we will not show spectra.
	//Still with a bug. ##Correct it if you want to use it.

	$i=0;
	$fileContent= fopen($filename,'r');
	$spectrumContent=fopen($spectrum,'w');
          
	fwrite($spectrumContent,"wavelenght,spectrum");
	while  (! feof($fileContent)){
		$row=fgets($fileContent);
		$data=explode(" ",$row);
		$i=1;
		foreach($data as $piece){
			if ($piece==""){
				continue;
			}
			if (! is_numeric($piece)){
				$i=0;
				break;
			}
			if ($i==1){
				$spectrumRow=$piece.",";
				$i++;
			}
			if ($i==2){
				$spectrumRow=$spectrumRow.$piece;
				$i++;
			}
		}
		if ($i==3){
			fwrite($spectrumContent,$spectrumRow);
		}
	}
	fclose($fileContent);
	fclose($spectrumContent);
   return $spectrum;
}

function signalRunning($user){
	#Function that records in the table users the fact that $user is 
	#running LBLRTM 

  	$query1="SELECT * FROM `users` WHERE ID='$user'";
	$row1=mysql_fetch_assoc(mysql_query($query1));
   date_default_timezone_set("Europe/Lisbon");
	$date= date("Y-m-d");
	if ($row1['date']==$date){
		$numberTimes=intval($row1['number_times']);
		$numberTimes=strval($numberTimes+1);
		$query2a="UPDATE `users` SET number_times='$numberTimes',    runningLBLRTM='1'   WHERE ID='$user'";
		mysql_query($query2a);
	} else {
		$query2b="UPDATE `users` SET number_times='1', date='$date', runningLBLRTM='1'   WHERE ID='$user'";
		mysql_query($query2b);
	}
}

function noLongerRunning($user){
	#Function that records in the table users the fact that $user is 
	#not running LBLRTM anymore. 
	$query="UPDATE `users` SET runningLBLRTM='0'   WHERE ID='$user'";
	mysql_query($query);
}
function showConfiguration($conf, $user,$mode="values"){
	//function that returns a string with all parameter values for a given 
	//configuration to be shown.
	//Whether only values are shown, or also all the description for all parameters,
	//depends on the value of the input parameter $mode: "values" (which is the 
	//default) for the former option, "descriptions" for the latter.
	$query="SELECT * FROM `parameters`";
	$result=mysql_query($query);
	$show="";
	while($row=mysql_fetch_assoc($result)){
		$parName=$row['name'];
		$value=getParField("value",$parName,$user,$conf);
		if (validParValue($value)){
         if (is_array($value)){
         	$showValue=$parName." has multiple values<br>";
         } elseif (strlen($value)<55) {
         	$showValue=$parName." has the value: ".$value."<br>";
         } else $showValue=$parName." has the value: ". substr($value,0,50)." ...<br>";
         $showValue=$showValue."message=    ".getParField("message",$parName,$user,$conf);
         if ($mode=='values'){
         	$show=$show.$showValue."<br><br>";
         }
      } else 
         $showValue="No value entered for ".$parName;
      if ($mode=="descriptions") {
 		   $show=$show. "name="           . $parName                              . "<br>";
			$show=$show.$showValue                                                 . "<br>";
			$show=$show. "record="         . getParField("parentRecord",$parName)  . "<br>";
			$show=$show. "dimesnion="      . getParField("dimension",$parName)     . "<br>";
			$show=$show. "default="        . getParField("defaultValue1",$parName) . "<br>";
			$show=$show. "allowed Values=" . getParField("possibleValues",$parName). "<br>";
			$show=$show. "description= "   . getParField("description",$parName)   . "<br>";
			$show=$show. "block=       "   . getParField("block",$parName)         . "<br>";
			$show=$show. "function= "      . getParField("function",$parName)      . "<br>";
			$show=$show. "<br>";
			$show=$show. "<br>";
			$show=$show. "<br>";
      }
	}
//	echo $show;
//	die();
	return $show;
}

function wrongDimension($list,$dimension){
	#Function that checks whether $list is a number of values separated by a coma
	#and whether that number matches $dimension
	$arrayList=explod(",",$list);
	return (sizeof("")!=intval($dimension));
}

function checkParameterValue($parName,$user){
	//Function that checks whehter the input parameter is OK to
	//be used to build the input for LBLRTM or has some major problem.
	$recordId=getParField("records_id",$parName);
	$warning="";
	if (toBeSkipped($recordId)){
		$warning="This parameter belongs to a record that should be skipped according to the values of the other parameters. ";
	}
	$recordDimension=getParField("record_dimension",$parName);
	$numericalRecordDimension=numericalValue($recordDimension);
	if ($numericalDimension===NULL){
		$warning=$warning."The parameters necessary to establish the dimension of the parent Record of this parameter are not set. ";
	}
  
  
   $values=getParField("value",$parName);
   $parDimension=getParField("dimension",$parName);
   $numericalParDimension=numericalValue($parDimension);
   if ($numericalParDimension===NULL) {      
	   if (is_array($values)){
	   	if (wrongDimension($values,$parDimension)){
	   		$warning=$warning."The dimension of the entry does not match the supposed dimension of the parameter";
	   	}
	   } else 
			foreach ($values2d as $values1d){
				foreach($values1d as $entry){
					if (wrongDimension($entry,$parDimension)){
						$warning=$warning."For at least one case the dimension of the entry does not match the supposed dimension of the parameter. ";
						break;
					}
				}
			}
	} else 
	   $warning=$warning."The parameters necessary to establish the dimension of this parameter are not set. ";


   $lastRepetitionRow=getLastRepetitionRow($parName,$user);
   if ($numericalDimension>1 and $lastRepetitionRow!= $numericalDimension){
    	$warning=$warning."The number of times this parameter has been entered does not match the dimension of the Record";
   }
    
	if ($warning==""){
		$message=$warning;
		$valid=false;
	} else {
	   $message='OK';
	   $valid=true;
	}
	
	$query="UPDATE `".$parName."` SET message='$message' WHERE user_id='$user' and conf='0' and message='not yet validated'";
	mysql_query($query);	
	return $valid;
}

function checkParameterValues($user){
	//Function that assigns the proper message in the database to all the parameters entered by the user.
	//thus signaling the possible error. Actually, this function only calls, for all 
	//parameters, the function checkParameterValue that does the job for the parameter 
	//given in input.
	$query="SELECT * FROM `parameters`";
	$result=mysql_query($query);
	while($row=mysql_fetch_assoc($result)){
		$parName=$row['name'];
		$value=getParField("value",$parName,$user);
		if (validParValue($value)){
         checkParameterValue($parName,$user);
      }	
	}
}

function lastConfiguration($user,$action="do not save"){
//Function that returns the number of the last configuration of parameters
//saved by the user, and, if required by setting $mode="save",
//saves the present configuration of parameters 

  $query="SELECT name from `parameters`";
  $lastConfiguration=0; 
  $result=mysql_query($query);
  while($row=mysql_fetch_array($result)){
  	  $tabName=$row['name'];
     $query1="SELECT MAX( conf ) FROM `".$tabName."` WHERE user_id = '$user'";
     $result1=mysql_query($query1);
     if ($result1){
     	  $thisLastConf=mysql_result($result1,0);
     	  $thisLastConf=intval(trim($thisLastConf));
     	  if ($thisLastConf>$lastConfiguration){
     		$lastConfiguration=$thisLastConf;
     	  }
     }
  }

  if ($action=="save"){
	  $query="SELECT name from `parameters`";
	  $result=mysql_query($query);
	  $currentConfiguration=$lastConfiguration+1;
	  while($row=mysql_fetch_array($result)){
	  	$name=$row[0];
	  	$query="UPDATE `".$name."` SET conf =". strval($currentConfiguration)." WHERE conf =0 AND user_id='".$user."'";
	   mysql_query($query);
	  }
	  return $currentConfiguration;
  }

  return $lastConfiguration;
  
}


function assignDefaults ($default,$user) {
   //Function that assign to all parameters their default values, which are stored in the table `parameters`
   //The defaults are always assigned to single values, so the process is easy.
   $defaultValue="defaultValue".$default;
   $query="SELECT * from `parameters`";
   $result=mysql_query($query);
   while($row=mysql_fetch_assoc($result)){
   	$value=$row[$defaultValue];
  		$name=$row['name'];
   	if (! validParValue($value)){
   		continue;
   	}
	   $message='OK';
	   $query1="SELECT COUNT(*) from `".$name."` WHERE user_id='".$user."' and conf='0'";
	   $numRows1=mysql_result(mysql_query($query1),0);
	   if($numRows1>0){
	   	$query2="UPADE `".$name."` SET value='".$value."', message='OK' WHERE user_id='".$user."' and conf='0'";
	   } else {
	   	$query2="INSERT INTO `".$name."` (value, message, user_id, conf) VALUES ('".$value."', '".$message."', '".$user."', '0')";
	   }
	   mysql_query($query2);
   }
}


function removeNonValidValues(){ 

   //Function to remove all non valid values from the database.
   //Based on the message, it spots the parameters where a mistake
   //was done and the value is turned into "# no value"


	$query="SELECT name from `parameters`";
	$result=mysql_query($query);
	while($row=mysql_fetch_array($result)){
		$name=$row[0];
		$query1="UPDATE `".$name."`  SET value = '# no value' WHERE ";
		$query1=$query1." message not like '' and message not like 'OK' and message not like 'not yet validated'";
	   mysql_query($query1);
	}
}

function times($i){
	//Function that transforms a number in a string with a sentence telling how many
	//times correspond to the input number. 
	if(trim($i)=="1"){
		return "once";
	}
	
	if(trim($i)=="2"){
		return "twice";
	}
	
	return $i . " times";
}

function ordinal($i){
	#Fucntion that transforms a number into an ordinal, by simply adding a th in the end,
	#or "st", or "nd", or "rd" if the number last digit is 1, 2 or 3 respectively. 
	if (! is_numeric($i)){
		return $i;
	}
	if ($i > 3 and $i<21){
		return $i . "th";
	}
	switch(lastDigit($i)){
		case "1":
		   return $i . "st";
		   break;
		case "2":
		   return $i . "nd";
		   break;
      case "3":
         return $i . "rd";
         break;
      default:
         return $i . "th"; 
         break;
	}
}

function lastDigit($i){
	#Function that returns the last digit of a number, for example 3
	#if the number is 33443, 4 if the number is 54.
	if (! is_numeric($i)){
		return $i;
	}
	return substr($i,-1);
}

function getLastRepetition($parName,$user,$conf='0'){
	//Function that reads from the database the last
	//repetition of the parameter value, i.e. the number of
	//repetitions already input of a set of Records, to which
	//the parameter belongs, to be indefinetely repeated 
	//those between --- and +++ in lines.
   $set          =getParField("block"          ,$parName);
   if($set=='0'){
   	return 0;
   } else {
   	$countRepQuery="SELECT MAX( repetition ) FROM `".$parName."` WHERE user_id = '$user' and conf='$conf'";
   	$result=mysql_query($countRepQuery);
   	if ($result){
   		$lastRepetition=mysql_result($result,0);
   		return intval($lastRepetition);
   	} else return 0;
   }
}

function getLastRepetitionRow($parName,$user,$conf='0'){
	//Function that reads from the database the last
	//repetitionRow (beware, this is different from repetition) of the parameter value, 
	//i.e. the number of repetitions already input in a Record that has
	//dimension larger than 1, to which the parameter in input belongs. In this case
	//the maximum dimension, contrary to the case of getLastRepetition,
	//is set in advance, it is stored in the database. In
	//this function we read how many entries of it are already stored
	//in the database.
   $set          =getParField("block"          ,$parName);
   if($set=='0'){
   	return 0;
   } else {
   	$countRepQuery="SELECT MAX( repetitionRow ) FROM `".$parName."` WHERE user_id = '$user' and conf='$conf'";
   	$result=mysql_query($countRepQuery);
   	if ($result){
   		$lastRepetition=mysql_result($result,0);
   		return intval($lastRepetition);
   	} else return 0;
   }
}

function editValue($parName,$value,$message,$user,$repetition,$repetitionRow){
	//function that adds/changes an entry to the database, namely in 
	//the table relative to the parameter whose name is given in input
	//along with the value, the user id,  and the message to be associated. 
   //The edition only concern the working session, i.e. rows with 
   //conf='0', and not the stored sessions. 
   //If repetition is 0, then old values have to be erased before insterting the new ones.
   //If the last parameter, instead of being a number, is set to the 
   //string "addRep", then the repetition with the highest present number
   //plus one is added.
   //If the last parameter, instead of being a number, is set to the 
   //string "changeLastRep", then the repetition with the highest present number
   //is removed and a one witht the same number is added.
   //Otherwise the values given in input are just inserted into the table.
   
  	$deleteQuery="DELETE FROM `".$parName."` WHERE conf='0' and user_id='$user' and repetition='$repetition' and repetitionRow='$repetitionRow'";
  	mysql_query($deleteQuery);
   	
   if(validParValue($value)){
		$newValues="$value','0', '$message', '$user', '$repetition', '$repetitionRow";
		$query2="INSERT INTO `".$parName."` (value, conf, message, user_id, repetition, repetitionRow) VALUES ('$newValues')";
		mysql_query($query2);

		$twinPars=twinPars($parName);
	   
		foreach ($twinPars as $name){
			$repetition=getLastRepetition($name,$user);
			$repetitionRow=getLastRepetition($name,$user);
			$query1="UPDATE `".$name.   "` SET value='$value', message='$message' WHERE conf='0' and repetition='$repetition' and user_id='$user' and repetitionRow='$repetitionRow'";
			mysql_query($query1); 
			$query1a="UPDATE `".$name.   "` SET write='0' WHERE conf='0' and user_id='$user'";
			mysql_query($query1a); 
		}
		
		if (! empty($twinPars)){
			$query2="UPDATE `".$parName."` SET write='1' WHERE conf='0' and user_id='$user'";
			mysql_query($query2); 
		}	
	}
}

function resetAll(){
	//Function to be used only by the administrator in special case to reset all the system.
	$query="SELECT name from `parameters`";
	$result=mysql_query($query);
	while($row=mysql_fetch_array($result)){
		$name=$row['name'];
		$query1="truncate table `".$name."`";
		mysql_query($query1);
	}
}


function resetParameter($parName,$user){
	//Function that erases the value previously entered by the user
	//in the current working session, i.e. conf='0', for the given parameter.
	$query="DELETE from `".$parName."` WHERE conf='0' and user_id ='$user'";
	mysql_query($query);
}

function deleteLastValue($parName,$user){
	//Function that erases the value previously entered by the user
	//in the current working session, i.e. conf='0', for the given parameter
	//and repetition equal to the last one. For parameters not belonging to 
	//blocks to be repeated, it is equivalent to resetParameter.
   $lastRepetition=getLastRepetition($parName,$user);
	$query="DELETE from `$parName` WHERE conf='0' and user_id ='$user' and repetition='$lastRepetition'";
	mysql_query($query);
}

function resetValues($user){
	//Function that cleans all parameters and removes all values of the working session (conf=0).
	$query="SELECT name from `parameters`";
	$result=mysql_query($query);
	while($row=mysql_fetch_array($result)){
		$name=$row['name'];
		$query1="DELETE from `".$name."` WHERE user_id='".$user."' and conf='0'";
		mysql_query($query1);
	}
}

function twinPars($parName){
	//function that returns an array with all the "twin parameters"
	//of the parameter given in input. The twin parameters are those
	//who have the same name except for a $$$ followed by the record
	//number. They are not supposed to have different values.

   $twins=array();
	$ss=explode("$$$",$parName);
	$baredParName=$ss[0];
	$query="SELECT name from `parameters` WHERE name = '$baredParName' or name LIKE '".$baredParName."$$$%'";
   $result=mysql_query($query);
   while ($row=mysql_fetch_assoc($result)){
   	if ($row['name']!=$parName){
   	   $twins[]=$row["name"];
   	}
   }
   return $twins;
}



function displayPars1(){
	//Function to display the parameters that uses the database ##update
   echo "<p>";
   $query ="SELECT name FROM `parameters`";
   $queryResult=mysql_query($query);
   while ($row=mysql_fetch_array($queryResult)){
   	$name=$row['name'];
   	$query1="SELECT value from `".$name."`";//you should eventually add something like WHERE code($user)=code($session) 
   	$result1=mysql_query($query1);
   	if ($result1){
	   	$row1=mysql_fetch_array($result1);
	   	if (validParValue($row1["value"])){
	         echo "name="           . $name         . "<br>";
		      echo "value="          . $row1['value']        . "<br>";
		      echo "<br>";
		   }
	   }
	   //non passa al successivo
	}
  echo "</p>";
}


function validParValue($value){
	//function that returns true if the input is a valid 
	//parameter value, false otherwise.
	if (is_array($value)){
		foreach ($value as $el){
			if (is_array($el)){
				foreach ($el as $rEl){
					if (trim($rEl)!="" and trim($rEl," \n")!='# no value' and $rEl!="erase" and trim($rEl)!="None"){
						return true;
					}
				}
			} elseif (trim($el)!="" and trim($el," \n")!='# no value' and $el!="erase" and trim($el)!="None" and $el!==NULL){
				return true;
			}
		}
		return false;
	}
	if (trim($value)=='0'){return true;}
	return (trim($value)!="" and trim($value," \n")!='# no value' and $value!="erase" and trim($value)!="None");
}



function invalid($message){
  //boolean function that returns true if the message refers to a non valid value.
  //It only returns false in 3 cases: the message is OK, the message is empty (means no value was given) or
  //the message says that the value was not yet validated by writeinput. 
  //The function is more complicated than necessary, because I am trying to fix some bugs.
  //The bugs are still there: 27 Aug 2014, 11h55
  $str=$message;
  if (strpos("not yet validated",$str)!==false){
  	  return false;
  }
  if ($str==""){
  	  return false;
  }
  if (strpos("OK",$str)===false){
  	  return true;
  }
  //At this point, if the function did not return
  //there is an OK in the message. The message is a
  //warning only in case it is long.
  if (strlen($str1)>4){
     return true;
  }
  return false;
}



function clean ($value, $format){
	$s1=mysql_real_escape_string($value);
	if (strpos($format,"A")!==false){
		$s2=preg_replace('~[^\p{L}\p{N}]++~u', ' ', $s1);
		return $s2;
	}
	if (strpos($format,"I")!==false){
		$s2=preg_replace('~[^\p{N}]++~u', '', $s1);
		return $s2;
	}
	$s2=preg_replace('~[^\p{N}]++~u', ',', $s1);
	$ss=explode(",",$s2);
	$time='first';
	$result="";
	foreach($ss as $part){
		if ($part==""){
			continue;
		}
		if ($time=='first'){
   		$result=$result.$part;
   		$time='next';
   	} else {
			if ($part==""){
				break;
			}
			$result=$result.".".$part;
			break;
		}
	}
	return $result;	
}

function turnIntoArray($range){
	//functions that transforms the string with the allowed values of some parameters,
	//which is of  of the type (1,2,3,7/9)
	//into an array of values, in this case like [1,2,3,7,8,9]
	$range=trim($range," ()");
	$range=explode(",",$range);
	$range_=array();

	foreach ($range as $value){
		if (!strpos($value,"/")){
			$range_[]=$value;
		} else {
			$minMax=explode("/",$value);
			$a=$minMax[0];
			$b=$minMax[1];
         if (! is_numeric($a)){
          	$a=valueOf($a);
        	}
         if (! is_numeric($b)){
           	$b=valueOf($b);
         }				
			for ($i=$a;$i<=$b;$i++){
				$range_[]=$i;
			}
		}
	}
	return $range_;
}

function lowerLimit($description){
	//Function that takes as input the description of a paramater,
	//which are written in detTAPEall.ini, 
	// and checks whether the pieces of description relative to the
	// possible values are all the same from some limit on. Like in the case of
	//NMOL, in which there is one single detailed description  for
	//all the values from 1 onwards, and this description starts with 
	//<1: In cases like this, this function returns the value 1
	//In cases without lower limit, it returns false.
	if (strpos($description,">")===false){
		return false;
   }
	$possibleLoweLimitsStr=explode(">",$description);
	foreach ($possibleLoweLimitsStr as $possibleLoweLimitStr){
		if (strpos($possibleLoweLimitStr,":")===false){
			continue;
		}
		$possibleLowerLimit=explode(":",$possibleLoweLimitStr);
		if (is_numeric($possibleLowerLimit[0])){
			return $possibleLowerLimit[0];
		}
	}
}
                                      
function getRecordField($field,$recordId){
	#Function that returns the value of the given parameter entered
	#by the user in the current working configuration, i.e. for conf=0.
	#the input variable $user is unnecessary if the field is not value or message,
	#therefore it is set for default to the null string.
	          

	#The parameter could be in the middle of the list without index, 
	#or with index in the middle or in the end of the list
	$query="SELECT $field from `records` WHERE ID='$recordId'";
	#echo "####".$query."###";
	$result=mysql_query($query);
 	$rows=mysql_fetch_assoc($result);
 	return $rows[$field];
  
}

function numericalValue($expression){
	#Function that transforms a string that expresses a dimension
	#of a string into a value. 
	#If it's a number, then the only thing to do is to return its 
	#value into an appropriate format. Otherwise evaluate the expression.
	$assignment="return " . $expression .";";
	$value=eval($assignment);
	if ($value==false) {return NULL;} 
   return $value;
}	

function failedPars($expression){
   #function that returns the parameters involved in an expression given in input 
   #for which a value could not be gotten. It returns false in case 
   if (is_numeric($expression)){
   	return false;
   }
   $pars=parametersInvolved($expression);
   $failedPars=array();
   $somePar=false;
   foreach($pars as $par){
   	if (valueOf($par)===NULL){
   		$failedPars[]=$par;
   		$somePar=true;
   	}
   }
   if ($somePar){
      return $failedPars;
   } else return false;
}

function writingFlag($parName,$user,$conf){
	#Function that returns true if the parameter can to be written on TAPE5;
	#false if it is just a twin parameter with some value copied from its sibling.

	$query1="SELECT COUNT(*)   FROM `".$parName."` WHERE user_id='".$user."' and conf='$conf'";
	$result1=mysql_query($query1);
	if ($result1){
		$count=mysql_result($result1,0);
		if ($count==0){
		   return false;
		}
	} else 
	   return false;
	$query="SELECT *   FROM `".$parName."` WHERE user_id='".$user."' and conf='$conf'";
	$result=mysql_query($query);
	$row=mysql_fetch_assoc($result);
	if (! array_key_exists("write", $row) ){
		return true;
	}
	return ($row['write']=='1');
}

	
function getParField($field,$parName,$user='',$conf='0'){
	#Function that returns the value of the given parameter entered
	#by the user and saved in a given configuration. If no configuration number
	#is given in input, then by default getParField returns the value relative 
	#to the current working configuration, for which conf=0.
	#The input variable $user is unnecessary if the field is not value or message,
	#therefore it is set for default to the empty string.
	#The field can given as input parameter to this function can also be a 
	#field of the table records, in which case the value returned is that of
	#the parent record of the parameter given in input.

	#***IF THE FIELD REQUESTED IS "value"*** THEN THE EITHER FOLLOWING CASES RELATED TO THE 
	#DIMENSION IS POSSIBLE:
	#
	#
	#CASE1, The parent record has dimension 1 (either by default or because the parameter 
	#that sets the dimension is 1 ) and it does not belong to a set that could be repeated:
	#
	# In this case $value is not an array, it is a single value.
	# If the parameter has dimension>1, $value will be a string with all the entries separated by
	# a coma, but it will have dimension 1 for php.
	#
	#
	#CASE2, either the parent record has dimension >1 or it belongs to a set of records to be 
	#repeated:
	#
	# In this case $value is an array with two indices, the first indicating the repetition of 
	# the record, i.e. the field 'repetition' in the table `records` possibly 0 if it does not 
	# belong to a set to be repeated, the second indicating the index of the record, i.e. the 
	# field 'repetitionRow' in the table `records`.

   $query1="SELECT * from `parameters` WHERE name ='$parName'";
   $row1=mysql_fetch_assoc(mysql_query($query1));
   $recordId=$row1['records_id'];
   if ($field=='records_id'){return $recordId;}
	$query2="SELECT * from `records` WHERE ID='$recordId'";
   $row2=mysql_fetch_assoc(mysql_query($query2));

   if ($field=='parentRecord'){return $row2['name'];}


   if (in_array(($field),array("block","record_dimension","format","conditions","file","parameter_list"))){
 	   return $row2[$field];
   }	
   
   if (in_array(($field),array("description","possibleValues","dimension","function","ID")) or strpos($field,"defaultValue")!==false){
	   $query3="SELECT  * from `parameters` WHERE name= '$parName'";
	   $result3=mysql_query($query3);
	  	$row3= mysql_fetch_assoc($result3);
	  	return $row3[$field];
   }
	

	$countRepQuery="SELECT MAX( repetition ) FROM `".$parName."` WHERE user_id = '$user' and conf='$conf'";
   $resultCount=mysql_query($countRepQuery);
	if ($resultCount){
		$lastRepetition=mysql_result($resultCount,0);
   } else $lastRepetition=0;


	if ($field=="value") {
      if ($lastRepetition==0){
			$query4="SELECT *   FROM `".$parName."` WHERE user_id='$user' and conf='$conf' and repetition='0'";
			$result4=mysql_query($query4);
			if ($result4){
				$value=array();
				$i=0;
				while($row4=mysql_fetch_array($result4)){
					if (validParValue($row4["value"])){
						$value[0][$row4['repetitionRow']]=$row4["value"];
		    		   $i++;
	    		   }
	    		}
	    		if ($i==0){ return "# no value";	}
	    		if ($i==1){ return $value[0][0];	} else return $value;
	    	} else return "# no value";
      } else {
      	$value=array();
      	for ($repetition=1;$repetition<=$lastRepetition;$repetition++){
      		$query4b="SELECT *   FROM `".$parName."` WHERE user_id='".$user."' and conf='$conf' and repetition='$repetition'";
      		$i=0;
      		$result4b=mysql_query($query4b);
       		while($row4b=mysql_fetch_assoc($result4b)){
      		   $value[$repetition][$row4b['repetitionRow']]=$row4b["value"];
      		}
      	}
      	return $value;
      }
	} 


	if ($field=="message") {
		$query5="SELECT *   FROM `".$parName."` WHERE user_id='$user' and conf='$conf' and repetition='$lastRepetition'";
		$result5=mysql_query($query5);
		if ($result5){
    		$row5=mysql_fetch_array($result5);
    		return $row5["message"];
    	} else return "";
	} 
   die("unknown field ". $field);
}

function parameterFormat($parName){
	//Function that returns the format for the specific parameter
	//Necessary to see check the values and prevent the entry of
	//unwanted ones.
	
	$ID=getParField("ID",$parName);
   $parList=getParField("parameter_list",$parName);
   $format =getParField("format",$parName);
   if (strpos($format,"!cond")!==false){
   	$formats=explode("///",$format);
   	$condition=$formats[1];
   	$condition=str_replace('!cond{',"\$satisfied= (",$condition);
   	$condition=str_replace("}",")",$condition);
   	$condition=$condition.";";
   	eval($condition);
   	if ($satisfied){
   		$format=$formats[0];
   	} else 
   	   $format=$formats[2];   	
   }
   $parFormats=explode(", ",$format);
   $recordPars=explode(" ",$parList);
   $i=0;

   foreach ($parFormats as $thisFormat){
   	if (strpos($thisFormat,"X")!==false){
   		continue;
   	}
   	if ($ID==$recordPars[$i]){
   		return $thisFormat;
   	}
   }
   return "failed search";
}

function restoreConfiguration($conf,$user){
	//Function that restores the configuration, whose number
	//is passed as input, for the $user also specified in input.
	//It simply copies all values from the database associated to
	//that particular configuration in a new row, but in this new
	//row the configuration number has changed from the input one
	//to 0, which means the current working value.
	
	resetValues($user);
	$query="SELECT name FROM `parameters`";
   $result=mysql_query($query);
   while($row=mysql_fetch_array($result)){
		$name=$row[0]; #name is the parameter name, and the name of the relative table.
		$query="UPDATE `".$name."` SET conf='0' WHERE user_id='".$user."' and conf='".$conf."'";
		mysql_query($query);
   }	
}

function getNext($list,$item,&$isTheLast=false){
	//return the item of a list which comes right
	//after the given item. If the item in input
	//is the last one, the output is the first of 
	//the list and the boolean variable $isTheLast 
	//is set to true.
	if (end($list)==$item){
		reset($list);
		$isTheLast=true;
		return current($list);
   }
	$isTheLast=false;
	reset($list);
	while (current($list)!=$item){
		next($list);
	}
	next($list);
	return current($list);
}

function fromNumberToDate($numberOfDays){
	//Function that transforms a number from 1 to 366
	//into a date in the format DD/MM
	if ($numberOfDays==0){
		return "DD/MM";
	}
	$months=array(31,29,31,30,31,30,31,31,30,31,30,31);
	$MM=1;
	$daysInPreviousMonths=0;
	foreach ($months as $month){
		if ($numberOfDays<=$daysInPreviousMonths+$month){
			break;
		}
		$MM++;
		$daysInPreviousMonths=$daysInPreviousMonths+$month;
	}
	$DD=$numberOfDays-$daysInPreviousMonths;
	if ($DD<10){
		$date="0".$DD."/";
	} else $date=$DD."/";
	if ($MM<10){
		$date=$date . "0".$MM;
	} else $date=$date.$MM;
	return $date;
}                  

function fromDateToNumber($date){
	//Function that transforms a date in the format DD/MM
	//into a number from 1 to 365
	$daysInMonth=array(31,29,31,30,31,30,31,31,30,31,30,31);
	$date=str_replace("-","/",$date);
	$date=str_replace(" ","",$date);
	$DD_MM=explode("/",$date);
	if (count($DD_MM)!==2){
		return 0;
	}
	$DD=clean($DD_MM[0],"I");
	$MM=clean($DD_MM[1],"I");
	if (! is_numeric($DD) or ! is_numeric($MM)){
		return 0;
	}
	if (intval($MM)!=$MM or $MM>12 or $MM<1 or intval($DD)!=$DD or $DD<1 or $DD>$daysInMonth[$MM-1]) {
		return 0;
	}
	$monthsPassed=1;
	$numberOfDays=0;
	foreach($daysInMonth as $month=>$days){
		if($month+1==$MM){
			break;
		}
		$numberOfDays=$numberOfDays+$days;
	}
	$numberOfDays=$numberOfDays+$DD;
	return $numberOfDays;
}

function toBeSkipped($record){
	//function that checks whether the condition for the record
	//in input is satisfied or not.
   $query="SELECT * from `records` WHERE ID='$record'";
   $row=mysql_fetch_assoc(mysql_query($query));
   $condition=$row['conditions'];   
   $name=$row['name'];
   if (trim($condition)==""){##this condition should be unnecessary when
                             ##everything is fine.
   	return false;
   }
  
   $dimension=$row['record_dimension']; 
	$assignment="\$numericalDimension=(".$dimension.");";
   eval($assignment);
   if ($numericalDimension===NULL){
    	return true;
   }

	$assignment="\$result=(".$condition.");";
	eval($assignment);
   return (! $result);
}

function controlRecord($recordId){
	//function that returns true if the record in input is a control one,
	//not to be displayed.
   $query="SELECT name from `records` WHERE ID='$recordId'";
   $row=mysql_fetch_assoc(mysql_query($query));
   $recordName=$row['name'];
   if (strpos($recordName,"control parameter")!==false){
   	return true;
   }
   return false;
}

function parametersInvolved($condition){
	#Function that returns an array of strings with the names
	#of the parameters involved in the condition given in input.
	$parNames=array();
	if (trim($condition)=="true"){
		return $parNames;
	}
   $condition1=str_replace("in_array(","",$condition);
	$firstDiv=explode('valueOf("',$condition1);
	foreach ($firstDiv as $stringElement){
		$subElements=explode('")',$stringElement);
		$parName=$subElements[0];
		if ($parName!=""){
			$parNames[]=$parName;
		}
	}
	return $parNames;
}

function lastEntry($values){
   #The value or the message as returned by getParField could be an array.
   #This function extracts the last element
   if (is_array($values)){
   	$value=array_pop($values);
   	if (is_array($value)){
   		$value1=array_pop($value);
   		$value=$value1;
   	}
   } else $value=$values;
   return $value;
}

function valueOf($parName){
	#Function that returns the value of the parameter whose name
	#is passed as input parameter.
	#if the value is numeric, it is in every case an integer, and
	#the integer value is given.
	#if it is a string, it must be checked whether it is a string, 
	#first value of it is taken in this case
	#if it is an array, its sum is taken (for the case of IREG).
	
	$user=getUser();

	$values = getParField("value",$parName,$user);
   //extract the last repetition and last record.
   $value=lastEntry($values);

	if (is_numeric($value)){
		return (intval($value));
	} elseif($value!="# no value" and strpos($value,",")===false){
		return $value;
	}


	if ($value=="# no value"){
		$twinPars=twinPars($parName);
		foreach($twinPars as $twinPar){
			$values = getParField("value",$twinPar,$user);
			$value=lastEntry($value);
			if (is_numeric($value)){
				return (intval($value));
			} elseif ($value!="# no value" and strpos($value,",")===false){
				return $value;
			}
	   }
	   return NULL;
   }

	$values=explode(",",$value);
	$sum=0;
	foreach($values as $element){
		$elementValue=trim($element);
		if (! is_numeric($elementValue)){
			return NULL;
		}
		$sum=$sum+intval($elementValue);
	}
	return $sum; #this return is used in case of IREG, which is a list.

}

function strValueOf($parName){
	#Function that returns the value of the parameter whose name
	#is passed as input parameter. It has to be called only in
	#the case such value has to be treated like a string, i.e. 
	#for JLONG.

   $user=substr(md5(session_id()),0,20); 
	$value = getParField("value",$parName,$user);
   return lastEntry($value);
}

function nextPar($parName){
	//Function that, given a parameter name,
	//returns the next in the due order.
	//it returns false in case the parameter
	//is not found.
	$parameterList=getPars();
	$i=array_search($parName,$parameterList);
	if ($i===false){
		return false;
	}
   if($i==sizeof($parameterList)-1) {
   	return false;
   }
   return $parameterList[$i+1];
}


function getPars(){
   //function that returns the list of all parameters.  
   //Such list is stored in the file "parList", which can be produced
   //with the bash line:  grep -F "[" detTAPEall.ini | grep -v cond  

   $query="SELECT * FROM `parameters`";
   $result=mysql_query($query);
   $parList=array();
   while($row=mysql_fetch_assoc($result)){
   	$parList[]=$row['name'];
   }
   return $parList;
}

function enteredValues($post,$format){
   //function that checks whether or not (returning true or false)
   //the input, $post, which is actually a $_POST of a form,
   //corresponds to the form for entering values for a parameter.
   //In such case, values are supposed to correspond to the keys of 
   //the kind: value_n_m. This function returns true if there
   //is at least on of this kind.
   foreach($post as $key=>$entry){
   	$entry=clean($entry,$format);
   	$keyName=explode("_",$key);
   	if ($keyName[0]=="value" and count($keyName)==3){
   		return true;
      }
   return false;
}
}

function missingValues($post,$format){
   //function that checks whether or not (returning true or false)
   //all values are correctly given in input. The input of this
   //function is $post, which is actually a $_POST of a form.
   //All the values are supposed to correspond to the keys of 
   //the kind: value_n_m

   foreach($post as $key=>$entry){
   	$entry=clean($entry,$format);
   	$keyName=explode("_",$key);
   	if ($keyName[0]!="value" or count($keyName)!=3){
   		print_r($keyName);
   		continue;
   	}
   	if (trim($entry)=="") {
   		return true;
      }
   }
   return false;
}


function extractValue($post,$format){
   //function that turns the input $post, which is actually 
   //a $_POST of a form, into an actual value to be stored in
   //a database.
   //All the values are supposed to correspond to the keys of 
   //the kind: value_n_m
   if (sizeof($post)==1){
   	$value=$post["value_0_0"];
   	return clean($value,$format);
   }


   $value=array();
   $thisRecordEntry="";
   ksort($post);
   $previousRecord="0";
   $firstOne=true;
   $lastRecord=0;
   foreach($post as $key=>$nonCleanedEntry){
   	$entry=clean($nonCleanedEntry,$format);
   	$keyName=explode("_",$key);
   	if ($keyName[0]!="value" or count($keyName)!=3){
   		continue;
   	}
   	$record=$keyName[1];
   	$lastRecord=max(array($lastRecord,$record));
   	if ($record==$previousRecord){
   		if($firstOne){
   	      $thisRecordEntry=$entry;
   	      $firstOne=false;
   	   } else $thisRecordEntry=$thisRecordEntry.", ".$entry;
   	} else {
   		 $value[]=$thisRecordEntry;
   		 $thisRecordEntry=$entry;
   		 $previousRecord=$record;
   		 $firstOne=true;
   	}
   }
  $value[]=$thisRecordEntry;

  return $value;
}


function  readFromNgtAtm ($dataRequired,$fileName){
	#This function reads the profiles for a given molecule or physical parameter 
	#from the model atmosphere.
   #It is necessary to build lists for height, pressure and molecules:  
   #MID-LATITUDE NIGHT PROFILES 
   #Set hydros.eqlbm pressure profile for Lat= 45.0 
   #using program HYDATM v.20FEB01  
   #03-JAN-2001  JJR  VERSION 3.0                                          
   #Originator: J.J. Remedios (EOS, Space Research Centre, Leicester, U.K.)
   #                 http://www-atm.physics.ox.ac.uk/RFM/atm               

   $dataString=file_get_contents($fileName);

   $needle="*".$dataRequired;
   $start=strpos($dataString,$needle);
   if ($start===false){
   	return "# no value";
   }
   $relevantData=substr($dataString,$start);
   $start1=strpos($relevantData,"]");
   $relevantData=substr($relevantData,$start1+1);
   $relevantData=substr($relevantData,0,strpos($relevantData,"*"));
   $dataPieces=explode(" ",$relevantData);
   $cleanData=array();
   
   foreach ($dataPieces as $piece){
   	if (trim($piece)!=""){
   		$clean=clean($piece,"F");
   		$cleanData[]=$clean;
   	}
   }

   return $cleanData;
}


function getUnits($property,$fileName){
	#Function that returns the string containing the unit of measure
	#associated to data about the property (molecule, temperature or pressure)
	#given in input, stored in the file whose name is also given in input.
	$data=file_get_contents($fileName);
   $fromLine=substr($data,strpos($data,'*'.$property.' ['));
   if ($fromLine===false){return ' ';}
   $start=strlen($property)+3;
   $stop=strpos($fromLine,']');
   $len=$stop-$start;
   $line=substr($fromLine,$start,$len);
   return $line;
}

function unitFlag($unit){
	#Function that returns the flag character associated with the 
	#unit of measure given in input, to build JCHAR.
	#directly from table 1 of the instructions of the LBLRTM code.
	switch($unit){
		case "ppmv":
		   return 'A';
		   break;
		case  "cm-3":
		   return 'B';
		   break;
		case  "gm/kg":
		   return 'C';
		   break;
		case  "gm m-3":
		   return 'D';
		   break;
		case  "mb":
		   return 'E';
		   break;
		case  "K":
		   return 'F';
		   break;
		case  "C":
		   return 'G';
		   break;
		case  "percent":
		   return 'H';
		   break;
		default:
			$MODEL=valueOf("MODEL");
      	if ($MODEL>=1 and $MODEL<=6){
      		return strval($MODEL);
      	} else return '-';##put the appropriate symbol here
	}
/*
        JCHAR = 1-6           - default to value for specified model atmosphere

              = " ",A         - volume mixing ratio (ppmv):

              = B             - number density (cm-3)

              = C             - mass mixing ratio (gm/kg)

              = D             - mass density (gm m-3)

              = E             - partial pressure (mb)

              = F             - dew point temp (K) *H2O only*

              = G             - dew point temp (C) *H2O only*

              = H             - relative humidity (percent) *H2O only*

              = I             - available for user definition

*/	
	
}

function firstOfRecord($parName){
	//Function that returns true in case the parameter whose name
	//is passed as input is the first of its record, false otherwise.
	$query="select * from `parameters` where name='$parName'";
	$result=mysql_query($query);
	$row=mysql_fetch_assoc($result);
	$recordsId=$row['records_id'];
	$parId=$row['ID'];
	$query1="select count(*) from `records` where ID='$recordsId' and parameter_list like '".$parId.",%' or parameter_list = '$parId'";
	$result1=mysql_query($query1);
	$counts=mysql_result($result1,0);
	return ($counts==1);
}

function lastOfRecord($parName){
	//Function that returns true in case the parameter whose name
	//is passed as input is the last of its record, false otherwise.
	$query="select * from `parameters` where name='$parName'";
	$result=mysql_query($query);
	$row=mysql_fetch_assoc($result);
	$recordsId=$row['records_id'];
	$parId=$row['ID'];
	$query1="select count(*) from `records` where ID='$recordsId' and parameter_list like '%, ".$parId."' or parameter_list = '$parId'";
	$result1=mysql_query($query1);
	$counts=mysql_result($result1,0);
	return ($counts==1);
}

function firstOfSet($parName){
	//Function that returns true in case the parameter whose name
	//is passed as input is the first of its set of Records, false otherwise.
	if (! firstOfRecord($parName)){return false;}
	$set          =getParField("block"          ,$parName);
	if ($set=='0'){return false;}
	$query="select ID from `parameters` where name='$parName'";
	$parId=mysql_result(mysql_query($query),0);
	$previousParId=strval(intval($parId)-1);
 	$query1="select name from `parameters` where ID='$previousParId'";
	$previousParName=mysql_result(mysql_query($query1),0);
	$previousSet=getParField("block"          ,$previousParName);
	return ($previousSet!=$set);
}

function lastOfSet($parName){
	//Function that returns true in case the parameter whose name
	//is passed as input is the last of its set of Records, false otherwise.
	//In case the parameter belongs to no set, false is returned
	$set          =getParField("block",$parName);
	if ($set=='0'){return false;}
	$nextParName=nextPar($parName);
	if ($nextParName===false){
		return true;
	}
	$nextSet=getParField("block",$nextParName);
	return ($nextSet!=$set);
}



function getParValuesFromFile($physicalParameters,$fileName){
	//Function that reads from the content of a parameter with physical data from the file whose name in input is given.
	//The function also computes a string of 39 chars that contains the flag relative to the unit used.
	
   if (! is_array($physicalParameters)){$physicalParameters=array($physicalParameters);}
   
   $result=array();
   $data=array();
   $JCHAR='';
   foreach($physicalParameters as $physicalParameter){
   	$data[$physicalParameter]=readFromNgtAtm($physicalParameter   ,$fileName);
   	$unit=getUnits($physicalParameter,$fileName);
   	$char=unitFlag($unit);
   	$JCHAR=$JCHAR.$char;
   	$nTimes=sizeof($data[$physicalParameter]);
   }
   $allData=array();
   for ($repetition=0;$repetition<$nTimes;$repetition++){
   	$thisLine=array();
    	foreach($physicalParameters as $physicalParameter){
   		$thisLine[]=$data[$physicalParameter][$repetition];
   	}
      $allData[$repetition+1]=implode(", ",$thisLine);
   }      
   $result['data']=$allData;
   $result['flagString']=$JCHAR;   
   $result['times']=$nTimes;   
   
   return $result;
}



function fromDatabaseToIniFile($user,$conf){
//Function that read the parameters from the Data Base, 
//and use them to build the ini file, suitable input
//for writeinput.py and its various versions


  	$query="SELECT * FROM `parameters`";
   $result=mysql_query($query);
   $row=mysql_fetch_assoc($result);
   $previousSet='0';
   $repetition=0;
   $repetitionRow=0;
   $nextPar=$row['name'];
   $iniFileContent='';
   while($nextPar){
      $parName=$nextPar;
      $value  =getParField("value"  ,$parName,$user,$conf);
      $message=getParField("message",$parName,$user,$conf);
      $file=   getParField("file"   ,$parName,$user,$conf);
      if (validParValue($value) and $file=="TAPE5"){
  	      $recordId     =getParField      ('records_id',    $parName); 
	      $set= getParField("block",$parName);
	      $lineDimension =getRecordField   ("record_dimension",$recordId);
	      $numericalLineDimension=numericalValue($lineDimension);
	      if ($set!='0'){
	       	$lastRepetition=getLastRepetition($parName,$user,$conf);
	      	if(firstOfSet($parName)){
	      		$repetition++;
	      		$firstParOfSet=$parName;
	      	}
	      	$iniParName="__".strVal($repetition)."__".$parName;
	      } else {
	      	$lastRepetition=0;
	      	$iniParName=$parName;
	      }
	      if($lineDimension!='1'){
	      	if (firstOfRecord($parName)){
	      		$repetitionRow++;
	      		$firstParOfRecord=$parName;
	      	}
	      	$iniParName=$iniParName."|".strVal($repetitionRow+1);
	      } else {
	      	$iniParName=$iniParName;
	      }
	      if (is_array($value)){
	      	$iniValue=$value[intval($repetition)][intval($repetitionRow)];
	      	$iniValue=trim($iniValue,"\n");
	      } else $iniValue=$value;
	      $iniFileContent=$iniFileContent."[".$iniParName."]\n";
	      $iniFileContent=$iniFileContent."value="  .$iniValue."\n";
	      $iniFileContent=$iniFileContent."message=".$message."\n";
	      if (lastOfRecord($parName) and $repetitionRow<$numericalLineDimension-1){
	      	$nextPar=$firstParOfRecord;
	      	continue;
	      }
	      if (lastOfSet($parName) and $repetition<$lastRepetition){##
	      	$nextPar=$firstParOfSet;
	      	continue;
	      }
      }
      $nextPar=nextPar($parName);
   }
   file_put_contents("detTAPE.ini",$iniFileContent);
}

function deleteConfiguration($user,$conf){
	//The name is self explanatory. The configuration, whose
	//number in input is given, will be removed, and all those
	//with higher number, if any, will have the number updated, 
	//the new one being the old value minus one.
	$query="SELECT name from `parameters`";
   $result=mysql_query($query);
   while($row=mysql_fetch_array($result)){
   	$tabName=$row['name'];
      $query1="SELECT MAX( conf ) FROM `".$tabName."` WHERE user_id = '$user'";
      $result1=mysql_query($query1);
      if ($result1){
      	$thisLastConf=mysql_result($result1,0);
     	   $thisLastConf=intval(trim($thisLastConf));
     	   if ($thisLastConf>=$conf){
     	   	$query2="DELETE FROM `".$tabName."` WHERE user_id='$user' and conf='$conf'";
     	  	   mysql_query($query2);
     	   }
     	   for ($configuration=intval($conf)+1;$configuration<=$thisLastConf;$configuration++){
     	   	$newConf=$configuration-1;
     	   	$query="UPDATE `".$tabName."` SET conf='$newConf' WHERE user_id='$user' and conf='$configuration'";
     	   	mysql_query($query);	
     	   }
      }
   }
}

function numAvChars($format){
	//Function that computes the total number of characters to be reserved 
	//in TAPE5 for a parameter with the format given in input.
	//It only accepts the following formats Fn.n, In, An, En.n

	if (strpos($format,"F")!==false) {
		$s=explode("F",$format);
		$s1=explode(".",$s[1]);
		$s2=trim($s1[0]);
		return intval($s2);
	}

	if (strpos($format,"E")!==false) {
		$s=explode("E",$format);
		$s1=explode(".",$s[1]);
		$s2=trim($s1[0]);
		return intval($s2);
	}

	if (strpos($format,"I")!==false) {
		$s=explode("I",$format);
		$s2=trim($s[1]);
		if ($s2==""){
			$s2=trim($s[0]);
		}
		return intval($s2);
	}

	if (strpos($format,"A")!==false) {
		$s=explode("A",$format);
		$s2=trim($s[1]);
		return intval($s2);
	}
	
}

function elementsForRow($format){
	//Function that, given a format specific for an array, returns the number of 
	//elements that have to be written in a line before changing line.
	//Only used for floating point and scientific formats

	if (strpos($format,"F")!==false) {
		$s=explode("F",$format);
		$s1=trim($s[0]);
		return intval(trim($s1));
	}

	if (strpos($format,"E")!==false) {
		$s=explode("E",$format);
		$s1=trim($s[0]);
		return intval(trim($s1));
	}

	return 1;
}

function parameterString($value,$format){
	//Function that transforms a parameter value into its proper representation
	//to be written in TAPE5
   //At the moment of tha call, $value is already a trimmed string.
   //What distinguishes this function from the function below, i.e.
   //singleParameterString, is that this function might deal with arrays of
   //values, while the lattar only with single values.
   //Here $format might have a number on the left of the letter (e.g. 8F10.3)

   if (strpos($value,",")===false){
   	return singleParameterString($value,$format);
   }

   $elementsForRow=elementsForRow($format);
   $values=explode(",",$value);
   $parStr="";
   $i=1;
   foreach ($values as $val){
   	$parStr=$parStr. singleParameterString($val,$format);
   	if($i==$elementsForRow){
   		$parStr=$parStr."\n";
   		$i=0;
   	}
   	$i++;
   }
   return $parStr;
}

function singleParameterString($value,$format){
	//Function that transforms a parameter value into its proper representation
	//to be written in TAPE5
   //At the moment of tha call, $value is already a trimmed string.
   //What distinguishes this function from the function above, i.e.
   //parameterString, is that this function only deals with single values, 
   //while the latter might deal with arrays.

   if ($format=="FLT"){
   	if ($value==""){
   		return " "; 
   	}
   	return $value;
   }
	
   $numAvChars=numAvChars($format);
	##The control that the value does not exceed the maximum allowed
	##should be done at the moment of sanitizing the post.

   if ($value===""){
   	return str_repeat(" ",$numAvChars);
   }

   if (strpos($format,"A")!==false){
   	$numSpaces=max(array($numAvChars-strlen($value),0));
   	return substr($value,0,$numAvChars). str_repeat(" ",$numSpaces);
   }   
   
   if (strpos($format,"I")!==false){
   	$numSpaces=$numAvChars-strlen($value);
   	return str_repeat(" ",$numSpaces).$value;
   }   
   
	if (strpos($format,"F")!==false) {
		$s=explode("F",$format);
		$s1=explode(".",$s[1]);
		$s2=trim($s1[1]);
		$numDecDig=intval($s2);
   	$value=strval(round($value,$numDecDig));
   	$numSpaces=$numAvChars-strlen($value);
   	$spaces=str_repeat(" ",$numSpaces);
   	return $spaces.$value;
	}

	if (strpos($format,"E")!==false) { ##To be changed to allow proper writing in scientific notation 
		$s=explode("E",$format);
		$s1=explode(".",$s[1]);
		$s2=trim($s1[1]);
		$numDecDig=intval($s2);
   	$value=strval(round($value,$numDecDig));
   	$numSpaces=$numAvChars-strlen($value);
   	$spaces=str_repeat(" ",$numSpaces);
   	return $spaces.$value;
	}

	
	
}


function recordString($record,$recordFormat){
	#Function that updates the value of the string to be
	#written to TAPE5 with one more record.
	
	#notes on the format, slightly modified in few cases from the instructions of the LBLRTM
	
	#F10.3 means a floating point of 3 decimal place that occupies 10 
	#characters, spaces are put at the beginning-
	#G and also E, to my experience, behave exactly like E. If they don't, this code needs to be revised.
	#Only for F and E (which here are treated in the same way) a number could be at the beginning, it 
	#is meant for lists of values. 
	#8F10.3 means that each element of that parameter, which is an array, has a format F10.3 and every 8
	#values there is a new line.
	
   #I and A are simple: the added information boils down to the number of characters after the letter.
   #In the case of A, the string, the spaces to fill the slot are put at the right, while for I, integer,
   #just like for every other case, are put at the left.  
	$values =explode("_",$record);
	$recordString="";
	$valueIndex=0;
	$recordFormat=str_replace("G","F",$recordFormat);
	$recordFormat=str_replace("E","F",$recordFormat);
	$formats=explode(" ",$recordFormat);

	foreach($formats as $format){
		if (trim($format)==""){ continue;}
		if (strpos($format,"X")!==false){
			$s1=explode("X",$format);
			$s2=trim($s1[0]);
			$numSpaces=intval($s2);
			$recordString=$recordString.str_repeat(" ",$numSpaces);
			continue;
		}
		if ($valueIndex>=sizeof($values)){
			break;
		}
		$value=trim(strval($values[$valueIndex]));
      $recordString=$recordString.parameterString($value,$format);
		$valueIndex++;
	}
	$recordString=$recordString. "\n";
	return $recordString;
}



function fromDatabaseToTAPE5($user,$configurations){
//Function that read the parameters from the Data Base, 
//and turns them into a proper input for the LBLRTM code: 
//the infamous TAPE5

   $fileContent='';
   $recordContent='';
   $parameterList=getPars();
   foreach ($configurations as $conf){
 	   $previousSet='0';
	   $repetition=0;
	   $repetitionRow=0;
      $nonEmptyRecord=false;
      $nextPar=$parameterList[0];
	   while($nextPar){
	      $parName=$nextPar;
  	      $nextPar=nextPar($parName);
	      $value  =getParField("value"  ,$parName,$user,$conf);
	      $message=getParField("message",$parName,$user,$conf);
	      $file=   getParField("file"   ,$parName,$user,$conf);
	      $set=          getParField("block",           $parName);
	      $lineDimension=getParField("record_dimension",$parName);
	      $format=       getParField("format",          $parName); 
	      $numericalLineDimension=numericalValue($lineDimension);
	      $write=writingFlag($parName,$user,$conf);
	      if (! validParValue($value) or $message!="OK" or $write=='0'){
	      	$value=" ";
	      } else 
	         $nonEmptyRecord=true;
	      if ($set!='0'){
	       	$lastRepetition=getLastRepetition($parName,$user,$conf);
	      	if(firstOfSet($parName)){
	      		$repetition++;
   	      	$repetitionRow=0;
	      		$firstParOfSet=$parName;
	      	}
	      } else {
	      	$lastRepetition=0;
	      	$firstParOfSet="non relevant";
	      }
	      if($lineDimension!='1' and firstOfRecord($parName)){
	      	$repetitionRow++;
	      	$firstParOfRecord=$parName;
	      }
	      if (is_array($value)){
	      	if (isset($value[intval($repetition)][intval($repetitionRow)])){
	         	$singleValue=$value[intval($repetition)][intval($repetitionRow)];
	         }
	      	$singleValue=trim($singleValue,"\n");
	      } else $singleValue=$value;
	      $recordContent=$recordContent.$singleValue." _ ";

	      if(lastOfRecord($parName)){
	      	if ($nonEmptyRecord and $file=="TAPE5"){
      	      $fileContent=$fileContent.recordString($recordContent,$format);
      	      ##extend to other input files (EM-REFL and others)
      	      ##it is easy, just do an associative array of strings for each file.
	      	}
      	   $recordContent="";
      	   $nonEmptyRecord=false;
	      }
	      if (lastOfSet($parName) and $repetition<$lastRepetition){
	      	$nextPar=$firstParOfSet;
	      	continue;
	      }
	      if (lastOfRecord($parName) and $repetitionRow<$numericalLineDimension-1){
	      	$nextPar=$firstParOfRecord;
	      	continue;
	      }
	   }
	   $fileContent=$fileContent."-1. \n";
	}
	$fileContent=$fileContent."% produced by ULME";
	return $fileContent;
}
?>
