CREATE OR REPLACE FUNCTION sp_login(Login character varying, Password character varying)
  RETURNS SETOF utilisateur AS
$BODY$
DECLARE
	rec utilisateur;
BEGIN
	FOR rec in SELECT * FROM utilisateur WHERE uti_login = Login and uti_password = Password
	LOOP
		RETURN NEXT rec;
	END LOOP;
	RETURN;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_AddUtilisateur(Nom character varying, Prenom character varying, Pseudo character varying, Mail character varying, Login character varying, Password character varying, Bonus integer, Malus integer, Admin boolean)
  RETURNS integer AS
$BODY$
BEGIN
	insert into utilisateur
	(uti_nom, uti_prenom, uti_pseudo, uti_mail, uti_login, uti_password, uti_bonus, uti_malus, uti_admin) 
	values 
	(Nom, Prenom, Pseudo, Mail, Login, Password, Bonus, Malus, Admin);
	RETURN 1;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_DeleteUtilisateur(Id integer)
  RETURNS integer AS
$BODY$
BEGIN
	delete from utilisateur where uti_id = Id;
	RETURN 1;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_GetUtilisateurById(Id integer)
  RETURNS SETOF utilisateur AS
$BODY$
DECLARE
	rec utilisateur;
BEGIN
	FOR rec in SELECT * FROM utilisateur WHERE uti_id = Id
	LOOP
		RETURN NEXT rec;
	END LOOP;
	RETURN;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_ListeUtilisateur()
  RETURNS SETOF utilisateur AS
$BODY$
DECLARE
	rec utilisateur;
BEGIN
	FOR rec in SELECT * FROM utilisateur order by uti_nom, uti_prenom
	LOOP
		RETURN NEXT rec;
	END LOOP;
	RETURN;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_GetGrilleAveugle(IdUtilisateur integer)
  RETURNS SETOF grille_aveugle AS
$BODY$
DECLARE
	rec grille_aveugle;
BEGIN
	FOR rec in SELECT * FROM grille_aveugle where gri_uti_id = IdUtilisateur order by gri_id
	LOOP
		RETURN NEXT rec;
	END LOOP;
	RETURN;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_DeleteGrilleAveugle(IdUtilisateur integer)
  RETURNS integer as
$BODY$
BEGIN
	DELETE FROM grille_aveugle where gri_uti_id = IdUtilisateur;
	RETURN 1;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_AddGrilleAveugle(IdUtilisateur integer, ScoreEquipe integer, ScoreVisiteur integer)
  RETURNS integer AS
$BODY$
BEGIN
	INSERT INTO grille_aveugle (gri_uti_id, gri_prono, gri_prono_v) values (IdUtilisateur, ScoreEquipe, ScoreVisiteur);
	RETURN 1;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_UpdateUtilisateur(Id integer, Nom character varying, Prenom character varying, Pseudo character varying, Mail character varying, Login character varying, Password character varying, Bonus integer, Malus integer, Admin boolean)
  RETURNS integer AS
$BODY$
BEGIN
	update utilisateur set
		uti_nom = Nom, 
		uti_prenom = Prenom,
		uti_pseudo = Pseudo,
		uti_mail = Mail, 
		uti_login = Login,
		uti_password = Password,
		uti_bonus = Bonus,
		uti_malus = Malus,
		uti_admin = Admin
	where uti_id = Id;
	RETURN 1;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_AddEquipe(Nom character varying)
  RETURNS integer AS
$BODY$
BEGIN
	insert into equipe(equ_nom) values (Nom);
	RETURN 1;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_DeleteEquipe(Id integer)
  RETURNS integer AS
$BODY$
BEGIN
	delete from equipe where equ_id = Id;
	RETURN 1;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_GetEquipeById(Id integer)
  RETURNS SETOF equipe AS
$BODY$
DECLARE
	rec equipe;
