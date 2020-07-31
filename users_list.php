<?php include('includes/head.php'); ?>
<title>Liste des pièces dans la base de données</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5))){
	
	

		$reponse=$bdd->query("SELECT u.*,o.officeName AS office,w.officeName AS workplace,r.nom AS nomr,r.prenom AS prenomr FROM `roomusers` AS u LEFT JOIN `rooms` AS o ON o.id_room=u.ref_office LEFT JOIN `rooms` AS w ON w.id_room=u.ref_workplace LEFT JOIN `roomusers` AS r ON u.ref_responsable=r.id_user WHERE u.active=1 AND u.roomStatus >0 ORDER BY u.nom");
		$line='<h1>Users</h1><table>';	
		
		$i=0;
		while($donnees=$reponse->fetch(PDO::FETCH_ASSOC)) {
			if($i % 10 == 0 ){
				$line.=th_users();		
			}
			if( in_array($_SESSION['roomStatus'], array(3,4,5)) ){
				$line.=display_users($donnees,1);
			}else{
				$line.=display_users($donnees,0);}
			$i++;
		}
		$line.='</table>';	
		echo $line;	

	
	echo '<script type="text/javascript" src="includes/rooms_list.js"></script>';	
}
?>
<?php include('includes/foot.php'); ?>  
