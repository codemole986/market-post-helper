<?php

/**
 * Plugin Name: Market Post Helper
 * Plugin URI: 
 * Description: This plugin adds the short code function to grab stat info from intrinio and other more market source into the post.
 * Version: 1.5
 * Author: Wilson Breiner
 * Author URI: 
 * License: GPL2
 */

error_reporting(E_ERROR | E_PARSE);

require_once(dirname(__FILE__) . '/includes.php');
require_once(dirname(__FILE__) . '/api.php');

function intrinio_activation_actions () {
	Intrinio_Shortcode::on_activate();
}
register_activation_hook( __FILE__, 'intrinio_activation_actions' );

add_action('wp_ajax_nopriv_mph_insert', 'mph_publish_post');
add_action('wp_ajax_mph_insert', 'mph_publish_post');

add_action('wp_ajax_nopriv_mph_meta', 'mph_meta');
add_action('wp_ajax_mph_meta', 'mph_meta');

function mph_meta() {

    $data = Intrinio_Helper::make_data_safe($_REQUEST);
    $webid = $data['i'];

    header('Content-Type: application/json');

    $homeUrls = parse_url(get_home_url());
    $host = $homeUrls['host'];

    $dateV = $data['dt'];

    $availableTime = strtotime(gmdate('Y-m-d H:i:s') . " -5 minute");
    $sentTime = strtotime($dateV);
    if ($sentTime < $availableTime) {
        // sent time is not valid
        echo Intrinio_Helper::json_encode(array('error' => 8));
        exit;
    }

    $str = $host . "|" . $dateV . "|meta|wilson16|";
    $hash = hash('sha256', $str);

    if ($hash != $data['h']) {
        // hash is not correct
        echo Intrinio_Helper::json_encode(array('error' => 7));
        exit;
    }

    $catArgs = array(
        'type'                     => 'post',
        'hide_empty'               => 0,
        'taxonomy'                 => 'category',
    );
    $catsRaw = get_categories($catArgs);
    $cats = array();
    foreach ($catsRaw as $cat) {
        $cats[$cat->slug] = $cat->name;
    }

    $htmlArgs = array(
        'echo'          => 0,
        'title_li'      => '',
        'hide_empty'    => 0,
        'walker'        => new Walker_Category_Checklist,
    );
    $catList = wp_list_categories( $htmlArgs );
    $catList = str_replace(' name="post_category[]"', ' name="publish_web_cats[' . $webid . '][]"', $catList);
    echo Intrinio_Helper::json_encode(array(
        'error' => 0, 
        'i' => $webid,
        'cats' => $cats,
        'catsList' => $catList,
    ));
    exit;
}

function mph_publish_post() {

    header('Content-Type: application/json');

    $data = Intrinio_Helper::make_data_safe($_REQUEST);

    $post = isset($data['p'])?$data['p']:false;
    if (!$post) {
        // Post not given
        echo Intrinio_Helper::json_encode(array('error' => 9));
        exit;
    }

    $homeUrls = parse_url(get_home_url());
    $host = $homeUrls['host'];
    $dateV = $data['dt'];

    $availableTime = strtotime(gmdate('Y-m-d H:i:s') . " -5 minute");
    $sentTime = strtotime($dateV);
    if ($sentTime < $availableTime) {
        // sent time is not valid
        echo Intrinio_Helper::json_encode(array('error' => 8));
        exit;
    }

    $str = $host . "|" . $dateV . "|post|wilson16|" . $post['post_title'];
    $hash = hash('sha256', $str);
    if ($hash != $data['h']) {
        // hash is not correct
        echo Intrinio_Helper::json_encode(array('error' => 7));
        exit;
    }

    $id = wp_insert_post($post, true);
    $newPost = get_post($id);
    // $url = get_post_permalink($id, true, true);
    $url = get_permalink($newPost);
    echo Intrinio_Helper::json_encode(array(
        'error' => 0,
        'id'    => $id,
        'url'   => $url,
    ));
    exit;
}

