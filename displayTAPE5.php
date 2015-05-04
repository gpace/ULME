<?php
ob_start();
require 'functions.php';
$configurationStr=$_COOKIE['configurations'];
$configurations=explode(", ",$configurationStr);
$fileName="TAPE5". substr(md5(session_id()),0,5).".txt";
$user=getUser();
$TAPE5=fromDatabaseToTAPE5($user,$configurations);
echo $TAPE5;
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"$fileName\"");
?>
