<?php include('includes/head.php'); ?>
<title>Add/Edit a week template</title>
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(5))){
	$edit=0;	
	$object=array();
	if(isset($_GET['id_template'])) {
		$edit=1;
		$object['id_template']=intval($_GET['id_template']);
		$reponse=$bdd->prepare("SELECT * FROM `template` WHERE id_template=:id_template");
		$reponse->execute($object);
		$object=$reponse->fetch();			
	}else{
		$object=array("active"=>1);
	}

////////////////////
//ADD OR EDIT THE TEMPLATE	
$submit=array("id_template"=>0,"ref_user"=>0, "name"=>'', "active"=>0);
foreach ($submit as $key => $value) {
     if (isset($_POST[$key])) {
         $submit[$key] = urldecode($_POST[$key]);
     }
}		
$submit['ref_user']=intval($_SESSION['id_user']);

if($submit['name']!='' AND in_array($_SESSION['roomStatus'],array(1,2,3,4,5)) AND $edit == 0){
		$request = $bdd->prepare("INSERT INTO `template`".requete_preparee_insert('(`id_template`, `ref_user`, `name`, `active`)'));
		$request->execute($submit);
		//echo var_export($request->errorInfo());
		$request->closeCursor(); 
		$idNew = $bdd->lastInsertId();
		$edit=3;
		$object=$submit;
		$object['id_template']=$idNew ;
}else if($edit == 1 AND $submit['name']!='' AND $_SESSION['id_user']==$object['ref_user'] AND in_array($_SESSION['roomStatus'],array(1,2,3,4,5))){
		$request = $bdd->prepare("UPDATE `template` SET ".requete_preparee_update($submit)." WHERE id_template=:id_template");
		$submit['id_template']=$object['id_template'];
		$request->execute($submit);
		//echo var_export($request->errorInfo());
		$count = $request->rowCount();
		$request->closeCursor(); 
		//echo json_encode($submit);
		$edit=4;	  
}
	  
//ADD OR EDIT THE TEMPLATE	
//////////////////////

//Querying the last version of the user
if($edit >= 1) {
		$reponse=$bdd->prepare("SELECT * FROM `template` WHERE id_template=:id_template");
		$reponse->execute(array("id_template"=>intval($object['id_template'])));
		$object=$reponse->fetch();
}	

//displaying form	
if($edit <= 1){	
	$line='';
	if($edit==0){
		$line.='<h2>Add a new template</h2>';	
	}elseif($edit==1) {
		$line.='<h2>Edit the template '.$object['name'].'</h2>';
	}

	$line.='<div class="twocolumns"><div class="column">';

	if($edit >= 1){
	   $line.='<form method="post" action="template_edit.php?id_template='. $object['id_template'].'">';
	}else {
	 	$line.='<form method="post" action="template_edit.php">';}	
	$line.='<label for="mail" class="label_court">Name<sup>*</sup> : </label>'.input_r('name', $object, 20).'<br>';	 		
	$line.='<label for="active" class="label_court">Active : </label>'.radio_r('active', '0', 'No', $object).radio_r('active', '1', 'Yes', $object).'<br>';
	$line.='<input type="submit" value="Send">
	<p class="warning" id="missingfield">A mandatory field is missing</p>';
	$line.='</form></div><div class="column padLeft">';	

	
	$line.='<div id="mapDisplay"></div></div></div>';
	echo $line;
//print_r($object);

}elseif($edit==3){
	echo '<p>The template '.$object['name'].'  has been added.</p>
	<a href="template_edit.php?id_template='.$object['id_template'].'"class="submit">Edit again this template</a>';
}elseif($edit==4){
	echo '<p>The template '.$object['name'].'  has been updated.</p>
	<a href="template_edit.php?id_template='.$object['id_template'].'" class="submit">Edit again this template</a>';
}


	echo '<script type="text/javascript" src="includes/formFunctions.js"></script>
	<script type="text/javascript" src="includes/template_edit.js"></script>';
		
}
?>
<?php include('includes/foot.php'); ?>  