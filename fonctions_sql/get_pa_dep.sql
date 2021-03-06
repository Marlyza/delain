--
-- Name: get_pa_dep(integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE OR REPLACE FUNCTION public.get_pa_dep(integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$/*****************************************************/
/* fonction get_pa_dep : retourne le nombre de PA    */
/*   nécessaire pour un déplacement                  */
/* on passe en paramètre :                           */
/*   $1 = perso_cod                                  */
/*****************************************************/
declare
	code_retour integer;
	personnage alias for $1;
	v_bonus integer;
	v_etage integer;
	v_type_perso integer;
	v_pos_modif_pa_dep integer;
	v_monture integer;
	v_monture_pa integer;
  v_ter_cod integer; -- terrain specifique
  v_gmon_cod integer;  -- code du monstre generique pour une monture

begin

  -- si le perso se deplace avec une monture on prend les PA nécéssaires pour la monture !
  select m.perso_cod into v_monture
      from perso as p
      join perso as m on m.perso_cod=p.perso_monture and m.perso_actif = 'O' and m.perso_type_perso=2
      where p.perso_cod=personnage and p.perso_type_perso=1 ;
  if found then
  	  code_retour := get_pa_dep(v_monture);
		  return code_retour;
	end if;

  -- cas d'un perso sans monture ou d'un monstre (monture ou non)
	code_retour := getparm_n(9) + valeur_bonus(personnage, 'DEP');
	select into v_etage,v_pos_modif_pa_dep
		pos_etage,pos_modif_pa_dep
		from positions,perso_position
		where ppos_perso_cod = personnage
		and ppos_pos_cod = pos_cod;

  select perso_type_perso into v_type_perso from perso where perso_cod = personnage;

	-- cas de l'étage de l'araigné, un malus de deplacement sur tous l'étage pour les joueurs
	if v_etage = 16 and v_type_perso != 2 then
			code_retour := code_retour + 2;
	end if;
	code_retour := code_retour + v_pos_modif_pa_dep;

	-- cas particulier d'un objet qui donne un malus !
	if exists (select 1 from perso_objets, objets where perobj_obj_cod = obj_cod and perobj_perso_cod = personnage and obj_gobj_cod = 860 and perobj_equipe = 'O') then
		-- Attelle. Malus de 1 au déplacement
		code_retour := code_retour + 1;
	end if;

  -- récupération du type de terrain pour un monstre avec des capacités de terrain
    select coalesce(pos_ter_cod,0), perso_gmon_cod into v_ter_cod, v_gmon_cod  from perso
      join perso_position on ppos_perso_cod=perso_cod
      join positions on pos_cod=ppos_pos_cod
      where perso_cod=personnage and perso_type_perso=2 limit 1;
  if found then
      -- cas d'un mosntre sur un terrain pécifique, on recarge les capacité de la monture sur ce terrain
      select tmon_terrain_pa into v_monture_pa from monstre_terrain where tmon_gmon_cod = v_gmon_cod and tmon_ter_cod=v_ter_cod limit 1;
      if found then
           code_retour := code_retour + v_monture_pa ;
      else
          -- si auncun capacité de terrain n'a pas été trouvé on regarde s'il y a une condition sur "autre trerrain v_ter_cod = -1 !
          select tmon_terrain_pa into v_monture_pa from monstre_terrain where tmon_gmon_cod = v_gmon_cod and tmon_ter_cod=-1 limit 1;
          if found then
               code_retour := code_retour + v_monture_pa ;
          end if;
      end if;
  end if;

  -- Marlyza le 14/01/2021 : modification du seuil minimal (en accord avec phenix et pnarcade)
  -- seuil minimum de 1 pour les déplacements!
	if code_retour < 1 then
		code_retour := 1;
	end if;

	-- Marlyza le 16/03/2021 : modification du seuil minimal (sauf monstre) 2PA si terrain standard/malus et 1PA si terrains à bonus
	if code_retour=1 and v_pos_modif_pa_dep>=0 and v_type_perso != 2  then
	  code_retour := 2;
	end if;

	-- fin de traitement, retourner le nombre de PA
	return code_retour;
end;
	$_$;


ALTER FUNCTION public.get_pa_dep(integer) OWNER TO delain;