if ( is_admin() ) { // admin actions
	add_action('admin_head', 'intrinio_shortcode_head' );
	add_action('admin_menu', 'intrinio_shortcode_menu');
	add_action('admin_init', 'init_intrinio_settings');

	add_action('init', 'setup_tinymce_plugin');
	add_action('wp_ajax_intrinio_load_comapines', 'intrinio_load_comapines');
	add_action('wp_ajax_intrinio_barchart', 'intrinio_load_barchart');
    add_action('wp_ajax_intrinio_import_finviz_items', 'intrinio_import_finviz_items');
    add_action('wp_ajax_intrinio_import_quandl_items', 'intrinio_import_quandl_items');
    add_action('wp_ajax_intrinio_preload', 'intrinio_preload');

    add_action( 'admin_enqueue_scripts', 'intrinio_add_admin_scripts' );
    add_action( 'add_meta_boxes', 'intrinio_add_news_load_modal');
	add_action( 'save_post', 'update_intrinio_shortcode');

} else {

    function intrinio_content_filter( $content ) {
        $disclaimer = get_post_meta( get_the_ID(), 'intrinio_post_disclaimer', true);
        if (!$disclaimer) {
            $disclaimer = get_option('intrinio_disclaimer');
        }
        
        /*
        remove_filter( 'the_content', 'intrinio_content_filter' );
        $disclaimer = apply_filters('the_content', $disclaimer);
        add_filter( 'the_content', 'intrinio_content_filter' );
        */

        return $content . wpautop($disclaimer);
    }

    add_filter( 'the_content', 'intrinio_content_filter' );
    add_filter( 'the_title', 'do_shortcode' );
    add_filter( 'document_title_parts', 'apply_shortcode_title' );
}

function apply_shortcode_title($title_parts) {
    foreach ($title_parts as $key => $value) {
        $title_parts[$key] = do_shortcode($title_parts[$key]);
    }
    return $title_parts;
}


add_shortcode("intr_code", "intrinio_process_shortcode");
add_shortcode("finviz_code", "intrinio_process_shortcode");
add_shortcode("quandl_code", "intrinio_process_shortcode");
add_shortcode("intr_chart", "intrinio_process_chart");
add_shortcode("intr_bto", "intrinio_process_bto");
add_shortcode("intr_summary", "intrinio_process_summary");
add_shortcode("rsi_code", "intrinio_process_rsi_code");
add_shortcode("ema_code", "intrinio_process_ema_code");
add_shortcode("market_watch", "intrinio_process_shortcode");
add_shortcode("barchart", "intrinio_process_shortcode");

add_action( 'wp_enqueue_scripts', 'intrinio_add_scripts' );

function intrinio_add_news_load_modal() {
    add_meta_box(
        'intrinio_post_disclaimer_box',
        'Disclaimer',
        'intrinio_render_disclaimer',
        'post',
        'normal',
        'high',
        array()
    );
    add_meta_box(
        'intrinio_load_news',
        'News',
        'intrinio_render_news_load',
        'post',
        'normal',
        'high',
        array()
    );
    add_meta_box(
        'intrinio_finviz',
        'Finviz',
        'intrinio_render_finviz',
        'post',
        'normal',
        'high',
        array()
    );
}

function intrinio_render_disclaimer() {
    include (plugin_dir_path(__FILE__) . 'view_postbox_disclaimer.tpl');
}

function intrinio_render_news_load() {
    include (plugin_dir_path(__FILE__) . 'view_postbox_load_news.tpl');
}

function intrinio_render_finviz() {
    include (plugin_dir_path(__FILE__) . 'view_postbox_finviz.tpl');
}

function intrinio_add_scripts() {
	wp_enqueue_style( 'fancybox', plugin_dir_url(__FILE__) . '/plugins/fancyapps-fancyBox/source/jquery.fancybox.css',false,'2.1','all');
	wp_enqueue_script( 'fancybox', plugin_dir_url(__FILE__) . '/plugins/fancyapps-fancyBox/source/jquery.fancybox.js', array ( 'jquery' ), '2.1', true);
	wp_enqueue_script( 'intrinio_frontend', plugin_dir_url(__FILE__) . '/js/frontend-script.js', array ( 'jquery' ), '1.1', true);
}

