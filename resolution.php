<?php

function assign_prob_and($fact_list, $keys, $prob)	
{
	foreach ($keys as $key)
		$fact_list[$key] = $prob;
}

function assign_prob_or($fact_list, $keys, $prob)
{
	foreach ($keys as $key)
		$fact_list[$key] = $prob / count($keys);
}

function resolve_exp($fact_list, $expression)
{
	do 
	{
		//use preg match to break the expression down into it's lower brackets
		unset($brackets);
		preg_match_all('/\(((?:[^()])*)\)/', $rule, $brackets);

		//solve the lower brackets and reinsert the results into the string
		if ($brackets != NULL)
			foreach ($brackets[1] as $lower_exp)
			{
				$result = resolve_exp($fact_list, $lower_exp);
				$lower_exp = preg_quote($lower_exp, "/");
				preg_replace($lower_exp, $result, $expression);
			}
	} while ($brackets != NULL);

	$expression = str_replace(array('(',')'), '', $expression);
	$or_sets = explode("|", $expression);

	//resolve + operations, reinsert result

	//resolve | opeartions, reinsert result

	//resolve ^ operations, resinsert result

	//repeat the above steps until no further bracket sets can be obtained or operations run

	//return result

	return (0);
}

function resolve_rule($fact_list, $rule)
{
	//split expression from affectants
	$sides = explode("=>", $rule);

	//create array for affectants dictating probablitiy allocation
	$affectant = $sides[1];

	//replace expression FACTS with their related probablities from $fact_list in the string itself
	$expression = $sides[0];
	$chars = str_split("ABCDEFGHIJKLMNOPQRSTUVWXYZ");
	foreach ($chars as $fact)
	{
		$pattern = "/\!" . $fact . "/";
		if ($fact_list[$fact] !== TRUE)
			$expression = preg_replace($pattern, (100 - sum($fact_list[$fact]) / count($fact_list[$fact])), $expression);
		else
			$expression = preg_replace($pattern, "0", $expression);
		$pattern = "/" . $fact . "/";
		if ($fact_list[$fact] !== TRUE)
			$expression = preg_replace($pattern, sum($fact_list[$fact]) / count($fact_list[$fact]), $expression);
		else
			$expression = preg_replace($pattern, "100", $expression);
	}

	//call resolve_exp on expression
	$probablility = resolve_exp($fact_list, $expression);
	
	//Allocate returned expression probablitiy to $fact_list according to the array created earlier
}

?>