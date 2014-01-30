<!--
var queryFile = "https://trinity.sunion.arizona.edu/computerassets/query.php";
var machineList= "";
var softwareHide = "";
var curLicense = -1;
var dropdown = "";
var insertedDate = "";
var curDNSSort = "";
function loadComputersList(){
	loadMarketingComputers();
return true;
}
function loadMarketingComputers() {
	var query = 'SELECT dns.ID, dns.IP_address, dns.name, nic.DNS_ID, nic.computerID, computer.* '+
		'FROM computer LEFT JOIN nic ON computer.ID = nic.computerID '+
		'LEFT JOIN dns ON nic.DNS_ID = dns.ID '+
		'WHERE computer.marketingAsset = 1 '+
		'ORDER BY dns.name, dns.IP_address ASC';
	$.post(queryFile, {'query': query}, function(data){
	
		var machines = eval('(' + data + ')');
		var i=0;
		machineList= "";
	
		machineList += '<table WIDTH="100%" CELLPADDING=0 CELLSPACING=0 style="border-bottom:1px solid #5A85CF;border-right:1px solid #5A85CF;">';
	
		machineList += '<tr><td colspan=4 style="font-size:16px;font-weight:bold;background-color:#003366;color:#FFFFFF;cursor:auto;">Marketing Computers</td></tr>';
	
		machineList += '<tr style="background-color:#CFEBFF;font-weight:bold;cursor:auto;">'+
				'<td style="width:70px;">Name</td>'+
				'<td style="width:auto;">Model</td>'+
				'<td style="width:110px;">Description</td>'+
				'<td style="width:90px;">IP</td>'+
			'</tr>';
		
		while(machines[i]){
			if (machines[i].name==null) {
				machines[i].name = " - "
			}
			if (machines[i].IP_address==null) {
				machines[i].IP_address = "Not Assigned"
			}
			machineList += '<tr style="cursor: pointer !important;" onClick="computerDetails('+machines[i].ID+');">'+
				'<td>'+machines[i].name+'</td>'+
				'<td>'+machines[i].type+' - '+machines[i].processorType+'</td>'+
				'<td>'+machines[i].location+'</td>'+
				'<td>'+machines[i].IP_address+'</td>'+
			'</tr>';
			i++;
		}
		
		loadOtherComputers();
		return true;
	});
}
function loadOtherComputers() {
	var query = 'SELECT dns.ID, dns.IP_address, dns.name, nic.DNS_ID, nic.computerID, computer.* '+
		'FROM computer LEFT JOIN nic ON computer.ID = nic.computerID '+
		'LEFT JOIN dns ON nic.DNS_ID = dns.ID '+
		'WHERE computer.marketingAsset = 0 '+
		'ORDER BY dns.name, dns.IP_address ASC';
	$.post(queryFile, {'query': query}, function(data){
	
		var machines = eval('(' + data + ')');
		var i=0;	
	
		machineList += '<tr><td colspan=4 style="font-size:16px;font-weight:bold;background-color:#003366;color:#FFFFFF;cursor:auto;">Other Computers</td></tr>';
	
		machineList += '<tr style="background-color:#CFEBFF;font-weight:bold;cursor:auto;">'+
				'<td>Name</td>'+
				'<td>Model</td>'+
				'<td>Description</td>'+
				'<td>IP</td>'+
			'</tr>';
		
		while(machines[i]){
			if (machines[i].name==null) {
				machines[i].name = " - "
			}
			if (machines[i].IP_address==null) {
				machines[i].IP_address = "Not Assigned"
			}
			machineList += '<tr style="cursor: pointer !important;" onClick="computerDetails('+machines[i].ID+');">';
			machineList += '<td>'+machines[i].name+'</td><td>'+machines[i].type+' - '+machines[i].processorType+'</td>';
			machineList += '<td>'+machines[i].location+'</td><td>'+machines[i].IP_address+'</td></tr>';
			i++;
		}
		
		loadOtherDevices();
		return true;
	});
}
function loadOtherDevices() {
	var query = 'SELECT dns.ID, dns.IP_address, dns.name, nic.DNS_ID, nic.otherID, others.* '+
		'FROM others LEFT JOIN nic ON others.ID = nic.otherID '+
		'LEFT JOIN dns ON nic.DNS_ID = dns.ID '+
		'ORDER BY dns.name, dns.IP_address ASC';
	$.post(queryFile, {'query': query}, function(data){
	
		var machines = eval('(' + data + ')');
		var i=0;	
	
		machineList += '<tr><td colspan=4 style="font-size:16px;font-weight:bold;background-color:#003366;color:#FFFFFF;cursor:auto;">Other Devices</td></tr>';
	
		machineList += '<tr style="background-color:#CFEBFF;font-weight:bold;cursor:auto;">'+
				'<td>Name</td>'+
				'<td>Model</td>'+
				'<td>Description</td>'+
				'<td>IP</td>'+
			'</tr>';
		
		while(machines[i]){
			if (machines[i].name==null) {
				machines[i].name = " - "
			}
			if (machines[i].IP_address==null) {
				machines[i].IP_address = "Not Assigned"
			}
			machineList += '<tr style="cursor: pointer !important;" onClick="otherDevicesDetails('+machines[i].ID+');"';
			machineList += '<td>'+machines[i].name+'</td><td>'+machines[i].description+'</td>';
			machineList += '<td>'+machines[i].location+'</td><td>'+machines[i].IP_address+'</td></tr>';
			i++;
		}
		
		machineList += '</table>';
		document.getElementById("machine_list").innerHTML = machineList;
		
		return true;
	});
}
function computerDetails(ID){
	var computerQuery = 'SELECT * FROM Computer '+
		'LEFT JOIN license ON Computer.ID=license.computerID '+
		'LEFT JOIN Purchase ON Computer.purchaseID=Purchase.ID '+
		'WHERE Computer.ID="'+ID+'"';
	$.post(queryFile, {'query': computerQuery}, function(data){
		var computers = eval('(' + data + ')');
		var computersdiv = "";

		computersdiv += '<div id="computer_NIC">'+
			'<div style="float:left;">'+
				'<h2>Computer</h2>'+
				'<input type="button" value="Update" align="right" onclick="update('+ID+')">'+
				'<form name="computer" onSubmit="return false;">'+
					'<table id="computer_details_table">'+
						'<tr><td>Type:</td><td style="width:150px;"><input type="text" name="type" value="'+computers[0].type+'"></td></tr>'+
						'<tr><td>Processor Type:</td><td><input type="text" name="processorType" value="'+computers[0].processorType+'"></td></tr>'+
						'<tr><td>Processor Speed:</td><td><input type="text" name="processorSpeed" value="'+computers[0].processorSpeed/1000000000+'">Ghz</td></tr>'+
						'<tr><td>Processor Number:</td><td><input type="text" name="processorNum" value="'+computers[0].processorNum+'">'+
						'<tr><td>Serial #:</td><td><input type="text" name="serialNum" value="'+computers[0].serialNum+'"></td></tr>'+
						'<tr><td>Asset Tag:</td><td><input type="text" name="assetTag" value="'+computers[0].assetTag+'"></td></tr>'+
						'<tr><td>Location:</td><td><input type="text" name="location" value="'+computers[0].location+'"></td></tr>'+
						'<tr><td>Purchase:</td><td>'+computers[0].date+'</td></tr>'+
						'<tr><td>Marketing Asset:</td><td><input type="text" name="marketingAsset" value="'+computers[0].marketingAsset+'"></td></tr>'+
					'</table'+
				'</form>'+
			'</div>';
			var nicQuery = 'SELECT *, DNS.name AS dnsname, NIC.name AS nicname, DNS.ID AS dnsid,  NIC.ID AS nicid '+
			'FROM NIC LEFT JOIN DNS ON DNS.ID=NIC.DNS_ID '+
			'WHERE computerID='+ID;
		$.post(queryFile, {'query': nicQuery}, function(data){
			var nic = eval('(' + data + ')');
			var i=0;
			
			computersdiv += '</table></div>';
      if (curPage == "licenses") {
        computersdiv += '<div style="float:left;">';
      }
      else {
        computersdiv += '<div style="float:left;width:100%;">';
      }
			computersdiv +=	'<h3>Network Info'+
					'<input type="button" value="Add" style="float:right; margin-right:15px;" onClick="addnic('+ID+')">'+
				'</h3>';

			if (nic[i]==null) {
				computersdiv += 'No nics attached';
			}
			else {
				computersdiv += '<table style="width:100%;">';
				while(nic[i]){
					computersdiv += '<tr>'+
						'<td>'+nic[i].nicname+'</td>'+
						'<td>'+nic[i].MAC+'</td>'+
						'<td><input type="button" value="Edit" onclick="editnic('+ nic[i].nicid+', '+ID+', '+nic[i].dnsid+')"></td>'+
						'<td><input type="button" value="Remove" onclick="removenic('+"'NIC', "+ nic[i].nicid+', 0, '+ID+', '+nic[i].dnsid+')"></td>'+
						'<td><input type="button" value="Surplus" onclick="removenic('+"'NIC', "+ nic[i].nicid+', -1, '+ID+', '+nic[i].dnsid+')"></td>'+
					'</tr>'+
					'<tr>'+
						'<td> - </td>'+
						'<td colspan=2>'+nic[i].dnsname+"."+nic[i].domain+
						'<td>'+nic[i++].IP_address+'</td></td><td>'+
					'</tr>';
	
				}
				computersdiv += '</table>'; 
			}
			computersdiv += '</div></div>';
		
			var licenseQuery = 'SELECT *, License.ID AS lid FROM Purchase, License, Software '+
				'WHERE computerID='+ID+
					' AND Purchase.ID=License.purchaseID '+
					'AND Software.ID=License.softwareID';
			$.post(queryFile, {'query': licenseQuery}, function(data){
				var licenses = eval('(' + data + ')');
				var i=0;
		
				computersdiv += '<div style="float:left;margin-top:10px;width:100%;">'+
					'<h3>Licenses'+
						'<input type="button" value="Add" style=" margin-left:30px;" onClick="addlic('+ID+')">'+
					'</h3>';
		
				computersdiv += '<table style="width:100%;">';
				while(licenses[i]){
					computersdiv += '<tr>'+
						'<td>'+licenses[i].date+'</td>'+
						'<td>'+licenses[i].Description+'</td>'+
						'<td><input type="button" value="Remove" onclick="remove('+"'License', "+ licenses[i++].lid+', 0, '+ID+')"></td>'+
					'</tr>';
				}
				computersdiv += '</table></div>';
				if (curPage == "licenses") {
				  $("#software_computer").html("").append(computersdiv);
				}
				else {
				  $("#machine_details").html("").append(computersdiv);
				}
			});
		});
	});
}
function otherDevicesDetails(ID){
	deviceQuery = 'select * from Others where ID='+ID;
	$.post(queryFile, {'query': deviceQuery}, function(data){
		var device = eval('(' + data + ')');
		var computersdiv = "";

		computersdiv += '<div id="computer_NIC" style="width:90%;">'+
			'<div style="float:left;width:100%;">'+
				'<h2>Other Device</h2>'+
				'<input type="button" value="Update" align="right" onclick="updateo('+ID+')">'+
				'<form name="otherd" onSubmit="return false;">'+
					'<table>'+
						'<tr><td>Description:</td><td><input type="text" name="description" value="'+device[0].description+'"></td></tr>'+
						'<tr><td>Location:</td><td><input type="text" name="location" value="'+device[0].location+'"></td></tr>'+
						'<tr><td>Asset Tag:</td><td><input type="text" name="assetTag" value="'+device[0].assetTag+'"></td></tr>'+
				'</form>';
			'</div>';

		var nicQuery = 'select *, DNS.name as dnsname, NIC.name as nicname, DNS.ID as dnsid,  NIC.ID as nicid '+
		'from NIC left join DNS on DNS.ID=NIC.DNS_ID  '+
		'where otherID='+ID;
		$.post(queryFile, {'query': nicQuery}, function(data){
			var nic = eval('(' + data + ')');
			var i=0;

			computersdiv += '<div style="float:left;width:100%;">'+
				'<h3>Network Info &nbsp;&nbsp;&nbsp;'+
					'<input type="button" value="Add" style="float:right; margin-right:15px;" onClick="addnico('+ID+')">'+
				'</h3>';

			computersdiv += '<table style="width:100%;">';
			while(nic[i]){
				computersdiv += '<tr>'+
					'<td>'+nic[i].nicname+'</td>'+
					'<td>'+nic[i].MAC+'</td>'+
					'<td><input type="button" value="Remove" onclick="removenico('+"'NIC', "+ nic[i].nicid+', 0, '+ID+', '+nic[i].dnsid+')"></td>'+
					'<td><input type="button" value="Surplus" onclick="removenico('+"'NIC', "+ nic[i].nicid+', -1, '+ID+', '+nic[i].dnsid+')"></td>'+
				'</tr>'+
				'<tr>'+
					'<td> - </td>'+
					'<td colspan=2>'+nic[i].dnsname+"."+nic[i].domain+'</td>'+
					'<td>'+nic[i++].IP_address+'</td>'+
					'</td><td>'+
				'</tr>';

			}
			computersdiv+='	 </table></div></div>'; 

			$("#machine_details").html("").append(computersdiv);
		});
	});
}
function findSerialnum(serial){
$.post(queryFile, {'query':'select ID from Computer where serialNum="'+serial+'"'}, function(data){
var myObject = eval('(' + data + ')');
if(myObject[0]!=null){
computerDetails(myObject[0].ID);
}
else {
  alert("No matching devices found");
}
});
}
function findAssettag(tag){
$.post(queryFile, {'query':'select ID from Computer where assetTag="'+tag+'"'}, function(data){
var myObject = eval('(' + data + ')');
if(myObject[0]!=null){
  computerDetails(myObject[0].ID);
}
else {
  alert("No matching devices found");
}
});
}
function findMac(macaddr){
$.post(queryFile, {'query':'select computerID from nic where MAC="'+macaddr+'"'}, function(data){
var myObject = eval('(' + data + ')');
if(myObject[0]!=null){
computerDetails(myObject[0].computerID);
}
else {
  alert("No matching devices found");
}
});
}
function addComputerDialogHandler() {
	var purchaseQuery = 'SELECT * FROM purchase ORDER BY date DESC';
	$.post(queryFile, {'query': purchaseQuery}, function(data){
		var purchases = eval('(' + data + ')');
		computersdiv=""
		var j=0;
		while(purchases[j]){
			computersdiv += '<option value="'+purchases[j].ID+'">'+purchases[j++].date+'</option>';
		}		
		addComputerDialog(computersdiv);
	});
}
function addComputerDialog(list){

	var computersdiv='<div align="center"><h3 align="center" style="padding-right:15px;">Add Computer<input type="button" value="Cancel" style="float:right; margin-right:15px;" onClick="$('+"'"+'#popup1'+"').css("+"'"+'display'+"',"+"'"+'none'+"' ).html("+"'"+"'"+');"></h3>';
	computersdiv+='		<form name="addComputerDialog" onSubmit="return false;">';
	computersdiv+='<table id="add_computer_dialog_table">';
	computersdiv+='<tr><td style="text-align:right;">type:</td><td><input style="border:1px solid #5A85CF;" type="text" name="type"></td></tr>';
	computersdiv+='<tr><td style="text-align:right;">processor Type:</td><td><input type="text" style="border:1px solid #5A85CF;" name="processorType"></td></tr>';
	computersdiv+='<tr><td style="text-align:right;">processor Speed:</td><td><input type="text" style="border:1px solid #5A85CF;" name="processorSpeed">Ghz</td></tr>';
	computersdiv+='<tr><td style="text-align:right;">processor Number:</td><td><input type="text" style="border:1px solid #5A85CF;" name="processorNum"></td></tr>';
	computersdiv+='<tr><td style="text-align:right;">Serial #:</td><td><input type="text" style="border:1px solid #5A85CF;" name="serialNum"></td></tr>';
	computersdiv+='<tr><td style="text-align:right;">Asset Tag:</td><td><input type="text" style="border:1px solid #5A85CF;" name="assetTag"></td></tr>';
	computersdiv+='<tr><td style="text-align:right;">Location:</td><td><input type="text" style="border:1px solid #5A85CF;" name="location"></td></tr>';
	computersdiv+='<tr><td style="text-align:right;">Purchase ID:</td><td><select name="PurchaseID" id="PurchaseID" >';
	computersdiv+=list;
	computersdiv+='</select></td></tr>';
	computersdiv+='<tr><td style="text-align:right;">Marketing Asset:</td><td><input type="checkbox" name="marketingAsset" value="1"></td></tr>';
	computersdiv+='</table>';
	computersdiv+='<input type="button" value="Add" onClick="addComputer();">';
	computersdiv+='		</form>';

	var top = 600/2 - 175;
	var left = 1000/2-150;

	$("#popup1").css("top", top+"px").css("left", left+"px").css("height", "350px").css("width", "300px").css("border", "1px solid #5A85CF").css("background-color", "white").css("display", "block").html(computersdiv); 
}
function addComputer(){
	var query = 'insert into Computer set type="'+document.addComputerDialog.type.value+'", processorType="'+document.addComputerDialog.processorType.value+'", processorSpeed="'+document.addComputerDialog.processorSpeed.value+'", processorNum='+document.addComputerDialog.processorNum.value*1+', serialNum="'+document.addComputerDialog.serialNum.value+'", assetTag="'+document.addComputerDialog.assetTag.value+'", location="'+document.addComputerDialog.location.value+'", marketingAsset="'+document.addComputerDialog.marketingAsset.value+'", PurchaseID="'+document.addComputerDialog.PurchaseID.value+'"';
	$.post(queryFile, {'query': query }, function(data){
	document.addComputerDialog.reset();
	$("#popup1").css("display","none").html("");
	$.post(queryFile, {'query': "select max(ID) as ID from Computer" }, function(data){
	var myObject = eval('(' + data + ')');

	computerDetails(myObject[0].ID);
	return true;
	});
	return true;
	});

}
function update(ID){
	var query = "update computer set type='"+document.computer.type.value+"', processorType='"+document.computer.processorType.value+"', processorSpeed="+document.computer.processorSpeed.value*1000000000+", processorNum="+document.computer.processorNum.value*1+", serialNum='"+document.computer.serialNum.value+"', assetTag='"+document.computer.assetTag.value+"'"+', location="'+document.computer.location.value+'", marketingAsset='+document.computer.marketingAsset.value+" where ID="+ID;


	//var query1 = "update DNS set IP_address='"+document.computer.type+"', name='"+document.computer.type+"', domain='"+document.computer.type+"', available='"+document.computer.type+"' where computerID='"+ID;
	$.post(queryFile, {'query': query}, function(data){
	 return true;
	 });

	loadComputersList();

}
function removenic(table, ID, surplus, cid, did){
	query = 'update '+table+' set computerID='+surplus+', DNS_ID=0 where ID='+ID;
	$.post(queryFile, {'query': query }, function(data){
	computerDetails(cid);
	return true;
	});

	query = 'update DNS set available=1 where ID='+did;
	$.post(queryFile, {'query': query }, function(data){
	loadComputersList();
	return true;
	});
}
function loadDnsList(sort) {
  if (sort==""||sort==null) {
    sort = "IP_address";
  }
  curDNSSort = sort;
  query =
  'SELECT dns. * , nic.DNS_ID, nic.computerID AS computerID, nic.otherID AS otherID, computer.serialNum '+
  'FROM dns '+
  'LEFT JOIN nic ON dns.ID = nic.DNS_ID '+
  'LEFT JOIN computer ON nic.computerID = computer.ID '+
  'LEFT JOIN others ON nic.otherID = others.ID '+
  'ORDER BY '+sort;
  $.post(queryFile, {'query': query}, function(data){
  
    var machines = eval('(' + data + ')');
    var i=1;
    machineList= "";
  
    machineList += '<table WIDTH="100%" CELLPADDING=0 CELLSPACING=0 style="border-bottom:1px solid #5A85CF;border-right:1px solid #5A85CF;">';
  
    machineList += '<tr style="background-color:#CFEBFF;font-weight:bold;cursor:auto;">'+
        '<td style="cursor:pointer;" onclick="loadDnsList(\'IP_address\');">IP</td>'+
        '<td style="cursor:pointer;" onclick="loadDnsList(\'name\');">Name</td>'+
        '<td>Domain</td>'+
        '<td>Machine Assigned</td>'+
      '</tr>';
    while(machines[i]){
      if (machines[i].name==null) {
        machines[i].name = " - "
      }
      if (machines[i].otherID==null&&machines[i].computerID==null) {
        machineList += '<tr style="cursor:auto;';
        if (machines[i].available==1) {
          machineList += 'background-color:#CFFFD5;';
        }
        else {
          machineList += 'background-color:#FFFFCF;';
        }
        machineList += '">';
      }
      else {
        if (machines[i].IP_address=="150.135.75.175"||machines[i].IP_address=="150.135.72.69") {
          machineList += '<tr style="cursor: pointer !important;background-color:#FFD3AF;" onClick="computerDetails('+machines[i].computerID+');">';
        }
        else if (machines[i].otherID==null) {
          machineList += '<tr style="cursor: pointer !important;" onClick="computerDetails('+machines[i].computerID+');">';
        }
        else {
          machineList += '<tr style="cursor: pointer !important;" onClick="otherDevicesDetails('+machines[i].otherID+');">';
        }
      }
      machineList += '<td>'+machines[i].IP_address+'</td>'+
        '<td><a href="vnc://'+machines[i].IP_address+'">'+machines[i].name+'</a></td>'+
        '<td>'+machines[i].domain+'</td>';
      if (machines[i].serialNum==null && machines[i].otherID!=null) {
        machineList += '<td> - </td>';
      }
      else if (machines[i].serialNum==null && machines[i].available==1) {
        machineList += '<td> - </td>';
      }
      else if (machines[i].serialNum==null) {
        machineList += '<td>No Record</td>';
      }
      else {
        machineList += '<td>'+machines[i].serialNum+'</td>';
      }
      '</tr>';
      i++;
    }
    machineList += '</table>';
    if (curPage == "index") {
      document.getElementById("machine_list").innerHTML = machineList;
      return true;
    }
    else if (curPage == "licenses") {
      loadSoftwareList(softwareHide);
      loadSoftwareLicenses(curLicense);
      return true;
    }
    else if (curPage == "dns") {
      document.getElementById("machine_list").innerHTML = machineList;
      return true;
    }
    return false;
  });
}
function loadPurchaseList(){
  getPurchaseDateDropdown();
  var query = 'SELECT * FROM purchase ORDER BY date DESC';
  $.post(queryFile, {'query': query}, function(data){

    var purchases = eval('(' + data + ')');
    var i=0;
    var purchaseDates= "";
    
    while(purchases[i]){
      purchaseDates += '<div id="purchase_date" onClick="show_info('+purchases[i].ID+',\''+purchases[i].date+'\');">'+purchases[i++].date+'</div>';
    }
    document.getElementById("purchase_dates").innerHTML = purchaseDates;
    
    return true;
  });
return true;
}
function show_info(id,date){
  var purchaseQuery = 'select concat(type, " ", round((processorSpeed/1000000000), 2), " GHz ", processorType) as descr from computer where PurchaseID='+id+' union all select description as descr from software join license on software.ID = license.softwareID where purchaseID='+id;
  $.post(queryFile, {'query': purchaseQuery}, function(data){
    var purchasediv ='';
    var purchaseItems = eval('(' + data + ')');
    purchasediv += '<table>';
    purchasediv += '<tr><td><h2>Purchase Items - '+date+'</h2></td></tr>';
    i= 0;
    while(purchaseItems [i]){
      purchasediv += '<tr ><td style="padding-left:5px;">'+purchaseItems[i++].descr+'</td></tr>';
    }
    purchasediv += '</table>';
    document.getElementById("purchase_items").innerHTML = purchasediv;                                                   
  });
}
function showPurchase(id,date){
  var purchaseQuery = 'select concat(type, " ", round((processorSpeed/1000000000), 2), " GHz ", processorType) as descr from computer where PurchaseID='+id+' union all select description as descr from software join license on software.ID = license.softwareID where purchaseID='+id;
  $.post(queryFile, {'query': purchaseQuery}, function(data){
    var purchasediv ='';
    var purchaseItems = eval('(' + data + ')');
    purchasediv += '<table>';
    purchasediv += '<tr><td><h2>Purchase Items - '+date+'</h2></td></tr>';
    i= 0;
    while(purchaseItems [i]){
      purchasediv += '<tr ><td style="padding-left:5px;">'+purchaseItems[i++].descr+'</td></tr>';
    }
    purchasediv += '</table>';
    document.getElementById("software_computer").innerHTML = purchasediv;                                                  
  });
}
function loadSoftwareList(hide) {
  var query = 'SELECT * FROM software ';
  if (hide=='current') {
    query += 'WHERE visible = 1 ';
  }
  query += 'ORDER BY Description ASC';
  $.post(queryFile, {'query': query}, function(data){

    var software = eval('(' + data + ')');
    var i=0;
    var softwareList= "";
    
    while(software[i]){
      softwareList += '<div id="software_title">'+
      '<input type=checkbox ';
      if (software[i].visible==1) {
        softwareList += 'checked onclick="softwareVisiblity('+software[i].ID+',\'hide\');" ';
      }
      else {
        softwareList += 'onclick="softwareVisiblity('+software[i].ID+',\'show\');" ';
      }
      softwareList += '/> <span onclick="loadSoftwareLicenses('+software[i].ID+');">'+
      software[i++].Description+'</span></div>';
    }
    document.getElementById("software_titles").innerHTML = softwareList;
    if (hide=='current') {
      document.getElementById("software_current").style.backgroundColor='#003366';
      document.getElementById("software_current").style.color='#FFFFFF';
      document.getElementById("software_all").style.backgroundColor='#FFFFFF';
      document.getElementById("software_all").style.color='#003366';
    }
    else {
      document.getElementById("software_current").style.backgroundColor='#FFFFFF';
      document.getElementById("software_current").style.color='#003366';
      document.getElementById("software_all").style.backgroundColor='#003366';
      document.getElementById("software_all").style.color='#FFFFFF';
    }
    if (curLicense>=0) {
      loadSoftwareLicenses(curLicense);
    }
    return true;
  });
softwareHide = hide;
return true;
}
function loadSoftwareLicenses(id) {
  if (id>=0) {
    var query = 'SELECT license. * , computer.serialNum, purchase.date, software.Description '+
      'FROM license '+
      'LEFT JOIN computer ON license.computerID = computer.ID '+
      'LEFT JOIN purchase ON license.purchaseID = purchase.ID '+
      'LEFT JOIN software ON license.softwareID = software.ID '+
      'WHERE software.ID = '+id;
    if (softwareHide=='current') {
      query += ' AND license.visible = 1 ';
    }
    $.post(queryFile, {'query': query}, function(data){
      var licenses = eval('(' + data + ')');
      var i=0;
      var softwareLicenses= '<table WIDTH="100%" CELLPADDING=0 CELLSPACING=0 style="border-bottom:1px solid #5A85CF;border-right:1px solid #5A85CF;">'+
          '<tr style="background-color:#CFEBFF;font-weight:bold;cursor:auto;">'+
          '<td style="width:auto;">Description</td>'+
          '<td style="width:130px;">Assigned Machine</td>'+
          '<td style="width:115px;">Purchase Date</td>'+
          '<td style="width:115px;">Visible</td>'+
        '</tr>';
      
      while(licenses[i]){
        softwareLicenses += '<tr>'+
          '<td>'+licenses[i].Description+'</td>';
          if (licenses[i].serialNum!=null) {
            softwareLicenses += '<td style="cursor:pointer;" onclick="computerDetails('+licenses[i].computerID+');" >'+licenses[i].serialNum+'</td>';
          }
          else {
            softwareLicenses += '<td style="background-color:#CFFFD5;"> - </td>';
          }
          softwareLicenses += '<td style="cursor:pointer;" onclick="showPurchase('+licenses[i].purchaseID+',\''+licenses[i].date+'\')">'+licenses[i].date+'</td>'+
          '<td><input type="checkbox" ';
          if (licenses[i].visible==1) {
            softwareLicenses += 'checked onclick="licenseVisiblity('+licenses[i].ID+',\'hide\');" ';
          }
          else {
            softwareLicenses += 'onclick="licenseVisiblity('+licenses[i].ID+',\'show\');" ';
          }
          softwareLicenses +='/></td>'+
        '</tr>';
        i++;
      }
      if (i==0) {
        softwareLicenses += '<tr>'+
          '<td>&nbsp;&nbsp;&nbsp;<strong>No Records</strong></td>'+
          '<td>&nbsp;</td>'+
          '<td>&nbsp;</td>'+
          '<td>&nbsp;</td>'+
        '</tr>';
      }
      softwareLicenses += '</table>';
      document.getElementById("software_licenses").innerHTML = softwareLicenses;
      curLicense = id;
      return true;
    });
} 
return true;
}
function editnic(id, cid, dnsid){
  $.post(queryFile, {'query': 'select * from DNS where available="1" or ID='+dnsid }, function(data){
    myObject = eval('(' + data + ')');
    i=0;
    var computersdiv='<div><h3 align="center">Available DNSs<input type="button" value="Done" style="float:right; margin-right:15px;" onClick="$('+"'"+'#popup'+"').css("+"'"+'display'+"',"+"'"+'none'+"' ).html("+"'"+"'"+');"></h3>';

    computersdiv+='<form name="dns3" onsubmit="return false;"><table>';
    while(myObject[i]){
      var sl='';
      if(myObject[i].available == 0) {
        sl='checked';
      }
      computersdiv+='<tr><td>';
      computersdiv+='<input type="radio" value="'+myObject[i].ID+'" name="dnss3" '+sl+'>';
      computersdiv+='</td><td>'+myObject[i].name;
      computersdiv+='</td><td>'+myObject[i++].IP_address;
      
      computersdiv+='</td>';
      computersdiv+='</tr>';
    }
    computersdiv+='<tr><td>';
    computersdiv+='<input type="radio" value="0" name="dnss3">';
    computersdiv+='</td><td>none';
    computersdiv+='</td><td>-';
    computersdiv+='</td>';
    computersdiv+='</tr>';
    computersdiv+='</form></table><input type="button" value="change" onclick="editnic1('+id+', '+cid+', '+dnsid+')"></div>';

    var top = 600/2 - 200;
    var left = 1000/2-150;

    $("#popup").css("top", top+"px").css("left", left+"px").css("height", "400px").css("width", "300px").css("border", "1px solid #5A85CF").css("background-color", "white").css("display", "block").html(computersdiv); 
  return true;
  }); 
}

