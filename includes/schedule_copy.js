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
	 data: {'week':tempweek[1],'year':tempweek[0],'id_user':$('#displayWeek').data('iduser')},
  			//contentType: "application/json;charset=UTF-8",
	 success: function(res) {
	 	var count=JSON.parse(res.trim());
	 	if(parseInt(count.nbSlots,10)===0){
	 		$('#week'+count.year+count.week).html('<img src="images/icones/ok.png" alt="OK"> <span class="nowarning">No slots programmed for this week</span>');
	 		$('#addedInputs').append('<input name="week'+innerIndix+'" id="week'+innerIndix+'" value="'+tempweek[0]+','+tempweek[1]+'" type="hidden"><input name="day'+innerIndix+'" id="day'+innerIndix+'" value="'+DateToFormat("dd-mm-yy",getDateOfISOWeek(tempweek[1],tempweek[0]))+'" type="hidden">');
	 	}else{
	 		$('#week'+count.year+count.week).html('<img src="images/icones/no.png" alt="NO"> <span class="warning">There are already some slots ('+count.nbSlots+') planned for this week</span>');
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
//initialize the current week
$('#currentweek').html(getWeekNumber(FormatToDate("dd-mm-yy",$('#date').val() ) )[1]);    
$('#date').prop('disabled',true);    



var weekSchedule=d3.select('#displayWeek').append('svg');

weekSchedule.attr("width", dims.width)
   .attr("height", dims.height)
   .attr("viewBox", [0, 0, dims.width, dims.height])
   .style("overflow", "visible")
   .style("display", "block");

var displayZone = weekSchedule.append('g')
		.attr('transform', 'translate(0,20)');

var scheduleZone = displayZone.append('g')
		.attr('transform', 'translate('+dayDims.width+',0)');

	
		
//Adding vertical bars as label
for(var j=sliderHour.min;j<=sliderHour.max;j+=15){
	
	if(j%60===0){
	scheduleZone.append('text')
		.attr("font-size","0.8em")
		.attr("x",(j-sliderHour.min))
		.attr("y",'-5px')
		.attr("text-anchor", "middle")
		.text(minToHoursOnly(j));	
	scheduleZone.append('path')
		.attr('d','M'+(j-sliderHour.min)+' 0 V '+(6*dayDims.height))
		.attr('stroke','#b3b3b3')
		.attr('stroke-width','2px');		
	}else{
	scheduleZone.append('path')
		.attr('d','M'+(j-sliderHour.min)+' 0 V '+(6*dayDims.height))
		.attr('stroke','#b3b3b3');	
	}

}	

		
//adding each day and make them clickable
for (var j=1;j<7;j++) {
	var day=displayZone.append('g')
		.attr("data-day",j)
		.attr("class","weekDay")
		.attr('transform', 'translate(0,'+((j-1)*45)+')')
	day.append('rect')
		.attr('width',dayDims.width+'px')
		.attr('height',dayDims.height+'px')
		.attr('fill',paletteDays[j]);
	day.append('text')
		.attr("x",20)
		.attr("y",'18px')
		.classed('nameDays',true)
		.text(weekDays[j]);
	day.append('text')
		.attr("x",20)
		.attr('id','dateDay'+j)
		.attr("y",'37px')
		.attr("text-anchor", "left")
		.attr("font-size","0.8em")
		.text();	
}



var sendSlotSchedule = function () {
       $.ajax({
		    url: 'includes/send_slotSchedule.php',
   	 method: 'POST',
   	 data: {'date':$('#date').val()},
  			//contentType: "application/json;charset=UTF-8",
   	 success: function(res) {
   	 	var inputDate=FormatToDate ("dd-mm-yy", $( 'input[name="date"]' ).val());
   	 	//Finding Monday of the selected week
   	 	var monday=getMonday(inputDate);
			//Creating an array containing the date of each day
   	 	var dateOfDays=[];
   	 	//trick to keep monday as the day with indix 1;
   	 	dateOfDays.push(monday);
   	 	//deleting everything if the week is changed
   	 	
			if(currentMonday!==DateToFormat("dd-mm-yy",monday) ){
				d3.selectAll('.existingSlot').remove();
				slotList=[];
				currentMonday=DateToFormat("dd-mm-yy",monday);
			}
   	 	slotList=JSON.parse(res.trim());
				for (var k=0;k<slotList.length;k++) {
					//Adding the slot to the table of slots
					if(parseInt(slotList[k].ref_user,10)===parseInt($('#displayWeek').data('iduser'),10)){
						addSlotToSchedule(slotList[k],scheduleZone,office,workplace);}
				}
   		}
 		 });	
}//end of function 

sendSlotSchedule();
});