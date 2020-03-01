<?php
$urldep = "https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search/?dataset=fr-esr-principaux-diplomes-et-formations-prepares-etablissements-publics&rows=0&sort=-rentree_lib&facet=dep_etab_lib&refine.rentree_lib=2017-18";
$urldip = "https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search/?dataset=fr-esr-principaux-diplomes-et-formations-prepares-etablissements-publics&rows=0&sort=-rentree_lib&facet=diplome_rgp&refine.rentree_lib=2017-18";
$urlfil = "https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search/?dataset=fr-esr-principaux-diplomes-et-formations-prepares-etablissements-publics&rows=0&sort=-rentree_lib&facet=sect_disciplinaire_lib&refine.rentree_lib=2017-18";
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
    /* résultats */
    $form = true;
    $post_diplome = $_POST["diplome"];
    $post_filiere = $_POST["filiere"];
    $post_departement = $_POST["departement"];
    $request = "https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search/?dataset=fr-esr-principaux-diplomes-et-formations-prepares-etablissements-publics&facet=etablissement_lib&rows=3000&refine.rentree_lib=2017-18";
    if (!empty($post_departement)) {
        $request = $request . "&refine.dep_etab_lib=" . $post_departement;
    }
    if (!empty($post_diplome)) {
        $request = $request . "&refine.diplome_rgp=" . $post_diplome;
    }
    if (!empty($post_filiere)) {
        $request = $request . "&refine.sect_disciplinaire_lib=" . $post_filiere;
    }
    if (empty($post_departement) && empty($post_diplome) && empty($post_filiere)) {
        $request = "";
        $form = false;
    }
    $json_content_request = file_get_contents($request);
    $json_request = json_decode($json_content_request);
    $record_request = $json_request->records;

    /* coordonées */

    if (!empty($json_request)) {
        $urlcoord = "https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search/?dataset=fr-esr-principaux-etablissements-enseignement-superieur&sort=uo_lib&refine.uai=";
        $coord = array();
        foreach ($record_request as $school) {
            $request = $urlcoord . $school->fields->etablissement;
            $json_coord_request = file_get_contents($request);
            $json_coord_decode = json_decode($json_coord_request);
            $value = $json_coord_decode->records[0]->fields;
            $nextschool = [
                "uai" => $value->uai,
                "X" => $value->coordonnees[0],
                "Y" => $value->coordonnees[1],
                "url" => $value->url,
                "adresse" => empty($value->adresse_uai) ? NULL : $value->adresse_uai,
            ];
            array_push($coord, $nextschool);
        }
    }


}
?>
<!doctype html>
<html lang="fr">
<head>
    <title>Bac+x</title>
    <link rel="stylesheet" href="css/finalstyle.css">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css"
          integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
          crossorigin=""/>
    <meta charset="utf-8">
    <title>BAC+x</title>
    <script src="JS/jquery-3.4.1.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"
            integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew=="
            crossorigin=""></script>

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
                        $phrase = "<p>nous avons trouvé " . sizeof($record_request) . " résultat(s) ";
                        $phrase = empty($post_departement) ? $phrase : $phrase . " à " . $post_departement;
                        $phrase = empty($post_diplome) ? $phrase : $phrase . " pour un(e) " . $post_diplome;
                        $phrase = empty($post_filiere) ? $phrase : $phrase . " en " . $post_filiere;
                        echo $phrase . "</p>";
                        ?>
                    </div>
                    <div class="mySchoolsinner">
                        <?php
                        foreach ($record_request as $school) {
                            foreach ($coord as $schoolinfo) {
                                if ($schoolinfo['uai'] == $school->fields->etablissement) {
                                    $x = $schoolinfo['X'];
                                    $y = $schoolinfo['Y'];
                                }
                            }
                            ?>
                            <div class="infobox infobox-1"
                                 onclick="mymap.flyTo([<?php echo $x ?>, <?php echo $y ?>], 15);">
                                <?php
                                echo "<p>" . $school->fields->etablissement_lib . "</p>";
                                echo "<p>" . $school->fields->diplome_rgp . " en " . $school->fields->discipline_lib . "</p>";
                                if ($school->fields->niveau_lib != "Non détaillé") echo "<p> niveau d'étude :" . $school->fields->niveau_lib . "</p>";
                                if (empty($x)) echo "<sub> aucune information détaillées disponible </sub>";
                                ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="infobox infobox-2">
                        <?php
                        $phrase = "<p> nous n'avons trouvé aucun résultat ";
                        $phrase = empty($post_departement) ? $phrase : $phrase . " à " . $post_departement;
                        $phrase = empty($post_diplome) ? $phrase : $phrase . " pour un(e) " . $post_diplome;
                        $phrase = empty($post_filiere) ? $phrase : $phrase . " en " . $post_filiere;
                        echo $phrase . "</p>";
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
<script type="text/javascript">

    <?php
    foreach ($coord as $schoolinfo) {
    if (!empty($schoolinfo['uai'])){
    ?>
    var marker = L.marker([<?php echo $schoolinfo['X'] ?>, <?php echo $schoolinfo['Y'] ?>]).addTo(mymap);
    marker.bindPopup("<b>adresse :</b><br /><p><?php echo $schoolinfo['adresse']?></p><br /><a href=\"<?php echo $schoolinfo['url'] ?>\"> <?php echo empty($schoolinfo['url']) ? "lien non renseigné" : $schoolinfo['url'] ?> </a>");
    <?php
    }
    }
    ?>
</script>
</body>
</html>