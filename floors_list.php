<?php include('includes/head.php'); ?>
<title>List of floors in the database</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(5))){
	
	function th_floors(){
		$line='<tr>';
		if(in_array($_SESSION['roomStatus'], array(5)) ){
			$line.='<th><img src="images/icones/edit.png" alt="edit"></th>';}		
		$line.= '<th>Building</th><th>Floor</th><th>File of the map</th></tr>';	
		return $line;
	}
	function display_floors($object){
		$line='<tr>';
		if(in_array($_SESSION['roomStatus'], array(5)) ){
			$line.='<td><a href="floors_edit.php?id_map='.$object['id_map'].'"><img src="images/icones/edit.png" alt="edit"></a></td>';}		
		$line.= '<td>'.$object['building'].'</td><td>'.$object['floor'].'</td><td>'.$object['file'].'</td></tr>';	
		return $line;
	}
	
	$line='';


	
	$listFloors=array();
	$line.='<h1>List of floors in the database</h1>
	<p>The <i>Floor</i> and <i>Building</i> columns for the floors will have to match exactly with the <i>Building</i> and <i>Floor</i> columns given in the rooms database (beware of lower and upper caps !).</p>';	
	$line.='<table>';
	$line.=th_floors();
	$floors = $bdd->query("SELECT id_map,floor,building,file FROM `maps`");	
	while($floor=$floors->fetch(PDO::FETCH_ASSOC)) {
		$line.=display_floors($floor);
		$listFloors[]=$floor;
	}
		$line.='</table><br><br>';	
		
	//checking for floors in the maps folder and not listed in the database	
	$directory = 'maps';
	$scanned_directory = array_diff(scandir($directory), array('..', '.'));	
	foreach($scanned_directory as $key => $value){
		if(!is_dir($directory . DIRECTORY_SEPARATOR . $value) AND endswith($value, '.svg') AND in_array($value, array_column($listFloors, 'file'),true) == FALSE){
			$line.='The file '.$value.' is not listed in your database. <a href="floors_edit.php?file='.$value.'">Add it ?</a><br><br>';
			 }
	}  		
		
		$line.='<a href="floors_edit.php" class="submit">Add a new floor</a>';	
		
		
		echo $line;	
		
		
}		
		
?>
<?php include('includes/foot.php'); ?>  