function editnic1(id, cid, dnsid){
  var i =0;
  if(document.dns3.dnss3.length==undefined){
  var dns = document.dns3.dnss3.value;
  }
  else{
  while(!document.dns3.dnss3[i].checked){
  i++
  }
  var dns = document.dns3.dnss3[i].value;
  }
  
  query = 'update NIC set computerID="'+cid+'", DNS_ID="'+dns+'" where ID="'+id+'"';
  $.post(queryFile, {'query': query }, function(data){
  document.dns3.reset();
  $.post(queryFile, {'query': 'update DNS set available=0 where ID="'+dns+'"' }, function(data){
  
  computerDetails(cid);
  loadComputersList();
  editnic(id, cid, dns);
  return true;
  });
  $.post(queryFile, {'query': 'update DNS set available=1 where ID="'+dnsid+'"' });
  return true;
  });
  
}
function updateo(ID){
var query = "update Others set description='"+document.otherd.description.value+"', assetTag='"+document.otherd.assetTag.value+"'"+', location="'+document.otherd.location.value+'" where ID='+ID;
$.post(queryFile, {'query': query}, function(data){
 return true;
 });

loadComputersList();

}
function remove(table, ID, surplus, cid){
query = 'update '+table+' set computerID='+surplus+' where ID='+ID;
$.post(queryFile, {'query': query }, function(data){
computerDetails(cid);
return true;
});
}
function removenico(table, ID, surplus, cid, did){


query = 'update '+table+' set otherID='+surplus+', DNS_ID=0 where ID='+ID;
$.post(queryFile, {'query': query }, function(data){
computerDetails(cid);
return true;
});

query = 'update DNS set available=1 where ID='+did;
$.post(queryFile, {'query': query }, function(data){
loadComputersList();
return true;
});


}
function changenic(ID, cid){

}

