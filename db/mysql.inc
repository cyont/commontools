<?php
class db_mysql{
		
	public $DBlink;
	public $query;
	public $result;
	
	
	
	function __construct($db = NULL, $host = NULL, $user = 'web', $pass = 'viv3nij'){
		require('/srv/www/htdocs/commontools/db/mysql_activeserver.inc');
		$activeServers = array('mysql_host', 'mysql_host2');
		
		if($host == NULL)
			$host = $activeServers[$active_server];
	
	
		
		$this->DBlink = mysql_connect($host, $user, $pass, true);
		if(!$this->DBlink){
			
		
			if($host==$activeServers[$active_server]){
				$this->connect_error($host, false);
				$this->DBlink = mysql_connect( $activeServers[++$active_server], $user, $pass, true);
				
				if($this->DBlink){
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
			if(mysql_select_db($db, $this->DBlink))
				return true;
			else	
				$this->select_error($db);
		}
	}
	
	function select($db){
		if(mysql_select_db($db, $this->DBlink))
			return true;
		else	
			$this->select_error($db);
	}
	
	function query($query = NULL){
		if($query != NULL)
			$this->query = $query;
			
		$this->result =  mysql_query($this->query, $this->DBlink);
		if($this->result)
			return $this->result;
		else{
			$this->query_error();
		}
	}
	//possibly turn off for test
	function escape($string, $length=NULL){
	    $string = $length?substr($string, 0, $length):$string;  
	    $string = mysql_real_escape_string($string);
	    return $string;
	}
	function connect_error($host, $die){
		$trace = debug_backtrace();
		file_put_contents ( "/srv/www/htdocs/commontools/db/mysql_error.inc" , date(DATE_RSS)." Error connecting to ".$host.": ".mysql_error().' - '.$trace[1]['file'].', line'.$trace[1]['line'].chr(10), FILE_APPEND );
		$this->email_error(" Error connecting to ".$host.": ".mysql_error($this->DBlink).' - '.$trace[1]['file'].', line '.$trace[1]['line']);
		if($_SERVER['SERVER_ENV'] == 'staging')
			die("Could not connect to database.");
		exit();	
	}
	function select_error($db){
		$trace = debug_backtrace();
		file_put_contents ( "/srv/www/htdocs/commontools/db/mysql_error.inc" , date(DATE_RSS)." Error selecting ".$db.": ".mysql_error($this->DBlink).' - '.$trace[1]['file'].', line'.$trace[1]['line'].chr(10), FILE_APPEND );
		$this->email_error(" Error selecting ".$db.": ".mysql_error($this->DBlink).' - '.$trace[1]['file'].', line '.$trace[1]['line']);
		if($_SERVER['SERVER_ENV'] == 'staging')
			die("Could not select database ".$db.".");
		exit();
	}
	function query_error(){
		$trace = debug_backtrace();
		file_put_contents ( "/srv/www/htdocs/commontools/db/mysql_error.inc" , date(DATE_RSS)." Error executiong ".$this->query.": ".mysql_error($this->DBlink).' - '.$trace[1]['file'].', line'.$trace[1]['line'].chr(10), FILE_APPEND );
		$this->email_error(" Error executiong: ".$this->query."; ".mysql_error($this->DBlink).' - '.$trace[1]['file'].', line '.$trace[1]['line']);
		if($_SERVER['SERVER_ENV'] == 'staging')
			print "Error executiong ".$this->query.": ".mysql_error($this->DBlink);
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
