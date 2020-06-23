$(document).ready(function() {



var showSendSlot=function () {
	var room=parseInt($('#room').text(),10);
	var day=parseInt($('input[name="day"]:checked').val(),10);
	
	if(!isNaN(room) && room!==0 && !isNaN(day) && day!==0 ){
		$('#sendSlot').show();
		checkOverlaps(slotList);	
	}else{$('#sendSlot').hide();}
}	

//Hide the last row of the table of slots
$('#rowDay7').hide();



//Updating the slot when one of the sliders is changed		
var updateEnd = function(start,slotLength){
	var start=parseInt(d3.select('#value-hour').attr('data-value'),10);
	var slotLength=parseInt(d3.select('#value-length').attr('data-value'),10);	 
	if(start+slotLength > sliderHour.max){
		slotLength=Math.max(15,parseInt(sliderHour.max,10)-start);
		Length.value(slotLength);
	}
	if(start+slotLength > sliderHour.max){
		start=sliderHour.max-slotLength;
		Hours.value(sliderHour.max-slotLength);
	}	
	
	d3.select('#value-end').text(minToHours(start+slotLength));
	d3.select('#slot').select('text').attr('opacity',1); 
	if (start && slotLength && d3.select('input[name="day"]:checked').node()) {
		slotDay=d3.select('input[name="day"]:checked').node().value;
		d3.select('#slot').attr('transform', 'translate('+(start-sliderHour.min)+','+(slotDay-1)*dayDims.height+')');
		d3.select('#slot').select('rect').attr('width',slotLength);
		checkOverlaps(slotList);	
	}	
}

//updating the schedule when the room is changed
$('#room').on("DOMSubtreeModified",function () {
	
	//$line.='<span id="office" data-idSvg="'.$user['oSvg'].'" data-id="'.$user['oId'].'" data-human="'.$user['office'].'"></span>';
		roomName=$('#hroom').text();
	
		if(roomName===office.human){
			d3.select('#slot').select('rect').attr('height',dayDims.height/3).attr('y','0');
			d3.select('#slot').select('text').text(roomName).attr('y','12').attr('opacity',1);
		}else if(roomName===workplace.human){
			d3.select('#slot').select('rect').attr('height',dayDims.height/3).attr('y','15');
			d3.select('#slot').select('text').text(roomName).attr('y','27').attr('opacity',1);
		}else{
			d3.select('#slot').select('rect').attr('height',dayDims.height/3).attr('y','30');
			d3.select('#slot').select('text').text(roomName).attr('y','42').attr('opacity',1);
		}		
	showSendSlot();
})


var addSlotToSchedule =function(slot,scheduleZone){
	var slotSchedule=scheduleZone.append('g')
			.attr('id','slot'+slot.id_slot)
			.attr('data-id',slot.id_slot)
			.attr('transform', 'translate('+(slot.start-sliderHour.min)+','+(slot.day-1)*dayDims.height+')');	
		slotSchedule.append('rect')
			.attr('width',slot.length)
			.attr("fill",'#e31a1c')
			.attr('height',dayDims.height/3);	
		slotSchedule.append('text')
			.attr('y','12px')
			.attr('x','3px')
			.attr('font-size','0.8em')
			.attr('opacity',1)
			.text(slot.room);
	
		if(slot.room===office.human){
			slotSchedule.select('rect').attr('y','0').attr('fill',paletteRooms[0]);
			slotSchedule.select('text').attr('y','12');
		}else if(slot.room===workplace.human){
			slotSchedule.select('rect').attr('y','15').attr('fill',paletteRooms[1]);
			slotSchedule.select('text').attr('y','27');
		}else{
			slotSchedule.select('rect').attr('y','30').attr('fill',paletteRooms[2]);
			slotSchedule.select('text').attr('y','42');
		}
}





//sliders configuration
var sliderHour = {'min':420,'max':1260,'step':15,'default':540,'ticks':6,'output':'#value-hour','input':'#slider-hour','update':updateEnd};
var sliderLength = {'min':15,'max':720,'step':15,'default':240.01,'ticks':9,'output':'#value-length','input':'#slider-length','update':updateEnd};



//Both sliders
var Hours=sliderStep(sliderHour);
var Length = sliderStep(sliderLength);

$('input[name="day"]').on("change",updateEnd);


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

		
//adding each day and make them clickable
for (var j=1;j<7;j++) {
	var day=displayZone.append('g')
		.attr("data-day",j)
		.attr("class","weekDay")
		.attr('transform', 'translate(0,'+((j-1)*45)+')')
		.on('click', function() {
			var indixDay=d3.select(this).attr('data-day');
			d3.selectAll('input[name="day"]:checked').property('checked', false);
      	d3.select('[id="day'+indixDay+'"]').property('checked', true);
      	//$('input[name="day"]').trigger("change");
      	updateEnd();
       });
	day.append('rect')
		.attr('width',dayDims.width+'px')
		.attr('height',dayDims.height+'px')
		.attr('fill',paletteDays[j]);
	day.append('text')
		.attr("x",20)
		.attr("y",'30px')
		.attr("text-anchor", "left")
		.attr("font-size","1em")
		.attr("font-weight",'bold')
		.text(weekDays[j]);
}


//ROOMS


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
   	 			$('#room').text(room.id_room);					
   	 		})
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

