<h2>Zoznam produktov</h2>

<div style="overflow:auto;border:solid 1px #ccc;margin-bottom:10px">
	<table id="the-list-x" width="100%" cellpadding="3" cellspacing="0">
		<thead>
			<tr>
				<th scope="col">ID</th>
				<th scope="col">Názov</th>
				<th scope="col">Verzia</th>
				<th scope="col">Dátum</th>
				<th scope="col" colspan="5">Download</th>
				<th scope="col" width="150">Poznámka</th>
				<th scope="col" colspan="3">-</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$produkty = $wpdb->get_results("SELECT nazov, id, verzia, datum, poznamka, download_win, download_lin, download_mac, download_port, changelog FROM ".$wpdb->prefix."produkty ORDER BY nazov ASC, id DESC");
				if ($produkty) :
					$r = 0;
					foreach ($produkty as $produkt) {
			?>
				<?php if ($r % 2) : ?>
					<tr>
				<?php else: ?>
					<tr class="alternate" style="background-color: #BBB;">
				<?php endif; ?>
						<th scope="row"><?php echo htmlspecialchars($produkt->id) ?></th>
						<td><?php echo htmlspecialchars($produkt->nazov) ?></td>
						<td style="text-align:center"><?php echo htmlspecialchars($produkt->verzia) ?></td>
						<td style="text-align:center"><?php echo htmlspecialchars(mysql2date(get_settings('date_format'), $produkt->datum)) ?></td>
						<td width="20">
							<?php if ($produkt->download_win) : ?>
								<a href="<?php echo htmlspecialchars($produkt->download_win) ?>"><img src="/wp-content/plugins/mozsk-cms/win_small.png" alt="Windows" /></a>
							<?php endif; ?>
						</td>
						<td width="20">
							<?php if ($produkt->download_lin) : ?>
								<a href="<?php echo htmlspecialchars($produkt->download_lin) ?>"><img src="/wp-content/plugins/mozsk-cms/lin_small.png" alt="Linux" /></a>
							<?php endif; ?>
						</td>
						<td width="20">
							<?php if ($produkt->download_mac) : ?>
								<a href="<?php echo htmlspecialchars($produkt->download_mac) ?>"><img src="/wp-content/plugins/mozsk-cms/mac_small.png" alt="Mac OS" /></a>
							<?php endif; ?>
						</td>
						<td width="20">
							<?php if ($produkt->download_port) : ?>
								<a href="<?php echo htmlspecialchars($produkt->download_port) ?>"><img src="/wp-content/plugins/mozsk-cms/portable.png" alt="Portable" /></a>
							<?php endif; ?>
						</td>
						<td width="20">
							<?php if ($produkt->changelog) : ?>
								<a href="<?php echo htmlspecialchars($produkt->changelog) ?>"><img src="/mozilla-16.png" alt="Changelog" /></a>
							<?php endif; ?>
						</td>
						<td><?php echo htmlspecialchars($produkt->poznamka) ?></td>
						<td><a href="#" class="edit" onclick="mskcms_Edit(<?php echo htmlspecialchars($produkt->id) ?>)">Upraviť</a></td>
						<td><a href="#" class="edit" onclick="mskcms_NuVer(<?php echo htmlspecialchars($produkt->id) ?>)">+Verzia</a></td>
						<td><a href="#" class="delete" onclick="mskcms_AskDel(<?php echo htmlspecialchars($produkt->id) ?>)">Zmazať</a></td>
					</tr>
			<?php
						$r++;
					}
				else:
			?>
				<tr><td colspan="6">V databáze nie sú žiadne produkty.</td></tr>
			<?php endif; ?>
		</tbody>
	</table>
</div>

<form method="post" action="">
	<div class="submit">
		<input id="ok-submit" type="submit" name="ok-submit" value="Pridať produkt &raquo;" />
	</div>
	<input id="todo" name="todo" type="hidden" value="pridat"/>
	<input id="param1" name="param1" type="hidden" value=""/>
</form>


<h2>Kontrola nových verzií produktov</h2>

<form method="post" action="" style="display: none;">
	<input id="ok_submit_ver" type="submit" name="ok_submit_ver" value="" style="display: none;"/>
	<input id="id_prod" name="id_prod" type="hidden" value=""/>
	<input id="todo" name="todo" type="hidden" value="last_ver_ok"/>
</form>

<script type="text/javascript">
	//<![CDATA[
	function mskcms_OK_new_version(id) {
		document.getElementById("id_prod").value = id;
		document.getElementById("ok_submit_ver").click();
	}
	//]]>
</script>

<div style="overflow:auto;border:solid 1px #ccc;margin-bottom:10px">
	<table width="100%" cellpadding="3" cellspacing="0">
		<thead>
			<tr>
				<th scope="col">ID</th>
				<th scope="col">Názov</th>
				<th scope="col">Posledná kontrola</th>
				<th scope="col">Posledná verzia</th>
				<th scope="col">Nová verzia</th>
				<th scope="col">-</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$temp_prod = $wpdb->get_results("SELECT id, name, last_version, last_check, new_version FROM ".$wpdb->prefix."last_produkty WHERE 1 ORDER BY id ASC");
				$result = "";
				if ($temp_prod) :
					$r = 0;
					foreach ($temp_prod as $prod) {
						$background_color = 'none';
						if ($prod->last_version == $prod->new_version) {
							if ($r % 2) {
								$background_color = "lightgreen";
							} else {
								$background_color = "chartreuse";
							}
						} else {
							if ($r % 2) {
								$background_color = "red";
							} else {
								$background_color = "tomato";
							}
						}
			?>
				<tr style='background-color: <?php echo $background_color ?>'>
					<td><?php echo htmlspecialchars($prod->id) ?></td>
					<td><?php echo htmlspecialchars($prod->name) ?></td>
					<td><?php echo htmlspecialchars($prod->last_check) ?></td>
					<td><?php echo htmlspecialchars($prod->last_version) ?></td>
					<td><?php echo htmlspecialchars($prod->new_version) ?></td>
					<td><a href="#" class="edit" onclick="mskcms_OK_new_version(<?php echo htmlspecialchars($prod->id) ?>)">OK</a></td>
				</tr>
			<?php
						$r++;
					}
				endif;
			?>
		</tbody>
	</table>
</div>
