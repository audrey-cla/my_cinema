<?php
$host = 'localhost';
$dbname = 'cinema';
$dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;
try {
    $pdo = new PDO($dsn, 'root', '');
} catch (PDOException $e) {
    die('Error: Data base inconnue');
}

ob_start(); // empeche le deversement d'information dans la page avant le chargement du template

echo '<div class="login_form"><h2>Connectez-vous a votre compte</h2><form action="login.php" method="POST">
<input type="text" name="email" placeholder="email here" required><br>
<input type="password" pattern="[^<>()]+" maxlength="30" name="password" placeholder="password here" required>
<br><input type="submit" class="search_but" name="login" value="se connecter">
</form></div>';

if (isset($_POST['login'])) {
    if ($_POST['email'] != '') {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $get_account = $pdo->query('SELECT * FROM login WHERE email LIKE "' . $email . '"');
        $get_account = $get_account->fetchAll();
        //   var_dump($get_account);

        if ($get_account == NULL) {
            echo "<div class='sorry_user_not_found '>impossible de se connecter, votre adresse mail est inconnue, veuillez recommencer</div>";
        } else {
            if ($get_account[0]['password'] == $password) {
                $id = $get_account[0]['id_perso'];
                if ($email == 'admin@admin.fr') {
                    header('Location: administrateur.php');
                    exit();
                } else {
                    header('Location: acc_client.php?id=' . $id);
                    exit();
                }
            } else {
                echo "<div class='sorry_user_not_found '>impossible de se connecter, mot de passe incorrect, veuillez recommencer</div>";
            }
        }
    }
}
$contenu = ob_get_clean(); // autorise le deversement d'information dans la variable contenu du site
include 'template.php';
