<?php include('includes/head.php'); ?>
		<title>Set/Reset my password</title>
<?php 
	include ('includes/body.php');
?>	
	
<?php
$object=array();
if (!in_array($_SESSION['roomStatus'],array(1,2,3,4,5)) AND isset($_POST['login']) ) {
$submit = array('login' => $_POST['login']);

$nobody = 0;
if ($submit['login'] != '') {
		//Looking if someone with this login exists in the database
      $reponse = $bdd->prepare("SELECT COUNT(*) AS userexists FROM `roomusers` WHERE login=:login AND active=1");
      $reponse->execute(array('login'=>$submit['login']));
      $donnees = $reponse->fetch();	
	if ($donnees['userexists'] == 1) {
    	  $reponse =  $bdd->prepare("SELECT * FROM `roomusers` WHERE login=:login AND active=1");
			$reponse->execute(array('login'=>$submit['login']));
      	$donnees = $reponse->fetch();	
        $submit_2= array('id'=>0,'ref'=>$donnees['id_user'],'cle_tmp'=>hash_mdp($submit['login'].date('Y-m-d H:i:s',time()).$donnees['password_2']),'timestamp'=>date('Y-m-d H:i:s',time()+7200));   
        $mail=$donnees['mail'];
    }else {        
        $nobody = 1;}
    
		if($nobody == 1){
			echo "There is nobody in the database with your login";
		} else{
		   $request = $bdd->prepare("INSERT INTO `room_recovery`".requete_preparee_insert("(`id`, `ref`, `cle_tmp`, `timestamp`)"));
		   $request->execute($submit_2);
		   //echo var_export($request->errorInfo());
		   $retour = $bdd->lastInsertId();
		   $id = $retour;
		        
		   $headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/plain; charset=UTF-8' . "\r\n";
			$headers .= 'From: '. $mail_sender['name'].' <'.$mail_sender['mail'].">\r\n";
			$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			$actual_link = str_replace('mdp_reset.php', 'mdp_change.php', $actual_link); 
			
$message='Hello,
        
You sent a message to change your password, for that, please follow the link below         

'.$actual_link.'?id='.$id.'&cle_tmp='.$submit_2['cle_tmp'].'

This link will be valid for two hours, after this delay, you will have to make a new request.

If you did not make a demand, do nothing.

With my best


'.$mail_sender['name'];   
		        if(mail($mail,'[SaRoomAn] Password recovery',wordwrap($message, 70, "\r\n"),$headers)){
		        		  echo "<br>An e-mail was sent to the following adress : ".$mail;
		        }else{
						echo "<br>Error when the recovery mail was sent.";}	
		}    
	}
}

echo '<h1>Change my password</h1>
<form method="post" action="mdp_reset.php"> 
<label for="login" class="label_court">Your login<sup>*</sup> :</label> &nbsp;'.
input_r('login', $object, 20).'<br><br><input type="submit" value="Send an e-mail">
</form>
<p>If you forgot both your login and password, ask your local informatician</p>';


		
 ?> 

<?php include('includes/foot.php'); ?>  





