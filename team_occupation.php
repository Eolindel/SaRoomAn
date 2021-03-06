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
	$line.='<h1 id="schedule" '.controlValues().'>Team '.$_SESSION['team'].'</h1>';
	$buildings = $bdd->query("SELECT DISTINCT building FROM `maps`");	
	while($building=$buildings->fetch(PDO::FETCH_ASSOC)) {
		$line.='<div id="building">';	
		$line.='</div>';	
		
		$floors = $bdd->prepare("SELECT floor,building,file FROM `maps` WHERE building=:building");		
		$floors->execute(array("building"=>$building["building"]));
		while($floor=$floors->fetch(PDO::FETCH_ASSOC)) {
			$reponse=$bdd->prepare("SELECT `id_room`, `building`, `floor`, `idSvg`, `officeName`, `commonName`, `surface`, `telephone1`, `telephone2`, `responsable`, `places`, `max`FROM `rooms` WHERE building=:building AND floor=:floor ORDER BY officeName");
			$reponse->execute(array("floor"=>$floor["floor"],"building"=>$floor["building"]));
			$line.='<span class="innermap" data-map="'.$floor["file"].'" data-floor="'.$floor["floor"].'" data-building="'.$floor["building"].'"></span>';//<h2>'.$floor["building"].$floor["floor"].'</h2>
		}		
	}
	$line.='<h2>Members of the team</h2>';
	$line.='<p><b>Team Leaders (status 3 or above)</b> : ';
	$reponse=$bdd->prepare("SELECT `id_user`,`prenom`,`nom` FROM `roomusers` WHERE team=:team AND roomStatus>=3");
	$reponse->execute(array("team"=>$_SESSION['team']));
	while($user=$reponse->fetch(PDO::FETCH_ASSOC)){
		$line.='<a href="schedule_user.php?id_user='.$user['id_user'].'">'.$user['prenom'].' '.$user['nom'].'</a>, ';
	}	
	$line.='</p><b>Permanent people, Supervisors (status 2) : </b>';
	$reponse=$bdd->prepare("SELECT `id_user`,`prenom`,`nom` FROM `roomusers` WHERE team=:team AND roomStatus=2");
	$reponse->execute(array("team"=>$_SESSION['team']));
	while($user=$reponse->fetch(PDO::FETCH_ASSOC)){
		$line.='<a href="schedule_user.php?id_user='.$user['id_user'].'">'.$user['prenom'].' '.$user['nom'].'</a>, ';
	}	
	$line.='</p><b>Non permanent members (status 1) : </b>';	
	$reponse=$bdd->prepare("SELECT `id_user`,`prenom`,`nom` FROM `roomusers` WHERE team=:team AND roomStatus=1");
	$reponse->execute(array("team"=>$_SESSION['team']));
	while($user=$reponse->fetch(PDO::FETCH_ASSOC)){
		$line.='<a href="schedule_user.php?id_user='.$user['id_user'].'">'.$user['prenom'].' '.$user['nom'].'</a>, ';
	}	
	$line.='</p>';
		
	$line.='<h2>Schedule (Per User)</h2>';
	$line.='<table id="PeopleList">';
	$line.=th_SchedulePerson();
	$line.='</table>';
	if(in_array($_SESSION['roomStatus'], array(3,4,5))){
		$line.='<br><br><form method="post" action="team_validate.php">'.input_r('date2', $object, 10,"hidden").'<input type="submit" value="As a team leader, confirm all the slots for this week"></form><br><br>';
	
	}	
	
	
	$line.='<table id="SlotList">';
	$line.=th_peopleWeek();
	$line.=th_Days();
	$line.='</table>';	
		
	echo $line;		
		
	echo '<script type="text/javascript" src="includes/functions.js"></script>
	<script type="text/javascript" src="includes/team_occupation.js"></script>';	
}
?>


<?php include('includes/foot.php'); ?>  