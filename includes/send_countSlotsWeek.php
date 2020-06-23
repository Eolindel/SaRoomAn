<?php
session_start();


include('db_connect.php');

header("Content-Type: text/plain; charset=utf-8", true);
if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5))){
	if(isset($_POST['week']) AND isset($_POST['year']) AND isset($_POST['id_user'])){
		$submit=array("week"=>intval($_POST['week']),"year"=>intval($_POST['year']),"id_user"=>intval($_POST['id_user']) );
		$request = $bdd->prepare("SELECT COUNT(*) As nbSlots FROM `slotSchedule` AS s WHERE s.week=:week AND s.year=:year AND s.valid>0 AND s.ref_user=:id_user ORDER BY s.day,s.start");
		$request->execute($submit);
		$slotCount=$request->fetch(PDO::FETCH_ASSOC);
		$submit['nbSlots']=$slotCount['nbSlots'];
		echo json_encode($submit);
	}
}
?>

