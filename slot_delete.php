<?php include('includes/head.php'); ?>
<title>Add/Edit a room</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5))){
	$edit=0;	
	$object=array();
	if(isset($_GET['id_slot'])) {
			$edit=1;
			$object['id_slot']=intval($_GET['id_slot']);
	}
	$people = allowedRights($bdd);
	$line='';
	$request = $bdd->prepare("SELECT s.valid,s.id_slot,s.ref_user,u.ref_responsable,s.date FROM `slotSchedule` AS s LEFT JOIN `roomusers` AS u ON s.ref_user=u.id_user WHERE id_slot=:id_slot");
	$request->execute($object);
	$slot = $request->fetch(PDO::FETCH_ASSOC);
	
	$line.='<h1 id="slot" data-rights="'.$_SESSION['roomStatus'].'">This slot was deleted</h1>';
	$line.='<table id="slotList">';
	$line.=th_slotFull();	
	$line.='</table>';
	
	if((in_array(intval($slot['ref_user']) ,array_column($people, 'id_user')) AND $slot['valid']>= $_SESSION['roomStatus']) OR intval($_SESSION['id_user'])==intval($slot['ref_user']) OR intval($_SESSION['id_user'])==intval($slot['ref_responsable'])  ){
		$request = $bdd->prepare("UPDATE `slotSchedule` SET valid='0' WHERE id_slot=:id_slot");
		$request->execute($object);		
		
		$line.='<form method="post" action="slot_delete2.php">
		'.input_r('id_slot',$slot,3,'hidden')
		.input_r('valid',$slot,3,'hidden').		
		'
		<input type="submit" value="No, undo and restore the slot">	
		</form>';	
	}else{
		$line.='<br><p class="warning">You can\'t delete this slot</p>'.input_r('id_slot',$object,3,'hidden');
	
	}
	$line.='<br><br><a href="schedule_build.php?date='.date ('d-m-Y',strtotime ($slot['date'])) .'&amp;id_user='.$slot['ref_user'].'" class="submit">Go back to the editing</a>';
	echo $line;
	
	echo '<script type="text/javascript" src="includes/functions.js"></script><script type="text/javascript" src="includes/slot_delete.js"></script>';	
}
?>
<?php include('includes/foot.php'); ?>  