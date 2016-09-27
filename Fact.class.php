<?php

	class Fact
	{

		/* Variables */

		static public $verbose = 0;
		private $_name = NULL;
		private $_dependencies = NULL;

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
		}
	}
?>