<?php
session_start();
define('ABSPATH', __DIR__.'/');
define('INC_PATH',ABSPATH. 'includes/');
define('FUNC_PATH',INC_PATH. 'func/');

require INC_PATH . 'config.php';
require INC_PATH . 'jdf.php';
date_default_timezone_set(SITE_TIMEZONE);
require INC_PATH . 'database.php';
require INC_PATH . 'functions.php';
require FUNC_PATH . 'func-router.php';
require FUNC_PATH . 'func-database.php';