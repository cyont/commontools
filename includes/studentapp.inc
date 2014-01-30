<?php
require_once('includes/mysqli.inc');
require_once('includes/career_services.inc');
$db = new db_mysqli('student_hiring');

class applying_for{
	public $type;
	public $application_id;
	public $position_ids = array();
	public $unit_ids = array();
	public $area_ids = array();
	public $record_ids = array();
	function __construct($application_id){
		global $db;
		$this->application_id = $application_id;
		$query = 'select * from applying_for where application_id='.$this->application_id;
		$result = $db->query($query);
		while($apply_for = $result->fetch_assoc()){
			switch($apply_for['type']){
				case 'any':
					$this->type = 'any';
					$this->position_ids = array();
					$this->unit_ids = array();
					$this->area_ids = array();
					$this->record_ids = array($apply_for['apply_for_id']);
				break;
				
				case 'area':
					$this->type = 'area';
					$this->position_ids = array();
					$this->unit_ids = array();
					$this->area_ids[] = $apply_for['area_id'];
					$this->record_ids[] = $apply_for['apply_for_id'];
				break;
				
				case 'unit':
					$this->type = 'position';
					$this->unit_ids[] = $apply_for['unit_id'];
					$this->area_ids = array();
					$this->record_ids[] = $apply_for['apply_for_id'];
				break;
				
				case 'position':
					$this->type = 'position';
					$this->position_ids[$apply_for['position_id']]['title'] = $apply_for['title'];
					$this->position_ids[$apply_for['position_id']]['unit_id'] = $apply_for['unit_id'];
					$this->area_ids = array();
					$this->record_ids[] = $apply_for['apply_for_id'];
				break;
			}
		}
	}

	function clear(){
		$this->position_ids = array();
		$this->unit_ids = array();
		$this->area_ids = array();
		$this->record_ids = array();
	}
	
	function set_any(){
		$this->type = 'any';
	}

	function add_area($area_id){
		$this->type = 'area';
		$this->area_ids[] = $area_id;
	}
	
	function add_unit($unit_id){
		$this->type = 'unit';
		$this->unit_ids[] = $unit_id;
	}

	function add_position($position_id, $title, $unit_id){
		$this->type = 'position';
		$this->position_ids[$position_id]['title'] = $title;
		$this->position_ids[$position_id]['unit_id'] = $unit_id;
	}
	
	function save(){
		global $db;
		$db->query('delete from applying_for where application_id='.$this->application_id);
		switch($this->type){
			case 'any':
				$db->query('insert into applying_for set type="any", application_id='.$this->application_id);
			break;
			case 'area':
				foreach($this->area_ids as $area_id){
					$db->query('insert into applying_for set type="area", area_id='.intval($area_id).', application_id='.$this->application_id);
				}
			break;
			case 'position':
				foreach($this->position_ids as $position_id => $info){
					$query = 'insert into applying_for set type="position", unit_id='.$info['unit_id'].', position_id='.intval($position_id).', title="'.$db->escape($info['title'], 75).'", application_id='.$this->application_id;
					$db->query($query);
				}
			break; 
			case 'unit':
				foreach($this->unit_ids as $unit_id){
					$db->query('insert into applying_for set type="unit", unit_id='.intval($unit_id).', application_id='.$this->application_id);
				}
			break;
		}
	}
}
class resume{
	public $has_resume;
	public $resume_id;
	public $name;
	
	function __construct($application_id){
		global $db;
		$query = 'select resume_id, name from resumes where application_id='.$application_id;
		$result = $db->query($query);
		if($result->num_rows == 0){
			$this->has_resume = 0;
		}
		else{
			$resume = $result->fetch_assoc();
			$this->has_resume = 1;
			$this->resume_id = $resume['resume_id'];
			$this->name = $resume['name'];
		}
	}
	
