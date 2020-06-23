<?php include('includes/head.php'); ?>
<title>Build a template for a week</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5))){

	if(isset($_GET['id_template'])) {
		$object['id_template']=intval($_GET['id_template']);
		$reponse=$bdd->prepare("SELECT * FROM `template` WHERE id_template=:id_template");
		$reponse->execute($object);
		$template=$reponse->fetch();			
	}
	
	
	//Schedule part
	$days=['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
	$line='';
	$line.='<h2 id="template" data-template="'.$template['id_template'].'">Build template '.$template['name'].'</h2>';
	$line.='<p>This page is to build a template and is NOT a definitive schedule ! You must select a day and a room before being able to submit.</p>';	
	
	$line.='<div id="displayWeek"></div>';	
	
	$line.='<label for="day" class="label_court2">Day<sup>*</sup> : </label>';
	for($i=1;$i<7;$i++){
		$line.=radio_r('day', $i,$days[$i] , $object);
	}
	$line.='	

	<div class="twocolumns"> 
    <span class="vtop align500"><label for="value-hour" class="label_court2">Starting Hour<sup>*</sup> : </label>&nbsp;<span id="value-hour"></span> - <span id="value-end"></span> <label for="value-end" class="label_court3">: End<sup>*</sup></label>&nbsp;</span>
    <span id="slider-hour" class="vtop"></span>
    
    </div>
		<div class="twocolumns"> 
    <span class="vtop align500"><label for="value-length" class="label_court2">Length<sup>*</sup> : </label>&nbsp;<span id="value-length"></span></span>
    <span id="slider-length" class="vtop"></span>
    </div>  
 ';
 
 
	//Room part
 
 	$line.='<label for="room" class="label_court2">Room<sup>*</sup> :	</label>&nbsp;<span id="hroom"></span>&nbsp;<span id="room"></span><br>';
	$object['id_template']=intval($_GET['id_template']);
	$reponse=$bdd->prepare("SELECT o.officeName AS office,o.id_room AS oId,o.idSvg AS oSvg,
											 w.officeName AS workplace,w.id_room AS wId,w.idSvg AS wSvg 
											 FROM `roomusers` AS u LEFT JOIN `rooms` AS o ON o.id_room=u.ref_office LEFT JOIN `rooms` AS w ON w.id_room=u.ref_workplace WHERE u.active=1 AND u.id_user=:id_user");
	$reponse->execute(array("id_user"=>$_SESSION['id_user'])); 	
	$user=$reponse->fetch(PDO::FETCH_ASSOC);
	$line.='<label for="" class="label_court2">Shortcuts :</label>';
	if($user['oId']!=0 AND $user['oId']!=''){
		$line.=	radio_r('room','office',$user['office']. ' (Office)' , array());
		$line.='<span id="office" data-idsvg="'.$user['oSvg'].'" data-id="'.$user['oId'].'" data-human="'.$user['office'].'"></span>';
	}	
	if($user['wId']!=0 AND $user['wId']!=''){
		$line.=	radio_r('room', 'workplace',$user['workplace']. '(Workplace)' , array());
		$line.='<span id="workplace" data-idsvg="'.$user['wSvg'].'" data-id="'.$user['wId'].'" data-human="'.$user['workplace'].'"></span>';
	}
	$line.='<br>';
	$line.='<div id="warnings"></div>';		
	$line.='<a href="#content" class="submit" id="sendSlot" data-edit="0">Add this slot</a>';	

	
	$line1='';
	$line2='';	
	
	$reponse=$bdd->query('SELECT * FROM `maps`');
	while($floor=$reponse->fetch()) {
		$line1.=checkbox_r($floor['building'].$floor['floor'],$floor['file'],$floor['building'].' '.$floor['floor'],array($floor['building'].$floor['floor']=>1),'floors');
		$line2.='<div id="'.str_ireplace('.svg', '',$floor['file']).'" data-map="'.$floor['file'].'" data-floor="'.$floor['floor'].'" data-building="'.$floor['building'].'" class="mapDisplay"> </div>';
	}

	$line.='<label for="" class="label_court2">Floors to display</label>'.$line1.'<br>'.$line2;	
	$line.='<table id="SlotList">';
	$line.=th_slotWeek();
	$line.=th_Days();
	$line.='</table>';
	echo $line;	
	
/*

*/
	
	echo '<script type="text/javascript" src="includes/template_build.js"></script>';	
}
?>
<?php include('includes/foot.php'); ?>  