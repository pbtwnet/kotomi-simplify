<?php

$root_path = './';
include($root_path . '_core/core.php');


$user->begin();


$smarty->display('test_body.htm');