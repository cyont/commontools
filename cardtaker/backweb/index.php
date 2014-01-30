<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<style>
.highlight{
 background-color:#9CF;	
}

.hidden{
	display:none;
}
</style>

<script>
var app=0;
var status="";
var within="";
var results = '';

function sendRequest(op, data, handler){
		var xmlHttp;
		
try
  {
  // Firefox, Opera 8.0+, Safari
  xmlHttp=new XMLHttpRequest();
  }
catch (e)
  {
  // Internet Explorer
  try
    {
    xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    }
  catch (e)
    {
    try
      {
      xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
      }
    catch (e)
      {
      alert("Your browser does not support AJAX!");
      return false;
      }
    }
  }
  
  xmlHttp.onreadystatechange=function()
{
if(xmlHttp.readyState==4)
  {
 	handler(xmlHttp.responseText);
  }
}
switch(op){
	case 'filter':
		data = 'op=filter&app='+app+'&status='+status+'&within='+within;
	break;
	case 'search':
		data = 'op=search&value='+data;
	break;
	case 'update_status':
		data = 'op=update_status&newstatus='+data.pop()+'&value='+data+'&app='+app+'&status='+status+'&within='+within;
	break;
	case 'show_pid':
		data = 'op=show_pid&value='+data;
	break;
}
//alert(data);
xmlHttp.open("POST", 'index.ajax.php', true);
//Send the proper header information along with the request
xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
//xmlHttp.setRequestHeader("Content-length", data.length);
//xmlHttp.setRequestHeader("Connection", "close");
xmlHttp.send(data);
	
}


function changed_dropdowm(result_obj){
	var results_header = '<form name="results_form" id="results_form"><table cellspacing="0" id="results_table"><thead>		<tr>			<th></th>			<th>App</th>			<th>First Name</th>			<th>Last Name</th>			<th>Address</th>			<th>Phone #</th>			<th>email</th>			<th>Amount</th>			<th>Order #</th>			<th>Decision</th>			<th>Reason</th>			<th>status</th>			<th>time</th>		</tr>	</thead>	<tbody>';
	
	var results_footer = '</tbody></table></form>';

	var results_body = '';
	
	eval(result_obj);
	
	
	for(x in results){	
		results_body +='<tr onmouseover="this.className = \'highlight\'" onmouseout="this.className =\'\'">';
		results_body +='	<td><input type="checkbox" name="payments[]" value="'+results[x].pid+'"/></td>';
		results_body +='	<td>'+results[x].name+'</td>';
		results_body +='	<td>'+results[x].billTo_firstName+'</td>';
		results_body +='	<td>'+results[x].billTo_lastName+'</td>';
		results_body +='	<td>'+results[x].billTo_street1+'</td>';
		results_body +='	<td>'+results[x].billTo_phoneNumber+'</td>';
		results_body +='	<td>'+results[x].billTo_email+'</td>';
		results_body +='	<td>'+results[x].orderAmount+'</td>';
		results_body +='	<td>'+results[x].orderNumber+'</td>';
		results_body +='	<td>'+results[x].decision+'</td>';
		results_body +='	<td>'+results[x].reasonCode+'</td>';
		results_body +='	<td>'+results[x].status+'</td>';
		results_body +='	<td>'+results[x].time_stamp+'</td>';
		results_body +='</tr>';
	}
	
	document.getElementById('results').innerHTML = results_header+results_body+results_footer;
	
	
	
}

function show_possible_results(result_obj){
	eval(result_obj);
	//alert(result_obj);
	var html ='';
	
	if(results == null){
		document.getElementById('search_results').innerHTML = 'no results';
		
	}
	else{
		for(x in results){
			html += '<h4>'+x+'</h4>';	
			
			for(y in results[x]){
				html += '<span onclick="document.getElementById(\'search_results\').style.display=\'none\'; sendRequest(\'show_pid\','+results[x][y][0]+', changed_dropdowm)">'+results[x][y][1]+'</span><br>';	
			}
		}
	
	
		document.getElementById('search_results').innerHTML = html;
	}
	document.getElementById('search_results').style.display = 'block';
}

function search_payments(value, e){
	e = e || window.event;
	if(e.keyCode != 13 && value != '')
		sendRequest('search', value, show_possible_results)
	else
		document.getElementById('search_results').style.display = 'none';
}

