<?php

// 這是強迫使用者只能執行在 php5.4 以上
if (!version_compare(PHP_VERSION, '5.4.0-dev', '>='))
{
	printf("Kotomi-Simplify need php 5.4 your php is %s", PHP_VERSION);
	exit();
}

// 獲取現在的目錄名稱
define('KS_CORE_DIR', substr(strrchr(str_replace("\\", "/", pathinfo(__FILE__, PATHINFO_DIRNAME)), "/"), 1) . "/");

// 定義資料表名稱常數，如果需要多個系統同資料庫可以透過更改資料表字首方式達成
define('T_USERS',			'ks_users');
define('T_CONFIG',			'ks_config');
define('T_SESSIONS',		'ks_sessions');
define('T_SESSIONS_KEYS',	'ks_sessions_keys');

error_reporting(E_ALL & ~E_NOTICE);



if (file_exists($root_path . KS_CORE_DIR . 'db.config.php'))
{
	require($root_path . KS_CORE_DIR . 'db.config.php');
}

/*****************************************************
class config
{
	var $data = array(
			
		'session_length'	=> '300',
			
		'cookie_expire'		=> '2592000',
		'cookie_name'		=> 'ks',
		'cookie_domain'		=> '',
		'cookie_path'		=> '/',
		'cookie_secure'		=> '0',
			
		'timezone' 			=> 'Asia/Taipei'
	);
}
******************************************************/




// 載入定義與類別庫
require_once($root_path . KS_CORE_DIR . 'functions.php');
require_once($root_path . KS_CORE_DIR . 'ks.php');
require_once($root_path . KS_CORE_DIR . 'config.php');
require_once($root_path . KS_CORE_DIR . 'sessions.php');
require_once($root_path . 'smarty_libs/Smarty.class.php');

$dsn = 'mysql:host=' . $dbhost . ';dbname=' . $dbname;
$options = [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'];


try {
	$db = new PDO($dsn, $dbuser, $dbpasswd, $options);
} catch (PDOException $e) {
	echo $e->getMessage();
	exit;
}
unset($dbpasswd);


$conf = new config;
$user = new sessions();
$smarty = new Smarty;
$smarty->caching = false;
$smarty->setTemplateDir($root_path . 'templates/');
$smarty->setCompileDir($root_path . 'templates_c/');
