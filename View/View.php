<?php
/*
 *    fichier  :  View.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  17 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

abstract class View
{
	public function setParameters($parameters)
	{
		while (list($key, $value) = each($parameters))
		{
			$sVar = "_".$key;
			$this->$sVar = $value;
		}
	}
}
?>
