DELIMITER //

DROP FUNCTION IF EXISTS CalculPoint//
DROP PROCEDURE IF EXISTS sp_CalculerMalus//
DROP PROCEDURE IF EXISTS sp_CalculerBonus//
DROP PROCEDURE IF EXISTS sp_CalculerJournee//
DROP PROCEDURE IF EXISTS sp_Pronostic//
DROP PROCEDURE IF EXISTS sp_CalculRisque//
DROP PROCEDURE IF EXISTS sp_CalculRisqueMatch//

--------------------------------------------------------------------------

CREATE FUNCTION CalculPoint(Resultat int, Resultat_v int, Prono int, Prono_v int, Risques int)
RETURNS int DETERMINISTIC
BEGIN

declare	difResultat int;
declare difProno int;
declare iResult int;

	set iResult = 0;
	If Resultat = Prono And Resultat_v = Prono_v then
		set iResult = 3;
	else
		set difResultat = Resultat - Resultat_v;
		set difProno = Prono - Prono_v;
		if difResultat < 0 and difProno < 0 Then
			set iResult = 2;
		elseif difResultat = 0 and difProno = 0 then
			set iResult = 2;
		elseif difResultat > 0 and difProno > 0 then
			set iResult = 2;
		end if;
	end if;

	if iResult > 0 then
		set iResult = iResult + Risques;
	end if;

	RETURN iResult;
END//

--------------------------------------------------------------------------

CREATE PROCEDURE sp_CalculerJournee(Id int)
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE IdUtilisateur int;
	DECLARE FilRouge boolean;
	DECLARE points int;
	DECLARE curUti CURSOR FOR
		select uti_id, juti_fil_rouge from UTILISATEUR u 
			inner join JOURNEE_UTILISATEUR j on u.uti_id = juti_uti_id
		where juti_jou_id = Id;
	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
	
	OPEN curUti;

	REPEAT
		FETCH curUti INTO IdUtilisateur, FilRouge;
		IF NOT done THEN
			-- On récupère les points de l'utilisateur
			select sum(CalculPoint(ren_result, ren_result_v, pro_prono, pro_prono_v, pro_risques)) into points from RENCONTRE r
			inner join PRONOSTIC p on r.ren_id = p.pro_ren_id
			where ren_jou_id = Id and pro_uti_id = IdUtilisateur;

			-- Mise à jour des points de l'utilisateur pour cette journée
			update JOURNEE_UTILISATEUR set juti_points = points where juti_jou_id = Id and juti_uti_id = IdUtilisateur;
		END IF;
	UNTIL done END REPEAT;
	CLOSE curUti;

	Call sp_CalculerMalus(Id);
	Call sp_CalculerBonus(Id);

	update JOURNEE_UTILISATEUR set juti_points = juti_points * 2 where juti_jou_id = Id and juti_fil_rouge = true;
END//

--------------------------------------------------------------------------
CREATE PROCEDURE sp_CalculerMalus(Id integer)
BEGIN
	DECLARE done INT;
	DECLARE malus int;
	DECLARE nbJoueur int;
	DECLARE points int;
	DECLARE nb int;
	DECLARE cur CURSOR FOR
		select juti_points as points, count(*) as nb 
		from journee_utilisateur 
		where juti_jou_id = Id 
		group by juti_points 
		order by juti_points;
	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;

	set done = 0;
	set nbJoueur = 0;
	set malus = 20;

	update JOURNEE_UTILISATEUR set juti_malus = 20 where juti_jou_id = Id and juti_aveugle = true;
	update JOURNEE_UTILISATEUR set juti_malus = 0 where juti_jou_id = Id and juti_aveugle = false;

	open cur;
	labelLoop :REPEAT
		FETCH cur INTO points, nb;
		IF NOT done THEN
			update JOURNEE_UTILISATEUR set juti_malus = juti_malus + malus where juti_jou_id = Id and juti_points = points;
			set nbJoueur = nbJoueur + nb;
			if nbJoueur >= 3 then
				leave labelLoop;
			end if;
		
			set malus = malus / 2;
		END IF;
	UNTIL done END REPEAT labelLoop;
	CLOSE cur;
