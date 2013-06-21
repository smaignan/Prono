<?php
/*
 *    fichier  :  Mail.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  18 févr. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

class Mail
{
	public $Destinataire = "";
	public $Sujet = "";
	public $Message = "";
	public $Header = "";
	
	function __construct()
	{
     	$this->Header = 'MIME-Version: 1.0' . "\r\n";
     	$this->Header .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	}
	
	function Send()
	{
		$bOK = mail($this->Destinataire, $this->Sujet, $this->Message, $this->Header);
		if (!$bOK)
		{
			echo "Erreur lors de l'envoi du mail.";
		}

	}
}
?>
