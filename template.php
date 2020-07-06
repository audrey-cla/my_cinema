<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>MY_CINEMA</title>
    <link rel="stylesheet" href="css.css">
    <link rel="stylesheet" href="framework.css">
    <link href="https://fonts.googleapis.com/css?family=Raleway:900&display=swap" rel="stylesheet">
</head>

<body>
    <div class="navbar  navbar-warning" id="navbar">
        <a href="index.php" class="navbar-name"><img src="https://i.ya-webdesign.com/images/department-clipart-transparent-6.png">My Cinema</a>
        <ul>
            <li><a href="index.php">voir les films</a></li>
            <li><a href="programme.php">voir le programme</a></li>
            <li><a href="">nos salles / abonnements</a></li>
        </ul>
        <ul class="connection">
            <li><a href="login.php">se connecter</a></li>
            <li><a href="login.php">administrateur</a></li>
        </ul>
        <button class="navbar-tog" id="navbar-tog">
            <span class="navbar-tog-burger"></span>
        </button>
        </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col--2 searchbar">
                <div class="col--11 searching">
                    <form action="index.php" method="POST">
                        <input type="text" id="search" name="titre" pattern="[^<>()]+" maxlength="40" placeholder="titre here">
                        <input type="submit" id="search_but" name="submit" value=">>">
                        <br>
                        <?php
                        $inputgenre = $pdo->query("SELECT nom,id_genre FROM genre");
                        while ($row = $inputgenre->fetch(PDO::FETCH_ASSOC)) {
                            echo '<div class="filtre"><label  for="' . $row["nom"] . '">' . $row["nom"] . '</label><input id="' . $row["nom"] . 
                            '" type="radio" name="genre" value="' . $row["id_genre"] . '"></div><br>';
                        }
                        $inputdistrib = $pdo->query("SELECT nom,id_distrib FROM distrib");
                        while ($row = $inputdistrib->fetch(PDO::FETCH_ASSOC)) {
                            echo '<div class="filtre"><label  for="' . $row["nom"] . '">' . $row["nom"] .
                             '</label><input id="' . $row["nom"] . '" type="radio" name="distrib" value="' . $row["id_distrib"] . '"></div><br>';
                        } ?>
                    </form>
                </div>
            </div>
            <div class="col--10 results_side">
                <?php echo $contenu; ?>
            </div>
        </div>
        <footer>
        my_cinema © 2020
        </footer>
</body>

</html>