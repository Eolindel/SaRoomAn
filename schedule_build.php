<?php include('includes/head.php'); ?>
<title>Contribute to the full schedule</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5))){
	$object=array();
	if(isset($_GET['id_user'])){
		$object['id_user']=intval($_GET['id_user']);
	}else{
		$object['id_user']=$_SESSION['id_user'];
	}
	if(isset($_GET['date']) AND preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $_GET['date'])){
		$object['date']=$_GET['date'];
	}	

	
	$people=array();
	
	$reponse=$bdd->prepare("SELECT u.id_user,u.prenom,u.nom,o.officeName AS office,o.id_room AS oId,o.idSvg AS oSvg,
											 w.officeName AS workplace,w.id_room AS wId,w.idSvg AS wSvg 
											 FROM `roomusers` AS u LEFT JOIN `rooms` AS o ON o.id_room=u.ref_office LEFT JOIN `rooms` AS w ON w.id_room=u.ref_workplace WHERE u.active=1 AND u.id_user=:id_user");
	$reponse->execute(array("id_user"=>$object['id_user'])); 	
	$user=$reponse->fetch(PDO::FETCH_ASSOC);	
	$people[]=$user;

	if(in_array($_SESSION['roomStatus'],array(4,5))){ // adminsitrator and controllers can act on all users
			$reponse=$bdd->query("SELECT u.id_user,u.prenom,u.nom,o.officeName AS office,o.id_room AS oId,o.idSvg AS oSvg,
											 w.officeName AS workplace,w.id_room AS wId,w.idSvg AS wSvg 
											 FROM `roomusers` AS u LEFT JOIN `rooms` AS o ON o.id_room=u.ref_office LEFT JOIN `rooms` AS w ON w.id_room=u.ref_workplace WHERE u.active=1 AND u.roomStatus>0 ORDER BY nom");
			while($person=$reponse->fetch(PDO::FETCH_ASSOC)){
				$people[]=$person;} 		
	}else if(in_array($_SESSION['roomStatus'],array(3))){ // team heads can act on all people from their team
			$reponse=$bdd->prepare("SELECT u.id_user,u.prenom,u.nom,o.officeName AS office,o.id_room AS oId,o.idSvg AS oSvg,
											 w.officeName AS workplace,w.id_room AS wId,w.idSvg AS wSvg 
											 FROM `roomusers` AS u LEFT JOIN `rooms` AS o ON o.id_room=u.ref_office LEFT JOIN `rooms` AS w ON w.id_room=u.ref_workplace WHERE u.active=1   AND u.roomStatus>0 AND u.team=:team ORDER BY nom");
			$reponse->execute(array('team'=>$_SESSION['team']));								 
			while($person=$reponse->fetch(PDO::FETCH_ASSOC)){
				$people[]=$person;} 		
	}else if(in_array($_SESSION['roomStatus'],array(2))){ // Permanent people can act on all the persons under their supervision
			$reponse=$bdd->prepare("SELECT u.id_user,u.prenom,u.nom,o.officeName AS office,o.id_room AS oId,o.idSvg AS oSvg,
											 w.officeName AS workplace,w.id_room AS wId,w.idSvg AS wSvg 
											 FROM `roomusers` AS u LEFT JOIN `rooms` AS o ON o.id_room=u.ref_office LEFT JOIN `rooms` AS w ON w.id_room=u.ref_workplace WHERE u.active=1   AND u.roomStatus>0 AND (u.ref_responsable=:id_user OR u.id_user=:id_user) ORDER BY nom");
			$reponse->execute(array('id_user'=>$_SESSION['id_user']));								 
			while($person=$reponse->fetch(PDO::FETCH_ASSOC)){
				$people[]=$person;} 		
	}//and normal users can only act on themselves

	if(isset($_GET['id_slot'])){
		$reponse=$bdd->prepare("SELECT s.* FROM `slotSchedule` AS s WHERE s.id_slot=:id_slot");
		$reponse->execute(array("id_slot"=>intval($_GET['id_slot']))); 	
		$testObject = $reponse->fetch(PDO::FETCH_ASSOC);
		if(in_array($testObject['ref_user'],array_column($people, 'id_user')) ){
			$object=$testObject;
			preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $testObject[ "date" ], $inputDate);
			$object['date']=$inputDate[3].'-'.$inputDate[2].'-'.$inputDate[1];
			$object['id_user']=$testObject['ref_user'];
			echo '<span id="requestEdit" data-id="'.intval($_GET['id_slot']).'"></span>';
		}
		
	}


	function selectUserWithRooms($value,$id,$object,$critere){
		$line="\t\t\t".'<option value="'.$value['id_user'].'" id="'.$id.'"';

		if(isset($object[$critere]) AND $object[$critere]==$value['id_user']){
			$line.=' selected="selected"';
		}
		if(isset($value['oId'])){
			$line.=' data-oidroom="'.$value['oId'].'" data-ohuman="'.$value['office'].'" data-oidsvg="'.$value['oSvg'].'"';
		}
		if(isset($value['wId'])){
			$line.=' data-widroom="'.$value['wId'].'" data-whuman="'.$value['workplace'].'" data-widsvg="'.$value['wSvg'].'"';
		}
		$line.='>'.$value['prenom'].' '.$value['nom'].'</option>'."\n";		
		
		return $line;
	}	


	//Schedule part

	$line='';
	$line.='<h2 id="schedule" '.controlValues().'>Contribute to the lab schedule <abbr data-tip="You must select a user, a room and a day before being able to add a slot"><img src="images/icones/help.png" alt="help"></abbr></h2>';


	$line.='<div class="trow"><div class="tCell"><form method="post" action="schedule_copy.php"><label for="date" class="label_court2">date : </label>&nbsp;'.input_r('date', $object, 10).' <img src="images/ajax-loader.gif" id="loadingDate"><input type="submit" id="submitShort" value="Copy"><br>';	

	$line.='<label for="id_user" class="label_court2">Schedule of user<sup>*</sup> : </label>';

	
	$line.='&nbsp;<select name="id_user" id="id_user">';
	foreach($people as $key => $value){
			$line.=selectUserWithRooms($value,'people'.$value['id_user'], $object, 'id_user');
	}
	$line.='</select></form></div>';
	
	$line.='<div class="tCell"><form method="post" action="schedule_delete.php" class="dInline"><input type="submit" id="deleteWeek" class="submitShort" value="Delete"></form> the schedule of this week'.input_r('date2', $object, 10,"hidden").input_r('id_user2', $object, 10,"hidden").'</div></div>&nbsp;<br>';		
	$line.='<div id="displayWeek"></div>';	
	$line.='<div id="occupationRoom"></div>';	
	$line.='<div id="occupationRoom2"></div>';	
	
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
	 	$line.='<span id="roomInformations"><label for="room" class="label_court2">Room<sup>*</sup> :	</label>&nbsp;<span id="hroom"></span>&nbsp;<input id="room" name="room" size="4"><br></span>';
	$line.='<label for="" class="label_court2">Shortcuts :</label>';
	if($user['oId']!=0 AND $user['oId']!=''){
		$line.=	checkbox2_r('shortRoom','office',$user['office']. ' (Office)' , array());
		$line.='<span id="office" data-idsvg="'.$user['oSvg'].'" data-id="'.$user['oId'].'" data-human="'.$user['office'].'"></span>';
	}else{
		$line.=	checkbox2_r('shortRoom','office','', array());
		$line.='<span id="office"></span>';	
	}	
	if($user['wId']!=0 AND $user['wId']!=''){
		$line.=	checkbox2_r('shortRoom', 'workplace',$user['workplace']. '(Workplace)' , array());
		$line.='<span id="workplace" data-idsvg="'.$user['wSvg'].'" data-id="'.$user['wId'].'" data-human="'.$user['workplace'].'"></span>';
	}else{
		$line.=	checkbox2_r('shortRoom', 'workplace','' , array());
		$line.='<span id="workplace"></span>';
	}

	if($user['wId']!=0 AND $user['wId']!='' AND $user['oId']!=0 AND $user['oId']!=''){
		$line.=	checkbox2_r('both', '','Both (Workplace+Office)' , array());
	}else{
		$line.=	checkbox2_r('both', '','' , array());	
	}

		
		
	$line.='<br>';
	$line.='<label for="commentaire" class="label_court2">Comment : </label>&nbsp;'.input_r('commentaire', $object, 20).'<br>';		
		
	$line.='<div id="warnings"></div>';		
	$line.='<a href="#content" class="submit" id="sendSlot" data-edit="0">Add this slot</a><br>
	<div style="text-align: center;" id="loadingSlot"><img src="images/ajax-loader.gif" class="loadingSlot"><img src="images/ajax-loader.gif" class="loadingSlot"><img src="images/ajax-loader.gif" class="loadingSlot"></div><br>';	
	$line1='';
	$line2='';	
	
	$reponse=$bdd->query('SELECT * FROM `maps`');
	while($floor=$reponse->fetch()) {
		$line1.=checkbox_r($floor['building'].$floor['floor'],$floor['file'],$floor['building'].' '.$floor['floor'],array($floor['building'].$floor['floor']=>1),'floors');
		$line2.='<div id="'.str_ireplace('.svg', '',$floor['file']).'" data-map="'.$floor['file'].'" data-floor="'.$floor['floor'].'" data-building="'.$floor['building'].'" class="mapDisplay"> </div>';
	}

	$line.='<span id="floorsInformations"><label for="" class="label_court2">Floors to display</label>'.$line1.'<br>'.$line2.'</span>';	
	$line.='<div id="tableRoom"><h2>Schedule of the room <span class="roomName"></span></h2>';	
	$line.='<table id="roomSlotList">';
	$line.=th_slotOccupation();
	$line.=th_Days();
	$line.='</table></div>';	

	$line.='<div id="tableRoom2"><h2>Schedule of the room <span class="roomName"></span></h2>';	
	$line.='<table id="roomSlotList2">';
	$line.=th_slotOccupation();
	$line.=th_Days();
	$line.='</table></div>';		
	
	$line.='<h2>Schedule of user</h2>';
	$line.='<table id="SlotList">';
	$line.=th_slotWeek();
	$line.=th_Days();
	$line.='</table>';	
	
	echo $line;	
		
	echo '<script type="text/javascript" src="includes/functions.js"></script><script type="text/javascript" src="includes/schedule_build.js"></script>';	
}
?>
<?php include('includes/foot.php'); ?>  