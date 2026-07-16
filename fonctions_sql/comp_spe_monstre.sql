--
-- Name: comp_spe_monstre(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

CREATE or replace FUNCTION comp_spe_monstre(integer, integer) RETURNS void
LANGUAGE plpgsql
AS $_$

declare
  --------------------------------------------------------------------------------
  -- renseignements de l attaquant
  --------------------------------------------------------------------------------
  v_attaquant alias for $1;	-- monstre attaquant
  v_cible alias for $2;		-- cible du monstre

begin

    -- attaques spéciales autonomes (balayage, garde manger, hydre, jeu de trolls)
    perform attaque_spe_monstre(v_attaquant, v_cible);

    -- modificateur d'attaque normale (feinte, coup de grâce, tir précis, etc.)
    perform comp_spe_attaque_monstre(v_attaquant, v_cible);

end;$_$;


ALTER FUNCTION public.comp_spe_monstre(integer, integer) OWNER TO delain;

--
-- Name: FUNCTION comp_spe_monstre(integer, integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION comp_spe_monstre(integer, integer) IS 'Fonction appelée par les IA pour déclencher les compétences spéciales des monstres';