	function save($application_id, $name, $data, $type, $size){
		global $db;
		$this->has_resume = 1;
		$this->name = $name;
		$query = 'delete from resumes where application_id='.$application_id;
		$db->query($query);
		$query = 'insert into resumes set'.
					'   application_id="'.$application_id.
					'", name="'.$name.
					'", resume="'.$db->escape($data).
					'", type="'.$db->escape($type, 100).
					'", size="'.intval($size).
					'"';
		$db->query($query);
	}
}
class schedule{
	public $schedule_id;
	public $semester_id;
	public $hours_week;
	public $mon;
	public $tue;
	public $wed;
	public $thu;
	public $fri;
	public $sat;
	public $sun;
	
	function __construct($schedule_id=0, $semester_id, $application_id, $hours_week=0, $mon=0, $tue=0, $wed=0, $thu=0, $fri=0, $sat=0, $sun=0){
		global $db;
		$this->schedule_id = $schedule_id;
		$this->semester_id = $semester_id;
		$this->application_id = $application_id;
		$this->hours_week = $hours_week;
		$this->mon = $mon;
		$this->tue = $tue;
		$this->wed = $wed;
		$this->thu = $thu;
		$this->fri = $fri;
		$this->sat = $sat;
		$this->sun = $sun;
	}
	function get_as_json(){
		$json[0] = str_split($this->mon);
		$json[1] = str_split($this->tue);
		$json[2] = str_split($this->wed);
		$json[3] = str_split($this->thu);
		$json[4] = str_split($this->fri);
		$json[5] = str_split($this->sat);
		$json[6] = str_split($this->sun);
		return $json;
	}
	function save(){
		global $db;
		$query = 'insert into schedules set'.
			'  application_id='.$this->application_id.
			', semester_id='.$this->semester_id.
			', hours_week='.intval($this->hours_week).
			', mon='.bindec(substr($this->mon, 0, 24)).
			', mon2='.bindec(substr($this->mon, 24)).
			', tue='.bindec(substr($this->tue, 0, 24)).
			', tue2='.bindec(substr($this->tue, 24)).
			', wed='.bindec(substr($this->wed, 0, 24)).
			', wed2='.bindec(substr($this->wed, 24)).
			', thu='.bindec(substr($this->thu, 0, 24)).
			', thu2='.bindec(substr($this->thu, 24)).
			', fri='.bindec(substr($this->fri, 0, 24)).
			', fri2='.bindec(substr($this->fri, 24)).
			', sat='.bindec(substr($this->sat, 0, 24)).
			', sat2='.bindec(substr($this->sat, 24)).
			', sun='.bindec(substr($this->sun, 0, 24)).
			', sun2='.bindec(substr($this->sun, 24)).
			'';
		$db->query($query);
	}
}
class schedules{
	public function get_schedules($application_id){
		global $db;
		$schedules = array();
		$query = 'select * from schedules where application_id='.$application_id;
		$result = $db->query($query);
		while($schedule = $result->fetch_assoc()){
			$schedules[$schedule['semester_id']] = new schedule($schedule['schedule_id'], $schedule['semester_id'], $application_id, $schedule['hours_week'], str_pad(decbin($schedule['mon']), 24, '0', STR_PAD_LEFT).str_pad(decbin($schedule['mon2']), 24, '0', STR_PAD_LEFT), str_pad(decbin($schedule['tue']), 24, '0', STR_PAD_LEFT).str_pad(decbin($schedule['tue2']), 24, '0', STR_PAD_LEFT), str_pad(decbin($schedule['wed']), 24, '0', STR_PAD_LEFT).str_pad(decbin($schedule['wed2']), 24, '0', STR_PAD_LEFT), str_pad(decbin($schedule['thu']), 24, '0', STR_PAD_LEFT).str_pad(decbin($schedule['thu2']), 24, '0', STR_PAD_LEFT), str_pad(decbin($schedule['fri']), 24, '0', STR_PAD_LEFT).str_pad(decbin($schedule['fri2']), 24, '0', STR_PAD_LEFT), str_pad(decbin($schedule['sat']), 24, '0', STR_PAD_LEFT).str_pad(decbin($schedule['sat2']), 24, '0', STR_PAD_LEFT), str_pad(decbin($schedule['sun']), 24, '0', STR_PAD_LEFT).str_pad(decbin($schedule['sun2']), 24, '0', STR_PAD_LEFT));
		}
		return $schedules;
	}
	
}
class work_history{
	public $work_history_id;
	public $application_id;
	public $company;
	public $address;
	public $city_state_zip;
	public $phone_number;
	public $supervisor_name;
	public $job_duties;
	public $fromm;
	public $fromy;
	public $tom;
	public $toy;
	public $pay_rate;
	public $reason_leave;
	
