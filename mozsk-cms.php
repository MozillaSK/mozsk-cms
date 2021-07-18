<?php
/*
Plugin Name: Mozilla.sk CMS Plugin
Plugin URI: http://www.mozilla.sk
Description: CMS plugin pre stránky Mozilla.sk
Author: wladow
Version: 0.5.6
Author URI: http://www.wladow.sk
*/


function get_newprodukt($produkt, $what) {

  	global $wpdb;
  	
    $result = $wpdb->get_var("SELECT verzia 
      FROM ".$wpdb->prefix."produkty 
      WHERE urlid = '$produkt' 
      ORDER by id DESC
/*        LPAD(REPLACE(SUBSTRING(verzia, 1, 2), '.', ''), 5, '0') DESC, 
        REPLACE(SUBSTRING(verzia, 3,2), '.', '') DESC, 
        LPAD(REPLACE(SUBSTRING(verzia, 5), '.', '0'), 5, '0') DESC */
      ");
  	if ($what == 'link' ) {
  	 $agent=$_SERVER["HTTP_USER_AGENT"];
  	 $os='win';
  	   if (strstr($agent,"Mac")) $os='mac'; elseif (strstr($agent,"Linux")) $os='lin';
     if ($produkt == "mozilla-sunbird") {
  	   $link = $wpdb->get_var("SELECT download_$os FROM ".$wpdb->prefix."produkty WHERE urlid='$produkt' AND verzia='$result'");
     } else {
       if ($os == "mac") {
         $os = "osx";
       }
       if ($os == "lin") {
         $os = $os . 'ux';
       }
       $link = "https://download.mozilla.org/?product=$produkt-$result&os=$os&lang=sk";
     }
	   return htmlspecialchars($link);
  	}

	return htmlspecialchars($result);
}

class Download_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'download_widget',
			'description' => '',
		);
		parent::__construct( 'download_widget', 'Aktuálne verzie', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		echo '<div class="infopanel-top"><div class="infopanel-bottom">';
		if (strstr($_SERVER["HTTP_USER_AGENT"],"Mac")) { $os='mac'; $os_name='Mac OS'; }
		elseif (strstr($_SERVER["HTTP_USER_AGENT"],"Linux")) { $os='lin'; $os_name='Linux'; }
		else { $os='win'; $os_name='Windows'; }
		echo '<div class="nadpis">Aktuálne verzie<br>pre '.$os_name.'</div>';
		echo '<div class="infopanel verzie">';
		echo '<p><img src="/wp-content/plugins/mozsk-cms/firefox.png" style="max-width: 36px;" alt="Firefox" />
			<a href="'.get_newprodukt('firefox','link').'">Firefox</a><br/><b>'.get_newprodukt('firefox','verzia').'</b></p>';

		echo '<p><img src="/wp-content/plugins/mozsk-cms/tb3_36.png" alt="Thunderbird" />
			<a href="'.get_newprodukt('thunderbird','link').'">Thunderbird</a><br/><b>'.get_newprodukt('thunderbird','verzia').'</b></p>';

		echo '<p><img src="/wp-content/plugins/mozsk-cms/lightning-64.png" alt="Lightning" />
			<a href="'.get_newprodukt('lightning','link').'">Lightning</a><br/><b>'.get_newprodukt('lightning','verzia').'</b></p>';

		echo '<p><img src="/wp-content/plugins/mozsk-cms/seamonkey34.png" alt="SeaMonkey" />
			<a href="'.get_newprodukt('seamonkey','link').'">SeaMonkey</a><br/><b>'.get_newprodukt('seamonkey','verzia').'</b></p>';

		echo '<small class="alignright tucne"><a href="/download/">Ďalšie verzie &raquo;</a></small>';
		echo '<br/>';
		echo '</div>';
		echo '</div></div>';
	}
}
add_action( 'widgets_init', function(){
	register_widget( 'Download_Widget' );
});

