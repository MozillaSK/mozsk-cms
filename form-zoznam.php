﻿<form method="post" action="">
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
	$r = 0;
	$produkty = $wpdb->get_results("SELECT nazov, id, verzia, datum, poznamka, download_win, download_lin, download_mac, download_port, changelog FROM ".$wpdb->prefix."produkty ORDER BY nazov ASC, id DESC");
	if($produkty)
	{
		foreach ($produkty as $produkt) 
		{
			if($r % 2) echo '<tr>';
			else echo '<tr class="alternate" style="background-color: #BBB;">';
			
			$df = get_settings('date_format');
			$datum = mysql2date($df, $produkt->datum);

			echo "<th scope=\"row\">{$produkt->id}</th>";
			echo "<td>{$produkt->nazov}</td>";
			echo "<td style=\"text-align:center\">{$produkt->verzia}</td>";
			echo "<td style=\"text-align:center\">$datum</td>";
			echo '<td width="20">'; if ($produkt->download_win) echo '<a href="'.htmlspecialchars($produkt->download_win).'"><img src="/wp-content/plugins/mozsk-cms/win_small.png" alt="Windows" /></a>'; echo '</td>';
			echo '<td width="20">'; if ($produkt->download_lin) echo '<a href="'.htmlspecialchars($produkt->download_lin).'"><img src="/wp-content/plugins/mozsk-cms/lin_small.png" alt="Linux" /></a>'; echo '</td>';
			echo '<td width="20">'; if ($produkt->download_mac) echo '<a href="'.htmlspecialchars($produkt->download_mac).'"><img src="/wp-content/plugins/mozsk-cms/mac_small.png" alt="Mac OS" /></a>'; echo '</td>';
			echo '<td width="20">'; if ($produkt->download_port) echo '<a href="'.htmlspecialchars($produkt->download_port).'"><img src="/wp-content/plugins/mozsk-cms/portable.png" alt="Portable" /></a>'; echo '</td>';
			echo '<td width="20">'; if ($produkt->changelog) echo '<a href="'.$produkt->changelog.'"><img src="/mozilla-16.png" alt="Changelog" /></a>'; echo '</td>';
			echo '<td>'.$produkt->poznamka.'</td>';
			echo '<td><a href="#" class="edit" onclick="mskcms_Edit('.$produkt->id.')">Upraviť</a></td>';
			echo '<td><a href="#" class="edit" onclick="mskcms_NuVer('.$produkt->id.')">+Verzia</a></td>';
			echo '<td><a href="#" class="delete" onclick="mskcms_AskDel('.$produkt->id.')">Zmazať</a></td>';
			echo '</tr>';
			$r++;
		}
	}	
	else
	{
		echo '<tr><td colspan="6">V databáze nie sú žiadne produkty.</td></tr>';
	}
?>
	</tbody>
	</table>
	</div>
	<div class="submit">
		<input id="ok-submit" type="submit" name="ok-submit" value="Pridať produkt &raquo;" />
	</div>
	<input id="todo" name="todo" type="hidden" value="pridat"/>
	<input id="param1" name="param1" type="hidden" value=""/>
</form>
<form method="post" action="">
<h2>Kontrola nových verzií produktov</h2>
<div style="overflow:auto;border:solid 1px #ccc;margin-bottom:10px">
<script type="text/javascript">
//<![CDATA[
function mskcms_OK_new_version(id) {
	document.getElementById("id_prod").value = id;
	document.getElementById("ok_submit_ver").click();
}
//]]>
</script>
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
	if ($temp_prod) {
    $r = -1;
		foreach ($temp_prod as $prod) {
      $r++;
      $result .= "<tr style='background-color: ";
      if ($prod->last_version == $prod->new_version) {
        if ($r % 2) {
		      $result .= "lightgreen";
        } else {
		      $result .= "chartreuse";
        }
      } else {
        if ($r % 2) {
		      $result .= "red";
        } else {
		      $result .= "tomato";
        }
      }
      $result .= ";'>";
      $result .= '<td>' . $prod->id . '</td><td>' . $prod->name . '</td><td>' . $prod->last_check . '</td>';
      $result .= '<td>' . $prod->last_version . '</td><td>' . $prod->new_version . '</td>';
			$result .= '<td><a href="#" class="edit" onclick="mskcms_OK_new_version(\''.$prod->id.'\')">OK</a></td>';
      $result .= '</tr>';
		}
	}
	echo $result;
  ?>
  </tbody>
</table>
</div>
	<input id="ok_submit_ver" type="submit" name="ok_submit_ver" value="" style="display: none;"/>
	<input id="id_prod" name="id_prod" type="hidden" value=""/>
	<input id="todo" name="todo" type="hidden" value="last_ver_ok"/>
</form>

