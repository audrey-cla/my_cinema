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
$date = $runtime = 0;

if (isset($_GET['film'])) {
    $film = $_GET['film'];
    
    if($film == 0)
    echo "Erreur: pas de film reseigné";
    return;
} else {
    echo "Erreur: pas de film reseigné";
    return;
}

$id_sql = $pdo->query('SELECT id_film FROM film WHERE titre LIKE "' . $film . '" ');
$id_sql = $id_sql->fetch();
if ($id_sql != NULL) {
    $id_sql = $id_sql[0];
}

$stmt = $pdo->query("SELECT * FROM film WHERE titre LIKE '$film' ");
$poster = $id_ext = $resume = $video = '';
if (strpos($film, ' and ') == true) {
    $film = str_replace(' and ', ' & ', $film);
}

$filmplus = str_replace(' ', '+', $film);
$json = file_get_contents('http://api.themoviedb.org/3/search/movie?api_key=a87445ad8c99b9725de371b7541c0ba8&query=' . $filmplus);
$obj = json_decode($json);

$countresults = count($obj->results); // Compte le nombre de résultat où le nom est compris
if ($countresults != 0) {
    for ($i = 0; $i < $countresults; $i++) {
        $id_ext =  $obj->results[$i]->id;
        $realtitle =  $obj->results[$i]->original_title;
        $translatetitre = $obj->results[$i]->title;

        if (strpos($translatetitre, ' and ') == true) {
            $film = str_replace(' & ', ' and ', $film);
        }
        $length = strlen($film);
        $totallenght = strlen($realtitle);
        if (
            $countresults == 1 || (substr($realtitle, -$length) == $film) == true  || (substr($realtitle, 0, $length) == $film) == true
            || strcasecmp($realtitle, $film) == 0 || strcasecmp($translatetitre, $film) == 0
        ) {
            $id_ext =  $obj->results[$i]->id;
            $poster = $obj->results[$i]->poster_path;
            break;
        }
    }

    $trailer = file_get_contents('http://api.themoviedb.org/3/movie/' . $id_ext . '?api_key=a87445ad8c99b9725de371b7541c0ba8&append_to_response=videos');
    $trailer = json_decode($trailer);
    $isit = $trailer->videos->results;

    if ($isit != false) {
        $video_key = $trailer->videos->results[0]->key;
        $video = '<h3>trailer</h3><iframe width="560" height="315" src="https://www.youtube.com/embed/' . $video_key . '" frameborder="0" allowfullscreen></iframe>';
    } else {
        $video = '';
    }
}

if ($poster != NULL)
    $poster = "<img src='https://image.tmdb.org/t/p/original/$poster' class='poster' alt='poster_$film'>";
else
    $poster = "<img src='https://www.my-bourg.ch/wp-content/uploads/2018/11/noavailable.png' class='poster' alt='poster_$film'>";

$details = $genre = $distrib = '';

if ($stmt == false) {
    if (isset($obj->results[$i])) {
        $resume = '<h3>Synopsis</h3>' . $obj->results[$i]->overview;
        $date = $obj->results[$i]->release_date;
        $genre =  $trailer->genres[0]->name;
        $runtime = $trailer->runtime;
        $distrib = $trailer->production_companies[0]->name;
    }
} else {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $resume =  '<h3>Synopsis</h3>' . $row['resum'];
        $date = $row['date_debut_affiche'];
        $runtime = $row['duree_min'];
        $id_distrib = $row['id_distrib'];
        $id_genre = $row['id_genre'];
        $genre = $pdo->query("SELECT nom FROM genre WHERE id_genre LIKE '$id_genre' ");
        $distrib = $pdo->query("SELECT nom FROM distrib WHERE id_distrib LIKE '$id_distrib' ");
        $genre = $genre->fetch();
        if (isset($genre['nom']))
            $genre = $genre['nom'];
        else
            $genre = '';
        $distrib = $distrib->fetch();
        if (isset($distrib['nom']))
            $distrib = $distrib['nom'];
        else
            $distrib = '';
        break;
    }
}

if ($date != NULL)
    $details = 'Release: ' . $date . ' | ';
if ($genre != NULL)
    $details .= 'Genre: ' . $genre . ' | ';
if ($runtime != NULL)
    $details .= 'Runtime: ' . $runtime . ' min | ';
if ($distrib != NULL)
    $details .= 'Distribution: ' . $distrib;
echo "  <div class='movie_table'><table><tr><td rowspan='2'>" . $poster . "</td><td><h2>$film</h2><div class='movie_details'><h3>details</h3>$details";
if ($id_ext != '') {
    echo "  </div><div class='movie_cast'><h3>Cast</h3>";
    $cast = file_get_contents('https://api.themoviedb.org/3/movie/' . $id_ext . '/credits?api_key=a87445ad8c99b9725de371b7541c0ba8');
    $cast = json_decode($cast);
    $count_crew = $cast->cast;
    $count_crew = count($count_crew);
    for ($j = 0; $j < $count_crew; $j++) {
        if ($j <= 14) {
            echo $crew = $cast->cast[$j]->name . ', ';
        } else {
            echo $crew = $cast->cast[$j]->name . '...';
            break;
        }
    }
}
echo " </div><div class='movie_resume'>$resume</div><div class='movie_trailer'>$video</div></td></tr></table>";

$avis = $pdo->query('SELECT * FROM historique_membre WHERE id_film LIKE "' . $id_sql . '" ');
while ($ligne = $avis->fetch(PDO::FETCH_ASSOC)) {
    if ($ligne['avis'] != '') {
        $id_membre = $ligne['id_membre'];
        $membre_id = $pdo->query("SELECT * FROM fiche_personne WHERE id_perso LIKE $id_membre");
        $id_membre = $membre_id->fetch();
        $pseudo = ucfirst($id_membre[2]) . ' ' . ucfirst($id_membre[1]);
        $avis_date = "le " . $ligne['date'];
        echo " <div class='movie_avis'> <div class='avis_details'>par $pseudo | $avis_date </div><p>" . ucfirst($ligne['avis']) . "</p></div>";
    }
}
echo "</div>";
$contenu = ob_get_clean(); // autorise le deversement d'information dans la variable contenu du site
include 'template.php';
