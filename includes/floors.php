<?php
session_start();

include('db_connect.php');


header("Content-Type: text/plain; charset=utf-8", true);
if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5))){
	$floors=array();
	$reponse = $bdd->query("SELECT DISTINCT floor FROM `maps`");
	while($floor=$reponse->fetch()) {
		//print_r($building);
		$floors[]=$floor['floor'];
	}
	echo json_encode($floors);
}

?>

