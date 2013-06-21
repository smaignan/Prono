<?php
/*
 *    fichier  :  Page.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  11 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

abstract class Page
{
	var $s_Contenu = "";
	
	function __construct()
	{
		$s_Contenu = "sss";
	}
	
	public function Affiche()
	{
		echo "ddd : ".$s_Contenu."<br>";
	}
}
?> 