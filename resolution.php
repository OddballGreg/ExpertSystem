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
	$chars = str_split("ABCDEFGHIJKLMNOPQRSTUVWXYZ");
	//replace rule FACTS with their related probablities from $fact_list in the string itself
	foreach ($chars as $fact)
	{
		$pattern = "/" . $fact . "/";
		$expression = preg_replace($pattern, sum($fact_list[$fact]) / count($fact_list[$fact]), $expression);
	}
	//use preg match to break the expression down into it's lower brackets
	preg_match_all('/\(((?:[^()])*)\)/', $rule, $brackets);

	//solve the lower brackets and reinsert the results into the string
	foreach ($brackets[1] as $single_exp)
	{
		while (contains($single_exp, "+"))
		{
			preg_match("/([^\+ ]* \+ [^\+ ]*)/", $single_exp, $solve);
			
		}
	}


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
	//create array for affectants dictating probablitiy allocation
	//call resolve_exp on expression
	//Allocate returned expression probablitiy to $fact_list according to the array created earlier
}

?>

( and ) which are fairly obvious. Example : A + (B | C) => D
• ! which means NOT. Example : !B
• + which means AND. Example : A + B
• | which means OR. Example : A | B
• ˆ which means XOR. Example : A ˆ B
• => which means "implies". Example : A + B => C
• <=> which means "if and only if". Example : A + B <=> C