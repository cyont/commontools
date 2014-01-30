<?php
	
	set_include_path(".:/srv/www/htdocs/commontools/");
	
	require_once("includes/webauth_control.inc");
	$webauth = new webauth("dksecnas");
?>


<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Webauth Control</title>
		
		<link rel="stylesheet" href="/styles.css" type="text/css" />
		<link href="/bootstrap/css/bootstrap.css" rel="stylesheet">
	    <style>
	      body {
	        padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */
	      }
	    </style>
	    <link href="/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
		<script src="/bootstrap/js/jquery.js"></script>

	    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
	    <!--[if lt IE 9]>
	      <script src="/bootstrap/js/html5shiv.js"></script>
	    <![endif]-->
		
	</head>
	
	<body>
		
		<div class="navbar navbar-inverse navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
	            		<span class="icon-bar"></span>
	            		<span class="icon-bar"></span>
	          		</button>
	          		<a class="brand" href="/">Webauth Control</a>
	          		<div class="nav-collapse collapse">
	            		<ul class="nav">
							<li class="active"><a href="#">Home</a></li>
							<li><a href="#">Analytics</a></li>
							<li><a href="#">My Profile</a></li>
	            		</ul>
						<ul class="nav pull-right">
							<li><a href="#">Logout</a></li>
						</ul>
	          		</div><!--/.nav-collapse -->
	        	</div>
	      	</div>
		</div>
		
		<div class="container">
		<?php if($_GET["page"] == "") { ?>
		<h3>Registered Apps</h3>
		
		<table id="apps">
			<tr>
				<th>App Name</th>
				<th>App Unid</th>
				<th>Num Users</th>
				<th>App Owner</th>
			</tr>
			<?php
				$apps = $webauth->getAppsByUser();
				foreach($apps as $app)
				{
					echo "
					<tr>
						<td><a href='/app/$app[app_unid]'>$app[app_name]</a></td>
						<td>$app[app_unid]</td>
						<td>$app[num_users]</td>
						<td><a href='/users/$app[user_netid]'>$app[user_name]</td>
					</tr>";
				}
			?>
		</table>
		<br>
		<a class="btn btn-success" href='/app/new'>New App</a>
		
		<?php 
			} else if($_GET["page"] == "app") {
				
			if($_GET["id"] == "new")
			{
				echo "<h3>New App</h3>
				<br><br>
				<form action='/functions/newApp' method='post'>
					App Name: <input type='text' name='app_name' /><br>
					App Type: <select name='app_type'>
								<option value='simple'>simple</option>
								<option value='extended'>extended</option>
							</select>
				</form>";
				exit();
			}
			
			$app = $webauth->getApp($_GET["id"]);
			if($app["app_id"] == "")
			{
				echo "App not found.";
				exit();
			}
			
			$page = ($_GET["action"] == "")?"info":$_GET["action"];
			
			$roles_query = $db_internal->query("SELECT * FROM roles WHERE app_id = $app[app_id] ORDER BY role_name ASC");
			
			eval("\$$page = \" selected\";");
			
			if($app["app_type"] != "simple")
				$roles_link = "<a class='button$roles' href='/app/$app[app_unid]/roles'>Edit Roles</a>";
			
			echo "
				<div id='navBar'>
					<a class='button$info' href='/app/$app[app_unid]'>App Info</a>
					<a class='button$users' href='/app/$app[app_unid]/users'>Edit Users</a>
					$roles_link
				</div>
				
				<div id='content'>
			";
			
			if($page == "info")
			{
				echo "
					App Name: $app[app_name]
					<br>
					App Type: $app[app_type]
					<br>
					Number of Users: $app[num_users]
					<br>
					Created: ".date("M jS, Y \a\\t g:ia", strtotime($app["app_date"]))."
					<br>
					Owner: <a href='/users/$app[user_netid]'>$app[user_name]</a>
				";
			}
			else if($page == "users")
			{
				$users_query = $db_internal->query("SELECT * FROM permissions LEFT JOIN users USING(user_id) LEFT JOIN roles USING(role_id) WHERE permissions.app_id = $app[app_id] ORDER BY role_name, user_name ASC");
				
				$users_roles = null;
				while($user = mysqli_fetch_assoc($users_query))
				{
					$users_roles[$user["role_name"]][] = $user;
				}
				
				echo "<div class='tabbable' style='margin-bottom: 18px;'>
						<ul class='nav nav-tabs'>";
				$i = 0;
				while($role = $roles_query->fetch_assoc())
				{
					//echo "<pre>".print_r($role, true)."</pre>";
					
					$active = ($i++ == 0)?"class='active'":"";
					echo "<li $active><a href='#role-$role[role_id]' data-toggle='tab'>$role[role_name]</a></li>";	
				}
				
				echo "</ul>
				<div class='tab-content' style='padding-bottom: 9px; border-bottom: 1px solid #ddd;'>";
				
				$i = 0;
				mysqli_data_seek($roles_query,0);
				while($role = $roles_query->fetch_assoc())
				{
					$active = ($i++ == 0)?"active":"";
					echo "<div class='tab-pane $active' id='role-$role[role_id]'>
							<ul class='userList'>";
							if(count($users_roles[$role["role_name"]]) > 0)
								foreach($users_roles[$role["role_name"]] as $user)
									echo "<li user_id='$user[user_id]'>
											<a href='/users/$user[user_netid]'>$user[user_name]</a>
											<span permission_id='$user[permission_id]' class='removeUser badge badge-important'>X</span>
										</li>";
							echo "
								<li><input type='text' class='addUserToRole' role_id='$role[role_id]' placeholder='add user to role' /></li>
							</ul>
						  </div>";
				}
				
				echo "</div>
			</div> <!-- /tabbable -->";
				
				echo "<div style='clear:both'></div>
					<div id='peopleSearch'></div>
					<script>
						$('.removeUser').on('click', function() {
							$.get('/functions/deletePermission?permissionId='+$(this).attr('permission_id'), function(data) {
								if(data['status'] == 'success')
									window.location.reload();
							}, 'json')
						});
					
						$('.addUserToRole').on('keyup', function(e) {
							
							//Escape Key
							if(e.keyCode == 27 || $(this).val() == '')
							{
								$('#peopleSearch').hide();
								return;
							}
							
							
							//Enter Key
							if(e.keyCode == 13) {
								if($('#peopleSearch').is(':visible'))
								{
									currentAddUserId = $('#peopleSearch .person.selected').attr('user_id');
									
									$(this).val($('#peopleSearch .person.selected').text());
									$('#peopleSearch').hide();
								
									roleId = $(this).attr('role_id');
									
									$.get('/functions/addPermission?appId=$app[app_id]&userId='+currentAddUserId+'&roleId='+roleId, function(data) {
										if(data['status'] == 'success')
											window.location.reload();
									}, 'json')
								}
								
								return;
							}
							
							//Down Key
							if(e.keyCode == 40) {
								if($('#peopleSearch .person.selected').length == 0 || $('#peopleSearch .person.selected').next().length == 0)
								{
									$('#peopleSearch .person').removeClass('selected')
									$('#peopleSearch .person:first').addClass('selected')
								}
								else
								{
									$('#peopleSearch .person.selected').removeClass('selected').next().addClass('selected');
								}
								
								return;
							}
							
							//Up Key
							if(e.keyCode == 38) {
								if($('#peopleSearch .person.selected').length == 0 || $('#peopleSearch .person.selected').prev().length == 0)
								{
									$('#peopleSearch .person').removeClass('selected')
									$('#peopleSearch .person:last').addClass('selected')
								}
								else
								{
									$('#peopleSearch .person.selected').removeClass('selected').prev().addClass('selected');
								}
								
								return;
							}
							
							el = $(this).offset();
							omit = '';
							
							$(this).parent().parent().find('li[user_id]').each(function() {
								omit += $(this).attr('user_id')+',';
							});
							
							
							$('#peopleSearch').offset(el).css('top', '+=28').show();
							
							$.get('/functions/searchUsers?omit='+omit+'&name='+$(this).val(), function(data) {
								$('#peopleSearch').html('');
								for(index in data)
									$('#peopleSearch').append('<div user_id=\''+data[index]['user_id']+'\' class=\'person\'>'+data[index]['user_name']+'</div>');
								
							}, 'json');
						})
					</script>
				";
			}
			else if($page == "roles")
			{	
				echo "
					Current Roles
					<br>
					<ul id='roles'>";
				
				while($role = mysqli_fetch_assoc($roles_query))
					echo "<li class='role' roleId='$role[role_id]'>$role[role_name] <span class='deleteRole badge badge-important'>X</span></li>";
				
				echo "<li><input type='text' id='newRole' placeholder='new role' />";
				echo "</ul>";
				
				echo "
				<script>
					$('#newRole').on('keydown', function(e) {
						if(e.keyCode == 13) {
							$.get('/functions/addRole?appId=$app[app_id]&roleName='+$(this).val(), function(data) {
								if(data['status'] == 'success')
									window.location.reload();
							}, 'json');
						}
					});
					
					$('.role').on('click', function() {
						$.get('/functions/deleteRole?appId=$app[app_id]&roleId='+$(this).attr('roleId'), function(data) {
							if(data['status'] == 'success')
								window.location.reload();
						}, 'json');
					});
				</script>";
			}
			
			echo "</div>";
		} else if($_GET["page"] == "users") {
			$user_netid = $_GET["id"];
			
			if($user_netid == "")
			{
				echo "User main page";
			}
			else
			{
				$query = $db_internal->query("SELECT * FROM users WHERE user_netid = '$user_netid'");
				$user = mysqli_fetch_assoc($query);
				
				echo "<h3>$user[user_name]</h3>";
				
				echo "
					<table id='users'>
						<tr>
							<th>App Name</th>
							<th>App Role</th>
							<th>Num Users</th>
							<th>App Owner</th>
						</tr>
				";
				
				$query = $db_internal->query("SELECT * FROM permissions LEFT JOIN applications USING(app_id) LEFT JOIN roles USING(role_id) LEFT JOIN users ON app_owner = users.user_id WHERE permissions.user_id = '$user[user_id]'");
				while($row = mysqli_fetch_assoc($query))
				{
					$num_people = $webauth->getApp($row["app_unid"]);
					$num_people = $num_people["num_users"];
					
					echo "
					<tr>
						<td>
							<a href='/app/$row[app_unid]'>$row[app_name]</a>
						</td>
						<td>$row[role_name]</td>
						<td>$num_people</td>
						<td>
							<a href='/users/$row[user_netid]'>$row[user_name]</a>
						</td>
					</tr>";
				}
				
				echo "</table>";
			}
		}?>
		</div> <!-- /container -->

	    <!-- Le javascript
	    ================================================== -->
	    <!-- Placed at the end of the document so the pages load faster -->
	    <script src="/bootstrap/js/bootstrap-transition.js"></script>
	    <script src="/bootstrap/js/bootstrap-alert.js"></script>
	    <script src="/bootstrap/js/bootstrap-modal.js"></script>
	    <script src="/bootstrap/js/bootstrap-dropdown.js"></script>
	    <script src="/bootstrap/js/bootstrap-scrollspy.js"></script>
	    <script src="/bootstrap/js/bootstrap-tab.js"></script>
	    <script src="/bootstrap/js/bootstrap-tooltip.js"></script>
	    <script src="/bootstrap/js/bootstrap-popover.js"></script>
	    <script src="/bootstrap/js/bootstrap-button.js"></script>
	    <script src="/bootstrap/js/bootstrap-collapse.js"></script>
	    <script src="/bootstrap/js/bootstrap-carousel.js"></script>
	    <script src="/bootstrap/js/bootstrap-typeahead.js"></script>
	</body>
</html>