function changenic1(cid){

}
function addnic(ID){
$.post(queryFile, {'query': 'select * from NIC where computerID="0"' }, function(data){
var myObject = eval('(' + data + ')');
var i=0;

var computersdiv='<div><h3 align="center">Available NICs<input type="button" value="Done" style="float:right; margin-right:15px;" onClick="$('+"'"+'#popup'+"').css("+"'"+'display'+"',"+"'"+'none'+"' ).html("+"'"+"'"+');"></h3>';



computersdiv+='<table style="width:100%;"><tr><td>name</td><td>MAC</td><td> </td></tr>';
while(myObject[i]){
computersdiv+='<tr><td>';
computersdiv+=myObject[i].name;
computersdiv+='</td><td>'+myObject[i].MAC;
computersdiv+='</td><td><input type="button" value="Add" onclick="addnic2('+ myObject[i++].ID+','+ID+'); addnic('+ID+');">';
computersdiv+='</td>';
computersdiv+='</tr>';
}
computersdiv+='<tr><td><form name="addnic" id="addnic" onsubmit="return false;"><input type="text" id="newName" name="newName" size="10" /></td><td><input id="newMac" type="text" name="newMac" size="17" /></td><td><input type="button" value="Add" onClick="addnic1('+ID+');"></td></tr>';
computersdiv+='  </table></form></div>';

$.post(queryFile, {'query': 'select * from DNS where available="1"' }, function(data){
 myObject = eval('(' + data + ')');
 i=0;

 computersdiv+='<div><h5 align="center">Available DNSs</h5>';

computersdiv+='<table>';

while(myObject[i]){
computersdiv+='<tr><td>';
computersdiv+='<form name="dns1" onsubmit="return false;"><input type="radio" value="'+myObject[i].ID+'" name="dnss">';
computersdiv+='</td><td>'+myObject[i].name;
computersdiv+='</td><td>'+myObject[i++].IP_address;

computersdiv+='</td>';
computersdiv+='</tr>';
}
computersdiv+='</form></table></div>';

var top = 600/2 - 200;
var left = 1000/2-150;

$("#popup").css("top", top+"px").css("left", left+"px").css("height", "450px").css("width", "400px").css("border", "1px solid #5A85CF").css("background-color", "white").css("display", "block").html(computersdiv); 

return true;
});

return true;
});

}