END//

--------------------------------------------------------------------------
CREATE PROCEDURE sp_CalculerBonus(Id int)
BEGIN
	DECLARE points int;

	update JOURNEE_UTILISATEUR set juti_bonus = 0 where juti_jou_id = Id;

	select max(juti_points) into points from JOURNEE_UTILISATEUR where juti_jou_id = Id and juti_fil_rouge = false;

	update JOURNEE_UTILISATEUR set juti_bonus = 10 where juti_jou_id = Id and juti_points = points;
END//

--------------------------------------------------------------------------
CREATE PROCEDURE sp_Pronostic(IdMatch int, IdUtilisateur int, ScoreEquipe int, ScoreVisiteur int)
BEGIN
	if (ScoreEquipe is null or ScoreVisiteur is null) then
		delete from PRONOSTIC where pro_ren_id = IdMatch and pro_uti_id = IdUtilisateur;
	else
		if ((select count(*) from PRONOSTIC where pro_ren_id = IdMatch and pro_uti_id = IdUtilisateur) = 0) then
			insert into PRONOSTIC
			(pro_ren_id, pro_uti_id, pro_prono, pro_prono_v)
			values
			(IdMatch, IdUtilisateur, ScoreEquipe, ScoreVisiteur);
		else
			update PRONOSTIC set
				pro_prono = ScoreEquipe, 
				pro_prono_v = ScoreVisiteur
			where pro_ren_id = IdMatch and pro_uti_id = IdUtilisateur;
		end if;
	end if;
END//

--------------------------------------------------------------------------
CREATE PROCEDURE sp_CalculRisque(IdJournee int)
BEGIN
	DECLARE done INT DEFAULT 0;
	DECLARE IdMatch int;
	DECLARE cur CURSOR FOR
		select ren_id from RENCONTRE where ren_jou_id = IdJournee;
	DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET done = 1;
	
	set done = 0;
	
	open cur;
	REPEAT
		FETCH cur INTO IdMatch;
		IF NOT done THEN
			Call sp_CalculRisqueMatch(IdMatch);
		END IF;
	UNTIL done END REPEAT;
	CLOSE cur;
END//

--------------------------------------------------------------------------
CREATE PROCEDURE sp_CalculRisqueMatch(IdMatch int)
BEGIN

declare	nb_v int;
declare nb_n int;
declare nb_d int;
declare iJournee int;
	
select ren_jou_id into iJournee from RENCONTRE where ren_id = IdMatch;

update PRONOSTIC set pro_risques = 0 where pro_ren_id = IdMatch and pro_uti_id in (
		select juti_uti_id from JOURNEE_UTILISATEUR where juti_aveugle = true and juti_jou_id = iJournee
);

select count(*) into nb_v from pronostic
where pro_ren_id = IdMatch and pro_prono > pro_prono_v and pro_uti_id in (
	select juti_uti_id from JOURNEE_UTILISATEUR where juti_aveugle = false and juti_jou_id = iJournee
);

if nb_v < 2 then
	update PRONOSTIC set pro_risques = 4
	where pro_ren_id = IdMatch and pro_prono > pro_prono_v and pro_uti_id in (
		select juti_uti_id from JOURNEE_UTILISATEUR where juti_aveugle = false and juti_jou_id = iJournee
	);
elseif nb_v < 4 then
	update PRONOSTIC set pro_risques = 3
	where pro_ren_id = IdMatch and pro_prono > pro_prono_v and pro_uti_id in (
		select juti_uti_id from JOURNEE_UTILISATEUR where juti_aveugle = false and juti_jou_id = iJournee
	);
elseif nb_v < 6 then
	update PRONOSTIC set pro_risques = 2
	where pro_ren_id = IdMatch and pro_prono > pro_prono_v and pro_uti_id in (
		select juti_uti_id from JOURNEE_UTILISATEUR where juti_aveugle = false and juti_jou_id = iJournee
	);
elseif nb_v < 8 then
	update PRONOSTIC set pro_risques = 1
	where pro_ren_id = IdMatch and pro_prono > pro_prono_v and pro_uti_id in (
		select juti_uti_id from JOURNEE_UTILISATEUR where juti_aveugle = false and juti_jou_id = iJournee
	);
