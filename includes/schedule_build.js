$(document).ready(function() {



//Make duplicate of form for the deletion of the slots of the week
var copyFormDelete = function(){
	$('#date2').val($('#date').val()).trigger("change");
	$('#id_user2').val(parseInt($('#id_user').val(),10)).trigger("change");
	
}
copyFormDelete();

$('#date,#id_user').on("change",copyFormDelete)

var updateRoomTable = function(targetTable,room){
	if($('.roomName',targetTable).text()!==room.officeName && $('.roomName',targetTable).text()!==room.officeName ){
		$('.roomName',targetTable).text(room.officeName);
		$('.rowslots',targetTable).remove();
		var slotListRoom = slotList.filter(function(obj){return parseInt(obj.ref_room,10)===parseInt(room.id_room,10)});
		for(var j=0;j<slotListRoom.length;j++){
			addSlotToTableRoom(slotListRoom[j],false,targetTable);
		}
	}
}

var showSendSlot=function () {//Show or hide the submit button and control the display of the occupation of the room

	var day=parseInt($('input[name="day"]:checked').val(),10);
	//updating the height of the slot
	updateRoomSchedule();
	//if both rooms are selected
	if($('#both').is(':checked')){
			var room1 = roomList.filter(function(obj){return parseInt(obj.id_room,10)===office.ref_room;} )[0];	
			var room2 = roomList.filter(function(obj){return parseInt(obj.id_room,10)===workplace.ref_room;} )[0];	
			updateRoomTable('#tableRoom',room1);		
			updateRoomTable('#tableRoom2',room2);	
			if(!isNaN(day) && day!==0 ){
				$('#sendSlot').show();
				checkOverlaps();
				//Displaying the occupation of the room
				var day= $('input[name="day"]:checked').val();
				var slot=makeSubmit();		
				roomOccupation(sliderHour.max-sliderHour.min+dayDims.width+5,room1,"#occupationRoom",'',day,false,slot);	
				roomOccupation(sliderHour.max-sliderHour.min+dayDims.width+5,room2,"#occupationRoom2",'',day,false,slot);	
			}else{$('#sendSlot').hide();}		
		
	}else{
		var roomId=parseInt($('#room').val(),10);
		if(!isNaN(roomId) && roomId!==0){//updating the slots of the room
			var room = roomList.filter(function(obj){return parseInt(obj.id_room,10)===roomId;} )[0];
			updateRoomTable('#tableRoom',room);
			//If day also given
			if(!isNaN(day) && day!==0 ){
				$('#sendSlot').show();
				checkOverlaps();	
				//Displaying the occupation of the room
				var day= $('input[name="day"]:checked').val();
				var slot=makeSubmit();		
				roomOccupation(sliderHour.max-sliderHour.min+dayDims.width+5,room,"#occupationRoom",'',day,false,slot);	
			}else{$('#sendSlot').hide();}
		}	
	}

	
	
	
}	

//Hide the last row of the table of slots and the loading circles
$('#rowDay7').hide();
$('#loadingDate').hide();
$('#loadingSlot').hide();


//Updating the slot when one of the sliders is changed		
var updateEnd = function(start,slotLength){
	var start=parseInt(d3.select('#value-hour').attr('data-value'),10);
	var slotLength=parseInt(d3.select('#value-length').attr('data-value'),10);	 
	if(start+slotLength > sliderHour.max){
		slotLength=Math.max(15,parseInt(sliderHour.max,10)-start);
		Length.value(slotLength);
		d3.select('#value-length').text(minToHours(slotLength)).attr('data-value',slotLength);
	}
	if(start+slotLength > sliderHour.max){
		start=sliderHour.max-slotLength;
		Hours.value(sliderHour.max-slotLength);
		d3.select('#value-hour').text(minToHours(start)).attr('data-value',start);
	}	
	
	d3.select('#value-end').text(minToHours(start+slotLength));
	d3.select('#slot').select('text').attr('opacity',1); 
	if (start && slotLength && d3.select('input[name="day"]:checked').node()) {
		slotDay=d3.select('input[name="day"]:checked').node().value;
		d3.select('#slot').attr('transform', 'translate('+(start-sliderHour.min)+','+(slotDay-1)*dayDims.height+')');
		d3.select('#slot').select('rect').attr('width',slotLength);
		//console.log('test');
		showSendSlot();
	}	
}

var updateRoomSchedule = function () {
		roomName=$('#hroom').text();
		if($('#both').is(':checked')){
			d3.select('#slot').select('rect').attr('height',dayDims.height*2/3).attr('y','0');
			d3.select('#slot').select('text').text(roomName).attr('y','12').attr('opacity',1);
		}else	if(roomName===office.human){
			d3.select('#slot').select('rect').attr('height',dayDims.height/3).attr('y','0');
			d3.select('#slot').select('text').text(roomName).attr('y','12').attr('opacity',1);
		}else if(roomName===workplace.human){
			d3.select('#slot').select('rect').attr('height',dayDims.height/3).attr('y','15');
			d3.select('#slot').select('text').text(roomName).attr('y','27').attr('opacity',1);
		}else{
			d3.select('#slot').select('rect').attr('height',dayDims.height/3).attr('y','30');
			d3.select('#slot').select('text').text(roomName).attr('y','42').attr('opacity',1);
		}
}

//updating the schedule when the room is changed
$('#room').on("change",	showSendSlot)

$('input[name="day"]').on('change',showSendSlot);



//sliders configuration
var sliderHour = {'min':420,'max':1260,'step':15,'default':420,'ticks':6,'output':'#value-hour','input':'#slider-hour','update':updateEnd};
var sliderLength = {'min':15,'max':720,'step':15,'default':360,'ticks':9,'output':'#value-length','input':'#slider-length','update':updateEnd};





//Both sliders
var Hours=sliderStep(sliderHour);
var Length = sliderStep(sliderLength);

$('input[name="day"]').on("change",	updateEnd);





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
//Make days rectangles clickable
d3.selectAll(".weekDay")
.on('click', function() {
	if($('input[name="day"]').length){
		var indixDay=d3.select(this).attr('data-day');
		$('input[name="day"]:checked').prop('checked', false);
   	$('[id="day'+indixDay+'"]').prop('checked', true);
   	$('input[name="day"]').filter(':checked').trigger('change');
   	//updateEnd();				
	}
 });



//SLOT
//SLOT	
//add the slot that we're adding
var addSlotSchedule=function () {
var slot=scheduleZone.append('g')
		.attr("id","slot")
		.attr('transform', 'translate(0,0)');
	slot.append('rect')
		.attr('width','0')
		.attr("fill",'#e31a1c')
		.attr('height',dayDims.height);	
	slot.append('text')
		.attr('y','12px')
		.attr('x','3px')
		.attr('font-size','0.8em')
		.attr('opacity',0);
}	

addSlotSchedule();	
//SLOT
//SLOT



//DELETE Schedule


//ROOMS

//hide the ref of the room
$('#room').hide();
$('#tableRoom2').hide();

//If a shortcut is used, do not check the checkbox to avoid any confusion
$('input[name="shortRoom"]').on('change',function(){
	$(this).prop('checked', false);
}).hide();


$('label[for="shortRoomoffice"]').addClass('officeRelated');
$('label[for="shortRoomworkplace"]').addClass('workplaceRelated');

//trigger room change if room clicked on map
var updateRoom = function (inData,context) {
	var room=inData;
	var innerContext=context;
   	 		d3.select(innerContext).select('svg').select('[id="'+room.idSvg+'"]')
   	 		.style('pointer-events','fill')
   	 		.style('fill',paletteRooms[2])
      		.style('fill-opacity',0.3)
   	 		.on("click",function(){
					$('#hroom').text(room.officeName);
   	 			$('#room').val(room.id_room);//.attr('data-places',room.places).attr('data-max',room.max);	
   	 			$('input[name="room"]').trigger('change');	
				
   	 		})
   	//styling the office if on the map 		
      if(office.idSvg){
      	d3.select(innerContext).select('svg').select('[id="'+office.idSvg+'"]')
      		.classed('highlightedOffice',true)
      		.style('fill',paletteRooms[0])
				.style('fill-opacity',0.7);
      }	
		//styling the workplace if on the map
      if(workplace.idSvg){
      	d3.select(innerContext).select('svg').select('[id="'+workplace.idSvg+'"]')
      		.classed('highlightedWorkplace',true)
      		.style('fill',paletteRooms[1])
      		.style('fill-opacity',0.7);
      }     	 		
}



//Hiding or not the maps if the checkboxes are changed 
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
   	 	roomList=roomList.concat(res);
   	 	for(var k=0;k<roomList.length;k++){
   	 		updateRoom(roomList[k],that[0]);
   	 	}
   		 //trigger color change if shortcut is used
			$('input[value="workplace"],input[value="office"]').on("change",function(){
					var shortcutRoom=$(this).val();
					//The room corresponding to the checked one
					var innerRoom=roomList.filter(function(obj){return parseInt(obj.id_room,10)===parseInt($('#'+shortcutRoom).data('id'),10)})[0];
					$('#hroom').text($('#'+shortcutRoom).data('human'));		
					$('#room').val($('#'+shortcutRoom).data('id'));//.attr('data-places',innerRoom.places ).attr('data-max',innerRoom.max);
					$('input[name="room"]').trigger('change');	
			})
   		}
 		 });   //end ajax     
      });	      
	});
	
	