BEGIN
	FOR rec in SELECT * FROM equipe WHERE equ_id = Id
	LOOP
		RETURN NEXT rec;
	END LOOP;
	RETURN;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_ListeEquipe()
  RETURNS SETOF equipe AS
$BODY$
DECLARE
	rec equipe;
BEGIN
	FOR rec in SELECT * FROM equipe order by equ_nom
	LOOP
		RETURN NEXT rec;
	END LOOP;
	RETURN;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_UpdateEquipe(Id integer, Nom character varying)
  RETURNS integer AS
$BODY$
BEGIN
	update equipe set
		equ_nom = Nom 
	where equ_id = Id;
	RETURN 1;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_ListeJournee()
  RETURNS SETOF journee AS
$BODY$
DECLARE
	rec journee;
BEGIN
	FOR rec in SELECT * FROM journee order by jou_date
	LOOP
		RETURN NEXT rec;
	END LOOP;
	RETURN;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_ListeJourneePronostics()
  RETURNS SETOF journee AS
$BODY$
DECLARE
	rec journee;
BEGIN
	FOR rec in SELECT * FROM journee where jou_diffuser_pronostics = 't' order by jou_date
	LOOP
		RETURN NEXT rec;
	END LOOP;
	RETURN;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_ListeJourneeResultats()
  RETURNS SETOF journee AS
$BODY$
DECLARE
	rec journee;
BEGIN
	FOR rec in SELECT * FROM journee where jou_diffuser_resultats = 't' order by jou_date
	LOOP
		RETURN NEXT rec;
	END LOOP;
	RETURN;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_GetJourneeById(Id integer)
  RETURNS SETOF journee AS
$BODY$
DECLARE
	rec journee;
BEGIN
	FOR rec in SELECT * FROM journee WHERE jou_id = Id
	LOOP
		RETURN NEXT rec;
	END LOOP;
	RETURN;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_GetNextJournee()
  RETURNS SETOF journee AS
$BODY$
DECLARE
	rec journee;
	IdJournee integer;
BEGIN
	select jou_id into IdJournee from journee where jou_date = (select min(jou_date) from journee where jou_date >= now());

	FOR rec in SELECT * FROM journee WHERE jou_id = IdJournee
	LOOP
		RETURN NEXT rec;
	END LOOP;
	RETURN;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_GetLastJourneePronostics(Admin boolean)
  RETURNS SETOF journee AS
$BODY$
DECLARE
	IdJournee integer;
	rec journee;
BEGIN
	If Admin Then
		select jou_id into IdJournee from journee where jou_date = (select min(jou_date) from journee where jou_diffuser_pronostics = 'f');
	else
		select jou_id into IdJournee from journee where jou_date = (select max(jou_date) from journee where jou_diffuser_pronostics = 't');
	end If;

	FOR rec in select * from journee where jou_id = IdJournee
	LOOP
		RETURN NEXT rec;
	END LOOP;
	RETURN;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_GetLastJourneeResultats(Admin boolean)
  RETURNS SETOF journee AS
$BODY$
DECLARE
	IdJournee integer;
	rec journee;
BEGIN
	If Admin Then
		select jou_id into IdJournee from journee where jou_date = (select min(jou_date) from journee where jou_diffuser_resultats = 'f');
	else
		select jou_id into IdJournee from journee where jou_date = (select max(jou_date) from journee where jou_diffuser_resultats = 't');
	end If;

	FOR rec in select * from journee where jou_id = IdJournee
	LOOP
		RETURN NEXT rec;
	END LOOP;
	RETURN;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_AddJournee(Numero integer, MatchAller boolean, DateFin date, DiffuserPronostics boolean, DiffuserResultats boolean)
  RETURNS integer AS
$BODY$
BEGIN
	insert into journee(jou_numero, jou_aller, jou_date, jou_diffuser_pronostics, jou_diffuser_resultats) values (Numero, MatchAller, DateFin, DiffuserPronostics, DiffuserResultats);
	RETURN 1;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_UpdateJournee(Id integer, Numero integer, MatchAller boolean, DateFin date, DiffuserPronostics boolean, DiffuserResultats boolean)
  RETURNS integer AS
