<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'halamile_cashbac');
define('DB_PASSWORD', 'Cashback786');
define('DB_SCHEMA', 'halamile_cashback');

require_once dirname(__FILE__) . '/classes/db.class.php'; 
if (!$GLOBALS['DB'] = new DB(DB_HOST, DB_USER, DB_PASSWORD, DB_SCHEMA))  die('Error: Unable to connect to database server.');


// ---------------------------- CJ Setting Starts Here
static $cj_developerKey		= 	'7qq297bm7x0qgg7f0cvgg8asms';
static $cj_websiteId		= 	'9122022';
static $Awin_key		    = 	'71a60863-3e28-48a1-8b6b-5867cd0e5d83';





