#!/usr/bin/php
<?php
require_once("libft_core.php");
require_once("resolution.php");

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
	foreach($file as $line)
	{
		preg_match("/([^#]+[<=> ]..+)#/", $line, $rule);
        if ($rule == NULL)
			preg_match("/(.+[<=> ]{2,3}[^\w].+)/", $line, $rule);
        if ($rule != NULL) {
			$rule_array[] = $rule[1];
        }
	}
	unset($line);
	foreach($file as $line)
	{
		preg_match("/=([A-Z]+)/", $line, $constant);
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
	$factsn = array();
	$facts = array();
	$chars = str_split("ABCDEFGHIJKLMNOPQRSTUVWXYZ");
	foreach ($chars as $char)
		$facts[$char] = array();
	foreach ($constant_array as $constant)
		$facts[$constant] = TRUE;
	unset($line);
	echo "\nRules\n";
    foreach ($rule_array as $elem) {
        echo $elem . "\n";
    }
	echo "\nConstants\n";
    foreach ($constant_array as $elem) {
        echo $elem . "\n";
    }
	echo "\nQueries\n";
    foreach ($query_array as $elem) {
        echo $elem . "\n";
    }
	$rule_list = $rule_array;
	$waiting_list = NULL;
    
	while($rule_list != NULL)
    {
		foreach ($rule_list as $rule)
		{
			if(contains($rule, "<=>"))
			{
				$temp = explode('<=>', $rule);
				$rule_list[] = trim($temp[1]) . ' => ' . trim($temp[0]);
                $rule_list[] = trim($temp[0]) . ' => ' . trim($temp[1]);
                $tmp_key = array_search($rule, $rule_list);
                unset($rule_list[$tmp_key]);
                $push = FALSE;
			}
			else
			{
				$push = FALSE;
				$lhs = explode("=>", $rule)[0];
				$rhs = explode("=>", $rule)[1];
               	preg_match_all("/([A-Z])/", $lhs, $deps);
				preg_match_all("/([A-Z])/", $rhs, $affs);
                if (strpos($rhs, "+") !== false) {
                    $rhs2 = str_replace("+", " ", $rhs);
                    $rhs2 = str_replace(" ", "", $rhs2);
                    $len = strlen($rhs2);
    
                }
				foreach ($rule_list as $rule_check)
				{
                    $tmp_key = array_search($rule, $rule_list);
                    if (array_search($rule_check, $rule_list) < $tmp_key + 1)
                        break;
					foreach ($deps[1] as $dep)
					{
						$check = explode("=>", $rule_check)[1];
						if (contains($check, trim($dep)) && $facts[trim($dep)] != TRUE)
							$push = TRUE;
					}
				}
				if ($waiting_list != NULL)
				{
					foreach ($waiting_list as $rule)
					{
						foreach ($deps[1] as $dep)
						{
							$check = explode("=>", $rule)[1];
							if (contains($check, $dep))
								$push = TRUE;
						}
					}
				}
                if ($push === TRUE)
                {
                    $waiting_list[] = $rule;
                    $push = FALSE;
                }
				else
				{
					$facts = resolve_rule($facts, $rule);
				}
			}
			
        }
		$rule_list = $waiting_list;
    }
    echo "\nQuery resolved:\n";
    foreach ($query_array as $query)
    {
        if ($facts[$query] === TRUE)
            echo "$query is TRUE" . PHP_EOL;
        else if ($facts[$query] != NULL)
        {
            $final_prob = array_sum($facts[$query]) / count($facts[$query]);
            if ($final_prob > 50)
                echo "$query is TRUE" . PHP_EOL;
            else if ($final_prob < 50)
                echo "$query is FALSE" . PHP_EOL;
            else
                echo "$query is UNDETERMINED" . PHP_EOL;
        }
        else
            echo "$query is FALSE" . PHP_EOL;

    }
}
?>
