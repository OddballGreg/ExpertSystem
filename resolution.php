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
    
    function $rhs_probs($rhs) {
        if (strpos($rhs, "+") !== false) {
            $rhs2 = str_replace("+", " ", $rhs);
            $rhs2 = str_replace(" ", "", $rhs2);
            $len = strlen($rhs2);
            echo "The length minus + is " . $len;
        }
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

	//solve or sets last by breaking on them first
	$or_sets = explode("|", $expression);
	//resolve + operations
	foreach ($or_sets as $set)
	{
		$and_set = explode("+", $set);
		foreach ($and_set as $item)
		{
			if (!contains($item, "^"))
				$results[] = trim($item); 
			else
			{
				$xor_set = explode("^", $item);
				if (count($xor_set) > 2)
					die("XOR rule over-complicated. Cannot parse exclusivity for more than 2 facts" . PHP_EOL);
				$results[] = ( (cap((100 - trim($xor_set[1]) + trim($xor_set[0])), 100) / 2) + 
							   (cap((100 - trim($xor_set[0]) + trim($xor_set[1])), 100) / 2) ) / 2;
			}
		}
		$or_results[] = max($result);
	}
	$end_prob = (min($or_results) + sum($or_results) / count($or_results)) / 2;
	return ($end_prob);
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