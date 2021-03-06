--
-- Name: change_num_perso(integer, integer); Type: FUNCTION; Schema: public; Owner: delain
--

create or replace function change_num_perso(integer, integer) RETURNS text
    LANGUAGE plpgsql
    AS $_$-- Change le numero de perso de $1 vers $2

declare
  num_orig alias for $1;
  num_new alias for $2;
  temp integer;
begin
  select into temp 1 from perso where perso_cod = $2;
  if found then
    return 'Choisissez un nouveau numero de perso non attribué !!';
  end if;
  update perso set perso_cod = $2 where perso_cod = $1;
  update action set act_perso1 = $2 where act_perso1 = $1;
  update action set act_perso2 = $2 where act_perso2 = $1;
  update action_monstre set act_perso_cod = $2 where act_perso_cod = $1;
  update bonbons set bonbon_donneur = $2 where bonbon_donneur = $1;
  update bonbons set bonbon_receveur = $2 where bonbon_receveur = $1;
  update bonus set bonus_perso_cod = $2 where bonus_perso_cod = $1;
  update cachettes_perso set persocache_perso_cod = $2 where persocache_perso_cod = $1;
  update carac_orig set corig_perso_cod = $2 where corig_perso_cod = $1;
  update compt_halloween set cpt_tueur = $2 where cpt_tueur = $1;
  update compt_noel set cn_perso_cod = $2 where cn_perso_cod = $1;
  update comptes_temp set compt_temp_perso_cod = $2 where compt_temp_perso_cod = $1;
  update concentrations set concentration_perso_cod = $2 where concentration_perso_cod = $1;
  update contact set contact_perso_cod = $2 where contact_perso_cod = $1;
  update contact_liste set cliste_perso_cod = $2 where cliste_perso_cod = $1;
  update dieu_perso set dper_perso_cod = $2 where dper_perso_cod = $1;
  update dieu_renegat set dren_perso_cod = $2 where dren_perso_cod = $1;
  update envois_mail set menv_perso_cod = $2 where menv_perso_cod = $1;
  update etage_visite set vet_perso_cod = $2 where vet_perso_cod = $1;
  update flicage set flic_perso = $2 where flic_perso = $1;
