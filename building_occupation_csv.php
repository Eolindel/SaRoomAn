<?php session_start();

if(in_array($_SESSION['roomStatus'], array(3,4,5))){
	
	include('includes/db_connect.php');

function th_csv(){
	return 'Last Name,First Name,Team,Mail,Position,Telephone,Building,Floor,Room,Date,Week,Start,End,Length,Auth. Level'."\n";	
}

function display_slot_csv($slot){
	return $slot['nom'].','.$slot['prenom'].','.$slot['team'].','.$slot['mail'].','.$slot['statut'].','.$slot['telephone'].','.$slot['building'].','.$slot['floor'].','.$slot['officeName'].','.date ('d-m-Y',strtotime ($slot['date'])) .','.$slot['week'].','.convertToHours($slot['start']).','.convertToHours($slot['end']).','.convertToHours($slot['length']).','.$slot['valid']."\n";

}

function	convertToHours($input){
	$value=intval($input);
	return str_pad(floor($value/60.0),2,'0',STR_PAD_LEFT).':'. str_pad(($value % 60),2,'0',STR_PAD_LEFT);
}	

	$line="";
	$edit=0;
	$object=array();
	if(isset($_POST['date']) AND preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $_POST['date'])){
		$object['date']=$_POST['date'];
		$edit=1;
	}
	if($edit==1 AND isset($_POST['date']) AND preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $_POST[ "date" ], $inputDate)){
		$request = $bdd->prepare("SELECT u.nom, u.prenom, u.team, u.mail, u.statut, u.telephone, r.building, r.floor,r.officeName, s.date, s.week, s.start, s.end, s.length,s.valid FROM slotSchedule AS s LEFT JOIN `roomusers` AS u ON u.id_user = s.ref_user LEFT JOIN rooms AS r ON s.ref_room = r.id_room WHERE s.valid>0 AND s.week = :week AND year=:year ORDER BY u.nom,s.day,s.start");
		$request->execute( array("week"=> date("W",strtotime($_POST['date'])),"year"=>$inputDate[3] ) );
		$line=th_csv();
		while($slot=$request->fetch(PDO::FETCH_ASSOC) ) {		
			$line.=display_slot_csv($slot);
		}
	}else {
		$line='Something went wrong, try again.';
	}
	

header("Content-type: application/csv");
header("Content-Disposition: attachment; filename='SaRoomAn-".$_POST['date'].".csv'");
header("Pragma: no-cache");
header("Expires: 0");
		
	echo $line;		
		
}
?>