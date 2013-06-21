<?php
/*
 *    fichier  :  Utilisateur_edit.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  16 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

require $_SERVER["DOCUMENT_ROOT"].cst_Racine."/Model/Utilisateur.php";

class ctrl_Utilisateur_edit extends Controller
{
	public function Load()
	{
		$utilisateur = ($this->GetParam("Id") == "") ? null : model_Utilisateur::GetUtilisateurById($this->GetParam("Id"));
		$this->AddData("Utilisateur", $utilisateur);
	}
	
	public function btValider_Click()
	{
		if ($this->GetParam("Id") == "")
			$this->AddUtilisateur();
		else
			$this->UpdateUtilisateur();
		
		Controller::Redirect("utilisateur");
	}
	
	public function btSupprimer_Click()
	{
		$utilisateur = model_Utilisateur::GetUtilisateurById($this->GetParam("Id"));
		$utilisateur->Delete();
		Controller::Redirect("utilisateur");
	}
	
	private function UpdateUtilisateur()
	{
		$utilisateur = model_Utilisateur::GetUtilisateurById($this->GetParam("Id"));
		$this->LoadUtilisateurByView($utilisateur);
		$utilisateur->Update();
	}
	
	private function AddUtilisateur()
	{
		$utilisateur = new model_Utilisateur();
		$this->LoadUtilisateurByView($utilisateur);
		$utilisateur->Add();
	}
	
	private function LoadUtilisateurByView(model_Utilisateur &$utilisateur)
	{
		$utilisateur->Nom = $this->GetParam("tbNom");
		$utilisateur->Prenom = $this->GetParam("tbPrenom");
		$utilisateur->Pseudo = $this->GetParam("tbPseudo");
		$utilisateur->Mail = $this->GetParam("tbMail");
		$utilisateur->Mail2 = $this->GetParam("tbMail2");
		$utilisateur->Login = $this->GetParam("tbLogin");
		$utilisateur->Password = $this->GetParam("tbPassword");
		$utilisateur->Bonus = $this->GetParam("tbBonus");
		$utilisateur->Malus = $this->GetParam("tbMalus");
		$utilisateur->Admin = ($this->GetParam("cbAdmin") == "on") ? true : false;
	}
}
?>