$('#sendSlot').hide();
	




///EDIT SLOTS
///EDIT SLOTS


var ediSlotToSchedule =function(slot,scheduleZone){
	//remove the slot from the slotlist to avoid wrong overlaps
	slotList.splice(slotList.findIndex(function(obj){ return parseInt(obj.id_slot,10) ===parseInt(slot.id_slot,10);}),1);	

	//if previous slot was in editable mode, we reset it to a normal slot
	var prevSlot=d3.select('.edited').classed('edited','false');
	prevSlot.select('rect')
		.attr('fill-opacity',1);
	prevSlot.select('text')
		.attr('opacity',1);
		
	//Make the old slot appear as a ghost	
	var oldSlot=d3.select('#slot'+slot.id_slot).classed('edited','true');
	oldSlot.select('rect')
		.attr('fill-opacity',0.3);
	oldSlot.select('text')
		.attr('opacity',0.3);
	//Withdrawing the possibility to change both rooms	
	if(BothRoomsVisible === 1){
		$('#both').prop('checked',false).trigger('change').hide();
		$('label[for="both"]').hide();
		BothRoomsVisible = 0;	
		$('#tableRoom2').hide();
	}

	//Edit the day so that it updates when changed
	$('input[name="day"]:checked').prop('checked', false);
   $('[id="day'+parseInt(slot.day,10)+'"]').prop('checked', true);
   $('input[name="day"]').trigger("change");		
   //update the room	and times
	Hours.value(parseInt(slot.start,10));
	Length.value(parseInt(slot.length,10));
	d3.select('#value-hour').attr('data-value',parseInt(slot.start,10));
	d3.select('#value-length').attr('data-value',parseInt(slot.length,10))
	
	
	//console.log(Hours.value());
	var innerRoom=roomList.filter(function(obj){return obj.id_room==slot.ref_room;})[0];
	$('#room').val(slot.ref_room);//.attr('data-places',innerRoom.places ).attr('data-max',innerRoom.max);	
	$('#hroom').text(slot.room);	

	//update the submit button	
	$('#sendSlot').text('Edit this Slot').data('edit',1).data('slot',slot.id_slot);		

	d3.select('#slot').remove();
	addSlotSchedule();	
	
	//update the current slot
	var slotSchedule=d3.select('#slot')
			.attr('data-id',slot.id_slot)
			.attr('transform', 'translate('+(parseInt(slot.start,10)-parseInt(sliderHour.min,10))+','+(parseInt(slot.day,10)-1)*dayDims.height+')');	
		slotSchedule.select('rect')
			.attr('width',parseInt(slot.length,10))
			.attr("fill",'#e31a1c')
			.attr('height',dayDims.height/3);	
		slotSchedule.select('text')
			.attr('y','12px')
			.attr('x','3px')
			.attr('font-size','0.8em')
			.attr('opacity',1)
			.text(slot.room);
		updateRoomSchedule();
}


