<?php

	class db extends mysqli
	{	
		public function __construct($host, $user, $pass, $db = null)
		{
			@parent::__construct($host, $user, $pass);
			
			if($this->connect_errno != 0)
			{
				die("Huston we have a database issue!");
			}
			
			if($db)
			{
				$this->select_db($db);
			}
		}
		
		function query($query)
		{
			$result = parent::query($query);
			if($result)
				return $result;
			else	
				echo "<span style='font-size: 14px;'>Mysql Error: <br>$query<br>".$this->error."</span><br><br>";
		}

		function select_db($database)
		{
			if(parent::select_db($database))
				return true;
			else
			{
				echo "Please select a database to connect to first.";
				exit(0);
			}
		}
	}
?>