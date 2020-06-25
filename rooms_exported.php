<?php include('includes/head.php'); ?>
<title>Exporter des données par bâtiment</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(5))){
	if(isset($_POST['floor']) AND isset($_POST['building'])) {
		$submit=array("floor"=>$_POST['floor'],"building"=>$_POST['building']);
		$reponse = $bdd->prepare("SELECT `id_room`, `building`, `floor`, `idSvg`, `officeName`, `commonName`, `surface`, `telephone1`, `telephone2`, `responsable`, `places`, `max` FROM `rooms` WHERE floor=:floor AND building=:building");
		$reponse->execute($submit);
		
		$line="building,floor,idSvg,officeName,commonName,surface,telephone1,telephone2,responsable,places,max\n";
	   while($room=$reponse->fetch(PDO::FETCH_ASSOC)) {
	   	unset($room['id_room']);
	   	$line.=implode(',',$room)."\n";
	   	
	   }
	   echo 'Le fichier '.$submit['building'].$submit['floor'].'-'.date ('Y-m-d-H-i').'.csv'.' a été enregistré dans le dossier exported_files.';
	   file_put_contents('exported_files/'.$submit['building'].$submit['floor'].'-'.date ('Y-m-d-H-i').'.csv', $line);
	}
}
?>
<?php include('includes/foot.php'); ?>  