<?php


/**
 * 這個方法會返回一個用戶的資料陣列(含index and key)
 * 如果找不到指定的資料將返回假
 * 如果參數 $user_id <= 1 將會拋出錯誤
 * @param int $user_id
 * @return array
 */
function get_userdata($user_id)
{
	global $db;

	$data = array();

	if(intval($user_id) <= 1)
		trigger_error('user_id <= 1', E_USER_ERROR);

	$sql = 'SELECT * FROM ' . T_USERS . ' WHERE user_id = ?';
	if ($sth = $db->prepare($sql))
	{
		$sth->execute([intval($user_id)]);
		$data = $sth->fetch();
		if(count($data) == 0)
		{
			$sth->closeCursor();
			return false;
		}
		$sth->closeCursor();
	} else trigger_error($sql, E_USER_ERROR);
	return $data;
}


function arr_htmlspecialchars($string)
{
	if(is_array($string))
	{
		foreach($string as $key => $val)
			$string[$key] = arr_htmlspecialchars($val);
	}
	else
	{
		$string = trim(htmlspecialchars(str_replace(array("\r\n", "\r", "\0"), array("\n", "\n", ''), $string), ENT_COMPAT, 'UTF-8'));
	}
	return $string;
}


/**
 * 這個方法仍需保留，因為處理外部變數仍有需要使用到這個方法，但這方法已經不再判斷 get_magic_quotes_gpc
 * 資料加入斜線
 * @param string|array $string
 * @return string|array
 */
function arr_addslashes($string)
{
	if(is_array($string))
	{
		foreach($string as $key => $val)
			$string[$key] = arr_addslashes($val);
	}
	else
	{
		$string = addslashes(trim($string));
	}
	return $string;
}

/**
 * 傳回一個全域唯一識別GUID 不含左右大括號
 * @link http://www.php.net/manual/en/function.uniqid.php#107512
 * @param string $namespace 可選，增加混淆的字串
 * @return string 不含左右大括號的GUID
 */
function new_guid($namespace = '')
{
	$guid = '';
	$uid = uniqid("", true);
	$data = $namespace;
	$data .= $_SERVER['REQUEST_TIME'];
	$data .= $_SERVER['HTTP_USER_AGENT'];
	$data .= $_SERVER['REMOTE_ADDR'];
	$data .= $_SERVER['REMOTE_PORT'];
	$hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
	$guid = substr($hash,  0,  8) . '-' . substr($hash,  8,  4) .
	'-' . substr($hash, 12,  4) . '-' . substr($hash, 16,  4) .
	'-' . substr($hash, 20, 12);
	return $guid;
}