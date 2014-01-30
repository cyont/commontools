<?php
class session_handler{
	private $db;
	function open($savePath, $sessionName){
		$this->db = new mysqli('mysql_host', 'web', 'viv3nij', 'session');
		return true;
	}
	function close(){
		$this->db->close();
	}
	function read($sessionId){
		$sessionId = $this->db->real_escape_string($sessionId);
		$result = $this->db->query("SELECT GET_LOCK('$sessionId', 30)");
		$query = "SELECT data from session where session_id='$sessionId'";
		$result = $this->db->query($query);
		if($result){
			$row = $result->fetch_assoc();
			return $row['data'];
			
		}
		else{
			return '';
			
		}
	}
	function write($sessionId, $data){
		$sessionId = $this->db->real_escape_string($sessionId);
		$data = $this->db->real_escape_string($data);
		$query = "INSERT INTO session set session_id='$sessionId', data='$data', accessed_on=NOW() ON DUPLICATE KEY UPDATE data='$data', accessed_on=NOW()";
		$this->db->query($query);
	}
	function destroy($sessionId){
		$query = "delete from session where session_id='$sessionId'";
		$result = $this->db->query($query);
		//var_dump($result, $query);
		return true;
	}
	function gc($lifetime){
		$this->db->query("delete from session where accessed_on<".(time()-$lifetime));	
	}
	function regenerate_id(){
		$this->destroy(session_id());
		session_regenerate_id();
	}
}

$handler = new session_handler();
session_set_save_handler(
    array($handler, 'open'),
    array($handler, 'close'),
    array($handler, 'read'),
    array($handler, 'write'),
    array($handler, 'destroy'),
    array($handler, 'gc')
    );

// the following prevents unexpected effects when using objects as save handlers
register_shutdown_function('session_write_close');