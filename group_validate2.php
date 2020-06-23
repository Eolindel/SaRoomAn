<?php include('includes/head.php'); ?>
<title>Occupation of the building for my group</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(2,3,4,5))){
	
	
	$object=array();
	if(isset($_POST['date']) AND preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $_POST['date'])){
		$object['date']=$_POST['date'];
	}

if(preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $object['date'], $inputDate)){
	$line='';
	$submit=array("week"=> date("W",strtotime($object['date'])),"year"=>$inputDate[3],'id_user'=>$_SESSION['id_user'] );
	$request = $bdd->prepare("UPDATE `slotSchedule` AS s LEFT JOIN `roomusers` AS u ON u.id_user = s.ref_user SET s.valid='3' WHERE s.week=:week AND s.year=:year AND s.valid=1 AND (u.id_user=:id_user OR u.ref_responsable=:id_user)");
	$request->execute($submit);
	
	$request = $bdd->prepare("SELECT u.nom, u.prenom, u.team, u.mail, u.statut, u.telephone, r.building, r.floor,r.officeName, s.date, s.week, s.start, s.end, s.length,s.valid FROM `slotSchedule` AS s LEFT JOIN `roomusers` AS u ON u.id_user = s.ref_user LEFT JOIN rooms AS r ON s.ref_room = r.id_room WHERE s.week=:week AND s.year=:year AND s.valid>=2 AND (u.id_user=:id_user OR u.ref_responsable=:id_user) ORDER BY u.nom,s.day,s.start");
	$request->execute( array("week"=> date("W",strtotime($object['date'])),"year"=>$inputDate[3],'id_user'=>$_SESSION['id_user'] ) );	
	$line.='<h1>Slots already validated</h1>';	
	$line.='<table>'.th_excel();;
	while($slot=$request->fetch(PDO::FETCH_ASSOC) ) {		
		$line.=display_slot_excel($slot);
	}
	$line.='</table>';			
		
	$line.='<br>All the slots are validated';
	echo $line;		
}		
	echo '<script type="text/javascript" src="includes/functions.js"></script>';	
}
?>


<?php include('includes/foot.php'); ?>  