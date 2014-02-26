<?php
define('USE_SMARTY', true);
$root_path = './';
include($root_path . '_core/core.php');


$user->begin();
$smarty->display('body.htm');