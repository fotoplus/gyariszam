<?php

$cli=false;

/**
 * Itt vannak az oldalak fájljai.
 */
$pages_dir = 'e/pages/';


	/**
	 * Feldaraboljuk az URL-t a / (per) jelek mentén egy tömbbe,
	 * ezek lesznek a szegmensek és egy $segments nevű tömbbe kerülnek.
	 * Az első szegmens mindig egy oldal (fájl) lesz.
	 */
	$_SERVER['REQUEST_URI_PATH'] = preg_replace('/\?.*/', '', $_SERVER['REQUEST_URI']);
	$segments = array_slice (explode('/', trim($_SERVER['REQUEST_URI_PATH'], '/')), URI_IGNORE);

	/**
	 * Az oldal "neve" az első (0.) szegmens rész, ha nincs ilyen,
	 * mert nincs semmi az első / után akkor a kezdőlap jelenik meg,
	 * ami a 'home'
	 */
	$page['name'] = !empty( $segments[0] ) ? $segments[0] : 'home';

	/**
	 * Az előzőből lesz a fájl, a mappájával és .php kiterjesztéssel.
	 */
	$page['file'] = $pages_dir . $page['name'] . '.php';

	/**
	 * Itt ellenőrizzük, ogy létezik-e a keresett fájl, ha nem, akkor egy hibaoldalra irányítjuk.
	 * 
	 */
	if(!file_exists($page['file']) ) {
		die('Nincs ilyen oldal!');
	} else {
		include($page['file']);
	}

?>