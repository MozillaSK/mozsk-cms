<script>
function fill_version() {
  var urlid = document.getElementById('urlid').value;
  var version = document.getElementById('verzia').value;
  var prefix = {"firefox":{"win": "Firefox", "lin": "firefox", "mac": "Firefox"}, 
                "thunderbird":{"win": "Thunderbird", "lin": "thunderbird", "mac": "Thunderbird"}, 
                "seamonkey":{"win": "SeaMonkey", "lin": "seamonkey", "mac": "SeaMonkey"}
              };
  if (urlid == 'seamonkey') {
    document.getElementById('changelog').value = 'httsp://www.seamonkey-project.org/releases/' + urlid + version + '/'; 
  } else {
    document.getElementById('changelog').value = 'https://www.mozilla.com/sk/' + urlid + '/' + version + '/releasenotes/';
  }
  document.getElementById('download_win').value = 'ftp://ftp.mozilla.org/pub/' + urlid + '/releases/' + version + '/win32/sk/' + prefix[urlid]["win"] + '%20Setup%20' + version + '.exe';
  document.getElementById('download_lin').value = 'ftp://ftp.mozilla.org/pub/' + urlid + '/releases/' + version + '/linux-i686/sk/' + prefix[urlid]["lin"] + '-' + version + '.tar.bz2';
  document.getElementById('download_mac').value = 'ftp://ftp.mozilla.org/pub/' + urlid + '/releases/' + version + '/mac/sk/' + prefix[urlid]["mac"] + '%20' + version + '.dmg';
}
													
</script>
<table cellpadding="2" cellspacing="2" border="0" style="text-align: left; width: 95%;">
<tbody>
	<tr>
		<td style="width:15%"><label for="nazov">Názov</label></td>
		<td><input type="text" id="nazov" name="nazov" size="50" value="<?php echo $nazov; ?>" /> </td>
	</tr>
	<tr>
		<td><label for="urlid">Úderník</label></td>
		<td><input type="text" id="urlid" name="urlid" size="50" value="<?php echo $urlid; ?>" />  (iba písmená, čísla, mínus)</td>
	</tr>
	<tr>
		<td><label for="datum">Dátum</label></td>
		<td><input id="datum" name="datum" type="text" value="<?php echo $datum; ?>" size="10"/> (formát RRRR-MM-DD)</td>
	</tr>
	<tr>
		<td><label for="verzia">Verzia</label></td>
		<td><input id="verzia" name="verzia" type="text" size="20" value="<?php echo $verzia; ?>" /></td>
	</tr>
	<tr>
		<td colspan="2"><h3>Download</h3></td>
	</tr>
	<tr>
		<td><label for="changelog">Changelog</label></td>
		<td><input id="changelog" name="changelog" type="text" size="82" value="<?php echo $changelog; ?>" />&nbsp;<img onclick="document.getElementById('changelog').value=''" src="/wp-content/plugins/mozsk-cms/zmaz.png" alt="zmazat" /></td>
	</tr>
	<tr>
		<td><label for="download_win">Windows</label></td>
		<td><input name="download_win" type="text" id="download_win" size="82" value="<?php echo $download_win; ?>" />&nbsp;<img onclick="document.getElementById('download_win').value=''" src="/wp-content/plugins/mozsk-cms/zmaz.png" alt="zmazat" /> <input id="velkwin" name="velkwin" type="text" size="4" value="<?php echo $velkwin; ?>" /> MB</td>
	</tr>
	<tr>
		<td><label for="download_lin">Linux</label></td>
		<td><input name="download_lin" type="text" id="download_lin" size="82" value="<?php echo $download_lin; ?>" />&nbsp;<img onclick="document.getElementById('download_lin').value=''" src="/wp-content/plugins/mozsk-cms/zmaz.png" alt="zmazat" /> <input id="velklin" name="velklin" type="text" size="4" value="<?php echo $velklin; ?>" /> MB</td>
	</tr>
	<tr>
		<td><label for="download_mac">Mac OS</label></td>
		<td><input name="download_mac" type="text" id="download_mac" size="82" value="<?php echo $download_mac; ?>" />&nbsp;<img onclick="document.getElementById('download_mac').value=''" src="/wp-content/plugins/mozsk-cms/zmaz.png" alt="zmazat" /> <input id="velkmac" name="velkmac" type="text" size="4" value="<?php echo $velkmac; ?>" /> MB</td>
	</tr>
	<tr>
		<td><label for="download_port">Portable</label></td>
		<td><input name="download_port" type="text" id="download_port" size="82" value="<?php echo $download_port; ?>" />&nbsp;<img onclick="document.getElementById('download_port').value=''" src="/wp-content/plugins/mozsk-cms/zmaz.png" alt="zmazat" /> <input id="velkport" name="velkport" type="text" size="4" value="<?php echo $velkport; ?>" /> MB</td>
	</tr>
	<tr>
		<td></td><td><input type="button" class="button" onclick="fill_version()" value="Vyplň" /> (musí byť vyplnené pole Verzia !!)  
	</tr>
	<tr>
		<td colspan="2"><h3>Iné</h3></td>
	</tr>
	<tr>
		<td><label for="poznamka">Poznámka</label></td>
		<td><textarea id="poznamka" name="poznamka" cols="80" rows="4"><?php echo $poznamka; ?></textarea></td>
	</tr>
</tbody>
</table>
