<?php include('includes/head.php'); ?>
<title>Add/Edit a Floor</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(5))){
	$edit=0;	
	$object=array();
	if(isset($_GET['id_map'])) {
			$edit=1;
			$object['id_map']=intval($_GET['id_map']);
	}
	if(isset($_GET['file'])){
		$object['file']=$_GET['file'];
	}

////////////////////
//ADD OR EDIT THE FLOOR	
$submit=array("id_map"=>0,"building"=>'', "floor"=>'', "file"=>'');
foreach ($submit as $key => $value) {
     if (isset($_POST[$key])) {
         $submit[$key] = urldecode(trim($_POST[$key]));
     }
}		

	//checking for floors in the maps folder
	$listFloors=array();
	$directory = 'maps';
	$scanned_directory = array_diff(scandir($directory), array('..', '.'));	
	foreach($scanned_directory as $key => $value){
		if(!is_dir($directory . DIRECTORY_SEPARATOR . $value) AND endswith($value, '.svg') AND in_array($value, array_column($listFloors, 'file'),true) == FALSE){
			$listFloors[]=$value;
			 }
	}  


if($submit['building']!='' AND $submit['floor']!='' AND in_array($submit['file'],$listFloors,true) AND in_array($_SESSION['roomStatus'],array(5)) AND $edit == 0){
		$request = $bdd->prepare("INSERT INTO `maps`".requete_preparee_insert('(`id_map`, `building`, `floor`, `file`)'));
		$request->execute($submit);
		echo var_export($request->errorInfo());
		$request->closeCursor(); 
		$idNew = $bdd->lastInsertId();
		$edit=3;
		$object=$submit;
		$object['id_map']=$idNew ;
}else if($submit['building']!='' AND $submit['floor']!='' AND in_array($submit['file'],$listFloors) AND in_array($_SESSION['roomStatus'],array(5)) AND $edit == 1){
		$request = $bdd->prepare("UPDATE `maps` SET ".requete_preparee_update($submit)." WHERE id_map=:id_map");
		$submit['id_map']=$object['id_map'];
		$request->execute($submit);
		//echo var_export($request->errorInfo());
		$count = $request->rowCount();
		$request->closeCursor(); 
		//echo json_encode($submit);
		$edit=4;	  
}
	  
//ADD OR EDIT THE FLOOR	
//////////////////////

//Querying the last version of the floor
if($edit >= 1) {
		$reponse=$bdd->prepare("SELECT * FROM `maps` WHERE id_map=:id_map");
		$reponse->execute(array("id_map"=>intval($object['id_map'])));
		$object=$reponse->fetch();
}	

//displaying form	
if($edit <= 1){	
	$line='';
	if($edit==0){
		$line.='<h2>Add a new floor</h2>';	
	}elseif($edit==1) {
		$line.='<h2>Edit the floor '.$object['floor'].' in the building '.$object['building'].'</h2>';
	}


	if($edit >= 1){
	   $line.='<form method="post" action="floors_edit.php?id_map='. $object['id_map'].'">';
	}else {
	 	$line.='<form method="post" action="floors_edit.php">';}		
	$line.='<label for="building" class="label_court">Building<sup>*</sup> : </label>'.input_r('building', $object, 20).'<br>';
	$line.='<label for="floor" class="label_court">Floor<sup>*</sup> : </label>'.input_r('floor', $object, 20).'<br>';
	$line.='<label for="file" class="label_court">File : </label>';
		$line.='&nbsp;<select name="file" id="file">
	<option value="0" id=""></option>';
	foreach($scanned_directory as $key => $value){
		if(!is_dir($directory . DIRECTORY_SEPARATOR . $value) AND endswith($value, '.svg')){
			$line.=select($value,'file'.$value,  $value, $object, 'file');		
		}
	} 	
	$line.='</select>&nbsp;<br>';	
	$line.='<input type="submit" value="Send">
	<p class="warning" id="missingfield">A mandatory field is missing</p>';
	$line.='</form>';
	$line.='<br><a href="floors_list.php" class="submit">Go back to the Floors list</a>';	
	echo $line;
//print_r($object);

}elseif($edit==3){
	echo '<p>The floor '.$object['floor'].' in the building '.$object['building'].' has been added.</p>
	<a href="floors_edit.php?id_map='.$object['id_map'].'" class="submit">Edit again this floor</a>
	<a href="floors_list.php" class="submit">Go back to the Floors list</a>';
}elseif($edit==4){
	echo '<p>The floor '.$object['floor'].' in the building '.$object['building'].' has been updated.</p>
	<a href="floors_edit.php?id_map='.$object['id_map'].'" class="submit">Edit again this floor</a>
	<a href="floors_list.php" class="submit">Go back to the Floors list</a>';
}


	echo '<script type="text/javascript" src="includes/formFunctions.js"></script>
		<script type="text/javascript" src="includes/floors_edit.js"></script>';
		
}
?>
<?php include('includes/foot.php'); ?>  