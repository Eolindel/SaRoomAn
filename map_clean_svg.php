<?php include('includes/head.php'); ?>
<title>Liste des pièces dans la base de données</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(5))){
	//Loading file entirely before doing some replace operations
	$lineFile=file_get_contents('maps/M6Rdc.svg');
	$lineFile=preg_replace('/^\s</','<',$lineFile);
	$lineFile=preg_replace('/\n\s*id="text[^"]*"/m','',$lineFile);
	$lineFile=preg_replace('/\n\s*id="rect[^"]*"/m','',$lineFile);
	$lineFile=preg_replace('/\n\s*id="path[^"]*"/m','',$lineFile);
	$lineFile=preg_replace('/\n\s*id="g[^"]*"/m','',$lineFile);
	$lineFile=preg_replace('/\n\s*inkscape:connector-curvature="0"/m','',$lineFile);
	$lineFile=preg_replace('/\n\s*(style="[^"]*")/m',' $1',$lineFile);
	$lineFile=preg_replace('/\n\s*(d="[^"]*")/m',' $1',$lineFile);
	$lineFile=preg_replace('/\n\s*(id="[^"]*")/m',' $1',$lineFile);
	$lineFile=preg_replace('/\n\s*(x="[^"]*")/m',' $1',$lineFile);
	$lineFile=preg_replace('/\n\s*(y="[^"]*")/m',' $1',$lineFile);
	$lineFile=preg_replace('/\n\s*(transform="[^"]*")/m',' $1',$lineFile);
	$lineFile=preg_replace('/\n\s*(class="[^"]*")/ms',' $1',$lineFile);
	$lineFile=preg_replace('/<metadata>.*<\/metadata>/ms','',$lineFile);
	$lineFile=preg_replace('/<sodipodi:namedview.*<\/sodipodi:namedview>/ms','',$lineFile);
	
	$lineFile=str_replace('stroke-width:0.99999994;','stroke-width:2;',$lineFile);
	$lineFile=str_replace('stroke-width:1;','stroke-width:2;',$lineFile);
	$lineFile=str_replace('stroke-width:1px;','stroke-width:2;',$lineFile);
	$lineFile=str_replace('stroke-width:1.25','stroke-width:2',$lineFile);
	$lineFile=str_replace('fill:#a500dd;','fill:#a500dd;font-weight:bold;',$lineFile);
	
	$lineFile=str_replace('stroke-linecap:butt;','',$lineFile);
	$lineFile=str_replace('stroke-miterlimit:4;','',$lineFile);
	$lineFile=str_replace('stroke-linejoin:miter;','',$lineFile);
	$lineFile=str_replace('stroke-dasharray:none;','',$lineFile);

	file_put_contents('maps/M6Rdc-cleaned.svg', $lineFile);
}
?>
<?php include('includes/foot.php'); ?>  