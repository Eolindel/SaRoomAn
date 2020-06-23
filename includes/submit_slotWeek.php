<?php
session_start();


include('db_connect.php');

header("Content-Type: text/plain; charset=utf-8", true);
if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5))){
	$submit=array("id_slot"=>0,"day"=>0,"start"=>0,"end"=>0,"length"=>0,"ref_room"=>0,"ref_user"=>intval($_SESSION['id_user']),"ref_template"=>0);	
    foreach ($submit as $key => $value) {
        if (isset($_POST[$key])) {
            $submit[$key] = intval($_POST[$key]);
        }
    }	

	
	if(isset($_POST['edit']) AND intval($_POST['edit'])==1 AND isset($_POST['id_slot'])) {
		$request = $bdd->prepare("UPDATE `slotWeek` SET ".requete_preparee_update($submit)." WHERE id_slot=:id_slot");
		$request->execute($submit);
		//echo var_export($request->errorInfo());
		$request->closeCursor(); 	
	}elseif (isset($_POST['edit']) AND intval($_POST['edit'])==0){
		$request = $bdd->prepare("INSERT INTO `slotWeek`".requete_preparee_insert('(`id_slot`, `day`, `start`, `end`, `length`, `ref_room`, `ref_user`, `ref_template`)'));
		$request->execute($submit);
		//echo var_export($request->errorInfo());
		$request->closeCursor(); 
		$idNew = $bdd->lastInsertId();
		$submit['id_slot']=$idNew ;	
	
	}
	
	$submit['room']=$_POST['room'];
	echo json_encode($submit);
}
?>

