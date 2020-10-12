<?php include('includes/head.php'); ?>
<title>Manual for roomplan</title>
  
<?php include('includes/body.php'); ?>  
  
<h1>Installation</h1>
<ol>
<li>In the file "dbconnect.php" in the « includes » folder, change your mySQL connecting parameters : "dbname", "user" and corresponding "password". This user must have a writing and reeading privelege over the corresponding database. Change also the mail and name of the contact person for the recovery mail or initialization of the password.</li>
<li>Create the different databases by importing the "import_bdd.sql"</li>
<li></li>
<li>Create a map of each floor and building (under inkscape) <span class="warning">according to the following instructions</span> and put them in the « maps » folder.</li>
<li>Put the characteristics of each room in the "room.csv" file of the « import_files » folder, the crucial data are the 'building', 'floor', 'idSvg', 'officeName', 'places' (usual capacity of the room) and 'max' columns (thrsehold imposed by your local rules)</li>
<li>Put your list of users in the file users.csv file of the « import_files » folder, the crucial columns are 'First Name', 'Last Name', 'active' (put a 1, a 0 will disable the corresponding person), 'Mail' (used as unique identifier), 'Office', 'Workplace' (if people work in a dedicated room on top of their office), 'roomStatus' (1 means basics user, 2 means user able to validate the schedule of people working directly under their supevision, 3 means local validator and will be able to validate the schedule of all the people in the same team, 4 means global validator and 5 means administrator )</li>
<li>Import the 'sql_template.sql' file in your database.</li>
<li>Upload the files on your server (ftp).</li>
<li>Go on your website, by default, a user with the administrating privileges is created with the login 'administrator' and the password 'roomplan'. </li>
<li>Go to the «rooms_preimport.php» file on the server, select the file containing the rooms data and import it.</li>
<li>Go to the «users_preimport.php» file on the server, select the file containing the users data and import it.</li>
<li>Go to the  <span class="warning">floors_edit.php</span> page to enter each floor of your building with the specific map (as given as a svg in the maps folder)</li>
<li>Logout and go the «mdp_reset.php» file and enter your login. It will force you to generate a password.</li>
<li class="warning">Delete the generic user 'administrator' with the password 'roomplan'. (otherwise anyone can hack the roomplan tool)</li>

<li class="warning">Go to the « rooms_list.php » page and check that the maps are correctly displayed and that all the rooms entered are shaded in green on the map.</li>
<li class="warning">Go to the « users_list.php » page and check that the users are correctly displayed with their information.</li>
</ol>



<?php include('includes/foot.php'); ?>  