	function __construct($application_id, $work_history_id=0, $company, $address, $city, $phone, $supervisor, $duties, $fromm, $fromy, $tom, $toy, $pay, $leave){
		$this->application_id = $application_id;
		$this->work_history_id = $work_history_id;
		$this->company = $company;
		$this->address = $address;
		$this->city_state_zip = $city;
		$this->phone_number = $phone;
		$this->supervisor_name = $supervisor;
		$this->job_duties = $duties;
		$this->fromm = $fromm;
		$this->fromy = $fromy;
		$this->tom = $tom;
		$this->toy = $toy;
		$this->pay_rate = $pay;
		$this->reason_leave = $leave;
	}
	
	function save(){
		global $db;
		$query = 'insert into work_history set'.
					'   application_id='.$this->application_id.
					',  company="'.$db->escape($this->company, 50).
					'", address="'.$db->escape($this->address, 50).
					'", city_state_zip="'.$db->escape($this->city_state_zip, 50).
					'", phone_number="'.$db->escape($this->phone_number, 16).
					'", supervisor_name="'.$db->escape($this->supervisor_name, 30).
					'", job_duties="'.$db->escape($this->job_duties, 500).
					'", fromm="'.intval($this->fromm).
					'", fromy="'.intval($this->fromy).
					'", tom="'.intval($this->tom).
					'", toy="'.intval($this->toy).
					'", pay_rate="'.$db->escape($this->pay_rate, 20).
					'", reason_leave="'.$db->escape($this->reason_leave, 200).
					'"';
		$db->query($query);
	}
}
class work_histories{
	public function get_work_histories($application_id){
		global $db;
		$work_histories = array();
		$query = 'select * from work_history where application_id='.$application_id;
		$result = $db->query($query);
		while($work_history = $result->fetch_assoc()){
			$work_histories[$work_history['work_history_id']] = new work_history($application_id, $work_history['work_history_id'], $work_history['company'], $work_history['address'], $work_history['city_state_zip'], $work_history['phone_number'], $work_history['supervisor_name'], $work_history['job_duties'], $work_history['fromm'], $work_history['fromy'], $work_history['tom'], $work_history['toy'], $work_history['pay_rate'], $work_history['reason_leave']); 
		}
		return $work_histories;
	}
	
}
class student_app{
	public $application_id;
	public $application_values;
	public $applying_for;
	public $resume;
	public $schedule;
	public $work_histories;
	public $parent;
	
	function __construct($email = NULL, $email = NULL, $netID = NULL, $parent){
		global $db;
		$this->parent = $parent;
		if(empty($email)){
			
    		$query = "select * from applications where app_id=".$this->parent->app_id." and netID='".$db->escape($netID, 25)."'";
			$this->netid = $netID; 
  		}
  		else{
  			
			$query = 'select * from applications where app_id='.$this->parent->app_id.' and email="'.$db->escape($email, 100).'" and password="'.$db->escape($password, 25).'"';
			$this->email = $email;
			$this->password = $password;
  		}
	
		$result = $db->query($query);
		if($result->num_rows == 0){
			
			$query = 'insert into applications set'.
						'   netID="'.$db->escape($this->netid, 25).
						'", email="'.$db->escape($this->email, 100).
						'", password="'.$db->escape($this->password, 25).
						'", emplId="'.$db->escape($_SESSION['webauth']['emplid'], 8).
						'", app_id='.$this->parent->app_id;
			$db->query($query);
			$this->application_id = $db->insert_id;
		}
		else{
			
			$app = $result->fetch_assoc();
			$this->application_id = $app['application_id'];
		}
		
		$query = 'select * from applications where application_id='.$this->application_id;
		$result = $db->query($query);
		$this->application_values = $result->fetch_assoc();
		$this->applying_for = new applying_for($this->application_id);
		$this->resume = new resume($this->application_id);
		$this->schedules = schedules::get_schedules($this->application_id);
		$this->work_histories = work_histories::get_work_histories($this->application_id);
	}