function get_dlpage_content($produkt) {

  	global $wpdb;
  	
  	$result='<p><strong>Verzia: ';
  	$temp_prod = $wpdb->get_row("SELECT verzia, datum, changelog, download_win, velkwin, download_lin, velklin, download_mac, velkmac, download_port, velkport 
      FROM ".$wpdb->prefix."produkty 
      WHERE urlid='$produkt' 
      ORDER BY 
        LPAD(REPLACE(SUBSTRING(verzia, 1, 2), '.', ''), 5, '0') DESC, 
        REPLACE(SUBSTRING(verzia, 3,2), '.', '') DESC, 
        LPAD(REPLACE(SUBSTRING(verzia, 5), '.', '0'), 5, '0') DESC 
      LIMIT 1");
	$result .= $temp_prod->verzia.'</strong><br/>';
	$result .= 'Vydané: '.date("d.m.Y",strtotime($temp_prod->datum)).' - <a href="'.$temp_prod->changelog.'"';
  if (strpos($temp_prod->changelog,'/sk/') == 0) $result .= ' hreflang="en"';
  $result .= '>poznámky k vydaniu</a></p>';

	$result .= '<ul>
		<li class="ico-win"><a href="'.htmlspecialchars($temp_prod->download_win).'">Windows <small>(.exe)</small></a> ('.$temp_prod->velkwin.' МB)</li>
		<li class="ico-lin"><a href="'.htmlspecialchars($temp_prod->download_lin).'">Linux</a> <small>(.tar.gz)</small> ('.$temp_prod->velklin.' МB)</li>
		<li class="ico-mac"><a href="'.htmlspecialchars($temp_prod->download_mac).'">Mac OS</a> <small>(.dmg)</small> ('.$temp_prod->velkmac.' МB)</li>';
		
/*	if ($temp_prod->download_port != "" ) $result .=	'<li class="ico-win"><a href="'.htmlspecialchars($temp_prod->download_port).'">Portable* verzia <small>(.zip)</small></a> ('.$temp_prod->velkport.' МB)</li>';
	  else {
		  	$temp_port = $wpdb->get_row("SELECT verzia, download_port,velkport FROM ".$wpdb->prefix."produkty WHERE urlid='$produkt' AND download_port != '' ORDER BY id DESC LIMIT 1");
		  	if  ($temp_port) $result .= '<li class="ico-win"><a href="'.htmlspecialchars($temp_port->download_port).'">Portable* verzia '.$temp_port->verzia.' <small>(.zip)</small></a> ('.$temp_port->velkport.' МB)</li>';
	  }*/
	$result .= '</ul>';

	return $result;

}

function get_dlpage($produkt) {
	echo get_dlpage_content($produkt);
}

function get_dlpage_shortcode($atts) {
	return get_dlpage_content($atts['produkt']);
}
add_shortcode( 'get-dlpage', 'get_dlpage_shortcode' );

function get_archiv_content($produkt) {

  	global $wpdb;
	
  	$temp_prod = $wpdb->get_results("SELECT verzia, nazov, datum, changelog, download_win, velkwin,
			download_lin,velklin,download_mac,velkmac,download_port,velkport FROM ".$wpdb->prefix."produkty WHERE urlid='$produkt' ORDER BY id DESC");
	if ($temp_prod) {
		
		$prvy = 1;
		$result = "";
		
		foreach ($temp_prod as $prod) {
			
			if ($prvy==1) { $result .= '<div class="arch '.$produkt.'_arch">'; $prvy=0;} else $result .= '<div class="arch">';
			
			    $result .= '<h1><a href="/'.$produkt.'/">'.$prod->nazov.' '.$prod->verzia.'</a></h1>';
				$result .= '<p class="description">vydané: '.date("d.m.Y",strtotime($prod->datum)).' - <a href="'.$prod->changelog.'"';
        if (strpos($prod->changelog,'/sk/') == 0) $result .= ' hreflang="en"';
        $result .= '>poznámky k vydaniu</a></p>';

				$result .= '<ul>
					<li class="ico-win"><a href="'.htmlspecialchars($prod->download_win).'">Windows <small>(.exe)</small></a> ('.$prod->velkwin.' МB)</li>
					<li class="ico-lin"><a href="'.htmlspecialchars($prod->download_lin).'">Linux</a> <small>(.tar.gz)</small> ('.$prod->velklin.' МB)</li>
					<li class="ico-mac"><a href="'.htmlspecialchars($prod->download_mac).'">Mac OS</a> <small>(.dmg)</small> ('.$prod->velkmac.' МB)</li>';

			$result .= '</ul></div>';
		}
			
	}

	echo $result;

}

function get_archiv($produkt) {
	echo get_archiv_content($produkt);
}

function get_archiv_shortcode($atts) {
	return get_archiv_content($atts['produkt']);
}
add_shortcode( 'get-archiv', 'get_archiv_shortcode' );

function get_napisali_content($pocet = 15, $sidebar = 0) {

  	global $wpdb;

  $result = '';

  $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
  
  $temp = ($paged*($pocet)-$pocet);

  $limit = ($pocet < 15) ? "LIMIT $pocet" : "LIMIT $temp, 15";
  
  $celkom = $wpdb->get_var("SELECT count(id) FROM ".$wpdb->prefix."napisali");
  $napisali = $wpdb->get_results("SELECT id, nazov, datum, odkaz, server, excerpt FROM ".$wpdb->prefix."napisali
										GROUP BY nazov ORDER BY id DESC $limit");

	if($napisali)
	{
		foreach ($napisali as $clanok) 
		{
		if ($sidebar == 1) {
			$result .= '<p><a href="'.htmlspecialchars($clanok->odkaz).'" target="_blank">';
			if ($clanok->server != '-') $result .= $clanok->server.': ';
			$result .= $clanok->nazov.'</a><br/>';
			$result .= htmlspecialchars($clanok->excerpt);
			$result .= '</p>';
		}
		else {
			$result .= '<h4><a href="'.htmlspecialchars($clanok->odkaz).'" target="_blank">';
			if ($clanok->server != '-') $result .= $clanok->server.': ';
			$result .= $clanok->nazov.'</a> <small>('.date("d.m.Y",strtotime($clanok->datum)).')</small></h4>';
			$result .= '<div>'.$clanok->excerpt;
			$result .= '</div>';
		}
  	}

    if (($pocet>=15) && ($sidebar == 0)) {
		$result .= '<br/><br/><div class="navigation">';
			if (($celkom/ 15)+1>$paged) { $result .= '<div class="alignleft"><a href="/napisali/page/'; $result .= $paged+1 .'/">&laquo; Staršie články</a></div>'; }
			if ($paged>1) { $result .= '<div class="alignright"><a href="/napisali/page/'; $result .= $paged-1 .'/">Novšie články &raquo;</a></div>';}
		$result .= '</div>';
         
          }	
	}
	else
	{

		$result .= '<div class="error">Momentálne nie sú v tejto rubrike dostupné žiadne články.</div>';
	}

	return $result;

}

function get_napisali($pocet = 15, $sidebar = 0) {
	echo get_napisali_content($pocet, $sidebar);
}

function get_napisali_shortcode($atts) {
	return get_napisali_content($atts['pocet'], $atts['sidebar']);
}
add_shortcode( 'get-napisali', 'get_napisali_shortcode' );

class Napisali_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'napisali_widget',
			'description' => '',
		);
		parent::__construct( 'napisali_widget', 'Napísali o Mozille', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		echo '<div class="infopanel-top"><div class="infopanel-bottom">';
		echo '<div class="nadpis">Napísali o Mozille</div>';
		echo '<div class="infopanel male">';
		get_napisali(3,1);
		echo '<br/><span class="alignright tucne"><a href="/napisali/">Ďalšie články &raquo;</a></span><br/>';
		echo '</div>';
		echo '</div></div>';
	}
}
add_action( 'widgets_init', function(){
	register_widget( 'Napisali_Widget' );
});

function mskcms_PanelProdukty() 
{
	global $wpdb;

	echo '<div class="wrap">';
	if (isset($_POST['todo'])) 
	{
		$todo = $_POST['todo'];
		//echo "todo: $todo";
		switch($todo)
		{
			case 'pridat':
				require_once("form-pridat.php");
				break;
			case 'pridat-ok':
				require_once("form-pridat-ok.php");
				break;
			case 'zmazat-ok':
				require_once("form-zmazat-ok.php");
				break;
			case 'upravit':
				require_once("form-upravit.php");
				break;
			case 'upravit-ok':
				require_once("form-upravit-ok.php");
				break;
			case 'pridat-ver':
				require_once("form-pridat-ver.php");
				break;
			case 'pridat-ver-ok':
				require_once("form-pridat-ver-ok.php");
				break;
      case 'last_ver_ok':
				require_once("form-last_ver_ok.php");
				break;
			default:
				echo '<p>Neviem, čo mám robiť.</p>';
				break;
		}
	}
	else
	{
		require_once("form-zoznam.php");
	} 
	echo "</div>";
}

function mskcms_PanelNapisali() 
{
	global $wpdb;

	echo '<div class="wrap">';
	if (isset($_POST['todo'])) 
	{
		$todo = $_POST['todo'];
		//echo "todo: $todo";
		switch($todo)
		{
			case 'pridat':
				require_once("napisali-pridat.php");
				break;
			case 'pridat-ok':
				require_once("napisali-pridat-ok.php");
				break;
			case 'zmazat-ok':
				require_once("napisali-zmazat-ok.php");
				break;
			case 'upravit':
				require_once("napisali-upravit.php");
				break;
			case 'upravit-ok':
				require_once("napisali-upravit-ok.php");
				break;

			default:
				echo '<p>Neviem, čo mám robiť.</p>';
				break;
		}
	}
	else
	{
		require_once("napisali.php");
	} 
	echo "</div>";
}


function mskcms_AddOptionsPage() {
	if (function_exists('add_submenu_page')) {
		add_submenu_page('plugins.php', 'Produkty', 'Produkty', 3, basename(__FILE__), 'mskcms_PanelProdukty'); 
		add_submenu_page('plugins.php', 'Napísali o Mozille', 'Napísali o Mozille', 1, 'napisali.php','mskcms_PanelNapisali');
	}		
}


function mskcms_Install()
{
	global $wpdb;
	
	$table_name = $wpdb->prefix.'produkty';
	if($wpdb->get_var("show tables like '$table_name'") != $table_name)
	{
		$sql = "CREATE TABLE `$table_name` (
  `id` int(11) NOT NULL auto_increment,
  `urlid` varchar(50) default NULL,
  `nazov` varchar(80) default NULL,
  `datum` date default NULL,
  `verzia` varchar(20) default NULL,
  `changelog` varchar(200) default NULL,
  `download_win` varchar(200) default NULL,
  `velkwin` varchar(10) default NULL,
  `download_lin` varchar(200) default NULL,
  `velklin` varchar(10) default NULL,
  `download_mac` varchar(200) default NULL,
  `velkmac` varchar(10) default NULL,
  `download_port` varchar(200) default NULL,
  `velkport` varchar(10) default NULL,
  `poznamka` text,
  PRIMARY KEY (`id`) );";
		require_once(ABSPATH . '/wp-admin/upgrade-functions.php');
		dbDelta($sql);
	//	$wpdb->query($sql);
	}

	$table_name = $wpdb->prefix.'napisali';
	if($wpdb->get_var("show tables like '$table_name'") != $table_name)
	{
		$sql = "CREATE TABLE `$table_name` (
  `id` int(11) NOT NULL auto_increment,
  `nazov` varchar(200) default NULL,
  `datum` date default NULL,
  `odkaz` varchar(200) default NULL,
  `server` varchar(50) default NULL,
  `excerpt` text,
  PRIMARY KEY (`id`) );";
		require_once(ABSPATH . '/wp-admin/upgrade-functions.php');
		dbDelta($sql);
	//	$wpdb->query($sql);
	}
  
  $table_name = $wpdb->prefix.'last_produkty';
  //id | name | last_version | last_check | check_url | check_variable | new_version
	if($wpdb->get_var("show tables like '$table_name'") != $table_name)
	{
		$sql = "CREATE TABLE `$table_name` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(30) default NULL, 
  `last_version` varchar(20) default NULL,
  `last_check` date default NULL,
  `check_url` varchar(200) default NULL,
  `check_variable` varchar(50) default NULL, 
  `new_version` varchar(20) default NULL,
  PRIMARY KEY (`id`) );";
		require_once(ABSPATH . '/wp-admin/upgrade-functions.php');
		dbDelta($sql);
	//	$wpdb->query($sql);
	}
}

