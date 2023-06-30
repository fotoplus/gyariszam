<?php


/**
 * Ellenőrizzük, van-e product süti, és hogy nem-e üres.
 * Ha nincs, vagy üres, akkor a $product false, amugy meg a süti értéke
 * 
 */
if (!isset($_COOKIE['product']) or $_COOKIE['product'] == '') {
	$product = false;
} else {
	$product = $_COOKIE['product'];
	$product = json_decode($product, true);
}



if(!$product):

	/**
	 * Ha nincs termék akkor bekérjük a terméknek a vonalkódját,
	 * és ellenőrizzük az eprofit adatbázis termekek táblájának BBARCODE mezőjében,
	 * hogy megtalálható-e a vonalkód.
	 * 
	 * A BBARCODE mezőben ha több vonalkód van megadva egy termékhez, akkor azok pontosvesszővel vannak elválasztva,
	 * és minden vonalkódot ellenőrizni kell.
	 * 
	 * Ha van találat, akkor a következő mezők értékét JSON formátumban elmentjük a product sütibe:
	 * - ANEV
	 * - ACIKK
	 * - BBARCODE
	 * 
	 */
	if (isset($_POST['action']) and $_POST['action'] == 'select-product' and isset($_POST['search'])) {
		$search = trim($_POST['search']);
		// Az ö -ket 0-ra kell cserélni
		$search = str_replace('ö', '0', $search);

		// Ellenőrizzük, hogy a termék létezik-e az adatbázisban
		//$products = $conn->query("SELECT ANEV,ACIKK,BBARCODE FROM eprofit.termekek WHERE BBARCODE LIKE '%$ean%'");
		$products = $conn->query("SELECT ANEV,ACIKK,BBARCODE FROM eprofit.termekek WHERE FIND_IN_SET('$search', REPLACE(BBARCODE, ';', ',')) > 0 OR ACIKK LIKE '".$search."';");

		// Ha van találat
		if ($products->num_rows > 0) {
			// Ha több találat van, akkor a legelsőt vesszük
			$product = $products->fetch_assoc();

			// A termék adatait JSON formátumban elmentjük a product sütibe
			setcookie('product', json_encode($product), time() + 3600, '/');

			// Átirányítjuk a felhasználót a termék hozzáadásához
			header('Location: ' . URL_PREFIX . '/ad');
			exit;
		} else {
			// Ha nincs találat, akkor hibaüzenetet jelenítünk meg
			echo '<div class="error">Nincs ilyen termék!</div>';
		}
	}
	

?>
<form id="form" method="post">
	<input type="hidden" name="action" value="select-product">
	<div>
		<label for="product">A termék vonalkódja vagy cikkszáma</label>
		<input type="text" name="search" id="product" required>
	</div>

	<button type="submit">Ez a termék</button>
	<a class="vissza" href="<?php echo URL_PREFIX; ?>/">Vissza</a>

</form>

<script>
	document.getElementById('product').focus();
</script>


<?php 
else:

?>
	<div class="product">
		<div class="product__name"><?php echo $product['ANEV']; ?></div>
		<div class="product__vpn"><?php echo $product['ACIKK']; ?></div>
		<div class="product__ean"><?php echo $product['BBARCODE']; ?></div>
	</div>
