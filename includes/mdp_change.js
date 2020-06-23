$(document).ready(function() {

$('#submit_mdp').hide();

$('input[name=password_1],input[name=password_2]').keyup(function (e){
	var pwd_1=$('input[name=password_1]').val();
	var pwd_2=$('input[name=password_2]').val();
	if(pwd_1 === pwd_2 && pwd_1!=='' && pwd_1.replace(/[^0-9]/g,"").length>0 && pwd_1.replace(/[^A-Z]/g,"").length>0 && pwd_1.length>=8){
		$('#submit_mdp').show();	
	}else{$('#submit_mdp').hide();}
	if(pwd_1.length<8){
		$('#plop_8').show();	
	}else{$('#plop_8').hide();}
	if(!pwd_1.replace(/[^0-9]/g,"").length>0){
		$('#plop_1').show();	
	}else{$('#plop_1').hide();}
	if(!pwd_1.replace(/[^A-Z]/g,"").length>0){
		$('#plop_A').show();	
	}else{$('#plop_A').hide();}
	if(pwd_1 !== pwd_2){
		$('#plop_diff').show();	
	}else{$('#plop_diff').hide();}
		
});
//
});
