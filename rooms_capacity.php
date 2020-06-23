<?php include('includes/head.php'); ?>
<title>Liste of rooms with their capacity</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5))){
	
	
	$floors = $bdd->query("SELECT * FROM `maps`");	
	while($floor=$floors->fetch(PDO::FETCH_ASSOC)) {
		$reponse=$bdd->prepare("SELECT * FROM `rooms` WHERE building=:building AND floor=:floor ORDER BY officeName");
		$reponse->execute(array("floor"=>$floor["floor"],"building"=>$floor["building"]));
		$line='<h1>'.$floor["building"].$floor["floor"].'</h1><div class="twocolumns"><div class="column innermap" data-map="'.$floor["file"].'"><span>Hover a given office name on the right to see its location.</span><br></div><div class="column"><div class="rightCol"><table>';	
		
		$i=0;
		while($donnees=$reponse->fetch()) {
			if($i % 20 == 0 ){
				$line.=th_rooms();		
			}
			$line.=display_rooms($donnees);
			$i++;
		}
		$line.='</table></div></div></div>';	
		echo $line;	
	}

	
	echo '<script type="text/javascript" src="includes/rooms_capacity.js"></script>';	
}
?>
<?php include('includes/foot.php'); ?>  