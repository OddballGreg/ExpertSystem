<?php

function resolve_rhs($rhs) 
{
	preg_match_all("/([A-Z]{1})/", $rhs, $facts);
	$facts = $facts[1];
	foreach ($facts as $fact)
		$dispersal[$fact] = 100;
	$index = -1;
	$bracket_sets = array();
	do
	{
		$brackets = NULL;
		preg_match_all('/\(((?:[^()])*)\)/', $rhs, $brackets);
        $bracket_sets = array_merge($bracket_sets, $brackets[1]);
        if ($bracket_sets != NULL)
		    while ($bracket_sets[++$index] != NULL)
		    	str_replace($bracket_sets[$index], $index, $rhs);
	} while ($brackets[0] != NULL);
	if (strlen(trim($rhs)) == 1)
		$dispersal[trim($rhs)] = 100;
	$plus_sets = explode("+", $rhs);
	foreach ($plus_sets as $set)
	{
		if (strlen(trim($set)) == 1)
			$dispersal[trim($set)] = 100;
        else
        {
            $or_sets = preg_split("/[\|\^]/", $set);
			foreach ($or_sets as $or)
			{
				$dispersal[trim($set)] = 100 / count($or_sets);
            }
        }
	}
    $index = -1;
    if ($bracket_sets != NULL)
    {
	    while ($bracket_sets[++$index] != NULL)
    	{
    		$plus_sets = explode("+", $bracket_sets[$index]);
    		foreach ($plus_sets as $set)
    		{
    			if (strlen(trim($set)) == 1)
    				$dispersal[trim($set)] = $disperal[$index];
    			else
    				$or_sets = preg_split("/[\|\^]/", $set);
    			foreach ($or_sets as $or)
    			{
    				$dispersal[trim($set)] = $disperal[$index] / count($or_sets);
    			}
    		}
    	}
    }
	return ($dispersal);
}

function resolve_exp($facts, $expression)
{
	do 
	{
		unset($brackets);
		preg_match_all('/\(((?:[^()])*)\)/', $expression, $brackets);
		if (count($brackets[0]) != 0)
			foreach ($brackets[1] as $lower_exp)
			{
				$result = resolve_exp($facts, $lower_exp);
                $lower_exp = "/\(" . preg_quote($lower_exp, "/") . "\)/";
                $expression = preg_replace($lower_exp, $result, $expression);
			}
	} while (count($brackets[0]) != 0);
	$expression = str_replace(array('(',')'), '', $expression);
	$or_sets = explode("|", $expression);
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
		$or_results[] = max($results);
	}
	$end_prob = (min($or_results) + array_sum($or_results) / count($or_results)) / 2;
	return ($end_prob);
}

function resolve_rule($facts, $rule)
{
	$sides = explode("=>", $rule);
	$affectant = $sides[1];
	$allocation = resolve_rhs($affectant);
	$expression = $sides[0];
	$chars = str_split("ABCDEFGHIJKLMNOPQRSTUVWXYZ");
	foreach ($chars as $fact)
    {
        if (array_key_exists($fact, $facts))
		{
			$pattern = "/\!" . $fact . "/";
			if ($facts[$fact] !== TRUE && $facts[$fact] != NULL)
				$expression = preg_replace($pattern, (100 - array_sum($facts[$fact]) / count($facts[$fact])), $expression);
			else
				$expression = preg_replace($pattern, "0", $expression);
			$pattern = "/" . $fact . "/";
			if ($facts[$fact] !== TRUE && $facts[$fact] != NULL)
				$expression = preg_replace($pattern, array_sum($facts[$fact]) / count($facts[$fact]), $expression);
			else
				$expression = preg_replace($pattern, "100", $expression);
		}
	}
	$probablility = resolve_exp($facts, $expression);
	foreach ($chars as $fact)
		if (array_key_exists($fact, $allocation))
            $facts[$fact][] = $probablility * ($allocation[$fact] / 100);
    return ($facts);
}
?>