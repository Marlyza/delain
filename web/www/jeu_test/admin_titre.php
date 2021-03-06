<?php

include "blocks/_header_page_jeu.php";

include_once '../includes/tools.php';

function insert_evt($mod_perso_cod, $texte_evt, $levt_visible = 'N', $levt_lu = 'N')
{
    $levt                  = new ligne_evt();
    $levt->levt_tevt_cod   = 43;
    $levt->levt_perso_cod1 = $mod_perso_cod;
    $levt->levt_texte      = $texte_evt;
    $levt->levt_attaquant  = $mod_perso_cod;
    $levt->levt_cible      = $mod_perso_cod;
    $levt->levt_lu         = $levt_lu;
    $levt->levt_visible    = $levt_visible;
    $levt->stocke(true);
    unset($levt);
}

?>
    <script>//# sourceURL=admin_titre.js

        function select_onglet(onglet) {
            if (onglet == 'titre') {
                $("#td-onglet-butin").removeClass("onglet").addClass("pas_onglet");
                $("#td-onglet-recompense").removeClass("onglet").addClass("pas_onglet");
                $("#td-onglet-titre").removeClass("pas_onglet").addClass("onglet");
                $("#onglet-butin").css("display", "none");
                $("#onglet-recompense").css("display", "none");
                $("#onglet-titre").css("display", "block");
                $("#section-recherche-objet").css("display", "none");
                $("#bouton-objet-butin").css("display", "none");
                $("#bouton-objet-recompense").css("display", "none");
            } else if (onglet == 'recompense') {
                $("#td-onglet-titre").removeClass("onglet").addClass("pas_onglet");
                $("#td-onglet-butin").removeClass("onglet").addClass("pas_onglet");
                $("#td-onglet-recompense").removeClass("pas_onglet").addClass("onglet");
                $("#onglet-titre").css("display", "none");
                $("#onglet-butin").css("display", "none");
                $("#onglet-recompense").css("display", "block");
                $("#section-recherche-objet").css("display", "block");
                $("#bouton-objet-butin").css("display", "none");
                $("#bouton-objet-recompense").css("display", "block");
            } else {
                $("#td-onglet-titre").removeClass("onglet").addClass("pas_onglet");
                $("#td-onglet-recompense").removeClass("onglet").addClass("pas_onglet");
                $("#td-onglet-butin").removeClass("pas_onglet").addClass("onglet");
                $("#onglet-titre").css("display", "none");
                $("#onglet-recompense").css("display", "none");
                $("#onglet-butin").css("display", "block");
                $("#section-recherche-objet").css("display", "block");
                $("#bouton-objet-butin").css("display", "block");
                $("#bouton-objet-recompense").css("display", "none");
            }
        }


        function updatePersoCount() {
            $('#nb-perso').text("(" + countTitrageList() + " persos)");
        }

        function setNomAndPosPerso(divname, cod) {
            //executer le service asynchrone
            $("#" + divname).text("");
            runAsync({request: "get_table_info", data: {info: "perso_compte_pos", perso_cod: cod}}, function (d) {
                if ((d.resultat == 0) && (d.data) && (d.data.perso_nom)) {
                    if (s_option == "monstre" && d.data.perso_type_perso != 2) {
                        $("#" + divname).html('Vous n\'avez pas les droits pour titrer/récompenser ce perso.');
                        $("#" + divname + "_compte").val(0);
                    } else {
                        $("#" + divname).html(d.data.perso_nom + ' <em style="font-size:10px;"> (X=' + d.data.pos_x + ' X=' + d.data.pos_y + ' ' + d.data.etage_libelle + ')</em>');
                        $("#" + divname + "_compte").val(d.data.compt_cod);
                    }
                }
            });
            updatePersoCount();
        }


        function listCoterie(cod) {
            $("#liste-ajout-rapide").html("");
            runAsync({request: "get_table_info", data: {info: "perso_coterie_pos", perso_cod: cod}}, function (d) {
                var content = "";
                var nb_perso = 0;
                var nb_monstre = 0;
                if ((d.resultat == 0) && (d.data) && (d.data.length > 0)) {
                    for (k in d.data) {
                        var data = d.data[k];
                        if (!isInTitrageList(data.perso_cod) && isAuthorized(data.perso_type_perso))     // si pas déjà dans la list et autorisé
                        {
                            if (data.perso_type_perso == 2) nb_monstre++; else nb_perso++;
                            content += '<div id="s-list-' + k + '" data-perso_cod="' + data.perso_cod + '" data-type_perso="' + data.perso_type_perso + '"><span title="ajouter dans la liste des persos à téléporter"><a href=#><img height="16px" src="/images/up-24.png" onclick="addFromSearchList(' + k + ')"></a>&nbsp;</span>' + data.perso_nom + ' <em style="font-size:10px;"> (X=' + data.pos_x + ' X=' + data.pos_y + ' ' + data.etage_libelle + ')</em></div>';
                        }
                    }
                    if (content != "") content += '<br><input type="button" class="test" value="ajouter tout" onclick="addFromSearchListAll(0)">';
                    if (nb_perso > 0 && nb_monstre > 0) content += '&nbsp;&nbsp;<input type="button" class="test" value="tous les persos" onclick="addFromSearchListAll(1)">';
                    if (nb_perso > 0 && nb_monstre > 0) content += '&nbsp;&nbsp;<input type="button" class="test" value="tous les monstres" onclick="addFromSearchListAll(2)">';
                    $("#liste-ajout-rapide").html(content);
                }
                if (content == "") $("#liste-ajout-rapide").html("Rien trouvé (de nouveau) dans sa coterie...");
            });
        }

        function listSurZone(cod) {
            $("#liste-ajout-rapide").html("");
            runAsync({request: "get_table_info", data: {info: "perso_zone_pos", perso_cod: cod}}, function (d) {
                var content = "";
                var nb_perso = 0;
                var nb_monstre = 0;
                if ((d.resultat == 0) && (d.data) && (d.data.length > 0)) {
                    for (k in d.data) {
                        var data = d.data[k];
                        if (!isInTitrageList(data.perso_cod) && isAuthorized(data.perso_type_perso))     // si pas déjà dans la list et autorisé
                        {
                            if (data.perso_type_perso == 2) nb_monstre++; else nb_perso++;
                            content += '<div id="s-list-' + k + '" data-perso_cod="' + data.perso_cod + '" data-type_perso="' + data.perso_type_perso + '"><span title="ajouter dans la liste des persos à téléporter"><a href=#><img height="16px" src="/images/up-24.png" onclick="addFromSearchList(' + k + ')"></a>&nbsp;</span>' + data.perso_nom + ' <em style="font-size:10px;"> (X=' + data.pos_x + ' X=' + data.pos_y + ' ' + data.etage_libelle + ')</em></div>';
                        }
                    }
                    if (content != "") content += '<br><input type="button" class="test" value="ajouter tout" onclick="addFromSearchListAll(0)">';
                    if (nb_perso > 0 && nb_monstre > 0) content += '&nbsp;&nbsp;<input type="button" class="test" value="tous les persos" onclick="addFromSearchListAll(1)">';
                    if (nb_perso > 0 && nb_monstre > 0) content += '&nbsp;&nbsp;<input type="button" class="test" value="tous les monstres" onclick="addFromSearchListAll(2)">';
                    $("#liste-ajout-rapide").html(content);
                }
                if (content == "") $("#liste-ajout-rapide").html("Rien trouvé (de nouveau) sur sa position...");
            });
        }

        function listControleur(cod) {
            $("#liste-ajout-rapide").html("");
            runAsync({request: "get_table_info", data: {info: "perso_compte_pos", perso_cod: cod}}, function (d) {
                var content = "";
                var nb_perso = 0;
                var nb_monstre = 0;
                if ((d.resultat == 0) && (d.data) && (d.data.length > 0)) {
                    for (k in d.data) {
                        var data = d.data[k];
                        if (!isInTitrageList(data.perso_cod) && isAuthorized(data.perso_type_perso))     // si pas déjà dans la list et autorisé
                        {
                            if (data.perso_type_perso == 2) nb_monstre++; else nb_perso++;
                            content += '<div id="s-list-' + k + '" data-perso_cod="' + data.perso_cod + '" data-type_perso="' + data.perso_type_perso + '"><span title="ajouter dans la liste des persos à téléporter"><a href=#><img height="16px" src="/images/up-24.png" onclick="addFromSearchList(' + k + ')"></a>&nbsp;</span>' + data.perso_nom + ' <em style="font-size:10px;"> (X=' + data.pos_x + ' X=' + data.pos_y + ' ' + data.etage_libelle + ')</em></div>';
                        }
                    }
                    if (content != "") content += '<br><input type="button" class="test" value="ajouter tout" onclick="addFromSearchListAll(0)">';
                    if (nb_perso > 0 && nb_monstre > 0) content += '&nbsp;&nbsp;<input type="button" class="test" value="tous les persos" onclick="addFromSearchListAll(1)">';
                    if (nb_perso > 0 && nb_monstre > 0) content += '&nbsp;&nbsp;<input type="button" class="test" value="tous les monstres" onclick="addFromSearchListAll(2)">';
                    $("#liste-ajout-rapide").html(content);
                }
                if (content == "") $("#liste-ajout-rapide").html("Rien trouvé (de nouveau) pour ce controleur...");
            });
        }

        function listPersoPX(cod) {
            $("#liste-ajout-rapide").html("");
            runAsync({request: "get_table_info", data: {info: "perso_gain_px", perso_cod: cod}}, function (d) {
                var content = "";
                var nb_perso = 0;
                var nb_monstre = 0;
                if ((d.resultat == 0) && (d.data) && (d.data.length > 0)) {
                    for (k in d.data) {
                        var data = d.data[k];
                        if (!isInTitrageList(data.perso_cod) && isAuthorized(data.perso_type_perso))     // si pas déjà dans la list et autorisé
                        {
                            if (data.perso_type_perso == 2) nb_monstre++; else nb_perso++;
                            content += '<div id="s-list-' + k + '" data-perso_cod="' + data.perso_cod + '" data-type_perso="' + data.perso_type_perso + '"><span title="ajouter dans la liste des persos à téléporter"><a href=#><img height="16px" src="/images/up-24.png" onclick="addFromSearchList(' + k + ')"></a>&nbsp;</span>' + data.perso_nom + ' <em style="font-size:10px;"> (X=' + data.pos_x + ' X=' + data.pos_y + ' ' + data.etage_libelle + ')</em></div>';
                        }
                    }
                    if (content != "") content += '<br><input type="button" class="test" value="ajouter tout" onclick="addFromSearchListAll(0)">';
                    if (nb_perso > 0 && nb_monstre > 0) content += '&nbsp;&nbsp;<input type="button" class="test" value="tous les persos" onclick="addFromSearchListAll(1)">';
                    if (nb_perso > 0 && nb_monstre > 0) content += '&nbsp;&nbsp;<input type="button" class="test" value="tous les monstres" onclick="addFromSearchListAll(2)">';
                    $("#liste-ajout-rapide").html(content);
                }
                if (content == "") $("#liste-ajout-rapide").html("Rien trouvé (de nouveau) pour ce monstre...");
            });
        }

        function listPersoTitre(titre) {
            $("#liste-ajout-rapide").html("");
            runAsync({request: "get_table_info", data: {info: "perso_titre", titre: titre}}, function (d) {
                var content = "";
                var nb_perso = 0;
                var nb_monstre = 0;
                if ((d.resultat == 0) && (d.data) && (d.data.length > 0)) {
                    for (k in d.data) {
                        var data = d.data[k];
                        if (!isInTitrageList(data.perso_cod) && isAuthorized(data.perso_type_perso))     // si pas déjà dans la list et autorisé
                        {
                            if (data.perso_type_perso == 2) nb_monstre++; else nb_perso++;
                            content += '<div id="s-list-' + k + '" data-perso_cod="' + data.perso_cod + '" data-type_perso="' + data.perso_type_perso + '"><span title="ajouter dans la liste des persos à téléporter"><a href=#><img height="16px" src="/images/up-24.png" onclick="addFromSearchList(' + k + ')"></a>&nbsp;</span>' + data.perso_nom + ' <em style="font-size:10px;"> (X=' + data.pos_x + ' X=' + data.pos_y + ' ' + data.etage_libelle + ')</em></div>';
                        }
                    }
                    if (content != "") content += '<br><input type="button" class="test" value="ajouter tout" onclick="addFromSearchListAll(0)">';
                    if (nb_perso > 0 && nb_monstre > 0) content += '&nbsp;&nbsp;<input type="button" class="test" value="tous les persos" onclick="addFromSearchListAll(1)">';
                    if (nb_perso > 0 && nb_monstre > 0) content += '&nbsp;&nbsp;<input type="button" class="test" value="tous les monstres" onclick="addFromSearchListAll(2)">';
                    $("#liste-ajout-rapide").html(content);
                }
                if (content == "") $("#liste-ajout-rapide").html("Rien trouvé (de nouveau) pour ce titre...");
            });
        }

        function listPersoListe(liste) {
            $("#liste-ajout-rapide").html("");
            runAsync({request: "get_table_info", data: {info: "perso_liste", liste: liste}}, function (d) {
                var content = "";
                var nb_perso = 0;
                var nb_monstre = 0;
                if ((d.resultat == 0) && (d.data) && (d.data.length > 0)) {
                    for (k in d.data) {
                        var data = d.data[k];
                        if (!isInTitrageList(data.perso_cod) && isAuthorized(data.perso_type_perso))     // si pas déjà dans la list et autorisé
                        {
                            if (data.perso_type_perso == 2) nb_monstre++; else nb_perso++;
                            content += '<div id="s-list-' + k + '" data-perso_cod="' + data.perso_cod + '" data-type_perso="' + data.perso_type_perso + '"><span title="ajouter dans la liste des persos à téléporter"><a href=#><img height="16px" src="/images/up-24.png" onclick="addFromSearchList(' + k + ')"></a>&nbsp;</span>' + data.perso_nom + ' <em style="font-size:10px;"> (X=' + data.pos_x + ' X=' + data.pos_y + ' ' + data.etage_libelle + ')</em></div>';
                        }
                    }
                    if (content != "") content += '<br><input type="button" class="test" value="ajouter tout" onclick="addFromSearchListAll(0)">';
                    if (nb_perso > 0 && nb_monstre > 0) content += '&nbsp;&nbsp;<input type="button" class="test" value="tous les persos" onclick="addFromSearchListAll(1)">';
                    if (nb_perso > 0 && nb_monstre > 0) content += '&nbsp;&nbsp;<input type="button" class="test" value="tous les monstres" onclick="addFromSearchListAll(2)">';
                    $("#liste-ajout-rapide").html(content);
                }
                if (content == "") $("#liste-ajout-rapide").html("Rien trouvé (de nouveau) pour cette liste...");
            });
        }

        function listPersoEtage(type_perso, etage_numero) {
            $("#liste-ajout-rapide").html("");
            runAsync({
                request: "get_table_info",
                data: {info: "perso_etage_pos", type_perso: type_perso, etage_numero: etage_numero}
            }, function (d) {
                var content = "";
                var nb_perso = 0;
                var nb_monstre = 0;
                if ((d.resultat == 0) && (d.data) && (d.data.length > 0)) {
                    for (k in d.data) {
                        var data = d.data[k];
                        if (!isInTitrageList(data.perso_cod) && isAuthorized(data.perso_type_perso) && (nb_perso < 1000))     // si pas déjà dans la list et autorisé
                        {
                            if (data.perso_type_perso == 2) nb_monstre++; else nb_perso++;
                            content += '<div id="s-list-' + k + '" data-perso_cod="' + data.perso_cod + '" data-type_perso="' + data.perso_type_perso + '"><span title="ajouter dans la liste des persos à téléporter"><a href=#><img height="16px" src="/images/up-24.png" onclick="addFromSearchList(' + k + ')"></a>&nbsp;</span>' + data.perso_nom + ' <em style="font-size:10px;"> (X=' + data.pos_x + ' X=' + data.pos_y + ' ' + data.etage_libelle + ')</em></div>';
                        }
                    }
                    if (nb_perso == 1000) content += "<br>La selection a été limitée aux 1000 premiers persos.";
                    if (content != "") content += '<br><input type="button" class="test" value="ajouter tout" onclick="addFromSearchListAll(0)">';
                    if (nb_perso > 0 && nb_monstre > 0) content += '&nbsp;&nbsp;<input type="button" class="test" value="tous les persos" onclick="addFromSearchListAll(1)">';
                    if (nb_perso > 0 && nb_monstre > 0) content += '&nbsp;&nbsp;<input type="button" class="test" value="tous les monstres" onclick="addFromSearchListAll(2)">';
                    $("#liste-ajout-rapide").html(content);
                }
                if (content == "") $("#liste-ajout-rapide").html("Rien trouvé (de nouveau) pour ce controleur...");
            });
        }


        function addRecompenseFromSearchList(list) {
            var chance = $("#chance-objet-recompense").val();
            $('#' + list + ' option').each(function () {
                // calcul du nombre d'objet
                var num = $(this).text().substring($(this).text().indexOf('(') + 1, $(this).text().indexOf(')'));
                for (i = 0; i < num; i++) {   // ajouter autant
                    $("#add-button-recompense").trigger("click");  // simuler le click sur le bouton ajouter un objet
                    var id = $('tr[id^="row-1-"]:last').attr("id");
                    $('#' + id + 'aqelem_param_num_1').val(chance);
                    $('#' + id + 'aqelem_misc_cod').val($(this).val());
                    $('#' + id + 'aqelem_misc_cod').trigger("change");
                }
            });
            $('#' + list).find('option').remove();
        }

        function addButinFromSearchList(list) {
            $('#' + list + ' option').each(function () {
                // calcul du nombre d'objet
                var num = $(this).text().substring($(this).text().indexOf('(') + 1, $(this).text().indexOf(')'));
                $("#add-button-butin").trigger("click");  // simuler le click sur le bouton ajouter un objet
                var id = $('tr[id^="row-2-"]:last').attr("id");
                $('#' + id + 'aqelem_param_num_1').val(num);
                $('#' + id + 'aqelem_misc_cod').val($(this).val());
                $('#' + id + 'aqelem_misc_cod').trigger("change");
            });
            $('#' + list).find('option').remove();
        }


        function addFromSearchListAll(type) {
            $('div[id^="s-list-"]').each(function () {
                var type_perso = $('#s-list-' + this.id.substr(7)).attr("data-type_perso");
                if (type == 0 || type == type_perso) addFromSearchList(this.id.substr(7))
            });
        }

        function addFromSearchList(k) {
            var perso_cod = $('#s-list-' + k).attr("data-perso_cod");
            $("#add-button-perso").trigger("click");  // simuler le click sur le bouton ajouter
            var id = $('tr[id^="row-0-"]:last').attr("id");
            $('#' + id + 'aqelem_misc_cod').val(perso_cod);
            $('#' + id + 'aqelem_misc_cod').trigger("change");
            $('#s-list-' + k).remove();
        }

        function reduceTripletteTitrageList() {
            var compteList = [];
            $('tr[id^="row-0-"]').each(function () {
                var compte = $('#' + this.id + 'aqelem_misc_nom_compte').val();
                if (compte != "") {
                    if (compteList[compte]) {
                        delQueteAutoParamRow($(this), 1);
                    } else {
                        compteList[compte] = true;
                    }
                }
            });
            updatePersoCount();
        }

        function isInTitrageList(cod) {
            var isInList = false;
            $('tr[id^="row-0-"]').each(function () {
                if ($('#' + this.id + 'aqelem_misc_cod').val() == cod) isInList = true;
            });
            return isInList;
        }

        function isAuthorized(type_perso) { // l'admin monstre ne peut titrer que les monstres et on peut titrer les familiers
            if (s_option == "monstre" && type_perso != 2) return false; else return true;
        }

        function countTitrageList() {
            var count = 0;
            $('tr[id^="row-0-"]').each(function () {
                if ($('#' + this.id + 'aqelem_misc_cod').val() > 0) count++;
            });
            return count;
        }

        function checkForm() {
            if (($("#action").val() == "add-titre") || ($("#action").val() == "del-titre")) {
                if ($("#titre").val() == "") {
                    alert("Vous devez saisir un titre à attribuer ou supprimer aux persos!");
                    return false;
                }
            }
            var nb_perso = countTitrageList();
            if (nb_perso == 0) {
                alert("Vous devez sélectionner des persos à titrer/récompenser!");
                return false;
            }
            return true; //confirm("Etes-vous sûr de vouloir titrer "+nb_perso+" perso(s) avec le titre: X="+$("#titre").val()+"?");
        }

    </script>

