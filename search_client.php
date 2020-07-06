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

ob_start(); // debut deversement
echo "<ul><li><a href='search_client.php'>recherche clients</a></li>
<li><a href='ajout_de_client.php'>ajouter un client</a></li>
<li><a href='get_place.php'>ajouter une place</a></li>
</ul>";
$menu = ob_get_clean(); // push du deversement

ob_start(); // empeche le deversement d'information dans la page avant le chargement du template
$findprenom = $findnom = $getprenom = $getnom = '';

echo '<div class="user_search"><h2>Recherche client</h2>
<form action="search_client.php" method="POST">
    <input type="text" pattern="[^<>()]+" maxlength="40" id="search_nom" name="nom" placeholder="Last Name here">
    <input type="text" id="search_prenom" pattern="[^<>()]+" maxlength="40" name="prenom" placeholder="First Name here">
    <input type="submit" id="search_but" name="submit" value=">>">
    <br>
</form></div>';

if (isset($_GET['nom']) != '') {
    $nom =  $_GET['nom'];
    if ($nom != NULL) {
         $findnom = " WHERE nom LIKE '%$nom%'";
    }
    $getnom = "&nom=" . $nom;
} 
if (isset($_POST['nom'])) {
    $nom = $_POST['nom'];
    $getnom = "&nom=" . $nom;
    if ($nom != NULL) {
            $findnom = " WHERE nom  LIKE '%$nom%'";
    }
}

if (isset($_GET['prenom']) != '') {
    $prenom =  $_GET['prenom'];

    if ($prenom != NULL) {
        if ($nom != NULL) {
            $findprenom = " AND prenom LIKE  '%$prenom%'";
         } else
            $findprenom = " WHERE prenom  LIKE '%$prenom%'";
    }
    $getprenom = "&prenom=" . $prenom;
}
if (isset($_POST['prenom'])) {
    $prenom = $_POST['prenom'];
    $getprenom = "&prenom=" . $prenom;
    if ($prenom != NULL) {
        if ($nom != NULL) {
              $findprenom = " AND prenom LIKE  '%$prenom%'";
         } else
            $findprenom = " WHERE prenom  LIKE '%$prenom%'";
    }
}

$parpage = 60;
$count = $pdo->query("SELECT COUNT(id_perso) FROM fiche_personne  $findnom  $findprenom");
$count->execute();
$row = $count->fetch();
$totalcount = $row[0];
$totaldepage = ceil($totalcount / $parpage);

if (isset($_GET['start']) >= 1) ///  MET LA VALEUR DE LA PAGE 1 PAR DEFAUT
    $page = $_GET['start'];
else {
    $page = 1;
}

$start = $page * $parpage - $parpage;
$stmt = $pdo->query("SELECT * FROM fiche_personne  $findnom  $findprenom LIMIT $start,$parpage");

echo "<div class='nav_pages'>";
for ($i = 1; $i <= $totaldepage; $i++) {
    if (isset($_GET['genre']) >= 1) {
        echo "<a href='search_client.php?start=" . $i . $getprenom . $getnom . "'>" . $i . "</a>  ";
    } else {
        echo "<a href='search_client.php?start=" . $i . $getprenom .  $getnom .  "'>" . $i . "</a>  ";
    }
}
echo "</div><table class='list'>";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

 echo  "<tr class='border_td'><td><a href='gestion_client.php?id=" . $row["id_perso"] . "'>"
        . ucfirst($row["nom"]) . '  ' . ucfirst(strtolower($row["prenom"])) ."</a></td>"
        ."</tr>";
}
echo "</table><br><br>";


$contenu = ob_get_clean(); // autorise le deversement d'information dans la variable contenu du site
include 'template_user.php';
