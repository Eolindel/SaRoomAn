$(document).ready(function() {

autocompleteField('building','includes/buildings.php');
autocompleteField('floor','includes/floors.php');

//check if the mandatory fields are filled or not.
mandatoryFields(['building','floor']);







});