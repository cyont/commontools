<?php
class db{
		
	public $DBlink;
	public $query;
	public $result;
	
	
	
	function __construct($db = NULL, $host = NULL, $user = 'web', $pass = 'viv3nij'){
		require('/Library/WebServer/commontools/mysql_activeserver.inc');
		$activeServers = array('mysql_host', 'mysql_host2');
		
		if($host == NULL)
			$host = $activeServers[$active_server];
	
	
		
		$this->DBlink = mysql_connect($host, $user, $pass, true);
		if(!$this->DBlink){
			
		
			if($host==$activeServers[$active_server]){
				$this->connect_error($host, false);
				$this->DBlink = mysql_connect( $activeServers[++$active_server], $user, $pass, true);
				
				if($this->DBlink){
					file_put_contents('/Library/WebServer/commontools/mysql_activeserver.inc', '<?php $active_server='.$active_server.'; ?>');
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
		file_put_contents ( "/Library/WebServer/commontools/mysql_error.inc" , date(DATE_RSS)." Error connecting to ".$host.": ".mysql_error().' - '.$trace[1]['file'].', line'.$trace[1]['line'].chr(10), FILE_APPEND );
		$this->email_error(" Error connecting to ".$host.": ".mysql_error($this->DBlink).' - '.$trace[1]['file'].', line '.$trace[1]['line']);
		die("Could not connect to database.");	
	}
	function select_error($db){
		$trace = debug_backtrace();
		file_put_contents ( "/Library/WebServer/commontools/mysql_error.inc" , date(DATE_RSS)." Error selecting ".$db.": ".mysql_error($this->DBlink).' - '.$trace[1]['file'].', line'.$trace[1]['line'].chr(10), FILE_APPEND );
		$this->email_error(" Error selecting ".$db.": ".mysql_error($this->DBlink).' - '.$trace[1]['file'].', line '.$trace[1]['line']);
		die("Could not select database ".$db.".");
	}
	function query_error(){
		$trace = debug_backtrace();
		file_put_contents ( "/Library/WebServer/commontools/mysql_error.inc" , date(DATE_RSS)." Error executiong ".$query.": ".mysql_error($this->DBlink).' - '.$trace[1]['file'].', line'.$trace[1]['line'].chr(10), FILE_APPEND );
		$this->email_error(" Error executiong ".$this->query.": ".mysql_error($this->DBlink).' - '.$trace[1]['file'].', line '.$trace[1]['line']);
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
		
		require_once('/Library/WebServer/commontools/phplib/mimemail/htmlMimeMail5.php');
		
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

function db_connect($host = NULL, $user = 'web', $pass = 'viv3nij'){
	
	require('/Library/WebServer/commontools/mysql_activeserver.inc');
	$activeServers = array('mysql_host', 'mysql_host2');
	
	if($host == NULL)
		$host = $activeServers[$active_server];


	
	$link = mysql_connect($host, $user, $pass);
	if($link){
		return $link;
	}
	
	else if($host==$activeServers[$active_server]){
		db_connect_error($host, false);
		$link = mysql_connect( $activeServers[++$active_server], $user, $pass);
		
		if($link){
			file_put_contents('/Library/WebServer/commontools/mysql_activeserver.inc', '<?php $active_server='.$active_server.'; ?>');
			return $link;
		}
		else{
			db_connect_error($host, true);
		}
	}
	
	else
		db_connect_error($host, true);
}
 

function email_error($error, $critical = 0){

	define("CRITICAL_EMAIL", "jmasson@email.arizona.edu");
	define("NONCRITICAL_EMAIL", "jmasson@email.arizona.edu");
	
	require_once('/Library/WebServer/commontools/phplib/mimemail/htmlMimeMail5.php');
	
	$mail = new htmlMimeMail5();

    $mail->setFrom('Trinity.sunion.arizona.edu<no-reply@email.arizona.edu>');
    
    $mail->setSubject('Database Error');
    
    $mail->setHTML($error);
    
	if($critical)
	    $result  = $mail->send(array(CRITICAL_EMAIL));
	else
		 $result  = $mail->send(array(NONCRITICAL_EMAIL));
}

function db_connect_error($host, $die){
	file_put_contents ( "/Library/WebServer/commontools/mysql_error.inc" , date(DATE_RSS)." Error connecting to ".$host.": ".mysql_error().' - '.$_SERVER['PHP_SELF'].chr(10), FILE_APPEND );
	email_error(date(DATE_RSS)." Error connecting to ".$host.": ".mysql_error().' - '.$_SERVER['PHP_SELF'], 1);
	if($die)
		die("Could not connect to database.");
}

function db_select_error($db){
	file_put_contents ( "/Library/WebServer/commontools/mysql_error.inc" , date(DATE_RSS)." Error selecting ".$db.": ".mysql_error().' - '.$_SERVER['PHP_SELF'].chr(10), FILE_APPEND );
	email_error(date(DATE_RSS)." Error selecting ".$db.": ".mysql_error().' - '.$_SERVER['PHP_SELF']);
	die("Could not select database.");
}

function db_query_error($query){
	file_put_contents ( "/Library/WebServer/commontools/mysql_error.inc" , date(DATE_RSS)." Error executiong ".$query.": ".mysql_error().' - '.$_SERVER['PHP_SELF'].chr(10), FILE_APPEND );
	email_error(date(DATE_RSS)." Error executiong ".$query.": ".mysql_error().' - '.$_SERVER['PHP_SELF']);
	print "Error executiong ".$query.": ".mysql_error();
	die("Database query failed.");
}


function db_select($db){
	if(mysql_select_db($db))
		return true;
	else	
		db_select_error($db);		
}

function db_query($query){
	$result =  mysql_query($query);
	if($result)
		return $result;
	else
		db_query_error($query);
}

function db_real_escape($string, $length=NULL){
    $string = $length?substr($string, 0, $length):$string;  
    $string = mysql_real_escape_string($string);
    return $string;
}

?>