<?php

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
?>
    <title>ATTRIBUTION DE TITRES</title>
<?php

$droit_modif = 'dcompt_modif_perso';
define('APPEL', 1);
include "blocks/_test_droit_modif_generique.php";
echo '<SCRIPT language="javascript" src="../scripts/controlUtils.js"></SCRIPT>';

if ($erreur == 0)
{
    $pdo = new bddpdo;

    $s_option =
        ($droit['modif_perso'] == 'N') ? "monstre" : "";                               // droit de tp de monstre uniquement
    echo "<script> var s_option='{$s_option}'; </script>"; // Injection javascript

    //=======================================================================================
    // == Main
    //=======================================================================================
    //-- traitement des actions
    if (isset($_REQUEST['methode']))
    {
        // Traitement des actions de téléportation
        if (($_REQUEST['methode'] == "recompense") && ($_REQUEST['action'] == "add-titre"))
        {
            //echo "<pre>"; print_r($_REQUEST); echo "</pre>"; die();
            $titre = $_REQUEST['titre'];
            $log   =
                date("d/m/y - H:i") . " $perso_nom (compte $compt_cod / $compt_nom) Ajout de titre en masse : $titre\n";

            // Récupération du code traitement_perso_admin (dev mode flash : on réfléchi pas on reprend ce qui marche ailleurs) !!!
            foreach ($_REQUEST["aqelem_misc_cod"][0] as $k => $mod_perso_cod)
            {
                if ($mod_perso_cod > 0)
                {
                    // Caracs du perso concerné
                    $req  = "select perso_nom, perso_type_perso from perso where perso_cod = :perso_cod ";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array(":perso_cod" => $mod_perso_cod), $stmt);

                    if ($result = $stmt->fetch())
                    {
                        $mod_perso_nom        = $result['perso_nom'];
                        $mod_perso_type_perso = $result['perso_type_perso'];

                        if ($mod_perso_type_perso != 2 && $s_option == "monstre")
                        {
                            // Un admin monstre qui force le passage pour ajouter un perso!!!
                            $log .= "vous n'avez pas les droits suffisants pour ce perso!";
                        } else
                        {
                            // empecher le double titrage (le même jour)

                            $req  =
                                "select count(*) as count from perso_titre where ptitre_perso_cod=:ptitre_perso_cod and ptitre_titre ilike :ptitre_titre and ptitre_date::date=now()::date ";
                            $stmt = $pdo->prepare($req);
                            $pdo->execute(array(":ptitre_perso_cod" => $mod_perso_cod, ":ptitre_titre" => $titre), $stmt);
                            $result = $stmt->fetch();

                            // titrage
                            if ((int)$result["count"] == 0)
                            {
                                $req  =
                                    "insert into perso_titre (ptitre_perso_cod,ptitre_titre,ptitre_date) values (:ptitre_perso_cod,:ptitre_titre,now()) ";
                                $stmt = $pdo->prepare($req);
                                $pdo->execute(array(":ptitre_perso_cod" => $mod_perso_cod, ":ptitre_titre" => $titre), $stmt);
                                $log .= "Titrage du perso : {$mod_perso_nom} ({$mod_perso_cod}) \n";
                            } else
                            {
                                $log .= "Perso {$mod_perso_nom} ({$mod_perso_cod}) : Non titré, il a déjà reçu le titre aujourd'hui!\n";
                            }
                        }
                    } else
                    {
                        $log .= "Perso #{$mod_perso_cod} : Non trouvé!\n";
                    }
                }
            }
            writelog($log, 'perso_edit');
            echo "<div class='bordiv'><pre>$log</pre></div>";
        } else if (($_REQUEST['methode'] == "recompense") && ($_REQUEST['action'] == "del-titre"))
        {
            //echo "<pre>"; print_r($_REQUEST); echo "</pre>"; die();
            $titre = $_REQUEST['titre'];
            $log   =
                date("d/m/y - H:i") . " $perso_nom (compte $compt_cod / $compt_nom) Suppression de titre en masse : $titre\n";

            // Récupération du code traitement_perso_admin (dev mode flash : on réfléchi pas on reprend ce qui marche ailleurs) !!!
            foreach ($_REQUEST["aqelem_misc_cod"][0] as $k => $mod_perso_cod)
            {
                if ($mod_perso_cod > 0)
                {
                    // Caracs du perso concerné
                    $req  = "select perso_nom, perso_type_perso from perso where perso_cod = :perso_cod ";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array(":perso_cod" => $mod_perso_cod), $stmt);

                    if ($result = $stmt->fetch())
                    {
                        $mod_perso_nom        = $result['perso_nom'];
                        $mod_perso_type_perso = $result['perso_type_perso'];

                        if ($mod_perso_type_perso != 2 && $s_option == "monstre")
                        {
                            // Un admin monstre qui force le passage pour ajouter un perso!!!
                            $log .= "vous n'avez pas les droits suffisants pour ce perso!";
                        } else
                        {
                            // dé-titrage
                            $req  =
                                "delete from perso_titre where ptitre_perso_cod=:ptitre_perso_cod and ptitre_titre ilike :ptitre_titre ";
                            $stmt = $pdo->prepare($req);
                            $stmt =
                                $pdo->execute(array(":ptitre_perso_cod" => $mod_perso_cod, ":ptitre_titre" => $titre), $stmt);

                            if ($stmt->rowCount() > 0)
                            {
                                $log .= "Suppression du titre sur le perso : {$mod_perso_nom} ({$mod_perso_cod}) \n";
                            } else
                            {
                                $log .= "Perso {$mod_perso_nom} ({$mod_perso_cod}) : Titre non trouvé!\n";
                            }
                        }
                    } else
                    {
                        $log .= "Perso #{$mod_perso_cod} : Non trouvé!\n";
                    }
                }
            }
            writelog($log, 'perso_edit');
            echo "<div class='bordiv'><pre>$log</pre></div>";
        } else if (($_REQUEST['methode'] == "recompense") && ($_REQUEST['action'] == "objets-et-bzf"))
        {
            //echo "<pre>"; print_r($_REQUEST);echo "</pre>";
            $log = date("d/m/y - H:i") . " $perso_nom (compte $compt_cod / $compt_nom) Don de récompense en masse \n";

            // Préparation de la liste des objets pour distribution-----
            $list_obj   = array();
            $nb_obj_min = 1 * (int)$_REQUEST["don-obj-min"];
            $nb_obj_max = 1 * (int)$_REQUEST["don-obj-max"];
            foreach ($_REQUEST["aqelem_misc_cod"][1] as $k => $gobj_cod)
            {
                if ((int)$gobj_cod > 0)
                {
                    $chance = (float)$_REQUEST['aqelem_param_num_1'][1][$k];
                    if ($chance == 0) $chance = 100; else if ($chance > 100) $chance = 100;
                    $list_obj[] = array("chance" => $chance, "ordre" => 0, "gobj_cod" => $gobj_cod);
                }
            }
            $nb_obj = sizeof($list_obj);
            if ($nb_obj_max == 0) $nb_obj_max = $nb_obj;     // S'il n'y a pas pas de max, on met tous les objets

            // On distribue à tous les persos
            foreach ($_REQUEST["aqelem_misc_cod"][0] as $k => $mod_perso_cod)
            {
                if ($mod_perso_cod > 0)
                {
                    // Caracs du perso concerné
                    $req  = "select perso_nom, perso_type_perso from perso where perso_cod = :perso_cod ";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array(":perso_cod" => $mod_perso_cod), $stmt);

                    if ($result = $stmt->fetch())
                    {
                        $mod_perso_nom        = $result['perso_nom'];
                        $mod_perso_type_perso = $result['perso_type_perso'];

                        if ($mod_perso_type_perso != 2 && $s_option == "monstre")
                        {
                            // Un admin monstre qui force le passage pour ajouter un perso!!!
                            $log .= "vous n'avez pas les droits suffisants pour ce perso!";
                        } else
                        {
                            // D'abord on s'occupe des Bzf-----------------------------
                            if (1 * (int)$_REQUEST["don-bzf-max"] == 0)
                            {
                                $don_bzf = 1 * (int)$_REQUEST["don-bzf-min"];
                            } else
                            {
                                $don_bzf =
                                    random_int(1 * (int)$_REQUEST["don-bzf-min"], 1 * (int)$_REQUEST["don-bzf-max"]);
                            }

                            if ($don_bzf > 0)
                            {
                                $req  = "update perso set perso_po = perso_po + :don_bzf where perso_cod = :perso_cod ";
                                $stmt = $pdo->prepare($req);
                                $stmt =
                                    $pdo->execute(array(":don_bzf" => $don_bzf, ":perso_cod" => $mod_perso_cod), $stmt);

                                $texte_evt = "[cible] a reçu {$don_bzf} brouzoufs.";
                                insert_evt($mod_perso_cod, $texte_evt, 'O', 'N');
                                $log .= "\nDon de {$don_bzf} brouzoufs au perso : {$mod_perso_nom} ({$mod_perso_cod}) \n";
                            }

                            // Ensuite du don de PX -----------------------------
                            if (1 * (int)$_REQUEST["don-px-max"] == 0)
                            {
                                $don_px = 1 * (int)$_REQUEST["don-px-min"];
                            } else
                            {
                                $don_px =
                                    random_int(1 * (int)$_REQUEST["don-px-min"], 1 * (int)$_REQUEST["don-px-max"]);
                            }

                            if ($don_px > 0)
                            {
                                $req  = "update perso set perso_px = perso_px + :don_px where perso_cod = :perso_cod ";
                                $stmt = $pdo->prepare($req);
                                $stmt =
                                    $pdo->execute(array(":don_px" => $don_px, ":perso_cod" => $mod_perso_cod), $stmt);

                                $texte_evt = "[cible] a reçu {$don_px} PX.";
                                insert_evt($mod_perso_cod, $texte_evt, 'O', 'N');

                                $log .= "\nDon de {$don_px} PX au perso : {$mod_perso_nom} ({$mod_perso_cod}) \n";
                            }

                            // Enfin le don d'objets -----------------------------

                            if ($nb_obj > 0)
                            {
                                $log .= "\nDon d'objet(s) au perso : {$mod_perso_nom} ({$mod_perso_cod}) \n";

                                // Pour ne pas distribuer la même chose à tout le monde on ajoute un peu de random
                                for ($o = 0; $o < $nb_obj; $o++)
                                {
                                    $list_obj[$o]["ordre"] = mt_rand(1, 10000);
                                }
                                // Tri selon les critères de chances
                                array_multisort(array_column($list_obj, 'chance'), SORT_ASC, array_column($list_obj, 'ordre'), SORT_ASC, $list_obj);


                                $don_obj    = array();
                                $nb_obj_don = 0;
                                // Première passe suivant les critères de chance
                                for ($o = 0; ($o < $nb_obj && $nb_obj_don < $nb_obj_max); $o++)
                                {
                                    $objet_generique = new objet_generique();
                                    $objet_generique->charge($list_obj[$o]["gobj_cod"]);

                                    $chance = rand(1, 100);
                                    if ($chance <= $list_obj[$o]["chance"])
                                    {
                                        //-- la chance est bonne, on donne l'objet
                                        $don_obj[$nb_obj_don++] = $list_obj[$o]["gobj_cod"];
                                        $log                    .= "Tirage Réussi pour objet {$objet_generique->gobj_nom} : {$chance} sur {$list_obj[$o]["chance"]}%\n";
                                    } else
                                    {
                                        $log .= "Tirage Raté pour objet {$objet_generique->gobj_nom} : {$chance} sur {$list_obj[$o]["chance"]}%\n";
                                    }
                                }

                                // Seconde passe pour les plus malchanceux qui n'ont pas reçu le nombre d'objet minimum
                                if ($nb_obj_don < $nb_obj_min)
                                {
                                    // En commençant par les objets les plus facile
                                    for ($o = $nb_obj - 1; ($o > 0 && $nb_obj_don < $nb_obj_min); $o--)
                                    {
                                        $don_obj[$nb_obj_don++] = $list_obj[$o]["gobj_cod"];

                                        $objet_generique = new objet_generique();
                                        $objet_generique->charge($list_obj[$o]["gobj_cod"]);
                                        $log .= "Chance forcée pour objet {$objet_generique->gobj_nom} : pour respect du minimum de {$nb_obj_min} objet(s)\n";
                                    }
                                }

                                if ($nb_obj_don > 0)
                                {
                                    foreach ($don_obj as $gobj_cod)
                                    {
                                        $req  = "select cree_objet_perso_nombre(:gobj_cod,:perso_cod,1) as obj_cod ";
                                        $stmt = $pdo->prepare($req);
                                        $stmt =
                                            $pdo->execute(array(":gobj_cod" => $gobj_cod, ":perso_cod" => $mod_perso_cod), $stmt);

                                        if ($result = $stmt->fetch())
                                        {
                                            $objet = new objets();
                                            $objet->charge(1 * $result["obj_cod"]);

                                            $texte_evt =
                                                '[cible] a reçu un objet <em>(' . $objet->obj_cod . ' / ' . $objet->get_type_libelle() . ' / ' . $objet->obj_nom . ')</em>';
                                            insert_evt($mod_perso_cod, $texte_evt, 'N', 'N');

                                            $log .= "Don de l'objet #" . $objet->obj_cod . " (" . $objet->get_type_libelle() . " / " . $objet->obj_nom . ")  au perso : {$mod_perso_nom} ({$mod_perso_cod}) \n";
                                        }
                                    }
                                }
                            }
                        }
                    } else
                    {
                        $log .= "Perso #{$mod_perso_cod} : Non trouvé!\n";
                    }
                }
            }
            writelog($log, 'perso_edit');
            echo "<div class='bordiv'><pre>$log</pre></div>";
        } else if (($_REQUEST['methode'] == "recompense") && ($_REQUEST['action'] == "butin"))
        {
            //echo "<pre>"; print_r($_REQUEST);echo "</pre>";
            $log = date("d/m/y - H:i") . " $perso_nom (compte $compt_cod / $compt_nom) partage de butin \n";

            // On prépare la liste de perso à récompenser trié par ordre aléatoire
            $list_perso = array();
            foreach ($_REQUEST["aqelem_misc_cod"][0] as $k => $mod_perso_cod)
            {
                if ($mod_perso_cod > 0)
                {
                    // Caracs du perso concerné
                    $req  = "select perso_nom, perso_type_perso from perso where perso_cod = :perso_cod ";
                    $stmt = $pdo->prepare($req);
                    $stmt = $pdo->execute(array(":perso_cod" => $mod_perso_cod), $stmt);

                    if ($result = $stmt->fetch())
                    {
                        $mod_perso_nom        = $result['perso_nom'];
                        $mod_perso_type_perso = $result['perso_type_perso'];

                        if ($mod_perso_type_perso != 2 && $s_option == "monstre")
                        {
                            // Un admin monstre qui force le passage pour ajouter un perso!!!
                            $log .= "vous n'avez pas les droits suffisants pour ce perso!";
                        } else
                        {
                            // Mettre le perso dans la liste
                            $list_perso[] =
                                array("ordre" => 0, "perso_cod" => $mod_perso_cod, "perso_nom" => $mod_perso_nom);
                        }
                    }
                }
            }
            $nb_perso = sizeof($list_perso);


            // Cette fois-ci on parse les objets
            $p = $nb_perso;    // Index du perso pour la distribution
            foreach ($_REQUEST["aqelem_misc_cod"][2] as $o => $gobj_cod)
            {
                if ((int)$gobj_cod > 0)
                {
                    $nb_obj = (int)$_REQUEST['aqelem_param_num_1'][2][$o];
                    if ($nb_obj == 0) $nb_obj = 1;       // Si pas de quantité renseigné on met 1 par défaut
                    while ($nb_obj > 0)
                    {
                        $nb_obj--;     // Un objet de moins à distribuer

                        // Tri selon les critères de chances - on mélange la liste de perso à chaque fois que tout le monde a été servi
                        if ($p >= $nb_perso)
                        {
                            // Pour ne pas distribuer la même chose à tout le monde on ajoute un peu d'aléatoire
                            for ($p = 0; $p < $nb_perso; $p++)
                            {
                                $list_perso[$p]["ordre"] = mt_rand(1, 10000);
                            }

                            $p = 0;    // On distribut à partir du début de la liste
                            array_multisort(array_column($list_perso, 'ordre'), SORT_ASC, $list_perso);
                        }

                        $mod_perso_cod = $list_perso[$p]["perso_cod"];    // Choix du perso gagnant
                        $mod_perso_nom = $list_perso[$p]["perso_nom"];    // récupe de son nom
                        $p++;   // passage au perso suivant

                        $req  = "select cree_objet_perso_nombre(:gobj_cod,:perso_cod,1) as obj_cod ";
                        $stmt = $pdo->prepare($req);
                        $stmt = $pdo->execute(array(":gobj_cod" => $gobj_cod, ":perso_cod" => $mod_perso_cod), $stmt);

                        if ($result = $stmt->fetch())
                        {
                            $objet = new objets();
                            $objet->charge(1 * $result["obj_cod"]);

                            $texte_evt =
                                '[cible] a reçu une part du butin <em>(' . $objet->obj_cod . ' / ' . $objet->get_type_libelle() . ' / ' . $objet->obj_nom . ')</em>';

                            insert_evt($mod_perso_cod, $texte_evt, 'N', 'N');
                            $log .= "Partage du butin, objet #" . $objet->obj_cod . " (" . $objet->get_type_libelle() . " / " . $objet->obj_nom . ") au perso : {$mod_perso_nom} ({$mod_perso_cod}) \n";
                        }
                    }
                }
            }

            writelog($log, 'perso_edit');
            echo "<div class='bordiv'><pre>$log</pre></div>";

        }
    }
    //print_r($_REQUEST);

    echo '<div class="hr">&nbsp;&nbsp;<strong  style=\'color: #800000;\'>GESTION DE TITRE/RECOMPENSE EN MASSE</strong>&nbsp;&nbsp;</div>';

    //=======================================================================================
    // == Constantes quete_auto
    //=======================================================================================
    //$request_select_etage_ref = "SELECT null etage_cod, 'Aucune restriction' etage_libelle, null etage_numero UNION SELECT etage_cod, etage_libelle, etage_numero from etage where etage_reference = etage_numero order by etage_numero desc" ;
    $request_select_etage =
        "SELECT etage_numero, case when etage_reference <> etage_numero then ' |- ' else '' end || etage_libelle as etage_libelle from etage order by etage_reference desc, etage_numero";

    echo '<strong>Liste des persos à titrer/récompenser:</strong>&nbsp;<span id="nb-perso">(0 persos)</span><br><br>';

    echo '<form  method="post" onkeypress="return event.keyCode != 13;" onSubmit="return checkForm()"><input type="hidden" name="methode" value="recompense" />';
    echo '<input name="action" id="action" type="hidden" value="">';
    echo '<table width="95%" align="center">';

    // Pour copier le modele quete-auto (pour un dev flash, on reprend de l'existant)
    $style_tr        = "display: block;";
    $param_id        = 0;
    $row             = 0;
    $row_id          = "row-$param_id-$row-";
    $aqelem_misc_nom = "";
    echo '<tr id="' . $row_id . '" style="' . $style_tr . '">';
    echo '<td><input type="button" class="test" value="Retirer" onClick="delQueteAutoParamRow($(this).parent(\'td\').parent(\'tr\'), 1); updatePersoCount();"></td>';
    echo '<td>Perso :
                    <input data-entry="val" id="' . $row_id . 'aqelem_cod" name="aqelem_cod[' . $param_id . '][]" type="hidden" value="">
                    <input name="aqelem_type[' . $param_id . '][]" type="hidden" value="">
                    <input id="' . $row_id . 'aqelem_misc_nom_compte" type="hidden" value="">
                    <input data-entry="val" name="aqelem_misc_cod[' . $param_id . '][]" id="' . $row_id . 'aqelem_misc_cod" type="text" size="5" value="" onChange="setNomAndPosPerso(\'' . $row_id . 'aqelem_misc_nom\', $(\'#' . $row_id . 'aqelem_misc_cod\').val());">
                    &nbsp;<em><span data-entry="text" id="' . $row_id . 'aqelem_misc_nom">' . $aqelem_misc_nom . '</span></em>
                    &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("' . $row_id . 'aqelem_misc","perso","Rechercher un perso", "' . $s_option . '");\'>
                    &nbsp;<input type="button" class="" value="sa coterie" onClick=\'listCoterie($("#' . $row_id . 'aqelem_misc_cod").val());\'>
                    &nbsp;<input type="button" class="" value="sa position" onClick=\'listSurZone($("#' . $row_id . 'aqelem_misc_cod").val());\'>
                    &nbsp;<input type="button" class="" value="controleur" onClick=\'listControleur($("#' . $row_id . 'aqelem_misc_cod").val());\'>
                    </td>';
    echo '<tr id="add-' . $row_id . '" style="' . $style_tr . '"><td> <input id="add-button-perso" type="button" class="test" value="ajouter" onClick="addQueteAutoParamRow($(this).parent(\'td\').parent(\'tr\').prev(),0);"> 
                                 <input id="del-button-triplette" type="button" class="test" value="Garder 1 par triplette" onClick="reduceTripletteTitrageList();"> 
                                 </td></tr>';
    echo '</table>';

    //---- section à onglet------------------------------------------------------------
    echo '<table cellspacing="0" cellpadding="0" width="100%"><tr style="height:25px;">';

    // --------------------------------------- ONGLET DES TITRES ------------------------------------------------------------
    echo '<td id="td-onglet-titre" class="pas_onglet"><p style="text-align:center"><a onclick="select_onglet(\'titre\');" href="#">GESTION DES TITRES</a></p></td>
          <td id="td-onglet-recompense" class="onglet"><p style="text-align:center"><a onclick="select_onglet(\'recompense\');" href="#">GESTION DES RECOMPENSES</a></p></td>
          <td id="td-onglet-butin" class="pas_onglet"><p style="text-align:center"><a onclick="select_onglet(\'butin\');" href="#">GESTION DES BUTINS</a></p></td></tr>';
    echo '<tr><td colspan="3" class="reste_onglet">';

    echo '<div id="onglet-titre" style="display: none">';
    echo 'Titre à attribuer ou à supprimer :<br><br>
                                    <input name="titre" id="titre" type="text" size="80" value="">&nbsp;
                                    <!--Date : <input name="date" id="date" type="text" size="10" value="" >&nbsp<em style="font-size: x-small">Au format jj/mm/aaaa (date du jour si vide)</em>-->
                                   <br><br>';
    echo '<input class="test" type="submit" value="Attribuer le titre aux persos" onclick="$(\'#action\').val(\'add-titre\')">';
    echo '&nbsp;&nbsp;<input class="test" type="submit" value="Supprimer le titre aux persos" onclick="$(\'#action\').val(\'del-titre\')">';
    echo '<br><br>';

    // --------------------------------------- ONGLET DES RECOMPENSES ------------------------------------------------------------
    echo '</div><div id="onglet-recompense" style="display: block; margin-top: 5px;">';

    echo 'Donner des brouzoufs au  
                minimum : <input name="don-bzf-min" id="don-bz-min" type="text" size="5" value="">&nbsp;
                et au maximum : <input name="don-bzf-max" id="don-bz-max" type="text" size="5" value="">&nbsp; <br><br>';

    echo 'Donner des PX au  
                minimum : <input name="don-px-min" id="don-bz-min" type="text" size="5" value="">&nbsp;
                et au maximum : <input name="don-px-max" id="don-bz-max" type="text" size="5" value="">&nbsp; <br><br>';

    echo 'Donner un nombre d\'objets au  
                minimum : <input name="don-obj-min" id="don-obj-min" type="text" size="5" value="">&nbsp;
                et au maximum : <input name="don-obj-max" id="don-obj-max" type="text" size="5" value="">&nbsp; parmi:<br>';


    echo '<table width="95%" align="center">';
    // Pour copier le modele quete-auto (pour un dev flash, on reprend de l'existant)
    $style_tr        = "display: block;";
    $param_id        = 1;
    $row             = 0;
    $row_id          = "row-$param_id-$row-";
    $aqelem_misc_nom = "";
    echo '<tr id="' . $row_id . '" style="' . $style_tr . '">';
    echo '<td><input type="button" class="test" value="Retirer" onClick="delQueteAutoParamRow($(this).parent(\'td\').parent(\'tr\'), 1);"></td>';
    echo '<td> Chance de  <input data-entry="val" id="' . $row_id . 'aqelem_param_num_1" name="aqelem_param_num_1[' . $param_id . '][]" type="text" size="2" value="">&nbsp;%
             d\'obtenir l\'objet générique :
                    <input data-entry="val" id="' . $row_id . 'aqelem_cod" name="aqelem_cod[' . $param_id . '][]" type="hidden" value="">
                    <input name="aqelem_type[' . $param_id . '][]" type="hidden" value="">
                    <input data-entry="val" name="aqelem_misc_cod[' . $param_id . '][]" id="' . $row_id . 'aqelem_misc_cod" type="text" size="5" value="" onChange="setNomByTableCod(\'' . $row_id . 'aqelem_misc_nom\', \'objet_generique\', $(\'#' . $row_id . 'aqelem_misc_cod\').val());">
                    &nbsp;<em><span data-entry="text" id="' . $row_id . 'aqelem_misc_nom">' . $aqelem_misc_nom . '</span></em>
                    &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("' . $row_id . 'aqelem_misc","objet_generique","Rechercher un objet générique");\'> 
                    </td>';
    echo '<tr id="add-' . $row_id . '" style="' . $style_tr . '"><td> <input id="add-button-recompense" type="button" class="test" value="ajouter" onClick="addQueteAutoParamRow($(this).parent(\'td\').parent(\'tr\').prev(),0);"> </td></tr>';
    echo '</table>';


    echo '<input class="test" type="submit" value="Distribuer les récompenses" onclick="$(\'#action\').val(\'objets-et-bzf\')">';
    echo '<br><br><strong>Les récompenses seront distribuées pour chaque persos de la liste en respectant les critères de minima/maxima et % d\'obtention</strong><br>
            <em style="font-size: x-small">Nota: <br>
            * si le minimum et le maximum de bzf (PX) ne sont pas pas renseignés aucun bzf (PX) ne sera distribué.<br>
            * si le maximum de bzf (PX) n\'est pas renseigné, le minimum sera distribué.<br>
            * si le minimum et le maximum d\'objet ne sont pas pas renseignés, la distribution suivra uniquement les critères de %.<br>
            * si le % de chance n\'est pas défini pour un objet, sont taux sera considéré à 100%.<br>
            * lors de la distribution suivant les %, si le minimum d\'objet n\'est pas atteint les objets avec le % le plus élévés seront donnés.<br>
            * lors de la distribution suivant les %, si le maximum d\'objet est dépassé les objets avec le % le plus élévé % seront retirés.<br>
            * Pour respecter la distribution min et max d\'objet, un tirage aléatoire départagera les % identiques.<br>
            </em>';
    echo '<br>';

    // --------------------------------------- ONGLET DES RECOMPENSES ------------------------------------------------------------
    echo '</div><div id="onglet-butin" style="display: none; margin-top: 5px;">';

    echo 'Liste des objets dans le butin :<br>';

    echo '<table width="95%" align="center">';
    // Pour copier le modele quete-auto (pour un dev flash, on reprend de l'existant)
    $style_tr        = "display: block;";
    $param_id        = 2;
    $row             = 0;
    $row_id          = "row-$param_id-$row-";
    $aqelem_misc_nom = "";
    echo '<tr id="' . $row_id . '" style="' . $style_tr . '">';
    echo '<td><input type="button" class="test" value="Retirer" onClick="delQueteAutoParamRow($(this).parent(\'td\').parent(\'tr\'), 1);"></td>';
    echo '<td> <input data-entry="val"  id="' . $row_id . 'aqelem_param_num_1" name="aqelem_param_num_1[' . $param_id . '][]" type="text" size="2" value=""> exemplaire(s)
             de l\'objet générique :
                    <input data-entry="val" id="' . $row_id . 'aqelem_cod" name="aqelem_cod[' . $param_id . '][]" type="hidden" value="">
                    <input name="aqelem_type[' . $param_id . '][]" type="hidden" value="">
                    <input data-entry="val" name="aqelem_misc_cod[' . $param_id . '][]" id="' . $row_id . 'aqelem_misc_cod" type="text" size="5" value="" onChange="setNomByTableCod(\'' . $row_id . 'aqelem_misc_nom\', \'objet_generique\', $(\'#' . $row_id . 'aqelem_misc_cod\').val());">
                    &nbsp;<em><span data-entry="text" id="' . $row_id . 'aqelem_misc_nom">' . $aqelem_misc_nom . '</span></em>
                    &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("' . $row_id . 'aqelem_misc","objet_generique","Rechercher un objet générique");\'> 
                    </td>';
    echo '<tr id="add-' . $row_id . '" style="' . $style_tr . '"><td> <input id="add-button-butin" type="button" class="test" value="ajouter" onClick="addQueteAutoParamRow($(this).parent(\'td\').parent(\'tr\').prev(),0);"> </td></tr>';
    echo '</table>';


    echo '<input class="test" type="submit" value="Partager le butin" onclick="$(\'#action\').val(\'butin\')">';
    echo '<br><br><strong>Partager le butin entre tous les persos</strong> (tous les objets seront ditribués aléatoirement)<br>
            <em style="font-size: x-small">Nota: <br>
            * S\'il y a moins d\'objets que de perso, certains n\'auront rien.<br>
            * S\'il y a plus d\'objets que de perso, chaque perso aura au moins une part du butin.<br>
            </em>';
    echo '<br>';


    echo '</div>';
    echo '</td></tr></table>';     // Fin des onglets!!
    //---- fin de section à onglet------------------------------------------------------------

    //=============================== SECTION RECHERCHE DE PERSO ===================================
    echo "</form><br><br><div class=\"titre\" style=\"padding:5px;\"><center><strong>Section de recherche de persos</strong></center></div><br><br>";
    //echo '<hr><strong>Section de recherche de persos</strong>:';
    echo '&nbsp;&nbsp;<input type="button" class="" value="chercher mes persos" onClick="listControleur(' . $perso_cod . ')"><br>';
    echo 'N° de Monstre <input id="perso_px" style="margin-top: 5px;" type=""text" size="5">&nbsp;:&nbsp;chercher les persos qui ont participé à sa mort&nbsp;<input type="button" class="" value="chercher par gain de px" onClick="listPersoPX($(\'#perso_px\').val());"><br>';
    echo 'Titre <input id="perso_titre" style="margin-top: 5px;" type=""text" size="50">&nbsp;:&nbsp;<input type="button" class="" value="chercher par titre" onClick="listPersoTitre($(\'#perso_titre\').val());"><br>';
    echo 'Liste <input id="perso_liste" style="margin-top: 5px;" type=""text" size="50">&nbsp;:&nbsp;<input type="button" class="" value="chercher par liste de noms" onClick="listPersoListe($(\'#perso_liste\').val());"> <em style="font-size: x-small">(liste de nom séparés par des ; comme pour la messagerie)</em><br>';
    echo create_selectbox_from_req("perso_etage", $request_select_etage, 0, array('id' => "perso_etage", 'style' => 'style="margin:5px; width: 350px;'));
    echo '&nbsp;&nbsp;<input type="button" class="" value="tous les persos de l\'étage" onClick="listPersoEtage(1, $(\'#perso_etage\').val());">&nbsp;&nbsp;<input type="button" class="" value="tous les familiers de l\'étage" onClick="listPersoEtage(3, $(\'#perso_etage\').val());"><br>';
    echo '<div id="liste-ajout-rapide"></div>';
    echo '<hr>';

    echo "<script> listControleur({$perso_cod}) </script>"; // Injection javascript => perso par défaut.


    //=============================== SECTION RECHERCHE D'OBJET ===================================
    // Pas de temps e faire de la cosmétique, on reprend tel quel la page de gestion de l'inventaire perso
    echo '<div id="section-recherche-objet">';
    echo "<br><br><div class=\"titre\" style=\"padding:5px;\"><center><strong>Section de recherche d'objet</strong></center></div><br><br>";
    echo '<SCRIPT language="javascript">
            var listeBase = [];';

    require "blocks/_admin_perso_et_titre.php";

    echo '>Tous; types; d;’objets</option>';


    $req_tobj = "select distinct tobj_libelle from type_objet order by tobj_libelle";
    $stmt     = $pdo->query($req_tobj);
    while ($result = $stmt->fetch())
    {
        $tobj_libelle = str_replace("\"", "", $result['tobj_libelle']);
        echo "<option value='$tobj_libelle'>$tobj_libelle</option>";
    }
    echo '
                        </select><br/>
                        <select style="width: 280px;" name="selectvaleur"
                                onchange=\'cleanOption(document.formInventaire.select2); addOptionArray(document.formInventaire.select2, listeBase, document.formInventaire.selecttype.value, this.value);\'>
                            <option value=\'\'>Valeur indéfinie</option>
                            <option value=\'0;1000\'>Moins de 1 000 brouzoufs</option>
                            <option value=\'1000;5000\'>Entre 1 000 et 5 000 brouzoufs</option>
                            <option value=\'5000;10000\'>Entre 5 000 et 10 000 brouzoufs</option>
                            <option value=\'10000;20000\'>Entre 10 000 et 20 000 brouzoufs</option>
                            <option value=\'20000;50000\'>Entre 20 000 et 50 000 brouzoufs</option>
                            <option value=\'50000;100000\'>Entre 50 000 et 100 000 brouzoufs</option>
                            <option value=\'100000;100000000\'>Plus de 100 000 brouzoufs</option>
                        </select><br/>
                        <select multiple name="select2" size="10" style="width:280px;">
                        </select>
                    </TD>
                </TR>
            </TABLE>

            <input type="hidden" name="compiledInv" value="">
            <SCRIPT>
                addOptionArray(document.formInventaire.select2, listeBase, \'\', \'\');
                fillOptions(document.formInventaire.select2, document.formInventaire.select1, listeCurrent);
                compileAccumulatorCounter(document.formInventaire.select1, document.formInventaire.compiledInv);
            </SCRIPT>
            <div id="bouton-objet-butin" style="display: none"><input type="button" class="test" value="Ajouter ces objets au butin" onClick="addButinFromSearchList(\'select1\');"></div>
            <div id="bouton-objet-recompense">Chance d\'obtenir les objets :&nbsp;<input id="chance-objet-recompense" type="text" size="2" value="">&nbsp;&nbsp;<input type="button" class="test" value="Ajouter ses objets comme récompense" onClick="addRecompenseFromSearchList(\'select1\');"></div>
            <br>
        </form>';
    echo '<hr>';
    echo '</div>';

}
?>
    <p style="text-align:center;"><a href="<?php echo $_SERVER['PHP_SELF'] ?>">Retour au début</a>
<?php $contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";