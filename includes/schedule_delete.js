$(document).ready(function() {



//Hide the last row of the table of slots and the loading circles
$('#rowDay7').hide();
$('#loadingStartDate').hide();
$('#loadingEndDate').hide();


initializeDate('#startdate');
initializeDate('#enddate');


function getDateOfISOWeek(w, y) {
    var simple = new Date(Date.UTC(y, 0, 1 + (w - 1) * 7));
    var dow = simple.getDay();
    var ISOweekStart = simple;
    if (dow <= 4)
        ISOweekStart.setDate(simple.getDate() - simple.getDay() + 1);
    else
        ISOweekStart.setDate(simple.getDate() + 8 - simple.getDay());
    return ISOweekStart;
}

var queryWeek=function(j,tempweek){
	$('#week'+tempweek[0]+tempweek[1]).html('<img src="images/ajax-loader.gif" id="loadingDate">');
	var innerIndix=j;
	$.ajax({
	    url: 'includes/send_countSlotsWeek.php',
	 method: 'POST',
	 data: {'week':tempweek[1],'year':tempweek[0],'id_user':$('#id_user').val()},
  			//contentType: "application/json;charset=UTF-8",
	 success: function(res) {
	 	var count=JSON.parse(res.trim());
	 	if(parseInt(count.nbSlots,10)===0){
	 		$('#week'+count.year+count.week).html('<img src="images/icones/ok.png" alt="OK"> <span class="nowarning">No slots programmed for this week, nothing will be deleted</span>');
	 	}else{
	 		$('#week'+count.year+count.week).html('<img src="images/icones/no.png" alt="NO"> <span class="warning">You are going to delete <em>'+count.nbSlots+'</em> slots on this week</span>');
			$('#addedInputs').append('<input name="week'+innerIndix+'" id="week'+innerIndix+'" value="'+tempweek[0]+','+tempweek[1]+'" type="hidden">');	 		
	 	}
	 }//end success
   })			
}


var updateWeeks=function(){

	var startDate=FormatToDate("dd-mm-yy",$('#startdate').val() ) ;
	var endDate=FormatToDate("dd-mm-yy",$('#enddate').val() );	
	var kindWeeks=$('input[name="kindsWeek"]:checked').val();
	var tempDate;
	var tempweek;
	var tempInter;


	//changing the order if start and end are inverted
	if(startDate>endDate){
		tempDate=new Date(endDate.getTime());
		endDate= new Date(startDate.getTime());
		startDate = new Date(tempDate.getTime());
		tempInter=$('#enddate').val();
		$('#enddate').val($('#startdate').val());
		$('#startdate').val(tempInter);
	}	
	
	var startWeek=getWeekNumber(FormatToDate("dd-mm-yy",$('#startdate').val() ) );
	var endWeek=getWeekNumber(FormatToDate("dd-mm-yy",$('#enddate').val() ));	
	$('#startweek').html(startWeek[1]);
	$('#endweek').html(endWeek[1]);		
	$('.rowWeek').remove();
	$('#addedInputs').html('');
	tempDate=new Date(startDate.getTime());
	var j=0;
	while(tempDate<=endDate){
		tempweek=getWeekNumber(tempDate);
		var thisMonday = getDateOfISOWeek(tempweek[1],tempweek[0]);
		var thisSunday = addDays(thisMonday, 6);	
		if(kindWeeks==='All' || (kindWeeks==='Odd' && tempweek[1]%2===0)||  (kindWeeks==='Even' && tempweek[1]%2===1)  ){
			$('#weekList').append('<tr class="rowWeek" data-week="'+tempweek[1]+'"><td>'+tempweek[1]+'</td><td>'+DateToFormat("dd-mm-yy",thisMonday)+'</td><td>'+DateToFormat("dd-mm-yy", thisSunday )+'</td><td id="week'+tempweek[0]+tempweek[1]+'" class="left"></td></tr>');
		}

		queryWeek(j,tempweek);
		tempDate = addDays(tempDate, 7);
		j++;
	}
	$('#addedInputs').append('<input name="total" value="'+j+'" id="total" type="hidden">');
	
}

$('#startdate,#enddate,input[name="kindsWeek"]').on("change",updateWeeks)

updateWeeks();
   



});