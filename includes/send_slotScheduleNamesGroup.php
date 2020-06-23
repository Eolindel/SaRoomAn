<?php
session_start();


include('db_connect.php');

header("Content-Type: text/plain; charset=utf-8", true);
if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5))){
	if(isset($_POST['date']) AND preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $_POST[ "date" ], $inputDate) ){
		$slots=array();
		$submit= array("week"=> date("W",strtotime($_POST['date'])),"year"=>$inputDate[3],"id_user"=>$_SESSION['id_user'] );
		if(isset($_POST['respo'])){
			$submit['id_user2']=intval($_POST['respo']);
		}else {
			$submit['id_user2']=-1;
		}
		$request = $bdd->prepare("SELECT s.id_slot, s.day, s.start, s.end, s.length, s.ref_room, s.ref_user,r.officeName AS room, r.max, r.places,r.idSvg, u.prenom, u.nom,u.team,u.ref_responsable FROM `slotSchedule` AS s LEFT JOIN `rooms` AS r ON r.id_room=s.ref_room LEFT JOIN `roomusers` AS u ON s.ref_user=u.id_user WHERE s.week=:week AND s.year=:year AND s.valid>0 AND (u.id_user=:id_user OR u.ref_responsable=:id_user OR u.ref_responsable=:id_user2 OR u.id_user=:id_user2) ORDER BY s.day,s.start");
		$request->execute( $submit);
		while($slot=$request->fetch(PDO::FETCH_ASSOC) ) {		
			$slots[]=$slot;
		}
		//SELECT `id_slot`, `day`, `start`, `end`, `length`, `ref_room`, `ref_user`, `ref_template` FROM `slotWeek` WHERE 1
		echo json_encode($slots);
	}
	


}
?>

