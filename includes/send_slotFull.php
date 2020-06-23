<?php
session_start();


include('db_connect.php');

header("Content-Type: text/plain; charset=utf-8", true);
if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5))){
	if(isset($_POST['id_slot']) ){

		$request = $bdd->prepare("SELECT u.nom,u.prenom, s.date,s.id_slot, s.day, s.start, s.end, s.length, s.ref_room, s.ref_user,r.officeName AS officeName, r.max, r.places,s.valid,s.commentaire FROM `slotSchedule` AS s LEFT JOIN `rooms` AS r ON r.id_room=s.ref_room LEFT JOIN `roomusers` AS u ON s.ref_user=u.id_user WHERE  s.id_slot=:id_slot ORDER BY s.day,s.start");
		$request->execute( array('id_slot'=> intval($_POST['id_slot']) ) );
		$slot=$request->fetch(PDO::FETCH_ASSOC);
		echo json_encode($slot);
	}
	


}
?>

