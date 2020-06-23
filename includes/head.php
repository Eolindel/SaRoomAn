<?php session_start();

if(!empty($_POST) OR !empty($_FILES))
{
    $_SESSION['sauvegarde'] = $_POST ;
    $_SESSION['sauvegardeFILES'] = $_FILES ;
    $fichierActuel = $_SERVER['PHP_SELF'] ;
    if(!empty($_SERVER['QUERY_STRING']))
    {
        $fichierActuel .= '?' . $_SERVER['QUERY_STRING'] ;
    }    
    header('Location: ' . $fichierActuel);
    exit;
}

if(isset($_SESSION['sauvegarde']))
{
    $_POST = $_SESSION['sauvegarde'] ;
    $_FILES = $_SESSION['sauvegardeFILES'] ;
    unset($_SESSION['sauvegarde'], $_SESSION['sauvegardeFILES']);
}



if(empty($_SESSION['nom'])){$_SESSION = array();
		$_SESSION['roomStatus']='';
		$_SESSION['id']='';
		$_SESSION['nom']='';
		$_SESSION['login']='';
		$_SESSION['prenom']='';
		$_SESSION['logged']=0;
} 


include('db_connect.php');






function hash_mdp($string){
	$hash=hash('sha256', str_rot13($string).$string.crc32($string), false);
	for($i=0;$i<10;$i++)	{
		$hash=hash('sha256', str_rot13($hash).$hash.crc32($hash), false);
	}
	return $hash;
}

function input_r($value,$donnees,$size=0,$type='',$required=0,$autofocus=0,$set=''){
	$modif='';
	if($size!=0){$modif=' size="'.$size.'"';}
	if($type!='' AND $type!='number'){$modif.=' type="'.$type.'"';}
	if($required==1){$modif.=' required="required"';}
	if($autofocus==1){$modif.=' autofocus';}
	if(isset($donnees[$value])){$modif.=' value="'.$donnees[$value].'"';
	}else if ($set!=''){$modif.=' value="'.$set.'"';
	}
	
	return '<input name="'.$value.'" id="'.$value.'"'.$modif.'>'."\n";
}

function select($value,$id,$legende,$object,$critere){
	if(isset($object[$critere]) AND $object[$critere]==$value)
	{return "\t\t\t".'<option value="'.$value.'" id="'.$id.'"  selected="selected">'.$legende.'</option>'."\n";
	} else {	return "\t\t\t".'<option value="'.$value.'" id="'.$id.'">'.$legende.'</option>'."\n";}
}
function radio_r($name,$value,$comment,$montage){	
$check='';
	if(isset($montage[$name]) AND $montage[$name]==$value){$check=' checked="checked"';}
   return '<input type="radio" name="'.$name.'" value="'.$value.'" id="'.$name.$value.'"'.$check.'><label for="'.$name.$value.'" class="label_radio">'.$comment.'</label> '."\n";
} 

function checkbox_r($name,$value,$legende,$decode=array(),$class=''){
	if(isset($decode[$name]) AND $decode[$name]==1)
	{return '<input type="checkbox" name="'.$name.'" id="'.$name.'" checked="checked" value="'.$value.'" class="'.$class.'"><label for="'.$name.'"> '.$legende.'</label>';}
	else{return '<input type="checkbox" name="'.$name.'" id="'.$name.'" value="'.$value.'"  class="'.$class.'"><label for="'.$name.'"> '.$legende.'</label>';}
}

function checkbox2_r($name,$value,$legende,$decode=array(),$class=''){
	if(isset($decode[$name]) AND $decode[$name]==1)
	{return '<input type="checkbox" name="'.$name.'" id="'.$name.'" checked="checked" value="'.$value.'" class="'.$class.'"><label for="'.$name.'"> '.$legende.'</label>';}
	else{return '<input type="checkbox" name="'.$name.'" id="'.$name.$value.'" value="'.$value.'"  class="'.$class.'"><label for="'.$name.$value.'"> '.$legende.'</label>';}
}


function endswith($string, $test) {
    $strlen = strlen($string);
    $testlen = strlen($test);
    if ($testlen > $strlen) return false;
    return substr_compare($string, $test, $strlen - $testlen, $testlen) === 0;
}
?>

<!doctype html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<link rel="icon" type="image/png" href="images/favicon.png">
	<link rel="start" title="Accueil" href="index.php">

   <script type="text/javascript" src="https://d3js.org/d3.v4.min.js"></script>
	<script type="text/javascript" src="includes/d3-simple-slider.min.js"></script>   
   
   <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>	 
	<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.4.5/jquery-ui-timepicker-addon.min.js"></script>
	<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.4.5/i18n/jquery-ui-timepicker-fr.js"></script>
	
	<link rel="stylesheet" type="text/css" href="includes/jquery-ui-1.12.1/jquery-ui.css" />
	<link rel="stylesheet" type="text/css" href="http://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.4.5/jquery-ui-timepicker-addon.css" />	
	<link type="text/css" href="styles.css" rel="stylesheet">