$BODY$
BEGIN
	update journee set
		jou_numero = Numero, 
		jou_aller = MatchAller,
		jou_date = DateFin,
		jou_diffuser_pronostics = DiffuserPronostics,
		jou_diffuser_resultats = DiffuserResultats
	where jou_id = Id;
	RETURN 1;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_DeleteJournee(Id integer)
  RETURNS integer AS
$BODY$
BEGIN
	delete from rencontre where ren_jou_id = Id;
	delete from journee where jou_id = Id;
	RETURN 1;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_ListeMatch(IdJournee integer)
  RETURNS SETOF rencontre AS
$BODY$
DECLARE
	rec rencontre;
BEGIN
	FOR rec in SELECT * FROM rencontre where ren_jou_id = IdJournee order by ren_id
	LOOP
		RETURN NEXT rec;
	END LOOP;
	RETURN;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE TYPE type_Pronostic AS (
  pro_ren_id integer,
  pro_uti_id integer,
  pro_prono integer,
  pro_prono_v integer,
  pro_risques integer,
  juti_aveugle boolean
);

-- DO Gestion grille aveugle : Gestion manuelle
CREATE OR REPLACE FUNCTION sp_PronosticUtilisateur(IdJournee integer, IdUtilisateur integer)
  RETURNS SETOF type_Pronostic AS
$BODY$
DECLARE
	indiceGrille integer;
	recMatch record;
	rec type_Pronostic;
BEGIN
	if ((select count(*) from journee where jou_id = 7 and jou_date < current_timestamp) > 0) then
		if ((select count(*) from pronostic p inner join rencontre r on p.pro_ren_id = r.ren_id where ren_jou_id = IdJournee and pro_uti_id = IdUtilisateur) = 0) then
			if ((select count(*) from grille_aveugle where gri_uti_id = IdUtilisateur) > 0) then
				indiceGrille = 0;
				for recMatch in select ren_id from rencontre where ren_jou_id = IdJournee order by ren_id
				loop
					insert into pronostic
					(pro_ren_id, pro_uti_id, pro_prono, pro_prono_v, pro_risques)
					select recMatch.ren_id, IdUtilisateur, gri_prono, gri_prono_v, 0 from grille_aveugle where gri_uti_id = IdUtilisateur order by gri_id limit 1 offset indiceGrille;
					indiceGrille = indiceGrille + 1;
				end loop;
	
				if ((select count(*) from journee_utilisateur where juti_jou_id = IdJournee and juti_uti_id = IdUtilisateur) = 0) then
					insert into journee_utilisateur (juti_uti_id, juti_jou_id, juti_fil_rouge, juti_points, juti_aveugle)
					values
					(IdUtilisateur, IdJournee, 'f', null, 't');
				else
					update journee_utilisateur set 
						juti_fil_rouge = 'f',
						juti_aveugle = 't'
					where juti_uti_id = IdUtilisateur and juti_jou_id = IdJournee;
				end if;
			end if;
		end if;
	end if;

	FOR rec in SELECT p.*, juti_aveugle FROM rencontre r 
		left outer join pronostic p on r.ren_id = p.pro_ren_id
		inner join journee_utilisateur ju on r.ren_jou_id = ju.juti_jou_id
	where ren_jou_id = IdJournee and p.pro_uti_id = IdUtilisateur
	LOOP
		RETURN NEXT rec;
	END LOOP;
	RETURN;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_AddMatch(IdEquipe integer, IdVisiteur integer, IdJournee integer)
  RETURNS integer AS
$BODY$
BEGIN
	insert into rencontre (ren_equ_id, ren_equ_id_v, ren_jou_id)
	values
	(IdEquipe, IdVisiteur, IdJournee);
	RETURN 1;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_ListeEquipeDisponible(IdJournee integer)
  RETURNS SETOF equipe AS
$BODY$
DECLARE
	rec equipe;