--  update formule_livraison_lieu set fll_perso_cod = $2 where fll_perso_cod = $1;
  update groupe set groupe_chef = $2 where groupe_chef = $1;
  update groupe_perso set pgroupe_perso_cod = $2 where pgroupe_perso_cod = $1;
  update guilde_banque_transactions set gbank_tran_perso_cod = $2 where gbank_tran_perso_cod = $1;
  update guilde_perso set pguilde_perso_cod = $2 where pguilde_perso_cod = $1;
  update guilde_revolution set revguilde_lanceur = $2 where revguilde_lanceur = $1;
  update guilde_revolution set revguilde_cible = $2 where revguilde_cible = $1;
  update guilde_revolution_vote set vrevguilde_perso_cod = $2 where vrevguilde_perso_cod = $1;
  update journal set journal_perso_cod = $2 where journal_perso_cod = $1;
  update ligne_evt set levt_perso_cod1 = $2 where levt_perso_cod1 = $1;
  update ligne_evt set levt_perso_cod2 = $2 where levt_perso_cod2 = $1;
  update ligne_evt set levt_attaquant = $2 where levt_attaquant = $1;
  update ligne_evt set levt_cible = $2 where levt_cible = $1;
  update lock_combat set lock_attaquant = $2 where lock_attaquant = $1;
  update lock_combat set lock_cible = $2 where lock_cible = $1;
  update log_objet set llobj_perso_cod = $2 where llobj_perso_cod = $1;
  update log_objet set lobj_dest_tran = $2 where lobj_dest_tran = $1;
  update logs_ia set lia_perso_cod = $2 where lia_perso_cod = $1;
  update mag_tran set mtra_perso_cod = $2 where mtra_perso_cod = $1;
  update mag_tran_generique set mgtra_perso_cod = $2 where mgtra_perso_cod = $1;
  update magasin_gerant set mger_perso_cod = $2 where mger_perso_cod = $1;
  update messages_dest set dmsg_perso_cod = $2 where dmsg_perso_cod = $1;
  update messages_exp set emsg_perso_cod = $2 where emsg_perso_cod = $1;
  update peine set peine_magistrat = $2 where peine_magistrat = $1;
  update peine set peine_perso_cod = $2 where peine_perso_cod = $1;
  update perso2 set perso_cod = $2 where perso_cod = $1;
  update perso_auberge set paub_perso_cod = $2 where paub_perso_cod = $1;
  update perso_banque set pbank_perso_cod = $2 where pbank_perso_cod = $1;
  update perso_commandement set perso_subalterne_cod = $2 where perso_subalterne_cod = $1;
  update perso_commandement set perso_superieur_cod = $2 where perso_superieur_cod = $1;
  update perso_competences set pcomp_perso_cod = $2 where pcomp_perso_cod = $1;
  update perso_compte set pcompt_perso_cod = $2 where pcompt_perso_cod = $1;
  update perso_concours_barde set pcb_perso_cod = $2 where pcb_perso_cod = $1;
  update perso_familier set pfam_perso_cod = $2 where pfam_perso_cod = $1;
  update perso_familier set pfam_familier_cod = $2 where pfam_familier_cod = $1;
  update perso_formule set pfrm_perso_cod = $2 where pfrm_perso_cod = $1;
  update perso_grand_escalier set pge_perso_cod = $2 where pge_perso_cod = $1;
  update perso_ia set pia_perso_cod = $2 where pia_perso_cod = $1;
  update perso_identifie_objet set pio_perso_cod = $2 where pio_perso_cod = $1;
  update perso_louche set plouche_perso_cod = $2 where plouche_perso_cod = $1;
  update perso_nb_comp set pnb_perso_cod = $2 where pnb_perso_cod = $1;
  update perso_nb_sorts set pnbs_perso_cod = $2 where pnbs_perso_cod = $1;
  update perso_nb_sorts_total set pnbst_perso_cod = $2 where pnbst_perso_cod = $1;
  update perso_objets set perobj_perso_cod = $2 where perobj_perso_cod = $1;
  update perso_pochette set ppoch_perso_cod = $2 where ppoch_perso_cod = $1;
  update perso_position set ppos_perso_cod = $2 where ppos_perso_cod = $1;
  update perso_sorts set psort_perso_cod = $2 where psort_perso_cod = $1;
  update perso_tableau_chasse set ptab_perso_cod = $2 where ptab_perso_cod = $1;
  update perso_temple set ptemple_perso_cod = $2 where ptemple_perso_cod = $1;
  update perso_titre set ptitre_perso_cod = $2 where ptitre_perso_cod = $1;
  update perso_visite_magasin set pvmag_perso_cod = $2 where pvmag_perso_cod = $1;
  update perso_vue_pos_1 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_2 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_3 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_4 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_5 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_6 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_7 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_8 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_9 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_10 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_11 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_12 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_13 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_14 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_15 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_16 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_17 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_18 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_19 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_20 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_21 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_22 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_23 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_24 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_25 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_27 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_28 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_31 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_32 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_33 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_34 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_35 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_36 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_37 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_38 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_39 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_40 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_41 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_42 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_43 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_44 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_45 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_46 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_47 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update perso_vue_pos_48 set pvue_perso_cod = $2 where pvue_perso_cod = $1;
  update quete_perso set pquete_perso_cod = $2 where pquete_perso_cod = $1;
  update recsort set recsort_perso_cod = $2 where recsort_perso_cod = $1;
  update riposte set riposte_attaquant = $2 where riposte_attaquant = $1;
  update riposte set riposte_cible = $2 where riposte_cible = $1;
  update rumeurs set rum_perso_cod = $2 where rum_perso_cod = $1;
  update sauve_evt_px set levt_perso_cod1 = $2 where levt_perso_cod1 = $1;
  update sauve_evt_px set levt_perso_cod2 = $2 where levt_perso_cod2 = $1;
  update sauve_evt_px set levt_attaquant = $2 where levt_attaquant = $1;
  update sauve_evt_px set levt_cible = $2 where levt_cible = $1;
  update sauve_monstre_pos set smon_perso_cod = $2 where smon_perso_cod = $1;
  update temp_partage_auto set tpa_perso_cod = $2 where tpa_perso_cod = $1;
  update temp_partage_auto set tpa_tueur = $2 where tpa_tueur = $1;
  update temple_fidele set tfid_perso_cod = $2 where tfid_perso_cod = $1;
  update transaction set tran_vendeur = $2 where tran_vendeur = $1;
  update transaction set tran_acheteur = $2 where tran_acheteur = $1;
  update transaction_echoppe set tran_vendeur = $2 where tran_vendeur = $1;
  update transaction_echoppe set tran_acheteur = $2 where tran_acheteur = $1;
  update triche set triche_perso_cod1 = $2 where triche_perso_cod1 = $1;
  update triche set triche_perso_cod2 = $2 where triche_perso_cod2 = $1;
  update tutorat set tuto_tuteur = $2 where tuto_tuteur = $1;
  update tutorat set tuto_filleul = $2 where tuto_filleul = $1;
  update vampire_hist set vamp_perso_pere = $2 where vamp_perso_pere = $1;
  update vampire_hist set vamp_perso_fils = $2 where vamp_perso_fils = $1;
  update vampire_tran set tvamp_perso_pere = $2 where tvamp_perso_pere = $1;
  update vampire_tran set tvamp_perso_fils = $2 where tvamp_perso_fils = $1;
  update potions.perso_toxic set ptox_perso_cod = $2 where ptox_perso_cod = $1;
  update quetes.perso_quete_automatique_etape set eqaperso_perso_cod = $2 where eqaperso_perso_cod = $1;

  return 'Modification du numero terminée';
end;$_$;


ALTER FUNCTION public.change_num_perso(integer, integer) OWNER TO delain;

--
-- Name: FUNCTION change_num_perso(integer, integer); Type: COMMENT; Schema: public; Owner: delain
--

COMMENT ON FUNCTION change_num_perso(integer, integer) IS 'Change le numero de perso de $1 vers $2';