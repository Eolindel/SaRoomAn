<?php include('includes/head.php'); ?>
<title>Occupation of a room for a given day</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5))){
	if(isset($_GET['id_room'])){
		$object=array("id_room"=>intval($_GET['id_room']));
	if(isset($_GET['date']) AND preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $_GET['date'])){
		$object['date']=$_GET['date'];
	}
	
	$data ='';

	
	$request=$bdd->prepare("SELECT `id_room`, `building`, `floor`, `idSvg`, `officeName`, `commonName`, `surface`, `telephone1`, `telephone2`, `responsable`, `places`, `max` FROM `rooms` WHERE id_room=:id_room");
	$request->execute(array('id_room'=>$object["id_room"]));
	$room=$request->fetch(PDO::FETCH_ASSOC);
	foreach($room as $key=>$value){
		if($value!=''){
			$data.=' data-'.$key.'="'.$value.'"';}
			
	}	
	
		
	$line='';
	
	$line.='<h2 id="room" '.$data.'>Schedule of room</h2><span id="schedule" '.controlValues().'>';
	$line.='<label for="date" class="label_court">date : </label>'.input_r('date', $object, 10).'<br>';
	$line.='<div id="warningOvercrowded"></div>';
	$line.='<div id="occupation"></div>';
	$line.='<table id="roomSlotList">';
	$line.=th_slotOccupation();
	$line.=th_Days();
	$line.='</table>';	
	echo $line;

	
	
	
	}	
	
	

	
	echo '<script type="text/javascript" src="includes/functions.js"></script>
	<script type="text/javascript" src="includes/rooms_occupation.js"></script>';	
}
?>


<?php include('includes/foot.php'); ?>  