<?php include('includes/head.php'); ?>
<title>Exporter des données par bâtiment</title>
  
<?php include('includes/body.php'); ?>  
  
<?php

if(in_array($_SESSION['roomStatus'], array(5))){
	$reponse = $bdd->query("SELECT * FROM `maps`");
	$line='';
   while($floor=$reponse->fetch()) {
   	$line.='<form method="post" action="rooms_exported.php">';
   	$line.=input_r('building',$floor,3,'hidden');
   	$line.=input_r('floor',$floor,3,'hidden');
   	 $line.='<input type="submit" value="Export data about floor '.$floor["floor"].' of the building '.$floor["building"].'"><br>';
   	$line.='</form>';
   }
  	echo $line;
}
?>
<?php include('includes/foot.php'); ?>  