function intrinio_add_admin_scripts($hook_suffix) {
    
    wp_enqueue_style( 'bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',false,'3.3.7','all');
    wp_enqueue_script( 'bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array ( 'jquery' ), '3.3.7', true);

    // if ($hook_suffix == 'toplevel_page_intrinio-shortcode-settings') {
        // wp_enqueue_style( 'jquery-ui', plugin_dir_url(__FILE__) . '/plugins/jquery-ui-1.12.1.custom/jquery-ui.min.css',false,'1.12.1','all');
        wp_enqueue_script( 'jquery-ui', plugin_dir_url(__FILE__) . '/plugins/jquery-ui-1.12.1.custom/jquery-ui.min.js', array ( 'jquery' ), '1.12.1', true);
    // }

    wp_enqueue_style( 'intrinio_admin', plugin_dir_url(__FILE__) . 'style.css',false,'1.2','all');
    wp_enqueue_script( 'intrinio_admin', plugin_dir_url(__FILE__) . '/js/admin-script.js', array ( 'jquery' ), '1.9.1', true);
    
}

function intrinio_shortcode_head() {
	$items = get_option('intrinio_item_list');
	$items = str_replace("\r\n", "\n", $items);
	$items = explode("\n", $items);
	sort($items);

	$items4mce = array();
	$items4mce[] = array(
		'text' => 'Select an Item',
		'value' => '',
	);
	foreach ($items as $v) {
		$nmn = explode(":", $v);
		$items4mce[] = array(
			'text' => $nmn[0],
			'value' => $nmn[1],
		);
	}

	$templates = Intrinio_Shortcode::get_templates();
	$templates4mce = array();
	$templates4store = array();
	$templates4mce[] = array(
		'text' => 'Select the Template',
		'value' => '',
	);
	foreach ($templates as $value) {
		$templates4store[basename($value[1])] = [
			$value[0],
			wpautop(utf8_decode(str_replace(["\r\n","\n"], "\r\n\r\n", file_get_contents($value[1])))),
		];
		$templates4mce[] = array(
			'text' => $value[0],
			'value' => basename($value[1]),
		);
	}

    $items = get_option('intrinio_finviz_items');
    if (!$items || count($items) < 1) {
        $items = Intrinio_Shortcode::import_finviz_items(false);
    }
    
    $finviz_items[] = array(
        'text' => 'Select an Item',
        'value' => '',
    );
    foreach ($items as $value) {
        $finviz_items[] = array(
            'text' => $value[0],
            'value' => $value[1],
        );
    }

    $quandl_items = array();
    /*
    $quandl_items[] = [
        'text' => 'Select the Quandl Item',
        'value' => '',
    ];
    */
    $itemset = get_option('intrinio_quandl_items');
    if ($itemset) {
        foreach ($itemset as $key => $items) {
            $quandl_items[] = array(
                'text' => Intrinio_Shortcode::$quandl_zacks[$key][0],
                'value' => '', // $key . "|all",
            );
            foreach ($items as $item) {
                $quandl_items[] = array(
                    'text' => $item,
                    'value' => $key . "|" . $item,
                );
            }
        }    
    }

    $marketwatch_items[] = array(
        'text' => 'Select an Item',
        'value' => '',
    );
    $marketwatch_items_set = Intrinio_Shortcode::get_market_watch_items();
    foreach ($marketwatch_items_set as $key => $value) {
        $marketwatch_items[] = array(
            'text' => $value[0],
            'value' => $key,
        );
    }

    $barchart_items[] = array(
        'text' => 'Select an Item',
        'value' => '',
    );
    $barchart_items_set = Intrinio_Shortcode::get_barchart_items();
    foreach ($barchart_items_set as $key => $value) {
        $barchart_items[] = array(
            'text' => $value[0],
            'value' => $key,
        );
    }

	?>
	<script type="text/javascript">
		var intrinio_item_list = <?php echo Intrinio_Helper::json_encode($items4mce); ?>;
		var intrinio_templates = <?php echo Intrinio_Helper::json_encode($templates4mce) ?>;
		var intrinio_templates_store = <?php echo Intrinio_Helper::json_encode($templates4store) ?>;
        var intrinio_finviz_item_list = <?php echo Intrinio_Helper::json_encode($finviz_items); ?>;
        var intrinio_quandl_item_list = <?php echo Intrinio_Helper::json_encode($quandl_items); ?>;
        var intrinio_marketwatch_items = <?php echo Intrinio_Helper::json_encode($marketwatch_items); ?>;
        var intrinio_barchart_items = <?php echo Intrinio_Helper::json_encode($barchart_items); ?>;
	</script>
	<?php
}

function intrinio_shortcode_menu() {
	add_menu_page('Market Post Helper Settings', 'Market Post Helper Settings', 'administrator', 'intrinio-shortcode-settings', 'intrinio_shortcode_settings', 'dashicons-admin-generic');
    add_submenu_page('edit.php', 'MPH Bulk Publish', 'MPH Bulk Publish', 'manage_options', 'mph_bulk_publish', 'mph_bulk_publish');
}

function mph_bulk_publish() {
    $data = $_REQUEST;
    Intrinio_Shortcode::render_mph_bulk_publish_page($data);
}

function intrinio_shortcode_settings() {
	Intrinio_Shortcode::render_settings_page();
}

function init_intrinio_settings() {
	Intrinio_Shortcode::init();
}

function setup_tinymce_plugin() {

    // Check if the logged in WordPress User can edit Posts or Pages
    // If not, don't register our TinyMCE plugin
    if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
        return;
    }

    // Check if the logged in WordPress User has the Visual Editor enabled
    // If not, don't register our TinyMCE plugin
    if ( get_user_option( 'rich_editing' ) !== 'true' ) {
        return;
    }

    // Setup some filters
    add_filter( 'mce_external_plugins', 'add_tinymce_plugin');
    add_filter( 'mce_buttons', 'add_tinymce_toolbar_button');

}

