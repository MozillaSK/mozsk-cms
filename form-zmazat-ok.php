<?php
	if (isset($_POST['param1'])) {
		$zmaz_id = intval($_POST['param1']);
		$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."produkty WHERE id = %d", $zmaz_id));
	}
?>

<div class="updated">
	<p><strong>produkt zmazaný.</strong></p>
</div>

<?php require_once("form-zoznam.php"); ?>