	function save(){
		global $db;
		$query = 'update applications set'.
					'   firstName="'.		$db->escape($this->application_values['firstName'], 50).
					'", lastName="'.		$db->escape($this->application_values['lastName'], 50).
					'", studentType="'.		$db->escape($this->application_values['studentType'], 20).
					'", classStanding="'.	$db->escape($this->application_values['classStanding'], 20).
					'", address="'.			$db->escape($this->application_values['address'], 50).
					'", email="'.			$db->escape($this->application_values['email'], 50).
					'", phoneNumber="'.		$db->escape($this->application_values['phoneNumber'], 20).
					'", workStudy="'.		$db->escape(intval($this->application_values['workStudy'])).
					'", previouslyWorked="'.$db->escape(intval($this->application_values['previouslyWorked'])).
					'", referredBy="'.		$db->escape($this->application_values['referredBy'], 50).
					'", hearAbout="'.		$db->escape($this->application_values['hearAbout'], 50).
					'", other="'.			$db->escape($this->application_values['other'], 50).
					'", major="'.			$db->escape($this->application_values['major'], 50).
					'", crime="'.			$db->escape(intval($this->application_values['crime'])).
					'", crimeInfo="'.		$db->escape($this->application_values['crimeInfo'], 255).
					'", stage="'.			$db->escape($this->application_values['stage']).
					'", city="'.			$db->escape($this->application_values['city'], 50).
					'", state="'.			$db->escape($this->application_values['state'], 2).
					'", zip="'.				$db->escape($this->application_values['zip'], 10).
					'", comments="'.		$db->escape($this->application_values['comments'], 500).
					'", interest="'.		$db->escape($this->application_values['interest'], 500).
					'", custService="'.		$db->escape($this->application_values['custService'], 500).
					'", skills="'.		    $db->escape($this->application_values['skills'], 500).
					'", cpr="'.				$db->escape(intval($this->application_values['cpr'])).
					'", cprExp="'.			$db->escape($this->application_values['cprExp'], 12).
					'", graduationDate="'.	$db->escape($this->application_values['graduationDate'], 12).
					'", dateAvailable="'.	$db->escape($this->application_values['dateAvailable'], 12).
					'", stage="'.			$db->escape(intval($this->application_values['stage'])).
					'", date_updated="'. $db->escape(date('Y-m-d H:i:s')).
					'" where application_id='.$this->application_id;
		$db->query($query);
		$this->applying_for->save();
		$query = 'delete from work_history where application_id='.$this->application_id;
		$db->query($query);
		foreach($this->work_histories as $work)
			$work->save();
		$query = 'delete from schedules where application_id='.$this->application_id;
		$db->query($query);
		foreach($this->schedules as $schedule)
			$schedule->save();
		
	}
	
	function activate(){
		global $db;
		$query = 'update applications set active=1 where application_id='.$this->application_id;
		$db->query($query);
		$this->application_values['active'] = 1;
	}
	
	function deactivate(){
		global $db;
		$query = 'update applications set active=-1 where application_id='.$this->application_id;
		$db->query($query);
		$this->application_values['active'] = -1;
	}
}

class student_app_proccess{
	public $app_id;
	public $app_name;
	public $splash_page;
	public $career_services_report_id;
	public $specificpos;
	public $application;
	public $fields;
	public $errors;
	public $error_messages;
	public $stage;
	
	function __construct($app){
		global $db;
		$app = mysqli_fetch_assoc($db->query('select * from apps where app_name="'.$app.'"'));
		$this->app_id = $app['app_id'];
		$this->app_name = $app['app_name'];
		$this->splash_page = $app['splash_url'];
		$this->career_services_report_id = $app['career_services_report_id'];
	}
	
