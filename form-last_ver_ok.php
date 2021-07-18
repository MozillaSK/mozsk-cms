<?php
	$uprav_id = "";
	if (isset($_POST['id_prod'])) :
		$uprav_id = intval($_POST['id_prod']);
		$wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."last_produkty SET last_version=new_version WHERE id=%d", $uprav_id));
?>

<div class="updated">
	<p><strong>Verzia produktu zmenená na aktuálnu hodnotu. (ID=<?php echo $uprav_id ?>)</strong></p>
</div>

<?php else : ?>

<div class="updated">
	<p><strong>Chyba. (ID=<?php echo $uprav_id ?>)</strong></p>
</div>

<?php endif; ?>

<?php require_once("form-zoznam.php"); ?>
