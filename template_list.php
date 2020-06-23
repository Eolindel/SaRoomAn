<?php include('includes/head.php'); ?>
<title>Liste des pièces dans la base de données</title>
  
<?php include('includes/body.php'); ?>  
   
<?php

if(in_array($_SESSION['roomStatus'], array(1,2,3,4,5))){
	
	

		$reponse=$bdd->prepare("SELECT * FROM `template` WHERE ref_user=:ref_user");
		$reponse->execute(array('ref_user'=>$_SESSION['id_user']));
		$line='<h1>My templates</h1><table>';	
		
		$i=0;
		while($donnees=$reponse->fetch()) {
			if($i % 20 == 0 ){
				$line.=th_template();		
			}
			$line.=display_template($donnees);
			$i++;
		}
		$line.='</table>';
		
		
		$line.='<br><a href="template_edit.php" class="submit">Add a new template</a>';	
		echo $line;	

	
	//echo '<script type="text/javascript" src="includes/template_list.js"></script>';	
}
?>
<?php include('includes/foot.php'); ?>  