function addnic1(cid){
var i =0;
if(document.dns1.dnss.length==undefined){
var dns = document.dns1.dnss.value;
}
else{
while(!document.dns1.dnss[i].checked){
i++
}
var dns = document.dns1.dnss[i].value;
}
var newName = document.getElementById("newName").value;
var newMac = document.getElementById("newMac").value;
query = 'insert into NIC set name="'+newName+'", MAC="'+newMac+'", computerID="'+cid+'", DNS_ID="'+dns+'"';
$.post(queryFile, {'query': query }, function(data){
document.addnic.reset();
$.post(queryFile, {'query': 'update DNS set available=0 where ID="'+dns+'"' }, function(data){

computerDetails(cid);
addnic(cid);
loadComputersList();
return true;
});
return true;
});

}

function addnic2(nicid, cid){
var i =0;
if(document.dns1.dnss.length==undefined){
var dns = document.dns1.dnss.value;
}
else{
while(!document.dns1.dnss[i].checked){
i++
}
var dns = document.dns1.dnss[i].value;
}

query = 'update NIC set computerID="'+cid+'", DNS_ID="'+dns+'" where ID="'+nicid+'"';
$.post(queryFile, {'query': query }, function(data){
document.addnic.reset();
$.post(queryFile, {'query': 'update DNS set available=0 where ID="'+dns+'"' }, function(data){

computerDetails(cid);
addnic(cid);
loadComputersList();
return true;
});
return true;
});

}

