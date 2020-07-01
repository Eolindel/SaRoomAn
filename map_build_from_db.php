<?php include('includes/head.php'); ?>
<title>Liste des pièces dans la base de données</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(5))){
$line='';
$ok=0;
$submit=array();
	if(isset($_POST['completefloor'])){
		$floor=explode('%',$_POST['completefloor']);	

		$submit['building']=$floor[0];
		$submit['floor']=$floor[1];
		$ok=1;	
	}elseif(isset($_POST['floor']) AND isset($_POST['building'])) {
		$submit['building']=$_POST['building'];
		$submit['floor']=$_POST['floor'];	
		$ok=1;
	}

if($ok==1){
$roomSize=array("height"=>40,"width"=>29);
	
	
		$reponse=$bdd->prepare("SELECT `id_room`, `building`, `floor`, `idSvg`, `officeName`, `commonName`, `surface`, `telephone1`, `telephone2`, `responsable`, `places`, `max` FROM `rooms` WHERE building=:building AND floor=:floor ORDER BY officeName");
		$reponse->execute($submit);
		$linesFile = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<svg
   xmlns:svg="http://www.w3.org/2000/svg"
   xmlns="http://www.w3.org/2000/svg"
   height="450"
   width="600"
   viewBox="0 0 600 450"
   version="1.1"
   pagecolor="#ffffff"
   bordercolor="#666666"
   borderopacity="1"
   objecttolerance="10"
   gridtolerance="10"
   guidetolerance="10"
>';
//floor and building
$linesFile.='  <text style="font-weight:bold;font-size:32px;font-family:sans-serif;text-align:start;text-anchor:start;opacity:1;fill:#000000;fill-opacity:1;" x="10" y="-20">'.$submit['building'].' '.$submit['floor'].'</text>';
//Lift
$linesFile.='<g id="lift" >
        <rect style="opacity:1;fill:#f2f2f2;fill-opacity:1;stroke:#000000;stroke-width:2;stroke-opacity:1" width="9.7607441" height="21.068747" x="38.046383" y="316.40369" ry="0.70212728" />
        <rect style="opacity:1;fill:#f2f2f2;fill-opacity:1;stroke:#000000;stroke-width:2;stroke-opacity:1" width="9.7607441" height="21.068747" x="47.807213" y="316.40369" ry="0.70212728" />
        <path d="m 42.150332,322.77047 v 11.10725 h 1.552197 v -11.10725 z" style="color:#000000;solid-color:#000000;solid-opacity:1;vector-effect:none;fill:#000000;fill-opacity:1;stroke-width:1.5163238;stroke-opacity:1;" />
        <path d="m 42.926805,324.63535 1.552423,1.55323 -1.552423,-5.43633 -1.552424,5.43633 z" style="fill:#000000;fill-opacity:1;stroke:#000000;stroke-width:0.30326477pt;stroke-opacity:1" />
        <path d="m 51.911542,319.99866 v 11.10524 h 1.5522 v -11.10524 z" style="color:#000000;solid-color:#000000;solid-opacity:1;vector-effect:none;fill:#000000;fill-opacity:1;stroke-width:1.5163238;stroke-opacity:1;" />
        <path d="m 52.687546,329.24082 -1.552424,-1.55323 1.552424,5.43633 1.552423,-5.43633 z" style="fill:#000000;fill-opacity:1;stroke:#000000;stroke-width:0.30326477pt;stroke-opacity:1" />
      </g>';
//Toilet
    $linesFile.='<g  id="toilet">
    <rect style="opacity:1;fill:#0000ff;fill-opacity:1;stroke:none;stroke-width:2;stroke-dasharray:1.00031508, 1.00031508;stroke-opacity:1" width="23.068342" height="23.069431" x="79.350273" y="315.40335" ry="4.6428037" />
    <text style="font-variant:normal;font-weight:bold;font-size:10.47148037px;font-family:sans-serif;fill:#ffffff;fill-opacity:1;stroke:none;stroke-width:1.00031507" x="81.445389" y="330.74213">WC</text>
</g>';    

//Wheelchair
    $linesFile.='    <g id="wheelchair">
      <path style="fill:#000000;stroke-width:1.00031495" d="m 159.42259,322.0057 c 0.72843,-0.0673 1.29437,-0.69485 1.29437,-1.42891 0,-0.7901 -0.64439,-1.43453 -1.43446,-1.43453 -0.79007,0 -1.43443,0.64443 -1.43443,1.43453 0,0.24095 0.0672,0.4875 0.17928,0.69484 l 0.51111,7.19234 5.26406,10e-4 2.15909,5.05911 2.83472,-1.11176 -0.43896,-1.0453 -1.58643,0.57269 -2.08905,-4.82318 -4.89442,0.0329 -0.0672,-0.91085 3.54318,10e-4 v -1.34771 l -3.67833,-10e-4 z" />
      <path style="fill:#000000;stroke-width:1.00031495" d="m 164.71685,332.3205 c -0.88847,1.75638 -2.74807,2.91351 -4.73163,2.91351 -2.91337,0 -5.28952,-2.37626 -5.28952,-5.28977 0,-2.04567 1.23974,-3.94666 3.08967,-4.78935 l 0.1197,1.56232 c -1.09418,0.68952 -1.76869,1.93365 -1.76869,3.23774 0,2.11355 1.72372,3.83733 3.83714,3.83733 1.93355,0 3.58233,-1.48394 3.80717,-3.38763 z" />
      <rect ry="4.542141" y="315.9035" x="150.46193" height="22.569254" width="22.568195" style="opacity:1;fill:none;fill-opacity:1;stroke:#000000;stroke-width:2;stroke-opacity:1" />
    </g>';  
//Turning stairs
    $linesFile.='<g id="stairs1">
      <path transform="rotate(-90)" d="m -326.67917,219.71404 a 10.534645,10.441067 0 0 1 9.68592,6.99208 10.534645,10.441067 0 0 1 -3.18035,11.45039 10.534645,10.441067 0 0 1 -11.93183,1.09393 10.534645,10.441067 0 0 1 -5.24564,-10.67787"  style="opacity:1;fill:none;fill-opacity:1;stroke:#000000;stroke-width:2;stroke-opacity:1" />
      <path d="m 237.68868,319.64701 -7.53673,7.29067 -10.4298,-0.48885" style="fill:none;stroke:#000000;stroke-width:0.50015754;stroke-opacity:1" />
      <path d="m 230.15195,326.93768 9.47549,-4.42465" style="fill:none;stroke:#000000;stroke-width:0.50015754;stroke-opacity:1" />
      <path d="m 240.5453,325.93161 -10.39335,1.00607 10.1247,2.57365" style="fill:none;stroke:#000000;stroke-width:0.50015754;stroke-opacity:1" />
      <path d="m 238.93915,332.62747 -8.7872,-5.68979 6.32715,8.38004" style="fill:none;stroke:#000000;stroke-width:0.50015754;stroke-opacity:1" />
      <path d="m 230.15195,326.93768 3.14322,10.04595" style="fill:none;stroke:#000000;stroke-width:0.50015754;stroke-opacity:1" />
      <path d="m 220.45587,323.02975 9.69608,3.90793 -7.82045,-6.97983" style="fill:none;stroke:#000000;stroke-width:0.50015754;stroke-opacity:1" />
      <path d="m 230.15195,326.93768 -4.89992,-9.30256" style="fill:none;stroke:#000000;stroke-width:0.50015754;stroke-opacity:1" />
      <path d="M 230.15195,326.93768 228.42926,316.5474" style="fill:none;stroke:#000000;stroke-width:0.50015754;stroke-opacity:1" />
      <path d="m 232.03031,316.57489 -1.87836,10.36279 5.1527,-9.16247" style="fill:none;stroke:#000000;stroke-width:0.50015754;stroke-opacity:1" />
      <path d="m 230.15195,326.93768 -1.44677,10.51693" style="fill:none;stroke:#000000;stroke-width:0.50015754;stroke-opacity:1" />
    </g>';  
//straight stairs
    $linesFile.='<g id="stairs2">
      <path d="m 282.39377,315.40335 v 27.28262" style="fill:none;stroke:#000000;stroke-width:2;stroke-opacity:1" />
      <path d="M 282.39377,316.30816 H 253.84241" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="M 282.39377,319.13849 H 253.84241" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="M 282.39377,321.96883 H 253.84241" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="M 282.39377,324.79917 H 253.84241" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="M 282.39377,327.62951 H 253.84241" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="M 282.39377,330.45985 H 253.84241" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="M 282.39377,333.29018 H 253.84241" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="M 282.39377,336.12052 H 253.84241" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="M 282.39377,338.95086 H 253.84241" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="M 282.39377,341.7812 H 253.84241" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="M 253.44623,342.68597 V 315.40335" style="fill:none;stroke:#000000;stroke-width:2;stroke-opacity:1" />
    </g>';  
// headpin stairs
    $linesFile.='<g id="stairs3">
      <path d="m 292.31741,353.13454 h 44.21888" style="fill:none;stroke:#000000;stroke-width:2;stroke-opacity:1" />
      <path d="m 307.86505,333.74011 h 27.28137" style="fill:none;stroke:#000000;stroke-width:2;stroke-opacity:1" />
      <path d="m 309.55768,333.74011 v 19.39443" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="m 312.38785,333.74011 v 19.39443" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="m 315.21805,333.74011 v 19.39443" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="m 318.04826,333.74011 v 19.39443" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="m 320.87847,333.74011 v 19.39443" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="m 323.70868,333.74011 v 19.39443" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="m 326.53889,333.74011 v 19.39443" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="m 329.36915,333.74011 v 19.39443" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="m 332.19932,333.74011 v 19.39443" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="m 335.02952,333.74011 v 19.39443" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="M 336.53629,333.78201 H 309.25492" style="fill:none;stroke:#000000;stroke-width:2;stroke-opacity:1" />
      <path d="M 335.02952,333.78201 V 316.40366" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="M 332.19932,333.78201 V 316.40366" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="M 329.36915,333.78201 V 316.40366" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="M 326.53889,333.78201 V 316.40366" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="M 323.70868,333.78201 V 316.40366" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="M 320.87847,333.78201 V 316.40366" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="M 318.04826,333.78201 V 316.40366" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="M 315.21805,333.78201 V 316.40366" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="M 312.38785,333.78201 V 316.40366" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="M 309.55764,333.78201 V 316.40366" style="fill:none;stroke:#000000;stroke-width:0.50015748;stroke-opacity:1" />
      <path d="m 292.31741,316.40366 h 44.21888" style="fill:none;stroke:#000000;stroke-width:2;stroke-opacity:1" />
      <path d="m 293.31775,315.40335 v 38.73151" style="fill:none;stroke:#000000;stroke-width:2;stroke-opacity:1" />
    </g>';  
//Double doors
    $linesFile.='<g id="door2">
      <path d="m 375.20446,326.74796 a 11.01741,11.017929 0 0 0 11.01743,-11.01793 h -11.01743 z" style="fill:none;fill-opacity:1;stroke:#000000;stroke-width:0.65334922" />
      <path d="M 375.20446,326.74796 V 315.73003" style="fill:none;stroke:#000000;stroke-width:1.55798674;stroke-opacity:1" />
      <path d="m 390.38336,319.87166 a 4.161502,4.1416313 0 0 1 -4.16151,-4.14163 h 4.16151 z" style="fill:none;fill-opacity:1;stroke:#000000;stroke-width:0.65334922" />
      <path d="m 390.38336,319.87166 v -4.14163" style="fill:none;stroke:#000000;stroke-width:1.55798674;stroke-opacity:1" />
    </g>';  
//Simple Door
    $linesFile.='<g id="door"
       inkscape:label="#g1245">
      <path d="m 357.973,326.74705 a 11.01741,11.017929 0 0 0 11.01744,-11.01792 H 357.973 Z" style="fill:none;fill-opacity:1;stroke:#000000;stroke-width:0.65334928;stroke-dasharray:none" />
      <path d="M 357.973,326.74705 V 315.72913" style="fill:none;stroke:#000000;stroke-width:1.55798674;stroke-opacity:1" />
    </g>';    
//empty room
    $linesFile.='<path id="room" d="m 456.93977,348.61121 h -40.92699 v -32.20752 h 40.92699 z" style="opacity:1;fill:none;fill-opacity:1;stroke:#000000;stroke-width:2;stroke-opacity:1" />';  
//Wall
    $linesFile.='<path style="fill:none;stroke:#000000;stroke-width:2;stroke-opacity:1" d="m 487.07013,318.0266 v 42.42476" id="wall" />';  
//Big arrow
    $linesFile.='<g id="bigArrow" >
      <path style="color:#000000;solid-color:#000000;solid-opacity:1;vector-effect:none;fill:#000000;fill-opacity:1;stroke-width:7.40233135;stroke-opacity:1;" d="m 532.60177,308.83934 v 32.5653 h 8.15331 v -32.5653 z" />
      <path style="fill:#000000;fill-opacity:1;stroke:#000000;stroke-width:1.48046646pt;stroke-opacity:1" d="m 536.67792,331.61966 -8.15286,-8.15327 8.15286,28.53644 8.15289,-28.53644 z" />
    </g>';  
   
    
                    
		$row=0;
		$col=0;
		$j=0;
		while($donnees=$reponse->fetch()) {
			//Computing the translation to apply to the room to distribute them
			$row=floor($j/15);
			$col=$j%15;			
			if(preg_match('/'.$donnees['building'].'[\s\.\-]?(.*)/', $donnees['officeName'], $nameReduce)){
				$donnees['officeName']=	$nameReduce[1];
			}
			$linesFile .='
			<g class="room" transform="translate('.($col*($roomSize["width"]+10)).','.($row*($roomSize["height"]+5)).')">
			  <path style="opacity:1;fill:none;fill-opacity:1;stroke:#000000;stroke-width:2;stroke-opacity:1" d="M 0 0 h '.$roomSize["width"].' v '.$roomSize["height"].' h -'.$roomSize["width"].' Z" id="'.$donnees['idSvg'].'"/>
			  <text y="'.($roomSize["width"]/2).'" x="0" style="font-size:14px;fill:#a500dd;fill-opacity:1;stroke:none;font-weight:bold;">'.$donnees['officeName'].'</text>
			  <g class="door" transform="translate(10,30)">
			    <path style="fill:none;fill-opacity:1;stroke:#000000;stroke-width:0.65314341" d="m 0,0 a 10 10, 0, 0,1, 10 10 l -10 0   z"/>
			    <path style="fill:none;stroke:#000000;stroke-width:1.55749607;stroke-opacity:1" d="m 0,0 v 10" />
			  </g>
			</g>';
			$j++;
		}
$linesFile .='</svg>';		
		$filename= 'maps_build/'.$submit['building'].$submit['floor'].'-'.date ('Y-m-d-H-i').'.svg';
		if(!file_exists($filename)){
			file_put_contents($filename, $linesFile);
			$line.='<h1>The draft of the map was created in '.$filename.'</h1>';
		}else{
			$line.='<h1>There is already a file for this floor and building, clean up the maps_build directory</h1>';		
		}




}else{
	$line.='You did not enter a floor and building';
}
		echo $line;
}
?>
<?php include('includes/foot.php'); ?>  