<?php
/*
 *    fichier  :  UserInfo.php
 *    version  :  1.0
 *    auteur   :  Sylvain
 *    date     :  13 Novembre 2008
 *
 *    description      :
 *    modification     :	auteur	date	modification
*/

$aUserInfo = array();

class UserInfo
{
	public static function set($key, $value)
	{
		global $aUserInfo;

		setcookie($key, $value);
		$aUserInfo[$key] = $value;
		//$_SESSION[$key] = $value;
	}

	public static function get($key)
	{
		global $aUserInfo;

		if (isset($aUserInfo[$key]))
			return $aUserInfo[$key];
		else
			return $_COOKIE[$key];
		//return $_SESSION[$key];
	}

	public static function is_set($key)
	{
		global $aUserInfo;

		if (isset($aUserInfo[$key]))
			return true;
		else
			return isset($_COOKIE[$key]);
		//return isset($_SESSION[$key]);
	}

	public static function destroy()
	{
		while (list($key, $value) = each($_COOKIE))
		{
			UserInfo::un_set($key);
		}
		//session_destroy();
	}

	public static function un_set($key)
	{
		global $aUserInfo;

		setcookie($key, '', time() - 3600);
		array_diff_key(array($key => 0), $aUserInfo);
		//unset($_SESSION[$key]);
	}
}
?>
