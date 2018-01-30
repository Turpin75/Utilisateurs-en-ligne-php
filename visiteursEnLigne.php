<?php  
	
try
{
    $bdd = new PDO('mysql:host=localhost;dbname=espace_membres;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
}
catch(Exception $e)
{
    die('Erreur : '. $e->getMessage());
}

// Temps pendant lequel l'utilisateur doit rester actif en secondes
$temps_session = 20;
// date("U") correspond au nombre de seondes depuis le 1er janvier 1970. Ce temps sera utilisé pour savoir si l'utilisateur est toujours actif ou pas
$temps_actuel = date("U");
// Permet d'afficher l'ip de l'utilisateur
$user_ip = $_SERVER['REMOTE_ADDR'];

$req_ip_exist = $bdd->prepare('SELECT * FROM online WHERE user_ip = ?');
$req_ip_exist->execute(array($user_ip));
$ip_exist = $req_ip_exist->rowCount();

if($ip_exist == 0)
{
    $add_ip = $bdd->prepare('INSERT INTO online(user_ip, time_t) VALUES(?,?)');
    $add_ip->execute(array($user_ip, $temps_actuel));
}
else
{
    $update_ip = $bdd->prepare('UPDATE online SET time_t = ? WHERE user_ip = ?');
    $update_ip->execute(array($temps_actuel, $user_ip));
}

$delete_session_time = $temps_actuel - $temps_session;

// On supprime les utlilisateurs qui sont restés plus de 20 secondes sans activité
$del_ip = $bdd->prepare('DELETE FROM online WHERE time_t < ?');
$del_ip->execute(array($delete_session_time));

$req_user_nbr = $bdd->query('SELECT * FROM online');
$user_nbr = $req_user_nbr->rowCount();

?>
