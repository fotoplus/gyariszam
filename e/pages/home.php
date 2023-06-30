<nav>
	<ul>
		<li>
			<a href="<?php echo URL_PREFIX;  ?>/ad">Gyáriszámok hozzáadása termékekhez</a>
		</li>
		<li>
			<a href="<?php echo URL_PREFIX;  ?>/mutat">Gyáriszámok mutatása</a>
		</li>
		<li>
			<a href="<?php echo URL_PREFIX;  ?>/megkeres">Megkeresi a terméket, hogy kitörölhesd</a>
		</li>
	</ul>
</nav>

<hr>

<h1>Statisztikailag</h1>

<h2>Aki a leghamarabb kezdte</h2>
<table>
	<?php

		$query_stat = ('SELECT name FROM gyariszam.gyariszamok GROUP BY name');
		$result_stat = $conn->query($query_stat);
		while ($row_stat = $result_stat->fetch_assoc()) {
			echo '<tr>';
			echo '<td>' . $row_stat['name'] . '</td>';
			echo '</tr>';
		}

	?>

</table>



<h2>Aki a legtöbbet</h2>
<table>
	<?php

		$query_stat = ('SELECT name, COUNT(name) AS mennyi FROM gyariszam.gyariszamok GROUP BY name ORDER BY COUNT(name) DESC');
		$result_stat = $conn->query($query_stat);
		while ($row_stat = $result_stat->fetch_assoc()) {
			echo '<tr>';
			echo '<td>' . $row_stat['name'] . ' </td>';
			echo '<td>' . $row_stat['mennyi'] . ' pitty</td>';
			echo '</tr>';
		}

	?>

</table>




