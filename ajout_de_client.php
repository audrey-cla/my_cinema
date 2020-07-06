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

ob_start(); // debut deversement

echo "<ul><li><a href='search_client.php'>recherche clients</a></li>
<li><a href='ajout_de_client.php'>ajouter un client</a></li>
<li><a href='get_place.php'>ajouter une place</a></li>
</ul>";
$menu = ob_get_clean(); // push du deversement

ob_start(); // empeche le deversement d'information dans la page avant le chargement du template

echo '<div class="add_client"><form class="add_client" action="ajout_de_client.php" method="POST">' . "
ajouter un client ?<br>
<input type='text' pattern='[^<>()]+' name='nom' placeholder='Last Name here' required><br>
<input type='text' pattern='[^<>()]+' name='prenom' placeholder='First Name here' required><br>
<input type='text' name='email' placeholder='email here' required><br>
<input type='date' name='date_naissance' required><br>
<input type='text' name='cpostal' pattern='[0-9]*'  placeholder='Zip code here'  required><br>
<input type='text' pattern='[^<>()]+' name='ville' placeholder='City here' required>
<br><input type='submit' class='search_but' name='submit_new_client' value='ajouter le client'>
</form></div>";
$datee = new DateTime('NOW');

if (isset($_POST['submit_new_client'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $date_naissance = $date->format('Y-m-d  H:i:s');;
    $cpostal = $_POST['cpostal'];
    $ville = $_POST['ville'];

    $biggest = $pdo->query("SELECT MAX(id_perso) FROM fiche_personne");
    $biggest->execute();
    $row = $biggest->fetch();
    $biggest = $row[0];
    $id_perso = $biggest + 1;
    $adresse = ' ';
    $data = [
        'id_perso' => $id_perso, 'nom' => $nom, 'prenom' => $prenom, 'date_naissance' => $date_naissance, 'email' => $email,
        'adresse' => $adresse, 'cpostal' => $cpostal, 'ville' => $ville,
    ];
    $sql = "INSERT INTO fiche_personne (id_perso,nom,prenom,date_naissance,email,adresse,cpostal,ville) VALUES ($id_perso,\"$nom\", \"$prenom\", \"$date_naissance\", \"$email\", \"$adresse\" ,\"$cpostal\",\"$ville\")";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute($data) == true) {
        echo " Client ajouté !";
    }
}

$contenu = ob_get_clean(); // autorise le deversement d'information dans la variable contenu du site
include 'template_user.php';
