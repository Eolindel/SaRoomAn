<?php include('includes/head.php'); ?>
<title>Manual for roomplan</title>
  
<?php include('includes/body.php'); ?>  
  
<h1>Installation</h1>

<h2>Initialize the connexion settings</h2>
<ol>
    <li>In the file "db_connect-TO-MODIFY.php" in the « includes » folder, change your mySQL connecting parameters : "dbname", "user" and corresponding "password". This user must have a writing and reading privelege over the corresponding database. Change also the mail and name of the contact person for the recovery mail or initialization of the password.</li>
    <li>Upload the files on your server (ftp).</li>
    <li>Create the different databases by importing the "import_bdd.sql"</li>
    <li><span class="warning">Create a log in as an administrator</span></li>
</ol>


<h2>Import the data for rooms and create the svg maps of the floors (with inkscape) </h2>
<ol>
    <li>Put the characteristics of each room in the "room.csv" file of the « import_files » folder, the crucial data are the 'building', 'floor', 'idSvg', 'officeName', 'places' (usual capacity of the room) and 'max' columns (threshold imposed by your local rules)</li>
    <li><span class="warning">Log in as the administrator</li>
    <li>Import the rooms in the database from the "Batch add/update of rooms" in the left part of the window</li>
    <li>Create a map of each floor and building (under inkscape) <a href="https://youtu.be/NfdDnW24XrU">Instructions (in French) here</a> and put them in the « maps » folder.</li>
    <li>Go to the  <span class="warning">floors_edit.php</span> page to enter each floor of your building with the specific map (as given as a svg in the "maps" folder)</li>
</ol>

<h2>Import the list of users to initialize the database</h2>
<ol>
    <li>Put your list of users in the file users.csv file of the « import_files » folder, the crucial columns are 'First Name', 'Last Name', 'active' (put a 1, a 0 will disable the corresponding person), 'Mail' (used as unique identifier), 'Office', 'Workplace' (if people work in a dedicated room on top of their office), 'roomStatus' (1 means basics user, 2 means user able to validate the schedule of people working directly under their supevision, 3 means local validator and will be able to validate the schedule of all the people in the same team, 4 means global validator and 5 means administrator )</li>
    <li>Go to the «users_preimport.php» file on the server, select the file containing the users data and import it.</li>
</ol>


<h2>Check your imported data</h2>
<ol>
    <li class="warning">Go to the « rooms_list.php » page and check that the maps are correctly displayed and that all the rooms entered are shaded in green on the map.</li>
    <li class="warning">Go to the « users_list.php » page and check that the users are correctly displayed with their information.</li>
</ol>



<?php include('includes/foot.php'); ?>  
