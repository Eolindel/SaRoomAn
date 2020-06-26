
//sliders configuration
var sliderHour = {'min':420,'max':1320,'step':15,'default':540,'ticks':6,'output':'#value-hour','input':'#slider-hour'};
var sliderLength = {'min':15,'max':720,'step':15,'default':240.01,'ticks':9,'output':'#value-length','input':'#slider-length'};


DateToFormat = function (dateFormate, datetime) {
	return $.datepicker.formatDate(dateFormate, datetime);}
    
FormatToDate = function  (dateFormate, datetime){
	return $.datepicker.parseDate(dateFormate,  datetime);} 

function getMonday(d) {
  d = new Date(d);
  var day = d.getDay(),
      diff = d.getDate() - day + (day == 0 ? -6:1); // adjust when day is sunday
  return new Date(d.setDate(diff));
}
function addDays(date, days) {
  var result = new Date(date);
  result.setDate(result.getDate() + days);
  return result;
}


var currentMonday = DateToFormat("dd-mm-yy",getMonday(new Date()));

//sélecteur pour la date
var initializeDate = function(dateInput){
//sélecteur pour la date
$( dateInput).datepicker({
	dateFormat: "dd-mm-yy",	
	gotoCurrent: true,
	changeMonth: true,
	changeYear: true,
	firstDay: 1, 
	showOtherMonths: true,
	numberOfMonths:2,
	onSelect: function(date){
		$(dateInput).trigger("change");}      
});	
	
//Date par défaut si la date est définie ou non
(function () {    	
	jour = $(dateInput).val();
if (jour){
	$(dateInput).datepicker( "option", "defaultDate", $(dateInput).val());
} else {
	$(dateInput).datepicker( "option", "defaultDate", new Date() );
	$(dateInput).val(DateToFormat('dd-mm-yy', new Date()));
	//alert(DateFormate('dd-mm-yy', new Date()));
};
})();	

}
initializeDate('#date')

function getWeekNumber(d) {
    // Copy date so don't modify original
    d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
    // Set to nearest Thursday: current date + 4 - current day number
    // Make Sunday's day number 7
    d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay()||7));
    // Get first day of year
    var yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1));
    // Calculate full weeks to nearest Thursday
    var weekNo = Math.ceil(( ( (d - yearStart) / 86400000) + 1)/7);
    // Return array of year and week number
    return [d.getUTCFullYear(), weekNo];
}