<?php

	/**
	 * Eltávolítjuk a termék adatait a sütiből, ha a QUERY_STRING == unset-product
	 * 
	 * 
	 */
	if($_SERVER['QUERY_STRING'] == 'unset-product'){
		setcookie('product', '', time() - 3600, '/');
		header('Location: ' . URL_PREFIX . '/ad');
		exit;
	}

	/**
	 * Visszavonjuk az utolsó termék hozzáadását, ha a QUERY_STRING == undo
	 * ehhez az adatbázisból kell törölni a rekordot a vpn sn érték pár alapján
	 * 
	 * 
	 * 
	 */
	if($_SERVER['QUERY_STRING'] == 'undo'){
		$last_product = $_COOKIE['last_product'];
		$last_product = json_decode($last_product, true);
		$conn->query("DELETE FROM gyariszam.gyariszamok WHERE vpn = '{$last_product['vpn']}' AND sn = '{$last_product['sn']}'");
		setcookie('last_product', '', time() - 5, '/');
		header('Location: ' . URL_PREFIX . '/ad');
		exit;
	}

	if(isset($_POST['action']) and $_POST['action'] == 'add' and isset($_POST['sn'])) {
		
		$sn = trim($_POST['sn']);
		// Az ö -ket 0-ra kell cserélni
		$sn = str_replace('ö', '0', $sn);

		// Ellenőrizzük, hogy a beolvasott érték nem-e véletlenül megint a vonalküdja a terméknek
		if($sn == $product['BBARCODE']){
			$is_ean = true;
		} else {
			$is_ean = false;
		}



		
		if (!isset($_POST['validated']) or $_POST['validated'] == '') {

			$sn_original = $sn;

			// Ha a beolvasott gyáiszám ?-el kezdődik, azt eltávolítjuk
			if(substr($sn, 0, 1) == '?'){
				$sn = substr($sn, 1);
			}

			// Alkalazzuk a ^.*?(?:(?<=21)(\d+)|21(.*)) regexet, hogy kinyerjük a gyáriszámot ha a vpn értéke 750-el kezdődik
			if(substr($product['ACIKK'], 0, 3) == '750'){
				preg_match('/^.*?(?:(?<=21)(\d+)|21(.*))/', $sn, $matches);
				if (!empty($matches)) {
					$sn = $matches[2];
				}
			}
			
			if(substr($product['ACIKK'], 0, 3) == '599') {
				$sn=str_replace('%', '', $sn);
			}

			$sn=str_replace(']C1', '', $sn);


			// Megjelenítjük a beolvasott és a korrigált gyáriszámot ellenőrzésre
			// És ellenőrizzük, szerepel e már az adatbázisban
			$sn_exists = $conn->query("SELECT sn FROM gyariszam.gyariszamok WHERE sn = '$sn' AND vpn = '{$product['ACIKK']}'");
			if ($sn_exists->num_rows > 0) {
				$sn_exists = $sn_exists->fetch_assoc();
				$sn_exists = $sn_exists['sn'];
			} else {
				$sn_exists = false;
			}

?>
			<div class="product__sn_group">
				<?php if ($sn_original != $sn): ?>
					<div class="product__sn_original"><?php echo $sn_original; ?></div>
				<?php endif; ?>
				<div class="product__sn"><?php echo $sn; ?></div>
				<?php if ($sn_exists): ?>
					<div class="error">Ez a gyáriszám már szerepel az adatbázisban!</div>
				<?php endif; ?>
				<?php if ($is_ean): ?>
					<div class="error">Ez mondjuk a termék vonalkódja...</div>
				<?php endif; ?>

			</div>

			<form method="post">
				<?php if(!$is_ean): ?>
				<input type="hidden" name="validated" value="<?php echo $sn; ?>">
				<input type="hidden" name="sn" value="<?php echo $sn; ?>">
				<button type="submit" name="action" value="add">Mentés</button>
				<?php endif; ?>
				<a class="vissza" href="<?php echo URL_PREFIX; ?>/ad">Mégse</a>
			</form>

<?php
		} else {
			// Ha a gyáriszám validálva van, akkor elmentjük az adatbázisba


			// Ellenőrizzük, hogy a gyáriszám már szerepel-e az adatbázisban ugyanehhez a termékhez
			$sn_exists = $conn->query("SELECT sn FROM gyariszam.gyariszamok WHERE sn = '$sn' AND vpn = '{$product['ACIKK']}'");

			// Ha még nem szerepel, akkor elmentjük
			if ($sn_exists->num_rows == 0) {
				$conn->query("INSERT INTO gyariszam.gyariszamok (`name`,`location`,`vpn`,`sn`) VALUES ('{$name}','{$location}','{$product['ACIKK']}','$sn')");
				// Elmentjük az utolsó vpn és sn értéket a last_product süttibe, hogy a mávelet visszavonható legyen, ez a süti 15 percig él csak
				setcookie('last_product', json_encode(array('vpn' => $product['ACIKK'], 'sn' => $sn)), time() + 900, '/');
				

				// Átirányítjuk a felhasználót a termék hozzáadásához
				header('Location: ' . URL_PREFIX . '/ad');
				exit;
			} else {
				// Ha már szerepel, akkor hibaüzenetet jelenítünk meg
				echo '<div class="error">Ez a gyáriszám már szerepel az adatbázisban!</div>';
				echo '<a class="vissza" href="' . URL_PREFIX . '/ad">Vissza</a>';
			}


		}



	} else {
?>

		<form method="post">
			<input type="hidden" name="action" value="ad">
			<label for="sn">Gyáriszám</label>
			<input type="text" name="sn" id="sn" required>
			<button type="submit" name="action" value="add">Hozzáad</button>
			<a class="btn reset" href="<?php echo $_SERVER['REQUEST_URI']; ?>?unset-product">Másik termék</a>
			<a class="vissza" href="<?php echo URL_PREFIX; ?>/">Vissza</a>

			<?php
				// Ha van last_product süti és az nem üres akkor megjelenítjük a visszavonás gombot
				if (isset($_COOKIE['last_product']) and $_COOKIE['last_product'] != '') {
			?>

			<a class="btn megsem" href="<?php echo URL_PREFIX; ?>/ad?undo">Előző visszavonása</a>
			<?php
			} ?>
		</form>

		<script>
			document.getElementById('sn').focus();
		</script>

<?php 
	}

endif; 

?>