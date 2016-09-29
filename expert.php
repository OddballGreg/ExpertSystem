<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

if ($argc < 0)
	die ("Please input the name and path of the .txt file containing your rules, constants and query as an arguement");
else if ($argc > 1)
	print ("This Expert System can only handle one arguement .txt file at a time. Additional arguements after the first will be ignored.");

//File handling done here.

//Validate File here

//Create objects based off of file input here

//Parse query here and fufill it using the objects created

//Print strings of query results and reasoning here

//exit
?>