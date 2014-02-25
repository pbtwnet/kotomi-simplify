<?php



?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Kotomi-Simplify</title>

</head>

<body>
<p>您現在在執行的是 install.php</p>
<p>這個工具您應該在使用完畢後將整個 install資料夾刪除</p>
<form id="form1" name="form1" method="post" action="">
<table width="100%" border="0" cellpadding="2" cellspacing="1">
  <tr>
    <th colspan="3">資料庫</th>
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
    <td><input type="text" name="pw" id="pw" /></td>
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
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
</form>

</body>
</html>