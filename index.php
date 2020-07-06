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

ob_start(); // empeche le deversement d'information dans la page avant le chargement du template
$findgenre = $finddistrib = $findtitre = $getgenre = $getdistrib = $gettitre = '';
if (isset($_GET['genre']) >= 1) {
    $genre =  $_GET['genre'];
    $findgenre = "WHERE id_genre = '$genre'";
    $getgenre = "&genre=" . $genre;
}
if (isset($_POST['genre'])) {
    $genre = $_POST['genre'];
    $findgenre = "WHERE id_genre = '$genre'";
    $getgenre = "&genre=" . $genre;
}
if (isset($_GET['distrib']) >= 1) {
    $distrib =  $_GET['distrib'];
    $finddistrib = "WHERE id_distrib = '$distrib'";
    $getdistrib = "&distrib=" . $distrib;
}
if (isset($_POST['distrib'])) {
    $distrib = $_POST['distrib'];
    $getdistrib = "&distrib=" . $distrib;
    if ($distrib != NULL) {
        if (isset($_POST['genre']))
            $finddistrib = " AND id_distrib = '$distrib'";
        else
            $finddistrib = " WHERE id_distrib = '$distrib'";
    }
}
if (isset($_GET['titre']) != '') {
    $titre =  $_GET['titre'];

    if ($titre != NULL) {
        if (isset($_GET['genre']) || isset($_GET['distrib']))
            $findtitre = ' AND titre LIKE  "%'.$titre.'%"';
        else
            $findtitre = ' WHERE titre LIKE  "%'.$titre.'%"';
    }
    $gettitre = "&titre=" . $titre;
}
if (isset($_POST['titre'])) {
    $titre = $_POST['titre'];
    $gettitre = "&titre=" . $titre;
    if ($titre != NULL) {
        if (isset($_POST['genre']) || isset($_POST['distrib']))
        $findtitre = ' AND titre LIKE  "%'.$titre.'%"';
        else
            $findtitre = ' WHERE titre LIKE  "%'.$titre.'%"';
    }
}
$parpage = 100;
$count = $pdo->query("SELECT COUNT(titre) FROM film  $findgenre $finddistrib $findtitre");
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
$stmt = $pdo->query("SELECT * FROM film  $findgenre $finddistrib $findtitre LIMIT $start,$parpage");

echo "<div class='nav_pages'>";
for ($i = 1; $i <= $totaldepage; $i++) {
    if (isset($_GET['genre']) >= 1) {
        echo "<a href='index.php?start=" . $i . $getgenre . $getdistrib . $gettitre . "'>" . $i . "</a>  ";
    } else {
        echo "<a href='index.php?start=" . $i . $getgenre .  $getdistrib .  $gettitre . "'>" . $i . "</a>  ";
    }
}
echo "</div><table class='list'>";

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
 echo  '<tr class="border_td"><td><a href="film.php?film=' . $row['titre'] . '">'. $row['titre'] . '</a></td></tr>';
}
echo "</table>";

$contenu = ob_get_clean(); // autorise le deversement d'information dans la variable contenu du site
include 'template.php';
