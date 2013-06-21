<?php
/*
 *    fichier  :  Rappel.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  09 mai 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/Model/Utilisateur.php";
require_once $_SERVER["DOCUMENT_ROOT"].cst_Racine."/Common/Mail.php";

class ctrl_Rappel extends Controller
{
	public function Load()
	{
		$aJournee = model_Journee::Rappel();
		$aUtilisateur = model_Utilisateur::Liste();
		$sMessage = "";

		foreach($aJournee as $journee)
		{
			$sSujet = "Rappel ".$journee->Numero."ème";
			$sMessage = "La ".$journee->Numero."ème est à jouer pour demain.";

			foreach ($aUtilisateur as $utilisateur)
			{
				$sMail = $utilisateur->Mail;
			
				if ($utilisateur->Mail2 != "")
				{
					if ($sMail == "") $sMail = $utilisateur->Mail2;
					else $sMail .= ",".$utilisateur->Mail2;
				}
			
				if ($sMail != "")
				{
					$mail = new Mail();
					$mail->Sujet = $sSujet;
					$mail->Destinataire = $sMail;
					$mail->Message = $sMessage;
					$mail->Send();
				}
			}
		}
	}
}
?>