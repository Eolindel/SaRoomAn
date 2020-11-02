$(document).ready(function() {
	
//Hide the last row of the table of slots
$('#rowDay7').hide();

var sendSlotSchedule = function (first = false) {
	
	var inputDate=FormatToDate ("dd-mm-yy", $( 'input[name="date"]' ).val());
 	//Finding Monday of the selected week
 	var monday=getMonday(inputDate);
	//Creating an array containing the date of each day
 	var dateOfDays=[];
 	for(var j=0;j<7;j++){
 		dateOfDays.push(DateToFormat("dd-mm-yy",addDays(monday,j-1)));
 		$('#day'+j).attr('data-date',dateOfDays[j]);
		d3.select('#dateDay'+j).text(dateOfDays[j]);
 	}
	if(currentMonday!==DateToFormat("dd-mm-yy",monday) || first===true){
		slotList=[];
		$('.LinesDays').remove();
		$('.weekuser').remove();
		currentMonday=DateToFormat("dd-mm-yy",monday);
		
       $.ajax({
		    url: 'includes/send_slotScheduleNamesTeam.php',
   	 method: 'POST',
   	 data: {'date':$('#date').val()},
   	 success: function(res) {
   	 	var res=JSON.parse(res.trim());
			slotList=res;
			console.log(' slotList length '+slotList.length);
   	 	//deleting everything if the week is changed
			var TablePerson = SchedulePerson(slotList);
				fullOccupation("#building",'');   
   		}
   		
 		 });		
	} 	
}//end of function 


//import maps
displayOccupation(sendSlotSchedule);

$('#date2').val($('#date').val());

$('#date').on("change",function(){
	$('#date2').val($('#date').val());
})

});