function mskcms_AddAdminJS() 
{
	if($_SERVER['SCRIPT_NAME'] == '/wp-admin/plugins.php' && ($_GET['page'] == basename(__FILE__)) || $_GET['page'] == 'napisali.php' || $_GET['page'] == 'ocakavane.php')
	{
		echo '<script type="text/javascript">
//<![CDATA[
function mskcms_AskDel(id)
{
	answer = window.confirm("Naozaj odstrániť túto verziu? Pozor, po stlačení OK ihneď maže!");
	if(answer)
	{
		document.getElementById("todo").value = "zmazat-ok";
		document.getElementById("param1").value = id;
		document.getElementById("ok-submit").click();
	}
}

function mskcms_Edit(id)
{
	document.getElementById("todo").value = "upravit";
	document.getElementById("param1").value = id;
	document.getElementById("ok-submit").click();
}

function mskcms_NuVer(id)
{
	document.getElementById("todo").value = "pridat-ver";
	document.getElementById("param1").value = id;
	document.getElementById("ok-submit").click();
}



//]]>
</script>';
	}
	//echo '<!-- ' . $_SERVER['SCRIPT_NAME'] . ' -->';
}

add_action('admin_menu', 'mskcms_AddOptionsPage');
add_action('admin_head', 'mskcms_AddAdminJS');
add_action('activate_mozsk-produkty/mozsk-produkty.php','mskcms_Install');

