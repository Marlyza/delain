<?php
include "blocks/_header_page_jeu.php";
$html = new html;
//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
include 'sadmin.php'; // Pour intégration XML
?>
    <SCRIPT language="javascript" src="../scripts/controlUtils.js"></SCRIPT>
    <p class="titre">Gestion des cachettes</p>
<?php

//************************************************
// Page de création ou modification d’une cachette
//************************************************

$erreur      = 0;
$droit_modif = 'dcompt_modif_perso';
define('APPEL', 1);
include "blocks/_test_droit_modif_generique.php";

if ($erreur == 0)
{
    $methode           = get_request_var('methode', 'debut');
    $mode              = get_request_var('mode', 'normal');

    switch ($methode)
    {
        case "debut":
            ?>
            <p>Choisissez votre méthode :</p>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=cre">Création d’une cachette ?</a> / <a
                href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=mod">Modification d’une cachette existante</a><br>
            <hr>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=update_cache_vide">Liste des cachettes vides</a><br>
            <hr>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=liste_cre">Création d’une liste d’objets pour
                cachette</a> / <a
                href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=liste_mod">Modification d’une liste d’objets
            existante</a>
            <br>
            <hr><br><br>
            <?php
            $included = true;
            include "modif_etage6.php";
            break;

        case "liste_cre": // création d’une liste d’objet pour cachettes
            ?>
            <form name="liste_modif" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="methode" value="liste_cre1">
                <table>
                    <tr>
                        <td class="soustitre2"><strong>Nom de la liste / <em>Attention indispensable !</em><strong></td>
                        <td><input type="text" name="nom_liste" value="à remplir !"></td>
                    </tr>
                    <tr>
                        <td class="soustitre2">Liste des objets disponibles</td>
                        <td class="soustitre2">Éléments à ajouter à la liste</td>
                    </tr>
                    <tr>
                        <td>
                            <select multiple name="gobj_cod" onChange="explode(document.liste_modif.gobj_cod.value);"
                                    size="40" style="width:340px;">
                                <option value="">---------------</option>
                                <?php
                                $req = 'select \'Type d’objet : \' || tobj_libelle as tobj_libelle, gobj_cod::text || \'#\' || gobj_nom as gobj_cod, gobj_nom from objet_generique,type_objet
				where gobj_tobj_cod not in (3,9,10)
					and gobj_tobj_cod = tobj_cod
				order by gobj_tobj_cod,gobj_nom';
                                echo $html->select_from_query($req, 'gobj_cod', 'gobj_nom', '', 'tobj_libelle');
                                ?>
                            </select><br>
                        </td>
                        <td>
                            <textarea name="obj_ajout_nom" COLS="50" rows="20"></textarea>
                            <br>
                            <div class="centrer"><input type="submit" class="test" value="Création de la liste"></div>
                            <input name="obj_ajout" value="">
                        </td>
                    </tr>
                </table>
            </form>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=liste_cre">Retour Création d’une liste d’objets pour
                cachette</a>
            <?php
            break;

        case "liste_cre1":
            //Liste des contrôles : nom de la liste existante / Nom de liste rempli
            $nom_liste = $_POST['nom_liste'];
            if ($nom_liste == 'à remplir !' or $nom_liste == '')
            {
                echo 'Vous n’avez pas saisi de nom de liste viable !'; ?>
                <br><a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=liste_cre">Retour Création d’une liste d’objets
                pour
                cachette</a>
                <?php
                break;
            }
            $req  = "select creappro_nom from cachette_reappro 
                where creappro_nom = :nom_liste";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":nom_liste" => $nom_liste), $stmt);
            if ($result = $stmt->fetch())
            {
                $req    = "select creappro_cache_liste_respawn 
                    from cachette_reappro 
                    order by creappro_cache_liste_respawn desc limit 1";
                $stmt   = $pdo->query($req);
                $result = $stmt->fetch();
                $liste  = $result['creappro_cache_liste_respawn'] + 1; // on crée le nouveau numéro de liste
                $objet  = explode(",", $_POST['obj_ajout']);
                foreach ($objet as $cle => $valeur)
                {
                    if ($valeur != '')
                    {
                        $req  =
                            "select creappro_gobj_cod 
                                from cachette_reappro 
                                where creappro_cache_liste_respawn = :liste
                                and creappro_gobj_cod = :valeur ";
                        $stmt = $pdo->prepare($req);
                        $stmt = $pdo->execute(array(":liste"  => $liste,
                                                    ":valeur" => $valeur), $stmt);
                        if ($result = $stmt->fetch())
                        {
                            echo $valeur . '<br />';
                            $req  =
                                "insert into cachette_reappro (creappro_cache_liste_respawn,creappro_gobj_cod,creappro_nom) 
                                values (:liste,:valeur,:nom_liste)";
                            $stmt = $pdo->prepare($req);
                            $stmt = $pdo->execute(array(":liste"     => $liste,
                                                        ":valeur"    => $valeur,
                                                        ":nom_liste" => $nom_liste), $stmt);

                        } else
                        {
                            echo 'Risque d’objet déjà présent :' . $valeur . '<br>';
                        }
                    }
                }
                ?>
                <br><a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=liste_cre">Retour Création d’une liste d’objets
                pour
                cachette</a>
                <br><a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=liste_mod">Modification d’une liste d’objets
                pour
                cachette</a>
                <?php
            } else
            {
                echo 'Vous n’avez pas saisi de nom de liste viable ! Il est déjà utilisé'; ?>
                <br><a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=liste_cre">Retour Création d’une liste d’objets
                pour
                cachette</a>
                <?php
            }
            break;

        case "liste_mod": // Modification d’une liste d’objets pour cachettes
            if (!isset($liste_num)) $liste_num = -1;
            ?>
            <form name="liste_modif_choix" method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="methode" value="liste_mod"/>
                <select name="liste_num">
                    <option value="-1">Choisir la liste à modifier</option>
                    ;
                    <?php
                    $req =
                        "select creappro_cache_liste_respawn, creappro_nom from cachette_reappro group by creappro_cache_liste_respawn,creappro_nom order by creappro_cache_liste_respawn";
                    echo $html->select_from_query($req, 'creappro_cache_liste_respawn', 'creappro_nom', $liste_num);
                    ?>
                </select>
                <input type='submit' class='test' value='Sélectionner'/>
            </form>
            <?php if ($liste_num > 0)
        {
            ?>
            <form name="liste_modif" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="methode" value="liste_mod1"/>
                <input type="hidden" name="liste_num" value="<?php echo $liste_num; ?>"/>
                <h1>Éléments à supprimer de la liste</h1>
                <table>
                    <tr>
                        <th class="soustitre2">Contenu de la liste
                        </td>
                        <th class="soustitre2">Éléments à supprimer
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <select multiple name="liste_objet" size="10"
                                    onChange="explode_cible(this.value, document.liste_modif.obj_sup, document.liste_modif.obj_sup_nom);"
                                    size="10" style="width:280px;">
                                <?php $req =
                                    "select gobj_cod::text || '#' || gobj_nom as creappro_gobj_cod, gobj_nom from cachette_reappro,objet_generique where creappro_gobj_cod = gobj_cod and creappro_cache_liste_respawn = $liste_num order by gobj_cod";
                                echo $html->select_from_query($req, 'creappro_gobj_cod', 'gobj_nom');
                                ?>
                            </select>
                        </td>
                        <td>
                            <textarea name="obj_sup_nom" COLS="50" rows="5"></textarea><br/>
                            <input name="obj_sup" value="">
                        </td>
                    </tr>
                </table>
                <h1>Éléments à rajouter à la liste</h1>
                <table>
                    <tr>
                        <th class="soustitre2">Liste des objets disponibles
                        </td>
                        <th class="soustitre2">Éléments à ajouter
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <select multiple name="gobj_cod"
                                    onChange="explode_cible(this.value, document.liste_modif.obj_ajout, document.liste_modif.obj_ajout_nom);"
                                    size="20" style="width:280px;">
                                <option value="">---------------</option>
                                <?php
                                $req = 'select \'Type d’objet : \' || tobj_libelle as tobj_libelle, gobj_cod::text || \'#\' || gobj_nom as gobj_cod, gobj_nom from objet_generique,type_objet
					where gobj_tobj_cod not in (3,9,10)
						and gobj_tobj_cod = tobj_cod
					order by gobj_tobj_cod,gobj_nom';
                                echo $html->select_from_query($req, 'gobj_cod', 'gobj_nom', '', 'tobj_libelle');
                                ?>
                            </select>
                        </td>
                        <td>
                            <textarea name="obj_ajout_nom" COLS="50" rows="5"></textarea><br/>
                            <input name="obj_ajout" value="">
                        </td>
                    </tr>
                </table>
                <input type="submit" class="test" value="Valider Suppression & Ajout"/>
            </form>
        <?php }
            ?>
            <hr><a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=liste_cre">Création d’une liste d’objets pour
            cachette</a>
            <?php
            break;

        case "liste_mod1":
            $liste = $_POST['liste_num'];
            $req       =
                "select creappro_nom from cachette_reappro 
                    where creappro_cache_liste_respawn = :liste
                    group by creappro_cache_liste_respawn,creappro_nom";
            $stmt      = $pdo->prepare($req);
            $stmt      = $pdo->execute(array(":liste" => $liste), $stmt);
            $result    = $stmt->fetch();
            $nom_liste = $result['creappro_nom'];
            $objet     = explode(",", $_POST['obj_sup']);
            echo 'Liste : "' . $liste . ' / ' . $nom_liste . '"<br>';
            echo 'Suppression de : <br>';
            $req  =
                "delete from cachette_reappro 
                        where creappro_cache_liste_respawn = :liste
                          and creappro_gobj_cod = :valeur";
            $stmt = $pdo->prepare($req);
            foreach ($objet as $cle => $valeur)
            {
                echo $valeur . '<br />';
                if ($valeur != '')
                {

                    $stmt = $pdo->execute(array(":liste"  => $liste,
                                                ":valeur" => $valeur), $stmt);
                }
            }
            $objet = explode(",", $_POST['obj_ajout']);
            echo 'Ajout de : <br>';
            $req  =
                "select creappro_gobj_cod from cachette_reappro 
                         where creappro_cache_liste_respawn = :liste
                        and creappro_gobj_cod = :valeur";
            $stmt = $pdo->prepare($req);
            foreach ($objet as $cle => $valeur)
            {
                if ($valeur != '')
                {

                    $stmt = $pdo->execute(array(":liste"  => $liste,
                                                ":valeur" => $valeur), $stmt);
                    if ($result = $stmt->fetch())
                    {
                        echo $valeur . '<br />';
                        $req  =
                            "insert into cachette_reappro (creappro_cache_liste_respawn,creappro_gobj_cod,creappro_nom) 
                            values (:liste,:valeur,:nom_liste)";
                        $stmt = $pdo->prepare($req);
                        $stmt = $pdo->execute(array(":liste"     => $liste,
                                                    ":valeur"    => $valeur,
                                                    ":nom_liste" => $nom_liste), $stmt);
                    } else
                    {
                        echo 'Risque d’objet déjà présent :' . $valeur . '<br>';
                    }
                }
            }
            ?>
            <br><a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=liste_mod">Modification d’une liste d’objets
            existante</a><br>
            <hr><br><br>
            <?php
            break;

        case "cre": // création d'une nouvelle cachette
            ?>
            <form name="cre" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <br> <strong>Première étape :</strong> pour créer une cachette, remplir les champs nécessaires
                <br> Cela va créer la cachette en tant que telle, avec son descriptif. Si certains champs ne doivent pas
                être remplis, laissez les vides
                <br>Ces informations sont utilisées par la suite, et si elles sont erronées, elles apparaîtront comme
                telles aux joueurs
                <br>Le remplissage de la cachette avec les objets se fait ensuite

                <input type="hidden" name="methode" value="cre1">
                <div class="centrer">
                    <table>
                        <tr>
                            <td class="soustitre2">Nom qui sera affiché</td>
                            <td><input type="text" name="nom"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Description</td>
                            <td><input type="text" name="desc"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Adresse de l’image associée</td>
                            <td><input type="text" name="image"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Position en X</td>
                            <td><input type="text" name="pos_x" value="<?php echo $pos_x ?>"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Position en Y</td>
                            <td><input type="text" name="pos_y" value="<?php echo $pos_y ?>"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Étage</td>
                            <td><select name="pos_etage">
                                    <?php
                                    echo $html->etage_select($pos_e);
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Coefficient d’impact de l’intelligence</td>
                            <td><input type="text" name="coef_int"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Coefficient d’impact de la dextérité</td>
                            <td><input type="text" name="coef_dex"></td>
                        </tr>
                        <tr>
                            <td colspan='2'><input type="hidden" name="liste_obj" value="1"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Nombre maximum d’objets dans la cachette (respawn)</td>
                            <td><input type="text" name="max_obj" value="3"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">% Chance de respawn des objets à chaque cycle</td>
                            <td><input type="text" name="chance_respawn" value="5"></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="centrer"><input type="submit" class="test" value="Valider !"></div>
                            </td>
                        </tr>

                    </table>
                </div>
            </form>
            <?php
            break;

        case "cre1":
            // vérification de la présence d’une position
            $erreur = 0;
            $req    = 'select pos_cod
				from positions
				where pos_x = :x
				AND pos_y = :y
				AND pos_etage = :etage';
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":x"     => $_POST['pos_x'],
                                          ":y"     => $_POST['pos_y'],
                                          ":etage" => $_POST['pos_etage']), $stmt);
            if (!$result = $stmt->fetch())
            {
                /*********************************/
                /* Il n’existe pas de position ! */
                /*********************************/
                echo 'Aucune position trouvée !<br>
					<a href="' . $_SERVER['PHP_SELF'] . '?methode=cre">Retour au début</a>';
                break;
            } else
            {
                $pos_cod = $result['pos_cod'];
            }
            //
            // on regarde si une cachette existe déjà sur cette position
            //
            $req2 = 'select cache_cod,cache_pos_cod 
                    from cachettes where 
                    cache_pos_cod = :pos';
            $stmt = $pdo->prepare($req2);
            $stmt = $pdo->execute(array(":pos" => $pos_cod), $stmt);

            if ($result = $stmt->fetch())
            {

                $cache_cod = $result['cache_cod'];
                echo 'Il y a une cachette existante sur cette position !<br>
					<a href="' . $_SERVER['PHP_SELF'] . '?cache_cod=' . $cache_cod . '&methode=update_cache">Changer son contenu ?</a><br>
					<a href="' . $_SERVER['PHP_SELF'] . '?methode=cre">Retour au début</a>';
                break;
            } else
            {
                //
                // on regarde si il y a déjà une autre fonction présente sur cette case
                //
                $req4         = 'select pos_fonction_arrivee 
                    from positions where pos_cod = :pos';
                $stmt         = $pdo->prepare($req4);
                $stmt         = $pdo->execute(array(":pos" => $pos_cod), $stmt);
                $fonction_cod = '';
                if ($result = $stmt->fetch())
                {
                    $fonction_cod = $result['pos_fonction_arrivee'];
                }

            }
            if ($fonction_cod != '')
            {
                /*******************************/
                /* Il y a une autre fonction ! */
                /*******************************/
                echo 'Il y a déjà une autre fonction sur cette case !<br> Fonction : ' . $fonction_cod . ' <br>
					<a href="' . $_SERVER['PHP_SELF'] . '?methode=update_fonction">Remplacer la fonction présente par une cachette ?</a><br> Attention, cette opération supprimera la précédente fonction !<br>
					<a href="' . $_SERVER['PHP_SELF'] . '?methode=cre">Retour au début</a>';
            } else
                //on intègre la nouvelle cachette
            {

                $req  = 'insert into cachettes (cache_cod,cache_nom,cache_desc,cache_image,cache_pos_cod,cache_max_respawn,cache_chance_respawn,cache_liste_respawn)
					values (DEFAULT, :nom, :desc, :image, :pos_cod,:max_obj,:chance_respawn,:liste_obj)';
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(":nom"            => $nom,
                                            ":desc"           => $desc,
                                            ":image"          => $image,
                                            ":pos_cod"        => $pos_cod,
                                            ":max_obj"        => $max_obj,
                                            ":chance_respawn" => $chance_respawn,
                                            ":liste_obj"      => $liste_obj
                                      ), $stmt);


                $cachette  = 'decouvre_cachette([perso],' . $_POST['coef_dex'] . ',' . $_POST['coef_int'] . ')';
                $req3      = "update positions set pos_fonction_arrivee = :cachette where pos_cod = :pos_cod";
                $stmt      = $pdo->prepare($req3);
                $stmt      = $pdo->execute(array(":cachette" => $cachette,
                                                 ":pos_cod"  => $pos_cod
                                           ), $stmt);
                $result    = $stmt->fetch();
                $req2      = 'select cache_cod from cachettes 
                    where cache_pos_cod = :pos_cod';
                $stmt      = $pdo->prepare($req2);
                $stmt      = $pdo->execute(array(
                                               ":pos_cod" => $pos_cod
                                           ), $stmt);
                $result    = $stmt->fetch();
                $cache_cod = $result['cache_cod'];
                echo '<p>L’insertion de cette nouvelle cachette s’est bien déroulée en ' . $_POST['pos_x'] . "," . $_POST['pos_y'] . " au " . $_POST['pos_etage'] . '<br>
					<a href="' . $_SERVER['PHP_SELF'] . '?cache_cod=' . $cache_cod . '&methode=update_cache">Changer son contenu ?</a><br>';
            }
            break;

        //Cas du remplacement d’une fonction présente,, par une cachette
        case "update_fonction":
            $cachette = 'decouvre_cachette([perso],' . $_POST['coef_dex'] . ',' . $_POST['coef_int'] . ')';
            $req3     = "update positions 
                set pos_fonction_arrivee = :cachette
                where pos_cod = :pos_cod";
            $stmt     = $pdo->prepare($req3);
            $stmt     = $pdo->execute(array(":cachette" => $cachette,
                                            ":pos_cod"  => $pos_cod), $stmt);

            echo 'Mise à jour réalisée <br>
				<a href="' . $_SERVER['PHP_SELF'] . '?cache_cod=' . $cache_cod . '&methode=update_cache">Changer son contenu ?</a><br>
				<a href="' . $_SERVER['PHP_SELF'] . '?methode=cre">Retour au début</a>';
            break;

        case "mod":
            ?>
            <p>Liste de l’ensemble des cachettes du jeu<br>
            <?php
            echo '<hr><a href="' . $_SERVER['PHP_SELF'] . '?methode=debut">Retour au début</a><hr>';
            $req  = 'select cache_cod,cache_nom,cache_desc,cache_image,pos_x,pos_y,pos_etage,etage_libelle
				from cachettes,positions,etage
				where cache_pos_cod = pos_cod
					and pos_etage = etage_numero
					and etage_numero <> 6
				order by pos_etage,cache_cod';
            $stmt = $pdo->query($req);
            while ($result = $stmt->fetch())
            {
                $cache_cod = $result['cache_cod'];
                echo '<br><strong>Cachette numéro </strong>' . $result['cache_cod'] . '<br><strong>Nom : </strong>' . $result['cache_nom'] . '<br><strong>Description : </strong>' . $result['cache_desc'] .
                     '<br><strong>X : ' . $result['pos_x'] . ' / Y : ' . $result['pos_y'] . ' / Étage : </strong>' . $result['etage_libelle'] . '<br>
					<a href="' . $_SERVER['PHP_SELF'] . '?cache_cod=' . $cache_cod . '&methode=update_cache">Modifier le contenu de cette cachette ?</a><hr>';
            }
            break;

        /*********************************************/
        /* On passe au remplissage d’une cachette    */
        /*********************************************/

        //Analyse des cachettes vides
        case "update_cache_vide":
            ?>
            <p>Liste des cachettes vides du jeu<br>
            <?php
            echo '<hr><a href="' . $_SERVER['PHP_SELF'] . '?methode=debut">Retour au début</a><hr>';
            $req = 'select cache_cod,cache_nom,cache_desc,cache_image,pos_x,pos_y,pos_etage,etage_libelle
				from cachettes,positions,etage
				where not exists
					(select objcache_cod_cache_cod from cachettes_objets where objcache_cod_cache_cod = cache_cod)
					and cache_pos_cod = pos_cod
					and pos_etage = etage_numero
				order by pos_etage,cache_cod';

            $stmt = $pdo->query($req);
            while ($result = $stmt->fetch())
            {
                $cache_cod = $result['cache_cod'];
                echo '<br><strong>Cachette numéro </strong>' . $result['cache_cod'] . '<br><strong>Nom : </strong>' . $result['cache_nom'] . '<br><strong>Description : </strong>' . $result['cache_desc'] .
                     '<br><strong>X : ' . $result['pos_x'] . ' / Y : ' . $result['pos_y'] . ' / Étage : </strong>' . $result['etage_libelle'] . '<br>
				<a href="' . $_SERVER['PHP_SELF'] . '?cache_cod=' . $cache_cod . '&methode=update_cache">Modifier le contenu de cette cachette ?</a><hr>';
            }
            break;

        case "update_cache":
            ?>
            <SCRIPT language="javascript">
                var listeBase = new Array();
                <?php            // LISTE DES OBJETS POSSIBLES
                require "blocks/_admin_perso_et_titre.php";
                ?>
                var listeCurrent = new Array();
                <?php            // LISTE DES OBJETS DANS LA CACHETTE
                $req_cache = 'select count(obj_cod) as nombre,gobj_cod,obj_nom
				from cachettes_objets,objets,objet_generique
				where objcache_cod_cache_cod = :cache_cod
					and obj_cod = objcache_obj_cod
					and obj_gobj_cod = gobj_cod
				group by gobj_cod,obj_nom
				order by obj_nom';
                $stmt = $pdo->prepare($req_cache);
                $stmt = $pdo->execute(array(":cache_cod" => $_REQUEST['cache_cod']), $stmt);
                $nb_tobj = 0;
                while ($result = $stmt->fetch())
                {
                    echo("listeCurrent[$nb_tobj] = new Array(0); \n");
                    echo("listeCurrent[$nb_tobj][0] = \"" . $result['gobj_cod'] . "\"; \n");
                    echo("listeCurrent[$nb_tobj][1] = \"" . $result['nombre'] . "\"; \n");
                    echo("listeCurrent[$nb_tobj][2] = \"\"; \n");
                    echo("listeCurrent[$nb_tobj][3] = \"\"; \n");
                    $nb_tobj++;
                }
                ?>

            </SCRIPT>

            <form method="post" name="formCache" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" name="methode" value="update_cache2">
                <input type="hidden" name="cache_cod" value="<?php echo $cache_cod ?>">
                <TABLE width="80%" class="centrer">
                    <TR>
                        <TD>
                            <select multiple name="select1" size="10" style="width:280px;">
                            </select>
                        </TD>
                        <TD>
                            <input class="test" type="button" value="<- 1 "
                                   onClick="addToOptions(document.formCache.select2,document.formCache.select1,1,document.formCache.compiledCache);">
                            <br><input class="test" type="button" value="<- 5 "
                                       onClick="addToOptions(document.formCache.select2,document.formCache.select1,5,document.formCache.compiledCache);">
                            <br><input class="test" type="button" value="-> 1 "
                                       onClick="substractToOptions(document.formCache.select1,1,document.formCache.compiledCache);">
                            <br><input class="test" type="button" value="-> 5 "
                                       onClick="substractToOptions(document.formCache.select1,5,document.formCache.compiledCache);">
                        </TD>
                        <TD>
                            <select style="width: 280px;" name="selecttype"
                                    onchange='cleanOption(document.formCache.select2); addOptionArray(document.formCache.select2, listeBase, this.value, document.formCache.selectvaleur.value);'>
                                <option value=''>Tous types d’objets</option>
                                <?php
                                $req_tobj = "select distinct tobj_libelle from type_objet order by tobj_libelle";
                                $stmt     = $pdo->query($req);

                                while ($result = $stmt->fetch())
                                {
                                    $tobj_libelle = str_replace("\"", "", $result['tobj_libelle']);
                                    echo "<option value='$tobj_libelle'>$tobj_libelle</option>";
                                }
                                ?>
                            </select><br/>
                            <select style="width: 280px;" name="selectvaleur"
                                    onchange='cleanOption(document.formCache.select2); addOptionArray(document.formCache.select2, listeBase, document.formCache.selecttype.value, this.value);'>
                                <option value=''>Valeur indéfinie</option>
                                <option value='0;1000'>Moins de 1 000 brouzoufs</option>
                                <option value='1000;5000'>Entre 1 000 et 5 000 brouzoufs</option>
                                <option value='5000;10000'>Entre 5 000 et 10 000 brouzoufs</option>
                                <option value='10000;20000'>Entre 10 000 et 20 000 brouzoufs</option>
                                <option value='20000;50000'>Entre 20 000 et 50 000 brouzoufs</option>
                                <option value='50000;100000'>Entre 50 000 et 100 000 brouzoufs</option>
                                <option value='100000;100000000'>Plus de 100 000 brouzoufs</option>
                            </select><br/>
                            <select multiple name="select2" size="10" style="width:280px;">
                            </select>
                        </TD>
                    </TR>
                </TABLE>

                <input type="hidden" name="compiledCache" value=""/>
                <SCRIPT>
                    addOptionArray(document.formCache.select2, listeBase, '', '');
                    fillOptions(document.formCache.select2, document.formCache.select1, listeCurrent);
                    compileAccumulatorCounter(document.formCache.select1, document.formCache.compiledCache);
                </SCRIPT>
                <input type="submit" value="Modifier la cachette" class="test">
            </form>
            <?php
            $req = 'select cache_cod,cache_nom,cache_desc,cache_image,cache_max_respawn,cache_chance_respawn,cache_liste_respawn,creappro_nom
				from cachettes,cachette_reappro
				where cache_cod = :cache_cod
				and creappro_cache_liste_respawn = cache_liste_respawn limit 1';

            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":cache_cod" => $_REQUEST['cache_cod']), $stmt);
            $result = $stmt->fetch();

            $cache_nom            = $result['cache_nom'];
            $cache_desc           = $result['cache_desc'];
            $cache_image          = $result['cache_image'];
            $cache_max_respawn    = $result['cache_max_respawn'];
            $cache_chance_respawn = $result['cache_chance_respawn'];
            $creappro_nom         = $result['creappro_nom'];
            $cache_liste_respawn  = $result['cache_liste_respawn'];

            echo 'num cachette : ' . $_REQUEST['cache_cod'] . '<br>';
            ?>
            <form name="update_nom" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

                <input type="hidden" name="methode" value="update_cache_nom">
                <input type="hidden" name="cache_cod" value="<?php echo $cache_cod ?>">
                <div class="centrer">
                    <table>
                        <tr>
                            <td class="soustitre2">Nom qui sera affiché</td>
                            <td><input type="text" name="nom" value="<?php echo $cache_nom ?>"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Description</td>
                            <td><input type="text" name="desc" value="<?php echo $cache_desc ?>"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Adresse de l’image associée</td>
                            <td><input type="text" name="image" value="<?php echo $cache_image ?>"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Nombre d’objets maximum dans la cachette</td>
                            <td><input type="text" name="cache_max_respawn" value="<?php echo $cache_max_respawn ?>">
                            </td>
                        </tr>
                        <tr>
                            <td class="soustitre2">% de chance de respawn des objets par cycle</td>
                            <td><input type="text" name="cache_chance_respawn"
                                       value="<?php echo $cache_chance_respawn ?>"></td>
                        </tr>
                        <tr>
                            <td class="soustitre2">Liste d’objets associés</td>
                            <td><select name="cache_liste_respawn">
                                    <?php
                                    $req =
                                        "select creappro_cache_liste_respawn,creappro_nom from cachette_reappro group by creappro_cache_liste_respawn,creappro_nom order by creappro_cache_liste_respawn";
                                    echo $html->select_from_query($req, 'creappro_cache_liste_respawn', 'creappro_nom', $cache_liste_respawn);
                                    ?>
                                </select></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="centrer"><input type="submit" class="test" value="Valider !"></div>
                            </td>
                        </tr>
                        <hr>

                    </table>
                </div>
            </form>

            <?php
            echo '<hr><a href="' . $_SERVER['PHP_SELF'] . '?methode=debut">Retour au début</a>';
            break;

        // MAJ des objets contenus dans la cachette
        case "update_cache2":
            $cache_cod = $_REQUEST['cache_cod'];
            // SUPPRESSION D’OBJETS DE LA CACHETTE
            $req_cache = "select distinct obj_gobj_cod
				from cachettes_objets,objets
				where objcache_cod_cache_cod = $cache_cod
					and objcache_obj_cod = obj_cod ";
            $stmt      = $pdo->prepare($req_cache);
            $stmt      = $pdo->execute(array(":cache_cod" => $cache_cod), $stmt);
            while ($result = $stmt->fetch())
            {
                $obj_cod = $result['obj_gobj_cod'];
                $to_find = $result['obj_gobj_cod'] . ":";
                $pos1    = strpos($compiledCache, $to_find);
                //$pos1 = stripos($compiledCache, $to_find);
                if ($pos1 === false)
                {
                    //echo "The string '$to_find' was not found in the string";
                    $req_add_obj = "select obj_cod
						from cachettes_objets,objets
						where objcache_cod_cache_cod = :cache_cod
							and objcache_obj_cod = obj_cod
							and obj_gobj_cod = :obj_cod";
                    $stmt        = $pdo->prepare($req_add_obj);
                    $stmt        = $pdo->execute(array(":obj_cod"   => $obj_cod,
                                                       ":cache_cod" => $cache_cod), $stmt);

                    $req_supr_obj = "select  f_del_objet(del_obj_cod)";
                    $stmt2        = $pdo->prepare($req_supr_obj);
                    while ($result = $stmt->fetch())
                    {

                        $stmt2 = $pdo->execute(array(":del_obj_cod" => $result['obj_cod']), $stmt2);

                    }
                }
            }
            $objs = explode(";", $compiledCache);

            $nb_ajoute = 0;
            for ($i = 0; $i < count($objs); $i++)
            {
                if ($objs[$i] != "")
                {
                    $obj        = explode(":", $objs[$i]);
                    $obj_cod    = $obj[0];
                    $obj_nb     = $obj[1];
                    $obj_nb_pre = 0;
                    // NOMBRE D'OBJETS DE CE TYPE DEJA DANS LA CACHETTE
                    $req_cache = "select count(obj_cod) as nombre
						from cachettes_objets,objets
						where objcache_cod_cache_cod = $cache_cod
							and objcache_obj_cod = obj_cod
							and obj_gobj_cod = :obj_cod ";
                    $stmt      = $pdo->prepare($req_cache);
                    $stmt      = $pdo->execute(array(":obj_cod" => $obj_cod), $stmt);
                    if ($result = $stmt->fetch())
                    {
                        $obj_nb_pre = $result['nombre'];
                    }
                    // NBR OBJETS A AJOUTER
                    $obj_nb_add = $obj_nb - $obj_nb_pre;
                    if ($obj_nb_add > 0)
                    {
                        // AJOUT D’OBJETS
                        $req_add_obj = "select cree_objet_cachette(:obj_cod,:cache_cod,:obj_nb_add)";
                        $stmt        = $pdo->prepare($req_add_obj);
                        $stmt        = $pdo->execute(array(":obj_cod"    => $obj_cod,
                                                           ":cache_cod"  => $cache_cod,
                                                           ":obj_nb_add" => $obj_nb_add), $stmt);

                        $req_cache = "select gobj_nom from objet_generique where gobj_cod = :obj_cod ";
                        $stmt      = $pdo->prepare($req_cache);
                        $stmt      = $pdo->execute(array(":obj_cod" => $obj_cod), $stmt);
                        $nb_ajoute++;
                    } else if ($obj_nb_add < 0)
                    {
                        // SUPPRESSION D'OBJETS
                        $req_cache = "select gobj_nom from objet_generique where gobj_cod = :obj_cod";
                        $stmt      = $pdo->prepare($req_cache);
                        $stmt      = $pdo->execute(array(":obj_cod" => $obj_cod), $stmt);

                        $nb_ajoute++;

                        $obj_nb_add   = -1 * $obj_nb_add;
                        $req_add_obj  = "select obj_cod
							from cachettes_objets,objets
							where objcache_cod_cache_cod = :cache_cod
								and objcache_obj_cod = obj_cod
								and obj_gobj_cod = $obj_cod LIMIT $obj_nb_add";
                        $stmt         = $pdo->prepare($req_add_obj);
                        $stmt         = $pdo->execute(array(":cache_cod" => $cache_cod,
                                                            ":obj_cod"   => $obj_cod), $stmt);
                        $req_supr_obj = "delete from cachettes_objets where objcache_obj_cod = :del_obj_cod";
                        $stmt2        = $pdo->prepare($req_supr_obj);
                        $req_supr_obj = "delete from objets where obj_cod = :del_obj_cod";
                        $stmt3        = $pdo->prepare($req_supr_obj);
                        while ($result = $stmt->fetch())
                        {
                            $pdo->execute(array("del_obj_cod" => $result['obj_cod']), $stmt2);
                            $pdo->execute(array("del_obj_cod" => $result['obj_cod']), $stmt3);
                        }
                    }
                }
            }
            echo "MAJ cachette";
            ?>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=cre">Création d’une cachette ?</a><br>
            <hr>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=mod">Modification d’une cachette existante ?</a><br>
            <hr>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=update_cache_vide">Liste des cachettes vides</a><br>
            <hr>
            <?php
            break;

        //MAJ du nom et description
        case "update_cache_nom":
            $req =
                'update cachettes set cache_nom = :nom, cache_desc = :desc, 
                     cache_image = :image, 
                     cache_liste_respawn = :cache_liste_respawn, 
                     cache_max_respawn = :cache_max_respawn, 
                cache_chance_respawn = :cache_chance_respawn 
                where cache_cod = :cache_cod';
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":nom"                  => $_REQUEST['nom'],
                                        ":desc"                 => $_REQUEST['desc'],
                                        ":image"                => $_REQUEST['image'],
                                        ":cache_liste_respawn"  => $_REQUEST['cache_liste_respawn'],
                                        ":cache_max_respawn"    => $_REQUEST['cache_max_respawn'],
                                        ":cache_chance_respawn" => $_REQUEST['cache_chance_respawn'],
                                        ":cache_cod"            => $_REQUEST['cache_cod']),
                                  $stmt);
            echo "MAJ cachette";
            ?>
            <br><a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=cre">Création d’une cachette ?</a><br>
            <hr>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=mod">Modification d’une cachette existante ?</a><br>
            <hr>
            <?php
            break;
    }
}
if ($mode == 'popup')
    echo '<script type="text/javascript">document.getElementById("colonne1").style.display="none";
		document.getElementById("colonne2").style.marginLeft="0";
		</script>';

$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
