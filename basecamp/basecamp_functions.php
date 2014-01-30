<?php

function basecamp_get($uri){
	$session = curl_init();
	$username = 'dbf5e62aa618e3a71da76d03999293681a7f2f89';
	$password = 'X';
	$basecamp_url = 'https://'.$username . ":" . $password.'@uamarketing.basecamphq.com';
	$url = $basecamp_url.$uri;
	
	curl_setopt($session, CURLOPT_URL, $url);
	curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($session, CURLOPT_HTTPGET, 1); 
	curl_setopt($session, CURLOPT_HEADER, false);
	curl_setopt($session, CURLOPT_HTTPHEADER, array('Accept: application/xml', 'Content-Type: application/xml'));
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	 
	$response = curl_exec($session);
	
	curl_close($session);
	
	return simplexml_load_string($response);
}

function basecamp_post($uri, $xml){
	   $request.="POST ".$uri." HTTP/1.1".chr(13).chr(10);
       $request.="Host: uamarketing.basecamphq.com".chr(13).chr(10);
       $request.="Content-type: application/xml".chr(13).chr(10);
	   $request.="Authorization: Basic c3VhZG1pbjpzdW1jNDQxNA==".chr(13).chr(10);
	   $request.="Content-length: ".strlen($xml).chr(13).chr(10);
       $request.="Connection: close".chr(13).chr(10);
       $request.=chr(13).chr(10);
       $request.=$xml;

       $fp = fsockopen('ssl://uamarketing.basecamphq.com',443);
       fputs($fp, $request);
       while(!feof($fp)) {
           $result .= fgets($fp, 128);
       }
       fclose($fp);
		$temp = explode(chr(13).chr(10).chr(13).chr(10), $result);
		$headers_temp = explode(chr(13).chr(10), $temp[0]);
		
		$headers['body'] = $temp[1];
		$headers['response'] = $headers_temp[0];
		for($x=1; $x<sizeof($headers_temp); $x++){
			$temp = explode(': ', $headers_temp[$x]);
			$headers[$temp[0]] = $temp[1];
		}
		
	   return $headers;
}

function createProject(){
		
}



//string createMessage(int $projID, string $message [, array $notifications])

//Returns a string containing a link to the new message.  The optional $notifications array contains the Basecamp IDs of anyone who should be notified when the message is created.  If $notifications array is not present, no //notifications should be sent.

function createMessage($projID, $title, $message , $categories = NULL, $milestone = NULL, $notifications = NULL){
	
	$requestBody = '<request>'.
  						'<post>'.
    						'<title>'.$title.'</title>'.
						    '<body>'.$message.'</body>'.
							($milestone?'<milestone-id type="integer">'.$milestone.'</milestone-id>':'').
							($categories?'<category-id type="integer">'.$categories.'</category-id>':'').							
  						'</post>';
	if(is_array($notifications)){
		foreach($notifications as $personID){
			$requestBody .= '<notify>'.$personID.'</notify>';
		}
	}
  	
	$requestBody .= '</request>';
	
	//var_dump($requestBody);
	//return new RestRequest('/projects/'.$projID.'/posts.xml', 'POST', $requestBody);
	
	$response = basecamp_post('/projects/'.$projID.'/posts.xml', $requestBody);
	
	return 'https://uamarketing.basecamphq.com/projects/'.$projID.'/posts/'.substr($response['Location'], 7, -4);
}

//string createMilestone(int $projID, string $title, date $dueDate)

//Returns a string containing a link to the new Milestone.

function  createMilestone($projID, $title, $dueDate){
	$requestBody = '<request>'.
						'<milestone>'.
							'<title>'.$title.'</title>'.
							'<deadline type="date">'.$dueDate.'</deadline>'.
						'</milestone>'.
					'</request>';


	
	$response = basecamp_post('/projects/'.$projID.'/milestones/create', $requestBody);
	
	//var_dump(substr($response['body'], strpos($response['body'], '<'), strrpos($response['body'], '>') - strpos($response['body'], '<')+1));
	$milestones = simplexml_load_string(substr($response['body'], strpos($response['body'], '<'), strrpos($response['body'], '>') - strpos($response['body'], '<')+1));
	$id = intval($milestones->milestone->id[0]);
	
	return 'https://uamarketing.basecamphq.com/projects/'.$projID.'/milestones/'.$id.'/comments';
}


