<?php include('includes/head.php'); ?>
<title>Occupation of the building for my team</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5))){
	
	
	$object=array();
	if(isset($_GET['date']) AND preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $_GET['date'])){
		$object['date']=$_GET['date'];
	}


	$line='<label for="date" class="label_court">date : </label>'.input_r('date', $object, 10).'<br>';
	$line.='<h1 id="schedule"  '.controlValues().'>Group</h1>';
	$buildings = $bdd->query("SELECT DISTINCT building FROM `maps`");	
	while($building=$buildings->fetch(PDO::FETCH_ASSOC)) {
		$line.='<div id="building">';	
		$line.='</div>';	
		
		$floors = $bdd->prepare("SELECT floor,building,file FROM `maps` WHERE building=:building");		
		$floors->execute(array("building"=>$building["building"]));
		while($floor=$floors->fetch(PDO::FETCH_ASSOC)) {
			$reponse=$bdd->prepare("SELECT `id_room`, `building`, `floor`, `idSvg`, `officeName`, `commonName`, `surface`, `telephone1`, `telephone2`, `responsable`, `places`, `max` FROM `rooms` WHERE building=:building AND floor=:floor ORDER BY officeName");
			$reponse->execute(array("floor"=>$floor["floor"],"building"=>$floor["building"]));
			$line.='<span class="innermap" data-map="'.$floor["file"].'" data-floor="'.$floor["floor"].'" data-building="'.$floor["building"].'"></span>';//<h2>'.$floor["building"].$floor["floor"].'</h2>
		}		
	}
	$line.='<h2>Members of the group</h2>';
	$line.='<p><b>Supervisor</b> ';
	if(in_array($_SESSION['roomStatus'], array(2,3,4,5) )  ){
		$line.=$_SESSION['prenom'].' '.$_SESSION['nom'];
	}else{
		if(isset($_SESSION['ref_responsable'])){
			$reponse=$bdd->prepare("SELECT `id_user`,`prenom`,`nom` FROM `roomusers` WHERE id_user=:ref_responsable");
			$reponse->execute(array("ref_responsable"=>$_SESSION['ref_responsable']));
			$responsable=$reponse->fetch(PDO::FETCH_ASSOC);
			$line.=$responsable['prenom'].' '.$responsable['nom'];
		}
	}
	$line.='</p><p><b>Group under supervision</b><br>';
	if(in_array($_SESSION['roomStatus'], array(2,3,4,5) )  ){
		$reponse=$bdd->prepare("SELECT `prenom`,`nom` FROM `roomusers` WHERE ref_responsable=:ref_responsable");
		$reponse->execute(array("ref_responsable"=>$_SESSION['id_user']));
		while($underling=$reponse->fetch(PDO::FETCH_ASSOC)){
			$line.=$underling['prenom'].' '.$underling['nom'].', ';
		}
	}else{
		if(isset($responsable)){
			$reponse=$bdd->prepare("SELECT `prenom`,`nom` FROM `roomusers` WHERE ref_responsable=:ref_responsable");
			$reponse->execute(array("ref_responsable"=>$responsable['id_user']));
			while($underling=$reponse->fetch(PDO::FETCH_ASSOC)){
				$line.=$underling['prenom'].' '.$underling['nom'];
			}
		}
	}	
	$line.='</p>';

	$line.='<h2>Schedule (Per User)</h2>';
	$line.='<table id="PeopleList">';
	$line.=th_SchedulePerson();
	$line.='</table>';
	if(in_array($_SESSION['roomStatus'], array(2,3,4,5))){
		$line.='<br><br><form method="post" action="group_validate.php">'.input_r('date2', $object, 10,"hidden").'<input type="submit" value="As a group leader, confirm all the slots for this week"></form><br><br>';
	
	}	
	
	
	$line.='<table id="SlotList">';
	$line.=th_peopleWeek();
	$line.=th_Days();
	$line.='</table>';	
		
	echo $line;		
		
	echo '<script type="text/javascript" src="includes/functions.js"></script>
	<script type="text/javascript" src="includes/group_occupation.js"></script>';	
}
?>


<?php include('includes/foot.php'); ?>  