<?php

///ENTER YOUR CONNECTION PARAMETERS HERE AND THEN RENAME THIS FILE AS db_connect.php

try{
   $bdd = new PDO('mysql:host=localhost;dbname=YOUR-DATABASE-NAME;charset=utf8', 'YOUR USER NAME FOR THE SQL DATABASE', 'YOUR PASSWORD TO CONNECT TO THE SQL DATABASE');
}catch (Exception $e){
        die('Erreur : ' . $e->getMessage());
}
//ENTER THE MAIL OF THE CONTACT PERSON FOR PASSWORD RECOVERY
$mail_sender=array("name"=>'John Doe',"mail"=>"john.doe@mail.org");



/////////////// DON'T TOUCH EVERYTHING BELOW /////////////



function requete_preparee_update($submit)
{
	$SQL='';
	$i=0;
    foreach ($submit as $key => $value) {
	 if(strpos($key, 'id_') === 0 AND $i==0) {
        unset($submit[$key]);
    }else{
            $SQL.= '`'.$key.'`=:'.$key.', ';}
        $i++;}
	return rtrim($SQL,", ");
} 
function requete_preparee_insert($string)
{//(`id_cours`, `sujet`, `sujet_court`, `nb_seance`, `ref_intervenant_1`, `ref_intervenant_2`, `type`, `actif`, `dominante`, `finished`, `commentaire_prive`, `commentaire_public`)
	$SQL=$string.' VALUES (';
	$simple=str_replace(array('(',')','`'),'', $string);
	$keys=explode(',', $simple);
    foreach ($keys as $key => $value) {
	             $SQL.= ':'.trim($value).', ';
        }
	return rtrim($SQL,", ").')';
} 

function allowedRights($bdd){
	$people=array();
	
	$submit=array("id_slot"=>0,'year'=>0,"week"=>0,'date'=>'',"day"=>0,"start"=>0,"end"=>0,"length"=>0,"ref_room"=>0,"ref_user"=>intval($_SESSION['id_user']),'commentaire'=>'','valid'=>1);
	if(in_array($_SESSION['roomStatus'],array(4,5))){ // administrator and controllers can act on all users
			$reponse=$bdd->query("SELECT u.id_user,u.prenom,u.nom FROM `roomusers` AS u WHERE u.active=1 AND u.roomStatus>0");
			while($person=$reponse->fetch(PDO::FETCH_ASSOC)){
				$people[]=$person;} 	
			$submit['valid']=4;				
	}else if(in_array($_SESSION['roomStatus'],array(3))){ // team heads can act on all people from their team
			$reponse=$bdd->prepare("SELECT u.id_user,u.prenom,u.nom FROM `roomusers` AS u WHERE u.team=:team AND u.active=1 AND u.roomStatus>0");
			$reponse->execute(array('team'=>$_SESSION['team']));								 
			while($person=$reponse->fetch(PDO::FETCH_ASSOC)){
			$people[]=$person;} 		
			$submit['valid']=3;
	}else if(in_array($_SESSION['roomStatus'],array(2))){ // Permanent people can act on all the persons under their supervision
			$reponse=$bdd->prepare("SELECT u.id_user,u.prenom,u.nom FROM `roomusers` AS u WHERE u.active=1 AND (u.ref_responsable=:id_user OR u.id_user=:id_user) ORDER BY nom  AND u.roomStatus>0");
			$reponse->execute(array('id_user'=>$_SESSION['id_user']));								 
			while($person=$reponse->fetch(PDO::FETCH_ASSOC)){
				$people[]=$person;} 		
			$submit['valid']=2;	
	}else{
		$people[]=array('id_user'=>intval($_SESSION['id_user']), 'prenom'=>$_SESSION['prenom'],'nom'=>$_SESSION['nom']);
	}
	return $people;

}



if (!function_exists('array_column')) {
    /**
     * Returns the values from a single column of the input array, identified by
     * the $columnKey.
     *
     * Optionally, you may provide an $indexKey to index the values in the returned
     * array by the values from the $indexKey column in the input array.
     *
     * @param array $input A multi-dimensional array (record set) from which to pull
     *                     a column of values.
     * @param mixed $columnKey The column of values to return. This value may be the
     *                         integer key of the column you wish to retrieve, or it
     *                         may be the string key name for an associative array.
     * @param mixed $indexKey (Optional.) The column to use as the index/keys for
     *                        the returned array. This value may be the integer key
     *                        of the column, or it may be the string key name.
     * @return array
     */
    function array_column($input = null, $columnKey = null, $indexKey = null)
    {
        // Using func_get_args() in order to check for proper number of
        // parameters and trigger errors exactly as the built-in array_column()
        // does in PHP 5.5.
        $argc = func_num_args();
        $params = func_get_args();
        if ($argc < 2) {
            trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
            return null;}
        if (!is_array($params[0])) {
            trigger_error(
                'array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given',
                E_USER_WARNING
            );
            return null;}
        if (!is_int($params[1]) 
            && !is_float($params[1])
            && !is_string($params[1])
            && $params[1] !== null
            && !(is_object($params[1]) && method_exists($params[1], '__toString'))
        ) {
            trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
            return false;}
        if (isset($params[2])
            && !is_int($params[2])
            && !is_float($params[2])
            && !is_string($params[2])
            && !(is_object($params[2]) && method_exists($params[2], '__toString'))
        ) {
            trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
            return false;}
        $paramsInput = $params[0];
        $paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;
        $paramsIndexKey = null;
        if (isset($params[2])) {
            if (is_float($params[2]) || is_int($params[2])) {
                $paramsIndexKey = (int) $params[2];
            } else {
                $paramsIndexKey = (string) $params[2];}
        }
        $resultArray = array();
        foreach ($paramsInput as $row) {
            $key = $value = null;
            $keySet = $valueSet = false;
            if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
                $keySet = true;
                $key = (string) $row[$paramsIndexKey];}
            if ($paramsColumnKey === null) {
                $valueSet = true;
                $value = $row;
            } elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
                $valueSet = true;
                $value = $row[$paramsColumnKey];}
            if ($valueSet) {
                if ($keySet) {
                    $resultArray[$key] = $value;
                } else {
                    $resultArray[] = $value;}
            }
        }
        return $resultArray;
    }
}


?>

