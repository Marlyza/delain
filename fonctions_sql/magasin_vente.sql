-- Function: public.magasin_vente(integer, integer, integer)

-- DROP FUNCTION public.magasin_vente(integer, integer, integer);

CREATE OR REPLACE FUNCTION public.magasin_vente(
    integer,
    integer,
    integer)
  RETURNS text AS
$BODY$/**************************************************************************/
/* fonction magasin_vente : gère la vente dans un magasin par un joueur   */
/* On passe en paramètres :                                               */
/*   $1 = perso_cod du joueur                                             */
/*   $2 = lieu_cod du magasin                                             */
/*   $3 = obj_cod de l'objet acheté                                       */
/* On a retour une chaine HTML exploitable par action.php                 */
/**************************************************************************/
/* Créé le 08/03/2004                                                     */
/* Modifié le 10/09/2004 par SD : prise en compte d'un paramètre pour     */
/*   le taux de rachat.                                                   */
/* modifié le 05/07/2006 par SD : prise en compte du obj_deposable        */
/**************************************************************************/
declare
----------------------------------------------------------------------------
-- variable de retour
----------------------------------------------------------------------------
	code_retour text;					-- texte formatté
----------------------------------------------------------------------------
-- variable concernant le joueur
----------------------------------------------------------------------------
	personnage alias for $1;		-- perso_cod du joueur
	qte_or integer;					-- nb de brouzoufs du joueur
	pos_joueur integer;
	karma_perso numeric;			-- karma du joueur

----------------------------------------------------------------------------
-- variable concernant le magasin
----------------------------------------------------------------------------
	magasin alias for $2;			-- lieu_cod du magasin
	pos_magasin integer;
	montant_marge integer;			-- marge encaissée par le magasin
	montant_prelev integer;			-- montant reversé à l'administration
	prix_origine integer;			-- prix sans marge
	marge integer;						-- marge du magasin
	prelev integer;					-- % de reversement
	type_magasin integer;			-- type de magasin (runique ou normal)
	taux_rachat numeric;				-- taux de rachat
----------------------------------------------------------------------------
-- variable concernant l'objet
----------------------------------------------------------------------------
	num_objet alias for $3;			-- obj_cod de l'objet acheté
	prix_objet integer;				-- prix de l'objet
	prix_init integer;				-- prix de l'objet hors modificateur
	nom_objet text;					-- nom de l'objet
	v_etat numeric;					-- etat de l'objet
	v_deposable text;
----------------------------------------------------------------------------
-- variable de calcul
----------------------------------------------------------------------------
	temp integer;						-- fourre tout
	modificateur numeric;			-- modif du prix de vente
	bonus_prix integer;
begin
----------------------------------------------------------------------------
-- Etape 1 : vérification
----------------------------------------------------------------------------
	select into qte_or,karma_perso perso_po,perso_kharma
		from perso
		where perso_cod = personnage;
	if not found then
		code_retour := '<p>Erreur ! Acheteur non trouvé !';
		return code_retour;
	end if;
	select into pos_joueur
		ppos_pos_cod
		from perso_position
		where ppos_perso_cod = personnage;
	if not found then
		code_retour := '<p>Erreur ! Position acheteur non trouvé !';
		return code_retour;
	end if;

	select into temp,marge,prelev,type_magasin lieu_cod,lieu_marge,lieu_prelev,lieu_tlieu_cod
		from lieu
		where lieu_cod = magasin;
	if not found then
		code_retour := '<p>Erreur ! Magasin non trouvé !';
		return code_retour;
	end if;
	select into temp,v_etat,v_deposable
		perobj_cod,obj_etat,obj_deposable
		from perso_objets,objets
		where perobj_perso_cod = personnage
		and obj_cod = num_objet
		and perobj_obj_cod = num_objet;
	if not found then
		code_retour := '<p>Erreur ! L''objet n''est pas dans votre inventaire !';
		return code_retour;
	end if;
	if v_deposable = 'N' then
		code_retour := '<p>Erreur ! il n''est pas possible de vendre cet objet !';
		return code_retour;
	end if;

	select into nom_objet obj_nom
		from objets
		where obj_cod = num_objet;

	prix_objet := f_prix_obj_perso_v(personnage,magasin,num_objet);
----------------------------------------------------------------------------
-- Etape 2 : on passe à la vente
----------------------------------------------------------------------------
-- on stocke
	insert into mag_tran
		(mtra_lieu_cod,mtra_perso_cod,mtra_obj_cod,mtra_sens,mtra_montant)
		values
		(magasin,personnage,num_objet,0,prix_objet);
-- on ajoute les brouzoufs
	update perso
		set perso_po = perso_po + prix_objet
		where perso_cod = personnage;
-- on passe aux marges
	prix_origine = (prix_objet*100)/(100 + marge);
	montant_marge = round((prix_objet * marge)/100);
	montant_prelev = round((prix_objet * prelev) / 100);
-- on met la marge au magasin
	update lieu set lieu_compte = lieu_compte + montant_marge where lieu_cod = magasin;
	update lieu set lieu_compte = lieu_compte - montant_prelev where lieu_cod = magasin;
	update parametres set parm_valeur = parm_valeur + montant_prelev where parm_cod = 39;
-- on ajoute dans l'incentaire du magasin
	insert into stock_magasin
		(mstock_obj_cod,mstock_lieu_cod)
		values
		(num_objet,magasin);
-- on enlève dans l'inventaire
	delete from perso_objets
		where perobj_obj_cod = num_objet;
-- on remet l'objet à neuf
	update objets set obj_etat = 100
		where obj_cod = num_objet;

-- on rajoute une transaction dans le total
	select into temp pvmag_cod
		from perso_visite_magasin
		where pvmag_perso_cod = personnage
		and pvmag_lieu_cod = magasin;

	if not found then
		insert into perso_visite_magasin
			(pvmag_perso_cod,pvmag_lieu_cod,pvmag_nombre)
			values
			(personnage,magasin,(prix_objet/5000)::numeric);
	else
		update perso_visite_magasin
			set pvmag_nombre = pvmag_nombre + (prix_objet/5000)::numeric
			where pvmag_perso_cod = personnage
			and pvmag_lieu_cod = magasin;
	end if;
-- on modifie l'alignement
	if karma_perso < 0 then
		update lieu
		set lieu_alignement = lieu_alignement - 1
		where lieu_cod = magasin;
	else
		update lieu
		set lieu_alignement = lieu_alignement + 1
		where lieu_cod = magasin;
	end if;
	code_retour := '<p>Vous avez vendu l''objet '||nom_objet||' pour la somme de '||trim(to_char(prix_objet,'99999999999'))||' brouzoufs.';
	return code_retour;
end;



	$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION public.magasin_vente(integer, integer, integer)
  OWNER TO delain;
GRANT EXECUTE ON FUNCTION public.magasin_vente(integer, integer, integer) TO delain;
GRANT EXECUTE ON FUNCTION public.magasin_vente(integer, integer, integer) TO public;
