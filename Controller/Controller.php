<?php
/*
 *    fichier  :  Controller.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  17 janv. 08
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

abstract class Controller
{
	private $oData = array();
	protected $request = null;
	
	public function setRequest(Request $request)
	{
		$this->request = $request;
	}
	
	protected function GetParam($key)
	{
		return $this->request->GetParam($key);
	}
	
	protected function AddData($key, $value)
	{
		$this->oData[$key] = $value;
	}
	
	public function GetData()
	{
		return $this->oData;
	}
	
	public static function Redirect($module)
	{
		header("Location:?Module=$module");
	}
}
?>
