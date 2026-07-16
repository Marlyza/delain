CREATE OR REPLACE FUNCTION public.attaque_spe_monstre(integer, integer) RETURNS text
LANGUAGE plpgsql
AS $_$
declare
v_attaquant alias for $1;
v_cible alias for $2;
code_retour text;
temp_txt text;
num_comp_spe integer;
v_type_attaque integer;
v_melee boolean;
chance integer;
v_comp_nb_util_tour integer;
nombre_util integer;
des integer;
v_pa integer;
compt_loop integer;
pos_attaquant integer;
nb_cibles_case integer;
begin

    code_retour := '';

    select into num_comp_spe, chance, v_comp_nb_util_tour
        gmoncomp_comp_cod, gmoncomp_chance, comp_nb_util_tour
    from monstre_generique_comp, perso, competences
    where perso_gmon_cod = gmoncomp_gmon_cod
      and perso_cod = v_attaquant
      and gmoncomp_comp_cod in (89,94,95,96)
      and gmoncomp_comp_cod = comp_cod
    order by random()
        limit 1;

    if not found then
        return 'Pas de compétence d''attaque spéciale disponible pour ce monstre.';
    end if;

    v_type_attaque := case num_comp_spe
        when 89 then 16   -- balayage        -> mêlée de zone
        when 94 then 17   -- garde manger    -> mono-cible, éloigne la cible
        when 95 then 18   -- hydre à neuf têtes -> mêlée de zone
        when 96 then 19   -- jeu de trolls   -> mono-cible, éloigne la cible
    end;

    v_melee := v_type_attaque in (16, 18);

    if v_melee then
        nombre_util := least(2, v_comp_nb_util_tour);
    else
        nombre_util := least(1, v_comp_nb_util_tour);
    end if;

    if v_melee then
        select into pos_attaquant ppos_pos_cod
            from perso_position
            where ppos_perso_cod = v_attaquant;

        select into nb_cibles_case count(perso_cod)
        from perso, perso_position
        where ppos_perso_cod = perso_cod
          and ppos_pos_cod = pos_attaquant
          and perso_cod != v_attaquant
          and perso_actif = 'O'
          and perso_tangible = 'O'
          and perso_type_perso in (1,3)
          and not exists (
            select 1 from lieu, lieu_position
            where lpos_pos_cod = ppos_pos_cod
          and lpos_lieu_cod = lieu_cod
          and lieu_refuge = 'O'
            );

        if nb_cibles_case < 2 then
              return 'Moins de 2 cibles sur la case, attaque de mêlée spéciale non tentée (PA conservés).';
        end if;
    end if;

    des := lancer_des(1,100);
    select into v_pa perso_pa from perso where perso_cod = v_attaquant;

    if des > chance or v_pa < 6 then
        return 'Attaque spéciale non déclenchée (jet raté ou PA insuffisants).';
    end if;

    compt_loop := 0;
    while v_pa >= 6 and compt_loop < nombre_util loop
        compt_loop := compt_loop + 1;
        temp_txt := attaque_spe(v_attaquant, v_cible, v_type_attaque);
        code_retour := code_retour || temp_txt;
        select into v_pa perso_pa from perso where perso_cod = v_attaquant;
    end loop;

    return code_retour;
end;
$_$;

ALTER FUNCTION public.attaque_spe_monstre(integer, integer) OWNER TO delain;