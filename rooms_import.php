<?php include('includes/head.php'); ?>
<title>Ajout d'une liste de pièce par lot</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(5))){
$filename='';
if(isset($_POST["file_rooms"])){
	$filename=$_POST["file_rooms"];
}

$handle = fopen('import_files/'.$filename, 'r');

if ($handle) {

	$i = 0;
	while (!feof($handle)) {
		//reading lines
		$buffer = trim(fgets($handle));
		$object = explode (',', $buffer);		
		if($i>0 AND isset($object[0]) AND isset($object[1]) ){//discarding first line

			$room=array("id_room"=>0,"building"=>$object[0], "floor"=>$object[1], "idSvg"=>$object[2], "officeName"=>$object[3],"commonName"=>$object[4], "surface"=>$object[5], "telephone1"=>$object[6], "telephone2"=>$object[7], "responsable"=>intval($object[8]), "places"=>intval($object[9]),"max"=>$object[10]);
			//print_r($room);
			//echo $room["officeName"].'<br>';
			
			$reponse = $bdd->prepare("SELECT COUNT(*) AS nbRooms,id_room FROM `rooms` WHERE officeName=:officeName AND building=:building AND floor=:floor AND idSvg=:idSvg");
			$reponse->execute(array("officeName"=>$room['officeName'], "building"=>$room['building'], "floor"=>$room['floor'], "idSvg"=>$room['idSvg']));			
			$answer=$reponse->fetch();
			if($answer["nbRooms"]==0) {
				$reponse = $bdd->prepare("INSERT INTO `rooms`".requete_preparee_insert('(`id_room`, `building`, `floor`, `idSvg`, `officeName`, `commonName`, `surface`, `telephone1`, `telephone2`, `responsable`, `places`, `max`)'));
      		$reponse->execute($room);
				//echo var_export($reponse->errorInfo());
      		//$id = $bdd->lastInsertId();
      		echo "Room ".$room["officeName"]. " in building ". $room["building"]. " at floor ".$room["floor"]." has been added to the database<br>";			
			}else{
				$request = $bdd->prepare("UPDATE `rooms` SET ".requete_preparee_update($room)." WHERE id_room=:id_room");
				$room['id_room']=$answer['id_room'];
				$request->execute($room);
				echo "Room ".$room["officeName"]. " in building ". $room["building"]. " at floor ".$room["floor"]." has been updated<br>";			
			}
		
			
		}
		$i++;
	}
}

}
?>
<?php include('includes/foot.php'); ?>  