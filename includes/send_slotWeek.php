<?php
session_start();


include('db_connect.php');

header("Content-Type: text/plain; charset=utf-8", true);
if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5))){
	if(isset($_POST['template'])){
		$slots=array();
		$request = $bdd->prepare("SELECT s.id_slot, s.day, s.start, s.end, s.length, s.ref_room, s.ref_user, s.ref_template,r.officeName AS room FROM `slotWeek` AS s LEFT JOIN `rooms` AS r ON r.id_room=s.ref_room WHERE s.ref_template=:template AND s.ref_user=:id_user ORDER BY s.day,s.start");
		$request->execute( array("template"=>intval($_POST['template']),'id_user'=>$_SESSION['id_user'] ) );
		while($slot=$request->fetch(PDO::FETCH_ASSOC) ) {		
			$slots[]=$slot;
		}
		//SELECT `id_slot`, `day`, `start`, `end`, `length`, `ref_room`, `ref_user`, `ref_template` FROM `slotWeek` WHERE 1
		echo json_encode($slots);
	}
	


}
?>

