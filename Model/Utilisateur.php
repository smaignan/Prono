<?php
/*
 *    fichier  :  Utilisateur.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  9 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/BDD/SGBD.php";
require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/Model/Pronostic.php";
require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/Model/Journee.php";
require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/Model/JourneeUtilisateur.php";

class model_Utilisateur
{
	public $Id = 0;
	public $Nom = "";
	public $Prenom = "";
	public $Pseudo = "";
	public $Mail = "";
	public $Mail2 = "";
	public $Login = "";
	public $Password = "";
	public $Points = 0;
	public $Bonus = 0;
	public $Malus = 0;
	public $Classement = 0;
	public $Mouvement = 0;
	public $Admin = false;
	public $ScoreEquipe = null;
	public $ScoreVisiteur = null;
	public $EtatFilRouge = 0;

	/*
	 * Description : Charge un objet utilisateur
	 * Paramètres  : Ligne de résultat 
	 */
	function LoadUtilisateurByRecord($row)
	{
		$this->Id = $row["uti_id"];
		$this->Nom = $row["uti_nom"];
		$this->Prenom = $row["uti_prenom"];
		$this->Pseudo = $row["uti_pseudo"];
		$this->Mail = $row["uti_mail"];
		$this->Mail2 = $row["uti_mail2"];
		$this->Login = $row["uti_login"];
		$this->Password = $row["uti_password"];
		$this->Admin = $row["uti_admin"];
		
		if (array_key_exists("points", $row))
		{
			$this->Points = $row["points"];
			$this->Bonus = $row["bonus"];
			$this->Malus = $row["malus"];
		}
		else
		{
			$this->Points = 0;
			$this->Bonus = 0;
			$this->Malus = 0;
		}
	}
	
	/*
	 * Description : Retourne un objet utilisateur
	 * Paramètres  : Ligne de résultat
	 */
	public static function GetUtilisateurByRecord($row)
	{
		$utilisateur = new model_Utilisateur();
		$utilisateur->LoadUtilisateurByRecord($row);
		return $utilisateur;
	}
	
	/*
	 * Description : Retourne un utilisateur par rapport à un identifiant
	 * Paramètres  : Identifiant de l'utilisateur
	 */
	public static function GetUtilisateurById($Id)
	{
		$sp = new StoredProcedure("sp_GetUtilisateurById");
		$sp->AddParameters("Id", $Id);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		
		if (!$result)
		{
			echo "Erreur lors de la récupération d'un utilisateur.";
			exit;
		}

		$utilisateur = self::GetUtilisateurByRecord($bdd->fetch_array($result));
		
		unset($bdd);
		
		return $utilisateur;
	}
		
	/*
	 * Retourne la liste des utilisateurs
	 */
	public static function Liste()
	{
		$sp = new StoredProcedure("sp_ListeUtilisateur");
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		$bdd->disconnect();
		
		if (!$result)
		{
			echo "Erreur lors de la récupération de la liste des utilisateurs.";
			exit;
		}
		
		$aUtilisateur = array();
		while ($row = $bdd->fetch_array($result))
			$aUtilisateur[] = self::GetUtilisateurByRecord($row);
		
		unset($bdd);
		
		return $aUtilisateur;
	}
	
	public static function Classement($Journee)
	{
		//On récupère le classement de la journée précédente
		$sp = new StoredProcedure("sp_Classement");
		$sp->AddParameters("IdJournee", $Journee->Numero - 1);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		$bdd->disconnect();
		
		if (!$result)
		{
			echo "Erreur lors de la récupération du classement des utilisateurs.";
			exit;
		}
		
		$aClassement = array();
		while ($row = $bdd->fetch_array($result))
		{
			 $utilisateur = self::GetUtilisateurByRecord($row);
			 $aClassement[] = $utilisateur->Id;
		}
		//---------------------------------------

		$sp = new StoredProcedure("sp_Classement");
		$sp->AddParameters("IdJournee", $Journee->Numero);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		$bdd->disconnect();
		
		if (!$result)
		{
			echo "Erreur lors de la récupération du classement des utilisateurs.";
			exit;
		}
		
		$iClassement = 0;
		$aUtilisateur = array();
		while ($row = $bdd->fetch_array($result))
		{
			 $utilisateur = self::GetUtilisateurByRecord($row);
			 $iClassement++;
			 $utilisateur->Classement = $iClassement;
			 
			 $iAncienClassement = array_search($utilisateur->Id, $aClassement);
			 $iAncienClassement++;
			 $utilisateur->Mouvement = $iAncienClassement - $iClassement;
			 
			 $utilisateur->EtatFilRouge = $utilisateur->EtatFilRouge($Journee);
			 
			 $aUtilisateur[] = $utilisateur;
		}

		unset($bdd);
		
		return $aUtilisateur;
	}
	
	public function Authentifie()
	{
		$sp = new StoredProcedure("sp_Login");
		$sp->AddParameters("Login", $this->Login);
		$sp->AddParameters("Password", $this->Password);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);

		if ($bdd->num_rows($result) == 1)
		{
			$bAuthentifie = true;
			$this->LoadUtilisateurByRecord($bdd->fetch_array($result));
		}
		else
		{
			$bAuthentifie = false;
		}
		$bAuthentifie = ($bdd->num_rows($result) == 1);
		
		unset($bdd);
		
		return $bAuthentifie;
	}
	
	/*
	 * Description : Ajout d'un utilisateur
	 */
	function Add()
	{
		$sp = new StoredProcedure("sp_AddUtilisateur");
		$sp->AddParameters("Nom", $this->Nom);
		$sp->AddParameters("Prenom", $this->Prenom);
		$sp->AddParameters("Pseudo", $this->Pseudo);
		$sp->AddParameters("Mail", $this->Mail);
		$sp->AddParameters("Mail2", $this->Mail2);
		$sp->AddParameters("Login", $this->Login);
		$sp->AddParameters("Password", $this->Password);
		$sp->AddParameters("Bonus", $this->Bonus);
		$sp->AddParameters("Malus", $this->Malus);
		$sp->AddParameters("Admin", $this->Admin);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		
		unset($bdd);
		
		if (!$result)
		{
			echo "Erreur lors de l'ajout d'un utilisateur.";
			exit;
		}
	}

	/*
	 * Description : Mise à jour d'un utilisateur
	 */	
	function Update()
	{
		$sp = new StoredProcedure("sp_UpdateUtilisateur");
		$sp->AddParameters("Id", $this->Id);
		$sp->AddParameters("Nom", $this->Nom);
		$sp->AddParameters("Prenom", $this->Prenom);
		$sp->AddParameters("Pseudo", $this->Pseudo);
		$sp->AddParameters("Mail", $this->Mail);
		$sp->AddParameters("Mail2", $this->Mail2);
		$sp->AddParameters("Login", $this->Login);
		$sp->AddParameters("Password", $this->Password);
		$sp->AddParameters("Bonus", $this->Bonus);
		$sp->AddParameters("Malus", $this->Malus);
		$sp->AddParameters("Admin", $this->Admin);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		
		unset($bdd);
		
		if (!$result)
		{
			echo "Erreur lors de la mise à jour d'un utilisateur.";
			exit;
		}
	}
	
	/*
	 * Description : Suppression d'un utilisateur
	 */
	function Delete()
	{
		if ($this->Id == UserInfo::get('UserId'))
		{
			echo "En tant qu'administrateur, vous ne pouvez pas supprimer votre profil.";
			exit;
		}
		
		$sp = new StoredProcedure("sp_DeleteUtilisateur");
		$sp->AddParameters("Id", $this->Id);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);

		unset($bdd);
		
		if (!$result)
		{
			echo "Erreur lors de la suppression d'un utilisateur.";
			exit;
		}
	}
	
	public function Pronostic(model_Match $match)
	{
		$sp = new StoredProcedure("sp_Pronostic");
		$sp->AddParameters("IdMatch", $match->Id);
		$sp->AddParameters("IdUtilisateur", $this->Id);
		$sp->AddParameters("ScoreEquipe", $match->ScoreEquipe);
		$sp->AddParameters("ScoreVisiteur", $match->ScoreVisiteur);

		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);

		unset($bdd);

		if (!$result)
		{
			echo "Erreur lors de la mise à jour du pronostic.";
			exit;
		}
	}
	
	public function DeletePronosticsJournee($IdJournee)
	{
		$sp = new StoredProcedure("sp_DeletePronosticJournee");
		$sp->AddParameters("IdJournee", $IdJournee);
		$sp->AddParameters("IdUtilisateur", $this->Id);

		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		
		unset($bdd);

		if (!$result)
		{
			echo "Erreur lors de la suppression des pronostics de la journée";
			exit;
		}
	}
	
	public function Joue($IdJournee, $FilRouge, $GrilleAveugle)
	{
		$sp = new StoredProcedure("sp_Joue");
		$sp->AddParameters("IdJournee", $IdJournee);
		$sp->AddParameters("IdUtilisateur", $this->Id);
		$sp->AddParameters("FilRouge", $FilRouge);
		$sp->AddParameters("GrilleAveugle", $GrilleAveugle);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);

		unset($bdd);
		
		if (!$result)
		{
			echo "Erreur lors de la journée de l'utilisateur.";
			exit;
		}
	}
	
	public function ListePronostic($IdJournee)
	{
		$sp = new StoredProcedure("sp_PronosticUtilisateur");
		$sp->AddParameters("IdJournee", $IdJournee);
		$sp->AddParameters("IdUtilisateur", $this->Id);

		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		$bdd->disconnect();
		
		if (!$result)
		{
			echo "Erreur lors de la récupération de la liste des matchs.";
			exit;
		}
		
		$aProno = array();
		while ($row = $bdd->fetch_array($result))
			$aProno[$row["pro_ren_id"]] = model_Pronostic::GetPronosticByRecord($row);

		unset($bdd);
		
		return $aProno;
	}
	
	public function GetJourneeUtilisateur($IdJournee)
	{
		$sp = new StoredProcedure("sp_GetJourneeUtilisateur");
		$sp->AddParameters("IdJournee", $IdJournee);
		$sp->AddParameters("IdUtilisateur", $this->Id);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		
		$journeeUti = model_JourneeUtilisateur::GetJourneeUtilisateurByRecord($bdd->fetch_array($result));
		
		$bdd->disconnect();
				
		if (!$result)
		{
			echo "Erreur lors de la récupération de la journée utilisateur.";
			exit;
		}
		
		return $journeeUti;
	}
	
	public function GetGrilleAveugle()
	{
		$sp = new StoredProcedure("sp_GetGrilleAveugle");
		$sp->AddParameters("IdUtilisateur", $this->Id);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		
		if (!$result)
		{
			echo "Erreur lors de la récupération de la grille aveugle.";
			exit;
		}
		
		$aGrilleAveugle = array();
		while ($row = $bdd->fetch_array($result))
			$aGrilleAveugle[] = array("ScoreEquipe"=>$row["gri_prono"], "ScoreVisiteur"=>$row["gri_prono_v"]);

		$bdd->disconnect();
		
		return $aGrilleAveugle;
	}
	
	public function AddGrilleAveugle($ScoreEquipe, $ScoreVisiteur)
	{
		$sp = new StoredProcedure("sp_AddGrilleAveugle");
		$sp->AddParameters("IdUtilisateur", $this->Id);
		$sp->AddParameters("ScoreEquipe", $ScoreEquipe);
		$sp->AddParameters("ScoreVisiteur", $ScoreVisiteur);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		$bdd->disconnect();
		
		if (!$result)
		{
			echo "Erreur lors de l'ajout d'une grille aveugle.";
			exit;
		}
	}
	
	public function DeleteGrilleAveugle()
	{
		$sp = new StoredProcedure("sp_DeleteGrilleAveugle");
		$sp->AddParameters("IdUtilisateur", $this->Id);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		$bdd->disconnect();
		
		if (!$result)
		{
			echo "Erreur lors de la suppression d'une grille aveugle.";
			exit;
		}
	}
	
	public function EtatFilRouge($Journee)
	{
		$sp = new StoredProcedure("sp_FilRouge");
		$sp->AddParameters("IdUtilisateur", $this->Id);
		$sp->AddParameters("MatchRetour", $Journee->MatchRetour);
		$sp->AddParameters("IdJournee", $Journee->Numero);
		
		$bdd = SGBD::GetInstance();
		$bdd->connect();
		$result = $bdd->Execute($sp);
		
		if (!$result)
		{
			echo "Erreur lors du test de la disponibilité du fil rouge.";
			exit;
		}
		
		$aGrilleAveugle = array();
		$row = $bdd->fetch_array($result);

		$iEtat = $row["nb"];
		if ($iEtat != 0)
		{
			$iEtat = ($row["numero"] == $Journee->Numero) ? 1 : -1;
		};

		$bdd->disconnect();
		
		return $iEtat;
	}
}
?>
