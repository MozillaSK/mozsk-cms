<h2>Nová verzia produktu</h2>

<form method="post" action="">
	<?php
		if (isset($_POST['param1']))  {
			$uprav_id = intval($_POST['param1']);
			$produkt = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."produkty WHERE id=%d", $uprav_id));
			if ($produkt) {
				$urlid = htmlspecialchars($produkt->urlid, ENT_QUOTES);
				$nazov = htmlspecialchars($produkt->nazov, ENT_QUOTES);
				$datum = date('Y-m-d');
				$verzia = htmlspecialchars($produkt->verzia, ENT_QUOTES);
				$changelog = htmlspecialchars($produkt->changelog, ENT_QUOTES);
				$download_win = htmlspecialchars($produkt->download_win, ENT_QUOTES);
				$velkwin = htmlspecialchars($produkt->velkwin, ENT_QUOTES);
				$download_lin = htmlspecialchars($produkt->download_lin, ENT_QUOTES);
				$velklin = htmlspecialchars($produkt->velklin, ENT_QUOTES);
				$download_mac = htmlspecialchars($produkt->download_mac, ENT_QUOTES);
				$velkmac = htmlspecialchars($produkt->velkmac, ENT_QUOTES);
				$download_port = htmlspecialchars($produkt->download_port, ENT_QUOTES);
				$velkport = htmlspecialchars($produkt->velkport, ENT_QUOTES);
				$poznamka = htmlspecialchars($produkt->poznamka, ENT_QUOTES);
				require_once("form-upravit-inc.php");
			} else {
				die ("Osudová chyba: Také ID tu nemám.");
			}
		}
	?>
	<div class="submit">
		<input type="submit" name="ok-submit" value="Potvrdiť údaje &raquo;" />
	</div>
	<input id="todo" name="todo" type="hidden" value="pridat-ok"/>
	<input id="param1" name="param1" type="hidden" value="<?php echo $uprav_id ?>"/>
</form>
