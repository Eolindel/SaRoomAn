<?php include('includes/head.php'); ?>
<title>Contribute to the full schedule</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5))){
	$object=array();
	if(isset($_POST['date2']) AND preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $_POST['date2'],$inputDate)){
		$object['date']=$_POST['date2'];
		$object['date2']=$_POST['date2'];
		$object["week"]=date("W",strtotime($_POST['date2']));	
		$object["firstweek"]=$object["week"];	
		$object["endweek"]=$object["week"];
		$object["year"]=$inputDate[3];	
		$object['startdate']=date('d-m-Y',strtotime($object["year"].'W'.$object["firstweek"]));		
		$object['enddate']=date('d-m-Y',strtotime($object["year"].'W'.$object["endweek"]));		
		$object['kindsWeek']='All';
		
		if(isset($_POST['id_user2'])){
			$object['id_user']=intval($_POST['id_user2']);
			$reponse=$bdd->prepare("SELECT u.id_user,u.prenom,u.nom,o.officeName AS office,o.id_room AS oId,o.idSvg AS oSvg,
													 w.officeName AS workplace,w.id_room AS wId,w.idSvg AS wSvg 
													 FROM `roomusers` AS u LEFT JOIN `rooms` AS o ON o.id_room=u.ref_office LEFT JOIN `rooms` AS w ON w.id_room=u.ref_workplace WHERE u.active=1 AND u.id_user=:id_user");
			$reponse->execute(array("id_user"=>$object['id_user'])); 	
			$user=$reponse->fetch(PDO::FETCH_ASSOC);	
			$object=array_merge($object,$user);
			//if both conditions are met
			
			$line='';
			$line.='<h2 id="schedule" '.controlValues().'>Delete the schedule of these weeks for '.$object['prenom'].' '.$object['nom'].'</h2>';
		
			$line.='<form method="post" action="schedule_delete2.php">';	
			$line.='<label for="startdate" class="label_court2">Starting week : </label>&nbsp;'.input_r('startdate', $object, 10).'<span id="startweek"></span> <img src="images/ajax-loader.gif" id="loadingStartDate"><br>';	
			$line.='<label for="enddate" class="label_court2">Ending week : </label>&nbsp;'.input_r('enddate', $object, 10).'<span id="endweek"></span> <img src="images/ajax-loader.gif" id="loadingEndDate"><br>';
			$line.='<label for="kindsWeek" class="label_court2">Kinds of week : </label>&nbsp;'.radio_r('kindsWeek', 'Odd', 'Odd', $object).radio_r('kindsWeek', 'Even', 'Even', $object).radio_r('kindsWeek', 'All', 'All', $object).'<br>'.input_r('id_user', $object, 10,'hidden').input_r('week', $object, 10,'hidden').input_r('year', $object, 10,'hidden').'<div id="addedInputs"></div>';
			if($user['oId']!=0 AND $user['oId']!=''){
				$line.='<span id="office" data-idsvg="'.$user['oSvg'].'" data-id="'.$user['oId'].'" data-human="'.$user['office'].'"></span>';
			}else{
				$line.='<span id="office"></span>';}	
			if($user['wId']!=0 AND $user['wId']!=''){
				$line.='<span id="workplace" data-idsvg="'.$user['wSvg'].'" data-id="'.$user['wId'].'" data-human="'.$user['workplace'].'"></span>';
			}else{
				$line.='<span id="workplace"></span>';}
			$line.='<table id="weekList"><tr><th>Week number</th><th>First day</th><th>Last day</th><th>No slots currently</th></tr></table><input type="submit" value="Delete the schedule for the weeks with planned slots."></form><br>';		
			//$line.='<div id="displayWeek" data-iduser="'.$object['id_user'].'"></div>';	
			
			echo $line;				
			
		}			
		
		
	}	

	echo '<script type="text/javascript" src="includes/functions.js"></script><script type="text/javascript" src="includes/schedule_delete.js"></script>';	
}
?>
<?php include('includes/foot.php'); ?>  