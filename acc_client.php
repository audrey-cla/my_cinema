<?php
$host = 'localhost';
$dbname = 'cinema';
$dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;
global $pdo;
try { 
    $pdo = new PDO($dsn, 'root', '');
 }
catch (PDOException $e) {
    die('Error: Data base inconnue');
 }

$date = new DateTime('NOW');

if (isset($_GET['id']))
{
    $id = $id_membre = $_GET['id'];
    if ($id == '') {
        echo "Erreur: pas d'utilisateur renseigné";
        return;
    }
}
else {
echo "Erreur: pas d'utilisateur reseigné";
return;
}

ob_start(); // debut deversement
echo '<ul><li><a href="acc_client.php?id='.$id.'">voir mon profil</a></li>
<li><a href="historique_client.php?id='.$id.'">voir mon historique</a></li>
</ul>';
$menu = ob_get_clean(); // push du deversement
ob_start(); // empeche le deversement d'information dans la page avant le chargement du template

$stmt = $pdo->query("SELECT * FROM fiche_personne WHERE id_perso = $id");
$user_details = $stmt->fetchAll();

if ($user_details == null) {
    echo "erreur 404: utilisateur introuvable";
} else {
    $date_naissance = ucfirst($user_details[0]['date_naissance']);
    $date_naissance = str_replace("00:00:00", "", $date_naissance);
    echo "<div class='client_table '><h2>détail client</h2>";
    echo "<table><tr><td><p>Nom:  " . ucfirst($user_details[0]['nom']) . "</p><p>Prenom:  " . ucfirst($user_details[0]['prenom'])
        . "</p><p>Date-de-naissance:  " . $date_naissance
        . "</p><p>Ville:  " . $user_details[0]['ville'] . "</p></td></tr></table>";
    $is_abo = $pdo->query("SELECT * FROM membre WHERE id_membre = $id");
    $is_abo = $is_abo->fetchAll();
    if ($is_abo != NULL) {
        $id_abo = $is_abo[0]['id_abo'];
        $get_abo = $pdo->query("SELECT * FROM abonnement WHERE id_abo = $id_abo");
        $get_abo = $get_abo->fetchAll();
        if ($get_abo != NULL) {
            $date_abo = $is_abo[0]['date_abo'];
            if (strpos($date_abo, "00:00:00") == true) {
                str_replace("00:00:00", "", $is_abo[0]['date_abo']);
            }
            if ($get_abo[0]['nom'] == "GOLD")
                $img_abo = 'https://cdn.discordapp.com/attachments/671429055672877116/671822619951104012/gold.png';
            else if ($get_abo[0]['nom'] == "VIP")
                $img_abo = 'https://media.discordapp.net/attachments/671429055672877116/671822624191545365/vip.png';
            else if ($get_abo[0]['nom'] == "pass day")
                $img_abo = 'https://media.discordapp.net/attachments/671429055672877116/671822621523968020/pqssdqy.png';
            else if ($get_abo[0]['nom'] == "Classic")
                $img_abo = 'https://media.discordapp.net/attachments/671429055672877116/672174408504180776/classic.png';
            else
                $img_abo = '';
            echo "<h2>Abonnement</h2>
                <table class='table_abo'><tr><td><img class='img_abo' src='" . $img_abo . "'></td><td>"
                . "<p>Type de l'abonnement: " . $get_abo[0]['nom'] . "</p>"
                . "<p>Prix: " . $get_abo[0]['prix'] . "€</p>"
                . "<p>Valable durant: " . $get_abo[0]['duree_abo'] . " jours</p>"
                . "</td>";
            echo "<td><div class='modifier_abo'><h3>Modifier l'abonnement</h3><div class='boite_modif'>" .
                "<form class='modif_abo' action='acc_client.php?id=" . $id . "' method='POST'>
                <select name='abonnement_modif'><option value='0'>choisissez un abonnement</option><option value='1'>VIP</option>
                <option value='2'>GOLD</option><option value='3'>Classic</option><option value='4'>Pass Day</option>
                </select>
                <input type='date' name='date_modif'><input type='submit' class='search_but' name='change_abo' value='>>'></form>";
            echo "</div><h3>Suprimer l'abonnement</h3>
                <form class='supp_abo' action='acc_client.php?id=" . $id . "' method='POST'>
                Supprimer l'abonnement en cours ? <input type='submit' class='search_but' name='remove_abo' value='OK'></form>"
                . "</div></td></tr></table>";
        } else {
            add_abo($id, $pdo, $date);
        }
    } else {
        add_abo($id, $pdo, $date);
    }
}

function add_abo($id, $pdo, $date)
{
    echo "<h2>Abonnement</h2><form class='choose_abo' action='acc_client.php?id=" . $id . "' method='POST'>
    ajouter un abonnement au membre ici
    <select name='abonnement'>
	<option value='0'>choisissez un abonnement</option>
	<option value='1'>VIP</option>
	<option value='2'>GOLD</option>
	<option value='3'>Classic</option>
	<option value='4'>Pass Day</option>
    </select>
    <input type='submit' class='search_but' name='submit_abo' value='>>'>
        <br>
    </form>";
    if (isset($_POST['submit_abo'])) {
        if ($_POST['abonnement'] != 0) {
            $id_membre = $id;
            $id_fiche_perso = 0;
            $id_abo = $_POST['abonnement'];
            $date_inscription = $date->format('Y-m-d');
            $data = ['id_membre' => $id_membre, 'id_fiche_perso' => $id_fiche_perso, 'id_abo' => $id_abo, 'date_inscription' => $date_inscription];
            $sql = "INSERT INTO membre (id_membre,id_fiche_perso,id_abo,date_inscription) VALUES (:id_membre, :id_fiche_perso, :id_abo, :date_inscription)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
        }
        echo "<meta http-equiv='refresh' content='0'>";
    }
}

if (isset($_POST['change_abo'])) {
    $id_abo = $_POST['abonnement_modif'];
    if ($_POST['date_modif'] != 0) {
        $date_inscription = $_POST['date_modif'];
    } else {
        $date_inscription = $date->format('Y-m-d');
    }
    if ($id_abo != 0) {
        $data = [
            'id_abo' => $id_abo,
            'date_inscription' => $date_inscription,
            'id' => $id,
        ];
        $sql = "UPDATE membre SET id_abo=:id_abo, date_inscription=:date_inscription WHERE id_membre=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
    }
    echo "<meta http-equiv='refresh' content='0'>";
}


if (isset($_POST['remove_abo'])) {
    $sql = "DELETE FROM membre WHERE id_membre = $id ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id_membre' => $id]);
    echo "<meta http-equiv='refresh' content='0'>";
}


echo "</div>";
$contenu = ob_get_clean(); // autorise le deversement d'information dans la variable contenu du site
include 'template_user.php';
