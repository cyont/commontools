// JavaScript Document
function validateInput(form){
	var noerror = true;
	if(form.billTo_firstName.value == ''){
		form.billTo_firstName.className = 'tberror';
		noerror=false;
	}
	else{
		form.billTo_firstName.className = '';
	}
	if(form.billTo_lastName.value == ''){
		form.billTo_lastName.className = 'tberror';
		noerror=false;
	}
	else{
		form.billTo_lastName.className = '';
	}
	if(form.billTo_street1.value == ''){
		form.billTo_street1.className = 'tberror';
		noerror=false;
	}
	else{
		form.billTo_street1.className = '';
	}
	if(form.billTo_city.value == ''){
		form.billTo_city.className = 'tberror';
		noerror=false;
	}
	else{
		form.billTo_city.className = '';
	}
	if(form.billTo_state.value == ''){
		document.getElementById('ct_state_error').innerHTML = '*';
		noerror=false;
	}
	else{
		document.getElementById('ct_state_error').innerHTML = '';
	}
	if(form.billTo_postalCode.value == ''){
		
		form.billTo_postalCode.className = 'tberror';
		noerror=false;
	}
	else{
		form.billTo_postalCode.className = '';
	}
	if(form.card_cardType.value == ''){
		document.getElementById('ct_type_error').innerHTML = '*';
		noerror=false;
	}
	else{
		document.getElementById('ct_type_error').innerHTML = '';
	}
	/*
	if(form.card_expirationMonth.value == ''){
		form.card_expirationMonth.className = 'tberror';
		noerror=false;
	}
	else{
		form.card_expirationMonth.className = '';
	}
	if(form.card_expirationYear.value == '' || form.card_expirationYear.value.length != 4){
		form.card_expirationYear.className = 'tberror';
		noerror=false;
	}
	else{
		form.card_expirationYear.className = '';
	}
	*/
	if(form.card_accountNumber.value == '' || (form.card_accountNumber.value.length != 16 && form.card_accountNumber.value.length != 15)){
		form.card_accountNumber.className = 'tberror';
		noerror=false;
	}
	else{
		form.card_accountNumber.className = '';
	}
	if(form.card_cvNumber.value == '' || (form.card_cvNumber.value.length != 3 && form.card_cvNumber.value.length != 4)){
		form.card_cvNumber.className = 'tberror';
		noerror=false;
	}
	else{
		form.card_cvNumber.className = '';
	}
try{
	if(form.billTo_email.className.indexOf('require', 0)==0){
		if(form.billTo_phoneNumber.value == ''){
			form.billTo_phoneNumber.className = 'require tberror';
			noerror=false;
		}
		else{
			form.billTo_phoneNumber.className = 'require';
		}
		if(form.billTo_email.value == '' || form.billTo_email.value.lastIndexOf('.') <= form.billTo_email.value.lastIndexOf('@')){
			form.billTo_email.className = 'require tberror';
			noerror=false;
		}
		else{
			form.billTo_email.className = 'require';
		}
	}
}
catch(e){}
	if(!noerror){
		document.getElementById('ct_error').innerHTML = 'Please correct the highlighted fields.';
		alert('Please correct the highlighted fields.');
	}
	
	return noerror;	
}