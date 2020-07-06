<?php
$host = 'localhost';
$dbname = 'cinema';
$dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;
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
}
else {
echo "Erreur: pas d'utilisateur reseigné";
return;
}

ob_start(); // debut deversement
echo "<ul><li><a href='acc_client.php?id=$id'>voir mon profil</a></li>
<li><a href='historique_client.php?id=$id'>voir mon historique</a></li>
</ul>";
$menu = ob_get_clean(); // push du deversement

ob_start(); // empeche le deversement d'information dans la page avant le chargement du template
$id = $id_membre = $_GET['id'];
$stmt = $pdo->query("SELECT * FROM fiche_personne WHERE id_perso = $id");
$user_details = $stmt->fetchAll();

if ($user_details == null) {
    echo "erreur 404: utilisateur introuvable";
} else {
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
    echo "<div class='client_table '><h2>Historique</h2><br><table class='list'>";
    echo '<form class="add_histo" action="historique_client.php?id=' . $id . '" method="POST">' . "
    ajouter un film à l'historique<br>
    <input type='text' pattern='[^<>()]+' id='add_movie' name='titre' placeholder='titre here' required>
    <br><input type='date' id='add_date' name='date'>
    <br><textarea pattern='[^<>]+' maxlength='100' name='avis'></textarea>
    <br><input type='submit' class='search_but' name='submit_histo' value='ajouter le film'>
    </form>";
    echo "<div class='nav_pages'>";
    for ($i = 1; $i <= $totaldepage; $i++) {
        if (isset($_GET['genre']) >= 1) {
            echo "<a href='historique_client.php?id=$id&start=$i'>" . $i . "</a>  ";
        } else {
            echo "<a href='historique_client.php?id=$id&start=$i'>" . $i . "</a>  ";
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
echo "</div>";
$contenu = ob_get_clean(); // autorise le deversement d'information dans la variable contenu du site
include 'template_user.php';
