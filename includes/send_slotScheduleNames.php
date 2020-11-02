<?php
session_start();


include('db_connect.php');

header("Content-Type: text/plain; charset=utf-8", true);
if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5))){
	if(isset($_POST['date']) AND preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $_POST[ "date" ], $inputDate) ){
		$slots=array();
		$request = $bdd->prepare("SELECT s.id_slot, s.day, s.start, s.end, s.length, s.ref_room, s.ref_user,r.officeName AS room, r.max, r.places,r.idSvg, u.prenom, u.nom,u.team,u.ref_responsable FROM `slotSchedule` AS s LEFT JOIN `rooms` AS r ON r.id_room=s.ref_room LEFT JOIN `roomusers` AS u ON s.ref_user=u.id_user WHERE s.week=:week AND s.year=:year AND s.valid>0 ORDER BY s.day,s.start");
		$week = intval(date("W",strtotime($_POST['date'])));
		//Correcting for sundays
		if(intval(date("w",strtotime($_POST['date']))) == 0) {
			$week = $week+1;
		}
		$request->execute( array("week"=> $week,"year"=>$inputDate[3] ) );
		while($slot=$request->fetch(PDO::FETCH_ASSOC) ) {		
			$slots[]=$slot;
		}
		//SELECT `id_slot`, `day`, `start`, `end`, `length`, `ref_room`, `ref_user`, `ref_template` FROM `slotWeek` WHERE 1
		echo json_encode($slots);
	}
	


}
?>

