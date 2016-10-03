#!/usr/bin/php
<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

if ($argc < 0)
	die ("Please input the name and path of the .txt file containing your rules, constants and query as an arguement.\n");
else if ($argc > 2)
	print ("This Expert System can only handle one arguement .txt file at a time.\n");

require_once "Fact.class.php";

if ($argc == 2)
{
	if ($argv[1] == NULL | !file_exists($argv[1]))
	{
		echo "Error: File does not exist\n";
		exit(0);
	}
	$file = file_get_contents($argv[1]);
	preg_match_all('/(.+?)[#$]/', $file, $info);
	preg_match_all("/=> (.+?)#/", $file, $rule_array);
	preg_match_all("/(.+?)=>/", $file, $depend_array);
	$values = implode(" ", $info[1]);;
	preg_match_all("/[A-Z]/", $values, $newvalues);
	$test = implode(" ", $newvalues[0]);
	$test = count_chars( $test, 3);
	preg_match_all("/[A-Z]/", $test, $newtest);
	
	$newarray = array_map('trim', $info[1]);
	$newarray = array_filter($newarray);
	$loop = TRUE;
	while ($loop == TRUE) {
		foreach ($newarray as $line) {
			$i = 0;
			if ($line[$i] === '?') {
				$query = str_split($line, 1);
			}
			else if ($line[$i] === '=') {
				$initial_facts = str_split($line, 1);
			}
		}
		if ($query != null && $initial_facts != null) {
			$loop = FALSE;
		}
	}
	array_shift($newarray);
	array_shift($query);
	array_shift($initial_facts);
	print_r($newarray); ////debuggery and other wizard shit to shows rules
	print_r($query); ////debuggery and other wizard shit to shows the ?query
	print_r($initial_facts); ////debuggery and other wizard shit to shows given true values
	$unique_values = array_combine($newtest[0], $newtest[0]);
	print_r($unique_values);
	foreach ($unique_values as $fact)
	{
		$facts[$fact] = new Fact($fact);
	}
	echo "\n";//debuggery and other wizard shit
	foreach ($initial_facts as $elem) {
		$facts[$elem]->set_constant();
	}
	echo "\n"; //debuggery and other wizard shit

	print_r($rule_array[1]);//debuggery and other wizard shit
	print_r($depend_array[1]);//debuggery and other wizard shit
	$counter = count($rule_array[1]);
	$i = 0;
	
	while ($i < $counter)
	{
		$tmp = $rule_array[1][$i];
		$dep = $depend_array[1][$i];
		echo $tmp . "\n";
		$facts[$tmp]->c_depend($dep);
		$i++;
	}
	echo ($tmp . "\n");

<<<<<<< HEAD
	$facts['A']->set_constant();
	$facts['B']->set_constant();
	$facts['G']->set_constant();
	$facts['C']->c_depend("A | B => C");
	$facts['C']->c_depend("A | F => C");
	$facts['D']->c_depend("A + B + C => D");
	$facts['H']->c_depend("C | !G => H");
=======
>>>>>>> edd38ac1abd73c40a561b6c665af6f207972bf00
	$facts['V']->c_anull("E + F => !V");
	$facts['C']->c_anull("E + F => !C");
	print_r($facts);
	$facts['G']->prove($facts);
	$facts['C']->prove($facts);
}
//File handling done here.

//Validate File here

//Create objects based off of file input here

//Parse query here and fufill it using the objects created

//Print strings of query results and reasoning here

//exit
?>