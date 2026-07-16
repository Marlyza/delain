CREATE OR REPLACE FUNCTION comp_spe_attaque_monstre(integer, integer) RETURNS void
LANGUAGE plpgsql
AS $_$
declare
v_attaquant alias for $1;
v_cible alias for $2;
num_comp_spe integer;
v_type_attaque integer;
chance integer;
nombre_util integer;
des integer;
v_pa integer;
compt_loop integer;
temp_txt text;
begin
    select into num_comp_spe, chance, nombre_util
            gmoncomp_comp_cod, gmoncomp_chance, comp_nb_util_tour
        from monstre_generique_comp, perso, competences
        where perso_gmon_cod = gmoncomp_gmon_cod
          and perso_cod = v_attaquant
          and gmoncomp_comp_cod in (25,61,62,63,64,65,66,67,68,72,73,74,75,76,77)
          and gmoncomp_comp_cod = comp_cod
        order by random()
            limit 1;

    if not found then
        return;
    end if;

    v_type_attaque := case num_comp_spe
        when 25 then 1    -- AF lvl 1
        when 61 then 2    -- AF lvl 2
        when 62 then 3    -- AF lvl 3
        when 63 then 4    -- Feinte lvl 1
        when 64 then 5    -- Feinte lvl 2
        when 65 then 6    -- Feinte lvl 3
        when 66 then 7    -- Coup de grâce lvl 1
        when 67 then 8    -- Coup de grâce lvl 2
        when 68 then 9    -- Coup de grâce lvl 3
        when 72 then 10   -- Bout portant lvl 1
        when 73 then 11   -- Bout portant lvl 2
        when 74 then 12   -- Bout portant lvl 3
        when 75 then 13   -- Tir précis lvl 1
        when 76 then 14   -- Tir précis lvl 2
        when 77 then 15   -- Tir précis lvl 3
    end;

    des := lancer_des(1,100);
    if des > chance then
        return;
    end if;

    select into v_pa perso_pa from perso where perso_cod = v_attaquant;

    -- même compétence relancée, plafonnée par comp_nb_util_tour (typiquement 2)
    compt_loop := 0;
    while v_pa >= getparm_n(9) and compt_loop < nombre_util loop
        compt_loop := compt_loop + 1;
        temp_txt := attaque(v_attaquant, v_cible, v_type_attaque);
        select into v_pa perso_pa from perso where perso_cod = v_attaquant;
    end loop;
end;
$_$;

ALTER FUNCTION public.comp_spe_attaque_monstre(integer, integer) OWNER TO delain;