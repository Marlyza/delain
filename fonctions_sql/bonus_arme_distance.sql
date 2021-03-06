--
-- Name: bonus_arme_distance(integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function bonus_arme_distance(integer) RETURNS integer
LANGUAGE plpgsql
AS $_$/**********************************************************/
/* fonction bonus_arme_distance : donne le bonus          */
/*   pour utiliser une arme à distance                    */
/* on passe en paramètres :                               */
/*   $1 = perso_cod                                       */
/* on a en retour un entier                               */
/**********************************************************/
declare
  code_retour integer;
  personnage alias for $1;
  dext integer;
begin
  select into dext perso_dex from perso
  where perso_cod = personnage;
  code_retour := (dext - 11)*3;
  return code_retour;
end;
$_$;


ALTER FUNCTION public.bonus_arme_distance(integer) OWNER TO delain;