//kontrola novych verzii
if (!wp_next_scheduled('my_daily_function_hook')) {
  wp_schedule_event( time(), 'daily', 'my_daily_function_hook' );
}
add_action( 'my_daily_function_hook', 'my_daily_function' );

function my_daily_function() { 
  global $wpdb;
  //$to_err = "cron run: ";

  $temp_prod = $wpdb->get_results("SELECT id, name, last_version, check_url, check_variable FROM ".$wpdb->prefix."last_produkty WHERE 1 ORDER BY id DESC");
	if ($temp_prod) {
    $user_info = get_userdatabylogin('mazarik');
	  $str_mail = 'Hello ' . $user_info->display_name . '!\n';
    $subj_mail = "";
    $send = 0;
		foreach ($temp_prod as $prod) {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $prod->check_url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $json_tmp = curl_exec($ch);
      curl_close($ch);      
      //$to_err .= $json_tmp;
      if ($json_tmp) {
        $json_de = json_decode($json_tmp, true);
        //$to_err .= $prod->check_variable . ' = ' . $json_de['' . $prod->check_variable] . '\n';
        $wpdb->query('UPDATE '.$wpdb->prefix.'last_produkty SET new_version="' . $json_de[$prod->check_variable] . '",last_check=CURRENT_DATE() WHERE id=' . $prod->id);
        if ($json_de[$prod->check_variable] != $prod->last_version) {
          $send = 1;
          if ($user_info) {
            $subj_mail .= ' New version of ' . $prod->name;
            $str_mail .= 'There is new version of ' . $prod->name . '.';
            $str_mail .= ' It has changed from ' . $prod->last_version . ' to ' . $json_de[$prod->check_variable] . '.';
          }
        }
      }
		}
    if ($send == 1) {
      $str_mail .= ' Do a upgrade soon!\n Best Regards,\nyour wordpress cron.\n';
      $message_headers = '';
      @wp_mail($user_info->user_email, $subj_mail, $str_mail, $message_headers);
    }
	}
  //error_log($to_err);
}
?>
