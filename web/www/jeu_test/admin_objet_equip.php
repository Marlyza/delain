<?php

include "blocks/_header_page_jeu.php";
include_once '../includes/tools.php';


//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
?>
    <title>CONDITION D'UTILISATION DES OBJETS / OBJETS GENERIQUES</title>
<?php

$droit_modif = 'dcompt_objet';
define('APPEL', 1);
include "blocks/_test_droit_modif_generique.php";

// TYPE DE ONDTION: 1 => Condition pour équiper 2 => Condition pour ramasser
$type_condition = isset( $_REQUEST['type_condition'] ) ?  $_REQUEST['type_condition'] : 1 ;
$text_type = $type_condition == 1 ? "d'équipement" : "de ramassage" ;
$verbe_type = $type_condition == 1 ? "équiper" : "ramasser" ;



if ($erreur == 0) {

    //=======================================================================================
    // == Main
    //=======================================================================================
    //-- traitement des actions
    if (isset($_REQUEST['methode']))
    {
        //echo "<pre>";  print_r($_REQUEST); echo "</pre>";

        $log = date("d/m/y - H:i") . " $perso_nom (compte $compt_cod / $compt_nom) ajout/modification de conditions $text_type sur objets / objets génériques:\n";

        // Sauvegarde des elements créés pour l'étape
        $log_elements = ""; // pour loger la différence sur les éléments
        $element_list = array();        // Liste des élement de l'etape, pour supprimer ceux qui ne son tplus utilisés
        // Boucle sur les elements de l'etape à sauvegarder
        if (is_array($_REQUEST['objelem_type']))
        {
            foreach ($_REQUEST['objelem_type'] as $param_id => $types)
            {
                // chaque paramètres définir plusieurs élements
                foreach ($types as $e => $type)
                {
                    // Il y a certain element qui sont définit 2x en fonction du type, on ne garde qu'un seul type
                    if ((!isset($_REQUEST["element_type"][$param_id])) || ($_REQUEST["element_type"][$param_id]==$type))
                    {
                        $element = new objet_element();
                        $new = true ;
                        $objelem_cod = isset($_REQUEST["objelem_cod"][$param_id][$e]) && $_REQUEST["objelem_cod"][$param_id][$e]!="" ?  1*( $_REQUEST["objelem_cod"][$param_id][$e] )  : 0 ;
                        if ( $objelem_cod != 0 ) {
                            $new = false ;
                            $element->charge($objelem_cod);
                        }
                        $clone_elem = clone $element ;

                        $element->objelem_gobj_cod = $_REQUEST["objelem_gobj_cod"]=="" ? NULL : $_REQUEST["objelem_gobj_cod"] ;
                        $element->objelem_obj_cod = $_REQUEST["objelem_obj_cod"]=="" ? NULL : $_REQUEST["objelem_obj_cod"]  ;
                        $element->objelem_param_id = $param_id  ;
                        $element->objelem_param_ordre = $e  ;
                        $element->objelem_type = $type ;
                        $element->objelem_misc_cod = 1*$_REQUEST["objelem_misc_cod"][$param_id][$e];
                        $element->objelem_param_num_1 = isset($_REQUEST["objelem_param_num_1"][$param_id][$e]) ? 1*$_REQUEST["objelem_param_num_1"][$param_id][$e] : NULL ;
                        $element->objelem_param_num_2 = isset($_REQUEST["objelem_param_num_2"][$param_id][$e]) ? 1*$_REQUEST['objelem_param_num_2'][$param_id][$e] : NULL ;
                        $element->objelem_param_num_3 = isset($_REQUEST["objelem_param_num_3"][$param_id][$e]) ? 1*$_REQUEST['objelem_param_num_3'][$param_id][$e] : NULL ;
                        $element->objelem_param_txt_1 = $_REQUEST["objelem_param_txt_1"][$param_id][$e];
                        $element->objelem_param_txt_2 = $_REQUEST['objelem_param_txt_2'][$param_id][$e];
                        $element->objelem_param_txt_3 = $_REQUEST['objelem_param_txt_3'][$param_id][$e];

                        //echo "<pre>"; print_r($element);echo "</pre>";
                        if ($element->objelem_misc_cod!=0)
                        {
                            $element->stocke($new);
                            $element_list[] = $element->objelem_cod ;
                            $log_elements .= obj_diff($clone_elem,$element, "Ajout/Modification element #".$element->objelem_cod."\n");
                        }
                    }
                }
            }
        }

        if ($_REQUEST["objelem_obj_cod"]=="") {
            if ($result = $element->clean_generique( $_REQUEST["objelem_gobj_cod"], $type_condition, $element_list))        // supprimer tous les elements du generique qui ne sont pas dans la liste.
            {
                // Logguer les supressions
                foreach ($result as $k => $e)
                {
                    $log_elements.="Suppression element #".$e->objelem_cod."\n".obj_diff($element, $e);
                }
            }
            // Logger les infos pour suivi admin
            $log .= "ajoute/modifie des conditions $text_type objet générique #" . $_REQUEST["objelem_gobj_cod"] . "\n" . $log_elements;
        } else {
            if ($result = $element->clean_specifique( $_REQUEST["objelem_obj_cod"], $type_condition, $element_list))        // supprimer tous les elements du specifique qui ne sont pas dans la liste.
            {
                // Logguer les supressions
                foreach ($result as $k => $e)
                {
                    $log_elements.="Suppression element #".$e->objelem_cod."\n".obj_diff($element, $e);
                }
            }
            // Logger les infos pour suivi admin
            $log .= "ajoute/modifie des conditions $text_type objet  #" . $_REQUEST["objelem_obj_cod"] . "\n" . $log_elements;
        }


        writelog($log,'objet_edit');
        echo "<div class='bordiv'><pre>$log</pre></div>";
    }


    echo '<div class="hr">&nbsp;&nbsp;<strong  style=\'color: #800000;\'>SELECTION D\'OBJET</strong>&nbsp;&nbsp;</div>';

    // Pour copier le modele quete-auto (pour un dev flash, on reprend de l'existant)
    $row_id = "obj-generique-";
    echo '<form name="selection-objet" action="' . $_SERVER['PHP_SELF'] . '" method="post"><input type="hidden" name="type_condition" value="'.$type_condition.'"';
    echo '<br><strong>Sélection d’un objet générique</strong><br>Code de l\'objet générique :
                    <input data-entry="val" name="objelem_gobj_cod" id="' . $row_id . 'misc_cod" type="text" size="5" value="" onChange="setNomByTableCod(\'' . $row_id . 'misc_nom\', \'objet_generique\', $(\'#' . $row_id . 'misc_cod\').val());">
                    &nbsp;<em><span data-entry="text" id="' . $row_id . 'misc_nom"></span></em>
                    &nbsp;<input type="button" class="test" value="rechercher" onClick=\'getTableCod("' . $row_id . 'misc","objet_generique","Rechercher un objet générique");\'>
                    &nbsp;<input type="submit" value="Voir/Modifier les conditions '.$text_type.' de cet objet" class="test"></form>';


    echo "<hr>";

    $objelem_gobj_cod = 1*(int)$_REQUEST["objelem_gobj_cod"] ;      // Le generique
    $objelem_obj_cod = 1*(int)$_REQUEST["objelem_obj_cod"] ;        // Le specifique
    if ($objelem_gobj_cod>0 || $objelem_obj_cod>0)
    {

        if ($objelem_gobj_cod>0)
        {
            $gobj = new objet_generique();
            $gobj->charge($objelem_gobj_cod);
            echo "Détail des <span style=\"color: white; font-size: 14px; background-color: #800000; font-weight:bold\">&nbsp;conditions $text_type&nbsp;</span> sur l'<u>OBJET GENERIQUE</u>: <strong>#{$gobj->gobj_cod} - {$gobj->gobj_nom}</strong><br>";
            $exemplaires = $gobj->getNombreExemplaires();
            echo "<br>Nombre d'exemplaire basé sur cet objet générique:<br>";
            echo "&nbsp;&nbsp;&nbsp;Total&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <strong>".$exemplaires->total."</strong><br>";
            echo "&nbsp;&nbsp;&nbsp;Inventaire : <strong>".$exemplaires->inventaire."</strong> <em style='font-size: x-small'>(possédés par les joueurs, monstres ou PNJ)</em><br>";
            echo "<br>";
        }
        else
        {
            $obj = new objets();
            $obj->charge($objelem_obj_cod);
            echo "Détail des <span style=\"color: white; font-size: 14px; background-color: #800000; font-weight:bold\">&nbsp;conditions $text_type&nbsp;</span>  sur l'<u>OBJET SPECIFIQUE</u>: <strong>#{$obj->obj_cod} - {$obj->obj_nom}</strong><br>";
            echo "L'objet:<br>";
            echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>".$obj->trouve_objet()."</strong><br>";
            echo "<br>";
        }


        // Mise en forme de l'étape pour la saisie des infos.
        echo '  <br>
                    <form  method="post"><input type="hidden" name="methode" value="sauve" />
                    <input type="hidden" name="type_condition" value="' . $type_condition  . '" />
                    <input type="hidden" name="objelem_gobj_cod" value="' . ($objelem_gobj_cod>0 ?  $objelem_gobj_cod : "")  . '" />
                    <input type="hidden" name="objelem_obj_cod" value="' . ($objelem_obj_cod>0 ?  $objelem_obj_cod : "") . '" />
                    <table width="80%" align="center">';
        echo '<table width="80%" align="center">';

        // En cas de type alternatif, il y a un ligne de saisie supplementaire
        $style_tr = "display: block;" ;
        $param_id = $type_condition ;         // ATTENTION: param_id = 1 => Condition d'équipement, param_id = 2 => Condition de ramassage
        $param = array();
        $param['type'] = "perso_condition" ;
        $param['n'] = "1" ;
        $param['M'] = "0" ;

        $element=new objet_element();
        if ($objelem_gobj_cod>0){
            $elements = $element->getBy_objelem_gobj_cod($objelem_gobj_cod);
        } else {
            $elements = $element->getBy_objelem_obj_cod($objelem_obj_cod);
        }

        // filtre sur le type de condition
        if ($elements || is_array($elements)) {
            $elements = array_filter($elements, function($e) use ($type_condition) { return ($e->objelem_param_id == $type_condition);} );
        }

        // Ajouter le nombre mini d'élément
        while ( !$elements || sizeof($elements) < (1*$param['n']) )   $elements[] =  new objet_element;
        $add_buttons = (1*$param['M']==1*$param['n'] && 1*$param['M']>0) ? false : true;

        echo "Liste des condtions que doit verifier le perso pour pouvoir <strong><u>$verbe_type</u></strong> l'objet.";

        foreach($elements as $row => $element)
        {
            $row_id = "row-$param_id-$row-";
            $objelem_misc_nom = "";
            echo '<tr id="' . $row_id . '" style="' . $style_tr . '">';
            if ($add_buttons) echo '<td><input type="button" class="test" value="Supprimer" onClick="delQueteAutoParamRow($(this).parent(\'td\').parent(\'tr\'), ' . $param['n'] . ');"></td>';

            switch ($param['type'])
            {
                case 'perso_condition':       // pour invocation
                    if ((1 * $element->objelem_misc_cod != 0) && ($element->objelem_type == $param['type'])) {
                        $bon = new bonus_type();
                        $bon->charge($element->objelem_misc_cod);
                        $objelem_misc_nom = $bon->tonbus_libelle;
                    }
                    echo '<td>Conditions :
                                    <input data-entry="val" id="' . $row_id . 'objelem_cod" name="objelem_cod[' . $param_id . '][]" type="hidden" value="' . ($element->objelem_type == $param['type'] ? $element->objelem_cod : '') . '"> 
                                    <input name="objelem_type[' . $param_id . '][]" type="hidden" value="' . $param['type'] . '"> 
                                     ' . create_selectbox("objelem_param_num_1[$param_id][]", array("0" => "ET", "1" => "OU"), 1 * $element->objelem_param_num_1, array('id' => "{$row_id}objelem_param_num_1", 'style' => 'style="width: 100px;" data-entry="val"')) . '
                                     ' . create_selectbox_from_req("objelem_misc_cod[$param_id][]", "select aqtypecarac_cod, aqtypecarac_nom from quetes.aquete_type_carac order by aqtypecarac_type, aqtypecarac_nom, aqtypecarac_cod", 1 * $element->objelem_misc_cod, array('id' => "{$row_id}objelem_misc_cod", 'style' => 'style="width: 250px;" data-entry="val"')) . '
                                     ' . create_selectbox("objelem_param_txt_1[$param_id][]", array("=" => "=", "!=" => "!=", "<" => "<", "<=" => "<=", "entre" => "entre", ">" => ">", ">=" => ">="), $element->objelem_param_txt_1, array('id' => "{$row_id}objelem_param_txt_1", 'style' => 'style="width: 50px;" data-entry="val"')) . '
                                     <input data-entry="val" name="objelem_param_txt_2[' . $param_id . '][]" id="' . $row_id . 'objelem_param_txt_2" type="text" size="15" value="' . $element->objelem_param_txt_2 . '" style="margin-top: 5px;">
                                     &nbsp;&nbsp;( et <input data-entry="val" name="objelem_param_txt_3[' . $param_id . '][]" id="' . $row_id . 'objelem_param_txt_3" type="text" size="15" value="' . $element->objelem_param_txt_3 . '"> &rArr; pour la condition « entre » seulement )
                                   </td>';
                    break;
                default:
                    echo '<td>Type de paramètre inconnu</td>';
                    break;
            }
            echo "</tr>";

        }

        if ($add_buttons) echo '<tr id="add-'.$row_id.'" style="'.$style_tr.'"><td> <input type="button" class="test" value="Nouveau" onClick="addQueteAutoParamRow($(this).parent(\'td\').parent(\'tr\').prev(), '.$param['M'].');"> </td></tr>';
        echo '</table>';

        echo '<table width="80%" align="center">';
        echo '<tr><td colspan="2"><br><input class="test" type="submit" value="Annuler"/>&nbsp;&nbsp;&nbsp;<input class="test" type="submit" value="Sauvegarder" /></td></tr>';
        echo '</table>';

        echo '</form>';
        echo '<hr>';
    }

    if ($objelem_gobj_cod>0 && $type_condition==1)
    {
        echo '<br> <strong><u>Remarques</u></strong>:<br>
            * N’oubliez pas que TOUS les exemplaires d’un objet générique possèdederont immédiatement ces conditions d’équipement.<br>
            * S’il n’y a aucune condition, les conditions d’usages habituelles seront appliquées: objet réservé aux persos/monstres.<br>
            * Par défaut un familier ne peut jamais vérifier les conditions d’équipement d’un objet, <b>SAUF</b>:<br>
            &nbsp;&nbsp;-> si il est explicitement spécifié dans une règle : <u>"ET Type perso = 3"</u><br>
            * si les conditions sont modifiées rendant inéquipable un objet à un perso alors que celui-ci a déjà équipé l’objet:<br>
            &nbsp;&nbsp;-> L’objet restera équipé et le perso en gardera ses avantages (bonus/malus, sorts, etc...)<br>
            &nbsp;&nbsp;-> Lorsqu’il déséquipera cet objet, le perso ne sera plus en mesure de le ré-équiper.
        <br><p style="text-align:center;"><a href="admin_objet_generique_edit.php?&gobj_cod='.$_REQUEST["objelem_gobj_cod"].'">Retour aux modificationx d’objets génériques</a>';
    }
    else if ($type_condition==1)
    {
        echo '<br> <strong><u>Remarques</u></strong>:<br>
            * N’oubliez pas les conditions ici seront appliquées <u>en plus</u> des conditions de l’objet générique.<br>
            * S’il n’y a aucune condition, les conditions d’usages habituelles seront appliquées: objet réservé aux persos/monstres.<br>
            * Par défaut un familier ne peut jamais vérifier les conditions d’équipement d’un objet, <b>SAUF</b>:<br>
            &nbsp;&nbsp;-> si il est explicitement spécifié dans une règle : <u>"ET Type perso = 3"</u><br>
            * si les conditions sont modifiées rendant inéquipable un objet à un perso alors que celui-ci a déjà équipé l’objet:<br>
            &nbsp;&nbsp;-> L’objet restera équipé et le perso en gardera ses avantages (bonus/malus, sorts, etc...)<br>
            &nbsp;&nbsp;-> Lorsqu’il déséquipera cet objet, le perso ne sera plus en mesure de le ré-équiper.
            <br><p style="text-align:center;"><a href="admin_objet_edit.php?&methode=objet&num_objet='.$_REQUEST["objelem_obj_cod"].'">Retour aux modifications de l’objets</a>';

    }
    else if ($objelem_gobj_cod>0)
    {
        echo '<br> <strong><u>Remarques</u></strong>:<br>
            * N’oubliez pas que TOUS les exemplaires d’un objet générique possèderont immédiatement ces conditions de ramssage.<br>
            * Par défaut un un perso peut toujours ramasser un objet, <b>SAUF</b>:<br>
            &nbsp;&nbsp;-> si il est explicitement spécifié dans une règle, exemple : <u>"ET Type perso != 1"</u><br>            
            * si les conditions sont modifiées rendant non-ramassable un objet pour un perso qui en possède déjà un exemplaire dans son inventaire:<br>
            &nbsp;&nbsp;-> L’objet restera dans son inventaire et le perso en gardera ses avantages (bonus/malus, sorts, etc...)<br>
            &nbsp;&nbsp;-> Lorsqu’il deposera cet objet, le perso ne sera plus en mesure de le re-ramasser.
        <br><p style="text-align:center;"><a href="admin_objet_generique_edit.php?&gobj_cod='.$_REQUEST["objelem_gobj_cod"].'">Retour aux modificationx d’objets génériques</a>';
    }
    else
    {
        echo '<br> <strong><u>Remarques</u></strong>:<br>
            * N’oubliez pas les conditions ici seront appliquées <u>en plus</u> des conditions de l’objet générique.<br>
            * Par défaut un un perso peut toujours ramasser un objet, <b>SAUF</b>:<br>
            &nbsp;&nbsp;-> si il est explicitement spécifié dans une règle, exemple : <u>"ET Type perso != 1"</u><br>                   
            * si les conditions sont modifiées rendant non-ramassable un objet pour un perso qui en possède déjà un exemplaire dans son inventaire:<br>
            &nbsp;&nbsp;-> L’objet restera dans son inventaire et le perso en gardera ses avantages (bonus/malus, sorts, etc...)<br>
            &nbsp;&nbsp;-> Lorsqu’il deposera cet objet, le perso ne sera plus en mesure de le re-ramasser.
            <br><p style="text-align:center;"><a href="admin_objet_edit.php?&methode=objet&num_objet='.$_REQUEST["objelem_obj_cod"].'">Retour aux modifications de l’objets</a>';

    }


}

$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
