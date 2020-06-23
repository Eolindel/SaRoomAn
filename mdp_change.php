<?php include ('includes/head.php'); ?>
		<title>New Password</title>
<?php include ('includes/body.php'); ?> 

<?php
if (!isset($_SESSION['statut']) OR empty($_SESSION['statut']) ) {
$donnees=array();
$object=array();
if (isset($_GET['id'])){
	$submit = array('id' => '','cle_tmp' => '');
	foreach ($submit as $key => $value) {
	    if (isset($_GET[$key])) {
	        $submit[$key] =$_GET[$key];
	    }
	}

      $reponse = $bdd->prepare("SELECT * FROM `room_recovery` WHERE id=:id AND cle_tmp=:cle_tmp AND NOW()<timestamp");
      $reponse->execute(array('id'=>$submit['id'],'cle_tmp'=>$submit['cle_tmp']));
      
      if($donnees = $reponse->fetch()){

		$reponse = $bdd->prepare("SELECT * FROM `roomusers` WHERE id_user=:id");
		$reponse->execute(array('id'=>$donnees['ref']));
		$donnees_perso= $reponse->fetch();
		echo '<h1>Changer mon mot de passe ('.$donnees_perso['prenom'].' '.$donnees_perso['nom'].')</h1>
		<form method="post" action="mdp_change.php">
		<label for="password_1" class="label_court">Password<sup>*</sup> :</label> &nbsp;'.input_r('password_1', $object, 20,"password"). 
		'<br><label for="password_2" class="label_court">Confirm password<sup>*</sup> :</label> &nbsp;'.input_r('password_2', $object, 20,"password").input_r('ref', $donnees, 20,"hidden");  
		echo '<p>Your password must contain at least a number and an upper case letter and must be more thant 8 characters long.</p>
		<p class="warning">If you don\'t see https at the beginning of this url, it means that your password will not be sent encrypted when connecting, that is a huge weakness and means that you should define a specific password for this website and not one already used elsewhere. (But the passwords are stored encrypted.)</p>
		<br><input type="submit" value="Change your password" id="submit_mdp">
		<br><span class="warning" id="plop_8">Your password does not have 8 characters or more.</span>
		<br><span class="warning" id="plop_A">Your password does not contain an upper case letter.</span>
		<br><span class="warning" id="plop_1">Your password does not contain a number.</span>
		<br><span class="warning" id="plop_diff">The passwords entered are different.</span>
		</form>';
	}
}


if (isset($_POST['password_1']) AND $_POST['password_1']!=''){
	$submit = array('password_2' => '');
	if (isset($_POST['password_1']) AND isset($_POST['password_2']) AND $_POST['password_2'] == $_POST['password_1'] AND strlen(preg_replace('/[^0-9]/', '', $_POST['password_2']))>0 AND strlen(preg_replace('/[^A-Z]/', '', $_POST['password_2']))>0 AND strlen($_POST['password_2'])>=8)  {
            $submit['password_2'] = hash_mdp($_POST['password_2']);
         $request = $bdd->prepare("UPDATE `roomusers` SET " . requete_preparee_update($submit) . " WHERE id_user=:id");
          $submit['id']=intval($_POST['ref']);
         $request->execute($submit);
   // echo var_export($request->errorInfo());
        echo '<h1>Your password was changed</h1>';

   }
        
        
}
	
?>
  
      <script type="text/javascript" src="includes/mdp_change.js"></script>	

<?php
} ?> 
<?php include ('includes/foot.php'); ?>  
