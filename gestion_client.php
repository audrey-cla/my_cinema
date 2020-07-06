<?php
$host = 'localhost';
$dbname = 'cinema';
$dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;
try {
    $pdo = new PDO($dsn, 'root', '');
} catch (PDOException $e) {
    die('Error: Data base inconnue');
}
$date = new DateTime('NOW');

ob_start(); // debut deversement
echo '<ul><li><a href="search_client.php">recherche clients</a></li>
<li><a href="ajout_de_client.php">ajouter un client</a></li>
<li><a href="get_place.php?id='.$id.'">ajouter une place</a></li></ul>
<li><a href="">gestion salle</a></li>
</ul>';


$menu = ob_get_clean(); // push du deversement

ob_start(); // empeche le deversement d'information dans la page avant le chargement du template

if (isset($_GET['id'])) {
    $id = $id_membre = $_GET['id'];
} else {
    echo "Erreur: pas d'utilisateur reseigné";
    return;
}

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
                "<form class='modif_abo' action='gestion_client.php?id=" . $id . "' method='POST'>
                <select name='abonnement_modif'><option value='0'>choisissez un abonnement</option><option value='1'>VIP</option>
                <option value='2'>GOLD</option><option value='3'>Classic</option><option value='4'>Pass Day</option>
                </select>
                <input type='date' name='date_modif'><input type='submit' class='search_but' name='change_abo' value='>>'></form>";
            echo "</div><h3>Suprimer l'abonnement</h3>
                <form class='supp_abo' action='gestion_client.php?id=" . $id . "' method='POST'>
                Supprimer l'abonnement en cours ? <input type='submit' class='search_but' name='remove_abo' value='OK'></form>"
                . "</div></td></tr></table>";
        } else {
            add_abo($id, $pdo, $date);
        }
    } else {
        add_abo($id, $pdo, $date);
    }



    echo '<h2>Collations</h2>
<div class="container_collation">
    <div class="collation"><img src="https://cdn.discordapp.com/attachments/671429055672877116/671446150917914645/skittles.png" alt="sendhelp"><p>sendhelp</p></div>
    <div class="collation"><img src="https://cdn.discordapp.com/attachments/671429055672877116/671429355951489054/pangobar.png" alt="pango"><p>pango</p></div>
    <div class="collation"><img src="https://cdn.discordapp.com/attachments/671429055672877116/671446147729981450/coca.png" alt="coca"><p>coca</p></div>
    <div class="collation"><img src="https://cdn.discordapp.com/attachments/671429055672877116/671447189373059092/coca_zero.png" alt="coca_zero"><p>coca zero</p></div>
    <div class="clear"></div>
</div>
';




    $parpage = 20;
    $count = $pdo->query("SELECT COUNT(id_film) FROM historique_membre WHERE id_membre = $id");
    $count->execute();
    $row = $count->fetch();
    $totalcount = $row[0];

    $totaldepage = ceil($totalcount / $parpage);

    if (isset($_GET['start']) >= 1) { ///  MET LA VALEUR DE LA PAGE 1 PAR DEFAUT
        $page = $_GET['start'];
    } else {
        $page = 1;
    }

    $start = $page * $parpage - $parpage;
    $get_historique = $pdo->query("SELECT * FROM historique_membre WHERE id_membre = $id LIMIT $start,$parpage");
    echo "<h2>Historique</h2><br><table class='list'>";
    echo '<form class="add_histo" action="gestion_client.php?id=' . $id . '" method="POST">' . "
    ajouter un film à l'historique<br>
    <input type='text' id='add_movie' name='titre'  pattern='[^<>()]+' maxlength='40' placeholder='titre here' required>
    <br><input type='date' id='add_date' name='date'>
    <br><textarea name='avis'></textarea>
    <br><input type='submit' class='search_but' name='submit_histo' value='ajouter le film'>
    </form>";
    echo "<div class='nav_pages'>";
    for ($i = 1; $i <= $totaldepage; $i++) {
        if (isset($_GET['genre']) >= 1) {
            echo "<a href='gestion_client.php?id=$id&start=$i'>" . $i . "</a>  ";
        } else {
            echo "<a href='gestion_client.php?id=$id&start=$i'>" . $i . "</a>  ";
        }
    }
    echo "</div>";
    if (isset($_POST['submit_histo'])) {
        if ($_POST['titre'] != '') {
            $titre = $_POST['titre'];
            $get_titre = $pdo->query('SELECT * FROM film WHERE titre LIKE "' . $titre . '"');
            $get_titre = $get_titre->fetchAll();
            if (empty($get_titre)) {
                echo " Veuiller entre un titre complet";
            } else {
                $id_membre = $id;
                $id_film = $get_titre[0]['id_film'];
                $date = $date->format('Y-m-d  H:i:s');
                $avis = $_POST['avis'];
                $data = ['id_membre' => $id_membre, 'id_film' => $id_film, 'date' => $date, 'avis' => $avis,];
                $sql = "INSERT INTO historique_membre (id_membre,id_film,date,avis) VALUES (:id_membre, :id_film, :date, :avis)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($data);
                echo "<meta http-equiv='refresh' content='0'>";
            }
        }
    }
    while ($row = $get_historique->fetch(PDO::FETCH_ASSOC)) {
        $id_film =  $row['id_film'];
        $get_film = $pdo->query("SELECT * FROM film WHERE id_film = $id_film");
        $get_film = $get_film->fetchAll();
        $avis_date = 'le ' . str_replace('00:00:00', '', $row['date']);
        echo " <div class='movie_historique'><div class='historique_titre'>"
            . '<a href="film.php?film=' . $get_film[0]['titre'] . '">'
            . $get_film[0]['titre'] . "</a>" . "</div><div class='historique_details'>$avis_date </div><div class='clear'></div><p>" . ucfirst($row['avis']) . "</p></div>";
    };
    echo "</table>";
}

function add_abo($id, $pdo, $date)
{
    echo "<h2>Abonnement</h2><form class='choose_abo' action='gestion_client.php?id=" . $id . "' method='POST'>
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
        var_dump($stmt);
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
