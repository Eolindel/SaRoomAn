$(document).ready(function() {
	
	///////////////////////////
	//////////////////////////
var roomList=[];

rights=allowedRights('#slot');
//Hide the last row of the table of slots
$('#rowDay7').hide();


$.ajax({
		    url: 'includes/send_slotFull.php',
   	 method: 'POST',
   	 data: {'id_slot':parseInt($('#id_slot').val(),10)},
  			//contentType: "application/json;charset=UTF-8",
   	 success: function(res) {
   	 	  	var res=JSON.parse(res.trim());
   	 		var deleteAllowed = false;
				if(rights >=res.valid){
					deleteAllowed = true
				}
				//console.log(res);
				addSlotToTableFull(res,deleteAllowed);  	 	
   	 	 }
   	 }
   	 );	




///SLOTS MANAGEMENT


	
	
	//////////////////////////
	//////////////////////////

	


});