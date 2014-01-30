function popupcal(opts){
	this.d = new Date();
	this.month = (this.d.getMonth());
	this.year = (this.d.getYear());
	
	if(this.year < 2000)
		this.year += 1900;

	this.months = ['January', 'Febuary', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];


	this.days_of_month_regular = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

	this.days_of_month_leap = [ 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

	this.days_of_month = [ this.days_of_month_regular, this.days_of_month_leap];

	this.cum_days_of_month_regular = [ 0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334, 365];

	this.cum_days_of_month_leap = [ 0, 31, 60, 91, 121, 152, 182, 213, 244, 274, 305, 335, 366];

	this.cum_days_of_month = [ this.cum_days_of_month_regular, this.cum_days_of_month_leap];

	this.init_j_2000 = 6;

	this.display_month = [26, 27, 28, 29, 30, 31, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 1, 2, 3, 4, 5];
	this.userdate = new Date();
	this.current_month = this.userdate.getMonth();
	this.current_day = this.userdate.getDate();
	this.current_year = this.userdate.getFullYear();

	this.mousex =0;
	this.mousey =0;

	this.id=0;
	
	this.set_mouse_coords = mouseCoords;
	this.show_cal = make_month_array;
	this.create_cal = create_cal;
	this.save_date = opts.callback;
}


 function mouseCoords(e){
	var xpos;
	var ypos;
	if (!e) var e = window.event;

	if (e.pageX || e.pageY)
	{
		xpos = e.pageX;
		ypos = e.pageY;
	}
	else if (e.clientX || e.clientY)
	{
		xpos = e.clientX;
		ypos = e.clientY;
		if (document.body.scrollLeft || document.body.scrollTop)
		{
			xpos += document.body.scrollLeft;
			ypos += document.body.scrollTop;
		}
		else if (document.documentElement.scrollLeft || document.documentElement.scrollTop)
		{
			xpos += document.documentElement.scrollLeft;
			ypos += document.documentElement.scrollTop;
		}
	}
	mousex = xpos;
	mousey = ypos;
	return {x:xpos, y:ypos};
}


function make_month_array(e, which_one){
	this.set_mouse_coords(e);
id=which_one;

if(this.year==1999 && this.month==11){
	this.year=2000;
	this.month=0;
}

var years_since = this.year-2000;


var leap_adj = parseInt((years_since-1)/4)+1;


if(this.year==2000){

	leap_adj=0;

}
	

	


if((this.month-1)<0){

	var month_before = 11;

}

else{

	var month_before = this.month-1;

}


if((this.year%4)==0){

	var leap_year = 1;

}

else{

	var leap_year = 0;

}



if(((this.year-1)%4)==0){

	var year_after_leap = 1;

}

else{

	var year_after_leap = 0;

}



var init_day = (this.init_j_2000 + years_since*365 + leap_adj + this.cum_days_of_month[leap_year][this.month])%7;


for(i=(init_day-1); i>=0; i--){

	this.display_month[i] = '<td style="color:#666666">'+(this.days_of_month[year_after_leap][month_before]-(init_day-i-1))
+'</td>';
}


var d=1;


for(i=init_day; i < (init_day+this.days_of_month[leap_year][this.month]); i++){
	this.display_month[i] = '<td onmouseover="this.style.backgroundColor = \'#0066cc\';" onmouseout="this.style.backgroundColor = \'\';" onclick="save_date('+d+');">'+(d++)+'</td>';

	//display_month[i] = '<td onmouseover="this.style.color = \'#0066cc\'; this.style.textDecoration=\'underline\';" onmouseout="this.style.color=\'#ffffff\'; this.style.textDecoration=\'none\';" onclick="save_date();"><a href="#" style="text-decoration:none;">'+(d++)+'</a></td>';

}


d=1;

for(i=i; i < 42; i++){

	this.display_month[i] = '<td style="color:#666666">'+(d++)+'</td>';

	
}

if(this.month==this.current_month && this.year==this.current_year){
	
	this.display_month[init_day+this.current_day-1] = '<td onmouseover="this.style.backgroundColor = \'#0066cc\';" onmouseout="this.style.backgroundColor = \'\';" onclick="save_date('+this.current_day+');"><u>'+(this.current_day)+'</u></td>';


}


this.create_cal();


return true;

}



function create_cal(){


//var mousePos = mouseCoords();

var cal = '<div style="padding-top:15px; color:#fff;"><div style="width:210px;" ><div style="float:left; width:20%;"><input type="button"  value="<" onclick="month = (--month+12)%12; if(month==11){year--;} make_month_array('+id+');" /></div><div style="float:left; width:60%;"><strong>'+this.months[this.month]+' '+this.year+'</strong></div><div style="float:left; width:20%;"><input type="button" value=">" onclick="month=++month%12; if(month==0){year++;} make_month_array('+id+');" /></div></div>';

 cal +='<table align="center" style="clear:both; padding-top:10px; cursor:pointer; text-align:center;">';

cal+=			'<tr>';

cal+=				'<td width="70px">Sun</td>';

cal+=				'<td width="70px">Mon</td>';

cal+=				'<td width="70px">Tue</td>';

cal+=				'<td width="70px">Wed</td>';

cal+=				'<td width="70px">Thu</td>'
;
cal+=				'<td width="70px">Fri</td>'
;
cal+=				'<td width="70px">Sat</td>';

cal+=			'</tr>';
var x=0;

for(i=0; i<6; i++){

	cal += '<tr>';

	cal +=		this.display_month[x++];

	cal +=		this.display_month[x++];

	cal +=		this.display_month[x++];

	cal +=		this.display_month[x++];

	cal +=		this.display_month[x++];

	cal +=		this.display_month[x++];

	cal +=		this.display_month[x++];

	cal +=	'</tr>';

}

cal += '</table><div align="center" style="cursor:pointer;" onclick="document.getElementById(\'caldiv\').style.display = \'none\';">close</div></div>';

document.getElementById('caldiv').innerHTML = cal;

if(document.getElementById('caldiv').style.display == 'none'){
document.getElementById('caldiv').style.top = mousey + 5 + "px";
document.getElementById('caldiv').style.left = mousex + 5 + "px";
}
document.getElementById('caldiv').style.display = 'block';




return true;


}





function save_date(day){
document.getElementById('caldiv').style.display = 'none';
document.getElementById('display_date'+id).innerHTML = months[month]+" "+day+", "+year;

$.post('/intra_new/hiring/interview', {'id':id, 'date': year+"-"+(month+1)+"-"+day});


return true;
}// JavaScript Document