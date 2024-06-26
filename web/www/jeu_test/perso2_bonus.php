<?php
$perso = new perso;
if (!$perso->charge($perso_cod))
{
    die('Erreur sur chargement perso');
}
if (isset($_REQUEST['ch_util']))
{
    $perso->perso_utl_pa_rest = $_REQUEST['ch_util'];
    $perso->stocke();
}

//
/* Concentration */
//
$concentration     = new concentrations();
$has_concentration = false;
if ($concentration->getByPerso($perso->perso_cod))
{
    $has_concentration = true;
}
//
/* BONUS PERMANENTS */
//
// Loop=0 pour bonus equipement  Loop=1 bonus temporaire
for ($loop = 0; $loop < 2; $loop++)
{
    $equipement = false;
    if ($loop == 0)
    {
        $equipement = true;
    }


    $carac_orig            = new carac_orig;
    $tab_carac_orig[$loop] = $carac_orig->getByPersoEquipement($perso->perso_cod, $equipement);

    $bonus_carac[$loop] = array();
    $malus_carac[$loop] = array();
    $record             = 0;
    foreach ($tab_carac_orig[$loop] as $detail_carac_orig)
    {
        $carac                  = $detail_carac_orig['corig_tbonus_libc'];
        $lib_carac              = $detail_carac_orig['tonbus_libelle'];
        $bm                     = $detail_carac_orig['bonus_carac'];
        $corig_mode             = $detail_carac_orig['corig_mode'];
        $tbonus_description     = $detail_carac_orig['tbonus_description'];
        $corig_obj_cod          = $detail_carac_orig['corig_obj_cod'];
        $obj_nom                = $detail_carac_orig['obj_nom'];
        if ($detail_carac_orig['corig_mode'] == 'E')
        {
            $duree = "Equipement";
        } else
        {
            $duree = ($detail_carac_orig['corig_nb_tours'] == 0) ? $detail_carac_orig['corig_dfin'] :
                $detail_carac_orig['corig_nb_tours'] . ' tour(s)';
        }

        // dans carac_orig une valaue >0 est toujours benefique et <0 malefique, on gère le signe du bonus/malus à l'affichage!
        if ($detail_carac_orig['tbonus_gentil_positif'] == 'false')  $bm = -$bm ;
        if ( $detail_carac_orig['bonus_carac'] > 0 )
        {
            $bonus_carac[$loop][$record]    = array();
            $bonus_carac[$loop][$record][0] = $bm;
            $bonus_carac[$loop][$record][1] = $duree;
            $bonus_carac[$loop][$record][2] = $carac;
            $bonus_carac[$loop][$record][3] = $corig_mode;
            $bonus_carac[$loop][$record][4] = $tbonus_description;
            $bonus_carac[$loop][$record][5] = $lib_carac;
            $bonus_carac[$loop][$record][6] = $corig_obj_cod;
            $bonus_carac[$loop][$record][7] = $obj_nom;
        } else
        {
            $malus_carac[$loop][$record]    = array();
            $malus_carac[$loop][$record][0] = $bm;
            $malus_carac[$loop][$record][1] = $duree;
            $malus_carac[$loop][$record][2] = $carac;
            $malus_carac[$loop][$record][3] = $corig_mode;
            $malus_carac[$loop][$record][4] = $tbonus_description;
            $malus_carac[$loop][$record][5] = $lib_carac;
            $malus_carac[$loop][$record][6] = $corig_obj_cod;
            $malus_carac[$loop][$record][7] = $obj_nom;
        }
        $record++;
    }

    $tab_bonus[$loop] = $perso->perso_bonus_equipement($equipement);

    if (count($tab_bonus[$loop]) + count($bonus_carac[$loop]) != 0)
    {
        foreach ($tab_bonus[$loop] as $key => $detail_bonus)
        {
            if (is_file(__DIR__ . "/../images/interface/bonus/" . $detail_bonus['tbonus_libc'] . ".png"))
            {
                $img = '/../images/interface/bonus/' . $detail_bonus['tbonus_libc'] . '.png';
            } else
            {
                $img = '/../images/interface/bonus/BONUS.png';
            }
            $tab_bonus[$loop][$key]['img'] = $img;
        }
        foreach ($bonus_carac[$loop] as $key => $bonus)
        {
            $valeur             = $bonus[0];
            $duree              = $bonus[1];
            $carac              = $bonus[2];
            $corig_mode         = $bonus[3];
            $tbonus_description = $bonus[4];
            $signe              = ($valeur >= 0) ? '+' : '';
            $lib_carac          = $bonus[5];

            $bonus_carac[$loop][$key]['lib_carac'] = $lib_carac;
            $bonus_carac[$loop][$key]['tbonus_description'] = $tbonus_description;
            if (is_file(__DIR__ . "/../images/interface/bonus/" . $carac . ".png"))
            {
                $img = '/../images/interface/bonus/' . $carac . '.png';
            } else
            {
                $img = "/../images/interface/bonus/BONUS.png";
            }
            $bonus_carac[$loop][$key]['img'] = $img;
        }
    }
    $tab_malus[$loop] = $perso->perso_malus_equipement($equipement);
    if (count($tab_malus[$loop]) + count($malus_carac[$loop]) != 0)
    {
        foreach ($tab_malus[$loop] as $key => $detail_malus)
        {
            if (is_file(__DIR__ . "/../images/interface/bonus/" . $detail_malus['tbonus_libc'] . ".png"))
            {
                $img = '/../images/interface/bonus/' . $detail_malus['tbonus_libc'] . '.png';
            } else
            {
                $img = '/../images/interface/bonus/MALUS.png';
            }
            $tab_malus[$loop][$key]['img'] = $img;
        }
        foreach ($malus_carac[$loop] as $key => $malus)
        {
            $valeur             = $malus[0];
            $duree              = $malus[1];
            $carac              = $malus[2];
            $corig_mode         = $malus[3];
            $tbonus_description = $malus[4];
            $signe     = ($valeur >= 0) ? '+' : '';
            $lib_carac          = $malus[5];

            $malus_carac[$loop][$key]['lib_carac'] = $lib_carac;
            $malus_carac[$loop][$key]['tbonus_description'] = $tbonus_description;
            if (is_file(__DIR__ . "/../images/interface/bonus/" . $carac . ".png"))
            {
                $img = '/../images/interface/bonus/' . $carac . '.png';
            } else
            {
                $img = '/../images/interface/bonus/MALUS.png';
            }
            $malus_carac[$loop][$key]['img'] = $img;
        }
    }
}

$template     = $twig->load('_perso2_bonus.twig');
$options_twig = array(

    'PERSO'             => $perso,
    'PHP_SELF'          => $_SERVER['PHP_SELF'],
    'CONCENTRATION'     => $concentration,
    'HAS_CONCENTRATION' => $has_concentration,
    'TAB_CARAC_ORIG'    => $tab_carac_orig,
    'TAB_BONUS'         => $tab_bonus,
    'TAB_MALUS'         => $tab_malus,
    'BONUS_CARAC'       => $bonus_carac,
    'MALUS_CARAC'       => $malus_carac
);
$contenu_page .= $template->render(array_merge($options_twig_defaut, $options_twig));
