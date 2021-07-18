<?php
/*
Plugin Name: Mozilla.sk CMS Plugin
Plugin URI: https://www.mozilla.sk
Description: CMS plugin pre stránky Mozilla.sk
Author: wladow
Version: 0.5.6
Author URI: http://www.wladow.sk
*/

function get_newprodukt($produkt, $what) {
	global $wpdb;

	$verzia = $wpdb->get_var($wpdb->prepare("SELECT verzia FROM ".$wpdb->prefix."produkty WHERE urlid = %s ORDER BY id DESC", $produkt));
	if ($what == 'link') {
		$agent = $_SERVER["HTTP_USER_AGENT"];
		$os = 'win';
		if (strstr($agent, "Mac")) {
			$os = "osx";
		} elseif (strstr($agent, "Linux")) {
			$os = "linux";
		}
		return htmlspecialchars("https://download.mozilla.org/?product=$produkt-$verzia&os=$os&lang=sk");
	} else {
		return htmlspecialchars($verzia);
	}
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
		$agent = $_SERVER["HTTP_USER_AGENT"];
		$os = 'win';
		$os_name = 'Windows';
		if (strstr($agent, "Mac")) {
			$os = 'mac';
			$os_name = 'Mac OS';
		} elseif (strstr($agent, "Linux")) {
			$os = 'lin';
			$os_name = 'Linux';
		}

		echo '
			<div class="infopanel-top"><div class="infopanel-bottom">
				<div class="nadpis">Aktuálne verzie<br>pre '.$os_name.'</div>
				<div class="infopanel verzie">
					<p>
						<img src="/wp-content/plugins/mozsk-cms/firefox.png" style="max-width: 36px;" alt="Firefox" /><a href="'.get_newprodukt('firefox','link').'">Firefox</a><br/><b>'.get_newprodukt('firefox','verzia').'</b>
					</p>
					<p>
						<img src="/wp-content/plugins/mozsk-cms/tb3_36.png" alt="Thunderbird" /><a href="'.get_newprodukt('thunderbird','link').'">Thunderbird</a><br/><b>'.get_newprodukt('thunderbird','verzia').'</b>
					</p>
					<p>
						<img src="/wp-content/plugins/mozsk-cms/seamonkey34.png" alt="SeaMonkey" /><a href="'.get_newprodukt('seamonkey','link').'">SeaMonkey</a><br/><b>'.get_newprodukt('seamonkey','verzia').'</b>
					</p>
					<small class="alignright tucne"><a href="/download/">Ďalšie verzie &raquo;</a></small>
					<br/>
				</div>
			</div></div>
		';
	}
}

add_action( 'widgets_init', function() {
	register_widget( 'Download_Widget' );
});

