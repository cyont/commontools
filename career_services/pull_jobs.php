<?php
class career_services{
	public $jobs;
	
	function __construct($report_id){
		$param = array('intReportID'=>$report_id);
	
		$client = new SoapClient("https://www.career.arizona.edu/apps/WS/CS_Webservices.asmx?WSDL", array('trace' => TRUE, 'soap_version'   => SOAP_1_2));
	
		$listing = $client->CS_Get_JobListings($param);
	
		$xmlparse = simplexml_load_string($listing->CS_Get_JobListingsResult->any);
	
		$i = 0;
	
		if($xmlparse->ReportData['Job_JobTitle']){
			$job = $xmlparse->ReportData;
			$job = get_object_vars($job);
			$this->jobs[$job['Job_ID']] = $job;
		}
		else{
		
			foreach($xmlparse->ReportData as $job) {
				$job = get_object_vars($job);
				$this->jobs[$job['Job_ID']] = $job;
			}
		}
		
		foreach($this->jobs as $id => $job){
			$this->jobs[$id]['Job_Description'] = str_replace("\n", '<br />', str_replace('&#226;&#128;&#153;', '\'', str_replace('&#226;&#128;&#162;&#9;', '<b>&middot;</b>&nbsp;', $this->jobs[$id]['Job_Description'])));
			$this->jobs[$id]['Job_Qualifications'] = str_replace("\n", '<br />', str_replace('&#226;&#128;&#153;', '\'', str_replace('&#226;&#128;&#162;&#9;', '<b>&middot;</b>&nbsp;', $this->jobs[$id]['Job_Qualifications']))); 
		}
	}
}

require_once('includes/mysqli.inc');
$db = new db_mysqli('student_hiring');
$query ='select unit_id, name from units';
$result = $db->query($query);
while($unit = $result->fetch_assoc())
	$units[$unit['name']] = $unit['unit_id']; 
	
$jobs = new career_services(952);

foreach($jobs->jobs as $job){
	$unit_id = $units[$job['Employer_Division']];
	if(!$unit_id)
		$unit_id=0;
	$query = 'insert into joblink set'.
				'   joblink_id='.intval($job["Job_ID"]).
				',  title="'.$db->escape($job['Job_JobTitle'], 255).
				'", description="'.$db->escape($job['Job_Description']).
				'", employer="'.$db->escape($job['Job_Employer']).
				'", contact="'.$db->escape($job['Job_Contact'], 50).
				'", pay="'.$db->escape($job['Job_SalaryLevel'], 20).
				'", contact_info="'.$db->escape($job['Job_ContactInformation']).
				'", start_date="'.$db->escape($job['Job_PostingDate']).
				'", end_date="'.$db->escape(date("Y-m-d", strtotime($job['Job_EndDate']))).
				'", qualifications="'.$db->escape($job['Job_Qualifications']).
				'", employer_division="'.$db->escape($job['Employer_Division']).
				'", unit_id="'.$unit_id.
				'", app_id="'."2".
				'"  on duplicate key update '.
				'   title="'.$db->escape($job['Job_JobTitle'], 255).
				'", description="'.$db->escape($job['Job_Description']).
				'", employer="'.$db->escape($job['Job_Employer']).
				'", contact="'.$db->escape($job['Job_Contact'], 50).
				'", pay="'.$db->escape($job['Job_SalaryLevel'], 20).
				'", contact_info="'.$db->escape($job['Job_ContactInformation']).
				'", start_date="'.$db->escape($job['Job_PostingDate']).
				'", end_date="'.$db->escape(date("Y-m-d", strtotime($job['Job_EndDate']))).
				'", qualifications="'.$db->escape($job['Job_Qualifications']).
				'", employer_division="'.$db->escape($job['Employer_Division']).
				'", unit_id="'.$unit_id.
				'", app_id="'."2".
				'"';
	$db->query($query);
}

$query = "delete from joblink where end_date<'".date("Y-m-d")."'";
$db->query($query);