//Returns the milestone Basecamp ID stripped from the $milestoneURL
function milestoneID_fromUrl($milestoneURL){
	$milestoneURL = str_replace('/comments', '', $milestoneURL);
	return substr($milestoneURL, strrpos($milestoneURL, '/')+1);
}





//string createToDoList(int $projID, string $title [, int $milestoneID [, array $toDoItems]])

//Returns a string containing a link to the new ToDo list.  Both $milestoneID and $toDoItems are optional.  If $milestoneID is passed in this ToDo list will be associated with Basecamp milestone that has that ID.  If $toDoItems is not passed in the ToDoList will be created with no ToDo Items in the list. All ToDoLists will have timetracking enabled. The $toDoItems array should have the following format: 
//$toDoItems[0]['item'] is a string containing the ToDo items text.
//$toDoItems[0]['assignee'] is the Basecamp ID for the person who the ToDo item is assigned to.
//$toDoItems[0]['dueDate'] is the date this ToDo item is due.

function createToDoList($projID, $title , $milestoneID = NULL, $toDoItems = NULL){
	
	$requestBody =	'<todo-list>'.
						($milestone?'<milestone-id type="integer">'.$milestone.'</milestone-id>':'').
						'<name>'.$title.'</name>'.
						'<private type="boolean">false</private>'.
						'<tracked type="boolean">true</tracked>'.
					'</todo-list>';


	
	$response = basecamp_post('/projects/'.$projID.'/todo_lists.xml', $requestBody);
	
	$toDoListID = substr($response['Location'], strrpos($response['Location'], '/')+1);
	
	
	if(is_array($toDoItems)){
		createToDoItems($toDoListID, $toDoItems);
	}
	
	return 'https://uamarketing.basecamphq.com/projects/'.$projID.'/todo_lists/'.$toDoListID;	
}

function createToDoItems($toDoListID, $toDoItems){
	foreach($toDoItems as $toDoItem){
		//var_dump($toDoItem);
		$requestBody =	'<todo-item>'.
							'<content>'.$toDoItem['item'].'</content>'.
							'<due-at type="datetime">'.$toDoItem['dueDate'].'</due-at>'.
							'<responsible-party>'.$toDoItem['assignee'].'</responsible-party>'.
							'<notify type="boolean">false</notify>'.
						'</todo-item>';
						var_dump($requestBody);
		$response = basecamp_post('/todo_lists/'.$toDoListID.'/todo_items.xml', $requestBody);	
		var_dump($response);
	
	}
}

function getProjects(){
	$response = basecamp_get('/projects.xml');
	
	//var_dump($response);
	
	$projects = array();
	foreach($response->project as $project){
		
		$projects[intval($project->id)] = $project->name.'';	
	}
	
	return $projects;
}

function getActiveProjectsByCompany(){
	$response = basecamp_get('/projects.xml');
	
	//var_dump($response);
	
	$projects = array();
	foreach($response->project as $project){
		if($project->status.'' == active)
		$projects[$project->company->name.''][intval($project->id)] = $project->name.'';	
	}
	
	return $projects;
}

function getToDoLists($projID){
	$response = basecamp_get('/projects/'.$projID.'/todo_lists.xml');
	$todolists = array();
	foreach($response->{'todo-list'} as $list){
		$todolists[intval($list->{'id'})] = $list->{'name'}.'';
	}
	return $todolists;
}
//array getProjectPeople(int $projID)

//Returns and array of People assigned to the project defined by $projID.  The resulting array should have the following format:
//array['id'] = "Persons Name", where the key 'id' is the Basecamp ID for that person.
function getProjectPeople($projID){
	
	$response = basecamp_get('/projects/'.$projID.'/people.xml');
	
	$people = array();
	foreach($response->person as $person){
		
		$people[intval($person->{'person-id'}[0])] = $person->{'first-name'}.' '.$person->{'last-name'};
	}
	
	return $people;
}


//array getMarketingPeople()