	function login(){
		if(isset($_GET['specificpos'])){
			$this->specificpos = $_GET['specificpos'];
		}

		if($_POST['email'] == NULL){
			$webauth_splash = $this->splash_page;
			require_once('webauth/include.php');
  		}
  		
		$this->application = new student_app($_POST['email'], $_POST['password'], $_SESSION['webauth']['netID'], $this);
		
		if($this->specificpos){
			$positions = $this->get_positions();
			$this->application->applying_for->add_position($this->specificpos, $positions[$this->specificpos]['Job_JobTitle']);
		}
		
		if(!$this->application->application_values['active'] || $_GET['edit'] == 1 || $this->specificpos){
			if($this->application->application_values['stage'] == 0)
				header("Location:start.php");				
			elseif($this->application->application_values['stage'] < 5)
				header("Location:".($this->application->application_values['stage']+1).".php");
			else
				header("Location:1.php");
			exit();
		}
		else{
			header("Location:status.php");
			exit();
		}
			
	}
	
	function form_start($stage){
		$this->fields = array();
		$this->stage = $stage;
		print '<form id="stage'.$stage.'" name="stage'.$stage.'" method="POST" action="'.($stage+1).'.php" enctype="multipart/form-data">';
	}

	function text($name, $length, $cur_val=''){
		$this->fields[] = $name;
		print '<input type="text" id="'.$name.'" name="'.$name.'" class="'.$this->errors[$name].'" value="'.htmlentities($cur_val, ENT_QUOTES).'" maxlength="'.$length.'" />';
	}
	
	function long_text($name, $length, $cur_val=''){
		$this->fields[] = $name;
		print '<input type="text" name="'.$name.'" class="'.$this->errors[$name].'" value="'.htmlentities($cur_val, ENT_QUOTES).'" size="'.$length.'" maxlength="'.$length.'" />';
	}
	
	function date($name, $length, $cur_val=''){
		$this->fields[] = $name;
		print '<input type="text" id="'.$name.'" name="'.$name.'" class="datepicker '.$this->errors[$name].'" value="'.htmlentities($cur_val, ENT_QUOTES).'" maxlength="'.$length.'" />';
	}
	
	function dropdown($name, $values, $cur_val=''){
		$this->fields[] = $name;
		print '<select id="'.$name.'" name="'.$name.'" class="'.$this->errors[$name].'">';
			foreach($values as $key => $value)
				print '<option value="'.$key.'" '.(($cur_val==$key)?'selected':'').'>'.$value.'</option>';
		print '</select>';
	}
	
	function radio($name, $value, $cur_val=''){
		$this->fields[] = $name;
		print '<input type="radio" name="'.$name.'" class="'.$this->errors[$name].'" value="'.$value.'" '.($value==$cur_val?'checked':'').'/>';
	}
	
	function textArea($name, $cur_val=''){
		$this->fields[] = $name;
		print '<textarea id="'.$name.'" name="'.$name.'" class="'.$this->errors[$name].'">'.htmlentities($cur_val, ENT_QUOTES).'</textarea>';
	}
	
	function checkbox($name, $value, $cur_vals = array(),$attr=NULL){
		$this->fields[] = $name;
		print '<input type="checkbox" name="'.$name.'[]" class="'.$this->errors[$name].'" value="'.$value.'" '.(in_array($value, $cur_vals)?'checked':NULL)." ".$attr .'/>';
	}
	function hidden($name, $value, $array=0){
		$this->fields[] = $name;
		print '<input type="hidden" name="'.$name.($array?'[]':'').'"  value="'.htmlentities($value, ENT_QUOTES).'" />';
	}
	function file_upload($name){
		$this->fields[] = $name;
		print '<input type="file" id="'.$name.'" name="'.$name.'" />';
	}
	function submit($value = "submit"){
		print '<input type="submit" value="'.$value.'" />';
	}
	
	
	function form_finish($stage){
		$this->fields = array_unique($this->fields);
		print '<input type="hidden" name="stage" value="'.$stage.'" /><input type="hidden" name="fields" value="'.implode(',',$this->fields).'" /></form>';
	}

	function get_areas(){
		global $db;
		$query = 'select * from areas where app_id='.$this->app_id;
		$result = $db->query($query);
		while($area = $result->fetch_assoc())
			$areas[$area['area_id']] = $area['name'];
		return $areas;
	}
	