function add_tinymce_plugin( $plugin_array ) {

    $plugin_array['intrinio_shortcode'] = plugin_dir_url( __FILE__ ) . 'js/tinymce-intrinio-shortcode.js';
    $plugin_array['intrinio_template'] = plugin_dir_url( __FILE__ ) . 'js/tinymce-intrinio-template.js';
    $plugin_array['intrinio_replacecode'] = plugin_dir_url( __FILE__ ) . 'js/tinymce-intrinio-replacecode.js';
    $plugin_array['intrinio_preload'] = plugin_dir_url( __FILE__ ) . 'js/tinymce-intrinio-preload.js';
    return $plugin_array;

}

function add_tinymce_toolbar_button( $buttons ) {

	array_push( $buttons, 'separator' );
    array_push( $buttons, 'intrinio_shortcode' );
    array_push( $buttons, 'intrinio_template' );
    array_push( $buttons, 'intrinio_replacecode' );
    array_push( $buttons, 'intrinio_preload' );

    return $buttons;

}

function intrinio_load_comapines() {
	$config = array(
		'user' => get_option('intrinio_api_username'),
		'password' => get_option('intrinio_api_password'),
	);
	$api = new Intrinio_API($config);
	$results = $api->call('companies?query=' . $_REQUEST['term']);

	$res = [];
	if (isset($results['data'])) {
		foreach ($results['data'] as $c) {
			$row = array(
				'id' => $c['ticker'],
				'value' => $c['ticker'],
				// 'label' => $c['ticker'],
				'label' => $c['ticker'] . ' - ' . $c['name'],
			);
			$res[] = $row;
		}
	}

	header('Content-Type: application/json');
	echo json_encode($res);
	exit;
}

function intrinio_load_barchart() {

	echo Intrinio_Shortcode::get_from_barchart($_REQUEST['ticker'], $_REQUEST['value']);
	exit;
}

function intrinio_import_finviz_items() {
    echo Intrinio_Shortcode::import_finviz_items();
    exit;
}

function intrinio_import_quandl_items() {
    Intrinio_Shortcode::import_quandl_items();
    exit;
}

function intrinio_preload() {
    $pattern = get_shortcode_regex();
    $data = Intrinio_Helper::make_data_safe($_REQUEST);
    Intrinio_Shortcode::intrinio_preload($data['title'], $data['content'], $pattern);
    exit;
}

function update_intrinio_shortcode ( $post_id ) {
	$post = get_post($post_id);

    $pattern = get_shortcode_regex();
    $post = Intrinio_Shortcode::process_intr_shortcode($post, $pattern);

    remove_action('save_post', 'update_intrinio_shortcode');
    wp_update_post($post);
    add_action('save_post', 'update_intrinio_shortcode');

}

function intrinio_process_shortcode($atts) {
	$attr = shortcode_atts(array('value'=>''), $atts);
    return $attr['value'];
}

function intrinio_process_rsi_code($atts) {
    $attr = shortcode_atts(array('value'=>'', 'color'=>'blue'), $atts);
    if ($attr['value'] == '') return '';

    $tpl = get_option('intrinio_stockta_rsi_' . $attr['color'] . '_text');
    return str_replace('{VALUE}', $attr['value'], $tpl);
}

function intrinio_process_ema_code($atts) {
    $attr = shortcode_atts(array('value'=>'', 'color'=>'blue'), $atts);
    if ($attr['value'] == '') return '';

    $tpl = get_option('intrinio_stockta_ema_' . $attr['color'] . '_text');
    $values = explode('|', $attr['value']);
    return str_replace(['{VALUE1}', '{VALUE2}'], [$values[0], $values[1]], $tpl);
}

function intrinio_process_chart($atts) {
	$attr = shortcode_atts(array('src'=>''), $atts);
    return '<a class="fancybox" href=' . $attr['src'] . '><img  src=' . $attr['src'] . '></a>';


	$ticker = $atts['ticker'];
	$img = '';
	if (!$ticker) {
		$img = '<img src="' . plugin_dir_url(__FILE__) . '/intrinio-chart.php?ticker=' . $ticker . '">';
	}
    return $attr['value'];
}

function intrinio_process_bto($atts) {
	$attr = shortcode_atts(array('content'=>''), $atts);

	$content = $attr['content'];
	if ($content != '') {
		$content = substr($content, 0, strlen($content)-1);	
	}
    return base64_decode($content);
}

function intrinio_process_summary($atts) {
	$attr = shortcode_atts(array('content'=>''), $atts);

	$content = $attr['content'];
	if ($content != '') {
		$content = substr($content, 0, strlen($content)-1);	
	}
    return base64_decode($content);
}