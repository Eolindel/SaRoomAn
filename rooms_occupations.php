<?php include('includes/head.php'); ?>
<title>Occupation of a room for a given day</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5))){
	$object=array();
	$line='<label for="date" class="label_court">date : </label>'.input_r('date', $object, 10).'<br><h2>Overcrowded rooms</h2><div id="warningOvercrowded" class="warning"></div>';
	echo $line;
	$floors = $bdd->query("SELECT * FROM `maps`");	
	while($floor=$floors->fetch(PDO::FETCH_ASSOC)) {
		$reponse=$bdd->prepare("SELECT * FROM `rooms` WHERE building=:building AND floor=:floor ORDER BY officeName");
		$reponse->execute(array("floor"=>$floor["floor"],"building"=>$floor["building"]));
		$line='<h1>'.$floor["building"].$floor["floor"].'</h1><div class="twocolumns">';
		$line.='<div class="column innermap" data-map="'.$floor["file"].'" data-floor="'.$floor["floor"].'" data-building="'.$floor["building"].'"><div id="occupationRoom"></div><br><!-- <span>Click on a given room name on the right to see its occupation.</span> --><br></div><div class="column"><div class="rightCol"><table id="floor'.$floor["building"].$floor["floor"].'">';	
			
		$i=0;
		while($donnees=$reponse->fetch()) {
			if($i % 20 == 0 ){
				$line.=th_rooms();		
			}
			$line.=display_rooms($donnees);
			$line.='<tr id="occupation'.$donnees['id_room'].'"><td colspan="10"></td></tr>';
			$i++;
		}
		$line.='</table></div></div></div>';	
		echo $line;	
	}

	
	echo '<script type="text/javascript" src="includes/functions.js"></script>
	<script type="text/javascript" src="includes/rooms_occupations.js"></script>';	
}
?>


<?php include('includes/foot.php'); ?>  