BEGIN
	FOR rec in SELECT * FROM equipe WHERE 
		equ_id NOT IN (SELECT ren_equ_id FROM rencontre WHERE ren_jou_id = IdJournee) AND
		equ_id NOT IN (SELECT ren_equ_id_v FROM rencontre WHERE ren_jou_id = IdJournee)
		ORDER BY equ_nom
	LOOP
		RETURN NEXT rec;
	END LOOP;
	RETURN;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_GetMatchById(Id integer)
  RETURNS SETOF rencontre AS
$BODY$
DECLARE
	rec rencontre;
BEGIN
	FOR rec in SELECT * FROM rencontre WHERE ren_id = Id
	LOOP
		RETURN NEXT rec;
	END LOOP;
	RETURN;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_DeleteMatch(Id integer)
  RETURNS integer AS
$BODY$
BEGIN
	delete from pronostic where pro_ren_id = Id;
	delete from rencontre where ren_id = Id;
	RETURN 1;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_UpdateScore(Id integer, ScoreEquipe integer, ScoreVisiteur integer)
  RETURNS integer AS
$BODY$
BEGIN
	update rencontre set
		ren_result = ScoreEquipe, 
		ren_result_v = ScoreVisiteur
	where ren_id = Id;
	RETURN 1;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_Pronostic(IdMatch integer, IdUtilisateur integer, ScoreEquipe integer, ScoreVisiteur integer)
  RETURNS integer AS
$BODY$
DECLARE
	result integer;
BEGIN
	if (ScoreEquipe is null or ScoreVisiteur is null) then
		delete from pronostic where pro_ren_id = IdMatch and pro_uti_id = IdUtilisateur;
	else
		if ((select count(*) from pronostic where pro_ren_id = IdMatch and pro_uti_id = IdUtilisateur) = 0) then
			insert into pronostic
			(pro_ren_id, pro_uti_id, pro_prono, pro_prono_v)
			values
			(IdMatch, IdUtilisateur, ScoreEquipe, ScoreVisiteur);
		else
			update pronostic set
				pro_prono = ScoreEquipe, 
				pro_prono_v = ScoreVisiteur
			where pro_ren_id = IdMatch and pro_uti_id = IdUtilisateur;
		end if;
	end if;

	select * into result from sp_CalculRisqueMatch(IdMatch);
	RETURN 1;
END;
$BODY$
 LANGUAGE plpgsql;

--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_CalculRisqueMatch(IdMatch integer)
  RETURNS integer AS
$BODY$
declare
	nb_v integer;
	nb_n integer;
	nb_d integer;
	iJournee integer;
BEGIN

select ren_jou_id into iJournee from rencontre where ren_id = IdMatch;

update pronostic set pro_risques = 0 where pro_ren_id = IdMatch and pro_uti_id in (
		select juti_uti_id from journee_utilisateur where juti_aveugle = 't' and juti_jou_id = iJournee
);

select count(*) into nb_v from pronostic
where pro_ren_id = IdMatch and pro_prono > pro_prono_v and pro_uti_id in (
	select juti_uti_id from journee_utilisateur where juti_aveugle = 'f' and juti_jou_id = iJournee
);

if nb_v < 2 then
	update pronostic set pro_risques = 4
	where pro_ren_id = IdMatch and pro_prono > pro_prono_v and pro_uti_id in (
		select juti_uti_id from journee_utilisateur where juti_aveugle = 'f' and juti_jou_id = iJournee
	);
elseif nb_v < 4 then
	update pronostic set pro_risques = 3
	where pro_ren_id = IdMatch and pro_prono > pro_prono_v and pro_uti_id in (
		select juti_uti_id from journee_utilisateur where juti_aveugle = 'f' and juti_jou_id = iJournee
	);
elseif nb_v < 6 then
	update pronostic set pro_risques = 2
	where pro_ren_id = IdMatch and pro_prono > pro_prono_v and pro_uti_id in (
		select juti_uti_id from journee_utilisateur where juti_aveugle = 'f' and juti_jou_id = iJournee
	);
