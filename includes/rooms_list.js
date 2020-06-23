$(document).ready(function() {
	

	





//Style to apply to highlighted rooms : Here, red background when hovering the column with the name of the office.
   function styleImportedSVG () {
     d3.selectAll(".troom")
     .style('pointer-events','fill')
      .on('mouseenter', function() {
      	var idSvg=d3.select(this).attr('data-room');
			//console.log(idSvg);
         d3.select('[id="'+idSvg+'"]')
         .style('fill','red')
         .style('fill-opacity',0.3);
       })
       .on('mouseleave', function() {
       	var idSvg=d3.select(this).attr('data-room');
         d3.select('[id="'+idSvg+'"]')
           .style('fill','green')
           .style('fill-opacity',0.08);
       }).each(function () {
       	var idSvg=d3.select(this).attr('data-room');
       d3.select('[id="'+idSvg+'"]')
         .style('fill','green')
         .style('fill-opacity',0.08).classed("clickable", true);
       });
       d3.selectAll("text").style('pointer-events','none')
       
       
       
       
       d3.selectAll(".clickable").on('click', function() {
       	var idSvg=d3.select(this).attr('id');
       	console.log($('#rowroom'+idSvg).position());
			//$('#rowroom'+idSvg).closest('.rightCol').animate({ scrollTop: $('#rowroom'+idSvg).position().top}, 500);
			$('#rowroom'+idSvg).get(0).scrollIntoView();
			highlightElem('#rowroom'+idSvg);
       })
	}


//Import the svg file to make it changeable by d3.js	
	$(".innermap").each(function () {
		var file=$(this).data('map');
		var that=$(this);
      d3.xml("maps/"+file,  function(xml) {
      var importedNode = document.importNode(xml.documentElement, true);
      d3.select(that[0])
        .each(function() {
          this.appendChild(importedNode);
        })
        //apply styling
        styleImportedSVG();
       
      });	
	
	})


});