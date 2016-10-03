<?php

	class Fact
	{

		/* Variables */

		static public $verbose = TRUE;
		private $_name = NULL;
		private $_constant = FALSE;
		private $_depend = array();
		private $_anull = array();
		private $_results = array();
		private $_avgtruth = 0;

		/*Standard Basic Methods*/
		
		function __construct($name) 
		{
			$this->_name = $name;
			if (self::$verbose == TRUE)
				print("Constructed: " . $this . PHP_EOL);
		}

		public function __destruct() 
		{
			if (self::$verbose == TRUE)
				print("Destructed: " . $this . PHP_EOL);
		}

		public function __toString() 
		{
			$this->calc_avg();
			return ($this->_name . ": " . $this->_avgtruth . "% TRUE\n");
		}

		public static function doc() 
		{
			print(file_get_contents("Fact.doc.txt"));
		}

		/* Class Specific Methods */

		private function calc_avg()
		{
			if ($this->_constant == TRUE)
				$this->_avgtruth = 100;
			else if (count($this->_results) != 0)
				$this->_avgtruth = array_sum($this->_results) / count($this->_results);
			else
				$this->_avgtruth = 0;
		}

		public function get_prob()
		{
			return ($this->_avgtruth);
		}

		public function prove($facts)
		{
			if (self::$verbose == TRUE)
			{
				print("Attempting to prove Fact " . $this . " using the following conditions:" . PHP_EOL);
				foreach ($facts as $fact)
					print($fact . PHP_EOL);
				unset($fact);
			}
			if ($this->_constant == TRUE)
			{
				if (self::$verbose == TRUE)
					print("Fact " . $this . " Proven TRUE as it is a Constant." . PHP_EOL);
				return (TRUE);
			}
		}

	/*	public function prove($facts) 
		{
			if (self::$verbose == TRUE)
			{
				print("Attempting to prove Fact " . $this . " using the following conditions:" . PHP_EOL);
				foreach ($facts as $fact)
					print($fact . PHP_EOL);
				unset($fact);
			}
			if ($this->_constant == TRUE)
			{
				if (self::$verbose == TRUE)
					print("Fact " . $this . " Proven TRUE as it is a Constant." . PHP_EOL);
				return (TRUE);
			}
			else
			{
				$status = NULL;
				print_r($this->_anull);
				foreach ($this->_anull as $rule)
				{
					$elems = explode("=>", $rule);
						print("Fact " . $this . "'s rule exploded on the => operater to provide :" . PHP_EOL);
						print_r($elems);
					$elems = explode("<", $elems[0]);
						print("Fact " . $this . "'s rule exploded on the < operater to provide :" . PHP_EOL);
						print_r($elems);
					$elems = array(explode("|", $elems[0]));
						print("Fact " . $this . "'s rule exploded on the | operater to provide :" . PHP_EOL);
						print_r($elems);
					$index = -1;
					foreach ($elems as $array)
					{
						$index = -1;
						while ($array[++$index])
							$array[$index] = array(explode("+", $array[0]));
					}
					unset($array);
					foreach ($elems as $array)
					{
						$array = array_map('trim' , $array);
					}
					unset ($array);
					unset($item);
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
				print_r($this->_depend);
				foreach ($this->_depend as $rule)
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
					foreach ($elems as $item)
							$item = explode("+", $item);
					unset($item);
					$elems = array_map('trim' , $elems);
					print("Fact " . $this . "'s rule exploded on the + operater to provide :" . PHP_EOL);
					print_r($elems);
					foreach ($elems as $or)
					{
						$results = array();
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
		} */

		public function c_anull($string)
		{
			if (self::$verbose == TRUE)
				print($string . " - added to the anullment list of " . $this . PHP_EOL);
			$this->_anull[] = $string;
		}

		public function c_depend($string)
		{
			if (self::$verbose == TRUE)
				print($string . " - added to the dependency list of " . $this . PHP_EOL);
			$this->_depend[0] = $string;
		}

		public function set_constant()
		{
			if (self::$verbose == TRUE)
				print($this . " was made a constant and will always resolve to TRUE" . PHP_EOL);
			$this->_constant = TRUE;
		}
	}
?>