var dayDims={'height':45,'width':150};
var dims={'width':(sliderHour.max-sliderHour.min+dayDims.width+20),'height':290};
var weekDays=['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
var paletteDays=['#cccccc','#b3e2cd','#fdcdac','#cbd5e8','#f4cae4','#e6f5c9','#fff2ae','#f1e2cc'];
var paletteRooms=['#d8b365','#5ab4ac','#e9a3c9'];
var slotList=[];

var office={'human':$('#office').data('human'),'ref_room':$('#office').data('id'),'idSvg':$('#office').data('idsvg')};
var workplace={'human':$('#workplace').data('human'),'ref_room':$('#workplace').data('id'),'idSvg':$('#workplace').data('idsvg')};  



//convert
var minToHours=function (val){
	return Math.floor(val/60.0).toString().padStart(2, '0')+':'+parseInt(val%60.0,10).toString().padStart(2, '0')
	}
//hours as label for the schedule	
var minToHoursOnly=function (val){
	return Math.floor(val/60.0).toString().padStart(2, '0')+' h';
	}

	
var slotsIntersecting=function(a,b){
	return !(a.end <= b.start || a.start >= b.end );
}	
	
var highlightElem =  function (elemId){
    var elem = $(elemId);
    elem.css("backgroundColor", "#ffffff"); // hack for Safari
    elem.animate({ backgroundColor: '#9ecae1' }, 800);
    setTimeout(function(){$(elemId).animate({ backgroundColor: "#ffffff" }, 800)},800);
}	
	

var buildSampling=function (slots, slot = false) {
	var Sampling=[];	 
	for (var k=0;k<slots.length;k++) {
		var start=parseInt(slots[k].start,10);
		var end=parseInt(slots[k].end,10);
		if(Sampling.indexOf(start)===-1){
			Sampling.push(start);		}
		if(Sampling.indexOf(end)===-1){
			Sampling.push(end);		}		
	};
	if(slots.length>0){
		if(parseInt(slots[0].start,10)!==sliderHour.min){
			Sampling.push(sliderHour.min);		}	
	}else{Sampling.push(sliderHour.min);}
	
	if(Sampling.indexOf(sliderHour.max)=== -1){
		Sampling.push(sliderHour.max);	}
			
	if(slot){
		if(Sampling.indexOf(parseInt(slot.start,10))===-1){
			Sampling.push(parseInt(slot.start,10));}
		if(Sampling.indexOf(parseInt(slot.end,10))===-1){
			Sampling.push(parseInt(slot.end,10));}	
	}
	return Sampling.sort(function compareNumbers(a, b) {return a - b;});
}		

var buildOccupation = function(subslots,room,slot){
	var overcrowded = false;
	var occupation=[];
	var currentSlots=[];
	var Sampling=[];
	if(slot){
		Sampling = buildSampling(subslots,slot);
	}else{Sampling = buildSampling(subslots)}
			
	var k=0;
	//for each sampling value (start or end of a slot in the slot list)
	while(k<Sampling.length){
		//We add all the slots starting at this hour and remove them from the slot list
		//console.log(Sampling[k]);
		while(subslots.length>0 && parseInt(subslots[0].start,10) === Sampling[k] ){
			currentSlots.push(subslots.shift());}
		//We remove all the slots ending at this hour
		for (var j=currentSlots.length-1;j>=0;j--) {
			if(parseInt(currentSlots[j].end)=== Sampling[k]){
				currentSlots.splice(j,1);}
		}
		if( currentSlots.length > room.max){
			overcrowded = true;
			for(var m=0;m<currentSlots.length;m++){
				$('#rowslot'+currentSlots[m].id_slot).addClass('warning');
			}
			
			}
		occupation.push({'x':Sampling[k],'y':currentSlots.length});
		k++;			
	}		
	return [overcrowded,occupation];

} 


//Load the occupation of the room
var roomOccupation = function(graphwidth,roomRef, targetZone, warningRef,day, week = false,refslot = false){
	var occupations = [];
	var innerdays =[];
	var room = roomRef;
	var subslotsRoomDay;
	var slot = refslot;
	var overcrowdedDuringWeek=false;

	
	//console.log(room);
	//creating slotlist ordered by start
	var subslotsRoom=slotList.filter(function(obj){return  parseInt(obj.ref_room,10)===parseInt(room.id_room,10);});

	//console.log(room);
	if(week === true ){
		for(var j=1;j<7;j++){
			innerdays.push(j);}
	}else if (day){
		innerdays.push(day);}

	for(var j=0;j<innerdays.length;j++){
		//Table containing all the occupations, either for a single day of the whole week
		var subslotsRoomDay = subslotsRoom.filter(function(obj){return  parseInt(innerdays[j],10)===parseInt(obj.day,10);}).sort(function(a,b){return parseInt(a.start,10)-parseInt(b.start,10);});
		var tempResult = buildOccupation(subslotsRoomDay,room,slot)
		if(tempResult[0]==true ){
			overcrowdedDuringWeek = true;
		}
		occupations.push(tempResult);}//
if(refslot===false)	{
	if(overcrowdedDuringWeek === true){
		$(warningRef).append('The room <a href="rooms_occupation.php?id_room='+room.id_room+'&date='+$('input[name="date"]').val()+'">'+room.officeName+'</a> is overcrowded at some point in the week.<br>');
		d3.select('[id="'+room.idSvg+'"]').style('fill','red').style('fill-opacity',0.3);
	}else{
		d3.select('[id="'+room.idSvg+'"]').style('fill','green').style('fill-opacity',0.08);}
}

	
//Building the graphs	
	var margins={'left':0,'top':14,'bottom':18,'right':5,'lineheight':30,'graphspace':9}
	var dimensionGraph={width:dims.width,height:(margins.lineheight*occupations.length+margins.graphspace*(occupations.length-1)+margins.top+margins.bottom)};
	var graphzone = d3.select(targetZone).html('').append('svg')
				.attr('width',graphwidth)
				.attr('height',dimensionGraph.height);

	var x = d3.scaleLinear()
	    .rangeRound([0, graphwidth-dayDims.width-margins.right]);
	var y = d3.scaleLinear()
	    .rangeRound([margins.lineheight, 0]);
	var interpolationMode = d3.curveStepAfter;
	var line = d3.line()
		 .x(function(d) { return x(d.x); })
	    .y(function(d) { return y(d.y); }).curve(interpolationMode);    


for(var j=0;j<occupations.length;j++){
	var maxOfGraph=d3.max(occupations[j][1], function(d) { return Math.max(d.y+1,parseInt(room.places,10)); });
	var graph = graphzone.append("g").attr("data-day",j).attr("transform", "translate("+dayDims.width+","+(margins.top+j*margins.lineheight+margins.graphspace*(j-1))+")");
	  x.domain(d3.extent(occupations[j][1], function(d) { return d.x; }));
	  y.domain([0,maxOfGraph]);

	 var textDay = graphzone.append("text").classed('nameDays',true)
	 		.attr("transform", "translate(20,"+(margins.top+(j+0.5)*margins.lineheight+margins.graphspace*(j-1))+")")
	      .text(weekDays[innerdays[j]]);
		if(occupations[j][0]===true){//if the room is overcrowded, the name of the day goes to red
			textDay.attr('fill','red')
		}
			
			
			
	if (j === occupations.length-1 ) {
	  graph.append("g")
	      .attr("transform", "translate(0,"+y(0)+")")
	      .call(d3.axisBottom(x).ticks(8).tickFormat(minToHours))
	      .append("text").attr("transform", "translate("+(dimensionGraph.width-30)+",15)").attr("y", 6).attr("dy", "0.71em")
	      .classed("xLabel",true)
	      .text("Hour");
	}	
	
	if(week!==true){
	 var textRoom = graphzone.append("text").classed('nameRoom',true)
	 		.attr("transform", "translate(25,"+(margins.top+(j+0.98)*margins.lineheight+margins.graphspace*(j-1))+")")
	      .text(room.officeName);	  	
	
	
	}
  graph.append("g").call(d3.axisLeft(y).ticks(3));
	      
	//plotting occupation before slot
	  graph.append("path")
	      .datum(occupations[j][1])
	      .classed('occupationLine',true)
	      .attr("d", line);

	  //plotting threshold and max capacity
	  graph.append("rect").attr("x",0).attr('y',0).classed('rectmax',true)
	  	.attr('width',sliderHour.max-sliderHour.min)
	  	.attr('height',Math.abs( y(maxOfGraph)-y(room.max) ) );
	  	
	  	
	  	if(slot){
	  		//plotting occupation after slot
			var lineSlot = d3.line()
			 .x(function(d) { return x(d.x); })
		    .y(function(d) { if(slot.start <= d.x && slot.end > d.x ){return y(d.y+1);}else{return y(d.y);} }).curve(interpolationMode);	  	  
		  
		  graph.append("path")
		      .datum(occupations[j][1])
		      .classed('slotLine',true)
		      .attr("d", lineSlot);	  	
	  	}
}//End for days	
}


var sliderStep = function(slider){
	var sliderStep = d3
    .sliderBottom()
    .min(slider.min)
    .max(slider.max)
    .width(300)
    .tickFormat(minToHours)
    .ticks(slider.ticks)
    .step(slider.step)
    .default(slider.default)
    .on('onchange', val => {
      d3.select(slider.output).text(minToHours(val)).attr('data-value',val);
     	slider.update.call(this);
    });
  var gStep = d3
    .select(slider.input)
    .append('svg')
    .attr('width', 350)
    .attr('height', 44)
    .append('g')
    .attr('transform', 'translate(30,10)');
  	gStep.call(sliderStep);
  	d3.select(slider.output).text(minToHours(sliderStep.value())).attr('data-value',sliderStep.value());
 	slider.update.call(this);
 	return sliderStep;
}

var addSlotToSchedule =function(slot,scheduleZone,office,workplace){
	var slotSchedule=scheduleZone.append('g')
			.attr('id','slot'+slot.id_slot)
			.attr('data-id',slot.id_slot)
			.attr('class','existingSlot')
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
	
		if(office!==undefined && parseInt(slot.ref_room,10)===parseInt(office.ref_room,10)){
			slotSchedule.select('rect').attr('y','0').attr('fill',paletteRooms[0]);
			slotSchedule.select('text').attr('y','12');
		}else if(workplace!==undefined && parseInt(slot.ref_room,10)===parseInt(workplace.ref_room,10)){
			slotSchedule.select('rect').attr('y','15').attr('fill',paletteRooms[1]);
			slotSchedule.select('text').attr('y','27');
		}else{
			slotSchedule.select('rect').attr('y','30').attr('fill',paletteRooms[2]);
			slotSchedule.select('text').attr('y','42');
		}
}

var addSlotToTable=function(data,removable = false){
	if(!$('#rowslot'+data.id_slot).length){
		//<th>Day</th><th>Start</th><th>End</th><th>Length</th><th>Room</th>
		var rights=allowedRights('#schedule');	
		var iduser=parseInt($('#schedule').data('iduser'),10);	
		var startType= parseInt(data.start,10)>780 ? 'morningSlot' : 'afternoonSlot';
		var endType=parseInt(data.end,10)>780 ? 'morningSlot' : 'afternoonSlot';
		var line='<tr class="rowslots rowslot'+data.id_slot+'" id="rowslot'+data.id_slot+'"><td>';
		//console.log(location.pathname.split("/").slice(-1)[0]);
		if(location.pathname.split("/").slice(-1)[0]==="schedule_build.php"){
			line+='<a href="#" class="editSlot" data-id="'+data.id_slot+'">';	
		}else{
			line+='<a href="schedule_build.php?id_slot='+data.id_slot+'" class="editSlot" data-id="'+data.id_slot+'">';		
		}
		if(editIcon(data)){
			line+='<img src="images/icones/edit.png" alt="edit">';
		}
		
		
		line+='</a></td><td>'+weekDays[data.day]+'</td><td class="'+startType+'">'+minToHours(data.start)+'</td><td class="'+endType+'">'+minToHours(data.end)+'</td><td>'+minToHours(data.length)+'</td><td>'+data.room+'</td><td>'+data.commentaire+'</td><td>'+data.valid+'</td><td>';
		if(deleteIcon(data)){
			line+='<a href="slot_delete.php?id_slot='+data.id_slot+'"><img src="images/icones/delete.png" alt="delete this slot"></a>';
		}
		line+='</td></tr>';
		$('#rowDay'+(parseInt(data.day,10)+1),'#SlotList').before(line);	
	}
}

var editIcon=function(data){
	var rights=allowedRights('#schedule');	
	var team=$('#schedule').data('team');
	var iduser=parseInt($('#schedule').data('iduser'),10);	
	var userRight  = rights>=parseInt(data.valid,10) && parseInt(data.ref_user,10)===iduser && rights===1;
	var groupRight = rights>=parseInt(data.valid,10) && parseInt(data.ref_responsable,10)===iduser && rights===2;
	var teamRight  = rights>=parseInt(data.valid,10) && rights===3 && team === data.team;
	var headRight  = rights>=parseInt(data.valid,10) && rights>=4;
	if(userRight || groupRight || teamRight || headRight){
		return true;
	}else{
		return false;
	}

} 
var deleteIcon=function(data){
	var rights=allowedRights('#schedule');	
	var team=$('#schedule').data('team');
	var iduser=parseInt($('#schedule').data('iduser'),10);	
	var userRight  = parseInt(data.ref_user,10)===iduser && rights===1;
	var groupRight = (parseInt(data.ref_responsable,10)===iduser || parseInt(data.ref_user,10)===iduser) && rights===2;
	var teamRight  = rights===3 && team === data.team;
	var headRight  = rights>=4;
	if(userRight || groupRight || teamRight || headRight){
		return true;
	}else{
		return false;
	}

} 

var addSlotToTableRoom=function(data,removable = false,refTable){
	var innerRefTable;
	if (refTable=== undefined ) {
		innerRefTable = '#roomSlotList';
	}else{
		innerRefTable = refTable;
	}
	var startType= parseInt(data.start,10)>780 ? 'morningSlot' : 'afternoonSlot';
	var endType=parseInt(data.end,10)>780 ? 'morningSlot' : 'afternoonSlot';	
	var line='<tr class="rowslots rowslot'+data.id_slot+'" id="rowslot'+data.id_slot+'"><td>';
		if(location.pathname.split("/").slice(-1)[0]==="schedule_build.php"){
			line+='<a href="#" class="editSlot" data-id="'+data.id_slot+'">';	
		}else{
			line+='<a href="schedule_build.php?id_slot='+data.id_slot+'" class="editSlot" data-id="'+data.id_slot+'">';
		}	
	if(editIcon(data)){
		line+='<img src="images/icones/edit.png" alt="edit">';
	}
	line+='</a></td><td>'+weekDays[data.day]+'</td><td class="'+startType+'">'+minToHours(data.start)+'</td><td class="'+endType+'">'+minToHours(data.end)+'</td><td>'+minToHours(data.length)+'</td>'
	if(data.id_user !==undefined){
		line+='<td><a href="schedule_user.php?id_user='+data.id_user+'">'+data.prenom+' '+data.nom+'</a></td>';
	}else{
		line+='<td><a href="schedule_user.php?id_user='+data.ref_user+'">'+data.prenom+' '+data.nom+'</a></td>';	
	}
	
	line+='<td>'+data.commentaire+'</td><td>'+data.valid+'</td><td>';//&amp;date='+data.date+'
	if(deleteIcon(data)){
		line+='<a href="slot_delete.php?id_slot='+data.id_slot+'"><img src="images/icones/delete.png" alt="delete this slot"></a>';
	}
	line+='</td></tr>';
	
	$('#rowDay'+(parseInt(data.day,10)+1),innerRefTable).before(line);
}

var addSlotToTableFull=function(data,removable = false){
	var startType= parseInt(data.start,10)>780 ? 'morningSlot' : 'afternoonSlot';
	var endType=parseInt(data.end,10)>780 ? 'morningSlot' : 'afternoonSlot';	
	var line='<tr class="rowslots rowslot'+data.id_slot+'" id="rowslot'+data.id_slot+'"><td>';
	if(editIcon(data)){
		line+='<a href="schedule_build.php?id_slot='+data.id_slot+'" class="editSlot" data-id="'+data.id_slot+'"><img src="images/icones/edit.png" alt="edit"></a>';
	}	
	line+='</td><td>'+weekDays[data.day]+'</td><td class="'+startType+'">'+minToHours(data.start)+'</td><td class="'+endType+'">'+minToHours(data.end)+'</td><td>'+minToHours(data.length)+'</td><td><a href="rooms_occupation.php?id_room='+data.ref_room+'">'+data.officeName+'</a></td><td><a href="schedule_user.php?id_user='+data.ref_user+'">'+data.prenom+' '+data.nom+'</a></td><td>'+data.commentaire+'</td><td>'+data.valid+'</td><td>';
	if(deleteIcon(data)){
		line+='<a href="slot_delete.php?id_slot='+data.id_slot+'"><img src="images/icones/delete.png" alt="delete this slot"></a>';
	}
	line+='</td></tr>';
	
	$('#slotList').append(line);
}

//Add line per person for the schedule of the week
var addToTablePerson=function(arrayPersons){
	for(var k=0;k<arrayPersons.length;k++){
		var line = '<tr class="weekuser"><td class="right"><a href="schedule_build.php?id_slot='+arrayPersons[k].id_slot+'">'+arrayPersons[k].prenom+'</a></td><td class="left"><a href="schedule_user.php?id_user='+arrayPersons[k].id_user+'&amp;date='+$('input[name="date"]' ).val()+'">'+arrayPersons[k].nom+'</a></td>';
		for(var j=1;j<weekDays.length;j++){
			line +='<td><table data-day="'+j+'">';
			for(l=0;l<arrayPersons[k].schedule[j].length;l++){
				var slot = arrayPersons[k].schedule[j][l];
				var startType= parseInt(slot.start,10)>780 ? 'morningSlot' : 'afternoonSlot';
				var endType=parseInt(slot.end,10)>780 ? 'morningSlot' : 'afternoonSlot';

				line +='<tr><td class="'+startType+'">'+minToHours(slot.start)+'</td><td class="'+endType+'">'+minToHours(slot.end)+'</td><td>'+slot.room+'</td></tr>';
			}
			line +='</table></td>';
		}
		line +='</tr>';
		$('#PeopleList').append(line);
	}
}

//TO be able to delete or add a slot
var allowedRights=function (insight) {
	return parseInt($(insight).data('rights'));
};
var rights=0;


//import maps
var displayOccupation=function(sendSlotSchedule){
	$(".innermap").each(function () {
		var file=$(this).data('map');
		var floor=$(this).data('floor');
		var building=$(this).data('building');
		var that=$(this);
      d3.xml("maps/"+file,  function(xml) {
      var importedNode = document.importNode(xml.documentElement, true);
      d3.select(that[0])
        .each(function() {this.appendChild(importedNode);})
      });	      
	});
   //Requesting the rooms for this floor to make them clickable    
	$('input[name="date"]').on("change",function(){
		$('#warningOvercrowded').html('');	
		sendSlotSchedule(false);
	})   
   sendSlotSchedule(true);	
}	



//Load the occupation of the room
var fullOccupation = function(targetZone, warningRef){
	var occupations = [];
	var innerdays =[];
	var subslotsDay;
	for(var j=1;j<7;j++){
		innerdays.push(j);}
	for(var j=0;j<innerdays.length;j++){
		//Table containing all the occupations, either for a single day of the whole week
		var subslotsDay = slotList.filter(function(obj){return  parseInt(innerdays[j],10)===parseInt(obj.day,10);}).sort(function(a,b){return parseInt(a.start,10)-parseInt(b.start,10);});
		occupations.push(buildingOccupation(subslotsDay));}//
	var margins={'left':0,'top':14,'bottom':18,'right':5,'lineheight':50,'graphspace':9}
	var dimensionGraph={width:dims.width,height:(margins.lineheight*occupations.length+margins.graphspace*(occupations.length-1)+margins.top+margins.bottom)};
	var graphzone = d3.select(targetZone).html('').append('svg')
				.attr('width',900)
				.attr('height',dimensionGraph.height);
	var interpolationMode = d3.curveStepAfter;
	var line = d3.line()
		 .x(function(d) { return x(d.x); })
	    .y(function(d) { return y(d.y); }).curve(interpolationMode);    

//Detect the x position of the mouse pointer
var getMinutesAxis =  function (that) {
	   var x0 = x.invert((d3.mouse(that)[0]));
        return Math.floor(x0);}
//Make the slot timetable dynamic with indication of hour and number of people in the building
var LiveOccupation = function (j,x,y) {
	var day=j,innerX=x,innerY=y;
	//Point bleu
	var focus = graph.append("g")
	      .attr("class", "focus")
	      .style("display", "none");
	  focus.append("circle").attr("r", 4.5);
	var textFocus = focus.append("text").attr("class","movLabel").attr("x", 0).attr("dy", "-0.5em");
	if(day===0){
		textFocus.attr("dy", "0.9em");}	
	//animating the focus element and adding extra information	
	var mousemove=function () {
	//console.log(occupations);
	var minutes=getMinutesAxis(this);
		intervalsBefore=occupations[day][1].filter(function(obj){return obj.x<minutes;}),
		matchingInterval=intervalsBefore[intervalsBefore.length-1];
    focus.attr("transform", "translate(" + (innerX(minutes)) + "," + (innerY(matchingInterval.y)) + ")");
    focus.select("text").text(minToHours(minutes)+' '+matchingInterval.y+' p');
	 focus.style("display", null);
	 
	 var smallSlots=slotList.filter(function (obj) {return parseInt(obj.day,10)===day+1 && parseInt(obj.start,10)<=minutes && parseInt(obj.end,10)>=minutes ;});
	 var RoomsOccupied=[];
	 for(var k=0;k<smallSlots.length;k++){
	 	var slot = smallSlots[k];
	 	//roomExisting = RoomsOccupied.filter(function (obj) {return parseInt(obj.idSvg,10)===slot.idSvg;});
	 	indexRoom = RoomsOccupied.map(function(e) { return e.idSvg; }).indexOf(slot.idSvg);
	 	if(indexRoom === -1){
	 		RoomsOccupied.push({'idSvg':slot.idSvg,'max':parseInt(slot.max,10),'places':parseInt(slot.places,10),occ:1});
	 	}else{RoomsOccupied[indexRoom].occ+=1;}
	 }
	 d3.selectAll('.innermap').selectAll('.coloredRoom').style('fill','none').classed('coloredRoom',false);
	 for(var k=0;k<RoomsOccupied.length;k++){
		var room=d3.selectAll('.innermap').select('#'+RoomsOccupied[k].idSvg);
		if(RoomsOccupied[k].occ < RoomsOccupied[k].max && RoomsOccupied[k].occ>0){
			room.style('fill','#d9ef8b').classed('coloredRoom',true);
		}else if (RoomsOccupied[k].occ === RoomsOccupied[k].max && RoomsOccupied[k].occ>0) {
			room.style('fill','#fee08b').classed('coloredRoom',true);
		}else if(RoomsOccupied[k].occ > RoomsOccupied[k].max && RoomsOccupied[k].occ>0){
			room.style('fill','#d73027').classed('coloredRoom',true);
		}
	 }	 
 
	}	
	
		//mouse detection zone
  graph.append("rect")
      .attr("class", "overlay").attr("width", 900-dayDims.width-margins.right).attr("height",margins.lineheight).attr('data-day',j)
      .attr("fill", "black")
      .on("mouseover", function() {focus.style("display", null);})
      .on("mouseout", function() {focus.style("display", "none");
      	d3.selectAll('.innermap').selectAll('.coloredRoom').style('fill','none').classed('coloredRoom',false);})
      .on("mousemove", mousemove);	
}

//console.log(occupations);
for(var j=0;j<occupations.length;j++){
	var maxOfGraph=d3.max(occupations[j][1], function(d) { return d.y+1; });
	var graph = graphzone.append("g").attr("data-day",j).attr("transform", "translate("+dayDims.width+","+(margins.top+j*margins.lineheight+margins.graphspace*(j-1))+")");
	var x = d3.scaleLinear()
	    .rangeRound([0, 900-dayDims.width-margins.right]).domain(d3.extent(occupations[j][1], function(d) { return d.x; }));
	var y = d3.scaleLinear()
	    .rangeRound([margins.lineheight, 0]).domain([0,maxOfGraph]);	
	//Add the name of the day (monday, etc))
	 var textDay = graphzone.append("text").classed('nameDays',true)
	 		.attr("transform", "translate(20,"+(margins.top+(j+0.4)*margins.lineheight+margins.graphspace*(j-1))+")")
	      .text(weekDays[innerdays[j]]);
	//Add the full date 31-12-2020
  	 var dateOfDays= new Date();
 			dateOfDays.setDate(FormatToDate("dd-mm-yy",currentMonday).getDate() + (innerdays[j]-1));
 			dateOfDays=DateToFormat("dd-mm-yy",dateOfDays);
	 var textDate = graphzone.append("text")
	 		.attr("text-anchor", "left")
			.attr("font-size","0.8em")
	 		.attr("transform", "translate(20,"+(margins.top+(j+0.8)*margins.lineheight+margins.graphspace*(j-1))+")")
	      .text(dateOfDays);	 
	addLineToTableEntries(occupations[j],innerdays[j],dateOfDays);	      
	//Adding axis label for the last graph		
	if (j === occupations.length-1 ) {
	  graph.append("g")
	      .attr("transform", "translate(0,"+y(0)+")")
	      .call(d3.axisBottom(x).ticks(8).tickFormat(minToHours))
	      .append("text").attr("transform", "translate("+(dimensionGraph.width-30)+",15)").attr("y", 6).attr("dy", "0.71em")
	      .classed("xLabel",true)
	      .text("Hour");
	}	
  graph.append("g").call(d3.axisLeft(y).ticks(3));
	      
	//plotting occupation before slot
	  graph.append("path")
	      .datum(occupations[j][1])
	      .classed('occupationLine',true)
	      .attr("d", line);
	//Animation with mouse moving
	LiveOccupation(j,x,y);	
	}//End for days	
}


//Build the timetable for the building entries and leavings
var addLineToTableEntries=function (occupation,day,date){
	for(var l=0;l<occupation[1].length;l++){
		var line = {'day':'','hour':minToHours(occupation[1][l].x),'occupation':0, 'Incoming':'', 'Leaving':'','Remaining':''};
		var incoming=[];
		var leaving=[];
		var remaining=[];		
		incoming = occupation[0][l].filter(function (obj) {return obj.state===1;});
		leaving = occupation[0][l].filter(function (obj) {return obj.state===-1;});
		remaining = occupation[0][l].filter(function (obj) {return obj.state===0;});
		
		//console.log(remaining);
		line.occupation = incoming.length + remaining.length ;
		
		for(var m=0;m<incoming.length;m++){
			line.Incoming += '<a href="schedule_user.php?id_user='+incoming[m].id_user+'&amp;date='+currentMonday+'"  class="incoming">'+	incoming[m].name+',</a> ';}
		for(var m=0;m<leaving.length;m++){
			line.Leaving += '<a href="schedule_user.php?id_user='+leaving[m].id_user+'&amp;date='+currentMonday+'" class="leaving">'+ leaving[m].name+',</a> ';}
		for(var m=0;m<remaining.length;m++){
			line.Remaining += '<a href="schedule_user.php?id_user='+remaining[m].id_user+'&amp;date='+currentMonday+'" class="remaining">'+ remaining[m].name+',</a> ';}
		if(l<occupation[1].length-1){
			line.hour += '-'+minToHours(occupation[1][l+1].x);}		
		$('#rowDay'+(parseInt(day,10)+1)).before('<tr class="LinesDays"><td>'+date+'</td><td>'+line.hour+'</td><td>'+line.occupation+'</td><td class="incoming">'+line.Incoming+'</td><td class="remaining">'+line.Remaining+'</td><td class="leaving">'+line.Leaving+'</td></tr>');
	}
}

//build the schedule for each roomuser
var SchedulePerson = function(slotList){
	var arrayPersons=[];
	for(var k=0;k<slotList.length;k++){
		if(!arrayPersons.some(function(elem){return elem.id_user === parseInt(slotList[k].ref_user,10); } ) ){//Addition if not in the table of unique user 
			arrayPersons.push({'id_user':parseInt(slotList[k].ref_user,10),'nom':slotList[k].nom,'prenom':slotList[k].prenom,'schedule':[[],[],[],[],[],[],[]]});
		}
		var indexP = arrayPersons.map(function(e) { return e.id_user; }).indexOf(parseInt(slotList[k].ref_user,10));
		var indexS = arrayPersons[indexP].schedule[slotList[k].day].findIndex(function(elem){return parseInt(elem.start,10) === parseInt(slotList[k].start,10) && parseInt(elem.end,10) === parseInt(slotList[k].end,10); });
		if ( indexS !== -1) {
			arrayPersons[indexP].schedule[slotList[k].day][indexS].room+=', '+slotList[k].room;
		}else{
			arrayPersons[indexP].schedule[slotList[k].day].push({'start':parseInt(slotList[k].start,10),'end':parseInt(slotList[k].end,10),'room':slotList[k].room});
		}
	}
	arrayPersons=arrayPersons.sort(function(a,b){return a['nom'].localeCompare(b['nom']);});
	addToTablePerson(arrayPersons);
}


//Construct the building occupation
var buildingOccupation = function(subslots){
	
	var overcrowded = false;
	var occupation=[];
	var names=[];
	var currentSlots=[];
	var Sampling =  buildSampling(subslots)
	var prevUsersList = [];
	var usersList = [];	
	var usersName = [];	
	var prevUsersName = [];
	var k=0;
	//for each sampling value (start or end of a slot in the slot list)
	while(k<Sampling.length){
		//console.log(Sampling[k]);
		while(subslots.length>0 && parseInt(subslots[0].start,10) === parseInt(Sampling[k],10) ){
			currentSlots.push(subslots.shift());}
		//We remove all the slots ending at this hour
		for (var j=currentSlots.length-1;j>=0;j--) {
			if(parseInt(currentSlots[j].end,10)=== parseInt(Sampling[k],10)){
				currentSlots.splice(j,1);}
		}
		//transferring the userList to test if the user leaves or enters the building
		prevUsersList = usersList;		
		prevUsersName = usersName.filter(function(obj){return parseInt(obj.state,10)>=0;});			
		//reinitiliazing at each value, not optimal but much simpler
		usersList = [];
		usersName = [];
		outgoingUsers = [];
		//Counting all the unique people in the currentslots
		for(var j=0;j<currentSlots.length;j++){
			//console.log(parseInt(currentSlots[j].ref_user,10));
			if(usersList.indexOf(parseInt(currentSlots[j].ref_user,10)) === -1){
				usersList.push(parseInt(currentSlots[j].ref_user,10));	
				var prevIndix = prevUsersList.indexOf(parseInt(currentSlots[j].ref_user,10));
				//console.log(prevIndix);
				if(prevIndix === -1){
					usersName.push({'name':currentSlots[j].prenom+' '+currentSlots[j].nom,'state':1,'id_user':currentSlots[j].ref_user} );
				}else{
					usersName.push({'name':currentSlots[j].prenom+' '+currentSlots[j].nom,'state':0,'id_user':currentSlots[j].ref_user} );
					prevUsersList.splice(prevIndix,1);
					prevUsersName.splice(prevIndix,1);					
				}
			}		
		}
		for (var l=0;l<prevUsersName.length;l++) {
			usersName.push({'name':prevUsersName[l].name,'state':-1,'id_user':prevUsersName[l].ref_user})}
		occupation.push({'x':Sampling[k],'y':usersList.length});
		names.push(usersName);
		k++;			
	}
	return [names,occupation];
} 



var addDaysRectangles=function(displayZone,paletteDays,weekDays){
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
}

var addBars = function (dayDims,scheduleZone,sliderHour) {
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
}

	