function addnico(ID){
$.post(queryFile, {'query': 'select * from NIC where computerID="0"' }, function(data){
var myObject = eval('(' + data + ')');
var i=0;

var computersdiv='<div><h3 align="center">Available NICs<input type="button" value="Done" style="float:right; margin-right:15px;" onClick="$('+"'"+'#popup'+"').css("+"'"+'display'+"',"+"'"+'none'+"' ).html("+"'"+"'"+');"></h3>';



computersdiv+='<table><tr><td>name</td><td>MAC</td><td> </td></tr>';
while(myObject[i]){
computersdiv+='<tr><td>';
computersdiv+=myObject[i].name;
computersdiv+='</td><td>'+myObject[i].MAC;
computersdiv+='</td><td><input type="button" value="Add" onclick="addnico2('+ myObject[i++].ID+','+ID+'); addnic('+ID+');">';
computersdiv+='</td>';
computersdiv+='</tr>';
}
computersdiv+='<tr><td><form name="addnic" onsubmit="return false;"><input type="text" name="name" size="10"></td><td><input type="text" name="mac" size="17"></td><td><input type="button" value="Add" onClick="addnico1('+ID+');"></td></tr>';
computersdiv+='  </table></form></div>';

$.post(queryFile, {'query': 'select * from DNS where available="1"' }, function(data){
 myObject = eval('(' + data + ')');
 i=0;

 computersdiv+='<div><h5 align="center">Available DNSs</h5>';

computersdiv+='<table>';

while(myObject[i]){
computersdiv+='<tr><td>';
computersdiv+='<form name="dns1" onsubmit="return false;"><input type="radio" value="'+myObject[i].ID+'" name="dnss">';
computersdiv+='</td><td>'+myObject[i].name;
computersdiv+='</td><td>'+myObject[i++].IP_address;

computersdiv+='</td>';
computersdiv+='</tr>';
}
computersdiv+='<input type="</form></table></div>';

var top = 600/2 - 200;
var left = 1000/2-150;

$("#popup").css("top", top+"px").css("left", left+"px").css("height", "400px").css("width", "300px").css("border", "1px solid #5A85CF").css("background-color", "white").css("display", "block").html(computersdiv); 

return true;
});

return true;
});

}

