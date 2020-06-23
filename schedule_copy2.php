<?php include('includes/head.php'); ?>
<title>Contribute to the full schedule</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5))){
//print_r($_POST);
	$line="";
$people = allowedRights($bdd);

//print_r($people);
$idUser=intval($_POST['id_user']);
if(in_array($idUser ,array_column($people, 'id_user'))) {
	$j=0;
	for($j=0;$j<intval($_POST['total']);$j++){
		if(isset($_POST['week'.$j]) AND isset($_POST['day'.$j]) ){
			//creating the proper date for all days of the week
			$weekIndices=explode(',', $_POST['week'.$j]);
			$monday=date_create(date("Y-m-d",strtotime($_POST['day'.$j])));
			$dates=[];
			$interval = new DateInterval('P1D');
			$dates[]=date_format(date_sub($monday, $interval), 'Y-m-d');
			for($k=1;$k<7;$k++){
					$dates[]=date_format(date_add($monday, $interval), 'Y-m-d');
			}
			
			$request = $bdd->prepare("SELECT s.* FROM `slotSchedule` AS s WHERE s.week=:week AND s.year=:year AND s.ref_user=:id_user AND s.valid>0 ORDER BY s.day,s.start");
			$request->execute( array("week"=> intval($_POST['week']),"year"=>intval($_POST['year']),"id_user"=> intval($_POST['id_user'])) );
			while($slot=$request->fetch(PDO::FETCH_ASSOC) ) {
				//updating the fields changed
				$submit=$slot;	
				$submit['id_slot']=0;	
				$submit['week']=$weekIndices[1];
				$submit['year']=$weekIndices[0];
				$submit['date']=$dates[$slot['day']];
				$submit['valid']=$_SESSION['roomStatus'];
				$request2 = $bdd->prepare("INSERT INTO `slotSchedule`".requete_preparee_insert('(`id_slot`, `year`, `week`, `date`, `day`, `start`, `end`, `length`, `ref_room`, `ref_user`, `commentaire`, `valid`)'));
				$request2->execute($submit);
				$request2->closeCursor();
				//print_r($submit);
				//echo '<br>';
				/*print_r($slot);
				
				
				echo '<br>';*/
			}
			
			$line.='<h2>The slots have been copied for the week '.$weekIndices[1].'</h2>';
		}
		//echo '<br>';
	}
}


	$line.='<a href="schedule_build.php?id_user='.intval($_POST['id_user']).'" class="submit">Go back to the schedule of this user</a>';
	echo $line;	

	
	//echo '<script type="text/javascript" src="includes/functions.js"></script><script type="text/javascript" src="includes/schedule_copy.js"></script>';	
}
?>
<?php include('includes/foot.php'); ?>  