<?php

/** PHP hibakijelzés
 * 
 * Bővebben: https://www.php.net/manual/en/errorfunc.configuration.php
 *
 */
#error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
error_reporting(E_ALL);
ini_set("display_errors", 1); 


/** ParseURI / URI_IGNORE
 * 
 * Az URI elejének figyelmenkívülhagyása, ha szükséges
 * Bővebben: /e/modules/parse_uri/README.md
 * 
 */
define("URI_IGNORE", 2);
define("URL_PREFIX", "/~matyi/gyariszam");



/** Title
 * 
 * Az oldal neve
 * 
 */
$title = "Gyáriszámok";


/** REDIRECT_URL
 * 
 * Elutasított hozzáférés esetén ide irányítja a látogatót.
 * 
 * Formátum: https://valami.hu
 * 
 */
define("REDIRECT_URL", "/hiba/403");



?>