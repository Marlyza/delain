<?php
include "blocks/_header_page_jeu.php";
ob_start();

$type_lieu = 1;
$nom_lieu = 'une banque';
define('APPEL', 1);
include "blocks/_test_lieu.php";


if ($quantite <= 0) {
    $erreur = 1;
    echo("<p>Vous ne pouvez pas déposer une somme inférieure ou égale à 0 !");
}

if ($erreur == 0) {

    echo("<img src=\"../images/banque3.png\"><br />");
    $req_depot = "select depot_banque($perso_cod,$quantite) as depot";
    $stmt = $pdo->query($req_depot);
    $result = $stmt->fetch();
    $tab_depot = pg_fetch_array($res_depot, 0);
    if ($result['depot'] == 0) {
        echo("<p>Vous venez de déposer <strong>$quantite</strong> brouzoufs sur votre compte en banque.");
    } else {
        printf("<p>Une anomalie est survenue : <strong>%s</strong>", $result['depot']);
    }
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";