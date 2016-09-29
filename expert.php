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
	array_shift($query);
	array_shift($initial_facts);
	print_r($newarray); //debug to shows rules
	print_r($query); //debug to shows the ?query
	print_r($initial_facts); //debug to shows given true values
	$unique_values = array_combine($newtest[0], $newtest[0]);
	print_r($unique_values);
	foreach ($unique_values as $fact)
	{
		$facts[$fact] = new Fact($fact);
	}

	$facts['A']->set_constant();
	$facts['B']->set_constant();
	$facts['G']->set_constant();
	$facts['C']->c_depend("A | B => C");
		$facts['C']->c_depend("A | F => C");
	$facts['D']->c_depend("A + B + C => D");
	$facts['H']->c_depend("C | !G => H");
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