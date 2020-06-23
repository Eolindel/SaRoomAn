

var autocompleteField=function(field,file){

    var cache = {};
$( "#"+field ).autocomplete({
      source: function( request, response ) {
        var term = request.term;
        if ( term in cache ) {
          response( cache[ term ] );
          return;
        }
 
        $.getJSON( file, request, function( data, status, xhr ) {
        	var key=$( "input[id='"+field+"']" ).val();
          cache[ term ] = data;
         var sorted=data.sort(function(a,b){
    if(a.includes(key) && !b.includes(key)){
        return -1;
    }else{
    		return 1;}
    
} );  
         response(sorted);
        });
      },
      minLength: 2,
    });


}

//check if the mandatory fields are filled or not.
var mandatoryFields= function(Fields){
	$('#missingfield').hide();
	$('input[type="submit"]').hide();
	var fieldCat='';
	for (var j=0;j<Fields.length;j++) {
		fieldCat+='input[id="'+Fields[j]+'"],';
	}
	fieldCat=fieldCat.slice(0,-1);
	
	$(fieldCat).on("change keyup",function () {
			$('input[type="submit"]').hide();
			var notNull = false;
			for (var j=0;j<Fields.length;j++) {
				if( $('input[id='+Fields[j]+']').val()==='' ){
					//console.log(Fields[j]);
					notNull=true;}
					
			}
			if(notNull===false){
				$('input[type="submit"]').show();
				$('#missingfield').hide();
			}else {
				$('#missingfield').show();
			}	
	});
	
}





