<?php include('includes/head.php'); ?>
<title>Test_map</title>
    <style>

      /* Make the chart container fill the page using CSS. */
      #chart {
        position: fixed;
        left: 0px;
        right: 0px;
        top: 0px;
        bottom: 0px;
      }
      #wrapper{display:table;}
      #colleft, #colright{display:table-cell;}

    </style>
  </head>
  <body>
<div id="wrapper">
<div id="colleft">

    <div id="vis"></div>
    <div id="visa"></div>
</div>
<div id="colright">

<form>
<input name="jour" id="jour">

</form>
aaaaaaaa
	 <div id="data"></div>
	 aaaaaaa
</div>	 
</div>

    <script>
    
DateToFormat = function (dateFormate, datetime) {
	return $.datepicker.formatDate(dateFormate, datetime);}
    
FormatToDate = function  (dateFormate, datetime){
	return $.datepicker.parseDate(dateFormate,  datetime);}     
    
    
    
$( "#jour").datepicker({
	dateFormat: "dd-mm-yy",	
gotoCurrent: true,
dayNamesMin: [ "Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa" ] ,
monthNames: [ "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre" ],
monthNamesShort: [ "Jan", "Fév", "Mar", "Avr", "Mai", "Juin", "Juil", "Aoû", "Sep", "Oct", "Nov", "Déc" ],
changeMonth: true,
changeYear: true,
firstDay: 1, 
showOtherMonths: true,
numberOfMonths:2,

/*onSelect: function(date)
{
	display_edt(date);
	$("#add_jour").attr("href","oral_add.php?jour="+date);
	$("#jour"+suffixe).trigger("change");
	//plop 
	},*/        
});
$("#jour").datepicker('setDate', new Date());	

    
      // load the external svg from a file
      //d3.xml("maps/M6Rdc.svg",  function(xml) {
      d3.xml("maps/M6R1.svg",  function(xml) {
      var importedNode = document.importNode(xml.documentElement, true);
      d3.select("div#vis")
        .each(function() {
          this.appendChild(importedNode);
        })
        // inside of our d3.xml callback, call another function
        // that styles individual paths inside of our imported svg
        styleImportedSVG()
       
      });
      
      d3.xml("maps/M6Rdc.svg",  function(xml) {
      var importedNode = document.importNode(xml.documentElement, true);
      d3.select("div#visa")
        .each(function() {
          this.appendChild(importedNode);
        })
        // inside of our d3.xml callback, call another function
        // that styles individual paths inside of our imported svg
        styleImportedSVG()
       
      });
      

      function styleImportedSVG () {
        d3.selectAll("[id^='M6']")
        .style('pointer-events','fill')
         .on('mouseenter', function() {
         	//console.log('mousein');
         	//console.log(this);
            d3.select(this)
            .style('fill','red')
            .style('fill-opacity',0.1);
            var idRoom=d3.select(this).attr('id').replace('M6','M6.');
            var date=DateToFormat("yy-mm-dd",$( "#jour").datepicker( "getDate" ));
            $('#data').text(idRoom+' '+date)
          })
          .on('mouseleave', function() {
            //console.log('mouseout');
            d3.select(this)
              .style('fill','none')
              .style('fill-opacity',1);
          });
          d3.selectAll("text").style('pointer-events','none')
      }
    </script>
  </body>
</html>