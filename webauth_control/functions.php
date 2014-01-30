<?php
	
	set_include_path(".:/srv/www/htdocs/commontools/");
	require_once("includes/webauth_control.inc");
	$webauth = new webauth("dksecnas");
	
	$action = $_GET["action"];
	
	if($action == "addUser")
	{
		if($_GET["id"] != "")
			echo $webauth->addUser($_GET["id"]);
		else
			echo "Please set the user id";
	}
	else if($action == "eds")
	{
		require_once("includes/eds.inc");
		$eds = new EDS("alampiss");
		
		print "<pre>".print_r($eds, true)."</pre>";
	}
	else if($action == "addPermission")
	{
		if($_GET["appId"] != "" && $_GET["userId"] != "" && $_GET["roleId"] != "")
			$return = $webauth->addPermission($_GET["appId"], $_GET["userId"], $_GET["roleId"]);
		else
		{
			$return["status"] = "fail";
			$return["message"] = "Either the app id, user id or the role id is not set.";
		}
	}
	else if($action == "deletePermission")
	{
		if($_GET["permissionId"] != "")
			$return = $webauth->deletePermission($_GET["permissionId"]);
		else
		{
			$return["status"] = "fail";
			$return["message"] = "Please set the permission id.";
		}
	}
	else if($action == "addRole")
	{
		if($_GET["roleName"] != "" && $_GET["appId"] != "")
			$return = $webauth->addRole($_GET["roleName"], $_GET["appId"]);
		else
		{
			$return["status"] = "fail";
			$return["message"] = "Either the role name or the app id is not set.";
		}
	}
	else if($action == "deleteRole")
	{
		if($_GET["roleId"] != "" && $_GET["appId"] != "")
			$return = $webauth->deleteRole($_GET["roleId"], $_GET["appId"]);
		else
		{
			$return["status"] = "fail";
			$return["message"] = "Either the role id or the app id is not set.";
		}
	}
	else if($action == "searchUsers")
	{
		if($_GET["name"] != "")
		{
			$users_query = $db_internal->query("SELECT * FROM users WHERE user_name LIKE '$_GET[name]%' OR user_netid LIKE '$_GET[name]%' ORDER BY user_name ASC");
			
			if(mysqli_num_rows($users_query) == 0)
			{
				$eds = new EDS($_GET["name"]);

				$name = $eds->vals["cn"];
				$emplid = $eds->vals["emplId"];
				$netid = $eds->vals["uid"];
				
				$ret["user_emplid"] = $emplid;
				$ret["user_netid"] = $netid;
				$ret["user_name"] = $name;
				
				if($ret["user_emplid"] != "")
				{
					$webauth->addUser($emplid);
				}
				
				$users_query = $db_internal->query("SELECT * FROM users WHERE user_name LIKE '$_GET[name]%' OR user_netid LIKE '$_GET[name]%' ORDER BY user_name ASC");
			}
			
			
			$omit = explode(",", $_GET["omit"]);
			
			while($row = mysqli_fetch_assoc($users_query))
				if(!in_array($row["user_id"], $omit))
					$return[] = $row;
		}
	}
	else if($action == "newApp")
	{
		$webauth->registerApp($_POST["app_name"], $_POST["app_type"]);
		header("location: /");
	}
	else
	{
		echo "The method $action is not yet defined";
	}
	
	if($return != null)
		echo json_encode($return);
?>