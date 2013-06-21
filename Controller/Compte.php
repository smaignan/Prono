<?php
/*
 *    fichier  :  Compte.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  6 févr. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/Model/Utilisateur.php";
require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/Model/Equipe.php";

class ctrl_Compte extends Controller
{
	public function Load()
	{
		//On récupère la grille aveugle de l'utilisateur connecté
		$utilisateur = model_Utilisateur::GetUtilisateurById(UserInfo::get('UserId'));
		$aGrille = $utilisateur->GetGrilleAveugle();
		$sPassword = $utilisateur->Password;
		
		//On récupère le nombre d'équipe
		$aEquipe = model_Equipe::Liste();
		$iNbEquipe = count($aEquipe) / 2;
		
		$this->AddData("GrilleAveugle", $aGrille);
		$this->AddData("NombreEquipe", $iNbEquipe);
		$this->AddData("Password", $sPassword);
	}
	
	public function btValider_Click()
	{
		//On récupère la grille aveugle de l'utilisateur connecté
		$utilisateur = model_Utilisateur::GetUtilisateurById(UserInfo::get('UserId'));
		$utilisateur->Password = $this->GetParam("tbPassword");
		$utilisateur->Update();
		
		//On récupère le nombre d'équipe
		$aEquipe = model_Equipe::Liste();
		$iNbEquipe = count($aEquipe) / 2;
		
		//Grille aveugle
		//On supprime la grille de l'utilisateur
		$utilisateur->DeleteGrilleAveugle();
		
		for ($i=0; $i<$iNbEquipe; $i++)
		{
			$sScore = $this->GetParam("tbGrille_".$i);
			if ($sScore != "")
			{
				list($sScoreEquipe, $sScoreVisiteur) = explode(" - ", $sScore);
				$utilisateur->AddGrilleAveugle($sScoreEquipe, $sScoreVisiteur);
			}
			else
			{
				$utilisateur->AddGrilleAveugle("", "");
			}
		}
	}
}
?>
