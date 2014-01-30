<?php
include('mysql_link.inc');
mysql_select_db('cardtaker');

switch($_POST['op']){
	
	case 'filter':
		$query = 'select *, payments.ID as pid from payments join applications on payments.appID=applications.ID where 1';
		if($_POST['app'] != 0)
			$query.= ' and payments.appID='.$_POST['app'];
		if($_POST['status'] != '')
			$query.= ' and payments.status="'.$_POST['status'].'"';
		if($_POST['within'] != '')
			$query.= ' and payments.time_stamp > SUBDATE(NOW(), INTERVAL '.$_POST['within'].')';
			
		$query .= ' order by name, billTo_lastName, billTo_firstName';
		
		//print $query;
		
		$result = mysql_query($query);
		while($payment = mysql_fetch_assoc($result))
			$payments[] = $payment;
	
		print 'results = '.json_encode($payments).';';
	exit();
	
	case 'search':
	
		$query = 'select *, payments.ID as pid from payments join applications on payments.appID=applications.ID where billTo_firstName like "'.$_POST['value'].'%"';
		$result = mysql_query($query);
		while($payment = mysql_fetch_assoc($result))
			$possibilities['First'][] = array($payment['pid'], $payment['billTo_lastName'].', '. str_ireplace($_POST['value'], '<b>'.$_POST['value'].'</b>', $payment['billTo_firstName']));
		
		
			
		$query = 'select *, payments.ID as pid from payments join applications on payments.appID=applications.ID where billTo_lastName like "'.$_POST['value'].'%"';
		$result = mysql_query($query);
		while($payment = mysql_fetch_assoc($result))
			$possibilities['Last'][] = array($payment['pid'], str_ireplace($_POST['value'], '<b>'.$_POST['value'].'</b>', $payment['billTo_lastName']).', '.$payment['billTo_firstName']);
			
			
			
		$query = 'select *, payments.ID as pid from payments join applications on payments.appID=applications.ID where billTo_email like "'.$_POST['value'].'%"';
		$result = mysql_query($query);
		while($payment = mysql_fetch_assoc($result))
			$possibilities['Email'][] = array($payment['pid'], str_ireplace($_POST['value'], '<b>'.$_POST['value'].'</b>', $payment['billTo_email']));
			
			
	
		print 'results = '.json_encode($possibilities).';';
		
	exit();
		
	case 'update_status':
	
		$query = 'update payments set status="'.$_POST['newstatus'].'" where ID in('.$_POST['value'].')';
		mysql_query($query);
			
			
		
		$query = 'select *, payments.ID as pid from payments join applications on payments.appID=applications.ID where 1';
		if($_POST['app'] != 0)
			$query.= ' and payments.appID='.$_POST['app'];
		if($_POST['status'] != '')
			$query.= ' and payments.status="'.$_POST['status'].'"';
		if($_POST['within'] != '')
			$query.= ' and payments.time_stamp > SUBDATE(NOW(), INTERVAL '.$_POST['within'].')';
			
		$query .= ' order by name, billTo_lastName, billTo_firstName';
		
		//print $query;
		
		$result = mysql_query($query);
		while($payment = mysql_fetch_assoc($result))
			$payments[] = $payment;
	
		print 'results = '.json_encode($payments).';';
	exit();
	
	
	case 'show_pid':
		$query = 'select *, payments.ID as pid from payments join applications on payments.appID=applications.ID where payments.ID='.$_POST['value'];
		$result = mysql_query($query);
		$payments[] = mysql_fetch_assoc($result);	
	
		print 'results = '.json_encode($payments).';';
		
	exit();
	
	
	
	
	
}?>