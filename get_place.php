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
echo "<ul><li><a href='search_client.php'>recherche clients</a></li>
<li><a href='ajout_de_client.php'>ajouter un client</a></li>
<li><a href='get_place.php'>ajouter une place</a></li></ul>";
$menu = ob_get_clean(); // push du deversement

ob_start(); // empeche le deversement d'information dans la page avant le chargement du template
$get_prog = $pdo->query("SELECT * FROM film INNER JOIN prog ON film.id_film = prog.id_film");

echo '<div class="collation_pack"><form action="test.php"  method="POST"><h2>Séance</h2><label for="id_member">id membre :  </label><input type="text" pattern="[0-9]" name="id_member" required>
<select class="choose_sceance" name="add_sceance"><option value="0">choisir une scéance<option>';
while ($row = $get_prog->fetch(PDO::FETCH_ASSOC)) {
    echo '<option value="' . $row["id_film"] . '">' . $row["titre"] . '</option>';
}
$date = $date->format('Y-m-d  H:i:s');

echo '</select>
<h2>Collations</h2>
<div class="container_collation"><div class="collation">
    <img src="https://cdn.discordapp.com/attachments/671429055672877116/671446150917914645/skittles.png" alt="sendhelp"><div>sendhelp<input type="number" name="sendhelp" min="0"></div></div>
    <div class="collation"><img src="https://cdn.discordapp.com/attachments/671429055672877116/671429355951489054/pangobar.png" alt="pango"><div>pango
    <input type="number" name="pango"min="0"></div></div><div class="collation"><img src="https://cdn.discordapp.com/attachments/671429055672877116/671446147729981450/coca.png" alt="coca">
    <div>coca<input type="number" name="coca" min="0"></div></div><div class="collation">
    <img src="https://cdn.discordapp.com/attachments/671429055672877116/671447189373059092/coca_zero.png" alt="coca_zero"><div>coca zero<input type="number" name="coca_zero" min="0"></div>
    </div><div class="clear"></div><input type="submit" class="big_button" name="add_place" value="valider la place"></div></form></div>';

if (isset($_POST['add_place'])) {
    if ($_POST["add_sceance"] != 0) {
        $id_film = $_POST["add_sceance"];
        $avis = '';
        $data = ['id_membre' => $id_membre, 'id_film' => $id_film, 'date' => $date, 'avis' => $avis];
        $sql = "INSERT INTO historique_membre (id_membre,id_film,date,avis) VALUES (:id_membre, :id_film, :date, :avis)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
    }
    $id_member = $_POST["id_member"];
    if ($_POST["sendhelp"] == '')
        $sendhelp = 0;
    else
        $sendhelp = $_POST["sendhelp"];

    if ($_POST["pango"] == '')
        $pango = 0;
    else
        $$pango = $_POST["pango"];

    if ($_POST["coca"] == '')
        $coca = 0;
    else
        $coca = $_POST["coca"];

    if ($_POST["coca_zero"] == '')
        $coca_zero = 0;
    else
        $coca_zero = $_POST["coca_zero"];
    $data = ['id_membre' => $id_membre, 'date' => $date, 'sendhelp' => $sendhelp, 'pango' => $pango, 'coca' => $coca, 'coca_zero' => $coca_zero];
    $sql = "INSERT INTO collations (id_membre,date,sendhelp,pango,coca,coca_zero) VALUES (:id_membre, :date, :sendhelp,:pango,:coca,:coca_zero)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
    echo "<meta http-equiv='refresh' content='0'>";
}

$contenu = ob_get_clean(); // autorise le deversement d'information dans la variable contenu du site
include 'template_user.php';