function addnico1(cid){
var i =0;
if(document.dns1.dnss.length==undefined){
var dns = document.dns1.dnss.value;
}
else{
while(!document.dns1.dnss[i].checked){
i++
}
var dns = document.dns1.dnss[i].value;
}
query = 'insert into NIC set name="'+document.addnic.name.value+'", MAC="'+document.addnic.mac.value+'", otherID="'+cid+'", DNS_ID="'+dns+'"';

$.post(queryFile, {'query': query }, function(data){
document.addnic.reset();
$.post(queryFile, {'query': 'update DNS set available=0 where ID="'+dns+'"' }, function(data){

otherDeviceDetails(cid);
addnico(cid);
loadComputersList();
return true;
});
return true;
});

}

function addnico2(nicid, cid){
var i =0;
if(document.dns1.dnss.length==undefined){
var dns = document.dns1.dnss.value;
}
else{
while(!document.dns1.dnss[i].checked){
i++
}
var dns = document.dns1.dnss[i].value;
}

query = 'update NIC set otherID="'+cid+'", DNS_ID="'+dns+'" where ID="'+nicid+'"';
$.post(queryFile, {'query': query }, function(data){
document.addnic.reset();
$.post(queryFile, {'query': 'update DNS set available=0 where ID="'+dns+'"' }, function(data){

otherDeviceDetails(cid);
addnico(cid);
loadComputersList();
return true;
});
return true;
});

}
function adddns(){

var computersdiv='<div align="center"><h3 align="center" style="padding-right:15px;">Add DNS<input type="button" value="Cancel" style="float:right; margin-right:15px;" onClick="$('+"'"+'#popup1'+"').css("+"'"+'display'+"',"+"'"+'none'+"' ).html("+"'"+"'"+');"></h3>';
computersdiv+='   <form name="adddns" onSubmit="return false;">';
computersdiv+='<br>   IP Address: <input type="text" name="IP_address">';
computersdiv+='<br>   Name: <input type="text" name="name">';
computersdiv+='<br>   Domain: <input type="text" name="domain">';
computersdiv+='<br>   <input type="button" value="Add" onClick="adddns1();">';
computersdiv+='   </form>';




var top = 600/2 - 200;
var left = 1000/2-200;

$("#popup1").css("top", top+"px").css("left", left+"px").css("height", "400px").css("width", "400px").css("border", "1px solid #5A85CF").css("background-color", "white").css("display", "block").html(computersdiv); 


}

