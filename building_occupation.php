<?php include('includes/head.php'); ?>
<title>Occupation ofthe building for a given week</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5))){
	
	
	$object=array();
	if(isset($_GET['date']) AND preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $_GET['date'])){
		$object['date']=$_GET['date'];
	}


	$line='<label for="date" class="label_court">date : </label>'.input_r('date', $object, 10).'<span id="schedule" '.controlValues().'></span><br>';
	$buildings = $bdd->query("SELECT DISTINCT building FROM `maps`");	
	while($building=$buildings->fetch(PDO::FETCH_ASSOC)) {
		$line.='<h1>'.$building["building"].'</h1><div id="building">';	
		$line.='</div>';	
		
		$floors = $bdd->prepare("SELECT * FROM `maps` WHERE building=:building");		
		$floors->execute(array("building"=>$building["building"]));
		while($floor=$floors->fetch(PDO::FETCH_ASSOC)) {
			$reponse=$bdd->prepare("SELECT * FROM `rooms` WHERE building=:building AND floor=:floor ORDER BY officeName");
			$reponse->execute(array("floor"=>$floor["floor"],"building"=>$floor["building"]));
			$line.='<span class="innermap" data-map="'.$floor["file"].'" data-floor="'.$floor["floor"].'" data-building="'.$floor["building"].'"></span>';//<h2>'.$floor["building"].$floor["floor"].'</h2>
		}		
	}
	$line.='<h2>Schedule (Per User)</h2>';
	$line.='<table id="PeopleList">';
	$line.=th_SchedulePerson();
	$line.='</table>';	
	if(in_array($_SESSION['roomStatus'], array(4,5))){
		$line.='<br><br><form method="post" action="building_validate.php">'.input_r('date2', $object, 10,"hidden").'<input type="submit" value="As lab head, confirm all the slots for this week"></form><br><br>';
	
	}		
	
	
	$line.='<table id="SlotList">';
	$line.=th_peopleWeek();
	$line.=th_Days();
	$line.='</table>';



		
	echo $line;		
		
	echo '<script type="text/javascript" src="includes/functions.js"></script>
	<script type="text/javascript" src="includes/building_occupation.js"></script>';	
}
?>


<?php include('includes/foot.php'); ?>  