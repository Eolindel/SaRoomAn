<?php
session_start();


include('db_connect.php');

header("Content-Type: text/plain; charset=utf-8", true);
if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5))){
	$rooms=array();
	$reponse = $bdd->prepare("SELECT id_room,idSvg,officeName,max,places,commonName FROM `rooms` WHERE floor=:floor AND building=:building");
	$reponse->execute(array("floor"=>$_POST['floor'],"building"=>$_POST['building']));
	while($room=$reponse->fetch(PDO::FETCH_ASSOC)) {
		//print_r($building);
		$rooms[]=$room;
	}
	echo json_encode($rooms);
}
?>

