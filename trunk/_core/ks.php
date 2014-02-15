<?php

/**
* 基礎功能庫，這個類別是靜態方法庫，內部的方法全都是靜態可以直接執行，會放在這類別內的方法
* 必須是為系統而用且常被使用的方法，如果某方法只為少數特定而用，它應該只是一般函數
* 會放置在ks內是因為這樣可以很方便的使用IDE的智慧語法功能
*/
class ks
{
	const GET = 1;
	const POST = 2;
	const GETPOST = 3;
	const SESSION = 4;
	const COOKIE = 5;
	const SERVER = 6;
	const ENV = 7;
	const REQUEST = 8;

	

	/**
	 * 為了讓模組開發時可以存取外部的變數
	 * @param string $type 可選 設定要取得的外部變數類型，如果不設定則會自行嘗試get或post兩種類型是否存在值
	 * @param string $name
	 * @param bool $filterhtml 是否過濾html特殊字元
	 * @return string array null
	 */
	static function external_var($name, $type = null, $filterhtml = false, $addslashes = false)
	{
		$data = null;
		switch ($type)
		{
			case self::GET:		$data = isset($_GET[$name])		? $_GET[$name]		: null; break;
			case self::POST:	$data = isset($_POST[$name])	? $_POST[$name]		: null; break;
			case self::SESSION:	$data = isset($_SESSION[$name]) ? $_SESSION[$name]	: null; break;
			case self::COOKIE:	$data = isset($_COOKIE[$name])	? $_COOKIE[$name]	: null; break;
			case self::SERVER:	$data = isset($_SERVER[$name])	? $_SERVER[$name]	: null; break;
			case self::ENV:		$data = isset($_ENV[$name])		? $_ENV[$name]		: null; break;
			case self::REQUEST:	$data = isset($_REQUEST[$name]) ? $_REQUEST[$name]	: null; break;
			
			case self::GETPOST:
			case null:
				$data = isset($_GET[$name]) ? $_GET[$name] : $data = isset($_POST[$name]) ? $_POST[$name] : null;
			break;
			default:
				return 'Please Set the correct type..';
			break;
		}
		
		if($data == null)
			return false;

		if($filterhtml)
			$data = arr_htmlspecialchars($data);
			
		if($addslashes)
			$data = arr_addslashes($data);
	

	
		return $data;
	}

}

