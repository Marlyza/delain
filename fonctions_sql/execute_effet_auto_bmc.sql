--
-- Name: execute_effet_auto_bmc(integer, text, numeric, numeric); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION execute_effet_auto_bmc(integer, text, numeric, numeric) RETURNS text
    LANGUAGE plpgsql
    AS $_$/*************************************************************/
/* fonction execute_effet_auto_bmc                                */
/*   Exécute les fonctions spécifiques liées à un monstre    */
/*    et/ou un personnage                                    */
/*   on passe en paramètres :                                */
/*   $1 = perso_cod : le perso_cod de la source              */
/*   $2 = type_bonus : le bonus qui a changé                 */
/*   $3 = bonus_vaant : le bonus avant changement            */
/*   $4 = bonus_apres : le bonus apres changement            */
/* on a en sortie les sorties concaténées des fonctions.     */
/*************************************************************/
/* Créé le 19/05/2014                                        */
/*************************************************************/
declare
  v_perso_cod    alias for $1;  -- Le code du perso en question
  v_bonus        alias for $2;  -- Le type de bonus
	v_valeur_avant alias for $3;  -- BM avant l'ajout
	v_valeur_apres alias for $4;  -- BM après l'ajout

	code_retour text;          -- Le retour de la fonction
	retour_fonction text;      -- Le résultat de l’exécution d’une fonction
	ligne_fonction record;     -- Les données de la fonction
	code_fonction text;        -- Le code SQL lançant la fonction
	v_gmon_cod integer;        -- Le code du monstre générique
	v_gmon_nom text;           -- Le nom du monstre générique
	v_perso_nom text;          -- Nom du perso avan modification
	v_nom text;                -- Racine du nom du monstre (ie sans le N°)
	v_raz text;                -- Raz du compteur à réaliser?.

begin

	code_retour := '';    -- code de retour
  v_raz := 'N';         -- pas de RAZ du compteur par défaut

  -- Eventuellement les fonction du monstre générique
	select into v_gmon_cod, v_gmon_nom, v_perso_nom perso_gmon_cod, gmon_nom, perso_nom from perso inner join monstre_generique on gmon_cod=perso_gmon_cod where perso_cod = v_perso_cod;
	if not found then
      v_gmon_cod:= null ;
      v_gmon_nom:= null ;
	end if;

  -- boucle sur toutes les fonctions specifiques de l'évenement pour le perso et le bonus qui vérifie le passage du seuil
	for ligne_fonction in (
      select fonc_cod, fonc_nom, trim(fonc_trigger_param->>'fonc_trig_raz'::text) as fonc_trig_raz, fonc_trigger_param->>'fonc_trig_nom'::text as fonc_trig_nom
      from fonction_specifique
      where (fonc_gmon_cod = coalesce(v_gmon_cod, -1) OR (fonc_perso_cod = v_perso_cod) OR (fonc_gmon_cod is null and fonc_perso_cod is null))
            and (fonc_type='BMC')
            and (fonc_trigger_param->>'fonc_trig_compteur'::text = v_bonus)
            and (
                (fonc_trigger_param->>'fonc_trig_sens'::text = '1' and v_valeur_avant<(fonc_trigger_param->>'fonc_trig_seuil'::text)::numeric and v_valeur_apres>=(fonc_trigger_param->>'fonc_trig_seuil'::text)::numeric)
              or
                (fonc_trigger_param->>'fonc_trig_sens'::text = '-1' and v_valeur_avant>(fonc_trigger_param->>'fonc_trig_seuil'::text)::numeric and v_valeur_apres<=(fonc_trigger_param->>'fonc_trig_seuil'::text)::numeric)
                )
            and (fonc_date_limite >= now() OR fonc_date_limite IS NULL)
      order by (fonc_trigger_param->>'fonc_trig_seuil'::text)::numeric, fonc_cod desc
		)
	loop
      -- changement de nom du perso (si monstre generique)
      if (coalesce(ligne_fonction.fonc_trig_nom, '') != '') and (v_gmon_nom is not null) then

          v_nom:= substr(v_perso_nom, 1, COALESCE(NULLIF(strpos(v_perso_nom, ' (n°')-1,-1), char_length(v_perso_nom))) ;
          update perso set perso_nom = replace(replace(ligne_fonction.fonc_trig_nom,'[nom_generique]',v_gmon_nom), '[nom]', v_nom) ||' (n° '||trim(to_char(perso_cod,'99999999'))||')' where perso_cod=v_perso_cod;
      end if;

      if ligne_fonction.fonc_trig_raz = 'O' then
          v_raz := 'O' ;
      end if;

      code_fonction := ligne_fonction.fonc_nom;
      retour_fonction := execute_fonction_specifique(v_perso_cod, v_perso_cod, ligne_fonction.fonc_cod) ;

      if coalesce(retour_fonction, '') != '' then
        -- code_retour := code_retour || code_fonction || ' : ' || coalesce(retour_fonction, '') || '<br />';
        code_retour := code_retour || coalesce(retour_fonction, '') || '<br />';
      end if;
	end loop;

  -- traitement du raz (s'il y en a et seulement pour les compteurs)
  if  v_raz = 'O' then
      -- seulement pour les bonus du type compteur (et pas les bonus equipement)
      select tbonus_compteur into v_raz from bonus_type where tbonus_libc = v_bonus and tbonus_compteur='O' ;
      if  v_raz = 'O' then
          delete from bonus where bonus_perso_cod = v_perso_cod and  bonus_mode != 'E' and  bonus_tbonus_libc = v_bonus ;
      end if;
  end if;


	if code_retour != '' then
		  code_retour := replace('<br /><b>Effets automatiques :</b><br />' || code_retour, '<br /><br />', '<br />') || '<br />';
	end if;

	return code_retour;
end;$_$;


ALTER FUNCTION public.execute_effet_auto_bmc(integer, text, numeric, numeric) OWNER TO delain;

--
-- Name: FUNCTION execute_effet_auto_bmc(integer, text, numeric, numeric); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION execute_effet_auto_bmc(integer, text, numeric, numeric) IS 'Exécute les fonctions liées au changement de BM du perso_cod donné.';