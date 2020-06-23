<?php include('includes/head.php'); ?>
<title>Import a list of users</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(5))){
$directory = 'import_files';
$scanned_directory = array_diff(scandir($directory), array('..', '.'));
	 $line='<p>If the user already exist (same mail), they will be updated, otherwise, they will be added.</p>
	 
	 <ul>
	 	<li>First Name and Last Name are explicit</li>
	 	<li>status : leave empty (not used for you)</li>
		<li>Team explicit</li>
		<li>login : mandatory, must be unique in the database (usually all everything before the @in the mail adress)</li>
		<li>active : 0 user disabled 1 user enables</li>
		<li>Mail : explicit, everything will be converted to lowercase</li>
		<li>notifications : mails enabled or not, use 1</li>
		<li>Office : full name of the office, it must correspond to the list of rooms given in the rooms database</li>
		<li>Workplace : full name of the workplace, it must correspond to the list of rooms given in the rooms database</li>
		<li>Position : Position (MCF, PR, IE, etc)</li>
		<li>Telephone : explicit</li>
		<li>roomStatus : 1 means basic user, 5 means administrator</li>
		<li>Responsable : full mail of the person responsible as entered in the mail column</li>
	 </ul>
	 
	 <form action="users_import.php" method="post">
        <label for="file_users" class="label_court">Import a user list<sup>*</sup> : </label><select name="file_users" id="file_users"><option value=""></option>';
	foreach($scanned_directory as $key => $value){
		if(!is_dir($directory . DIRECTORY_SEPARATOR . $value) AND endswith($value, '.csv')){
			 $line.=select($value, 'file_users',$value, array(), 'file_users');
		}
	}  
   $line.='</select><br>';     
	$line.='<input type="submit" value="Send"></form><br><br>'; 
	echo $line;
}
?>
<?php include('includes/foot.php'); ?>  