elseif nb_v < 8 then
	update pronostic set pro_risques = 1
	where pro_ren_id = IdMatch and pro_prono > pro_prono_v and pro_uti_id in (
		select juti_uti_id from journee_utilisateur where juti_aveugle = 'f' and juti_jou_id = iJournee
	);
else
	update pronostic set pro_risques = 0
	where pro_ren_id = IdMatch and pro_prono > pro_prono_v and pro_uti_id in (
		select juti_uti_id from journee_utilisateur where juti_aveugle = 'f' and juti_jou_id = iJournee
	);
end if;

select count(*) into nb_n from pronostic
where pro_ren_id = IdMatch and pro_prono = pro_prono_v and pro_uti_id in (
	select juti_uti_id from journee_utilisateur where juti_aveugle = 'f' and juti_jou_id = iJournee);

if nb_n < 2 then
	update pronostic set pro_risques = 4
	where pro_ren_id = IdMatch and pro_prono = pro_prono_v and pro_uti_id in (
		select juti_uti_id from journee_utilisateur where juti_aveugle = 'f' and juti_jou_id = iJournee
	);
elseif nb_n < 4 then
	update pronostic set pro_risques = 3
	where pro_ren_id = IdMatch and pro_prono = pro_prono_v and pro_uti_id in (
		select juti_uti_id from journee_utilisateur where juti_aveugle = 'f' and juti_jou_id = iJournee
	);
elseif nb_n < 6 then
	update pronostic set pro_risques = 2
	where pro_ren_id = IdMatch and pro_prono = pro_prono_v and pro_uti_id in (
		select juti_uti_id from journee_utilisateur where juti_aveugle = 'f' and juti_jou_id = iJournee
	);
elseif nb_n < 8 then
	update pronostic set pro_risques = 1
	where pro_ren_id = IdMatch and pro_prono = pro_prono_v and pro_uti_id in (
		select juti_uti_id from journee_utilisateur where juti_aveugle = 'f' and juti_jou_id = iJournee
	);
else
	update pronostic set pro_risques = 0
	where pro_ren_id = IdMatch and pro_prono = pro_prono_v and pro_uti_id in (
		select juti_uti_id from journee_utilisateur where juti_aveugle = 'f' and juti_jou_id = iJournee
	);
end if;

select count(*) into nb_d from pronostic
where pro_ren_id = IdMatch and pro_prono < pro_prono_v and pro_uti_id in (
	select juti_uti_id from journee_utilisateur where juti_aveugle = 'f' and juti_jou_id = iJournee);

if nb_d < 2 then
	update pronostic set pro_risques = 4
	where pro_ren_id = IdMatch and pro_prono < pro_prono_v and pro_uti_id in (
		select juti_uti_id from journee_utilisateur where juti_aveugle = 'f' and juti_jou_id = iJournee
	);
elseif nb_d < 4 then
	update pronostic set pro_risques = 3
	where pro_ren_id = IdMatch and pro_prono < pro_prono_v and pro_uti_id in (
		select juti_uti_id from journee_utilisateur where juti_aveugle = 'f' and juti_jou_id = iJournee
	);
elseif nb_d < 6 then
	update pronostic set pro_risques = 2
	where pro_ren_id = IdMatch and pro_prono < pro_prono_v and pro_uti_id in (
		select juti_uti_id from journee_utilisateur where juti_aveugle = 'f' and juti_jou_id = iJournee
	);
elseif nb_d < 8 then
	update pronostic set pro_risques = 1
	where pro_ren_id = IdMatch and pro_prono < pro_prono_v and pro_uti_id in (
		select juti_uti_id from journee_utilisateur where juti_aveugle = 'f' and juti_jou_id = iJournee
	);
else
	update pronostic set pro_risques = 0
	where pro_ren_id = IdMatch and pro_prono < pro_prono_v and pro_uti_id in (
		select juti_uti_id from journee_utilisateur where juti_aveugle = 'f' and juti_jou_id = iJournee
	);
