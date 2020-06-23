<?php
session_start();

include('db_connect.php');


header("Content-Type: text/plain; charset=utf-8", true);
if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5))){,
	$buildings=array();
	$reponse = $bdd->query("SELECT DISTINCT building FROM `maps`");
	while($building=$reponse->fetch()) {
		//print_r($building);
		$buildings[]=$building['building'];
	}
	echo json_encode($buildings);
}

?>

