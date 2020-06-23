$(document).ready(function() {
	
	///////////////////////////
	//////////////////////////
var roomList=[];

var currentMonday = DateToFormat("dd-mm-yy",getMonday(new Date()));


//Hide the last row of the table of slots
$('#rowDay7').hide();


//ROOMS


function styleImportedSVG (thisfloor) {
  d3.select(thisfloor).selectAll(".troom")
  .style('pointer-events','fill')
   .each(function () {
   	var idSvg=d3.select(this).attr('data-room');
   	d3.select('[id="'+idSvg+'"]').classed("clickable", true);
   	showDataRow(d3.select(this));
    });
    d3.selectAll("text").style('pointer-events','none');
    
	d3.selectAll(".clickable").on('click', function() {
       	var idSvg=d3.select(this).attr('id');
       	//console.log($('#rowroom'+idSvg).position());
			//$('#rowroom'+idSvg).closest('.rightCol').animate({ scrollTop: $('#rowroom'+idSvg).position().top}, 500);
			$('#rowroom'+idSvg).get(0).scrollIntoView();
			highlightElem('#rowroom'+idSvg);
       })    
    
}



var showDataRow = function(row) {
			var day= FormatToDate('dd-mm-yy', $('input[name="date"]').val()).getDay();
      	var room={'idSvg':row.attr('data-room'),'id_room':row.attr('data-id_room'),'officeName':row.attr('data-officeName'),'places':row.attr('data-places'),'max':row.attr('data-max')};
			$('#room').val(room.id_room).attr('data-places',room.places).attr('data-max',room.max);
			roomOccupation(800,room,"#occupation"+room.id_room+' td','#warningOvercrowded',day,true,false);         
}
       



//import maps
var displayOccupation=function(){
	$(".innermap").each(function () {
		var file=$(this).data('map');
		var floor=$(this).data('floor');
		var building=$(this).data('building');
		var that=$(this);
      d3.xml("maps/"+file,  function(xml) {
      var importedNode = document.importNode(xml.documentElement, true);
      d3.select(that[0])
        .each(function() {this.appendChild(importedNode);})
      //Requesting the rooms for this floor to make them clickable    
		$('input[name="date"]').on("change",function(){
			$('#warningOvercrowded').html('');	
			sendSlotSchedule('#floor'+building+floor);
		})   
			$('#warningOvercrowded').html('');	   
      	sendSlotSchedule('#floor'+building+floor);
			//Styling must be done here !
      });	      
	});
}	


displayOccupation();	
	
//LOAD EXISTING SLOTS for the full week

var sendSlotSchedule = function (refZone) {
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
   	 	var res=JSON.parse(res.trim());
			slotList=res;
			styleImportedSVG (refZone);				
   		}
 		 });	
}//end of function 

sendSlotSchedule();



	


});