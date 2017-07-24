<?php
if (isset($_POST['param1'])) 
{
	$zmaz_id = $_POST['param1'];
	$wpdb->query("DELETE FROM ".$wpdb->prefix."produkty WHERE id = '$zmaz_id'");
}
?>
<div class="updated">
	<p><strong>produkt zmazaný.</strong></p>
</div>
<?php
require_once("form-zoznam.php");
?>
