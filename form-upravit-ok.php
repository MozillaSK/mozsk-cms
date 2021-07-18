<?php
	$uprav_id = intval($_POST['param1']);


	$wpdb->query(
		$wpdb->prepare(
			"UPDATE ".$wpdb->prefix."produkty SET
				urlid = %s,
				nazov = %s,
				datum = %s,
				verzia = %s,
				changelog = %s,
				download_win = %s, velkwin = %s,
				download_lin = %s, velklin = %s,
				download_mac = %s, velkmac = %s,
				download_port = %s, velkport = %s,
				poznamka = %s
				WHERE id = %d",
				$_POST['urlid'],
				$_POST['nazov'],
				$_POST['datum'],
				$_POST['verzia'],
				$_POST['changelog'],
				$_POST['download_win'], $_POST['velkwin'],
				$_POST['download_lin'], $_POST['velklin'],
				$_POST['download_mac'], $_POST['velkmac'],
				$_POST['download_port'], $_POST['velkport'],
				$_POST['poznamka'],
				$uprav_id
		)
	);
?>

<div class="updated">
	<p><strong>Produkt upravený. (ID=<?php echo $uprav_id ?>)</strong></p>
</div>

<?php require_once("form-zoznam.php"); ?>
