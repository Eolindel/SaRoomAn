<?php include('includes/head.php'); ?>
<title>Liste des pièces dans la base de données</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(5))){
	$object=array();
	$line='';	
	$line.='<h1>Prebuild a map for the following floor</h1>';	
	$line.='<form method="post" action="map_build_from_db.php">';
	$line.='<label for="completefloor" class="label_court">Existing Floor in database<sup>*</sup> : </label>';
	
	$line.='&nbsp;<select name="completefloor" id="completefloor">
	<option value="0" id=""></option>';		
	
	$floors = $bdd->query("SELECT id_map,floor,building,file FROM `maps`");	

	
	while($floor=$floors->fetch(PDO::FETCH_ASSOC)) {
		$line.=select( $floor['building'].'%'.$floor['floor'],'floor'.$floor['id_map'], $floor['building'].' '.$floor['floor'], $object, 'ref_office');
	}
	$line.='</select><br>
	<p>or floor and building as entered in the list of rooms&nbsp;</p>';
		$line.='<label for="building" class="label_court">Building<sup>*</sup> : </label>'.input_r('building', $object, 20).'<br>';	
		$line.='<label for="floor" class="label_court">Floor<sup>*</sup> : </label>'.input_r('floor', $object, 20).'<br><br>';

	
	
	
	
	$line.='<input type="submit" value="Create a map for this floor">';
	$line.='</form>';
	echo $line;
}
?>
<?php include('includes/foot.php'); ?>  