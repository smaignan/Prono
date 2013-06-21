<?php
/*
 *    fichier  :  Diffuser.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  15 févr. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/Model/Journee.php";
require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/Model/Utilisateur.php";
require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/Common/Mail.php";

class ctrl_Diffuser extends Controller
{
	public function Load()
	{
		$this->AddData("IdJournee", $this->GetParam("IdDiffuser"));
		$this->AddData("Type", $this->GetParam("Type"));
	}
	
	public function btValider_Click()
	{
		$journee = model_Journee::GetJourneeById($this->GetParam("IdDiffuser"));
		
		$sSujet = "Pronos de la ".$journee->Numero."e journée";

		$sUrl = "http://".$_SERVER["HTTP_HOST"].cst_Racine."/?Module";

		$sMessage = str_replace("href=\\\"?Module", "href=\"".$sUrl, $this->GetParam("textMail"));

		$sMessage = str_replace("\\\">", "\">", $sMessage);

		$sMessage = str_replace("&amp;", "&", $sMessage);

		$sMessage = str_replace("href=\\\"?Module", "href=\"".$sUrl, $sMessage);

		$aUtilisateur = model_Utilisateur::Liste();

		$sMail = '';
		foreach ($aUtilisateur as $utilisateur)
		{
			
			$sMail .= $utilisateur->Mail.', ';
			
			if ($utilisateur->Mail2 != "")
			{
				$sMail .= $utilisateur->Mail2.', ';
			}
		}

		if ($sMail != "")
		{
			$sMail = substr($sMail, 0, -2);

			$mail = new Mail();
			$mail->Sujet = $sSujet;
			$mail->Destinataire = $sMail;
			$mail->Message = $sMessage;
			$mail->Send();
		}
		
		switch ($this->GetParam("Type"))
		{
			case "pronostics" :
				$journee->DiffuserPronostics = true;
				$journee->Update();
				
				$_GET["lbJournee"] = $journee->Id;
				
				$controller = new FrontController();
				$controller->ModuleTemp = "pronostics";
				$controller->Load();
				$controller->view->_Mode = "VIEW";

				file_put_contents($_SERVER['DOCUMENT_ROOT'].cst_TableJournee."/Pronostics_".$journee->Id.".txt", $controller->view->getTable());
				
				break;
			case "resultats" :
				$journee->DiffuserResultats = true;
				$journee->Update();
				
				$_GET["lbJournee"] = $journee->Id;
				
				$controller = new FrontController();
				$controller->ModuleTemp = "resultat";
				$controller->Load();
				$controller->view->_Mode = "VIEW";

				file_put_contents($_SERVER['DOCUMENT_ROOT'].cst_TableJournee."/Resultats_".$journee->Id.".txt", $controller->view->getTable());
				
				$controller = new FrontController();
				$controller->ModuleTemp = "classement";
				$controller->Load();
				$controller->view->_Mode = "VIEW";

				file_put_contents($_SERVER['DOCUMENT_ROOT'].cst_TableJournee."/Classement.txt", $controller->view->getTable());
				
				break;
		}
		
		Controller::Redirect("resultat");
	}
}
?>
