<?php

	class Fact
	{

		/* Variables */

		static public $verbose = 1;
		private $_name = NULL;
		private $_depend = NULL;
		private $_anull = NULL;
		private $_constant = FALSE;

		/*Standard Basic Methods*/
		
		function __construct() 
		{
			if (self::$verbose == TRUE)
				print("Constructed: " . $this . PHP_EOPL);
		}

		public function __destruct() 
		{
			if (self::$verbose == TRUE)
				print("Destructed: " . $this . PHP_EOPL);
		}

		public function __toString() 
		{
			//if (self::$verbose == TRUE)
			//	return ();
			return ($this->_name);
		}

		public static function doc() 
		{
			print(file_get_contents("Fact.doc.txt"));
		}

		/* Class Specific Methods */

		public function prove($facts) 
		{
			if (self::$verbose == TRUE)
			{
				print("Attempting to prove Fact " . $this . "using the following conditions:" . PHP_EOL);
				foreach ($facts as $fact)
					print($fact . PHP_EOL);
			}
			if ($this->_constant == TRUE)
			{
				if (self::$verbose == TRUE)
					print("Fact " . $this . "Proven TRUE as it is a Constant." . PHP_EOL);
					return (TRUE);
			}
			else
			{
				$status = NULL;
				foreach ($_anull as $rule)
				{
					$elems = explode(" ", $rule);
					//Prove each element, then check following symbols to determine how to proceed
				}
			}
		}

		public function c_anull($string)
		{
			if (self::$verbose == TRUE)
				print($string . "added to the anullment list of " . $this . PHP_EOPL);
			$this->_anull[] = $string;
		}

		public function c_depend($string)
		{
			if (self::$verbose == TRUE)
				print($string . "added to the dependency list of " . $this . PHP_EOPL);
			$this->_depend[] = $string;
		}

		public function set_constant()
		{
			if (self::$verbose == TRUE)
				print($this . "was made a constant and will always resolve to TRUE" . PHP_EOPL);
			$this->_constant = TRUE;
		}
	}
?>