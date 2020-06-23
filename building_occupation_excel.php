<?php include('includes/head.php'); ?>
<title>Occupation ofthe building for a given week</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(3,4,5))){
	
	echo '<h1>Table extraction of the occupation of the building</h1>';	
	
	$edit=0;
	$object=array();
	if(isset($_GET['date']) AND preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $_GET['date'])){
		$object['date']=$_GET['date'];
	}
	if(isset($_POST['date']) AND preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $_POST['date'])){
		$object['date']=$_POST['date'];
		$edit=1;
	}
	if($edit==1 AND isset($_POST['date']) AND preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $_POST[ "date" ], $inputDate)){
		$request = $bdd->prepare("SELECT u.nom, u.prenom, u.team, u.mail, u.statut, u.telephone, r.building, r.floor,r.officeName, s.date, s.week, s.start, s.end, s.length,s.valid FROM slotSchedule AS s LEFT JOIN `roomusers` AS u ON u.id_user = s.ref_user LEFT JOIN rooms AS r ON s.ref_room = r.id_room WHERE s.valid>0 AND s.week = :week AND year=:year ORDER BY u.nom,s.day,s.start");
		$request->execute( array("week"=> date("W",strtotime($_POST['date'])),"year"=>$inputDate[3] ) );
		
		$line='<table>';
		$line.=th_excel();
		while($slot=$request->fetch(PDO::FETCH_ASSOC) ) {		
			$line.=display_slot_excel($slot);
		}
		$line.='</table>';
	}else if($edit==0){
		$line='<form method="post"><label for="date" class="label_court">date : </label>'.input_r('date', $object, 10).'<input type="submit" value="Extraire pour la semaine choisie"></form><br>';
	}else {
		$line='Something went wrong, try again.';
	}
	

	
		
	echo $line;		
		
	echo '<script type="text/javascript" src="includes/building_occupation_excel.js"></script>';	
}
?>


<?php include('includes/foot.php'); ?>  