function update_status(status){
	toBeUpdated = new Array();
	var form = document.getElementById('results_form');
	var length = form.elements.length
	for(x=0; x<length; x++){
		//alert(form.elements[x].checked)
		if(form.elements[x].checked)
			toBeUpdated.push(results[x].pid);
	}
	if(toBeUpdated.length > 0){
		toBeUpdated.push(status);
		sendRequest('update_status', toBeUpdated, changed_dropdowm);
	}
}






</script>
</head>
<body>
<?php
include('mysql_link.inc');
mysql_select_db('cardtaker');
?>
<div style="float:left; border-right:1px solid #666666;">
App: <select id="app" name="app" onchange="app = this.options[this.selectedIndex].value; sendRequest('filter', '', changed_dropdowm);">
		<option id="0">all</option>
<?php
$result = mysql_query('select ID, name from applications order by name');
while($app = mysql_fetch_assoc($result)){
	print '<option value="'.$app['ID'].'">'.$app['name'].'</option>';	
}
?>
</select>
<br />

status: <select name="status" id="status" onchange="status = this.options[this.selectedIndex].value; sendRequest('filter', '', changed_dropdowm);">
				<option value="">any</option>
				<option value="authorized">authorized</option>
				<option value="captured">captured</option>
				<option value="hidden">hidden</option>
				<option value="credited">credited</option>
				<option value="authorization reversed">authorization reversed</option>
			  </select>
		
<br />		
		
with in: <select name="time" id="time" onchange="within = this.options[this.selectedIndex].value; sendRequest('filter', '', changed_dropdowm);">
				<option value="">any time</option>
				<option value="1 DAY">today</option>
				<option value="7 DAY">the last week</option>
				<option value="1 MONTH">the last month</option>
				<option value="6 MONTH">the last 6 months</option>
				<option value="1 YEAR">the last year</option>
			 </select>
<br />
<div style="float:left;">
Search: <input type="text" name="search" id="search" style="width:200px; padding-right:0px; margin-right:0px;" onkeyup="search_payments(this.value, event)"/>
</div>
<div id="search_results" style="float:left; border:1px solid; margin-left:-205px; width:203px; margin-top:20px; display:none;">
adfadf
</div>
</div>
<div  style="position:relative; float:left;">
<div id="results">
<table cellspacing="0">
	<col id="col1"/>
	<col id="col2"/>
	<col id="col3"/>
	<col id="col4"/>
	<col id="col5"/>
	<col id="col6"/>
	<col id="col7"/>
	<col id="col8"/>
	<col id="col9"/>
	<col id="col10"/>
	<col id="col11"/>
	<col id="col12"/>
	<col id="col13"/>	
	<thead>
		<tr>
			<th></th>
			<th>App</th>
			<th>First Name</th>
			<th>Last Name</th>
			<th>Address</th>
			<th>Phone #</th>
			<th>email</th>
			<th>Amount</th>
			<th>Order #</th>
			<th>Decision</th>
			<th>Reason</th>
			<th>status</th>
			<th>time</th>
		</tr>
	</thead>
	<tbody>
	<script>sendRequest('filter', '', changed_dropdowm);</script>
	
		
	</tbody>
</table>
			
</div>
<div style="margin-top:25px;">
	<input type="button" value="authorized" onclick="update_status('authorized');"/><input type="button" value="captured" onclick="update_status('captured');"/><input type="button" value="hidden" onclick="update_status('hidden');"/><input type="button" value="credited" onclick="update_status('credited');"/><input type="button" value="authorization reversed" onclick="update_status('authorization reversed');"/>
</div>
</div>








<!--
<?php
	$result = mysql_query('select *, payments.ID as pid from payments join applications on payments.appID=applications.ID order by name, billTo_lastName, billTo_firstName');
	print mysql_error();
	while($payment = mysql_fetch_assoc($result)){
?>
		<tr onmouseover="this.className = 'highlight'" onmouseout="this.className =''">
			<td><input type="checkbox" name="payments[]" value="<?=$payment['pid']?>"/></td>
			<td><?=$payment['name']?></td>
			<td><?=$payment['billTo_firstName']?></td>
			<td><?=$payment['billTo_lastName']?></td>
			<td><?=$payment['billTo_street1']?></td>
			<td><?=$payment['billTo_phoneNumber']?></td>
			<td><?=$payment['billTo_email']?></td>
			<td><?=$payment['orderAmount']?></td>
			<td><?=$payment['orderNumber']?></td>
			<td><?=$payment['decision']?></td>
			<td><?=$payment['reasonCode']?></td>
			<td><?=$payment['status']?></td>
			<td><?=$payment['time_stamp']?></td>
		</tr>
<?php
	}
?>
-->
</body>
</html>