<?php
error_reporting(E_ALL & ~E_NOTICE);

$show_form = true;
$show_msgbox = false;
$msg = '';



if($_POST['mode'] == 'test' ||$_POST['mode'] == 'install')
{
	$mode		= $_POST['mode'];
	$dbhost		= $_POST['db_host'];
	$dbname		= $_POST['db_name'];
	$dbpasswd	= $_POST['pw'];
	$dbuser		= $_POST['id'];
	$table_prefix = $_POST['table_prefix'];
	
	$show_form = false;
	$show_msgbox = true;
	
	$dsn = 'mysql:host=' . $dbhost . ';dbname=' . $dbname;
	$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
	
	@header("Refresh: 3; url=install.php");
	
	try {
		$db = new PDO($dsn, $dbuser, $dbpasswd, $options);
	} catch (PDOException $e) {
		$msg = $e->getMessage();
	}
	
	if($mode == 'test')
	{
		
		if($msg == '')
		{
			$msg = '這個設定目前看來應該是正確的 您可以點 <a href="install.php">這裡</a> 返回';
		}
		
	}
	if($mode == 'install' && $msg == '')
	{
		$sql = 'CREATE TABLE ' . $table_prefix . 'config (config_name varchar(255) NOT NULL, config_value varchar(255) NOT NULL);';
		if (!$db->query($sql))
			trigger_error($sql, E_USER_ERROR);
		
		$sql = 'CREATE TABLE ' . $table_prefix . 'sessions (
			  session_id char(36) binary NOT NULL,
			  session_user_id mediumint(8) unsigned NOT NULL DEFAULT \'0\',
			  session_start int(11) unsigned NOT NULL,
			  session_last_time int(11) unsigned NOT NULL DEFAULT \'0\',
			  session_ip varchar(40) binary NOT NULL,
			  session_page varchar(255) binary NOT NULL,
			  PRIMARY KEY (session_id),
			  KEY session_user_id (session_user_id,session_last_time))';
		if (!$db->query($sql))
			trigger_error($sql, E_USER_ERROR);
		
		$sql = 'CREATE TABLE ' . $table_prefix . 'sessions_keys (
			  key_id char(36) binary NOT NULL,
			  user_id mediumint(8) unsigned NOT NULL,
			  add_time int(11) unsigned NOT NULL DEFAULT \'0\',
			  last_login int(11) unsigned NOT NULL DEFAULT \'0\',
			  last_ip varchar(40) binary NOT NULL,
			  PRIMARY KEY (key_id,user_id),
			  KEY last_login (last_login))';
		if (!$db->query($sql))
			trigger_error($sql, E_USER_ERROR);
		
		$sql = 'CREATE TABLE ' . $table_prefix . 'users (
			  user_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
			  user_enable enum(\'N\',\'Y\') NOT NULL DEFAULT \'N\',
			  user_level tinyint(2) unsigned NOT NULL DEFAULT \'0\',
			  user_name varchar(255) binary NOT NULL,
			  user_password varchar(32) binary NOT NULL,
			  user_regdate int(11) unsigned NOT NULL DEFAULT \'0\',
			  last_login int(11) unsigned NOT NULL DEFAULT \'0\',
			  PRIMARY KEY (user_id),
			  UNIQUE KEY user_name (user_name))';
		if (!$db->query($sql))
			trigger_error($sql, E_USER_ERROR);
		
		$sql ='INSERT INTO ' . $table_prefix . 'users  VALUES (1, \'Y\', 0, \'anonymous\', \'\', 0, 0)';
		if (!$db->query($sql))
			trigger_error($sql, E_USER_ERROR);
		

		$sql ='INSERT INTO ' . $table_prefix . 'config VALUES';
		$sql .='(\'cookie_domain\', \'\'),';
		$sql .='(\'cookie_expire\', \'2592000\'),';
		$sql .='(\'cookie_name\', \'ks\'),';
		$sql .='(\'cookie_path\', \'/\'),';
		$sql .='(\'cookie_secure\', \'0\'),';
		$sql .='(\'session_length\', \'300\'),';
		$sql .='(\'timezone\', \'Asia/Taipei\');';
		if (!$db->query($sql))
			trigger_error($sql, E_USER_ERROR);
		
		$msg = '動作完成 您可以點 <a href="install.php">這裡</a> 返回';
	}
	
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Kotomi-Simplify</title>
<script type="text/javascript" src="jquery-2.1.0.js"></script>

<script type="text/javascript">

$(function() {
	$("#button").click(function() {
		var db_host = $("#db_host").val();
		var db_name = $("#db_name").val();
		var id = $("#id").val();
		var pw = $("#pw").val();
		var msg = '';
		if(db_host == '' || db_name == '' || id == '') {
			if(db_host == '')
				msg = '資料庫位置';
			if(db_name == '')
				msg = '資料庫名稱';
			if(id == '')
				msg = '使用者帳號';
			if(pw == '')
				msg = '使用者密碼';
			alert("請正確填寫必要資料: " + msg);
			return false;
		}

	});
});

</script>

</head>

<body>
<?php if($show_msgbox) {?>
<table width="900" border="0" align="center" cellpadding="2" cellspacing="1">
  <tr>
    <th>訊息</th>
  </tr>
  <tr>
    <td align="center"><?=$msg?></td>
  </tr>
</table>
<?php } ?>
<?php if($show_form) {?>
<p>您現在在執行的是 install.php</p>
<p>這個工具您應該在使用完畢後將整個 install 資料夾刪除</p>
<form id="form1" name="form1" method="post" action="">
<table width="100%" border="0" align="center" cellpadding="2" cellspacing="1">
  <tr>
    <th colspan="3">資料庫</th>
  </tr>
  <tr>
    <td>資料庫位置</td>
    <td><input name="db_host" type="text" id="db_host" value="localhost" /></td>
    <td>通常這個設定值預設都是 localhost。</td>
  </tr>
  <tr>
    <td>資料庫名稱</td>
    <td><input type="text" name="db_name" id="db_name" /></td>
    <td>設定您的資料庫名稱，資料庫您必須自行建立，這個工具並不會幫您建立。</td>
  </tr>
  <tr>
    <td>使用者帳號</td>
    <td><input type="text" name="id" id="id" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>使用者密碼</td>
    <td><input type="password" name="pw" id="pw" /></td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>測試/安裝</td>
    <td><label>
      <input name="mode" type="radio" id="radio" value="test" checked="checked" />
    測試
    <input type="radio" name="mode" id="radio2" value="install" />
安裝</label></td>
    <td>如果是選擇測試將只會測試是否可以正常連接到資料庫，如果不想測試可以直接選擇安裝即可</td>
  </tr>
  <tr>
    <th colspan="3">安裝選項</th>
    </tr>
  <tr>
    <td>資料表字首</td>
    <td><input name="table_prefix" type="text" id="table_prefix" value="ks_" /></td>
    <td>如果您變更這個就必須自行修改core.php內的資料表名稱</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><label>
      <input type="reset" name="button2" id="button2" value="重設" />
      <input type="submit" name="button" id="button" value="送出" />
    </label></td>
  </tr>
</table>
</form>
<?php } ?>
</body>
</html>