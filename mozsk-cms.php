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

	$verzia = $wpdb->get_var($wpdb->prepare("SELECT verzia FROM ".$wpdb->prefix."produkty WHERE urlid = %s ORDER by id DESC", $produkt));
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
			"SELECT verzia, datum, changelog, download_win, velkwin, download_lin, velklin, download_mac, velkmac, download_port, velkport
				FROM ".$wpdb->prefix."produkty
				WHERE urlid=%s
				ORDER BY
					LPAD(REPLACE(SUBSTRING(verzia, 1, 2), '.', ''), 5, '0') DESC,
					REPLACE(SUBSTRING(verzia, 3,2), '.', '') DESC,
					LPAD(REPLACE(SUBSTRING(verzia, 5), '.', '0'), 5, '0') DESC
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
			<strong>Verzia: '.$temp_prod->verzia.'</strong>
			<br/>
			Vydané: '.date("d.m.Y",strtotime($temp_prod->datum)).' - <a href="'.htmlspecialchars($temp_prod->changelog).'" '. $hreflang .'>poznámky k vydaniu</a>
		</p>
		<ul>
			<li class="ico-win"><a href="'.htmlspecialchars($temp_prod->download_win).'">Windows <small>(.exe)</small></a> ('.htmlspecialchars($temp_prod->velkwin).' МB)</li>
			<li class="ico-lin"><a href="'.htmlspecialchars($temp_prod->download_lin).'">Linux</a> <small>(.tar.gz)</small> ('.htmlspecialchars($temp_prod->velklin).' МB)</li>
			<li class="ico-mac"><a href="'.htmlspecialchars($temp_prod->download_mac).'">Mac OS</a> <small>(.dmg)</small> ('.htmlspecialchars($temp_prod->velkmac).' МB)</li>
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
			"SELECT verzia, nazov, datum, changelog, download_win, velkwin, download_lin,velklin,download_mac,velkmac,download_port,velkport FROM ".$wpdb->prefix."produkty WHERE urlid=%s ORDER BY id DESC",
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
						<li class="ico-win"><a href="'.htmlspecialchars($prod->download_win).'">Windows <small>(.exe)</small></a> ('.htmlspecialchars($prod->velkwin).' МB)</li>
						<li class="ico-lin"><a href="'.htmlspecialchars($prod->download_lin).'">Linux</a> <small>(.tar.gz)</small> ('.htmlspecialchars($prod->velklin).' МB)</li>
						<li class="ico-mac"><a href="'.htmlspecialchars($prod->download_mac).'">Mac OS</a> <small>(.dmg)</small> ('.htmlspecialchars($prod->velkmac).' МB)</li>
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
      case 'last_ver_ok':
				require_once("form-last_ver_ok.php");
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

if (!wp_next_scheduled('my_daily_function_hook')) {
  wp_schedule_event( time(), 'daily', 'my_daily_function_hook' );
}
add_action( 'my_daily_function_hook', 'my_daily_function' );

function my_daily_function() {
  global $wpdb;

	$send_to_user = 'mazarik';

  $temp_prod = $wpdb->get_results("SELECT id, name, last_version, check_url, check_variable FROM ".$wpdb->prefix."last_produkty WHERE 1 ORDER BY id DESC");
	if ($temp_prod) {
    $user_info = get_userdatabylogin($send_to_user);
	  $str_mail = 'Hello ' . $user_info->display_name . '!\n';
    $subj_mail = "";
    $send = 0;
		foreach ($temp_prod as $prod) {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $prod->check_url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $json_tmp = curl_exec($ch);
      curl_close($ch);
      if ($json_tmp) {
        $json_de = json_decode($json_tmp, true);
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
}

?>
