<?php
class db_mysqli extends mysqli{
	public $query;
	function __construct($db = NULL, $host = NULL, $user = 'web', $pass = 'viv3nij'){
		require('/srv/www/htdocs/commontools/db/mysql_activeserver.inc');
		$activeServers = array('mysql_host', 'mysql_host2');
		
		if($host == NULL)
			$host = $activeServers[$active_server];
			
		@parent::__construct($host, $user, $pass);
		
		if($this->connect_errno != 0){
			
		
			if($host==$activeServers[$active_server]){
				$this->connect_error($host, false);
				
				@parent::__construct($activeServers[++$active_server], $user, $pass);
				if($this->connect_errno == 0){
					file_put_contents('/srv/www/htdocs/commontools/db/mysql_activeserver.inc', '<?php $active_server='.$active_server.'; ?>');
					return true;
				}
				else{
					$this->connect_error($host, true);
				}
			}
			
			else
				$this->connect_error($host, true);
		}
		
		if($db){
			$this->select_db($db);
		}
	}
	function select_db($db){
		if(parent::select_db($db))
			return true;
		else	
			$this->select_error($db);
	}
	function query($query){
		$this->query = $query;
		$result = parent::query($query);
		if($result)
			return $result;
		else	
			$this->query_error($query);
	}
	function escape($string, $length=NULL){
	    $string = $length?substr($string, 0, $length):$string;  
	    $string = parent::real_escape_string($string);
	    return $string;
	}
	function connect_error($host, $die){
		$trace = debug_backtrace();
		file_put_contents ( "/srv/www/htdocs/commontools/db/mysql_error.inc" , date(DATE_RSS)." Error connecting to ".$host.": ".$this->connect_error.' - '.$trace[1]['file'].', line'.$trace[1]['line'].chr(10), FILE_APPEND );
		$this->email_error(" Error connecting to ".$host.": ".$this->connect_error.' - '.$trace[1]['file'].', line '.$trace[1]['line']);
		if($_SERVER['SERVER_ENV'] == 'staging')
			die("Could not connect to database.");
		exit();	
	}
	function select_error($db){
		$trace = debug_backtrace();
		file_put_contents ( "/srv/www/htdocs/commontools/db/mysql_error.inc" , date(DATE_RSS)." Error selecting ".$db.": ".$this->error.' - '.$trace[1]['file'].', line'.$trace[1]['line'].chr(10), FILE_APPEND );
		$this->email_error(" Error selecting ".$db.": ".$this->error.' - '.$trace[1]['file'].', line '.$trace[1]['line']);
		if($_SERVER['SERVER_ENV'] == 'staging')
			die("Could not select database ".$db.".");
		exit();
	}
	function query_error(){
		$trace = debug_backtrace();
		file_put_contents ( "/srv/www/htdocs/commontools/db/mysql_error.inc" , date(DATE_RSS)." Error executiong ".$this->query.": ".$this->error.' - '.$trace[1]['file'].', line'.$trace[1]['line'].chr(10), FILE_APPEND );
		$this->email_error(" Error executiong: ".$this->query."; ".$this->error.' - '.$trace[1]['file'].', line '.$trace[1]['line']);
		if($_SERVER['SERVER_ENV'] == 'staging')
			print "Error executiong: ".$this->query."; ".$this->error;
		exit();
	}
	function email_error($message){
		ob_start();
			var_dump($_SESSION);
			print '<br />';
			var_dump($_SERVER);
		$env = ob_get_clean();
		$env = nl2br($env);
		$message = date(DATE_RSS).$message.'<br /><br />'.$env;
		
		define("CRITICAL_EMAIL", "jmasson@email.arizona.edu");
		define("NONCRITICAL_EMAIL", "jmasson@email.arizona.edu");
		
		require_once('/srv/www/htdocs/commontools/phplib/mimemail/htmlMimeMail5.php');
		
		$mail = new htmlMimeMail5();
	
	    $mail->setFrom('Trinity.sunion.arizona.edu<no-reply@email.arizona.edu>');
	    
	    $mail->setSubject('Database Error');
	    
	    $mail->setHTML($message);
	    
		if($critical)
		    $result  = $mail->send(array(CRITICAL_EMAIL));
		else
			 $result  = $mail->send(array(NONCRITICAL_EMAIL));
	}
	
}
?>