var slotEditable=function (){
	$('.editSlot').on("click",function(){ 

		var id = $(this).data('id');
		//console.log('id'+id);
		//console.log(slotList);	
		var slot=slotList.filter(function(obj){return  parseInt(id,10)===parseInt(obj.id_slot,10);} )[0];
		//removing the slot from the slotlist to avoid comparison when checking for overlap
		//console.log('bb');
		//console.log(slot);
		ediSlotToSchedule(slot,scheduleZone);			
	})	
}
//LOAD EXISTING SLOTS for the full week

var sendSlotSchedule = function (office,workplace) {
	//console.log('test');
	$('#loadingDate').show();
 	var inputDate=FormatToDate ("dd-mm-yy", $( 'input[name="date"]' ).val());
 	//Finding Monday of the selected week
 	var monday=getMonday(inputDate);
	//Creating an array containing the date of each day
 	var dateOfDays=[];
 	//trick to keep monday as the day with indix 1;
 	dateOfDays.push(monday);
 	for(var j=1;j<7;j++){
 		dateOfDays.push(DateToFormat("dd-mm-yy",addDays(monday,j-1)));
 		$('input[id="day'+j+'"]').data('date',dateOfDays[j]);
 		//console.log(j+' '+dateOfDays[j]);
		d3.select('#dateDay'+j).text(dateOfDays[j]);
 	}
 	//deleting everything if the week is changed
 	
	if(currentMonday!==DateToFormat("dd-mm-yy",monday) ){
		d3.selectAll('.existingSlot').remove();
		slotList=[];
		$('.rowslots').remove();
		$('.roomName').text('');
		currentMonday=DateToFormat("dd-mm-yy",monday);
	}	
	
       $.ajax({
		    url: 'includes/send_slotSchedule.php',
   	 method: 'POST',
   	 data: {'date':$('#date').val()},
  			//contentType: "application/json;charset=UTF-8",
   	 success: function(res) {
   	 	var res=JSON.parse(res.trim());

			//console.log($( "#id_user" ).val());
			slotList = res;

			for (var k=0;k<slotList.length;k++) {
				//Adding the slot to the table of slots
				if(parseInt(slotList[k].ref_user,10)===parseInt($( "#id_user" ).val(),10)){
					addSlotToTable(slotList[k],true);	
					addSlotToSchedule(slotList[k],scheduleZone,office,workplace);	
				}
				//slotList.push(slotList[k]);				
			}
				
		slotEditable();
		
		d3.select('#slot').remove();
		//add new empty slot
		addSlotSchedule();					

		if($('#requestEdit').length){
			var slot=slotList.filter(function(obj){return  parseInt($('#requestEdit').data('id'),10)===parseInt(obj.id_slot,10);} )[0];
			//console.log('aa'+slot);
			ediSlotToSchedule(slot,scheduleZone);	
		}

	},//end function in ajax
   complete: function(){
     $('#loadingDate').hide();
   }
 });	//end of ajax
 		 
}//end of function 



