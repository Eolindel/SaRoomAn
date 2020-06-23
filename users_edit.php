<?php include('includes/head.php'); ?>
<title>Add/Edit a user</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(3,4,5))){
	$edit=0;	
	$object=array();
	if(isset($_GET['id_user'])) {
			$edit=1;
			$object['id_user']=intval($_GET['id_user']);
	}
	if($_SESSION['roomStatus'] == 3){
		$object['team']=$_SESSION['team'];
	}
////////////////////
//ADD OR EDIT THE USER	
$submit=array("id_user"=>0,"prenom"=>'', "nom"=>'', "status"=>0, "team"=>'',"login"=>'', "active"=>'', "mail"=>'', "notifications"=>'', "password_2"=>'', "ref_office"=>0 ,"ref_workplace"=>0,'statut'=>'','telephone'=>'','roomStatus'=>1,"ref_responsable"=>0);
foreach ($submit as $key => $value) {
     if (isset($_POST[$key])) {
         $submit[$key] = strip_tags(urldecode($_POST[$key]));
     }
}		

if($submit['mail']!='' AND $submit['prenom']!='' AND $submit['nom']!='' AND in_array($_SESSION['roomStatus'],array(3,4,5)) AND $edit == 0){
		$request = $bdd->prepare("INSERT INTO `roomusers`".requete_preparee_insert('(`id_user`, `prenom`, `nom`, `status`, `team`, `login`, `active`, `mail`, `notifications`, `password_2`, `ref_office`, `ref_workplace`, `statut`, `telephone`, `roomStatus`, `ref_responsable`)'));
		$request->execute($submit);
		//echo var_export($request->errorInfo());
		$request->closeCursor(); 
		$idNew = $bdd->lastInsertId();
		$edit=3;
		$object=$submit;
		$object['id_user']=$idNew ;
}else if($submit['mail']!='' AND $submit['prenom']!='' AND $submit['nom']!='' AND in_array($_SESSION['roomStatus'],array(3,4,5)) AND $edit == 1){
		unset($submit['password_2']);
		unset($submit['status']);
		$request = $bdd->prepare("UPDATE `roomusers` SET ".requete_preparee_update($submit)." WHERE id_user=:id_user");
		$submit['id_user']=$object['id_user'];
		$request->execute($submit);
		//echo var_export($request->errorInfo());
		$count = $request->rowCount();
		$request->closeCursor(); 
		//echo json_encode($submit);
		$edit=4;	  
}
	  
//ADD OR EDIT THE USER	
//////////////////////

//Querying the last version of the user
if($edit >= 1) {
		$reponse=$bdd->prepare("SELECT * FROM `roomusers` WHERE id_user=:id_user");
		$reponse->execute(array("id_user"=>intval($object['id_user'])));
		$object=$reponse->fetch();
}	

//displaying form	
if($edit <= 1){	
	$line='';
	if($edit==0){
		$line.='<h2>Add a new user</h2>';	
	}elseif($edit==1) {
		$line.='<h2>Edit the user '.$object['prenom'].' '.$object['nom'].'</h2>';
	}

	$line.='<div class="twocolumns"><div class="column">';

	if($edit >= 1){
	   $line.='<form method="post" action="users_edit.php?id_user='. $object['id_user'].'">';
	}else {
		$object['active']=1;
		$object['notifications']=1;
		$object['roomStatus']=1;
	 	$line.='<form method="post" action="users_edit.php">';}	
	$line.='<label for="mail" class="label_court">Mail<sup>*</sup> : </label>'.input_r('mail', $object, 20).'<br>';	 		
	$line.='<span id="otherInputs"><label for="prenom" class="label_court">First Name<sup>*</sup> : </label>'.input_r('prenom', $object, 20).'<br>';
	$line.='<label for="nom" class="label_court">Last Name<sup>*</sup> : </label>'.input_r('nom', $object, 20).'<br>';
	$line.='<label for="officeName" class="label_court">Login<sup>*</sup> : </label>'.input_r('login', $object, 20).'<br>';	
	$line.='<label for="active" class="label_court">Active : </label>'.radio_r('active', '0', 'No', $object).radio_r('active', '1', 'Yes', $object).'<br>';
	$line.='<label for="notifications" class="label_court">Notifications : </label>'.radio_r('notifications', '0', 'No', $object).radio_r('notifications', '1', 'Yes', $object).'<br>';
	
	$line.='<label for="ref_office" class="label_court">Office : </label>';	
	$line.='&nbsp;<select name="ref_office" id="ref_office">
	<option value="0" id=""></option>';
	$rooms=$bdd->query("SELECT id_room,officeName,building,floor FROM `rooms` ORDER BY officeName");
	while($room=$rooms->fetch()){
			$line.=select($room['id_room'],'link'.$room['id_room'], $room['building'].' '.$room['floor'].' '.$room['officeName'], $object, 'ref_office');		
	}
	$line.='</select>&nbsp;<br>';	
	$line.='<label for="ref_workplace" class="label_court">Workplace : </label>';	
	$line.='&nbsp;<select name="ref_workplace" id="ref_office">
	<option value="0" id=""></option>';
	$rooms=$bdd->query("SELECT id_room,officeName,building,floor FROM `rooms` ORDER BY officeName");
	while($room=$rooms->fetch()){
			$line.=select($room['id_room'],'link'.$room['id_room'], $room['building'].' '.$room['floor'].' '.$room['officeName'], $object, 'ref_workplace');		
	}
	$line.='</select>&nbsp;<br>';		
	
	$line.='<label for="commonName" class="label_court">Position : </label>'.input_r('statut', $object, 20).'<br>';
	$line.='<label for="team" class="label_court">Team : </label>'.input_r('team', $object, 20).'<br>';	
	$line.='<label for="telephone" class="label_court">Telephone : </label>'.input_r('telephone', $object, 20).'<br>';	
	$line.='<label for="roomStatus" class="label_court">roomStatus : </label>';
	$line.='<select name="roomStatus" id="roomStatus">';
	foreach($roomStatuses as $key=>$value){
		if($key<=$_SESSION['roomStatus']){
			$line.=select($key,'roomStatus'.$key, $value, $object, 'roomStatus');
		}
	}
	$line.='</select><br>';
	//$line.=input_r('roomStatus', $object, 20).'<br>';	
	$line.='<label for="responsable" class="label_court">Manager : </label>';
		$line.='&nbsp;<select name="ref_responsable" id="ref_responsable">
	<option value="0" id=""></option>';
	$peoples=$bdd->query("SELECT id_user,nom,prenom FROM `roomusers` WHERE active='1' ORDER BY nom");
	while($people=$peoples->fetch()){
			$line.=select($people['id_user'],'link'.$people['id_user'], $people['prenom'].' '.$people['nom'], $object, 'ref_responsable');		
	}
	$line.='</select>&nbsp;<br>';	
	//id_user`, `prenom`, `nom`, `status`, `team`, `login`, `active`, `mail`, `notifications`, `password_2`, `ref_office`, `ref_workplace`, `statut`, `telephone`, `roomStatus`, `ref_responsable


	$line.='<input type="submit" value="Send">
	<p class="warning" id="missingfield">A mandatory field is missing</p>';
	$line.='</span></form></div></div>';
	$line.='<div class="column padLeft">';	

	
	$line.='<div id="mapDisplay">';
	$floors = $bdd->query("SELECT * FROM `maps`");	
	while($floor=$floors->fetch(PDO::FETCH_ASSOC)) {
		$line.='<img src="maps/'.$floor['file'].'" alt="building '.$floor['building'].' floor '.$floor['floor'].'">';
	}	
	$line.='</div></div>';
	echo $line;
//print_r($object);

}elseif($edit==3){
	echo '<p>The user '.$object['prenom'].' '.$object['nom'].'  has been added.</p>
	<a href="users_edit.php?id_user='.$object['id_user'].'"class="submit">Edit again this user</a>';
}elseif($edit==4){
	echo '<p>The user '.$object['prenom'].' '.$object['nom'].'  has been updated.</p>
	<a href="users_edit.php?id_user='.$object['id_user'].'" class="submit">Edit again this user</a>';
}


	echo '<script type="text/javascript" src="includes/formFunctions.js"></script>
	<script type="text/javascript" src="includes/users_edit.js"></script>';
		
}
?>
<?php include('includes/foot.php'); ?>  