end if;

	RETURN 1;
END;
$BODY$
 LANGUAGE plpgsql;

--------------------------------------------------------------------------
-- DO : A reprendre, est ce vraiment nécessaire ?
CREATE OR REPLACE FUNCTION sp_CalculRisqueJournee(IdJournee integer)
  RETURNS integer AS
$BODY$
declare
	rec RECORD;
	result integer;
BEGIN
	FOR rec in select * from rencontre where ren_jou_id = IdJournee
	LOOP
		select * into result from sp_CalculRisqueMatch(rec.ren_id);
	END LOOP;
	RETURN 1;
END;
$BODY$
 LANGUAGE plpgsql;

--------------------------------------------------------------------------
-- TODO : A reprendre
CREATE OR REPLACE FUNCTION CalculPoint(Resultat integer, Resultat_v integer, Prono integer, Prono_v integer, Risques integer)
  RETURNS integer AS
$BODY$
declare
	difResultat integer;
	difProno integer;
	iResult integer;
BEGIN
	iResult = 0;
	If Resultat = Prono And Resultat_v = Prono_v then
		iResult = 3;
	else
		difResultat = Resultat - Resultat_v;
		difProno = Prono - Prono_v;
		if difResultat < 0 and difProno < 0 Then
			iResult = 2;
		elseif difResultat = 0 and difProno = 0 then
			iResult = 2;
		elseif difResultat > 0 and difProno > 0 then
			iResult = 2;
		end if;
	end if;

	if iResult > 0 then
		iResult = iResult + Risques;
	end if;

	RETURN iResult;
END;
$BODY$
 LANGUAGE plpgsql;

--------------------------------------------------------------------------
-- TODO : A reprendre
CREATE OR REPLACE FUNCTION sp_CalculerJournee(Id integer)
  RETURNS integer AS
$BODY$
declare
	rec RECORD;
	points integer;
	result integer;
BEGIN
	FOR rec in select uti_id, juti_fil_rouge from utilisateur u inner join journee_utilisateur j on u.uti_id = juti_uti_id where juti_jou_id = Id
	LOOP
		-- Calcul des points pour l'utilisateur en cours
		select sum(CalculPoint(ren_result, ren_result_v, pro_prono, pro_prono_v, pro_risques)) into points from rencontre r
		inner join pronostic p on r.ren_id = p.pro_ren_id
		where ren_jou_id = Id and pro_uti_id = rec.uti_id;

		-- Mise à jour des points de l'utilisateur pour cette journée
		update journee_utilisateur set juti_points = points where juti_jou_id = Id and juti_uti_id = rec.uti_id;
	END LOOP;

	select * into result from sp_CalculerMalus(Id);
	select * into result from sp_CalculerBonus(Id);

	update journee_utilisateur set juti_points = juti_points * 2 where juti_jou_id = Id and juti_fil_rouge = 't';

	RETURN 1;
END;
$BODY$
 LANGUAGE plpgsql;

--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_GetJourneeUtilisateur(IdJournee integer, IdUtilisateur integer)
  RETURNS SETOF journee_utilisateur AS
$BODY$
DECLARE
	rec journee_utilisateur;
BEGIN
	FOR rec in select * from journee_utilisateur where juti_jou_id = IdJournee and juti_uti_id = IdUtilisateur
	LOOP
		RETURN NEXT rec;
	END LOOP;
	RETURN;
END;
$BODY$
 LANGUAGE plpgsql;

--------------------------------------------------------------------------
CREATE OR REPLACE FUNCTION sp_Joue(IdJournee integer, IdUtilisateur integer, FilRouge boolean, GrilleAveugle boolean)
  RETURNS integer AS
