$(document).ready(function() {
autocompleteField('building','includes/buildings.php');
autocompleteField('floor','includes/floors.php');


var mandFields=['building','floor','idSvg','officeName'];

mandatoryFields(mandFields);


$('input, select','#otherInputs').on('change',function(){
	$('input[name="building"]').trigger('change');
})


//mapDisplay


var highlightRoom=function () {
	if ($('input[id="building"]').val()!=='' && $('input[id="floor"]').val()!=='' && $('input[id="idSvg"]').val()!=='') {
		var file=$('input[id="building"]').val()+$('input[id="floor"]').val()+'.svg';
		var idSvg=$('input[id="idSvg"]').val();
		$.get("maps/"+file)
	    .done(function() {
	    		d3.select('#mapDisplay').select('.highlighted')
	    		 .classed("highlighted", false)
	    		 .style('fill','none')
	         .style('fill-opacity',1); 
				d3.select('#mapDisplay').select('[id="'+idSvg+'"]')
	         .style('fill','red')
	         .style('fill-opacity',0.3)
	         .classed("highlighted", true);    		    		
	    })   
	}
}

var loadMap=function(){

if ($('input[id="building"]').val()!=='' && $('input[id="floor"]').val()!=='') {
	var file=$('input[id="building"]').val()+$('input[id="floor"]').val()+'.svg';
	$.get("maps/"+file)
    .done(function() { 
		$('#mapDisplay').html('');
		d3.xml("maps/"+file,  function(xml) {
	      var importedNode = document.importNode(xml.documentElement, true);
	      d3.select('#mapDisplay')
	        .each(function() {
	          this.appendChild(importedNode);
	      });		
		})
		highlightRoom();		
		
    }).fail(function() { 
    		$('#mapDisplay').html('');
    		$('#mapDisplay').append('<p class="warning">This combination of floor and building does not correspond to a specific svg file. Please upload it first.</p>')
    })	
	}
}

loadMap();

$('input[id="building"],input[id="floor"]').on("change",loadMap);
$('input[id="idSvg"]').on("change paste keyup",highlightRoom);



});