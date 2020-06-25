<?php include('includes/head.php'); ?>
<title>Contribute to the full schedule</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5))){
	$line="";
$people = allowedRights($bdd);

//print_r($people);
$idUser=intval($_POST['id_user']);
if(in_array($idUser ,array_column($people, 'id_user'))) {
	$j=0;
	for($j=0;$j<intval($_POST['total']);$j++){
		if(isset($_POST['week'.$j])){
			$weekIndices=explode(',', $_POST['week'.$j]);
			$request = $bdd->prepare("UPDATE `slotSchedule` AS s SET s.valid='0' WHERE s.week=:week AND s.year=:year AND s.ref_user=:id_user AND s.valid>0");
			$request->execute( array("week"=> intval($weekIndices[1]),"year"=>intval($weekIndices[0]),"id_user"=> intval($_POST['id_user'])) );
		
			$line.='<h2>The slots have been deleted for the week '.$weekIndices[1].'</h2>';
		}
		//echo '<br>';
	}
}


	$line.='<a href="schedule_build.php?id_user='.intval($_POST['id_user']).'" class="submit">Go back to the schedule of this user</a>';
	echo $line;	


?>
<?php include('includes/foot.php'); ?>  