//update the schedule when the date is changed
$('input[name="date"]').on("change",function(){
	//console.log('datechanged');
	sendSlotSchedule(office,workplace);
})


var BothRoomsVisible = 1 ;
//manage things when both is checked to hide/show some possibilities and prevent people from doing things 
var bothRooms = function(office,workplace){
	if($( "#id_user option:selected" ).data('widroom') !== undefined && $( "#id_user option:selected" ).data('oidroom') !== undefined ){
		BothRoomsVisible = 1;
	}else{
		BothRoomsVisible = -1;
	}

	//If checked, hiding the floors and the room shortcuts, updating the rooms
	$('#both').on("change",function(){

		if($(this).is(":checked")){
			$('label.officeRelated').hide();
			$('label.workplaceRelated').hide();
			$('div.mapDisplay').hide();
			$('#hroom').text(office.human+' + '+workplace.human);
			$('#floorsInformations').hide();
			$('#occupationRoom2').show();
			$('#tableRoom2').show();
		}else{
			$('#tableRoom2').hide();
			$('#occupationRoom2').hide();
			$('label.officeRelated').show();
			$('label.workplaceRelated').show();
			$('div.mapDisplay').show();	
			$('#hroom').text('');	
			$('#floorsInformations').show();
		}	
		showSendSlot();
	})
}





