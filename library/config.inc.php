<?
// ENVIRONMENT SETTINGS
DEFINE('ENVIRONMENT_URL_LOCALHOST', 'localhost');
DEFINE('ENVIRONMENT_URL_LIVE', 'beta.skaratech.com');

switch ($_SERVER['SERVER_NAME']) {
	case ENVIRONMENT_URL_LOCALHOST:
		DEFINE('_DB_DBNAME', 'db_mindvalley');
		DEFINE('_DB_DBUSER', 'root');
		DEFINE('_DB_DBPASS', '');
		DEFINE("SYSTEM_DOCUMENT_ROOT", "X:/xampp/htdocs/sites/mindvalley");
		DEFINE("SYSTEM_SITE_URL", "http://".$_SERVER['HTTP_HOST']."/sites/mindvalley");
		DEFINE("API_ENCRYPT", true);
		//error_reporting(0);
		break;
	default:
	case ENVIRONMENT_URL_LIVE:
		DEFINE('_DB_DBNAME', 'db_mindvalley');
		DEFINE('_DB_DBUSER', 'mindvalley');
		DEFINE('_DB_DBPASS', 'mindvalley123');
		DEFINE("SYSTEM_DOCUMENT_ROOT", "D:/xampp/htdocs/beta.skaratech.com.my/mindvalley");
		DEFINE("SYSTEM_SITE_URL", "http://".$_SERVER['HTTP_HOST']);
		DEFINE("API_ENCRYPT", true);
		//error_reporting(0);
		break;
}

// DATABASE SETTINGS
DEFINE('_DB_DBTYPE', 'mysqlt');
DEFINE('_DB_DBHOST', 'localhost');
DEFINE('_DB_DBDEBUGMODE', false);
DEFINE('_DB_TRANSACTION', true);
DEFINE('_DB_LOGSTATEMENT', true);

// SITE SETTINGS
session_start();
session_set_cookie_params(14400);
DEFINE("SYSTEM_SECRET_KEY", "mindvalley123");
DEFINE("SYSTEM_DEBUG_IP", "");
DEFINE("SYSTEM_ACCESS_IP", "");
DEFINE("SYSTEM_NAME", "Prototype System for Mindvalley");
DEFINE("SYSTEM_TINY_URL_HOST", "http://munster.me");
DEFINE("SYSTEM_PREVIEW_URL_HOST", "http://preview.munster.me");
DEFINE("SYSTEM_RESULTS_PER_PAGE", 10);
?>