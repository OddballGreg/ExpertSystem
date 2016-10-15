#!/usr/bin/php
<?php
require_once("libft_core.php");
require_once("resolution.php");

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

	//File read and parse

	$file = file($argv[1]);
	//print_r($file); //debug
    $rhs_array;
	$rule_array;
	$constant_array;
	$query_array;
	foreach($file as $line)
	{
		preg_match("/([^#]+[<=> ]..+)#/", $line, $rule); // Will blatantly ignore the #symbol if vaild code is after it.
		if ($rule == NULL)
			preg_match("/(.+[<=> ]{2,3}[^\w].+)/", $line, $rule);
        if ($rule != NULL) {
			$rule_array[] = $rule[1];
        }
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
    
	$factsn = array();
	$facts = array();

	//below code will generate facts only according to those used in the defined rules
	/*foreach($rule_array as $line)
	{
		preg_match_all("/([A-Z]{1})/", $line, $ufact);
		$factsn = array_merge($factsn, $ufact[1]);
		$factsn = array_unique($factsn);
		foreach($factsn as $fact)
		{
			$facts[$fact] = array();
		}
		unset($fact);
	}
	unset($line);*/

	//generate all possible facts
	$chars = str_split("ABCDEFGHIJKLMNOPQRSTUVWXYZ");
	foreach ($chars as $char)
	{
		$facts[$char] = array();
	}
	//Define constants in a fact array as boolean true rather than arrays
	foreach ($constant_array as $constant)
		$facts[$constant] = TRUE;
	unset($line);
	echo "\nRules\n";
	print_r($rule_array);
	echo "Constants\n";
	print_r($constant_array);
	echo "Queries\n";
	print_r($query_array);
	echo "Facts\n";
	print_r($facts);
	echo "\n";

    /******************** Splitting rhs *********************/
    /*
    $i = 0;
    foreach ($rule_array as $elem) {
        echo ($elem . "\n");
        $rule = substr($elem, strpos($elem, ">") + 1);
        $rhs_array[$i] = $rule;
        $i++;
    }
    
    
    print_r($rhs_array);
    */
    /************************ END ***************************/
    
	//Solving

	$rule_list = $rule_array;
	$waiting_list = NULL;
    
	while($rule_list != NULL) //Loop runs through the fact list infinitely to make sure all rules are used.
	{
		foreach ($rule_list as $rule)
		{
			if(contains($rule, "<=>")) //create singular sub rules from <=>
			{
				$temp = explode('<=>', $rule);
				$rule_list[] = trim($temp[1]) . ' => ' . trim($temp[0]);
				$rule_list[] = trim($temp[0]) . ' => ' . trim($temp[1]);
			}
			else
			{
				$push = FALSE;
				$lhs = explode("=>", $rule)[0];
				$rhs = explode("=>", $rule)[1];
                				preg_match_all("/([A-Z])/", $lhs, $deps);
				preg_match_all("/([A-Z])/", $rhs, $affs);
                
                /******************** TESTING PHASE *********************/
                
               // $dep_keys = array_combine($deps[1], $deps[1]);
               // $aff_keys = array_combine($affs[1], $affs[1]);
				echo "Affectants: ";
				print_r($affs[1]);
				echo "\nDepenendents: ";
				print_r($deps[1]);
				echo "\n";
				echo ("lhs = " . $lhs . "\n" . "rhs = " . $rhs . "\n");
            
                if (strpos($rhs, "+") !== false) {
                    $rhs2 = str_replace("+", " ", $rhs);
                    $rhs2 = str_replace(" ", "", $rhs2);
                    $len = strlen($rhs2);
    
                }
                
                /******************** END TESTING PHASE *********************/
                
				foreach ($rule_list as $rule) // Check for fact dependencies in remaining rules
				{
					foreach ($deps[1] as $dep)
					{
						$check = explode("=>", $rule)[1];
						if (contains($check, trim($dep)) && $facts[trim($dep)] != TRUE) // Forces the rule to be pushed to the next list if it's listed dependencies have not been fully defined and are not constants
							$push = TRUE; //Variable sets this rule to be pushed to the next list.
					}
				}
				if ($waiting_list != NULL) // Check for fact dependencies in rules already pushed to the waiting list.
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
					$waiting_list[] = $rule; // Pushes the rule to the waiting list for the next runthrough
				else // Resolve the rule
				{
					resolve_rule($facts, $rule);

					/***************************************************************************** 
					This code would solve a singular expression rule, even if brackets. eg (C) => E

					preg_match_all('/\(((?:[^()])*)\)/', $rule, $brackets);
					print_r($brackets[1]);
					print_r($facts);
					print_r($rhs);
					if ($facts[trim($brackets[1][0])] === TRUE)
						assign_prob_and($rule_list, trim($rhs), 100);
					else
						$facts[trim($rhs)][] = $facts[trim($brackets[1])]; 
					******************************************************************************/
				}
			}
			
		}
		print_r($rule_list); // DEBUG
		print_r($facts);
        print_r($waiting_list);
		$rule_list = $waiting_list;
        exit(0);
	}

    
    /*************************** old parse function ****************************/

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
 /****************************** end of old parse function **************************/

}
?>
