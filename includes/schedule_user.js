$(document).ready(function() {

//Hide the last row of the table of slots
$('#rowDay7').hide();

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

addBars(dayDims,scheduleZone,sliderHour);	
		
addDaysRectangles(displayZone,paletteDays,weekDays);	

//ROOMS




//trigger room change if room clicked on map
var updateRoom = function (inData,context) {
	var room=inData;
	var innerContext=context;
 	d3.select(innerContext).select('svg').select('[id="'+room.idSvg+'"]')
 		.style('pointer-events','fill')
 		.style('fill',paletteRooms[2])
		.style('fill-opacity',0.3);
	//styling the office if on the map 		
   if(office.idSvg){
   	d3.select(innerContext).select('svg').select('[id="'+office.idSvg+'"]')
   		.style('fill',paletteRooms[0])
			.style('fill-opacity',0.7);
   }	
	//styling the workplace if on the map
   if(workplace.idSvg){
   	d3.select(innerContext).select('svg').select('[id="'+workplace.idSvg+'"]')
   		.style('fill',paletteRooms[1])
   		.style('fill-opacity',0.7);
   }     	 		
}




$('.floors').on("change",function(){
		if($(this).is(':checked')){
			$('div[data-map="'+$(this).attr('value')+'"]').show();
		}else{
			$('div[data-map="'+$(this).attr('value')+'"]').hide();
		}
})

var roomList=[]

//import maps
	$(".mapDisplay").each(function () {
		var file=$(this).data('map');
		var floor=$(this).data('floor');
		var building=$(this).data('building');
		var that=$(this);
      d3.xml("maps/"+file,  function(xml) {
      var importedNode = document.importNode(xml.documentElement, true);
      d3.select(that[0])
        .each(function() {
          this.appendChild(importedNode);
        })
        
      //Disable text
      d3.selectAll("text").style('pointer-events','none')   
      //Requesting the rooms for this floor to make them clickable    
        
       $.ajax({
		    url: 'includes/send_rooms.php',
   	 method: 'POST',
   	 data: {
  			"floor" : floor,
  			"building" : building},
  			//contentType: "application/json;charset=UTF-8",
   	 success: function(res) {
   	 	var res=JSON.parse(res.trim());
   	 	roomList=res;
   	 	for(var k=0;k<res.length;k++){
   	 		updateRoom(res[k],that[0]);
   	 	}

   		}
 		 });        
			//Styling must be done here !
      });	      
	});
		
//LOAD EXISTING SLOTS for the full week

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
   	 	slotList=JSON.parse(res.trim());
				for (var k=0;k<slotList.length;k++) {
					//Adding the slot to the table of slots
					if(parseInt(slotList[k].ref_user,10)===parseInt($('#displayWeek').data('iduser'),10)){
						addSlotToTable(slotList[k]);	
						addSlotToSchedule(slotList[k],scheduleZone,office,workplace);}
				}
   		}
 		 });	
}//end of function 

sendSlotSchedule();

$('input[name="date"]').on("change",function(){
	sendSlotSchedule();
})

///SLOTS MANAGEMENT


		
	
	
});