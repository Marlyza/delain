<?php
define('APPEL', 1);
include "blocks/_header_page_jeu.php";
ob_start();
$methode = get_request_var('methode', 'debut');
require "blocks/_guilde_test_perso.php";


if ($autorise)
{
    $num_guilde = $guilde->guilde_cod;
    switch ($methode)
    {
        case "debut":
            $admin['O'] = 'Administrateur';
            $admin['N'] = 'Membre';
            $req        = "select rguilde_libelle_rang,rguilde_rang_cod,rguilde_cod,rguilde_admin
									 		from guilde_rang
									 		where rguilde_guilde_cod = $num_guilde
									 		order by rguilde_cod ";
            echo "<form name=\"modif\" method=\"post\" action=\"guilde_modif_rang.php\">";
            echo "<input type=\"hidden\" name=\"methode\" value=\"suite\">";
            echo "<input type=\"hidden\" name=\"vperso\" value=\"$vperso\">";
            echo "<p>Choisissez le rang à affecter :";
            echo "<select name=\"rang\">";
            $stmt = $pdo->query($req);
            while ($result = $stmt->fetch())
            {
                echo "<option value=\"", $result['rguilde_rang_cod'], "\">", $result['rguilde_libelle_rang'], "</option>";
            }
            echo "</select><br><center><input type=\"submit\" class=\"test\" value=\"Valider !\"><center>";
            echo "</form>";
            break;
        case "suite":
            // on compte le nombre d'admin, si >2, alors un admin peut changer son rang pour devenir simple membre
            $req          = "select count(pguilde_perso_cod) as nombre from guilde_perso,guilde_rang
										where pguilde_guilde_cod = 
												(select pguilde_guilde_cod from guilde_perso,guilde_rang
														where pguilde_perso_cod = $vperso
														and pguilde_guilde_cod = rguilde_guilde_cod
														and pguilde_rang_cod = rguilde_rang_cod) 
										and pguilde_guilde_cod = rguilde_guilde_cod
										and pguilde_rang_cod = rguilde_rang_cod
										and rguilde_admin = 'O'";
            $stmt         = $pdo->query($req);
            $result       = $stmt->fetch();
            $nombre_admin = $result['nombre'];

            // on recherche le rang d'admin précédent			
            $req    = "select rguilde_admin from guilde_perso,guilde_rang
										where pguilde_perso_cod = $vperso
										and pguilde_guilde_cod = rguilde_guilde_cod
										and pguilde_rang_cod = rguilde_rang_cod ";
            $stmt   = $pdo->query($req);
            $result = $stmt->fetch();
            if ($result['rguilde_admin'] == 'O')
            {
                // on recherche le nouveau rang admin
                $req    = "select rguilde_admin from guilde_rang 
											where rguilde_guilde_cod = $num_guilde
											and rguilde_rang_cod =$rang";
                $stmt   = $pdo->query($req);
                $result = $stmt->fetch();
                if ($vperso == $perso_cod)
                {
                    if ($result['rguilde_admin'] != 'O' and $nombre_admin <= 1)
                    {
                        echo "<p>Erreur ! Le nouveau rang n'est pas un rang <strong>administrateur</strong>, alors que vous êtes actuellement <strong>le seul administrateur</strong> de cette guilde !";
                    } else if ($result['rguilde_admin'] != 'O' and $nombre_admin > 1)
                    {
                        $req  = "update guilde_perso set pguilde_rang_cod = $rang
														where pguilde_perso_cod = $vperso ";
                        $stmt = $pdo->query($req);
                        echo "<p>Le rang de l'utilisateur à été modifié. Vous étiez <strong>administrateur</strong> de cette guilde, et vous êtes redevenu simple <strong>membre</strong>.";
                    } else /* Le nouveau rang est un rang admin */
                    {
                        $req  = "update guilde_perso set pguilde_rang_cod = $rang
														where pguilde_perso_cod = $vperso ";
                        $stmt = $pdo->query($req);
                        echo "<p>Le rang de l'utilisateur à été modifié.";
                    }
                } else
                {
                    if ($result['rguilde_admin'] != 'O')
                    {
                        echo "<p>Erreur ! Le nouveau rang n'est pas un rang <strong>administrateur</strong>. Vous ne pouvez pas dégrader un administrateur, seul lui peut se changer de rang !";
                    } else
                    {
                        $req  = "update guilde_perso set pguilde_rang_cod = $rang
														where pguilde_perso_cod = $vperso ";
                        $stmt = $pdo->query($req);
                        echo "<p>Le rang de l'utilisateur à été modifié.";
                    }
                }
            } else
            {
                $req  = "update guilde_perso set pguilde_rang_cod = $rang
												where pguilde_perso_cod = $vperso ";
                $stmt = $pdo->query($req);
                echo "<p>Le rang de l'utilisateur à été modifié.";
            }
            break;
    }
} else
{
    echo "<p>Vous n'êtes pas un administrateur de guilde !";
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
