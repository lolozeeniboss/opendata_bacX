<?php
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
    $request = "https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search/?dataset=fr-esr-principaux-diplomes-et-formations-prepares-etablissements-publics&facet=etablissement_lib";
    if (!empty($post_departement)) {
        $request = $request . "&refine.dep_etab_lib=" . $post_departement;
    } else if (!empty($post_diplome)) {
        $request = $request . "&refine.diplome_rgp=" . $post_diplome;
    } else if (!empty($post_filiere)) {
        $request = $request . "&refine.discipline_lib=" . $post_filiere;
    } else {
        $request = "";
        $form = false;
    }
    $json_content_request = file_get_contents($request);
    $json_request = json_decode($json_content_request);
    $record_request = $json_request->records;
    $IDs = array();
    $myschoolsname = array();
    foreach ($record_request as $school) {
        array_unique($IDs);
        if (!(in_array($school->fields->etablissement, $IDs))) {
            $IDs[] = $school->fields->etablissement;
            $myschoolsname[] = $school->fields->etablissement_lib;
        }
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bac+x</title>
    <link rel="stylesheet" href="css/finalstyle.css">

    <!--old-->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css"
          integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
          crossorigin=""/>
    <meta charset="utf-8">
    <title>BAC+x</title>
    <script src="JS/jquery-3.4.1.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"
            integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew=="
            crossorigin=""></script>
    <!--    ----->

</head>
<body>
<div class="box">
    <div class="row header">
        <nav>
            <ul>
                <form action="index.php" method="POST" name="form">
                    <select class="select" placeholder="votre diplome" name="diplome">
                        <option value="" selected>-- votre diplome --</option>
                        <?php
                        foreach ($facets_dip as $facet) {
                            $data = $facet->path;
                            print "<option value=\"" . $data . "\">" . $data . "</option>";
                        }

                        ?>
                    </select>
                    <select class="select" placeholder="votre departement" name="departement">
                        <option value="" selected>-- votre departement --</option>
                        <?php
                        foreach ($facets_dep as $facet) {
                            $data = $facet->path;
                            print "<option value=\"" . $data . "\">" . $data . "</option>";
                        }
                        ?>
                    </select>
                    <select class="select" placeholder="votre filiere" name="filiere">
                        <option value="" selected>-- votre filiere --</option>
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
        </nav>
    </div>
    <div class="row content">
        <?php
        if ($form) {
            ?>
            <div class="mySchools">
                <?php
                if (isset($record_request)) {
                    ?>
                    <div class="infobox infobox-2">
                        <?php
                        echo "<p>nous avons trouvé " . sizeof($myschoolsname) . " résultat(s) en " . $post_filiere . " dans le département de " . $post_departement . " pour un " . $post_diplome . "</p>";
                        ?>
                    </div>
                    <div class="mySchoolsinner">
                        <?php
                        foreach ($myschoolsname as $school) {
                            ?>
                            <div class="infobox infobox-1">
                                <?php
                                echo "<p>" . $school . "</p>";
                                ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="infobox">
                        <?php
                        echo "nous n'avons trouvé aucun résultat en " . $post_filiere . " dans le département de " . $post_departement . " pour un " . $post_diplome . "<br>";
                        ?>
                    </div>
                    <?php

                }
                ?>
            </div>
            <?php
        }
        ?>

        <div id="mapcontainer" style="width: <?php echo $form ? '80%' : '100%'; ?>">
            <div id="mapid"></div>
        </div>
    </div>
    <footer class="row footer">
        <nav>
            <ul class="importantlinks">
                <li><a href="https://github.com/lolozeeniboss/opendata_bacX.git">en savoir plus(GitHub)</a></li>
            </ul>
        </nav>
    </footer>
</div>
<script src="JS/map.js"></script>
</body>
</html>