function adddns1(){
var query = 'insert into DNS set IP_address="'+document.adddns.IP_address.value+'", name="'+document.adddns.name.value+'", domain="'+document.adddns.domain.value+'", available=1';
$.post(queryFile, {'query': query }, function(data){
document.adddns.reset();
return true;
});

}
function newSoftwareDialog() {
  query= 'SELECT * FROM Software ORDER BY Description ASC';
  $.post(queryFile, {'query': query}, function(data){
    var myObject = eval('(' + data + ')');
    var i=0;
    
    var computersdiv='<div align="center">'+
      '<h3 align="center" style="padding-right:15px;">'+
        'Add New Software'+
        '<input type="button" value="Cancel" style="float:right; margin-right:15px;" onClick="$('+"'"+'#popup3'+"').css("+"'"+'display'+"',"+"'"+'none'+"' ).html("+"'"+"'"+');">'+
      '</h3>'+
      '<form name="addsoft" onSubmit="return false;"><table cellpadding="3px">'+
        '<tr>'+
          '<td>Software</td>'+
          '<td>&nbsp;</td>'+
        '</tr>'+
        '<tr>'+
          '<td><input type="text" id="newName" name="newName" size="30" /></td>'+
          '<td><input type="button" value="Add" onClick="addNewSoftware(document.addsoft.newName.value)"></td>'+
        '</tr>'+
      '</table></form>'+
    '</div>';

var top = 600/2 - 75;
var left = 1000/2-225;

$("#popup3").css("top", top+"px").css("left", left+"px").css("height", "150px").css("width", "450px").css("border", "1px solid #5A85CF").css("background-color", "white").css("display", "block").html(computersdiv); 
return true
});

return true;
}
function addNewSoftware(newName) {
  var query = 'INSERT INTO software set Description="'+newName+'"';
  $.post(queryFile, {'query': query }, function(data){
    loadSoftwareList(softwareHide);
  });
  $("#popup3").css("display","none").html("");
  return true;
}
function addpurchasedate(newdate){
  var query = 'insert into Purchase set date="'+newdate+'"';
  insertedDate = newdate;
  $.post(queryFile, {'query': query }, function(data){
    addpurchase(insertedDate);
    loadPurchaseList();
    return true;
  });
}
function getPurchaseDateDropdown() {
  dropdown = "";
  query = 'SELECT * FROM purchase ORDER BY date DESC';
  $.post(queryFile, {'query': query}, function(data){
    var purchases = eval('(' + data + ')');
    var i=0;
    while(purchases[i]){
      dropdown += '<option value="'+purchases[i].date+'">'+purchases[i].date+'</option>';
      i++;
    }
  });
  return true;
}
function addpurchase(date){
if (date==null) {
  var computersdiv ="";
  computersdiv += '<form name="purchasedate" id="purchasedate" >'+
    '<div style="float:left;width:100%;">'+
      '<div style="float:left;margin-top:10px;width:180px;height:25px;padding-left:15px;">'+
        '<h2 style="margin-top:3px;">Add Purchase Date</h2>'+
      '</div>'+
      '<div style="float:right;margin-top:10px;width:180px;height:25px;padding-right:15px;text-align:right;">'+
        '<input type="button" value="Cancel" onclick=\'$("#popup2").css("display","none").html("");\' />'+
      '</div>'+
    '</div>'+
    '<div style="float:left;width:100%;margin-top:25px;">'+
      '<div style="float:right;width:200px;margin-left:10px;">'+
        '<input type="button" value="Add New Date" onclick="addpurchasedate(document.purchasedate.newdate.value)" />'+
      '</div>'+
      '<div style="float:right;width:150px;text-align:right;">'+
        '<input id="newdate" name="newdate" type="text" size="12" />'+
      '</div>'+
    '</div>'+
    '<div style="float:left;width:100%;margin-top:15px;">'+
      '<div style="float:right;width:200px;margin-left:10px;">'+
        '<input type="button" value="Add Purchases to Date" onclick="addpurchase(document.purchasedate.date.value)" />'+
      '</div>'+
      '<div style="float:right;width:150px;text-align:right;">'+
        '<select name="date" id="date">'+
          '<option value=""></option>'+
          dropdown+
        '</select>'+
      '</div>'+
    '</div>'+
  '</form>';
  
  
  var top = 600/2 - 75;
  var left = 1000/2-200;
  $("#popup2").css("top", top+"px").css("left", left+"px").css("height", "150px").css("width", "400px").css("border", "1px solid #5A85CF").css("background-color", "white").css("display", "block").html(computersdiv);
  return true;
}
else {
  query= 'select Computer.type, Purchase.ID from Purchase left join Computer on Purchase.ID=Computer.PurchaseID where date="'+date+'"  union all select Software.Description, Purchase.ID from (Purchase join License on Purchase.ID=License.purchaseID) join Software on License.softwareID=Software.ID where date="'+date+'"';
  
  //'select Computer.type, Software.Description, Purchase.ID from ((Purchase left outer join Computer on Computer.PurchaseID=Purchase.ID) left outer join License on Purchase.ID=License.purchaseID) left outer join Software on Software.ID=License.softwareID where date="'+ date + '"'
  
  $.post(queryFile, {'query': query}, function(data){
  
  var myObject = eval('(' + data + ')');
  var i=0;
  
  var computersdiv='<div><h3 align="center">Purchase<input type="button" value="Done" style="float:right; margin-right:15px;" onClick="$('+"'"+'#popup2'+"').css("+"'"+'display'+"',"+"'"+'none'+"' ).html("+"'"+"'"+');"></h3>';
  
  computersdiv+='<table>';
  while(myObject[i]){
    if(myObject[i].type!=null) {
      computersdiv+='<tr><td>';
      computersdiv+=myObject[i].type;
      computersdiv+='</td>';
      computersdiv+='</tr>';
    }
    i++;
  }
  computersdiv+='<tr><td><input type="button" value="Add Computer" onClick="addcp('+myObject[0].ID+', \''+date+'\');">&nbsp;&nbsp;&nbsp;<input type="button" value="Add Software" onClick="addsoft('+myObject[0].ID+', \''+date+'\')"></td></tr>';
  computersdiv+='  </table></div>';
  
  
  
  
  
  var top = 600/2 - 200;
  var left = 1000/2-200;
  
  $("#popup2").css("overflow", "auto").css("top", top+"px").css("left", left+"px").css("height", "400px").css("width", "400px").css("border", "1px solid #5A85CF").css("background-color", "white").css("display", "block").html(computersdiv); 
  return true;
  });
  
  return true;
}
return false;
}

function addsoft(pid, date){
  query= 'select * from Software order by Description asc';

  $.post(queryFile, {'query': query}, function(data){
    var myObject = eval('(' + data + ')');
    var i=0;
    
    var computersdiv='<div align="center">'+
      '<h3 align="center" style="padding-right:15px;">'+
        'Add Software'+
        '<input type="button" value="Cancel" style="float:right; margin-right:15px;" onClick="$('+"'"+'#popup3'+"').css("+"'"+'display'+"',"+"'"+'none'+"' ).html("+"'"+"'"+');">'+
      '</h3>'+
      '<form name="addsoft" onSubmit="return false;"><table cellpadding="3px">'+
        '<tr>'+
          '<td>Software</td>'+
          '<td>Quantity</td>'+
        '</tr>'+
        '<tr>'+
          '<td>'+
            '<select name="Description">';
            while(myObject[i]){
              computersdiv+='<option value="'+myObject[i].ID+'">'+myObject[i++].Description+'</option>';
            }
            computersdiv+='</select>'+
          '</td>'+
          '<td><input type="text" id="quantity" name="quantity" size="5" /></td>'+
        '</tr>'+
      '</table>'+
      '<br /><br />'+
      '<div style="text-align: right; width: 430px;margin-right: 20px;"><input type="button" value="Add" onClick="addsoft1('+pid+', \''+date+'\');"></div>'+
      '</form>'+
    '</div>';

var top = 600/2 - 75;
var left = 1000/2-225;

$("#popup3").css("top", top+"px").css("left", left+"px").css("height", "150px").css("width", "450px").css("border", "1px solid #5A85CF").css("background-color", "white").css("display", "block").html(computersdiv); 
return true
});

return true;
}

