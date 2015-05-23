<?php
require ("config.inc.php");
require ("global.inc.php");

// SMARTY SETTINGS
require ("smarty/Smarty.class.php");
require ("smarty/SmartyPaginate.class.php");
$smarty = new Smarty;
$smarty->force_compile = true;
$smarty->debugging = false;
$smarty->caching = true;
$smarty->cache_lifetime = 120;
$smarty->template_dir = "templates";
$smarty->compile_dir = SYSTEM_DOCUMENT_ROOT."/library/smarty/templates_c";
$smarty->cache_dir = SYSTEM_DOCUMENT_ROOT."/library/smarty/cache";
$smarty->left_delimiter = "<{";
$smarty->right_delimiter = "}>";

// DATABASE CONNECTION
require ("db.inc.php");
$db = new db;

// COMMON CLASSES
require ("classes/Common.php");
$Common = new Common;
$smarty->assign('Common', new Common);

require ("classes/Navigation.php");
$Navigation = new Navigation;
$smarty->assign('Navigation', new Navigation);

// SPECIAL CLASSES
require ("classes/Member.php");
$Member = new Member;
$smarty->assign('Member', new Member);

require ("classes/Bookmark.php");
$Bookmark = new Bookmark;
$smarty->assign('Bookmark', new Bookmark);
?>