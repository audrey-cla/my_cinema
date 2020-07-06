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
                    <?php echo $menu; ?>
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