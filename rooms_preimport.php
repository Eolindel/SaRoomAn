<?php include('includes/head.php'); ?>
<title>Import a list of rooms</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(5))){
$directory = 'import_files';
$scanned_directory = array_diff(scandir($directory), array('..', '.'));

	 $line='<p>If the rooms already exist (same building, floor, officeName and idSvg), they will be updated, otherwise, they will be added.</p><br><form action="rooms_import.php" method="post">
        <label for="file_rooms" class="label_court">Import data about rooms<sup>*</sup> : </label><select name="file_rooms" id="file_DS"><option value=""></option>';
	foreach($scanned_directory as $key => $value){
		if(!is_dir($directory . DIRECTORY_SEPARATOR . $value) AND endswith($value, '.csv')){
			 $line.=select($value, 'file_rooms',$value, array(), 'file_rooms');
		}
	    
	}  
   $line.='</select><br>';     
	$line.='<input type="submit" value="Send"></form><br><br>'; 
	echo $line;
}
?>
<?php include('includes/foot.php'); ?>  