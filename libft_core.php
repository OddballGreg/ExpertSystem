<?php

function contains($haystack, $needle)
{
	if (strpos($haystack, $needle) === FALSE)
		return (FALSE);
	return (TRUE);
}

function cap($value, $cap)
{
	if ($value > $cap)
		return ($cap);
	return ($value);
}

?>