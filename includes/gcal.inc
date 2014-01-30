<?php
class gcal{
	public $service;
	public $calendars;
	function __construct($calendars = array(), $user = 'azstudentunion@gmail.com', $pass = 'sumc4414'){
		// Seem to be standard Zend and class includes
		date_default_timezone_set('America/Phoenix');
		require_once 'Zend/Loader.php';
		Zend_Loader::loadClass('Zend_Gdata');
		Zend_Loader::loadClass('Zend_Gdata_AuthSub');
		Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
		Zend_Loader::loadClass('Zend_Gdata_Calendar');
		
		// Parameters for ClientAuth authentication
		$service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
		$user = $user;
		$pass = $pass;
		
		//$user = 'azwellutest@gmail.com';
		//$pass = 'gcalstart86';
		
		// Create an authenticated HTTP client
		$client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
		
		// Create an instance of the Calendar service
		$this->service = new Zend_Gdata_Calendar($client);
		$this->calendars = $calendars;
	}

	function getEvents($start, $end){
		$events = array();
		foreach($this->calendars as $name => $cal_id){
		
			$query = 'https://www.google.com/calendar/feeds'.
						'/'.$cal_id.
						'/private'.
						'/full'.
						'?'.
						//'start-min='.date("Y-m-d").'T00:00:00'.
						//'&start-max='.date("Y-m-d", time()+86400).'T00:00:00';
						'start-min='.$start.
						'&start-max='.$end;
						//var_dump($query);
		
			// Retrieve the event list from the calendar server
			
			try {
			    $eventFeed = $this->service->getCalendarEventFeed($query);
			} catch (Zend_Gdata_App_Exception $e) {
			    echo "Error: " . $e->getMessage();
			}
			//print sizeof($eventFeed);
			
			foreach ($eventFeed as $event) {
				if ($event->eventStatus->value!="http://schemas.google.com/g/2005#event.canceled") {
					
						$where =$event->getWhere();
						$where = $where[0]->getValueString();
						$when = $event->getWhen();
						$date_start = strtotime($when[0]->getStartTime());
						$date_end = strtotime($when[0]->getEndTime());
						$title = $event->title;
						$desc = $event->getContent();
						$events[] = array('title' => $title.'', 'location' => $where, 'start'=>$date_start, 'end'=>$date_end, 'description' => $desc.'');
				}
			}
		}
		return $events;
	}

	function getCalendarList(){
		try {
    		$listFeed= $this->service->getCalendarListFeed();
		} catch (Zend_Gdata_App_Exception $e) {
    		echo "Error: " . $e->getMessage();
		}
		
		return $listFeed;
	}
}
