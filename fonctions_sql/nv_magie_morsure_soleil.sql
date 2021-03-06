--
-- Name: nv_magie_morsure_soleil(integer, integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION nv_magie_morsure_soleil(integer, integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*****************************************************************/
/* function magie_morsure_soleil : lance le sort morsure du sol. */
/* On passe en paramètres                                        */
/*   $1 = lanceur                                                */
/*   $2 = cible                                                  */
/*   $3 = type lancer                                            */
/*        0 = rune                                               */
/*        1 = mémo                                               */
/* Le code sortie est une chaine html utilisable directement     */
/*****************************************************************/
/* Créé le 20/07/2003                                            */
/* Liste des modifications :                                     */
/*   08/09/2003 : ajout d un tag pour amélioration auto          */
/*   29/01/2004 : modif du type code sortie                      */
/*   10/09/2018 : Marlyza gestion de la valeur du malus          */
/*****************************************************************/
declare
-------------------------------------------------------------
-- variables servant pour la sortie
-------------------------------------------------------------
	code_retour text;				-- chaine html de sortie
	texte_evt text;				-- texte pour évènements
	nom_sort text;					-- nom du sort
-------------------------------------------------------------
-- variables concernant le lanceur
-------------------------------------------------------------
	lanceur alias for $1;		-- perso_cod du lanceur
	v_perso_niveau integer;		-- niveau du lanceur
-------------------------------------------------------------
-- variables concernant la cible
-------------------------------------------------------------
	cible alias for $2;			-- perso_cod de la cible
	nom_cible text;				-- nom de la cible
-------------------------------------------------------------
-- variables concernant le sort
-------------------------------------------------------------
	num_sort integer;				-- numéro du sort à lancer
	type_lancer alias for $3;	-- type de lancer (memo ou rune)
	cout_pa integer;				-- Cout en PA du sort
	px_gagne text;				-- PX gagnes
	v_bonus_toucher integer;	-- bonus toucher
-------------------------------------------------------------
-- variables de contrôle
-------------------------------------------------------------
	magie_commun_txt text;		-- texte pour magie commun
	res_commun integer;			-- partie 1 du commun
	distance_cibles integer;	-- distance entre lanceur et cible
	ligne_rune record;			-- record des rune à dropper
	temp_ameliore_competence text;
										-- chaine temporaire pour amélioration
	v_bloque_magie integer;		-- vérif si résistance magique
-------------------------------------------------------------
-- variables de calcul
-------------------------------------------------------------
	des integer;					-- lancer de dés
	compt integer;					-- fourre tout
begin
-------------------------------------------------------------
-- Etape 1 : intialisation des variables
-------------------------------------------------------------
-- on renseigne d abord le numéro du sort
	num_sort := 35;
-- les px
	px_gagne := 0;
-------------------------------------------------------------
-- Etape 2 : contrôles
-------------------------------------------------------------
	select into nom_cible perso_nom from perso
		where perso_cod = cible;
	select into nom_sort sort_nom from sorts
		where sort_cod = num_sort;
	magie_commun_txt := magie_commun(lanceur,cible,type_lancer,num_sort);
	res_commun := split_part(magie_commun_txt,';',1);
	if res_commun = 0 then
		code_retour := split_part(magie_commun_txt,';',2);
		return code_retour;
	end if;
	code_retour := split_part(magie_commun_txt,';',3);
	px_gagne := split_part(magie_commun_txt,';',4);

	v_bloque_magie := split_part(magie_commun_txt,';',2);
	select into v_perso_niveau perso_niveau from perso
		where perso_cod = lanceur;
	v_bonus_toucher := 3*v_perso_niveau;

	if v_bloque_magie = 0 then
------------------------
-- magie non résistée --
------------------------
		v_bonus_toucher := -10;
		code_retour := code_retour||'Votre adversaire n''arrive pas à résister au sort.<br>';
	else
--------------------
-- magie résistée --
--------------------
		v_bonus_toucher := -4;
		code_retour := code_retour||'Votre adversaire résiste partiellement au sort.<br>';
	end if;
	perform ajoute_bonus(cible, 'VUE', 4, v_bonus_toucher);
        -- pour le moment, la valeur du bonus de DESorientation est binaire 0 : pas d'effet, 1 : désorienté
        -- Marlyza - 2018-09-10 : depuis l'ouverture du marais la valeur est modulée 0: pas d'effet, 1:33% de raté, 2: 66% de raté et 3+: 100% de raté
        -- Pour ne pas modifier le comportement de la morsure, le malus est mis à la valeur 100, soit toujours 100% de raté.
        -- Marlyza - 2020-05-10 : avec l'arrivé des bonus d'équipement, la désorientation passe en procentage, ici 100 pour 100% de raté
        perform ajoute_bonus(cible, 'DES', 3, 100);

	code_retour := code_retour||'<br>'||nom_cible||' a un malus à la vue de '||trim(to_char(v_bonus_toucher,'99'))||' pendant 4 tours.';
	code_retour := code_retour||'<br>Vous gagnez '||px_gagne||' PX pour cette action.<br>';
	texte_evt := '[attaquant] a lancé '||nom_sort||' sur [cible], ';
   if v_bloque_magie != 0 then
   	texte_evt := texte_evt||'qui a partiellement résisté au sort.';
   end if;
   insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     	values(nextval('seq_levt_cod'),14,now(),1,lanceur,texte_evt,'O','O',lanceur,cible);
   if (lanceur != cible) then
    insert into ligne_evt(levt_cod,levt_tevt_cod,levt_date,levt_type_per1,levt_perso_cod1,levt_texte,levt_lu,levt_visible,levt_attaquant,levt_cible)
     	values(nextval('seq_levt_cod'),14,now(),1,cible,texte_evt,'N','O',lanceur,cible);
   end if;

  ---------------------------
  -- les EA liés au lancement d'un sort et ciblé par un sort (avec protagoniste) #EA#
  ---------------------------
  code_retour := code_retour || execute_fonctions(lanceur, cible, 'MAL', json_build_object('num_sort', num_sort)) || execute_fonctions(cible, lanceur, 'MAC', json_build_object('num_sort', num_sort)) ;

	return code_retour;
end;
$_$;


ALTER FUNCTION public.nv_magie_morsure_soleil(integer, integer, integer) OWNER TO delain;