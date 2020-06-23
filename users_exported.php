<?php include('includes/head.php'); ?>
<title>Export data about users</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(5))){


		$reponse = $bdd->query("SELECT u.*,o.officeName AS office,w.officeName AS workplace,r.mail AS mailr FROM `roomusers` AS u LEFT JOIN `rooms` AS o ON o.id_room=u.ref_office LEFT JOIN `rooms` AS w ON w.id_room=u.ref_workplace LEFT JOIN `roomusers` AS r ON u.ref_responsable=r.id_user WHERE u.active=1 ORDER BY u.nom");
		$line="First Name,Last Name,status,Team,login,active,Mail,notifications,Office,Workplace,Position,telephone,roomStatus,Responsable (mail)\n";
		//(`id_user`, `prenom`, `nom`, `status`, `team`, `login`, `active`, `mail`, `notifications`, `password_2`, `ref_office`, `ref_workplace`, `statut`, `telephone`, `roomStatus`, `ref_responsable`)

	   while($user=$reponse->fetch(PDO::FETCH_ASSOC)) {
	   	unset($user['id_user']);
			unset($user['password_2']);
			$user['ref_responsable']=$user['mailr'];
			$user['ref_office']=$user['office'];
			$user['ref_workplace']=$user['workplace'];
			unset($user['office']);	
			unset($user['workplace']);	
			unset($user['mailr']);	
	   	$line.=implode(',',$user)."\n";
	   }
	   echo 'Le fichier users-'.date ('Y-m-d-H-i').'.csv'.' a été enregistré dans le dossier exported_files.';
	   file_put_contents('exported_files/users-'.date ('Y-m-d-H-i').'.csv', $line);
}
?>
<?php include('includes/foot.php'); ?>  