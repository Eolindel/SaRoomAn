<?php include('includes/head.php'); ?>
<title>Add/Edit a user</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5))){
	$edit=0;	
	$object=array();
	if(isset($_GET['id_user'])) {
			$edit=1;
			$object['id_user']=intval($_GET['id_user']);
	}
		$reponse=$bdd->prepare("SELECT u.*,o.officeName AS office,w.officeName AS workplace,r.nom AS nomr,r.prenom AS prenomr FROM `roomusers` AS u LEFT JOIN `rooms` AS o ON o.id_room=u.ref_office LEFT JOIN `rooms` AS w ON w.id_room=u.ref_workplace LEFT JOIN `roomusers` AS r ON u.ref_responsable=r.id_user WHERE u.id_user=:id_user AND u.roomStatus >0 ORDER BY u.nom");
		$reponse->execute($object);
		$object=$reponse->fetch();
	$line='<h2>'.$object['prenom'].' '.$object['nom'].'</h2>';	
	echo $line;
}
?>
<?php include('includes/foot.php'); ?>  