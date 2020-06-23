<?php include('includes/head.php'); ?>
<title>Add/Edit a room</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5))){
	$edit=0;	
	$object=array();
	if(isset($_POST['id_slot'])) {
			$edit=1;
			$object['id_slot']=intval($_POST['id_slot']);
	}

	$line='';
	$request = $bdd->prepare("SELECT s.valid,s.id_slot,s.ref_user,u.ref_responsable FROM `slotSchedule` AS s LEFT JOIN `roomusers` AS u ON s.ref_user=u.id_user WHERE id_slot=:id_slot");
	$request->execute($object);
	$slot = $request->fetch(PDO::FETCH_ASSOC);
	
	$line.='<h1 id="slot" data-rights="'.$_SESSION['roomStatus'].'">This slot was restored</h1>';
	$line.='<table id="slotList">';
	$line.=th_slotFull();	
	$line.='</table>';
	$slot['valid']=intval($_POST['valid']);
	if($_SESSION['roomStatus']>=$slot['valid'] OR intval($_SESSION['id_user'])==intval($slot['ref_user'])  OR intval($_SESSION['id_user'])==intval($slot['ref_responsable']) ){
		unset($slot['ref_user']);
		unset($slot['ref_responsable']);
		$request = $bdd->prepare("UPDATE `slotSchedule` SET valid=:valid WHERE id_slot=:id_slot");
		$request->execute($slot);		
		$line.=input_r('id_slot',$slot,3,'hidden');
	}else{
		$line.='<br><p class="warning">You can\'t change this slot</p>'.input_r('id_slot',$object,3,'hidden');
	
	}

	echo $line;
	
	echo '<script type="text/javascript" src="includes/functions.js"></script><script type="text/javascript" src="includes/slot_delete.js"></script>';	
}
?>
<?php include('includes/foot.php'); ?>  