$BODY$
BEGIN
	if ((select count(*) from journee_utilisateur where juti_jou_id = IdJournee and juti_uti_id = IdUtilisateur) = 0) then
		insert into journee_utilisateur (juti_uti_id, juti_jou_id, juti_fil_rouge, juti_points, juti_aveugle)
		values
		(IdUtilisateur, IdJournee, FilRouge, null, GrilleAveugle);
	else
		update journee_utilisateur set 
			juti_fil_rouge = FilRouge,
			juti_aveugle = GrilleAveugle
		where juti_uti_id = IdUtilisateur and juti_jou_id = IdJournee;
	end if;
	RETURN 1;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
CREATE TYPE type_classement AS (
  uti_id integer, 
  uti_nom character varying(50), 
  uti_prenom character varying(50),
  uti_pseudo character varying(50),
  uti_mail character varying(100),
  uti_login character varying(20),
  uti_password character varying(20),
  uti_cookies boolean,
  uti_points integer,
  uti_bonus integer,
  uti_malus integer,
  uti_admin boolean,
  points bigint,
  bonus bigint,
  malus bigint);

CREATE OR REPLACE FUNCTION sp_Classement(IdJournee integer)
  RETURNS SETOF type_classement AS
$BODY$
DECLARE
	Id integer;
	Numero integer;
	rec type_classement;
BEGIN
	If IdJournee = 0 then
		select jou_id into Id from journee where jou_date in (select max(jou_date) from journee where jou_diffuser_resultats = 't');
	else
		Id = IdJournee;
	end if;

	select jou_numero into Numero from journee where jou_id = Id;

	FOR rec in select u.*, p.points as points, p.bonus as bonus, p.malus as malus from utilisateur u inner join (select juti_uti_id, sum(juti_points) as points, sum(juti_bonus) as bonus, sum(juti_malus) as malus from journee_utilisateur ju inner join journee j on ju.juti_jou_id = j.jou_id	where j.jou_numero <= Numero group by juti_uti_id) p on u.uti_id = p.juti_uti_id order by p.points desc, p.bonus - p.malus desc
	LOOP
		RETURN NEXT rec;
	END LOOP;
	RETURN;
END;
$BODY$
 LANGUAGE plpgsql;

--------------------------------------------------------------------------
-- TODO : A reprendre
CREATE OR REPLACE FUNCTION sp_CalculerMalus(Id integer)
  RETURNS integer AS
$BODY$
declare
	rec RECORD;
	malus integer;
	nbJoueur integer;
BEGIN
	nbJoueur = 0;
	malus = 20;

	update journee_utilisateur set juti_malus = 20 where juti_jou_id = Id and juti_aveugle = 't';
	update journee_utilisateur set juti_malus = 0 where juti_jou_id = Id and juti_aveugle = 'f';

	FOR rec in select juti_points as points, count(*) as nb from journee_utilisateur where juti_jou_id = Id group by juti_points order by juti_points
	LOOP
		update journee_utilisateur set juti_malus = juti_malus + malus where juti_jou_id = Id and juti_points = rec.points;
		nbJoueur = nbJoueur + rec.nb;
		if nbJoueur >= 3 then
			exit;
		end if;
		
		malus = malus / 2;
	END LOOP;

	RETURN 1;
END;
$BODY$
 LANGUAGE plpgsql;
--------------------------------------------------------------------------
-- TODO : A reprendre
CREATE OR REPLACE FUNCTION sp_CalculerBonus(Id integer)
  RETURNS integer AS
$BODY$
declare
	rec RECORD;
	points integer;
BEGIN
	-- A AMELIORER, PAS BESOIN DE FAIRE DE LOOP, SEUL LE PREMIER ENREGISTREMENT NOUS INTERESSE
	update journee_utilisateur set juti_bonus = 0 where juti_jou_id = Id;

	update journee_utilisateur set juti_bonus = 10 where juti_jou_id = Id and juti_points = (select max(juti_points) from journee_utilisateur where juti_jou_id = Id and juti_fil_rouge = 'f');

	RETURN 1;
END;
$BODY$
 LANGUAGE plpgsql;