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
<li><a href=''>gestion salle</a></li>
</ul>";
$menu = ob_get_clean(); // push du deversement

ob_start(); // empeche le deversement d'information dans la page avant le chargement du template

$contenu = ob_get_clean(); // autorise le deversement d'information dans la variable contenu du site
include 'template_user.php';
