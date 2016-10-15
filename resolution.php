<?php

function resolve_rhs($rhs) 
{
	echo("resolve rhs dealing with:" . $rhs . PHP_EOL);
	preg_match_all("/([A-Z]{1})/", $rhs, $facts);
	$facts = $facts[1];
	foreach ($facts as $fact)
		$dispersal[$fact] = 100;
	$index = -1;
	$bracket_sets = array();
	echo("starting loop" . PHP_EOL);
	do
	{
		echo("index = " . $index . "for" . $rhs . PHP_EOL);
		$brackets = NULL;
		preg_match_all('/\(((?:[^()])*)\)/', $rhs, $brackets);
		print_r($brackets);
        $bracket_sets = array_merge($bracket_sets, $brackets[1]);
        if ($bracket_sets != NULL)
		    while ($bracket_sets[++$index] != NULL)
		    	str_replace($bracket_sets[$index], $index, $rhs);
	} while ($brackets[0] != NULL);
	if (strlen(trim($rhs)) == 1)
		$dispersal[trim($rhs)] = 100;
	$plus_sets = explode("+", $rhs);
	echo("exploded on +" . PHP_EOL);
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
	echo("dealing with brackets" . PHP_EOL);
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
	echo("Returning Dispersal" . PHP_EOL);
	return ($dispersal);
}

function resolve_exp($facts, $expression)
{
	do 
	{
		//use preg match to break the expression down into it's lower brackets
		unset($brackets);
		preg_match_all('/\(((?:[^()])*)\)/', $expression, $brackets);

		//solve the lower brackets and reinsert the results into the string
	    print_r($brackets);
            echo ("brackets count =" . count($brackets[0]) . PHP_EOL);
		if (count($brackets[0]) != 0)
			foreach ($brackets[1] as $lower_exp)
			{
                echo ("Lower expression before preg_quote and resolve_exp = " . $lower_exp . PHP_EOL);
				$result = resolve_exp($facts, $lower_exp);
                $lower_exp = "/\(" . preg_quote($lower_exp, "/") . "\)/";
                echo ("Result = " . $result . PHP_EOL);
                echo ("Lower expression = " . $lower_exp . PHP_EOL);
                $expression = preg_replace($lower_exp, $result, $expression);
                echo ($expression . PHP_EOL);
			}
	} while (count($brackets[0]) != 0);
	echo("resolve_exp dowhile loop ended" . PHP_EOL);
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
		$or_results[] = max($results);
	}
	$end_prob = (min($or_results) + array_sum($or_results) / count($or_results)) / 2;
    echo ("End_prob before return: " . $end_prob . PHP_EOL);
	return ($end_prob);
}

function resolve_rule($facts, $rule)
{
	//split expression from affectants
	$sides = explode("=>", $rule);

	//create array for affectants dictating probablitiy allocation
	$affectant = $sides[1];
	$allocation = resolve_rhs($affectant);

	//replace expression FACTS with their related probablities from $facts in the string itself
	$expression = $sides[0];
	$chars = str_split("ABCDEFGHIJKLMNOPQRSTUVWXYZ");
	echo("Replacing expression FACTS with related probs" . PHP_EOL);
	foreach ($chars as $fact)
    {
        echo ("Checking if $fact is in facts" . PHP_EOL);
        print_r ($facts);
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
	echo($expression . PHP_EOL);

	//call resolve_exp on expression
	echo("Resolving Expression" . PHP_EOL);
	$probablility = resolve_exp($facts, $expression);

	//Allocate returned expression probablitiy to $facts according to the array created earlier
	echo("Allocating Probability" . PHP_EOL);
	foreach ($chars as $fact)
		if (array_key_exists($fact, $allocation))
            $facts[$fact][] = $probablility * ($allocation[$fact] / 100);

    return ($facts);
}

?>
