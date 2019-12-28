<?php
include "data/phpdata/départementfr.php";
?>
<!DOCTYPE html>
<html dir="ltr" lang="fr">
<head>
    <link href="css/style.css" rel="stylesheet">
    <meta charset="utf-8">
    <title>Projet temporairement sans nom</title>
    <script src="JS/jquery-3.4.1.min.js"></script>
</head>
<body>
<div id="panel">
    <div class="main_menu" id="panel-inner">
        <nav>
            <lu>
                <div class="TypeDiplomeFormation">
                    <select name="diplome" id="diplome" placeholder="Type de diplôme ou de formation">
                        <option value="diplome1">diplome1</option>
                        <option value="diplome3">diplome2</option>
                        <option value="diplome3">diplome3</option>
                        <option value="diplome4">diplome4</option>
                    </select>
                </div>
            </lu>
            <lu>
                <div class="localisation département">
                    <select name="DP" id="DP" placeholder="votre département">
                        <?php
                        $req = file_get_contents("https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search/?dataset=fr-esr-principaux-diplomes-et-formations-prepares-etablissements-publics&facet=rentree_lib&facet=etablissement_type2&facet=etablissement_type_lib&facet=etablissement&facet=etablissement_lib&facet=champ_statistique&facet=dn_de_lib&facet=cursus_lmd_lib&facet=diplome_rgp&facet=diplome_lib&facet=typ_diplome_lib&facet=diplom&facet=niveau_lib&facet=disciplines_selection&facet=gd_disciscipline_lib&facet=discipline_lib&facet=sect_disciplinaire_lib&facet=spec_dut_lib&facet=localisation_ins&facet=com_etab&facet=com_etab_lib&facet=uucr_etab&facet=uucr_etab_lib&facet=dep_etab&facet=dep_etab_lib&facet=aca_etab&facet=aca_etab_lib&facet=reg_etab&facet=reg_etab_lib&facet=com_ins&facet=com_ins_lib&facet=uucr_ins&facet=dep_ins&facet=dep_ins_lib&facet=aca_ins&facet=aca_ins_lib&facet=reg_ins&facet=reg_ins_lib&refine.rentree_lib=2017-18&refine.etablissement_type2=Grand+%C3%A9tablissement");
                        $tab = json_decode($req, true);

                        foreach ($tab["records"] as $res){
                            print("<option value='00'>".$res["fields"]["etablissement_lib"]."</option>");
                        }

                        ?>
                    </select>
                </div>
            </lu>
            <lu>
                <li>
                    <form action="index.php" class="" method="post">
                        <div class="slidecontainer">
                            <h2>Bac+: <span id="output"></span></h2>
                            <label for="myRange"></label>
                            <input class="slider" id="myRange" max="8" min="1" type="range" value="0">
                        </div>
                    </form>
                </li>
            </lu>
        </nav>
    </div>

    <div class="wideScreen">
        <button id="flip">
            <img alt="arrowup" src="IMG/arrow.svg">
        </button>
    </div>
    <div class="mySchoolOrigin">
    </div>
</div>

<div class="Map">
</div>
<script src="JS/slider.js"></script>
<script src="JS/showAndHideMenu.js"></script>
<footer>
    <nav class="primalien">
        <ul>
            <li><a href="source.html">source</a></li>
        </ul>
    </nav>
</footer>
</body>
</html>