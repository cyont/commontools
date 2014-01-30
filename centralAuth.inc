<?php
	
	class CentralAuth
	{
		public function __construct()
		{	
			self::updateCertExp();
			
			$urlParts = explode('.', $_SERVER['HTTP_HOST']);
			$this->sub_domain = $urlParts[0];
			$this->domain = $urlParts[1];
		}

		function login($user, $pass)
		{	
			$result = self::validateUser($user, $pass);
			
			if($result["account_state"] == 1)
			{
				session_regenerate_id(true);		
				$id = $result["account_id"];
				
				$cert = self::makeCertForUser($id);
				
				$_SESSION["user"] = $result;
				$_SESSION["user"]["cert"] = $cert;
				
				$app = (($_GET['app'] == "")?"www":$_GET['app']).".";

				if(isset($_GET["ajax"]))
				{
					$return["status"] = "success";
					$return["message"] = "Login Successfull";
					$return["location"] = $_GET['return'];
					$return["app"] = $app;
				}
				else
					header("location: http://{$app}{$this->domain}.com$_GET[return]");
			}
			else if($result["account_state"] == 0)
			{
				if(isset($_GET["ajax"]))
				{
					$return["status"] = "warning";
					$return["message"] = "Your Account is Pending";
				}
				else
					header("location: /pending");
			}
			else
			{	
				if(isset($_GET["ajax"]))
				{
					$return["status"] = "fail";
					$return["message"] = "Wrong Username or Password";
				}
				else
					header("location: /login?return=$_GET[return]&app=$_GET[app]&error=400");
			}
			
			return $return;
		}
		
		function makeCertForUser($userId)
		{
			global $db;
			self::cleanDatabase();

			$cert = self::randomString(30);
			$expr = self::mysqlTime(60*60);

			$query = $db->query("SELECT * FROM central_auth_certs WHERE account_id=$userId and cert_is_active=1 and cert_expiration > '".self::mysqlTime()."'");
			$certNum = mysqli_num_rows($query);

			if($certNum == 1)
			{
				$dbCert = mysqli_fetch_assoc($query);
				$cert = $dbCert["cert_value"];
			}
			else
			{
				$db->query("INSERT INTO central_auth_certs (cert_value, account_id, cert_expiration, cert_is_active, cert_ip) VALUES ('$cert', $userId, '$expr', 1, '".$_SERVER['REMOTE_ADDR']."')");
			}

			setcookie("usercert", $cert, 0, "/", ".{$this->domain}.com", 0 , 1);

			$db->query("UPDATE accounts SET account_date_accessed='".self::mysqlTime()."' WHERE account_id=$userId");

			return $cert;
		}

		function updateCertExp($cert = "")
		{
			global $db;
			$cert = ($cert == "")?$_COOKIE["usercert"]:$cert;
			
			$expr = self::mysqlTime(60*60);
			$db->query("UPDATE central_auth_certs SET cert_expiration='$expr' WHERE cert_value='$cert' AND cert_is_active=1");
		}

		function revokeCert($cert)
		{
			global $db;
			$db->query("UPDATE central_auth_certs SET cert_is_active=0 WHERE cert_value='$cert'");
		}

		function getPrivilegesFromUsername($username)
		{
			global $db;
			
			$query = $db->query("SELECT * FROM accounts WHERE account_username='$username'");
			$result = mysqli_fetch_assoc($query);
			return $result["account_type"].", ".$result["account_group"];
		}

		function getUserFromCert($cert)
		{	
			global $db;
			if(self::isCertValid($cert) == 1)
			{
				$query = $db->query("SELECT * FROM central_auth_certs WHERE cert_value='$cert'");
				if(mysqli_num_rows($query) == 1)
				{
					$result = mysqli_fetch_assoc($query);
					$id = $result["account_id"];

					$query = $db->query("SELECT * FROM accounts WHERE account_id=$id");
					$result = mysqli_fetch_assoc($query);

					return $result;
				}
				else
				{
					return null;
				}
			}
			else if(self::isCertValid($cert) == -1)
			{			
				self::logout("Expired");
			}
			else
			{
				return null;
			}
		}

		function getUserById($id)
		{
			global $db;
			
			$query = $db->query("SELECT * FROM accounts WHERE account_id=$id");
			$result = mysqli_fetch_assoc($query);
			
			if(mysqli_num_rows($query) != 1)
				return false;
			
			return $result;
		}

		function getUserFromResetToken($token)
		{
			$query = db_query("SELECT * FROM accounts WHERE account_resetToken='$token'");
			if(mysql_num_rows($query) == 1)
			{
				$result = mysql_fetch_assoc($query);

				return $result;
			}
			else
			{
				return null;
			}
		}

		function isCertValid($cert, $error="")
		{
			global $db;
			$cert = $db->real_escape_string($cert);

			$query = $db->query("SELECT * FROM central_auth_certs WHERE cert_value='$cert' AND cert_is_active=1 AND cert_expiration > '".self::mysqlTime()."'");
			$certNum = mysqli_num_rows($query);

			if($certNum == 1)
			{				
				$cert = mysqli_fetch_assoc($query);
				if($cert['cert_is_active'])
					return 1;
				else
					return -1;
			}
			else if($certNum == 0)
			{	
				$query = $db->query("SELECT * FROM central_auth_certs WHERE cert_value='$cert' AND cert_expiration > '".self::mysqlTime(-60)."'");
				$certN = mysqli_num_rows($query);

				if($certN > 0)
					return -1;
				else
					return 0;
			}
			else if($certNum > 1)
			{
				if($error != '')
				{
					echo $error;
					db_query("UPDATE central_auth_certs SET cert_is_active=0 WHERE cert='$cert'");
					exit(0);
				}
				self::cleanDatabase();
				self::isCertValid($cert, "Brute force attempt to access server detected.");
			}
		}

		function setUserPasswordFromToken($token, $password)
		{
			$password = md5($password);
			$user = self::getUserFromResetToken($token);

			if($user)
			{
				$userId = $user['id'];
				db_query("UPDATE cdsengineers.members SET password='$password', resetToken='' WHERE id='$userId'");

				return true;
			}
			else
			{
				return false;
			}
		}

		function createUser($fullname, $username, $password)
		{
			global $db;
			
			if(self::isUser($username))
				return false;

			$password = md5($password);
			$token = self::randomString(30);

			$result = $db->query("INSERT INTO accounts (account_fullname, account_username, account_password, account_activation_key) VALUES
			 						('$fullname', '$username', '$username', '$password', '$token')");

			return true;
		}

		function activateUser($token)
		{	
			global $db;
			
			$result = $db->query("SELECT * FROM accounts WHERE account_activation_key='$token'");
			if(mysqli_num_rows($result) == 1)
			{
				$row = mysqli_fetch_assoc($result);
				$userId = $row['account_id'];

				$db->query("UPDATE accounts SET account_state=1, account_activation_key='' WHERE account_id=$userId");

				return $row;
			}
			else
			{
				return false;
			}
		}

		function recoverUser($username, $email)
		{
			$result = db_query("SELECT * FROM accounts WHERE account_username='$username' and email='$email'");
			$num = mysql_num_rows($result);

			if($num == 1)
			{
				$row = mysql_fetch_assoc($result);
				$token = self::randomString(30);
				$userId = $row['id'];

				if($row['account_state'])
				{
					if($row["accountType"] == "student")
					{
						db_query("UPDATE accounts SET account_resetToken='$token' WHERE id='$userId'");

						$message = '
						  	Hello '.$row['fullname'].',<br>You have chosen to reset your password for your CDS Engineers account. <a href="http://cas.team2134.com/resetPassword?token='.$token.'">Click here</a> to reset your password. If you have not chosen to reset your password, ignore this message.<br><br>Thanks,<br>CDS Engineers
						';
						sendEmail($row["email"], "CDS Engineers Password Reset", $message);

						return 1;
					}
					else
					{
						return -1;
					}
				}
				else
					return 0;
			}
			else
				return 0;
		}

		function validateUser($username, $password)
		{
			global $IS_IN_SAFE_MODE;
			global $SAFE_MODE_SUPER_USER_PW;
			global $db;

			if($IS_IN_SAFE_MODE && md5($password) == $SAFE_MODE_SUPER_USER_PW)
			{
				$login = $db->query("SELECT * FROM accounts WHERE account_username='$username'");
			}
			else
			{	                                                                                                                         
				$login = $db->query("SELECT * FROM accounts WHERE account_username='$username' AND account_password='".md5($password)."'");
			}

			$row = mysqli_fetch_assoc($login);
			$num = mysqli_num_rows($login);
			
			if($num != 1)
			{	
				$row["account_state"] = -1;
			}
			
			return $row;
		}

		function isActiveUser($username)
		{
			global $db;
			$login = $db->query("SELECT account_state FROM accounts WHERE account_username='$username'");
			$row = mysqli_fetch_assoc($login);
			
			if(self::isUser($username) && $row["account_state"])
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		function isUser($username)
		{
			global $db;
			$login = $db->query("SELECT * FROM accounts WHERE account_username='$username'");
			$num = mysqli_num_rows($login);

			if($num == 1)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		function isLevel($pos = "")
		{
			$pass = false;
			
			if($_COOKIE["usercert"] != "" && ($_COOKIE["usercert"] != $_SESSION["user"]["cert"]))
			{
				$user = self::getUserFromCert($_COOKIE["usercert"]);
				$_SESSION["user"] = $user;
				$_SESSION["user"]["cert"] = $_COOKIE["usercert"];
			}
			else
				$user = self::getUserFromCert($_COOKIE["usercert"]);
			
			$username = $user["account_username"];
			if(self::isActiveUser($username))
			{
				$priv = self::getPrivilegesFromUsername($username);
				
				if($pos == "")
					$pass = true;
				else if(strpos($priv, "admin") !== false)
					$pass = true;	
				else if(strpos($priv, $pos) !== false)
					$pass = true;	
			}
			else if(self::isUser($username))
			{
				header("location: http://cas.{$this->domain}.com/pending");
				exit(0);
			}

			return $pass;
		}

		function protect($level = "")
		{	
			if($_SERVER["SYS_ENV"] == "local_dev_box")
				return;
			
			$pass = self::isLevel($level);
			if(!$pass)
			{
				$pass = self::isLevel();
				
				if(!$pass)
					self::logout("403");
				else
				{
					header("location: http://cas.{$this->domain}.com/");
				}
			}
		}
		
		function logout($error = "")
		{
			self::revokeCert($_COOKIE["usercert"]);

			unset($_SESSION);
			
			if($_COOKIE["PHPSESSID"] != "")
				session_destroy();

			setcookie("notebowl", "", time()-200, "/", ".{$this->domain}.com", 0 , 1);
			setcookie("usercert", "", time()-200, "/", ".{$this->domain}.com", 0 , 1);

			$loc = $_SERVER['REQUEST_URI'];
			$loc = str_replace("logout", "", $loc);

			header("location: http://cas.{$this->domain}.com/login?app={$this->sub_domain}&return=".$loc.(($error != "")?("&error=".$error):""));
			exit(0);
		}
		
		function getErrorMessageFromCode($code)
		{
			if($code == "400") {
					return '<div id="error">Your username or password was incorrect.</div>';
			}
			else if($code == "403") {
					return '<div id="error">You tried to access a restricted area. Please login.</div>';
			}
		}

		function mysqlTime($offset = 0)
		{

			return date('Y-m-d H:i:s', strtotime("+".$offset." seconds"));
			date_default_timezone_set('America/Phoenix');
		}

		function randomString($length = 20)
		{
		    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		    $string = "";    
		    for ($p = 0; $p < $length; $p++) {
		        $string .= $characters[mt_rand(0, strlen($characters))];
		    }
		    return $string;
		}

		function cleanDatabase()
		{
			global $db;
			
			//Remove all entries that are older than the current time
			$query = $db->query("SELECT * FROM central_auth_certs WHERE cert_expiration < '".self::mysqlTime()."'");
			while($dbCert = mysqli_fetch_assoc($query))
			{
				$db->query("UPDATE central_auth_certs SET cert_is_active=0 WHERE cert_id=".$dbCert["cert_id"]);
			}

			//Remove all reccords that exist more than once for the same user but leave the most recent cert active
			$query = $db->query("SELECT * FROM central_auth_certs WHERE account_id IN (SELECT account_id FROM central_auth_certs WHERE cert_is_active=1 GROUP BY account_id HAVING count(account_id) > 1) ORDER BY cert_expiration DESC LIMIT 1, 5");
			while($dbCert = mysqli_fetch_assoc($query))
			{
				$db->query("UPDATE central_auth_certs SET cert_is_active=0 WHERE cert_id=".$dbCert["cert_id"]);
			}

		}
	}
?>