<?php

$root_path = './';
include($root_path . '_core/core.php');


$user->begin();

$logon = ks::external_var('logon');
$lout = ks::external_var('lout');
if($logon == 1)
{
	$auto = ks::external_var('autologon') == '1' ? true : false;
	$user->login(ks::external_var('id'), ks::external_var('pw'), $auto);
	
}
if($lout == 1)
{
	header("location: index.php");
	$user->logout();
	exit;
}


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Kotomi-Simplify</title>
</head>

<body>
<?php if($user->data['user_id'] == 1) { ?>
<div style="background:#CCC; width:300px">
  <form id="form1" name="form1" method="post" action="">
    登入
      <input name="logon" type="hidden" id="logon" value="1" />
    <br />
  帳號:<input type="text" name="id" id="id" />
  <br />
  密碼:<input type="text" name="pw" id="pw" />
  <br />
  自動登入:
  <input name="autologon" type="checkbox" id="autologon" value="1" />
  <input type="submit" name="button" id="button" value="送出" />
  </form>
  <div style="text-align:right">這只是一個用來測試登入的表單</div>
</div>
<?php } else { ?>
您是: <?=$user->data['user_name']?> 
<a href="index.php?lout=1">可以點選這裡登出</a>
<?php } ?>
</body>
</html>