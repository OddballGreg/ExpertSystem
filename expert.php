#!/usr/bin/php
<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

if ($argc < 0)
	die ("Please input the name and path of the .txt file containing your rules, constants and query as an arguement.\n");
else if ($argc > 2)
	print ("This Expert System can only handle one arguement .txt file at a time.\n");

if ($argc == 2)
{
	if ($argv[1] == NULL | !file_exists($argv[1]))
	{
		echo "Error: File does not exist\n";
		exit(0);
	}

	$file = file($argv[1]);
	print_r($file); //debug
	$rule_array;
	$constant_array;
	$query_array;
	foreach($file as $line)
	{
		preg_match("/([^#]+[<=> ]..+)#/", $line, $rule); // Will blatantly ignore the #symbol if vaild code is after it.
		if ($rule == NULL)
			preg_match("/(.+[<=> ]{2,3}[^\w].+)/", $line, $rule);
		if ($rule != NULL)
			$rule_array[] = $rule[1];
	}
	unset($line);
	foreach($file as $line)
	{
		preg_match("/=([A-Z]+)/", $line, $constant); // Test for only '=' given to define constants
		if ($constant != NULL)
		{
			$constant_a[] = explode(' ', $constant[1])[0];
			$constant_array = str_split($constant_a[0]);
		}
	}
	unset($line);
	foreach($file as $line)
	{
		preg_match("/\?(.*)/", $line, $query);
		if ($query != NULL)
		{
			$query_a[] = explode(' ', $query[1])[0];
			$query_array = str_split($query_a[0]);
		}
	}
	unset($line);
	print_r($rule_array);
	print_r($constant_array);
	print_r($query_array);

	$fact_list = $rule_array;
	$waiting_list = NULL;
	while($fact_list != NULL)
	{
		foreach ($fact_list as $rule)
		{
			if(strpos($rule, "<=>") === TRUE)
			{
				$temp = explode('<=>', $rule);
				$fact_list[] = $temp[1] . '=>' . $temp[0];
				$fact_list[] = $temp[0] . '=>' . $temp[1];
			}
			else
			{
				$lhs = explode("=>", $rule)[0];
				$rhs = explode("=>", $rule)[1];
				preg_match_all("/([A-Z])/", $lhs, $deps);
				preg_match_all("/([A-Z])/", $rhs, $affs);
				print_r($affs[1]);
				print_r($deps[1]);
			}
			
		}
		print_r($fact_list); // DEBUG
		$fact_list = $waiting_list;
	}


/*	$file = file_get_contents($argv[1]);
	preg_match_all('/(.+?)[#$]/', $file, $info);
	preg_match_all("/=> (.+?)#/", $file, $affectant_array);
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
		$facts[$fact] = 0;
	}
	echo "\n";//debuggery and other wizard shit
	foreach ($initial_facts as $elem) {
		$facts[$elem] = 100;
	}
	echo "\n"; //debuggery and other wizard shit

	print_r($rule_array[1]);//debuggery and other wizard shit
	print_r($depend_array[1]);//debuggery and other wizard shit
	$counter = count($rule_array[1]);
	$i = 0;
	
	while ($i < $counter)
	{
		$tmp = $rule_array[1][$i];
		$tmp = trim($tmp);
		$dep = $depend_array[1][$i];
		echo "Affectant " . $tmp . "\n";
		echo "Dependecy " .  $dep . "\n\n";
		$i++;
	}
	echo ($tmp . "\n");

	print_r($facts);
	print_r($affectant_array);
	print_r($depend_array);

	*/
}
?>