bothRooms(office,workplace);
//////////////////
///CHANGE OF USER
//////////////////
var updatingUser = function(){
	
	//resetting color of office and workplace
d3.select('.highlightedWorkplace').classed('highlightedWorkplace',false)
	.style('fill',paletteRooms[2])
   .style('fill-opacity',0.3);

d3.select('.highlightedOffice').classed('highlightedOffice',false)
	.style('fill',paletteRooms[2])
   .style('fill-opacity',0.3);
	
	//defining the new office and style it
	if($( "#id_user option:selected" ).data('oidroom') !== undefined){
		$('#office').data('human',$( "#id_user option:selected" ).data('ohuman') )
				.data('id',$( "#id_user option:selected" ).data('oidroom') )
				.data('idsvg',$( "#id_user option:selected" ).data('oidsvg') );	
		office={'human':$('#office').data('human'),'ref_room':$('#office').data('id'),'idSvg':$('#office').data('idsvg')};
		$('input[value="office"]').show();
		$('label[for="shortRoomoffice"]').text(office.human+' (Office)').show();	
		d3.selectAll('svg').select('[id="'+office.idSvg+'"]')
      		.classed('highlightedOffice',true)
      		.style('fill',paletteRooms[0])
				.style('fill-opacity',0.7);		
				
		$('#shortRoomoffice').prop('checked', true).trigger('change');
		$('#room').trigger('change');	
	}else{	
		$('input[value="office"]').hide();
		$('label[for="shortRoomoffice"]').text('').hide();	
	}
	
//updating the workplace if it exists and style it
	if($( "#id_user option:selected" ).data('widroom') !== undefined ){
		$('#workplace').data('human',$( "#id_user option:selected" ).data('whuman') )
				.data('id',$( "#id_user option:selected" ).data('widroom') )
				.data('idsvg',$( "#id_user option:selected" ).data('widsvg') );
		workplace={'human':$('#workplace').data('human'),'ref_room':$('#workplace').data('id'),'idSvg':$('#workplace').data('idsvg')};
		$('input[value="workplace"]').show();
		$('label[for="shortRoomworkplace"]').text(workplace.human+' (Workplace)').show();	
		d3.selectAll('svg').select('[id="'+workplace.idSvg+'"]')
      		.classed('highlightedWorkplace',true)
      		.style('fill',paletteRooms[1])
				.style('fill-opacity',0.7);			
		
	}else{	
		$('input[value="workplace"]').hide();
		$('label[for="shortRoomworkplace"]').text('').hide();	}		
	if($( "#id_user option:selected" ).data('widroom') !== undefined && $( "#id_user option:selected" ).data('oidroom') !== undefined ){
		$('input[name="both"]').show();
		$('input[name="both"]').prop("checked",false);
		$('label[for="both"]').show();
		bothRooms(office,workplace);
	}else{
		$('input[name="both"]').hide();	
		$('label[for="both"]').hide();
	}	
	$('input[name="both"]').prop('checked',false);
	$('#occupationRoom2').hide();
	
//hiding the checkboxes		
$('input[name="shortRoom"]').hide();		
		
//resetting the slots
		$('.rowslots').remove();
		d3.selectAll('.existingSlot').remove();		
		$('input[name="room"]').trigger('change');
		//console.log('updating user');
		sendSlotSchedule(office,workplace);	
}


$('select[name="id_user"]').on("change",updatingUser)
updatingUser();


///SLOTS MANAGEMENT


//Create an object which corresponds to what must be submitted
var makeSubmit = function(){
	var submit={'day':$('input[name="day"]:checked').val(),
					'date':$('input[name="day"]:checked').data('date'),
					'start':parseInt(Hours.value(),10),
					'length':parseInt(Length.value(),10),
					'ref_room':parseInt($('#room').val(),10),
					'ref_room2':0,
					'ref_user':parseInt($('#id_user').val(),10),
					'room':$('#hroom').text(),
					'room2':'',	
					'edit':parseInt($('#sendSlot').data('edit'),10),
					'commentaire':$('input[name="commentaire"]').val(),
					'both':false,
					}
	submit.end=submit.start+submit.length;
	if(!isNaN(parseInt($('#sendSlot').data('slot'),10))){
		submit.id_slot=parseInt($('#sendSlot').data('slot'),10);}
	if($('#both').is(":checked")){
		submit.ref_room=parseInt(office.ref_room,10);
		submit.ref_room2=parseInt(workplace.ref_room,10);
		submit.both=true;
		submit.room=$('#office').data('human');
		submit.room2=$('#workplace').data('human');
	}
	//console.log(submit);
	return submit;
}


