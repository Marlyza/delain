<?php
define('APPEL', 1);
include "blocks/_header_page_jeu.php";
$perso = $verif_connexion->perso;
ob_start();
include G_CHE . "/includes/constantes.php";

$erreur     = 0;
$type_lance = get_request_var('type_lance');
if (!isset($type_lance))
{
    echo "<p>Erreur technique lors de l’appel du sort. Veuillez recommencer.</p>";
    $erreur     = 1;
    $type_lance = -1;
}

$perso = new perso;
$perso = $verif_connexion->perso;

$sort = new sorts();

if ($type_lance == 0) // runes
{
    require "blocks/_get_rune_combi.php";

    if (!$sort->getByCombinaison($resultat))
    {
        echo "debug " . $resultat;
        echo "<p>Vous ne vous sentez pas capable d'associer ces runes pour lancer un sort.";
        $erreur = 1;
    } else
    {
        $sort_cod = $sort->sort_cod;
        $pcomp    = new perso_competences();
        if (!$pcomp->getByPersoComp($perso_cod, $sort->sort_comp_cod))
        {
            echo "<p>Vous ne vous sentez pas capable d'associer ces runes pour lancer un sort.";
            $erreur = 1;
        }
    }
} else if ($type_lance == 5)
{
    $sort_cod = $_REQUEST['sort'];
    if ($sort_cod == '' || !isset($sort_cod))
    {
        echo "<p>Erreur sur le sort lancé !";
        $erreur = 1;
    }
    $sort->charge($sort_cod);

    // pour les objet magique on verifie que le perso le possede bien et qu'il est équipé si besoin
    $pdo         = new bddpdo;
    $objsort_cod = $_REQUEST['objsort_cod'];

    $found   = false;
    $objsort = new objets_sorts();
    // On prend la lsite des sorts d'ojet (appel de la fonction pour mise à jour si le joueur utilise des raccroucis)
    if ($liste_sorts = $objsort->get_perso_objets_sorts($perso_cod))
    {
        foreach ($liste_sorts as $sorts_attaches)
        {
            if ($sorts_attaches->objsort_cod == $objsort_cod)
            {
                $found = true;
                break;
            }
        }
    }

    if (!$found)
    {
        echo "<p>Vous ne possédez plus l'objet vous permettant de faire ce sort (ou il n'est plus équipé ou encore il n'a plus de charge).";
        // au passage on supprime son raccourci!
        $req  = "DELETE from perso_favoris WHERE pfav_perso_cod=:pfav_perso_cod AND pfav_type=:pfav_type AND pfav_misc_cod=:pfav_misc_cod";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":pfav_perso_cod" => $perso_cod, ":pfav_type" => "sort5", "pfav_misc_cod" =>$objsort_cod), $stmt);
        $erreur = 1;
    }


} else if ($type_lance == 6)
{
    $sort_cod = $_REQUEST['sort'];
    if ($sort_cod == '' || !isset($sort_cod))
    {
        echo "<p>Erreur sur le sort lancé !";
        $erreur = 1;
    }

    // pour les objet magique on verifie que le perso le possede bien et qu'il est équipé si besoin
    $pdo         = new bddpdo;
    $objsort_cod = $_REQUEST['objsort_cod'];

    $found   = false;
    $objsortbm = new objets_sorts_bm();
    // On prend la lsite des sorts d'ojet (appel de la fonction pour mise à jour si le joueur utilise des raccroucis)
    if ($liste_sorts = $objsortbm->get_perso_objets_sorts_bm($perso_cod))
    {
        foreach ($liste_sorts as $sorts_attaches)
        {
            if ($sorts_attaches->objsortbm_cod == $objsort_cod)
            {
                $found = true;
                break;
            }
        }
    }

    if (!$found)
    {
        echo "<p>Vous ne possédez plus l'objet vous permettant de faire ce sort (ou il n'est plus équipé ou encore il n'a plus de charge).";
        // au passage on supprime son raccourci!
        $req  = "DELETE from perso_favoris WHERE pfav_perso_cod=:pfav_perso_cod AND pfav_type=:pfav_type AND pfav_misc_cod=:pfav_misc_cod";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(":pfav_perso_cod" => $perso_cod, ":pfav_type" => "sort6", "pfav_misc_cod" =>$objsort_cod), $stmt);
        $erreur = 1;
    }

    $objsortbm->charge($objsort_cod);

} else
{
    //($type_lance == 1) // mémorisé
    //($type_lance == 2) // receptacle
    //($type_lance == 3) // divin
    //($type_lance == 4) // parchemin
    //($type_lance == 5) // objets magiques => lancement de sort attaché
    //($type_lance == 6) // objets magiques => lancement de bonus attaché (comme des sorts)
    //($type_lance == 7) // combo de sort (plusieur sort lancé successivement)

    $sort_cod = $_REQUEST['sort'];
    if ($sort_cod == '')
    {
        echo "<p>Erreur sur le sort lancé !";
        $erreur = 1;
    }
    if (!isset($sort_cod))
    {
        echo "<p>Erreur sur le sort lancé !";
        $erreur = 1;
    }
    $sort->charge($sort_cod);
}

