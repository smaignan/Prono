<?php
/*
 *    fichier  :  IBDD.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  13 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

interface IBDD
{
	public function connect();
	
	public function query($sql);
	
	public function fetch_array($resultat);
	
	public function close();
	
	public function execute();
}
?>
