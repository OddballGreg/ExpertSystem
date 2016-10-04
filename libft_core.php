<?php

function contains($haystack, $needle)
{
	if (strpos($haystack, $needle) === FALSE)
		return (FALSE);
	return (TRUE);
}

?>