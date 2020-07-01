<?php
	$days=['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
	$roomStatuses=array("1"=>"1 : Basic User (PhD, Post Doc, Internship, Short contract)",
							"2"=>"2 : Permanent position, Group supervisor",
							"3"=>"3 : Team Leader",
							"4"=>"4 : Head of Lab",
							"5"=>"5 : Administrator");
function	convertToHours($input){
	$value=intval($input);
	return str_pad(floor($value/60.0),2,'0',STR_PAD_LEFT).':'. str_pad(($value % 60),2,'0',STR_PAD_LEFT);

}	
	
	function th_rooms(){
		$line='';
		$line.='<tr>';
		if($_SESSION['roomStatus']==5){
			$line.='<th><img src="images/icones/edit.png" alt="edit"></th>';}
		$line.='<th>Building</th><th>Floor</th><th>Office Name</th><th>Common name</th><th>Surface (m²)</th><th>Telephone</th><th>Capacity</th><th>Threshold</th><th>id in Svg file</th></tr>';
		return $line;	
	}

function th_excel(){
	return '<tr><th>Last Name</th><th>First Name</th><th>Team</th><th>Mail</th><th>Position</th><th>Telephone</th><th>Building</th><th>Floor</th><th>Room</th><th>Date</th><th>Week</th><th>Start</th><th>End</th><th>Length</th><th>Auth. Level</th></tr>';	
}

function controlValues(){
	$line = 'data-rights="'.$_SESSION['roomStatus'].'" data-iduser="'.$_SESSION['id_user'].'" data-team="'.$_SESSION['team'].'"';
	if(isset($_SESSION['ref_responsable']) AND $_SESSION['ref_responsable']!=0){
		$line .= ' data-respo="'.$_SESSION['ref_responsable'].'"';
	}
	return  $line;

} 
function display_slot_excel($slot){
	return '<tr><td>'.$slot['nom'].'</td><td>'.$slot['prenom'].'</td><td>'.$slot['team'].'</td><td>'.$slot['mail'].'</td><td>'.$slot['statut'].'</td><td>'.$slot['telephone'].'</td><td>'.$slot['building'].'</td><td>'.$slot['floor'].'</td><td>'.$slot['officeName'].'</td><td>'.date ('d-m-Y',strtotime ($slot['date'])) .'</td><td>'.$slot['week'].'</td><td>'.convertToHours($slot['start']).'</td><td>'.convertToHours($slot['end']).'</td><td>'.convertToHours($slot['length']).'</td><td>'.$slot['valid'].'</td></tr>';

}



	function display_rooms($object){
		$line='';
		$line.='<tr id="rowroom'.$object['idSvg'].'" data-room="'.$object['idSvg'].'" data-officeName="'.$object['officeName'].'" data-id_room="'.$object['id_room'].'" data-places="'.$object['places'].'"  data-max="'.$object['max'].'" class="troom">';
		if($_SESSION['roomStatus']==5){
			$line.='<td><a href="rooms_edit.php?id_room='.$object['id_room'].'"><img src="images/icones/edit.png" alt="edit"></a></td>';}		
		$line.='<td>'.$object['building'].'</td><td>'.$object['floor'].'</td><td><a href="rooms_occupation.php?id_room='.$object['id_room'].'"><span data-room="'.$object['idSvg'].'" class="room" title="">'.$object['officeName'].'</span></a></td><td>'.$object['commonName'].'</td><td>'.$object['surface'].'</td><td>'.$object['telephone1'].' '.$object['telephone2'].'</td><td class="capacity">'.$object['places'].'</td><td class="threshold">'.$object['max'].'</td><td>'.$object['idSvg'].'</td></tr>';
		return $line;	
	}//

//(`id_user`, `prenom`, `nom`, `status`, `team`, `login`, `active`, `mail`, `notifications`, `password_2`, `ref_office`, `ref_workplace`, `statut`, `telephone`, `roomStatus`, `ref_responsable`)

	function th_users(){
		$line='';
		$line.='<tr><th>';
		if(in_array($_SESSION['roomStatus'],array(3,4,5))){
			$line.='<img src="images/icones/edit.png" alt="edit">';}
		$line.='</th><th>First Name</th><th>Last Name</th><th>Team</th>';
		$line.='<th>Office</th><th>Workplace</th><th>Position</th><th>Telephone</th><th>Director</th>';
		if($_SESSION['roomStatus']==5){
			$line.='<th>login</th><th>Active</th><th>e-mail</th><th>notifications</th><th>Room Status</th><th>Status</th>';		}
		$line.='</tr>';
		return $line;		
	}

	function display_users($object,$disclose=1){
		$line='';
		$line.='<tr><td>';
		if(in_array($_SESSION['roomStatus'],array(3,4,5))){
			$line.='<a href="users_edit.php?id_user='.$object['id_user'].'"><img src="images/icones/edit.png" alt="edit">';}
		$line.='</td><td class="right"><a href="schedule_user.php?id_user='.$object['id_user'].'">'.$object['prenom'].'</a></td><td class="left">&nbsp;<a href="schedule_user.php?id_user='.$object['id_user'].'">'.$object['nom'].'</a></td><td>'.$object['team'].'</td>';
		$line.='<td>'.$object['office'].'</td><td>'.$object['workplace'].'</td><td>'.$object['statut'].'</td><td>'.$object['telephone'].'</td><td>'.$object['prenomr'].' '.$object['nomr'].'</td>';
		if($_SESSION['roomStatus']==5 AND $disclose==1){
			$line.='<td>'.$object['login'].'</td><td>'.$object['active'].'</td><td>'.$object['mail'].'</td><td>'.$object['notifications'].'</td><td>'.$object['roomStatus'].'</td><td>'.$object['status'].'</td>';}
		$line.='</tr>';
		return $line;
	}//


	function th_template(){
		$line='';
		$line.='<tr><th>';
		if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5)) ){
			$line.='<img src="images/icones/edit.png" alt="edit">';}
		$line.='</th><th>Name</th><th>Active</th>';
		$line.='</tr>';
		return $line;		
	}

	function display_template($object){
		$line='';
		$line.='<tr>';
		if($_SESSION['id_user']==$object['ref_user'] ){
			$line.='<td><a href="template_edit.php?id_template='.$object['id_template'].'"><img src="images/icones/edit.png" alt="edit"></td>';}
		$line.='<td><a href="template_build.php?id_template='.$object['id_template'].'">'.$object['name'].'</a></td><td>'.$object['active'].'</td>';
		$line.='</tr>';
		return $line;
	}//
	
	function th_slotWeek(){
		$line='';
		$line.='<tr><th>';
		if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5)) ){
			$line.='<img src="images/icones/edit.png" alt="edit">';}
		$line.='</th><th>Day</th><th>Start</th><th>End</th><th>Length</th><th>Room</th><th>Comment</th><th>Slot Status</th><th>';
		if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5)) ){
			$line.='<img src="images/icones/delete.png" alt="edit">';}
		$line.='</th></tr>';
		return $line;		
	}
	function th_slotOccupation(){
		$line='';
		$line.='<tr><th>';
		if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5)) ){
			$line.='<img src="images/icones/edit.png" alt="edit">';}
		$line.='</th><th>Day</th><th>Start</th><th>End</th><th>Length</th><th>User</th><th>Comment</th><th>Slot Status</th><th>';
		if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5)) ){
			$line.='<img src="images/icones/delete.png" alt="edit">';}
		$line.='</th></tr>';
		return $line;		
	}
	function th_slotFull(){
		$line='';
		$line.='<tr><th>';
		if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5)) ){
			$line.='<img src="images/icones/edit.png" alt="edit">';}
		$line.='</th><th>Day</th><th>Start</th><th>End</th><th>Length</th><th>Room</th><th>User</th><th>Comment</th><th>Slot Status</th><th>';
		if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5)) ){
			$line.='<img src="images/icones/delete.png" alt="edit">';}
		$line.='</th></tr>';
		return $line;		
	}	
	/*	var line='<tr class="rowslots" id="rowslot'+data.id_slot+'"><td><a href="#" class="editSlot" data-id="'+data.id_slot+'"><img src="images/icones/edit.png" alt="edit"></a></td><td>'+weekDays[data.day]+'</td><td>'+minToHours(data.start)+'</td><td>'+minToHours(data.end)+'</td><td>'+minToHours(data.length)+'</td><td><a href="schedule_user.php?id_user='+data.id_user+'">'+data.prenom+' '+data.nom+'</a></td><td>';
	if(removable === true){
		line+='<a href="slot_delete.php?id_slot='+data.id_slot+'"><img src="images/icones/delete.png" alt="delete this slot"></a>';
	}
	line+='</td></tr>';
	*/
	
	
	
	function th_peopleWeek(){
		$line='';
		$line.='<tr>
			<th>Day</th><th>Hour</th><th>Occupation</th><th>Incoming</th><th>Remaining</th><th>Leaving</th>
		</tr>';
		return $line;		
	}
	
	
	function th_Days(){
	$days=['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
	$line='';
	for($j=1;$j< count($days);$j++){
		$line.='<tr id="rowDay'.$j.'"><th colspan="9">'.$days[$j].'</th></tr>';
	}
	$line.='<tr id="rowDay7"><th colspan="9"></th></tr>';	
	return $line;
	}
	
	
	function th_SchedulePerson(){
		$days=['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
		$line='';
		$line.='<tr>
			<th>First Name</th><th>Last Name</th>';
		for($j=1;$j<count($days);$j++) {
			$line.='<th data-day="'.$j.'">'.$days[$j].'</th>';
		}	
		$line.='</tr>';
		return $line;			
	}	
	
	
?> 
 
 
   
    
</head>
<body>
<a href="#content" accesskey="s" id="gocontent">Go to content</a>
<div id="wrapper">
	<div id="header">
	<div id="topTitle">
		<div id="laboratoire">
		<?php if($_SESSION['roomStatus']>=1){
					 echo '<div class="right"><a href="schedule_user.php?id_user='.$_SESSION['id_user'].'">'.$_SESSION['prenom'].' '.$_SESSION['nom'].'</a>  | <a href="logout.php">Log out</a> |</div>';	} ?>
		</div>
					
			<a href="http://www.ens-lyon.eu"><img src="images/green/blank.png" alt="logo ENS" title="Go to ENS Lyon web page" role="banner" class="mobile_hide"></a> 
			<a href="index.php" id="logo"><img src="images/logo2.svg" alt="SaRoomAn"></a>
	</div>
			
		<div id="nav" role="navigation">
<?php 
		echo '<a href="index.php">Home</a>';
	    if ($_SESSION['roomStatus']==0) {
	    	echo '<a href="connexion.php">Connexion</a>';	
	    }
	    if(in_array($_SESSION['roomStatus'],array(1,2,3,4,5))) {
	    	echo '<a href="schedule_build.php">Add Slots</a>';
	    }		    
	    if(in_array($_SESSION['roomStatus'],array(1,2,3,4,5))) {
	    	echo '<a href="rooms_list.php">Rooms</a>
	    	<a href="users_list.php">Users</a>';
	    }
	    if(in_array($_SESSION['roomStatus'],array(1,2,3,4,5))) {
	    	echo '<a href="team_occupation.php">Team</a>';
	    	echo '<a href="building_occupation.php">Building</a>';
	    }
	    if(in_array($_SESSION['roomStatus'],array(1,2,3,4,5))) {
	    	echo '<a href="group_occupation.php">Group</a>';
	    }	    
	    ?>
	    	<span id="expand_menu" class="full_hide"><img src="images/icones/expand_white.png" alt="Plus d'actions" id="img_expand"></span>   
		</div>					
	</div>
<div id="main">
	<div id="aside" role="navigation">
<?php
$path = $_SERVER['PHP_SELF'];$file = basename ($path);
echo '<a href="index.php" class="mobile_hide"><img src="images/icon_left.png" alt="Home"></a>';

if(isset($_SESSION['roomStatus']) AND in_array($_SESSION['roomStatus'], array(1,2,3,4,5)) ){
$line='';
$line.='<span class="link_left_2">Building</span>
		<a href="building_occupation.php" class="link_left">Occupation</a>';
if(isset($_SESSION['roomStatus']) AND in_array($_SESSION['roomStatus'], array(3,4,5))) {
	$line.='<a href="building_occupation_excel.php" class="link_left">Table extraction</a>';
}		
$line.='<span class="link_left_2">Full Schedule</span>
		<a href="schedule_build.php" class="link_left">Add my slots</a>
		<span class="link_left_2">Users</span>
		<a href="users_list.php" class="link_left">List of users</a>';
if(isset($_SESSION['roomStatus']) AND in_array($_SESSION['roomStatus'], array(3,4,5))) {
	$line.='<a href="users_edit.php" class="link_left">Add a single new user</a>';
}		
if(isset($_SESSION['roomStatus']) AND $_SESSION['roomStatus']==5) {
	$line.='<a href="users_preimport.php" class="link_left">Batch add/update of users</a>
		<a href="users_exported.php" class="link_left">Export users</a>';
 }										
		$line.='<span class="link_left_2">Rooms</span>
		<a href="rooms_list.php" class="link_left">List of rooms</a>
		<a href="rooms_capacity.php" class="link_left">Capacity</a>
		<a href="rooms_threshold.php" class="link_left">Threshold</a>
		<a href="rooms_occupations.php" class="link_left">Occupation</a>';	
if(isset($_SESSION['roomStatus']) AND $_SESSION['roomStatus']==5) {		
		$line.='<a href="rooms_preimport.php" class="link_left">Batch add/update of rooms</a>
		<a href="rooms_export.php" class="link_left">Export rooms</a>';		
}		
		
if(isset($_SESSION['roomStatus']) AND in_array($_SESSION['roomStatus'], array(5))) {		
		$line.='<span class="link_left_2">Floors</span>
		<a href="floors_list.php" class="link_left">Floors</a>
		<a href="map_prebuild.php" class="link_left">Build map from imported data</a>
		';		
 }		
 echo $line;
	}

	
	
/*
<!-- 		<span class="link_left_2">Template</span>
<a href="template_edit.php" class="link_left">Add template</a>
<a href="template_list.php" class="link_left">My templates</a> -->
*/	
				?>		
	</div>
	<div id="content" role="main">    
    
    
    
 