//SEND SLOT
$('#sendSlot').on("click",function () {
		$('#loadingSlot').show();
		$('#sendSlot').hide();
		submit=makeSubmit();
		console.log(submit);
       $.ajax({
		    url: 'includes/submit_slotSchedule.php',
   	 method: 'POST',
   	 data: submit,
  			//contentType: "application/json;charset=UTF-8",
   	 success: function(res) {
   	 	//console.log(res);
   	 	var submittedSlots=JSON.parse(res.trim());
   	 	
		if(submittedSlots.length>0){				
	 		if(submit.edit===1 && submit.both===false ){
	 			$('.rowslot'+submittedSlots[0].id_slot).remove();
	 			$('#sendSlot').text('Add this slot').data('edit',0).removeProp('data-slot');
				//if slot edited, we remove the old one				
				d3.select('#slot'+submit.id_slot).remove();  
				if(parseInt(BothRoomsVisible,10)===0){
					$('#both').show();
					$('label[for="both"]').show();
					BothRoomsVisible = 1;
				}
	 		}	
   	 	for(var k=0;k<submittedSlots.length;k++){
				//Add the slot to the slot list
				slotList.push(submittedSlots[k]);   
	 			//console.log(submittedSlot);
   	 		//converting the current slot as a definitive one and add a new incoming slot
				addSlotToSchedule(submittedSlots[k],scheduleZone,office,workplace);
   	 		//Adding the slot to the table of slots		
				addSlotToTable(submittedSlots[k],true);   
				//Same thing for the room slots
				if(k===0){
					addSlotToTableRoom(submittedSlots[k],false,'#tableRoom');				
				}else{
					addSlotToTableRoom(submittedSlots[k],false,'#tableRoom2');					
				} 	 	
   	 	}
			slotEditable();	

   	 						
				d3.select('#slot').remove();
				//add new empty slot
				addSlotSchedule();	
								
				//removing the day value to avoid overlap
				$('input[name="day"]:checked').prop('checked', false);
				$('input[name="room"]').trigger('change');
				//triggering the update of the slot
				updateEnd();
				var innerRoom=roomList.filter(function(obj){return obj.id_room==slot.ref_room;})[0];
				$('#room').val(submit.ref_room);//.attr('data-places',innerRoom.places ).attr('data-max',innerRoom.max);		
				d3.select('#slot').select('text').attr('opacity',0); 
				//Trigger the appearance of the slot
				showSendSlot();
		}
   		},
	      complete: function(){
	        		$('#loadingSlot').hide();
	      }
 		 });	
	
})	


var checkOverlaps = function(){
	$('#warnings').html('');
	slot=makeSubmit();
	intersectingSlot=[];	
	subslots=slotList.filter(function(obj){return  parseInt(slot.day,10)===parseInt(obj.day,10) && parseInt(obj.ref_user)===parseInt($( "#id_user" ).val(),10);}).sort(function(a,b){return parseInt(a.start,10)-parseInt(b.start,10);});
	//var Sampling=buildSampling(subslots);
	//console.log(slot.start);
	for(var k=0;k<subslots.length;k++){
		if(!(subslots[k].end <= slot.start || subslots[k].start >= slot.end ) ){
			intersectingSlot.push(subslots[k]);
		}
	}
	for (var k=0;k<intersectingSlot.length;k++) {
		//for the office and workplace
		var roomIntersecting = parseInt(intersectingSlot[k].ref_room,10)
		//checking if a slot corresponds to the same room
		if(roomIntersecting===slot.ref_room || roomIntersecting===slot.ref_room2){
			$('#warnings').append('<p class="warning">You already programmed a slot for this room</p>');
			$('#sendSlot').hide();	
		}
		//checking for too much slot outside of office and workplace
		if([office.ref_room,workplace.ref_room].indexOf(slot.ref_room) === -1 && [office.ref_room,workplace.ref_room].indexOf(roomIntersecting)===-1){
			$('#warnings').append('<p class="warning">You already programmed a slot outside of you workplace and office.</p>');
			$('#sendSlot').hide();			
		}
	}
	if(intersectingSlot.length>0){
		$('#warnings').append('<p class="softwarning">You already programmed a slot over the same period of time.</p>');
	}
}	
	
	
});