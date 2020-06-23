$(document).ready(function() {


autocompleteField('team','includes/teams.php');
autocompleteField('statut','includes/statut.php');

mandatoryFields(['mail','prenom','nom']);

$('input, select','#otherInputs').on('change',function(){
	$('input[name="mail"]').trigger('change');
})

capitalize = function (string) {
    return string.replace(/^./, Function.call.bind("".toUpperCase));
}    

	//l'identifiant est le préfixe de l'adresse mail.
	$("#mail").on("change",function () {
		var mailSplitted=$("#mail").val().split('@');
		var identite=mailSplitted[0].split('.');
				
		if($("#login").val()===""){
			$("#login").val(mailSplitted[0]);}
			
		if($("#prenom").val()===""){
			$("#prenom").val(capitalize(identite[0]) )}
		if($("#nom").val()===""){
			$("#nom").val(capitalize(identite[1]) )}		
			
			
		
	})
	
	
	

});