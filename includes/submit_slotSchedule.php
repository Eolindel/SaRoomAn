<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('db_connect.php');





header("Content-Type: text/plain; charset=utf-8", true);
if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5)) AND preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $_POST[ "date" ], $inputDate)){

	//retrieving the list of users that the user can add or edit
	$people = allowedRights($bdd);
	
	if(in_array(intval($_POST['ref_user']) ,array_column($people, 'id_user')) ){//possibility of edition only if the person in in the people list
		$slots=[];	
		$submit=array("id_slot"=>0,'year'=>0,"week"=>0,'date'=>'',"day"=>0,"start"=>0,"end"=>0,"length"=>0,"ref_room"=>0,"ref_user"=>intval($_SESSION['id_user']),'commentaire'=>'','valid'=>1);	
	    foreach ($submit as $key => $value) {
	        if (isset($_POST[$key])) {
	            $submit[$key] = intval($_POST[$key]);
	        }
	    }

	   $submit['commentaire'] = trim(strip_tags($_POST['commentaire']));	
		$submit["week"]=date("W",strtotime($_POST['date']));	
		$submit["year"]=$inputDate[3];
		$submit["date"]=date("Y-m-d", strtotime($_POST['date']));
		//edit slot
		if(isset($_POST['edit']) AND intval($_POST['edit'])==1 AND isset($_POST['id_slot']) AND $_SESSION['roomStatus']>=$submit["valid"]) {
			$request = $bdd->prepare("SELECT valid FROM `slotSchedule` WHERE id_slot=:id_slot");
			$request->execute(array('id_slot'=>$submit['id_slot']));
			$initialSlot=$request->fetch(PDO::FETCH_ASSOC);
			$request->closeCursor(); 	
			
			if($_SESSION['roomStatus']>=$initialSlot["valid"]){//edition only for users able to do so : with a status above the initial status of the slot
				$submit["valid"]=$_SESSION['roomStatus'];
				$request = $bdd->prepare("UPDATE `slotSchedule` SET ".requete_preparee_update($submit)." WHERE id_slot=:id_slot");
				$request->execute($submit);
				//echo var_export($request->errorInfo());
				$request->closeCursor(); 	
				$submit['room']=$_POST['room'];
				$key = array_search($submit['ref_user'], array_column($people, 'id_user'));				
				$submit['nom']=$people[$key]['nom'];
				$submit['prenom']=$people[$key]['prenom'];
				$slots[]=$submit;
			}
		}elseif (isset($_POST['edit']) AND intval($_POST['edit'])==0){//add new slot
			$submit["valid"]=$_SESSION['roomStatus'];
			$request = $bdd->prepare("INSERT INTO `slotSchedule`".requete_preparee_insert('(`id_slot`, `year`, `week`, `date`, `day`, `start`, `end`, `length`, `ref_room`, `ref_user`, `commentaire`, `valid`)'));
			$request->execute($submit);
			//echo var_export($request->errorInfo());
			$idNew = $bdd->lastInsertId();
			$request->closeCursor(); 
			$submit['id_slot']=$idNew ;	
			$submit['room']=$_POST['room'];
			$key = array_search($submit['ref_user'], array_column($people, 'id_user'));				
			$submit['nom']=$people[$key]['nom'];
			$submit['prenom']=$people[$key]['prenom'];			
			$slots[]=$submit;
			
			if(isset($_POST['ref_room2']) AND intval($_POST['ref_room2'])!=0 ){
				$submit['id_slot']=0;
				unset($submit['room']);
				unset($submit['nom']);
				unset($submit['prenom']);
				$submit['ref_room']=intval($_POST['ref_room2']);
				$request = $bdd->prepare("INSERT INTO `slotSchedule`".requete_preparee_insert('(`id_slot`, `year`, `week`, `date`, `day`, `start`, `end`, `length`, `ref_room`, `ref_user`, `commentaire`, `valid`)'));
				$request->execute($submit);
				//echo var_export($request->errorInfo());
				$idNew = $bdd->lastInsertId();
				$request->closeCursor(); 
				$submit['id_slot']=$idNew ;	
				$submit['room']=$_POST['room2'];
				$key = array_search($submit['ref_user'], array_column($people, 'id_user'));				
				$submit['nom']=$people[$key]['nom'];
				$submit['prenom']=$people[$key]['prenom'];				
				$slots[]=$submit;								
			}
		}
		
		
		echo json_encode($slots);	
	
	
	}

}

//echo json_encode($submit);	
?>

