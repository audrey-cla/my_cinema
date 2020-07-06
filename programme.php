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
ob_start(); // empeche le deversement d'information dans la page avant le chargement du template

$parpage = 100;
$count = $pdo->query("SELECT COUNT(id_film) FROM prog");
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
$stmt = $pdo->query("SELECT * FROM prog LIMIT $start,$parpage");

echo "<div class='nav_pages'>";
if ($totaldepage > 1) {
    for ($i = 1; $i <= $totaldepage; $i++) {
        echo "<a href='programme.php?start=" . $i . "'>" . $i . "</a>  ";
    }
}
echo '</div><div class="movie_result_container"><h2>Programme de cette semaine</h2>';

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $details = "";
    $id_film_api = $row['id_film_api'];
    $id_film = $row['id_film'];

    $fetch_all = $pdo->query("SELECT * FROM film WHERE id_film = $id_film");
    $fetch_all = $fetch_all->fetchAll();
    $titre = $fetch_all[0]['titre'];
    $resume = $fetch_all[0]['resum'];
    $genre = $fetch_all[0]['id_genre'];
    $runtime = $fetch_all[0]['duree_min'];

    $fetch_sceance = $pdo->query("SELECT * FROM grille_programme WHERE id_film = $id_film");
    $fetch_sceance = $fetch_sceance->fetchAll();

    $genre = $pdo->query("SELECT nom FROM genre WHERE id_genre = $genre");
    $genre = $genre->fetchAll();
    if ($genre != NULl)
        $details .= "Genre: " . $genre[0]['nom'] . ' | ';
    if ($runtime != NULl)
        $details .= "Runtime: " . $runtime;

    $json = file_get_contents('http://api.themoviedb.org/3/movie/' . $id_film_api . '?api_key=a87445ad8c99b9725de371b7541c0ba8');
    $obj = json_decode($json);
    $poster = $obj->poster_path;

    $seances = "Séances:  ";
    if (count($fetch_sceance) == NULL) {
        $seances .= "aucune séance aujourd'hui";
    } else {
        for ($i = 0; $i < count($fetch_sceance); $i++) {
            $seances_total = $fetch_sceance[$i]['debut_sceance'];
            $seances .= substr($seances_total, -8, 5);
            if ($i + 1 != count($fetch_sceance))
                $seances .= ", ";
        }
    }
    if ($poster != NULL) {
        $poster = "<img src='https://image.tmdb.org/t/p/original/$poster' class='mini_poster' alt='poster_$titre'>";
    } else {
        $poster = "<img src='https://www.my-bourg.ch/wp-content/uploads/2018/11/noavailable.png' class='mini_poster' alt='poster_$titre'>";
    }
    echo '<div class="movie_result">' . $poster . '<a class="movie_title" href="film.php?film=' . $titre . '">' . $titre . '</a><p>'
        . $resume . '</p><p>' . $details . '</p><p>' . $seances . '</p></div>';
}
echo '</div>';

$contenu = ob_get_clean(); // autorise le deversement d'information dans la variable contenu du site
include 'template.php';
