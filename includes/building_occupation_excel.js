$(document).ready(function() {
	
	
var DateToFormat = function (dateFormate, datetime) {
	return $.datepicker.formatDate(dateFormate, datetime);}
    
var FormatToDate = function  (dateFormate, datetime){
	return $.datepicker.parseDate(dateFormate,  datetime);} 	
	
//sélecteur pour la date
$( "#date").datepicker({
	dateFormat: "dd-mm-yy",	
	gotoCurrent: true,
	changeMonth: true,
	changeYear: true,
	firstDay: 1, 
	showOtherMonths: true,
	numberOfMonths:2,
	onSelect: function(date){
		$("#date").trigger("change");}      
});	



	
//Date par défaut si la date est définie ou non
(function () {    	
	jour = $('input[name="date"]').val();
if (jour){
	$('input[name="date"]').datepicker( "option", "defaultDate", $('input[name="date"]').val());
} else {
	$('input[name="date"]').datepicker( "option", "defaultDate", new Date() );
	$('input[name="date"]').val(DateToFormat('dd-mm-yy', new Date()));
	//alert(DateFormate('dd-mm-yy', new Date()));
};
})();	

});