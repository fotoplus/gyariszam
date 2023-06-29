<!-- vissza a főoldalra -->
<a class="vissza" href="<?php echo URL_PREFIX; ?>">Vissza</a>
<?php

// A telephely összes gyáriszáma

$query = $conn->query("SELECT * FROM gyariszam.gyariszamok WHERE location LIKE '{$location}' ORDER BY created ASC");
if ($query->num_rows > 0) {
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
			</tr>
<?php
	}
?>
		</tbody>
	</table>
<?php


}

?>
<!-- vissza a főoldalra -->
<a class="vissza" href="<?php echo URL_PREFIX; ?>">Vissza</a>