else
	update PRONOSTIC set pro_risques = 0
	where pro_ren_id = IdMatch and pro_prono > pro_prono_v and pro_uti_id in (
		select juti_uti_id from JOURNEE_UTILISATEUR where juti_aveugle = false and juti_jou_id = iJournee
	);
end if;

select count(*) into nb_n from PRONOSTIC
where pro_ren_id = IdMatch and pro_prono = pro_prono_v and pro_uti_id in (
	select juti_uti_id from JOURNEE_UTILISATEUR where juti_aveugle = false and juti_jou_id = iJournee);

if nb_n < 2 then
	update PRONOSTIC set pro_risques = 4
	where pro_ren_id = IdMatch and pro_prono = pro_prono_v and pro_uti_id in (
		select juti_uti_id from JOURNEE_UTILISATEUR where juti_aveugle = false and juti_jou_id = iJournee
	);
elseif nb_n < 4 then
	update PRONOSTIC set pro_risques = 3
	where pro_ren_id = IdMatch and pro_prono = pro_prono_v and pro_uti_id in (
		select juti_uti_id from JOURNEE_UTILISATEUR where juti_aveugle = false and juti_jou_id = iJournee
	);
elseif nb_n < 6 then
	update PRONOSTIC set pro_risques = 2
	where pro_ren_id = IdMatch and pro_prono = pro_prono_v and pro_uti_id in (
		select juti_uti_id from JOURNEE_UTILISATEUR where juti_aveugle = false and juti_jou_id = iJournee
	);
elseif nb_n < 8 then
	update PRONOSTIC set pro_risques = 1
	where pro_ren_id = IdMatch and pro_prono = pro_prono_v and pro_uti_id in (
		select juti_uti_id from JOURNEE_UTILISATEUR where juti_aveugle = false and juti_jou_id = iJournee
	);
else
	update PRONOSTIC set pro_risques = 0
	where pro_ren_id = IdMatch and pro_prono = pro_prono_v and pro_uti_id in (
		select juti_uti_id from JOURNEE_UTILISATEUR where juti_aveugle = false and juti_jou_id = iJournee
	);
end if;

select count(*) into nb_d from PRONOSTIC
where pro_ren_id = IdMatch and pro_prono < pro_prono_v and pro_uti_id in (
	select juti_uti_id from JOURNEE_UTILISATEUR where juti_aveugle = false and juti_jou_id = iJournee);

if nb_d < 2 then
	update PRONOSTIC set pro_risques = 4
	where pro_ren_id = IdMatch and pro_prono < pro_prono_v and pro_uti_id in (
		select juti_uti_id from JOURNEE_UTILISATEUR where juti_aveugle = false and juti_jou_id = iJournee
	);
elseif nb_d < 4 then
	update PRONOSTIC set pro_risques = 3
	where pro_ren_id = IdMatch and pro_prono < pro_prono_v and pro_uti_id in (
		select juti_uti_id from JOURNEE_UTILISATEUR where juti_aveugle = false and juti_jou_id = iJournee
	);
elseif nb_d < 6 then
	update PRONOSTIC set pro_risques = 2
	where pro_ren_id = IdMatch and pro_prono < pro_prono_v and pro_uti_id in (
		select juti_uti_id from JOURNEE_UTILISATEUR where juti_aveugle = false and juti_jou_id = iJournee
	);
elseif nb_d < 8 then
	update PRONOSTIC set pro_risques = 1
	where pro_ren_id = IdMatch and pro_prono < pro_prono_v and pro_uti_id in (
		select juti_uti_id from JOURNEE_UTILISATEUR where juti_aveugle = false and juti_jou_id = iJournee
	);
else
	update PRONOSTIC set pro_risques = 0
	where pro_ren_id = IdMatch and pro_prono < pro_prono_v and pro_uti_id in (
		select juti_uti_id from JOURNEE_UTILISATEUR where juti_aveugle = false and juti_jou_id = iJournee
	);
end if;

END//

DELIMITER ;