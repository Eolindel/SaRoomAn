$(document).ready(function() {
	
var paletteColors=['#ffffff','#3288bd', '#66c2a5', '#abdda4','#e6f598','#fee08b','#fdae61', '#f46d43', '#d53e4f'];
var minLabels=['',1,2,3,4,5,6,11,'>21'];
var maxLabels=['',1,2,3,4,5,10,20,''];


var colorValue=function(val){
	if(val===0){
		return paletteColors[val];
	}else if(val<6){
		return paletteColors[val];
	}else if(val<11){
		return paletteColors[6];
	}else if(val<21){
		return paletteColors[7];
	}else{
		return paletteColors[8];
	}
}

		//start pallette creation

var createLabel=function (svg) {
	var dims={'width':paletteColors.length*30,'height':50};
   svg.attr("width", dims.width)
   .attr("height", dims.height)
   .attr("viewBox", [0, 0, dims.width, dims.height])
   .style("overflow", "visible")
   .style("display", "block");
   for (var k=0;k<paletteColors.length;k++) {
   	svg.append('rect').attr("width","30px")
			.attr("height","15px")
			.attr("fill",paletteColors[k])
			.attr("x",(k*30)+"px")
			.attr("y","15px")
		svg.append('text')
			.attr("x",(k*30+15)+"px")
			.attr("y",function(){
				if(k%2===0){
					return "42px";
				}else{
					return "12px";}					
				})
			.attr("text-anchor", "middle")
			.attr("font-size","0.8em")
			.text(function(){
				if(minLabels[k]===maxLabels[k]){
				 return minLabels[k];
				}else{
					return minLabels[k]+'-'+maxLabels[k];}
			})
	}//end label creation
}





//Style to apply to highlighted rooms : Here, red background when hovering the column with the name of the office.
   function styleImportedSVG () {
     d3.selectAll(".troom")
     .style('pointer-events','fill')
     .each(function(){
     	   var idSvg=d3.select(this).attr('data-room');
     	   var threshold=parseInt(d3.select(this).select('.threshold').text());
         d3.select('[id="'+idSvg+'"]')
         .style('fill',colorValue(threshold))
         .style('fill-opacity',0.8);
     }).on('mouseenter', function() {
      	var idSvg=d3.select(this).attr('data-room');
			//console.log(idSvg);
         d3.select('[id="'+idSvg+'"]')
         .style('fill','red')
         .style('fill-opacity',1);
       })
       .on('mouseleave', function() {
     	   var idSvg=d3.select(this).attr('data-room');
     	   var threshold=parseInt(d3.select(this).select('.threshold').text());
         d3.select('[id="'+idSvg+'"]')
         .style('fill',colorValue(threshold))
         .style('fill-opacity',0.8);
       });
       d3.selectAll("text").style('pointer-events','none')
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
				
		that.append('<div class="colorLabel"></div>');
	
		var svg=d3.select(that[0]).select('.colorLabel').append('svg');		
		createLabel(svg);
	})


});