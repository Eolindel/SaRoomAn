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
	"includes/send_worldcat.php",
	//{'q':ref_principale}, 
	function ( data ) {

	var people=[]
		
		
		
var parser = new DOMParser();
var dom = parser.parseFromString (data, "text/html");


var iterator=dom.evaluate('//*[@id="annuaire-table"]/table/tbody/tr', dom, null, XPathResult.UNORDERED_NODE_ITERATOR_TYPE, null)
//var iterator = document.evaluate('//phoneNumber', documentNode, null, XPathResult.UNORDERED_NODE_ITERATOR_TYPE, null );

try {
  var thisNode = iterator.iterateNext();
  
  while (thisNode) {
    console.log( thisNode.textContent );
    thisNode = iterator.iterateNext();
  }	
}
catch (e) {
  console.log( 'Erreur : L\'arbre du document a été modifié pendant l\'itération ' + e );
}

//getElementByXpath(dom,'//*[@id="bibdata"]/h1').stringValue;
//*[@id="annuaire-table"]/table

//console.log(book)




		*/		
	}, "html");



};
 
 });
