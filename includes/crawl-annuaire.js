$(document).ready(function() {



(function(DOMParser) {  
    "use strict";  
    var DOMParser_proto = DOMParser.prototype  
      , real_parseFromString = DOMParser_proto.parseFromString;

    // Firefox/Opera/IE throw errors on unsupported types  
    try {  
        // WebKit returns null on unsupported types  
        if ((new DOMParser).parseFromString("", "text/html")) {  
            // text/html parsing is natively supported  
            return;  
        }  
    } catch (ex) {}  

    DOMParser_proto.parseFromString = function(markup, type) {  
        if (/^\s*text\/html\s*(?:;|$)/i.test(type)) {  
            var doc = document.implementation.createHTMLDocument("")
              , doc_elt = doc.documentElement
              , first_elt;

            doc_elt.innerHTML = markup;
            first_elt = doc_elt.firstElementChild;

            if (doc_elt.childElementCount === 1
                && first_elt.localName.toLowerCase() === "html") {  
                doc.replaceChild(first_elt, doc_elt);  
            }  

            return doc;  
        } else {  
            return real_parseFromString.apply(this, arguments);  
        }  
    };  
}(DOMParser));

var getElementByXpath= function (dom,path) {
  return dom.evaluate(path, dom, null, XPathResult.STRING_TYPE, null);
}

var load_objet=function (){
	//ref_principale = parseInt($('input[name="id"]').val(),10);

$.get( 
	"includes/send_annuaire.php",
	//{'q':ref_principale}, 
	function ( data ) {

	var people=[]
		
		
		
var parser = new DOMParser();
var dom = parser.parseFromString (data, "text/html");


var iterator=dom.evaluate('//*[@id="annuaire-table"]/table/tbody/tr', dom, null, XPathResult.UNORDERED_NODE_ITERATOR_TYPE, null)
//var iterator = document.evaluate('//phoneNumber', documentNode, null, XPathResult.UNORDERED_NODE_ITERATOR_TYPE, null );

try {
	var arrPeople = [];
	
  var thisNode = iterator.iterateNext();
  while (thisNode) {
  	arrPeople.push(thisNode);
   thisNode = iterator.iterateNext();
  }	
}
////*[@id="parent-fieldname-office-06540040c08d4b2abe0aebb911f604a3"]
//*[@id="annuaire-contact"]/ul/li[7]/strong
////html/body/div[2]/div[3]/div[1]/div[2]/div/div/ul/li[4]/strong
catch (e) {
  console.log( 'Erreur : L\'arbre du document a été modifié pendant l\'itération ' + e );
}
//console.log(arrPeople)
////*[@id="annuaire-contact"]/ul/li[9]
var callPeople = function (URI,j,maxCalls){
	var innerIndex=j;
$.get( 
	"includes/send_page_annuaire.php",
	{'q':URI}, 
	function ( data1 ) {
		
		var parser = new DOMParser();
		var dom = parser.parseFromString (data1, "text/html");
		for (var l=0;l<10;l++) {
			var fieldValue = getElementByXpath(dom,'//*[@id="annuaire-contact"]/ul/li['+l+']/span').stringValue.replace(/^\s*/,'').replace(/\s*$/,'');
			var fieldName = getElementByXpath(dom,'//*[@id="annuaire-contact"]/ul/li['+l+']/strong/span').stringValue.replace(/^\s*/,'').replace(/\s*$/,'');
			var fieldName2 = getElementByXpath(dom,'//*[@id="annuaire-contact"]/ul/li['+l+']/strong').stringValue.replace(/^\s*/,'').replace(/\s*$/,'');
			//console.log(fieldName+' '+fieldValue);
			if(fieldName==='Office'){
				people[innerIndex].bureau=	fieldValue;		
			}else if(fieldName2==='E-mail'){
				people[innerIndex].mail=fieldValue+'@ens-lyon.fr';
			}
		}
		totCalls+=1;
		if(totCalls===maxCalls){
			//console.log(people);
			for (var k=0;k<people.length;k++) {
				$('#annuaire').append('<tr><td>'+people[k].prenom+'</td><td>'+people[k].nom+'</td><td>'+people[k].mail+'</td><td>'+people[k].tel+'</td><td>'+people[k].bureau+'</td><td>'+people[k].axe+'</td><td>'+people[k].statut+'</td></tr>')
			}
		}
		//console.log(totCalls);
	},//fin data1
	"html")	//fin get
}
var totCalls=0;
for(var i=1;i<arrPeople.length;i++){
	var fullName=arrPeople[i].cells[0].lastElementChild.innerHTML.replace(/^M\.\s*/,'').replace(/^Mme\s*/,'');
	//console.log(fullName)
	var matches= fullName.match(/^\s*([A-Z\s\-ÜÉÈÇÀÏËÑ]*)\s((\w|\s|é|è|ç|ñ|à|ï|ü|ë|\-)*)$/);
	var index=people.push({'prenom':matches[2],
					'nom':matches[1],
					'lien':arrPeople[i].cells[0].children[0].href,
					'tel':arrPeople[i].cells[1].lastElementChild.innerHTML,
					'axe':arrPeople[i].cells[2].lastElementChild.innerHTML,
					'statut':arrPeople[i].cells[3].lastElementChild.innerHTML});
		index-=1;
	callPeople(people[index].lien,index,arrPeople.length-1);				
					

}
//console.log(people)
	
	}, "html");//fin function(data))



};//fin load_objet
 
load_objet(); 
 
 });


    /*var nom=dom.evaluate('/td', thisNode, null, XPathResult.STRING, null);
    var thatNode= nom.iterateNext();
    while (thatNode) {
    	console.log(thatNode);
    	console.log(thatNode.textContent);
    	thatNode = nom.iterateNext();
    }
    //console.log(thisNode);
	 //console.log(nom);
	 //console.log('a');*/