function get_dlpage_content($produkt) {
	global $wpdb;

	$temp_prod = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT verzia, datum, changelog, download_win, download_lin, download_mac, download_port
				FROM ".$wpdb->prefix."produkty
				WHERE urlid=%s
				ORDER BY id DESC
				LIMIT 1",
				$produkt
		)
	);

	if (strpos($temp_prod->changelog,'/sk/') == 0) {
		$hreflang = ' hreflang="en"';
	} else {
		$hreflang = '';
	}

	return '
		<p>
			<strong>Verzia: '.$temp_prod->verzia.'</strong> - <a href="'.htmlspecialchars($temp_prod->changelog).'" '. $hreflang .'>poznámky k vydaniu</a>
		</p>
		<ul>
			<li class="ico-win"><a href="'.htmlspecialchars($temp_prod->download_win).'">Windows <small>(.exe)</small></a></li>
			<li class="ico-lin"><a href="'.htmlspecialchars($temp_prod->download_lin).'">Linux</a> <small>(.tar.gz)</small></li>
			<li class="ico-mac"><a href="'.htmlspecialchars($temp_prod->download_mac).'">Mac OS</a> <small>(.dmg)</small></li>
		</ul>
	';
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

	$temp_prod = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT verzia, nazov, datum, changelog, download_win, download_lin, download_mac, download_port FROM ".$wpdb->prefix."produkty WHERE urlid=%s ORDER BY id DESC",
			$produkt
		)
	);

	$result = "";

	if ($temp_prod) {
		$r = 0;
		foreach ($temp_prod as $prod) {
			if ($r == 0) {
				$arch_class = 'arch '.$produkt.'_arch';
			} else {
				$arch_class = 'arch';
			}

			if (strpos($prod->changelog,'/sk/') == 0) {
				$hreflang = ' hreflang="en"';
			} else {
				$hreflang = '';
			}

			$result .= '
				<div class="'.$arch_class.'">
					<h1><a href="/'.htmlspecialchars($produkt).'/">'.htmlspecialchars($prod->nazov).' '.htmlspecialchars($prod->verzia).'</a></h1>
					<p class="description">
						vydané: '.date("d.m.Y",strtotime($prod->datum)).' - <a href="'.htmlspecialchars($prod->changelog).'"'.$hreflang.'>poznámky k vydaniu</a>
					</p>
					<ul>
						<li class="ico-win"><a href="'.htmlspecialchars($prod->download_win).'">Windows <small>(.exe)</small></a></li>
						<li class="ico-lin"><a href="'.htmlspecialchars($prod->download_lin).'">Linux</a> <small>(.tar.gz)</small></li>
						<li class="ico-mac"><a href="'.htmlspecialchars($prod->download_mac).'">Mac OS</a> <small>(.dmg)</small></li>
					</ul>
				</div>
			';
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

function mskcms_PanelProdukty() {
	global $wpdb;

	echo '<div class="wrap">';
	if (isset($_POST['todo'])) {
		switch($_POST['todo']) {
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
			default:
				echo '<p>Neviem, čo mám robiť.</p>';
				break;
		}
	} else {
		require_once("form-zoznam.php");
	}
	echo "</div>";
}

function mskcms_AddOptionsPage() {
	if (function_exists('add_submenu_page')) {
		add_menu_page('Produkty', 'Produkty', 3, basename(__FILE__), 'mskcms_PanelProdukty');
	}
}

function mskcms_Install() {
	global $wpdb;

	$table_name = $wpdb->prefix.'produkty';
	if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {
		require_once(ABSPATH . '/wp-admin/upgrade-functions.php');
		dbDelta(
			"CREATE TABLE `$table_name` (
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
				PRIMARY KEY (`id`)
			);"
		);
	}

  $table_name = $wpdb->prefix.'last_produkty';
	if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {
		require_once(ABSPATH . '/wp-admin/upgrade-functions.php');
		dbDelta(
			"CREATE TABLE `$table_name` (
				`id` int(11) NOT NULL auto_increment,
				`name` varchar(30) default NULL,
				`last_version` varchar(20) default NULL,
				`last_check` date default NULL,
				`check_url` varchar(200) default NULL,
				`check_variable` varchar(50) default NULL,
				`new_version` varchar(20) default NULL,
				PRIMARY KEY (`id`)
			);"
		);
	}
}

function mskcms_AddAdminJS() {
	if($_SERVER['SCRIPT_NAME'] == '/wp-admin/admin.php' && ($_GET['page'] == basename(__FILE__))) {
		echo '
			<script type="text/javascript">
				//<![CDATA[
				function mskcms_AskDel(id) {
					if (window.confirm("Naozaj odstrániť túto verziu? Pozor, po stlačení OK ihneď maže!")) {
						document.getElementById("todo").value = "zmazat-ok";
						document.getElementById("param1").value = id;
						document.getElementById("ok-submit").click();
					}
				}

				function mskcms_Edit(id) {
					document.getElementById("todo").value = "upravit";
					document.getElementById("param1").value = id;
					document.getElementById("ok-submit").click();
				}

				function mskcms_NuVer(id) {
					document.getElementById("todo").value = "pridat-ver";
					document.getElementById("param1").value = id;
					document.getElementById("ok-submit").click();
				}
				//]]>
			</script>
		';
	}
}

add_action('admin_menu', 'mskcms_AddOptionsPage');
add_action('admin_head', 'mskcms_AddAdminJS');
add_action('activate_mozsk-produkty/mozsk-produkty.php','mskcms_Install');

?>
