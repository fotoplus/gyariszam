<?php
$user = false;
$name = false;
$location = false;


/**
 * Ha van POSt user és location, akkor ezeket egy JSON formátumú sütibe tesszük.
 * 
 */
if (isset($_POST['action']) && $_POST['action'] == 'login') {
	$name = $_POST['user'];
	$location = $_POST['location'];
	$cookie = json_encode(array('name' => $name, 'location' => $location));
	setcookie('user', $cookie, time() + 60 * 60 * 24 * 30, '/');
	header('Location: ' . $_SERVER['REQUEST_URI']);
}

/**
 * Ha van QUERY_STRING és az logout, akkor töröljük az user sütit,
 * és visszairányítjuk a felhaszálót a REQUEST_URI QUERY_STRING nélküli részére.
 */
if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] == 'logout') {
	setcookie('user', '', time() - 3600, '/');
	header('Location: ' . str_replace('?logout', '', $_SERVER['REQUEST_URI']));
	$user = false;
}


/**
 * Ha nincs 'user' süti, vagy az üres, akkor a $user változót false-ra rakjuk,
 * ha van, akkor a JSON formátumu sütiből kivesszük a 'name' és 'location' értékét.
 * 
 */
if (!isset($_COOKIE['user']) or $_COOKIE['user'] == '') {
	$user = false;
} else {
	$cookie = json_decode($_COOKIE['user'], true);
	$name = $cookie['name'];
	$location = $cookie['location'];
	$user = true;
}

