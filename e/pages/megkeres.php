<?php 
// Törlés

if(
	(isset($_POST['action']) AND $_POST['action'] == 'delete')
	AND
	(isset($_POST['action2']) AND $_POST['action2'] == 'delete')
) {

	$id = $_POST['id'];
	$query = "DELETE FROM gyariszam.gyariszamok WHERE id = '$id'";
	$conn->query($query);
	header('Location: ' . $_SERVER['REQUEST_URI']);
	exit;
}

?>

<form id="form" method="post">
	<input type="hidden" name="action" value="show-product">
	<div>
		<label for="product">A terméknek a  vonalkódja vagy cikkszáma</label>
		<input type="text" name="search" id="product" required>
	</div>
	<button type="submit">Mutasd</button>
	<a class="vissza" href="<?php echo URL_PREFIX; ?>/">Vissza</a>
</form>
<?php

$show = false;
if(isset($_POST['action']) and $_POST['action'] == 'show-product' and isset($_POST['search'])) {
	$search = trim($_POST['search']);

	 $query ="SELECT ANEV,ACIKK,BBARCODE FROM eprofit.termekek WHERE FIND_IN_SET('$search', REPLACE(BBARCODE, ';', ',')) > 0 OR ACIKK LIKE '".$search."'";
	 $query = $conn->query($query);

	 // Ha a $query 1 sort visszaad, akkor megjelenítjük a termék adatait.
	if($query->num_rows == 1) {
		$search_product = $query->fetch_assoc();

		// Eltároljuk az eredményt a search sütiben
		setcookie('search', json_encode($search_product), time() + 300, '/');
	}
} else {

	// ha van search süti, és az adat json akkor beállítjuk mint $search_product
	if(isset($_COOKIE['search']) and !empty($_COOKIE['search'])) {
		$search_product = json_decode($_COOKIE['search'], true);
	} else if(isset($_COOKIE['product']) and !empty($_COOKIE['product'])) {
		$search_product = json_decode($_COOKIE['product'], true);
	}



}


if(isset($search_product) and !empty($search_product)) {
	?>
		<div class="product">
			<div class="product__name"><?php echo $search_product['ANEV']; ?></div>
			<div class="product__vpn"><?php echo $search_product['ACIKK']; ?></div>
			<div class="product__ean"><?php echo $search_product['BBARCODE']; ?></div>
		</div>
	<?php


	$vpn = $search_product['ACIKK'];
	$query = 'SELECT * FROM gyariszam.gyariszamok WHERE vpn LIKE "'.$vpn.'" AND `location` LIKE "'.$location.'"';
	$query = $conn->query($query);

	if($query->num_rows > 0) {
		$show = true;
	} else {
		// Nincs ilyen termék a telephelyen
		?>
			<div class="product">
				<div class="product__name">Nincs még ilyen termék ezen a telephelyen</div>
			</div>
		<?php
	}
}

if($show) {


	// táblázatban kirakjuk a következő mezőket:
	// id, vpn, sn, name, location, created
	?>
		<table>
			<thead>
				<tr>
					<th>id</th>
					<th>vpn</th>
					<th>sn</th>
					<th>name</th>
					<th>location</th>
					<th>created</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
	<?php
		while ($row = $query->fetch_assoc()) {
	?>
				<tr>
					<td><?php echo $row['id']; ?></td>
					<td><?php echo $row['vpn']; ?></td>
					<td><?php echo $row['sn']; ?></td>
					<td><?php echo $row['name']; ?></td>
					<td><?php echo $row['location']; ?></td>
					<td><?php echo $row['created']; ?></td>
					<td>
						<!-- Törlés form -->
						<form id="to-delete" method="post">
							<input type="hidden" name="action" value="delete">
							<?php
								$delete2=false;
								if(isset($_POST['id']) AND $_POST['id'] == $row['id'] ) { 
									$delete2=true; 
									?>
										<input type="hidden" name="action2" value="delete">
									<?php
								} 
								 ?>
							<input type="hidden" name="id" value="<?php echo $row['id']; ?>">

							<button type="submit" <?php if($delete2) { echo 'class="torles"'; } ?>>Törlés</button>
						</form>
					</td>
				</tr>
	<?php
		}
	?>
			</tbody>
		</table>
	<?php

	?>
	<!-- vissza a főoldalra -->
	<a class="vissza" href="<?php echo URL_PREFIX; ?>">Vissza</a>

<?php

}

?>