<?php
/************************************\
|**      OpenBobX Interpreter      **|
|** Making WebDev hell since 1982! **|
|**    (c) Henri Watson 2010       **|
\************************************/

// General Configuration
$_BOBX['page'] = 'index'; // Set to the page you want to load by default
$_BOBX['pagevariable'] = 'page'; // Use as a GET variable to override $bobx['page']
$_BOBX['enablephp'] = true; // Change to allow use of normal PHP

/* DO NOT TOUCH BELOW THIS LINE UNLESS YOU WANT BOB TO TAKE HIS SERVERS BACK */

/* Validation of data */
if (isset($_GET[$_BOBX['pagevariable']])) { // Check if the page override is set
	$_BOBX['page'] = $_GET[$_BOBX['pagevariable']]; // If it is, use it.
}

if (!ctype_alpha($_BOBX['page'])) { // If the page name contains a non-alphabetic character...
	header("HTTP/1.1 404 Not Found");
	die('xboberrorcode 1:PAGE NAME CONTAINS NON-ALPHABETIC CHARACTER'); // Yell at the user
}

if (!file_exists($_BOBX['page'].'.bobx')) { // If the page does not exist...
	header("HTTP/1.1 404 Not Found");
	die('xboberrorcode 2:PAGE DOES NOT EXIST'); // Yell at the user
}

/* Load BobX file */
$_BOBX['rawfile'] = file_get_contents($_BOBX['page'].'.bobx');

/* Start parsing the file */
$_BOBX['parsedfile'] = $_BOBX['rawfile'];

if (!$_BOBX['enablephp']) { // If normal PHP is off
	if (preg_match("-\<\?php (.*) \?>-", $_BOBX['rawfile'])) { // If the code contains PHP...
		die('xboberrorcode 59:INVALID FUNCTION CALL FOUND'); // Yell at the user! (Are you starting to get the hang of this?)
	}
}

// Parse xbobprint tags
$_BOBX['parsedfile'] = preg_replace("-\<xbobprint\>(.*)\<\/xbobprint\>-", "<?php sleep(1); echo '$1'; ?>", $_BOBX['parsedfile']);

// Parse xbobdefine tags
$_BOBX['parsedfile'] = preg_replace("-\<xbobdefine (.*)=\"(.*)\" \>-", "<?php \$$1 = \"$2\"; sleep(2); ?>", $_BOBX['parsedfile']);

// Parse xbobif tags
$_BOBX['parsedfile'] = preg_replace("-\<xbobif condition=\"(.*)\">(.*)\<xbobendif>-", "<?php sleep(5); if ($1) {?>$1<? } ?>", $_BOBX['parsedfile']);

/* Pass the parsed file over to PHP */

eval('?>'.$_BOBX['parsedfile'].'<?');