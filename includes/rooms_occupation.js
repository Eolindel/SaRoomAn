$(document).ready(function() {
	
	///////////////////////////
	//////////////////////////
var roomList=[];

rights=allowedRights('#room');

//Hide the last row of the table of slots
$('#rowDay7').hide();


//ROOMS


$('input[name="date"]').on("change",function(){
	$('#warningOvercrowded').html('');	
	sendSlotSchedule();
}) 


//display teh occupation of the room.
var showDataRow = function() {
			var day= FormatToDate('dd-mm-yy', $('input[name="date"]').val()).getDay();
      	var room={'idSvg':$('#room').attr('data-room'),'id_room':$('#room').attr('data-id_room'),'officeName':$('#room').attr('data-officeName'),'places':$('#room').attr('data-places'),'max':$('#room').attr('data-max')};
			roomOccupation(800,room,"#occupation",'#warningOvercrowded',day,true,false);         
}
       


//LOAD EXISTING SLOTS for the full week

var sendSlotSchedule = function () {
       $.ajax({
		    url: 'includes/send_slotScheduleRoom.php',
   	 method: 'POST',
   	 data: {'date':$('#date').val(),'id_room':$('#room').attr('data-id_room')},
  			//contentType: "application/json;charset=UTF-8",
   	 success: function(res) {
   	 	var inputDate=FormatToDate ("dd-mm-yy", $( 'input[name="date"]' ).val());
   	 	//Finding Monday of the selected week
   	 	var monday=getMonday(inputDate);
			//Creating an array containing the date of each day
   	 	var dateOfDays=[];
   	 	//trick to keep monday as the day with indix 1;
   	 	dateOfDays.push(monday);
   	 	for(var j=1;j<7;j++){
 				dateOfDays.push(DateToFormat("dd-mm-yy",addDays(monday,j-1)));
   	 		$('#day'+j).attr('data-date',dateOfDays[j]);
				d3.select('#dateDay'+j).text(dateOfDays[j]);
   	 	}
   	 	//deleting everything if the week is changed
   	 	
			if(currentMonday!==DateToFormat("dd-mm-yy",monday) ){
				d3.selectAll('.existingSlot').remove();
				slotList=[];
				$('.rowslots').remove();
				currentMonday=DateToFormat("dd-mm-yy",monday);
			}
   	 	var res=JSON.parse(res.trim());
				slotList=res;	
			for(var j=0;j<slotList.length;j++){
				var deleteAllowed = false;
				if(rights >= slotList[j].valid){
					deleteAllowed = true
				}
				addSlotToTableRoom(slotList[j],deleteAllowed);
			}	
			showDataRow();			
   		}
   		

 		 });	
}//end of function 

sendSlotSchedule();



///SLOTS MANAGEMENT


	
	
	//////////////////////////
	//////////////////////////

	


});