	function get_units(){
		global $db;
		$query = 'select * from units where app_id='.$this->app_id;
		$result = $db->query($query);
		while($unit = $result->fetch_assoc())
			$units[$unit['unit_id']] = array("title"=>$unit['name'],"accept"=>$unit['accepting_applications']);
		return $units;
	}
	
	function get_unit_jobs($unit){
		global $db;
		$query = 'select * from unit_jobs where unit_id='.$unit.' and app_id='.$this->app_id;
		$result = $db->query($query);
		while($unit_job = $result->fetch_assoc())
			$unit_jobs[$unit_job['unit_job_id']] = $unit_job['description'];
		return $unit_jobs;
	}

	function get_positions(){
		$career = new career_services($this->career_services_report_id,$this->app_id);
		$career->get_saved_jobs($this->app_id);
		return $career->jobs;
	}
	
	function get_active_semester_ids(){
		global $db;
		$query = 'select * from semesters where app_id='.$this->app_id.' and start_hiring<="'.date("Y-m-d").'" and stop_hiring>="'.date("Y-m-d").'"';
		$result = $db->query($query);
		while($semester = $result->fetch_assoc()){
			$semesters[$semester["semester_id"]] = $semester["name"];
		}
		return $semesters;
	}
	
	function get_schedules_as_json($semesters){
		foreach($semesters as $id => $name){
			if($this->application->schedules[$id]){
				
				$schedules[][0] = $this->application->schedules[$id]->get_as_json();
			}
			else{
				$this->add_new_schedule($id);
				$schedules[][0] = $this->application->schedules[$id]->get_as_json();
			}
		}
		return json_encode($schedules, JSON_NUMERIC_CHECK);
	}

	function add_new_schedule($semester_id){
		$this->application->schedules[$semester_id] = new schedule(0, $semester_id, $this->application->application_id, str_pad('', 48, '0', STR_PAD_LEFT), str_pad('', 48, '0', STR_PAD_LEFT), str_pad('', 48, '0', STR_PAD_LEFT), str_pad('', 48, '0', STR_PAD_LEFT), str_pad('', 48, '0', STR_PAD_LEFT), str_pad('', 48, '0', STR_PAD_LEFT), str_pad('', 48, '0', STR_PAD_LEFT));
	}

