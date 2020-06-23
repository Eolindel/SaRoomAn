<?php include('includes/head.php'); ?>
<title>See the schedule of a given person</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5))){
	$object=array();
	if(isset($_GET['id_user'])){
		$object['id_user']=intval($_GET['id_user']);
	}else{
		$object['id_user']=$_SESSION['id_user'];}	
		
		
	if(isset($_GET['date'])){
		$object['date']=$_GET['date'];}


	$line='';	
//Loading data about the person
	$reponse=$bdd->prepare("SELECT o.officeName AS office,o.id_room AS oId,o.idSvg AS oSvg,
											 w.officeName AS workplace,w.id_room AS wId,w.idSvg AS wSvg, u.nom,u.prenom 
											 FROM `roomusers` AS u LEFT JOIN `rooms` AS o ON o.id_room=u.ref_office LEFT JOIN `rooms` AS w ON w.id_room=u.ref_workplace WHERE u.active=1 AND u.id_user=:id_user");
	$reponse->execute(array("id_user"=>$object['id_user'])); 	
	$user=$reponse->fetch(PDO::FETCH_ASSOC);
	if($user['oId']!=0 AND $user['oId']!=''){
		$line.='<span id="office" data-idsvg="'.$user['oSvg'].'" data-id="'.$user['oId'].'" data-human="'.$user['office'].'"></span>';
	}	
	if($user['wId']!=0 AND $user['wId']!=''){
		$line.='<span id="workplace" data-idsvg="'.$user['wSvg'].'" data-id="'.$user['wId'].'" data-human="'.$user['workplace'].'"></span>';
	}	
	
	

	//Schedule part

	$line.='<h2 id="schedule" '.controlValues().'>See the schedule of '.$user['prenom'].' '.$user['nom'].'</h2>';


	$line.='<label for="date" class="label_court">date : </label>'.input_r('date', $object, 10).'<br>';	


	$line.='<div id="displayWeek" data-iduser="'.$object['id_user'].'"></div>';	


	$line.='<br>';
		$line.='<table id="SlotList">';
	$line.=th_slotWeek();
	$line.=th_Days();
	$line.='</table>';
	$line1='';
	$line2='';	
	
	$reponse=$bdd->query('SELECT * FROM `maps`');
	while($floor=$reponse->fetch()) {
		$line1.=checkbox_r($floor['building'].$floor['floor'],$floor['file'],$floor['building'].' '.$floor['floor'],array($floor['building'].$floor['floor']=>1),'floors');
		$line2.='<div id="'.str_ireplace('.svg', '',$floor['file']).'" data-map="'.$floor['file'].'" data-floor="'.$floor['floor'].'" data-building="'.$floor['building'].'" class="mapDisplay"> </div>';
	}

	$line.='<label for="" class="label_court2">Floors to display</label>'.$line1.'<br>'.$line2;	

	echo $line;	
	
/*

*/
	
	echo '<script type="text/javascript" src="includes/functions.js"></script><script type="text/javascript" src="includes/schedule_user.js"></script>';	
}
?>
<?php include('includes/foot.php'); ?>  