//trigger color change if shortcut is used
$('input[name="room"]').on("change",function(){
		var shortcutRoom=$("input[name='room']:checked").val();
		$('#hroom').text($('#'+shortcutRoom).data('human'));		
		$('#room').text($('#'+shortcutRoom).data('id'));
})


$('.floors').on("change",function(){
		if($(this).is(':checked')){
			$('div[data-map="'+$(this).attr('value')+'"]').show();
		}else{
			$('div[data-map="'+$(this).attr('value')+'"]').hide();
		}
})

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
   	 	for(var k=0;k<res.length;k++){
   	 		updateRoom(res[k],that[0]);
   	 	}
   		 
   		}
 		 });        
			//Styling must be done here !
      });	      
	});
	
	


$('#sendSlot').hide();
	
$('input[name="day"]').on('change',showSendSlot);



///EDIT SLOTS
///EDIT SLOTS


var ediSlotToSchedule =function(slot,scheduleZone){
	//Make the old slot appear as a ghost
	var oldSlot=d3.select('#slot'+slot.id_slot).classed('edited','true');
	oldSlot.select('rect')
		.attr('fill-opacity',0.3);
	oldSlot.select('text')
		.attr('opacity',0.3);
		
	//Edit the day so that it updates when changed
	d3.selectAll('input[name="day"]:checked').property('checked', false);
   d3.select('[id="day'+parseInt(slot.day,10)+'"]').property('checked', true);
   $('input[name="day"]').trigger("change");		
   //update the room	and times
	Hours.value(parseInt(slot.start,10));
	Length.value(parseInt(slot.length,10));		
	$('#room').text(slot.ref_room);
	$('#hroom').text(slot.room);	

	//update the submit button	
	$('#sendSlot').text('Edit this Slot').data('edit',1).data('slot',slot.id_slot);		

	d3.select('#slot').remove();
	addSlotSchedule();	
	
	//update the current slot
	var slotSchedule=d3.select('#slot')
			.attr('data-id',slot.id_slot)
			.attr('transform', 'translate('+(slot.start-sliderHour.min)+','+(slot.day-1)*dayDims.height+')');	
		slotSchedule.select('rect')
			.attr('width',slot.length)
			.attr("fill",'#e31a1c')
			.attr('height',dayDims.height/3);	
		slotSchedule.select('text')
			.attr('y','12px')
			.attr('x','3px')
			.attr('font-size','0.8em')
			.attr('opacity',1)
			.text(slot.room);
	
		if(slot.room===office.human){
			slotSchedule.select('rect').attr('y','0');
			slotSchedule.select('text').attr('y','12');
		}else if(slot.room===workplace.human){
			slotSchedule.select('rect').attr('y','15');
			slotSchedule.select('text').attr('y','27');
		}else{
			slotSchedule.select('rect').attr('y','30');
			slotSchedule.select('text').attr('y','42');
		}
}


var slotEditable=function (){
	$('.editSlot').on("click",function(){ 
		var id = $(this).data('id');
		var slot=slotList.filter(function(obj){return  parseInt(id,10)===parseInt(obj.id_slot,10);} )[0];
		//removing the slot from the slotlist to avoid comparison when checking for overlap
		slotList.splice(slotList.indexOf(slot),1);		
		
		ediSlotToSchedule(slot,scheduleZone);			
	})	
}
//ADD EXISTING SLOTS


       $.ajax({
		    url: 'includes/send_slotWeek.php',
   	 method: 'POST',
   	 data: {'template':parseInt($('#template').data('template'),10)},
  			//contentType: "application/json;charset=UTF-8",
   	 success: function(res) {
   	 	var res=JSON.parse(res.trim());
				for (var k=0;k<res.length;k++) {
					//Adding the slot to the table of slots
					addSlotToTable(res[k]);	
					addSlotToSchedule(res[k],scheduleZone);	
					slotList.push(res[k]);				
				}
			
				slotEditable();
				
   		}
 		 });	




