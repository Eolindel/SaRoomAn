<?php include('includes/head.php'); ?>
		<title>Log in</title>
		
	
		<?php
		$submit=array('login'=>'',
					'password'=>'',);
$wrong_pwd=0;
$nobody=0;
$unsure=0;
foreach ($submit as $key => $value)	{
	if(isset($_POST[$key]))		{
		$submit[$key]=$_POST[$key];}}

if($submit['login']!='')	{
	$reponse = $bdd->prepare("SELECT * FROM `roomusers` WHERE login=:login AND active='1'");
	$reponse->execute(array("login"=>$submit['login']));
	
	//echo var_export($reponse->errorInfo());
	$user=array();
	while($donnees =$reponse->fetch()){
		$user=$donnees;}

	if(empty($user)){//Check if the login entered is correct
		$nobody=1;
	}else{
		if($user['password_2']==hash_mdp($_POST['password'])){
			$user['userexists']=1;
			$_SESSION['roomStatus']=$user['roomStatus'];
			$_SESSION['team']=$user['team'];
			$_SESSION['id_user']=$user['id_user'];
			$_SESSION['login']=$user['login'];
			$_SESSION['nom']=$user['nom'];
			$_SESSION['mail']=$user['mail'];
			if(intval($user['ref_responsable'])!=0 ){
				$_SESSION['ref_responsable']=$user['ref_responsable'];
			}
			$_SESSION['prenom']=$user['prenom'];
			$_SESSION['logged']=1;		
			
			if($user['password_2']==hash_mdp(strtolower($user['login'])))	{
				$unsure=2;
			}elseif($user['password_2']==hash_mdp(strtolower($user['prenom']))){
				$unsure=1;
			}elseif($user['password_2']==hash_mdp('')){
				$unsure=3;
			}				
		}else{$wrong_pwd=1;}
	}
}

		?>	
<?php include('includes/body.php'); ?> 



<?php

	
	

if($nobody==1){
	echo '<h2>Wrong login</h2>
	<p class="warning">There is nobody in the database with your login.</p>';
}else if($wrong_pwd==1){
	echo '<h2>Wrong Password</h2>';
	if($unsure==1)	{
		echo '<p class="warning">Your password is your first name in lower caps.</p>';
		echo '<p>Your password may have been resetted due to a technical error, I\'m truly sorry for any disturbance. <a href="mailto:martin.verot@ens-lyon.fr">Martin</a>.';
	}	elseif($unsure==2){
		echo '<p class="warning">Your password is your login in lower caps.</p>';
	}	elseif($unsure==3){
		echo '<p class="warning">Enter only your login, leave the password empty and then you can enter.</p>
		<p>Your password has been changed to nothing. This is due to a technical error from my part and is now resolved, I\'m truly sorry for the disturbance generated. Martin</p>';
	}	else{
		echo '
	<p class="warning">You entered a wrong password.</p>
	<a href="mailto:martin.verot@ens-lyon.fr?subject=Reset My Password&amp;body=My $@%*!!° password is not working, please reset it. Thanks" class="submit">Ask to reset the password</a><br>';	
	}
}

if(!isset($_SESSION['roomStatus']) OR $_SESSION['roomStatus']==0){ ?>
<h2>Log in</h2>
<form method="post" action="connexion.php">
<label for="login" class="label_court">Log in<sup>*</sup>  : </label> 
	<input name="login" id="login"> 
	<abbr data-tip="The same as for the webmail"><img src="images/icones/help.png" alt="help"></abbr>
	<br> 
	
<label for="password" class="label_court">Password<sup>*</sup>  : </label> 
	<input type="password" name="password" id="password"> 
	<abbr data-tip="Unset at the beginning, follow the lost password link"><img src="images/icones/help.png" alt="help"></abbr>
		<p class="right"><a href="mdp_reset.php">Forgotten password</a></p>


<input type="submit" value="Log in">
</form>

<p>If you forgot your login, contact the financial manager, he will be able to reset your password.</p>
 
<?php 
}else{
	echo '<h2>You are logged in as '.$_SESSION['prenom'].' '.$_SESSION['nom'].'</h2>';
	if($unsure!=0)	{
	echo '<p class="warning">Your password is a default one and is unsure. You must change it.</p>
			<a href="adduser.php?id='.$_SESSION['id'].'" class="submit">Change password</a>';	}
}

?>
<?php include('includes/foot.php'); ?>  
