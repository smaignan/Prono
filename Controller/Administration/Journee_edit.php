<?php
/*
 *    fichier  :  Journee_edit.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  23 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/Model/Journee.php";
require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/Model/Match.php";
require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/Model/Equipe.php";

class ctrl_Journee_edit extends Controller
{
	public function Load()
	{
		$journee = null;
		$aMatch = array();
		
		if ($this->GetParam("Id") == "")
		{
			$journee = model_Journee::GetDerniereJournee();
		}
		else
		{
			$journee = model_Journee::GetJourneeById($this->GetParam("Id"));
			$aMatch = $journee->ListeMatch();
		}
		
		$this->AddData("Id", $this->GetParam("Id"));
		$this->AddData("Journee", $journee);
		$this->AddData("ListeMatch", $aMatch);
	}
	
	public function btValider_Click()
	{
		if ($this->GetParam("Id") == "")
		{
			$this->AddJournee();
		}
		else
		{
			$this->UpdateJournee();
		}
		
		Controller::Redirect("journee");
	}
	
	public function btSupprimer_Click()
	{
		$journee = model_Journee::GetJourneeById($this->GetParam("Id"));
		$journee->Delete();
		Controller::Redirect("journee");
	}
	
	private function UpdateJournee()
	{
		$journee = model_Journee::GetJourneeById($this->GetParam("Id"));
		$this->LoadJourneeByView($journee);
		$journee->Update();
	}
	
	private function AddJournee()
	{
		$journee = new model_Journee();
		$this->LoadJourneeByView($journee);
		$journee->Add();
	}
	
	private function LoadJourneeByView(model_Journee &$journee)
	{
		$journee->Numero = $this->GetParam("tbNumero");
		$journee->MatchRetour = ($this->GetParam("cbMatchRetour") == "on") ? true : false;
		$journee->DateFin = SGBD::GetTimeStampFromView($this->GetParam("tbDateFin"));
		$journee->DateJournee = SGBD::GetTimeStampFromView($this->GetParam("tbDateJournee"));
		$journee->DiffuserPronostics = ($this->GetParam("cbDiffuserPronostics") == "on") ? true : false;
		$journee->DiffuserResultats = ($this->GetParam("cbDiffuserResultats") == "on") ? true : false;
	}
	
	public function lbAjouter_Click()
	{
		$journee = model_Journee::GetJourneeById($this->GetParam("Id"));
		
		//On récupère la liste des équipes
		$this->AddData("ListeEquipe", $journee->EquipeDisponible());
		$this->AddData("Mode", "ADD");
	}
	
	public function lbValidMatch_Click()
	{
		$match = new model_Match();
		$match->IdEquipe = $this->GetParam("lbEquipe");
		$match->IdVisiteur = $this->GetParam("lbVisiteur");
		$match->IdJournee = $this->GetParam("Id");
		$match->Add();
		Controller::Redirect("journee_edit&Id=".$this->GetParam("Id"));
	}
	
	public function lbSupprimer_Click()
	{
		$match = model_Match::GetMatchById($this->GetParam("IdRencontre"));
		$match->Delete();
		Controller::Redirect("journee_edit&Id=".$this->GetParam("Id"));
	}
}
?>