///SLOTS MANAGEMENT



var makeSubmit = function(){
	var submit={'day':$('input[name="day"]:checked').val(),
					'start':parseInt(d3.select('#value-hour').attr('data-value'),10),
					'length':parseInt(d3.select('#value-length').attr('data-value'),10),
					'ref_room':parseInt($('#room').text(),10),
					'ref_template':parseInt($('#template').data('template'),10),
					'room':$('#hroom').text(),
					'edit':parseInt($('#sendSlot').data('edit'),10)
					}
					submit.end=submit.start+submit.length;
					if(!isNaN(parseInt($('#sendSlot').data('slot'),10))){
						submit.id_slot=parseInt($('#sendSlot').data('slot'),10);
					}
					return submit;
	
}


//SEND SLOT
$('#sendSlot').on("click",function () {

		submit=makeSubmit();
		//console.log(submit);
       $.ajax({
		    url: 'includes/submit_slotWeek.php',
   	 method: 'POST',
   	 data: submit,
  			//contentType: "application/json;charset=UTF-8",
   	 success: function(res) {
   	 	var res=JSON.parse(res.trim());
				//Add the slot to the slot list
				slotList.push(res);   
	 	
   	 		//converting the current slot as a definitive one and add a new incoming slot
				addSlotToSchedule(res,scheduleZone);
				d3.select('#slot').remove();
				//add new empty slot
				addSlotSchedule();	

				
   	 		if(submit.edit===1){
   	 			$('#rowslot'+res.id_slot).remove();
   	 			$('#sendSlot').text('Add this slot').data('edit',0).removeProp('data-slot');
					//if slot edited, we remove the old one				
					d3.select('#slot'+submit.id_slot).remove();   	 			
   	 		}	
   	 		//Adding the slot to the table of slots		
				addSlotToTable(res); 
				slotEditable();
				//removing the day value to avoid overlap
				$('input[name="day"]:checked').prop('checked', false);
				//triggering the update of the slot
				updateEnd();
				$('#room').text(submit.ref_room);		
				d3.select('#slot').select('text').attr('opacity',0); 
				//Trigger the appearance of the slot
				showSendSlot();

   		}
 		 });	
	
})	


	
var checkOverlaps = function(slots){
	$('#warnings').html('');
	slot=makeSubmit();
	intersectingSlot=[];
	
	
	subslots=slots.filter(function(obj){return  parseInt(slot.day,10)===parseInt(obj.day,10);}).sort(function(a,b){return parseInt(a.start,10)<parseInt(b.start,10);});
	//var Sampling=buildSampling(subslots);
	//console.log(slot.start);
	for(var k=0;k<subslots.length;k++){
		if(!(subslots[k].end <= slot.start || subslots[k].start >= slot.end ) ){
			intersectingSlot.push(subslots[k]);
		}
	}
	//console.log(slot);
	console.log(intersectingSlot.length);

	for (var k=0;k<intersectingSlot.length;k++) {
		//for the office and workplace
		var roomIntersecting = parseInt(intersectingSlot[k].ref_room,10)
		//checking if a slot corresponds to the same room
		if(roomIntersecting===slot.ref_room){
			$('#warnings').append('<p class="warning">You already programmed a slot for this room</p>');
			$('#sendSlot').hide();	
		}
		//checking for a second slot on top of a slot for an extra room
		if([office.ref_room,workplace.ref_room].indexOf(slot.ref_room) === -1 && [office.ref_room,workplace.ref_room].indexOf(roomIntersecting)===-1){
			$('#warnings').append('<p class="warning">You already programmed a slot outside of you workplace and office.</p>');
			$('#sendSlot').hide();			
		}
	}
	if(intersectingSlot.length>0){
		$('#warnings').append('<p class="softwarning">You already programmed a slot over the same period of time.</p>');
	}
	//console.log(intersectingSlot);
}	
	
	
});