	function validate(){
		$error = 0;
		$this->error_messages = '';
		$this->errors = array();
		$this->fields = explode(',',$_POST['fields']);
		foreach($this->fields as $field){
			switch($field){
				case 'firstName':
					if(empty($_POST[$field])){
						$error = 1;
						$this->errors[$field] = 'error';
						$this->error_messages .= 'Missing first name.<br />';
					}
					else{
						$this->application->application_values[$field] = $_POST[$field];
					}
				break;
				
				case 'lastName':
					if(empty($_POST[$field])){
						$error = 1;
						$this->errors[$field] = 'error';
						$this->error_messages .= 'Missing last name.<br />';
					}
					else{
						$this->application->application_values[$field] = $_POST[$field];
					}
				break;
				
				case 'phoneNumber':
					if(empty($_POST[$field])){
						$error = 1;
						$this->errors[$field] = 'error';
						$this->error_messages .= 'Missing phone number.<br />';
					}
					// else if(!is_int($_POST[$field])){
					// 	$error = 1;
					// 	$this->errors[$field] = 'error';
					// 	$this->error_messages .= 'Invalid phone number.<br />';
					// }
					else{
						$this->application->application_values[$field] = $_POST[$field];
					}
				break;
				
				case 'address':
					if(empty($_POST[$field])){
						$error = 1;
						$this->errors[$field] = 'error';
						$this->error_messages .= 'Missing address.<br />';
					}
					else{
						$this->application->application_values[$field] = $_POST[$field];
					}
				break;
				
				case 'city':
					if(empty($_POST[$field])){
						$error = 1;
						$this->errors[$field] = 'error';
						$this->error_messages .= 'Missing city.<br />';
					}
					else{
						$this->application->application_values[$field] = $_POST[$field];
					}
				break;
				
				case 'state':
					if(empty($_POST[$field])){
						$error = 1;
						$this->errors[$field] = 'error';
						$this->error_messages .= 'Missing state.<br />';
					}
					else{
						$this->application->application_values[$field] = $_POST[$field];
					}
				break;
				
				case 'zip':
					if(empty($_POST[$field])){
						$error = 1;
						$this->errors[$field] = 'error';
						$this->error_messages .= 'Missing or Invalid Zip Code.<br />';
					}
					else{
						$this->application->application_values[$field] = $_POST[$field];
					}
				break;
				
				case 'email':
					$email  = filter_var($_POST[$field], FILTER_SANITIZE_EMAIL);
					if(empty($email)){
						$error = 1;
						$this->errors[$field] = 'error';
						$this->error_messages .= 'Missing email address.<br />';
					}else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
						$error = 1;
						$this->errors[$field] = 'error';
						$this->error_messages .= 'Invalid email address.<br />';
					}else{

						$this->application->application_values[$field] = $email;
					}
				break;
				
				case 'dateAvailable':
					if($_POST[$field] == ''){
						$error = 1;
						$this->errors[$field] = 'error';
						$this->error_messages .= 'Please tell us when you are available to start work.<br />';
					}
					else{
						$this->application->application_values[$field] = $_POST[$field];
					}
				break;
				
				case 'classStanding':
					if(empty($_POST[$field])){
						$error = 1;
						$this->errors[$field] = 'error';
						$this->error_messages .= 'Please select your class standing.<br />';
					}
					else{
						$this->application->application_values[$field] = $_POST[$field];
					}
				break;
				
				case 'major':
					if(empty($_POST[$field])){
						$error = 1;
						$this->errors[$field] = 'error';
						$this->error_messages .= 'Missing major.<br />';
					}
					else{
						$this->application->application_values[$field] = $_POST[$field];
					}
				break;
				
				case 'graduationDate':
					if($_POST[$field] == ''){
						$error = 1;
						$this->errors[$field] = 'error';
						$this->error_messages .= 'Please tell us when you expect to graduate.<br />';
					}
					else{
						$this->application->application_values[$field] = $_POST[$field];
					}
				break;
				
				case 'referredBy':
					
					 	// optional
						$this->application->application_values[$field] = $_POST[$field];
					 
				break;
				
				case 'hearAbout':
					if(empty($_POST[$field])){
						$error = 1;
						$this->errors[$field] = 'error';
						$this->error_messages .= 'Please select how you heard about this job opening.<br />';
					}
					else{
						$this->application->application_values[$field] = $_POST[$field];
					}
				break;
				
				case 'other':
					if($_POST['hearAbout'] == "Other")
					{
						if(empty($_POST[$field])){
							$error = 1;
							$this->errors[$field] = 'error';
							$this->error_messages .= 'Please tell us how you heard about this job opening.<br />';
						}
						else{
							$this->application->application_values[$field] = $_POST[$field];
						}
					}
					else{
						$this->application->application_values[$field] = $_POST[$field];
					}
				break;
				
				case 'previouslyWorked':
					if($_POST[$field] == ''){
						$error = 1;
						$this->errors[$field] = 'error';
						$this->error_messages .= 'Please tell us if you have worked here before.<br />';
					}
					else{
						$this->application->application_values[$field] = $_POST[$field];
					}
				break;
				
				case 'cpr':
						//optional
						$this->application->application_values[$field] = $_POST[$field];
					 
				break;
				
				case 'cprExp':
					if($_POST['cpr'] == "1")
					{
						if(empty($_POST[$field])){
							$error = 1;
							$this->errors[$field] = 'error';
							$this->error_messages .= 'Please enter the expiration date of your CPR/AED PRO certification.<br />';
						}
						else {
							$this->application->application_values[$field] = $_POST[$field];
						}
					}
					else{
						$this->application->application_values[$field] = $_POST[$field];
					}
				break;
				
				case 'interest':
					if($_POST[$field] == ''){
						$error = 1;
						$this->errors[$field] = 'error';
						$this->error_messages .= 'Please tell us why you are interested in working for Campus Rec.<br />';
					}
					else{
						$this->application->application_values[$field] = $_POST[$field];
					}
				break;
				
				case 'custService':
					if($_POST[$field] == ''){
						$error = 1;
						$this->errors[$field] = 'error';
						$this->error_messages .= 'Please tell us what customer service means to you.<br />';
					}
					else{
						$this->application->application_values[$field] = $_POST[$field];
					}
				break;
				
				case 'skills':
					// optional
					$this->application->application_values[$field] = $_POST[$field];
				break;
				case 'redistribute':
					$this->application->applying_for->clear();
					switch($_POST['redistribute']){
						case 'any':
							$this->application->applying_for->set_any();
						break;
						
						case 'area':
							foreach($_POST['area'] as $area_id)
								$this->application->applying_for->add_area($area_id);
						break;
						
						case 'position':
							$positions = $this->get_positions();
							if(sizeof($_POST['unit']) > 0)
							{
								foreach($_POST['unit'] as $unit_id)
									$this->application->applying_for->add_unit($unit_id);
							}
							if(sizeof($positions) > 0)
							{
								foreach($_POST['position'] as $position_id){
									$this->application->applying_for->add_position($position_id, $positions[$position_id]['Job_JobTitle'], $positions[$position_id]['unit_id']);
								}
							}
						break;

						case 'unit':
							foreach($_POST['unit'] as $unit_id)
								$this->application->applying_for->add_unit($unit_id);
						break;
					}
				break;
				
				case 'semester_id':
					foreach($_POST['semester_id'] as $semester_id){
						$this->application->schedules[$semester_id]->hours_week = $_POST['hours_week'.$semester_id];
						$this->application->schedules[$semester_id]->mon = str_replace(',', '', $_POST['schedule']['schedule_'.$semester_id][0]);
						$this->application->schedules[$semester_id]->tue = str_replace(',', '', $_POST['schedule']['schedule_'.$semester_id][1]);
						$this->application->schedules[$semester_id]->wed = str_replace(',', '', $_POST['schedule']['schedule_'.$semester_id][2]);
						$this->application->schedules[$semester_id]->thu = str_replace(',', '', $_POST['schedule']['schedule_'.$semester_id][3]);
						$this->application->schedules[$semester_id]->fri = str_replace(',', '', $_POST['schedule']['schedule_'.$semester_id][4]);
						$this->application->schedules[$semester_id]->sat = str_replace(',', '', $_POST['schedule']['schedule_'.$semester_id][5]);
						$this->application->schedules[$semester_id]->sun = str_replace(',', '', $_POST['schedule']['schedule_'.$semester_id][6]);
					}
				break;
				
				case 'company':
					if(is_array($_POST['company'])){
						$this->application->work_histories = array();
						for($x=1; $x<=sizeof($_POST['company']); $x++){
							$this->application->work_histories[] = new work_history($this->application->application_id, 0, $_POST['company'][$x], $_POST['com_address'][$x], $_POST['com_address2'][$x], $_POST['com_phone'][$x], $_POST['com_supervisor'][$x], $_POST['com_duties'][$x], $_POST['com_from_month'][$x], $_POST['com_from_year'][$x], $_POST['com_to_month'][$x], $_POST['com_to_year'][$x], $_POST['com_pay'][$x], $_POST['com_leave'][$x]);
						}
					}
				break;
				
				case 'resume':
					if($_FILES['resume']['size'] > 0){
						$data = file_get_contents($_FILES['resume']['tmp_name']);
						$this->application->resume->save($this->application->application_id, $_FILES['resume']['name'], $data, $_FILES['resume']['type'], $_FILES['resume']['size']);
					}
				break;
				
				case 'comments':
					$this->application->application_values[$field] = $_POST[$field];
				break; 

				case 'hours_week5':
					if($_POST[$field] == 0){
						$error = 1;
						$this->errors[$field] = 'error';
						$this->error_messages .= 'Please tell us how many hours you can work per week.<br />';
					}else{
						$this->application->application_values[$field] = $_POST[$field];
					}
				 
			}
		}
		if($error){
			header("Location:".$_POST['stage'].".php");
			exit();
		} 
		$this->stage++;
		$this->application->application_values['stage'] = max($this->stage-1, $this->application->application_values['stage']);
	}

	function save(){
		$this->application->save();
	}
	
	function finish(){
		$this->application->activate();
	}
}