//Returns and array of the Student Affairs Markenting team (currently Project Managers in Basecamp).  The resulting array should have the following format:
//array['id'] = "Persons Name", where the key 'id' is the Basecamp ID for that person.
function getMarketingPeople(){
	$response = basecamp_get('/companies/1073432/people.xml');
	
	
	$people = array();
	foreach($response->person as $person){
		//var_dump($person);
		$people[intval($person->{'id'}[0])] = $person->{'first-name'}.' '.$person->{'last-name'};
	}
	
	return $people;
}

//function string createWriteboard(int $projID, string $title [, string $contents])

//Returns a string containing a link to the new writeboard.  $contents is optional and defaults to an empty string, so if you don't pass it in you will get an empty writeboard.
function createWriteboard($projID, $title, $contents =''){
	$username = 'jmasson';
	$password = '01857085';
  
  	$session = curl_init();
	$url = 'https://uamarketing.basecamphq.com/login';
	
	curl_setopt($session, CURLOPT_URL, $url);
	curl_setopt($session, CURLOPT_HTTPGET, 1); 
	curl_setopt($session, CURLOPT_HEADER, false);
	//curl_setopt($session, CURLOPT_HTTPHEADER, array('Accept: application/xml', 'Content-Type: application/xml'));
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($session, CURLOPT_COOKIEFILE, '/Library/WebServer/commontools/basecamp/cookies.tmp');
	curl_setopt($session, CURLOPT_COOKIEJAR, '/Library/WebServer/commontools/basecamp/cookies.tmp');
	$response = curl_exec($session);
	//var_dump($response);
	$response = substr($response, strpos($response, '<input name="authenticity_token" type="hidden" value="')+53);
	$response = substr($response, 0, strpos($response, '"'));
	//var_dump('token:'.$response);
	curl_close($session);
	
	$session = curl_init();
	$url = 'https://launchpad.37signals.com/authenticate';
	$login_array = array('authenticity_token'=> $response, 'product' => 'basecamp', 'subdomain'=>'uamarketing' , 'username'=>$username, 'password'=> $password, 'commit'=>'Sign in', 'openid_identifier'=>'');
	curl_setopt($session, CURLOPT_URL, $url);
	curl_setopt($session, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($session, CURLOPT_POST, true);
	curl_setopt($session, CURLOPT_POSTFIELDS, $login_array); 
	curl_setopt($session, CURLOPT_HEADER, false);
	//curl_setopt($session, CURLOPT_HTTPHEADER, array('Accept: application/xml', 'Content-Type: application/xml'));
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($session, CURLOPT_COOKIEFILE, '/Library/WebServer/commontools/basecamp/cookies.tmp');
	curl_setopt($session, CURLOPT_COOKIEJAR, '/Library/WebServer/commontools/basecamp/cookies.tmp');
	$response = curl_exec($session);
	//var_dump($response);
	curl_close($session);
  
  
  	$session = curl_init();
	$url = 'https://uamarketing.basecamphq.com/projects/'.$projID.'/writeboards';
	
	curl_setopt($session, CURLOPT_URL, $url);
	curl_setopt($session, CURLOPT_HTTPGET, 1); 
	curl_setopt($session, CURLOPT_HEADER, false);
	//curl_setopt($session, CURLOPT_HTTPHEADER, array('Accept: application/xml', 'Content-Type: application/xml'));
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($session, CURLOPT_COOKIEFILE, '/Library/WebServer/commontools/basecamp/cookies.tmp');
	curl_setopt($session, CURLOPT_COOKIEJAR, '/Library/WebServer/commontools/basecamp/cookies.tmp');
	$response = curl_exec($session);
	//var_dump($response);
	$response = substr($response, strpos($response, '<input name="authenticity_token" type="hidden" value="')+53);
	$response = substr($response, 0, strpos($response, '"'));
	//var_dump('token:'.$response);
	curl_close($session);
	$token = $response;
	
	$session = curl_init();
	$url = 'https://uamarketing.basecamphq.com/projects/'.$projID.'/writeboards/new';
	$writeboard_array = array('authenticity_token'=> $token, 'writeboard_title' => $title, 'commit' => 'Create a new writeboard');
	curl_setopt($session, CURLOPT_URL, $url);
	curl_setopt($session, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($session, CURLOPT_POST, true);
	curl_setopt($session, CURLOPT_POSTFIELDS, $writeboard_array); 
	curl_setopt($session, CURLOPT_HEADER, true);
	//curl_setopt($session, CURLOPT_HTTPHEADER, array('Accept: application/xml', 'Content-Type: application/xml'));
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($session, CURLOPT_COOKIEFILE, '/Library/WebServer/commontools/basecamp/cookies.tmp');
	curl_setopt($session, CURLOPT_COOKIEJAR, '/Library/WebServer/commontools/basecamp/cookies.tmp');
	$response = curl_exec($session);
	//var_dump($response);
	$password = substr($response, strpos($response, 'X-Login-Password: ')+18);
	$password = substr($password, 0, strpos($password, chr(13).chr(10)));
	//var_dump('password:'.$password);
	$location = substr($response, strpos($response, 'X-Login-Url:')+13);
	$location = substr($location, 0, strpos($location, chr(13).chr(10)));
	//var_dump('location:'.$location);
	
	curl_close($session);
  
   	$session = curl_init();
	$url = $location;
	$writeboard_array = array('authenticity_token'=> $token, 'writeboard_title' => $title, 'commit' => 'Create a new writeboard', 'password' => $password);
	curl_setopt($session, CURLOPT_URL, $url);
	curl_setopt($session, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($session, CURLOPT_POST, true);
	curl_setopt($session, CURLOPT_POSTFIELDS, $writeboard_array); 
	curl_setopt($session, CURLOPT_HEADER, true);
	curl_setopt($session, CURLOPT_HTTPHEADER, array('Accept: application/xml', 'Content-Type: application/xml'));
	curl_setopt($session, CURLOPT_HTTPHEADER, array('X-Login-Password: '.$password, 'X-Login-Url: '.$location));
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($session, CURLOPT_COOKIEFILE, '/Library/WebServer/commontools/basecamp/cookies.tmp');
	curl_setopt($session, CURLOPT_COOKIEJAR, '/Library/WebServer/commontools/basecamp/cookies.tmp');
	$response = curl_exec($session);
	//var_dump($response);
	
	
	
	curl_close($session);
	
	
	
	$session = curl_init();
	$url = str_replace('login', 'v/create', $location);
	//var_dump($url);
	$writeboard_array = array('version[title]'=> $title, 'version[body]' => $contents, 'commit' => 'Save this writeboard', 'version[author_name]' => 'admin');
	curl_setopt($session, CURLOPT_URL, $url);
	curl_setopt($session, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($session, CURLOPT_POST, true);
	curl_setopt($session, CURLOPT_POSTFIELDS, $writeboard_array); 
	curl_setopt($session, CURLOPT_HEADER, true);
	//curl_setopt($session, CURLOPT_HTTPHEADER, array('Accept: application/xml', 'Content-Type: application/xml'));
	//curl_setopt($session, CURLOPT_HTTPHEADER, array('X-Login-Password: '.$password, 'X-Login-Url: '.$location));
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($session, CURLOPT_COOKIEFILE, '/Library/WebServer/commontools/basecamp/cookies.tmp');
	curl_setopt($session, CURLOPT_COOKIEJAR, '/Library/WebServer/commontools/basecamp/cookies.tmp');
	$response = curl_exec($session);
	//var_dump($response);
	
	
	
	curl_close($session);
	
	return str_replace('login', '', $location);
}

//TEST FUNCTIONS


//$temp = createMessage(4456386, 'test', 'this is a test', 45047281, 13757378, array(2859196));
//print '<a href="'.$temp.'">here</a>';

//$temp = createMilestone(4456386, 'test', '2010-04-12');
//print '<a href="'.$temp.'">here</a>';
//print milestoneID_fromUrl($temp);

//$temp = createToDoList(4456386, 'test' , 13757378, array(array('item'=>'test', 'assignee'=>2859196, 'dueDate'=>'2010-04-12')));
//print '<a href="'.$temp.'">here</a>';

//$temp = getProjectPeople(4456386);
//print_r($temp);

//$temp = getMarketingPeople();
//print_r($temp);

//$temp = createWriteboard(4456386, 'writeboard title', 'this is the content');
//print '<a href="'.$temp.'">here</a>';