if (($erreur == 0) && ($type_lance == 7))
{

    if ($_REQUEST["sort"] == 1)
    {
        // Cas particulier, on ne lance pas  rellement un sort, mais un bonus comme si c'était un sort.
        $nom_sort    = "Combo de Combat basique (BS/MTS/ATT)"   ;
        echo "<br /><p> Vous vous apprêtez à lancer le sort <strong>" . $nom_sort . "</strong>.<br></p>";

        $sort->charge(2);   // la combo à les mêmes caracs qu'une BS
        $aggressif   = $sort->sort_aggressif;
        $soi_meme    = $sort->sort_soi_meme;
        $sort_joueur = $sort->sort_joueur;
        $sort_dieu   = substr($sort->sort_fonction, 0, 2);
        $dist_sort = $sort->sort_distance;
        $type_cible  = "0";
        if ($sort->sort_monstre == 'O')
        {
            $type_cible = $type_cible . ",2,3";
        }
        if ($sort->sort_joueur == 'O')
        {
            $type_cible = $type_cible . ",1";
        }
        include "include_magie.php";
    }
    else
    {
        echo "<p>Erreur combo inconnue!";
        $erreur = 1;
    }


} else if (($erreur == 0) && ($type_lance == 6))
{
    // Cas particulier, on ne lance pas  rellement un sort, mais un bonus comme si c'était un sort.
    $nom_sort    = isset( $_REQUEST["objsort_name"] ) ? $_REQUEST["objsort_name"] : $objsortbm->getNom()   ;
    echo "<br /><p> Vous vous apprêtez à lancer le sort <strong>" . $nom_sort . "</strong>.<br></p>";

    $aggressif   = $objsortbm->objsortbm_bonus_aggressif;
    $soi_meme    = $objsortbm->objsortbm_bonus_soi_meme;
    $sort_joueur = $objsortbm->objsortbm_bonus_joueur;
    $sort_familier = $objsortbm->objsortbm_bonus_familier;
    $dist_sort   = $objsortbm->objsortbm_bonus_distance ;
    $type_cible  = "0";
    if ($objsortbm->objsortbm_bonus_monstre == 'O')
    {
        $type_cible = $type_cible . ",2";
    }
    if ($objsortbm->objsortbm_bonus_familier == 'O')
    {
        $type_cible = $type_cible . ",3";
    }
    if ($objsortbm->objsortbm_bonus_joueur == 'O')
    {
        $type_cible = $type_cible . ",1";
    }
    include "include_magie.php";

} else if ($erreur == 0)
{
    /*************************/
    /* R E C E P T A C L E S */
    /*************************/
    $nb_rec = $perso->perso_nb_receptacle;
    $nb_pa  = $perso->perso_pa;

    $req    = "select count(recsort_cod) as nombre from recsort where recsort_perso_cod = :perso_cod ";
    $stmt   = $pdo->prepare($req);
    $stmt   = $pdo->execute(array(":perso_cod" => $perso->perso_cod), $stmt);
    $result = $stmt->fetch();

    $nb_rec_utl = $result['nombre'];

    if ($nb_rec_utl < $nb_rec)
    {
        if (($type_lance != 3) && ($type_lance != 4) && ($type_lance != 5))
        {
            echo "<p><a href=\"action.php?methode=receptacle&sort=", $sort_cod, "&type_lance=", $type_lance, "\">Mettre ce sort dans un réceptacle ?</a>";
        }
    }

    $portee      = $sort->sort_distance;
    $nom_sort    = isset( $_REQUEST["objsort_name"] ) ? $_REQUEST["objsort_name"] : $sort->sort_nom   ;
    $sort_niveau = $sort->sort_niveau;
    /***********************/
    /* E N L U M I N U R E */
    /***********************/
    if (($type_lance != 3) && ($type_lance != 4) && ($type_lance != 5))
    {
        $is_enlumineur_niv3 = $perso->existe_competence(93);
        $is_enlumineur_niv2 = $is_enlumineur_niv3 || $perso->existe_competence(92);
        $is_enlumineur_niv1 = $is_enlumineur_niv2 || $perso->existe_competence(91);
        $has_peau_niv1      = $perso->compte_objet(481) >= 1;
        $has_peau_niv2      = $perso->compte_objet(482) >= 1;
        $has_peau_niv3      = $perso->compte_objet(483) >= 1;
        $has_peau_niv4      = $perso->compte_objet(729) >= 1;

        if (($is_enlumineur_niv1 && $has_peau_niv1 && $sort_niveau <= 2)
            || ($is_enlumineur_niv1 && $has_peau_niv2 && $sort_niveau <= 3)
            || ($is_enlumineur_niv2 && $has_peau_niv3 && $sort_niveau <= 4)
            || ($is_enlumineur_niv3 && $has_peau_niv4 && $sort_niveau <= 5))
        {
            echo "<p><a href='action.php?methode=enluminure&sort=$sort_cod&type_lance=$type_lance'>Enluminer ce sort ?</a></p><br />";
        }
    }
    $pnb = new perso_nb_sorts();
    if ($pnb->getByPersoSort($perso_cod, $sort_cod) || isset( $_REQUEST["objsort_name"] ))
    {
        echo "<br /><p> Vous vous apprêtez à lancer le sort <strong>" . $nom_sort . "</strong>.<br></p>";
    }

    if ($sort->sort_case == 'N')
    {
        $aggressif   = $sort->sort_aggressif;
        $soi_meme    = $sort->sort_soi_meme;
        $sort_joueur = $sort->sort_joueur;
        $sort_dieu   = substr($sort->sort_fonction, 0, 2);
        $dist_sort = $sort->sort_distance;
        $type_cible  = "0";
        if ($sort->sort_monstre == 'O')
        {
            $type_cible = $type_cible . ",2,3";
        }
        if ($sort->sort_joueur == 'O')
        {
            $type_cible = $type_cible . ",1";
        }
        include "include_magie.php";
    } else
    {
        include "incl_magie_case.php";
    }
}


$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";