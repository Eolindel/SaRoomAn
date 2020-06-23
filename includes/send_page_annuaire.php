<?php
header("Content-Type: text/plain; charset=utf-8", true);
//article={'id':ref_principale,'title':'', 'authors':'', 'abstract':'', 'annee':'', 'mois':'', 'volume':'', 'numero':'', 'pages':'', 'numberOfPages':''};
$q = urlencode(htmlentities($_GET['q']));


function getURLContent($url){
    $doc = new DOMDocument;
    $doc->preserveWhiteSpace = FALSE;
    @$doc->loadHTMLFile($url);
    return $doc->saveHTML();
}

echo getURLContent($q);

?>

