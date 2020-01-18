<?php
include "data/phpdata/départementfr.php";


$urldep = "https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search/?dataset=fr-esr-principaux-diplomes-et-formations-prepares-etablissements-publics&rows=0&sort=-rentree_lib&facet=dep_etab_lib&refine.rentree_lib=2017-18";
$urldip = "https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search/?dataset=fr-esr-principaux-diplomes-et-formations-prepares-etablissements-publics&rows=0&sort=-rentree_lib&facet=diplome_rgp&refine.rentree_lib=2017-18";
$urlfil = "https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search/?dataset=fr-esr-principaux-diplomes-et-formations-prepares-etablissements-publics&rows=0&sort=-rentree_lib&facet=discipline_lib&refine.rentree_lib=2017-18";
//mes département
$json_content_dep = file_get_contents($urldep);
$json_dep = json_decode($json_content_dep);
$facets_dep = $json_dep->facet_groups[0]->facets;

//mes diplome
$json_content_dip = file_get_contents($urldip);
$json_dip = json_decode($json_content_dip);
$facets_dip = $json_dip->facet_groups[0]->facets;

//mes filliaires
$json_content_fil = file_get_contents($urlfil);
$json_fil = json_decode($json_content_fil);
$facets_fil = $json_fil->facet_groups[0]->facets;


//mes fonctions
if (isset($_POST["formbutton"])) {
    $form = true;
    $post_diplome = $_POST["diplome"];
    $post_filiere = $_POST["filiere"];
    $post_departement = $_POST["departement"];
    $request = "https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search/?dataset=fr-esr-principaux-diplomes-et-formations-prepares-etablissements-publics&rows=10&facet=etablissement_lib&refine.dep_etab_lib=" . $post_departement . "&refine.diplome_rgp=" . $post_diplome . "&refine.discipline_lib=" . $post_filiere;
    $json_content_request = file_get_contents($request);
    $json_request = json_decode($json_content_request);
    $facets_request = $json_request->facet_groups[2]->facets;
}
?>
<!DOCTYPE html>
<html dir="ltr" lang="fr">
<head>
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css"
          integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
          crossorigin=""/>
    <meta charset="utf-8">
    <title>Projet temporairement sans nom</title>
    <script src="JS/jquery-3.4.1.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"
            integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew=="
            crossorigin=""></script>
</head>
<body>
<div id="panel">
    <div class="main_menu" id="panel-inner">
        <nav>
            <ul>
                <form action="index.php" method="POST" name="form">
                    <select class="select" placeholder="votre diplome" name="diplome">
                        <option value="" disabled selected>-- votre diplome --</option>
                        <?php
                        foreach ($facets_dip as $facet) {
                            $data = $facet->path;
                            print "<option value=\"" . $data . "\">" . $data . "</option>";
                        }

                        ?>
                    </select>
                    <select class="select" placeholder="votre departement" name="departement">
                        <option value="" disabled selected>-- votre departement --</option>
                        <?php
                        foreach ($facets_dep as $facet) {
                            $data = $facet->path;
                            print "<option value=\"" . $data . "\">" . $data . "</option>";
                        }
                        ?>
                    </select>
                    <select class="select" placeholder="votre filiere" name="filiere">
                        <option value="" disabled selected>-- votre filiere --</option>
                        <?php
                        foreach ($facets_fil as $facet) {
                            $data = $facet->path;
                            print "<option value=\"" . $data . "\">" . $data . "</option>";
                        }

                        ?>
                    </select>
                    <input type="submit" value="rechercher" name="formbutton">
                </form>
            </ul>
            <ul>
                <li>
                    <form action="index.php" class="" method="post">
                        <div class="slidecontainer">
                            <h2>Bac+: <span id="output"></span></h2>
                            <label for="myRange"></label>
                            <input class="slider" id="myRange" max="8" min="1" type="range" value="0">
                        </div>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
    <div class="wideScreen">
        <button id="flip">
            <img alt="arrowup" src="IMG/arrow.svg">
        </button>
    </div>
</div>
<div class="mySchools">
    <?php
    if ($form) {
        if (isset($facets_request)) {
            echo "nous avons trouvé " . sizeof($facets_request) . " résultat(s) en " . $post_filiere . " dans le département de " . $post_departement . " pour un " . $post_diplome . "<br><br>";
            foreach ($facets_request as $school) {
                echo ">>>" . $school->{'name'} . "<<<" . "<br>";
            }
        }else{
            echo "nous n'avons trouvé aucun résultat en " . $post_filiere . " dans le département de " . $post_departement . " pour un " . $post_diplome . "<br><br>";
        }
    }
    ?>
</div>
<div id="mapid"></div>
<script src="JS/slider.js"></script>
<script src="JS/showAndHideMenu.js"></script>
<script src="JS/map.js"></script>
</body>
</html>