<?php
	$wpdb->query(
		$wpdb->prepare(
			"INSERT INTO ".$wpdb->prefix."produkty (
				urlid,
				nazov,
				datum,
				verzia,
				changelog,
				download_win, velkwin,
				download_lin, velklin,
				download_mac, velkmac,
				download_port, velkport,
				poznamka
			) VALUES (
				%s,
				%s,
				%s,
				%s,
				%s,
				%s, %s,
				%s, %s,
				%s, %s,
				%s, %s,
				%s
			)",
			$_POST['urlid'],
			$_POST['nazov'],
			$_POST['datum'],
			$_POST['verzia'],
			$_POST['changelog'],
			$_POST['download_win'], $_POST['velkwin'],
			$_POST['download_lin'], $_POST['velklin'],
			$_POST['download_mac'], $_POST['velkmac'],
			$_POST['download_port'], $_POST['velkport'],
			$_POST['poznamka']
		)
	);
?>

<div class="updated">
	<p><strong>Produkt pridaný. (ID=<?php echo $wpdb->insert_id ?>)</strong></p>
</div>

<?php require_once("form-zoznam.php"); ?>
