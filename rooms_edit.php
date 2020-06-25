<?php include('includes/head.php'); ?>
<title>Add/Edit a room</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(5))){
	$edit=0;	
	$object=array();
	if(isset($_GET['id_room'])) {
			$edit=1;
			$object['id_room']=intval($_GET['id_room']);
	}

////////////////////
//ADD OR EDIT THE ROOM	
$submit=array("id_room"=>0,"building"=>'', "floor"=>'', "idSvg"=>'', "officeName"=>'', "commonName"=>'', "surface"=>'', "telephone1"=>'', "telephone2"=>'', "responsable"=>0, "places"=>0,"max"=>0);
foreach ($submit as $key => $value) {
     if (isset($_POST[$key])) {
         $submit[$key] = strip_tags(urldecode($_POST[$key]));
     }
}		

if($submit['building']!='' AND $submit['floor']!='' AND $submit['idSvg']!='' AND $submit['officeName']!='' AND in_array($_SESSION['roomStatus'],array(5)) AND $edit == 0){
		$request = $bdd->prepare("INSERT INTO `rooms`".requete_preparee_insert('(`id_room`, `building`, `floor`, `idSvg`, `officeName`, `commonName`, `surface`, `telephone1`, `telephone2`, `responsable`, `places`, `max`)'));
		$request->execute($submit);
		//echo var_export($request->errorInfo());
		$request->closeCursor(); 
		$idNew = $bdd->lastInsertId();
		$edit=3;
		$object=$submit;
		$object['id_room']=$idNew ;
}else if($submit['building']!='' AND $submit['floor']!='' AND $submit['idSvg']!='' AND $submit['officeName']!='' AND in_array($_SESSION['roomStatus'],array(5)) AND $edit == 1){
		$request = $bdd->prepare("UPDATE `rooms` SET ".requete_preparee_update($submit)." WHERE id_room=:id_room");
		$submit['id_room']=$object['id_room'];
		$request->execute($submit);
		//echo var_export($request->errorInfo());
		$count = $request->rowCount();
		$request->closeCursor(); 
		//echo json_encode($submit);
		$edit=4;	  
}
	  
//ADD OR EDIT THE ROOM	
//////////////////////

//Querying the last version of the room
if($edit >= 1) {
		$reponse=$bdd->prepare("SELECT `id_room`, `building`, `floor`, `idSvg`, `officeName`, `commonName`, `surface`, `telephone1`, `telephone2`, `responsable`, `places`, `max` FROM `rooms` WHERE id_room=:id_room");
		$reponse->execute(array("id_room"=>intval($object['id_room'])));
		$object=$reponse->fetch();
}	

//displaying form	
if($edit <= 1){	
	$line='';
	if($edit==0){
		$line.='<h2>Add a new room</h2>';	
	}elseif($edit==1) {
		$line.='<h2>Edit the room '.$object['officeName'].' in the building '.$object['building'].' at floor '.$object['floor'].'</h2>';
	}

	$line.='<div class="twocolumns"><div class="column">';

	if($edit >= 1){
	   $line.='<form method="post" action="rooms_edit.php?id_room='. $object['id_room'].'">';
	}else {
	 	$line.='<form method="post" action="rooms_edit.php">';}		
	$line.='<label for="building" class="label_court">Building<sup>*</sup> : </label>'.input_r('building', $object, 20).'<br>';
	$line.='<label for="floor" class="label_court">Floor<sup>*</sup> : </label>'.input_r('floor', $object, 20).'<br>';
	$line.='<label for="idSvg" class="label_court">id in Svg file<sup>*</sup> : </label>'.input_r('idSvg', $object, 20).'<br>';
	$line.='<label for="officeName" class="label_court">Office Name<sup>*</sup> : </label>'.input_r('officeName', $object, 20).'<br>';
	$line.='<span id="otherInputs"><label for="commonName" class="label_court">Common Name : </label>'.input_r('commonName', $object, 20).'<br>';
	$line.='<label for="surface" class="label_court">Surface : </label>'.input_r('surface', $object, 20).'<br>';	
	$line.='<label for="telephone1" class="label_court">Telephone number 1 : </label>'.input_r('telephone1', $object, 20).'<br>';	
	$line.='<label for="telephone2" class="label_court">Telephone number 2 : </label>'.input_r('telephone2', $object, 20).'<br>';		
	$line.='<label for="responsable" class="label_court">Manager of the room : </label>';
		$line.='&nbsp;<select name="responsable" id="responsable">
	<option value="0" id=""></option>';
	$peoples=$bdd->query("SELECT id_user,nom,prenom FROM `roomusers` WHERE active='1' ORDER BY nom");
	while($people=$peoples->fetch()){
			$line.=select($people['id_user'],'link'.$people['id_user'], $people['prenom'].' '.$people['nom'], $object, 'responsable');		
	}
	$line.='</select>&nbsp;<br>';	
	$line.='<label for="places" class="label_court">Normal capacity : </label>'.input_r('places', $object, 20).'<br>';		
	$line.='<label for="max" class="label_court">Threshold : </label>'.input_r('max', $object, 20).'</span><br>';
	$line.='<input type="submit" value="Send">
	<p class="warning" id="missingfield">A mandatory field is missing</p>';
	$line.='</form></div><div class="column padLeft">';	

	
	$line.='<div id="mapDisplay"></div></div></div>';
	echo $line;
//print_r($object);

}elseif($edit==3){
	echo '<p>The room '.$object['officeName'].' in the building '.$object['building'].' at floor '.$object['floor'].'  has been added.</p>
	<a href="rooms_edit.php?id_room='.$object['id_room'].'" class="submit">Edit again this room</a>';
}elseif($edit==4){
	echo '<p>The room '.$object['officeName'].' in the building '.$object['building'].' at floor '.$object['floor'].'  has been updated.</p>
	<a href="rooms_edit.php?id_room='.$object['id_room'].'" class="submit">Edit again this room</a>';
}


	echo '<script type="text/javascript" src="includes/formFunctions.js"></script>
			<script type="text/javascript" src="includes/rooms_edit.js"></script>';
		
}
?>
<?php include('includes/foot.php'); ?>  