function addsoft1(pid, date){
  var softwareID = document.addsoft.Description.value;
  var quantity = document.addsoft.quantity.value;
  var i = 1;

  var query='insert into License set softwareID='+softwareID+', computerID="0", purchaseID="'+pid+'"';
  while (i<=quantity) {
    $.post(queryFile, {'query': query }, function(data){
      addpurchase(date);
      $("#popup3").css("display","none").html("");
      return true;
    });
    i++;
  }
  return true;
}

function addcp(pid, date){

var computersdiv='<div align="center"><h3 align="center" style="padding-right:15px;">Add Computer<input type="button" value="Cancel" style="float:right; margin-right:15px;" onClick="$('+"'"+'#popup3'+"').css("+"'"+'display'+"',"+"'"+'none'+"' ).html("+"'"+"'"+');"></h3>';
computersdiv+='<form id="addc" name="addc" onSubmit="return false;"><table>';
computersdiv+='<tr><td>Type:</td><td><input type="text" name="type"></td></tr>';
computersdiv+='<tr><td>Processor Type:</td><td><input type="text" name="processorType"></td></tr>';
computersdiv+='<tr><td>Processor Speed:</td><td><input type="text" name="processorSpeed">Ghz</td></tr>';
computersdiv+='<tr><td>Processor Number:</td><td><input type="text" name="processorNum"></td></tr>';
computersdiv+='<tr><td>Serial #:</td><td><input type="text" name="serialNum"></td></tr>';
computersdiv+='<tr><td>Asset Tag:</td><td><input type="text" name="assetTag"></td></tr>';
computersdiv+='<tr><td>Location:</td><td><input type="text" name="location"></td></tr>';
computersdiv+='<tr><td>Marketing Asset:</td><td><input type="checkbox" name="marketingAsset" value="1"></td></tr></table>';
computersdiv+='<input type="button" value="Add" onClick="addcp1('+pid+', \''+date+'\');" >';
computersdiv+='</form>';




var top = 600/2 - 200;
var left = 1000/2-200;

$("#popup3").css("top", top+"px").css("left", left+"px").css("height", "400px").css("width", "400px").css("border", "1px solid #5A85CF").css("background-color", "white").css("display", "block").html(computersdiv); 



}

function addcp1(pid, date){
var query = 'insert into Computer set type="'+document.addc.type.value+'", processorType="'+document.addc.processorType.value+'", processorSpeed="'+document.addc.processorSpeed.value+'", processorNum='+document.addc.processorNum.value*1+', serialNum="'+document.addc.serialNum.value+'", assetTag="'+document.addc.assetTag.value+'", location="'+document.addc.location.value+'", marketingAsset='+document.addc.marketingAsset.checked+', PurchaseID="'+pid+'"';
$.post(queryFile, {'query': query }, function(data){
document.addc.reset();
$.post(queryFile, {'query': "select max(ID) as ID from Computer" }, function(data){
var myObject = eval('(' + data + ')');

computerDetails(myObject[0].ID);
addpurchase(date);
$("#popup3").css("display","none").html("");
return true;
});
return true;
});

}

function addlic(cid){
$.post(queryFile, {'query': 'select License.ID, Software.Description, Purchase.date from License, Software, Purchase  where Purchase.ID=License.purchaseID and computerID="0" and License.SoftwareID=Software.ID and License.visible=1 ORDER BY Software.Description, Purchase.Date' }, function(data){
 myObject = eval('(' + data + ')');
 i=0;

var computersdiv='<div><h3 align="center">Available Licenses<input type="button" value="Cancel" style="float:right; margin-right:15px;" onClick="$('+"'"+'#popup'+"').css("+"'"+'display'+"',"+"'"+'none'+"' ).html("+"'"+"'"+');"></h3>';

computersdiv+='<form name="lic1" onsubmit="return false;"><table>';

while(myObject[i]){
computersdiv+='<tr><td>';
computersdiv+='<input type="checkbox" value="'+myObject[i].ID+'" name="license">';
computersdiv+='</td><td>'+myObject[i].Description;

computersdiv+='</td>';
computersdiv+='<td>'+myObject[i++].date+'</td>';
computersdiv+='</tr>';
}
computersdiv+='<input type="button" value="Add" onClick="addlic1(\''+cid+'\');"></form></table></div>';

var top = 600/2 - 300;
var left = 1000/2 - 200;

$("#popup").css("top", top+"px").css("overflow", "auto").css("left", left+"px").css("height", "600px").css("width", "400px").css("border", "1px solid #5A85CF").css("background-color", "white").css("display", "block").html(computersdiv); 

return true;
});

}

function addlic1(cid){
if(document.lic1.license.length==undefined){
var query="update License set computerID='"+cid+"' where ID='"+document.lic1.license.value+"'";
$.post(queryFile, {'query': query }, function(data){return true});
}
else{
for(i=0; i<document.lic1.license.length; i++){

if(document.lic1.license[i].checked){
var query="update License set computerID='"+cid+"' where ID='"+document.lic1.license[i].value+"'";
}

$.post(queryFile, {'query': query }, function(data){return true});
}}
computerDetails(cid);
$("#popup").css("display","none").html("");
}
function softwareVisiblity(softwareID,show) {
  if (show=="hide") {
    var query = 'UPDATE software SET visible="0" WHERE ID="'+softwareID+'"';
    $.post(queryFile, {'query': query }, function(data){
      loadSoftwareList(softwareHide);
      return true;
    });
  }
  else {
    var query = 'UPDATE software SET visible="1" WHERE ID="'+softwareID+'"';
    $.post(queryFile, {'query': query }, function(data){
      loadSoftwareList(softwareHide);
      return true;
    });
  }
return true;
}
function licenseVisiblity(licenseID,show) {
  if (show=="hide") {
    var query = 'UPDATE license SET license.visible="0" WHERE license.ID="'+licenseID+'"';
    $.post(queryFile, {'query': query }, function(data){
      loadSoftwareList(softwareHide);
      loadSoftwareLicenses(curLicense);
      return true;
    });
  }
  else {
    var query = 'UPDATE license SET license.visible="1" WHERE license.ID="'+licenseID+'"';
    $.post(queryFile, {'query': query }, function(data){
      loadSoftwareList(softwareHide);
      loadSoftwareLicenses(curLicense);
      return true;
    });
  }
return true;
}
-->