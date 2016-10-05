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

	return (0);
}

function resolve_rule($fact_list, $rule)
{

}

?>