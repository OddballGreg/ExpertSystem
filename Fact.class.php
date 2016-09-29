<?php

	class Fact
	{

		/* Variables */

		static public $verbose = TRUE;
		private $_name = NULL;
		private $_depend = NULL;
		private $_anull = NULL;
		private $_constant = FALSE;

		/*Standard Basic Methods*/
		
		function __construct($name) 
		{
			$_name = $name;
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
					$elems = explode("=>", $rule);
						print("Fact " . $this . "'s rule exploded on the => operater to provide :" . PHP_EOL);
						print_r($elems);
					$elems = explode("<", $elems[0]);
						print("Fact " . $this . "'s rule exploded on the < operater to provide :" . PHP_EOL);
						print_r($elems);
					$elems = explode("|", $elems[0]);
						print("Fact " . $this . "'s rule exploded on the | operater to provide :" . PHP_EOL);
						print_r($elems);
					$index = -1;
					while ($elems[++$index])
							$elems[$index] = explode("+", $elems[$index]);
					print("Fact " . $this . "'s rule exploded on the + operater to provide :" . PHP_EOL);
					print_r($elems);
					foreach ($elems as $or)
					{
						$results;
						foreach ($or as $and)
						{
							$resolution = 0;
							foreach ($and as $item)
							{
								if ($facts[$item]->prove($facts) === TRUE)
									$resolution++;
							}
							if ($resolution == count($and))
							$results[] = FALSE;
						}
						if (in_array(FALSE, $results) == TRUE)
						{
							if ($status === NULL)
								$status = FALSE;
							else if ($status == TRUE)
								$status = FALSE;
						}
					}
				}
				foreach ($_depend as $rule)
				{
					$elems = explode("=>", $rule);
						print("Fact " . $this . "'s rule exploded on the => operater to provide :" . PHP_EOL);
						print_r($elems);
					$elems = explode("<", $elems[0]);
						print("Fact " . $this . "'s rule exploded on the < operater to provide :" . PHP_EOL);
						print_r($elems);
					$elems = explode("|", $elems[0]);
						print("Fact " . $this . "'s rule exploded on the | operater to provide :" . PHP_EOL);
						print_r($elems);
					$index = -1;
					while ($elems[++$index])
						$elems[$index] = explode("+", $elems[$index]);
					print("Fact " . $this . "'s rule exploded on the + operater to provide :" . PHP_EOL);
					print_r($elems);
					foreach ($elems as $or)
					{
						$results;
						foreach ($or as $and)
						{
							$resolution = 0;
							foreach ($and as $item)
							{
								if ($facts[$item]->prove($facts) === TRUE)
									$resolution++;
							}
							if ($resolution == count($and))
							$results[] = TRUE;
						}
						if (in_array(TRUE, $results) == TRUE)
						{
							if ($status === NULL)
								$status = TRUE;
							else if ($status == FALSE)
								$status = "UNDETERMINED";
						}
					}
				}
				//check dependencies
				if ($status === NULL)
					return (FALSE);
				else if ($status === FALSE)
					return (FALSE);
				else if ($status === TRUE)
					return (TRUE);
				else
					return ("UNDETERMINED");
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