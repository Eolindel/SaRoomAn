<?php include('includes/head.php'); ?>
<title>Ajout d'une liste de pièce par lot</title>
  
<?php include('includes/body.php'); ?>  
   
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if(in_array($_SESSION['roomStatus'], array(5))){
$filename='';
if(isset($_POST["file_users"])){
	$filename=$_POST["file_users"];
}

$handle = fopen('import_files/'.$filename, 'r');

if ($handle) {
	$i = 0;
	while (!feof($handle)) {
		
		//reading lines
		$buffer = trim(fgets($handle));
		$object = explode (',', $buffer);	

		if($i>0 AND isset($object[0]) AND isset($object[1]) ){//discarding first line and empty lines
		//prenom,nom,status,Team,login,active,Mail,notifications,Office,Workplace,Statut,telephone,roomStatus
			
			if(isset($object[6]) AND $object[6]!=''){
				$userfound=0;
				//echo $object[6].' '.strtolower(str_ireplace('@ens-lyon.fr', '', $object[6])).'<br>'; 
				//searching based on mail converted to lowercase
				$submit=array("id_user"=>0,"prenom"=>'', "nom"=>'', "status"=>0, "team"=>'',"login"=>'', "active"=>'', "mail"=>'', "notifications"=>'', "password_2"=>'', "ref_office"=>0 ,"ref_workplace"=>0,'statut'=>'','telephone'=>'','roomStatus'=>1,"ref_responsable"=>0);
				$reponse = $bdd->prepare("SELECT id_user FROM `roomusers` WHERE mail=:mail");
				$reponse->execute(array("mail"=>strtolower($object[6])));
				if($user=$reponse->fetch(PDO::FETCH_ASSOC)){
					//echo 'first test'.'<br>';
					//print_r($user);
					$userfound=1;
					$submit['id_user']=$user['id_user'];
				}
				if($userfound==0){//searching based on mail with @ens-lyon.fr added
					$reponse = $bdd->prepare("SELECT id_user FROM `roomusers` WHERE mail=:mail OR mail=:mail2");
					$reponse->execute(array("mail"=>strtolower($object[6]).'@ens-lyon.fr',"mail2"=>strtolower(str_ireplace('@ens-lyon.fr', '', $object[6])) ));					
					if($user=$reponse->fetch(PDO::FETCH_ASSOC)){
						//echo 'second test'.'<br>';
						//print_r($user);
						$userfound=1;
						$submit['id_user']=$user['id_user'];
					}				
				}
				//echo 'ufound'.$userfound.'</br>';				
				
				
					$submit["prenom"]=$object[0];
					$submit["nom"]=$object[1];				
					$submit["status"]=intval($object[2]);
					$submit["team"]=$object[3];
					$submit["login"]=$object[4];
					$submit["active"]=$object[5];
					$submit["mail"]=strtolower($object[6]);
					$submit["notifications"]=$object[7];	
					$submit["statut"]=$object[10];		
					$submit["telephone"]=$object[11];	
					$submit["roomStatus"]=intval($object[12]);	
					if(isset($object[13]) AND $object[13]!==''){
						$reponse = $bdd->prepare("SELECT id_user FROM `roomusers` WHERE mail=:mail AND active='1'");	
						$reponse->execute(array("mail"=>strtolower( $object[13]) ) );
						if($user=$reponse->fetch()){
							$submit["ref_responsable"]=$user['id_user'];
						}		
					}		
					if($submit["login"]==''){
						$submit["login"]=strtolower(str_ireplace('@ens-lyon.fr', '', $object[6]));
					}									
				if($userfound==1){
					
					unset($submit["password_2"]);
					unset($submit["status"]);
					if(isset($object[8]) AND $object[8]!==''){
						$reponse = $bdd->prepare("SELECT id_room FROM `rooms` WHERE officeName=:officeName");	
						$reponse->execute(array("officeName"=>$object[8]));
						if($room=$reponse->fetch()){
							$submit["ref_office"]=$room['id_room'];
						}else{unset($submit["ref_office"]);}		
					}else{unset($submit["ref_office"]);}
					if(isset($object[9]) AND $object[9]!==''){
						$reponse = $bdd->prepare("SELECT id_room FROM `rooms` WHERE officeName=:officeName");	
						$reponse->execute(array("officeName"=>$object[9]));
						//echo var_export($reponse->errorInfo());
						if($room=$reponse->fetch()){
							//echo 'workplace'.'<br>';
							//	print_r($room);
							$submit["ref_workplace"]=$room['id_room'];
						}else{unset($submit["ref_workplace"]);}		
					}else{unset($submit["ref_workplace"]);}	
				}else{
					if(isset($object[8]) AND $object[8]!==''){
						$reponse = $bdd->prepare("SELECT id_room FROM `rooms` WHERE officeName=:officeName");	
						$reponse->execute(array("officeName"=>$object[8]));
						
						if($room=$reponse->fetch()){
							$submit["ref_office"]=$room['id_room'];}		
					}
					if(isset($object[9]) AND $object[9]!==''){
						$reponse = $bdd->prepare("SELECT id_room FROM `rooms` WHERE officeName=:officeName");	
						$reponse->execute(array("officeName"=>$object[9]));
						echo var_export($reponse->errorInfo());
						if($room=$reponse->fetch()){
								//echo 'workplacea'.'<br>';
								//print_r($room);							
							$submit["ref_workplace"]=$room['id_room'];}		
					}	

				}
				
					//echo $buffer.'<br>';
					//print_r($submit);
					//echo '<br><br>';
					
				if($userfound==1){
					$request = $bdd->prepare("UPDATE `roomusers` SET ".requete_preparee_update($submit)." WHERE id_user=:id_user");
					//print_r($submit);					
					//echo '<br>'.requete_preparee_update($submit).'<br>';
					$request->execute($submit);
					//echo var_export($request->errorInfo());
					echo "User ".$submit["prenom"].' '.$submit["nom"]. " has been updated<br><br>";			
				}else{
					$reponse = $bdd->prepare("INSERT INTO `roomusers`".requete_preparee_insert('(`id_user`, `prenom`, `nom`, `status`, `team`, `login`, `active`, `mail`, `notifications`, `password_2`, `ref_office`, `ref_workplace`, `statut`, `telephone`, `roomStatus`, `ref_responsable`)'));
      			$reponse->execute($submit);
					//echo var_export($reponse->errorInfo());
      		//$id = $bdd->lastInsertId();
      			echo "User ".$submit["prenom"].' '.$submit["nom"]. " has been added to the database<br>";				
				}					
					
					
					
			}

			//print_r($room);
			//echo $room["officeName"].'<br>';
		/*	
			$reponse = $bdd->prepare("SELECT COUNT(*) AS nbRooms,id_room FROM `rooms` WHERE officeName=:officeName AND building=:building AND floor=:floor AND idSvg=:idSvg");
			$reponse->execute(array("officeName"=>$room['officeName'], "building"=>$room['building'], "floor"=>$room['floor'], "idSvg"=>$room['idSvg']));			
			$answer=$reponse->fetch();
			if($answer["nbRooms"]==0) {
				$reponse = $bdd->prepare("INSERT INTO `rooms`".requete_preparee_insert('(`id_user`, `prenom`, `nom`, `status`, `team`, `login`, `active`, `mail`, `notifications`, `password_2`, `ref_office`, `ref_workplace`, `statut`, `telephone`, `roomStatus`)'));
      		$reponse->execute($room);
				//echo var_export($reponse->errorInfo());
      		//$id = $bdd->lastInsertId();
      		echo "Room ".$room["officeName"]. " in building ". $room["building"]. " at floor ".$room["floor"]." has been added to the database<br>";			
			}else{
				$request = $bdd->prepare("UPDATE `rooms` SET ".requete_preparee_update($room)." WHERE id_room=:id_room");
				$room['id_room']=$answer['id_room'];
				$request->execute($room);
				echo "Room ".$room["officeName"]. " in building ". $room["building"]. " at floor ".$room["floor"]." has been updated<br>";			
			}
		*/
			
		}
		$i++;
	}
}

}
?>
<?php include('includes/foot.php'); ?>  