<?php

	require_once("includes/mysqli.inc");
	require_once("includes/eds.inc");
	

	class webauth {
		
		function __construct($appid, $db_name = "webauth_control", $service = "", $splash = "")
		{	
			global $db_internal;
			session_start();
			
			if(!$_SESSION['webauth'] || empty($_SESSION['webauth']['netID']))
			{
				if($service == NULL)
					$service = ($_SERVER['HTTPS']?'https://':'http://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				else
					$service = ($_SERVER['HTTPS']?'https://':'http://').$_SERVER['HTTP_HOST'].$service;

				if($splash != NULL)
					$splash = 'https://'.$_SERVER['HTTP_HOST'].$splash;

				$_SESSION['webauth_service'] = $service;
				$_SESSION['webauth_splash'] = $splash;

				if($_SERVER['SERVER_ENV'] == 'staging'){
					header("Location:http://elvis.sunion.arizona.edu:8088/webauth/redirect.php?sid=".session_id());
					exit();
				}
				else
				{ 
					header("Location:https://webauth.arizona.edu/webauth/login?service=".($_SERVER['HTTPS']?'https://':'http://').$_SERVER['HTTP_HOST']."$port/commontools/webauth/redirect.php");
					exit();
				}
			}
			
			global $db;
			$db = new db_mysqli($db_name);
			
			$emplid = $_SESSION["webauth"]["emplid"];
			$this->db_internal = $db_internal = new db_mysqli("webauth_control", "192.168.217.218");
			$this->appid = $appid;
			
			
			$user = mysqli_fetch_assoc($this->db_internal->query("SELECT * FROM users WHERE user_emplid = $emplid"));
			$this->user = $user;
			
			$result = $db_internal->query("SELECT * FROM applications WHERE app_unid = '$appid'");
			if(mysqli_num_rows($result) == 1)
			{
				$pass = $this->hasPermissionLevel($level);
				if(!$pass)
				{
					die("You do not have permission to use this app");
				}
			}
			else
			{
				die("This is not a registered app");
			}
		}
		
		function hasPermissionLevel($level)
		{
			$db_internal = $this->db_internal;
			$appid = $this->appid;
			$user = $this->user;
			
			$result = $db_internal->query("SELECT * FROM applications WHERE app_unid = '$appid'");
			if(mysqli_num_rows($result) == 1)
			{
				$app = mysqli_fetch_assoc($result);
				
				$result = $db_internal->query("SELECT * FROM permissions LEFT JOIN roles USING(role_id) WHERE permissions.app_id = $app[app_id] AND (user_id = $user[user_id] OR user_id = 0) ORDER BY role_name ASC");
				
				//print "<pre>".print_r(mysqli_fetch_assoc($result), true)."</pre>";
				//exit();
				$pass = false;
				
				while($row = mysqli_fetch_assoc($result))
				{
					if($level == "" || $level == $row["role_name"] || $row["role_name"] == "admin")
					{
						$pass = true;
					}
				}
				
				return $pass;
			}
			else
			{
				die("This is not a registered app");
			}
		}
		
		
		function getRoles() {
			return $this->roles;
		}
		
		function getApp($appUnid)
		{
			$query = $this->db_internal->query("SELECT *, COUNT(permission_id) as num_users FROM applications 
															LEFT JOIN permissions USING (app_id) 
															LEFT JOIN users ON users.user_id=applications.app_owner 
															WHERE app_unid = '$appUnid'");
			return mysqli_fetch_assoc($query);
		}
		
		function registerApp($appName, $appType = "simple", $appUnid = "", $appOwner = "") {
			
			if($appOwner == "")
				$appOwner = $this->user["user_id"];
			
			if($appUnid == "")
				$appUnid = $this->generateRandomString();
			
			$this->db_internal->query("INSERT INTO applications (app_name, app_unid, app_type, app_owner) VALUES ('$appName', '$appUnid', '$appType', '$appOwner')");
			$app = $this->getApp($appUnid);
			
			$this->addRole("standard", $app["app_id"]);
			$user_role = $this->addRole("admin", $app["app_id"]);
			
			$this->addPermission($app["app_id"], $appOwner, $user_role["role"]["role_id"]);
		}
		
		function addPermission($appId, $userId, $roleId) {
			$exists = $this->db_internal->query("SELECT * FROM permissions WHERE app_id = $appId AND user_id = $userId AND role_id = $roleId");
			if(mysqli_num_rows($exists) > 0)
			{
				$return["status"] = "fail";
				$return["message"] = "User already has this permission.";
			}
			else
			{
				$this->db_internal->query("INSERT INTO permissions (app_id, user_id, role_id) VALUES ($appId, $userId, $roleId)");
			
				$return["status"] = "success";
				$return["message"] = "The permission was successfully created.";
			}
			
			return $return;
		}
		
		function deletePermission($permissionId) {
			$exists = $this->db_internal->query("SELECT * FROM permissions WHERE permission_id = $permissionId");
			if(mysqli_num_rows($exists) == 0)
			{
				$return["status"] = "fail";
				$return["message"] = "This permission does not exist.";
			}
			else
			{
				$this->db_internal->query("DELETE FROM permissions WHERE permission_id = $permissionId");
			
				$return["status"] = "success";
				$return["message"] = "The permission was successfully deleted.";
			}
			
			return $return;
		}
		
		function addRole($roleName, $appId) {
			
			$query = "SELECT * FROM roles WHERE role_name = '$roleName' AND app_id = $appId";
			$exists = $this->db_internal->query($query);
			if(mysqli_num_rows($exists) > 0)
			{
				$return["status"] = "fail";
				$return["message"] = "The role $roleName has already been created.";
			}
			else
			{
				$this->db_internal->query("INSERT INTO roles (role_name, app_id) VALUES ('$roleName', $appId)");
			
				$return["status"] = "success";
				$return["message"] = "The role $roleName was successfully created.";
			}
			
			$exists = $this->db_internal->query($query);
			$return["role"] = mysqli_fetch_assoc($exists);
			
			return $return;
		}
		
		function deleteRole($roleId, $appId) {
			
			$exists = $this->db_internal->query("SELECT * FROM roles WHERE role_id = '$roleId' AND app_id = $appId");
			if(mysqli_num_rows($exists) == 0)
			{
				$return["status"] = "fail";
				$return["message"] = "The role with the id $roleId does not exist.";
			}
			else
			{
				$row = mysqli_fetch_assoc($exists);
				$this->db_internal->query("DELETE FROM roles WHERE role_id = '$roleId' AND app_id = $appId");
			
				$return["status"] = "success";
				$return["message"] = "The role $row[role_name] was successfully deleted.";
			}
			
			return $return;
		}
		
		function getAllApps() {
			$query = $this->db_internal->query("SELECT *, COUNT(*) as num_users FROM applications LEFT JOIN permissions USING (app_id) LEFT JOIN users ON users.user_id=applications.app_owner GROUP BY app_id");
			
			while($row = mysqli_fetch_assoc($query))
				$return[] = $row;
			
			return $return;
		}
		
		function getAppsByUser() {
			$id = $this->user["user_id"];
			
			$user = mysqli_fetch_assoc($this->db_internal->query("SELECT * FROM users WHERE user_id = $id"));
			$no_admin = (($user["user_admin"] == 1)?"":"WHERE (users.user_id=$id)");
			
			$query = $this->db_internal->query("SELECT *, COUNT(*) as num_users FROM applications LEFT JOIN permissions USING (app_id) LEFT JOIN users ON users.user_id=applications.app_owner $no_admin GROUP BY app_id");
			
			while($row = mysqli_fetch_assoc($query))
				$return[] = $row;
			
			return $return;
		}
		
		function addUser($id, $admin = 0) {
			
			$eds = new EDS($id);
			
			$name = $eds->vals["cn"];
			$emplid = $eds->vals["emplId"];
			$netid = $eds->vals["uid"]; 
			
			if($name == "" || $name == null)
				return "The netid passed it not valid";
			
			$query = $this->db_internal->query("SELECT * FROM users WHERE user_netid = '$netid'");
			
			if(mysqli_num_rows($query) == 0)
			{
				$this->db_internal->query("INSERT INTO users (user_emplid, user_netid, user_name, user_admin) VALUES ($emplid, '$netid', '$name', $admin)");
				return "Successfully added $name to the user database";
			}
			else
			{
				return "A user with the netid $netid already exists.";
			}
		}
		
		function getUserEDSInfo() {
			require_once("includes/eds.inc");
			$eds = new EDS($_SESSION["webauth"]["netID"]);
			
			return $eds;
		}
		
		function getUserById($id) {
			require_once("includes/eds.inc");
			$eds = new EDS($id);
			
			return $eds;
		}
		
		function generateRandomString($length = 8) {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
			$string = "";    
			for ($p = 0; $p < $length; $p++) {
			    $string .= $characters[mt_rand(0, strlen($characters))];
			}
			
			return $string;
		}
	}
	
?>