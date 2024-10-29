<?php
/*
Plugin Name: Altra Side Menu
Plugin URI: http://pulseextensions.com/wordpress-altra-side-menu.html
Description: You can upload your own icon for menu, set your menu URL, choose weather you want to display vertical-left or verticle-right. You can use the shortcode [altra-side-menu] in page/post, template tag for php file <strong>&lt;?php if ( function_exists('altra_side_menu') ) echo altra_side_menu(); ?&gt;</strong>.
Version: 2.0
Author: Pulse Extensions
Author URI: http://www.pulseextensions.com
License: GPL2
*/

if( !defined('ABSPATH') ) die('Error');
$upload_dir = wp_upload_dir();
$baseDir = $upload_dir['basedir'].'/';
$baseURL = $upload_dir['baseurl'].'';
$pluginsURI = plugins_url('/altra-side-menu/');

add_action('init', 'altra_my_script');
add_action('init', 'altra_side_process_post');
add_action('wp_ajax_update-menu-icon-order', 'altra_side_save_ajax_order' );
add_action('admin_menu', 'altra_add_menu_pages');
add_action('admin_enqueue_scripts', 'altra_admin_style');

if( isset($_GET['page']) ) {
	if( $_GET['page']=='altra_menu_icon_add' ) {
		add_action('admin_enqueue_scripts', 'altra_admin_enqueue' );
	}
}

register_activation_hook(__FILE__,'altra_db_install');
add_shortcode('altra-side-menu', 'altra_side_menu');

if (isset($_GET['delete'])) {
	
	if ($_GET['id'] != '')
	{
		$table_name = $wpdb->prefix . "altra_side_menu";
		$image_file_path = $baseDir;
		$wpdb->delete( $table_name, array( 'id' => $_GET['id'] ), array( '%d' ) );
		$msg = "Delete Successfully!!!"."<br />";
	}
}

function altra_admin_style() {
	global $cnssPluginsURI;
	 wp_register_style( 'altra_admin_css', plugins_url( '/css/admin-style.css', __FILE__), array(), false, 'all' );
        wp_enqueue_style( 'altra_admin_css' );
}

function altra_my_script() {
	global $pluginsURI;
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script('jquery-ui-sortable');
	wp_register_style( 'altra-side-menu', plugins_url( '/css/altra.css', __FILE__), array(), false, 'all' );
	wp_enqueue_style( 'altra-side-menu' );	
}

function altra_admin_enqueue() {
	global $pluginsURI;
	wp_enqueue_media();
	wp_register_script('altra_admin_js',plugins_url( '/js/altra_admin.js', __FILE__), array(), false, false);
	wp_enqueue_script( 'altra_admin_js' );
}

function altra_add_menu_pages() {
	add_menu_page('Altra Side Menu', 'Altra Side Menu', 'manage_options', 'altra_menu_icon_page',
	 'altra_menu_icon_sort_fn',plugins_url('/images/settings.png', __FILE__) );
	
	add_submenu_page('altra_menu_icon_page', 'Manage Menu', 'Manage Menu', 'manage_options', 'altra_menu_icon_page', 'altra_menu_icon_sort_fn');
	
	add_submenu_page('altra_menu_icon_page', 'Add Menu', 'Add Menu', 'manage_options', 'altra_menu_icon_add', 'altra_menu_icon_add_fn');
		
	add_submenu_page('altra_menu_icon_page', 'Options', 'Options', 'manage_options', 'altra_menu_icon_option', 'altra_menu_icon_option_fn');
	
	add_action( 'admin_init', 'register_altra_settings' );
	}
	//function admin menu
function register_altra_settings() {
    register_setting( 'altra-settings-group', 'altra-jquery-load' );
	register_setting( 'altra-settings-group', 'altra-width' );
	register_setting( 'altra-settings-group', 'altra-height' );
	register_setting( 'altra-settings-group', 'altra-margin' );
	register_setting( 'altra-settings-group', 'altra-row-count' );
	register_setting( 'altra-settings-group', 'altra-menu-direction' );
	register_setting( 'altra-settings-group', 'altra-opacity' );
	register_setting( 'altra-settings-group', 'altra-menu-bet-box' );
	register_setting( 'altra-settings-group', 'altra-bg-color' );
	register_setting( 'altra-settings-group', 'altra-border-color' );
	register_setting( 'altra-settings-group', 'altra-border-size' );
	register_setting( 'altra-settings-group', 'altra-font-color' );
	register_setting( 'altra-settings-group', 'altra-font-size' );
	register_setting( 'altra-settings-group', 'altra-font-weight' );
	register_setting( 'altra-settings-group', 'altra-font-family' );
	register_setting( 'altra-settings-group', 'altra-keep-data' );
	register_setting( 'altra-settings-group', 'altra-background-image-size' );
}
/*Form For Options Setting*/
function altra_menu_icon_option_fn() {
    $altra_jquery_load =get_option('altra-jquery-load');
	$altra_width = get_option('altra-width');
	$altra_height = get_option('altra-height');
	$altra_margin = get_option('altra-margin');
	$altra_rows = get_option('altra-row-count');
	$text_align = get_option('altra-menu-direction');
	$altra_opacity = get_option('altra-opacity');
	$altra_menu_bet_box = get_option('altra-menu-bet-box');
	$altra_bg_color = get_option('altra-bg-color');
	$altra_border_color = get_option('altra-border-color');
	$altra_border_size = get_option('altra-border-size');
	$altra_font_color = get_option('altra-font-color');
	$altra_font_size = get_option('altra-font-size');
	$altra_font_weight = get_option('altra-font-weight');
	$altra_font_family = get_option('altra-font-family');
	$altra_keep_data = get_option('altra-keep-data');
    $altra_background_image_size = get_option('altra-background-image-size');

    $jquery_no1 ='';
	$jquery_yes1 ='';
	if($altra_jquery_load=='no') $jquery_no1 = 'checked="checked"';
	if($altra_jquery_load=='yes') $jquery_yes1 = 'checked="checked"';

	$left ='';
	$right ='';
	if($text_align=='left') $left = 'checked="checked"';
	if($text_align=='right') $right = 'checked="checked"';

	$yes ='';
	$no ='';
	if($altra_keep_data=='yes') $yes = 'checked="checked"';
	if($altra_keep_data=='no') $no = 'checked="checked"';
?>
<script type="text/javascript">
	$(document).ready(function(){
			$('ul.tabs li').click(function(){
				var tab_id = $(this).attr('data-tab');
				$('ul.tabs li').removeClass('current');
				$('.tab-content').removeClass('current');
				$(this).addClass('current');
				$("#"+tab_id).addClass('current');
			});
		});
</script>
	
<div class="wrap">
	<form method="post" action="options.php">
		<?php settings_fields( 'altra-settings-group' ); ?>
		<div id="post-body-content" class="gallery-options">
			<div id="post-body-heading">
				<h3>General Options</h3>
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</div>
			<div style="clear: both;"></div>
			<div id="gallery-options-list">
				<ul id="gallery-view-tabs" class="tabs">
					<li class="tab-link current" data-tab="tab-1"><a href="#gallery-view-options-0">GENERAL SETTINGS</a></li>
				</ul>
				<div id="tab-1" class="tab-content current">
					<table class="form-table" style="width:42%;">
						<tr valign="top">
							<th scope="row">JQUERY LOAD</th>
							<td>
								<input <?php echo esc_attr($jquery_no1); ?> type="radio" name="altra-jquery-load" id="altra-jquery-load" value="no" />&nbsp;<label for="no">No</label><br />
							<input <?php echo esc_attr($jquery_yes1); ?> type="radio" name="altra-jquery-load" id="altra-jquery-load" value="yes" />&nbsp;<label for="yes">Yes</label>
							</td>
						</tr>
					    <tr valign="top">
							<th scope="row">MENU DIRECTION</th>
							<td>
							<input <?php echo esc_attr($left) ?> type="radio" name="altra-menu-direction" id="left" value="left" />&nbsp;<label for="left">Left</label><br />
							<input <?php echo esc_attr($right) ?> type="radio" name="altra-menu-direction" id="right" value="right" />&nbsp;<label for="right">Right</label>
					        </td>
						</tr>
						<tr valign="top">
							<th scope="row">BOX WIDTH</th>
							<td>
					        <input type="text" name="altra-width" id="altra-width" class="text" value="<?php echo esc_attr($altra_width)?>"/>px
					        </td>
						</tr>
						<tr valign="top">
							<th scope="row">BOX HEIGHT</th>
							<td>
					        <input type="text" name="altra-height" id="altra-height" class="text" value="<?php echo esc_attr($altra_height)?>" />px
					        </td>
						</tr>
						<tr valign="top">
							<th scope="row">TOP SPACE</th>
							<td>
					        <input type="text" name="altra-margin" id="altra-margin" class="text" value="<?php echo esc_attr($altra_margin)?>" />px
					        </td>
						</tr>
					   
					   	<tr valign="top">
							<th scope="row">MENU OPACITY</th>
					        <td>
					        <input type="text" name="altra-opacity" id="altra-opacity" class="text" value="<?php echo esc_attr($altra_opacity)?>" />
					        </td>
						</tr>
					    <tr valign="top">
							<th scope="row">SPACE BETWEEN BLOCK</th>
							<td>
					        <input type="text" name="altra-menu-bet-box" id="altra-menu-bet-box" class="text" value="<?php echo esc_attr($altra_menu_bet_box)?>" />px
					        </td>
						</tr>
						<tr valign="top">
						<th scope="row">BACKGROUND COLOR</th>
							<td>
					        <input type="color" name="altra-bg-color" id="altra-bg-color" class="text" value="<?php echo esc_attr($altra_bg_color)?>"/>
					         </td>
						</tr>
						<tr valign="top">
							<th scope="row">BORDER SIZE</th>
							<td>
					        <input type="text" name="altra-border-size" id="altra-border-size" class="text" value="<?php echo esc_attr($altra_border_size)?>" />px
					        </td>
						</tr>
					    <tr valign="top">
						<th scope="row">BORDER COLOR</th>
							<td>
					        <input type="color" name="altra-border-color" id="altra-border-color" class="text" value="<?php echo esc_attr($altra_border_color)?>" />
					        </td>
						</tr>
					    <tr valign="top">
						<th scope="row">FONT COLOR</th>
							<td>
					        <input type="color" name="altra-font-color" id="altra-font-color" class="text" value="<?php echo esc_attr($altra_font_color)?>" />
					        </td>
						</tr>
					    <tr valign="top">
							<th scope="row">FONT SIZE</th>
							<td>
					        <input type="text" name="altra-font-size" id="altra-font-size" class="text" value="<?php echo esc_attr($altra_font_size)?>" />px
					        </td>
						</tr>
							<tr valign="top">
							<th scope="row">FONT WEIGHT</th>
							<td>
					        <input type="text" name="altra-font-weight" id="altra-font-weight" class="text" value="<?php echo esc_attr($altra_font_weight)?>" />
					        </td>
						</tr>
					    <tr valign="top">
						<th scope="row">FONT FAMILY</th>
							<td>
					        <input type="text" name="altra-font-family" id="altra-font-family" class="text" value="<?php echo esc_attr($altra_font_family)?>" />
					        </td>
						</tr>
					    <tr valign="top">
						<th scope="row">KEEP DATA WHEN DELETING THE PLUGIN</th>
							<td>
							<input <?php echo $no ?> type="radio" name="altra-keep-data" id="altra-keep-data" value="no" />&nbsp;<label for="no">No</label><br />
							<input <?php echo $yes ?> type="radio" name="altra-keep-data" id="altra-keep-data" value="yes" />&nbsp;<label for="yes">Yes</label>
					        </td>
						</tr>	
						<tr valign="top">
						<th scope="row">BACKGROUND IMAGE SIZE</th>
							<td>
					        <input type="text" name="altra-background-image-size" id="altra-background-image-size" class="text" value="<?php echo esc_attr($altra_background_image_size)?>" />(It must be in Pixel Ex: <b>40px 40px</b> )
					        </td>
						</tr>
					</table>
				</div>
			</div>
		</form>
	
<?php }
function altra_db_install () {
	global $wpdb;
	global $altra_db_version;
	$upload_dir = wp_upload_dir();
	$table_name = $wpdb->prefix . "altra_side_menu";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
	$sql2 = "CREATE TABLE `$table_name` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT, 
	`title` VARCHAR(255) NULL, 
	`url` VARCHAR(255) NOT NULL, 
	`image_url` VARCHAR(255) NOT NULL, 
	`sortorder` INT NOT NULL DEFAULT '0', 
	`date_upload` VARCHAR(100) NULL, 
	`target` tinyint(1) NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`)) ENGINE = InnoDB;";
	$altraside_url = plugins_url('/images/', __FILE__);
	$altraside_wimg1 = $altraside_url .'img1.png';
	$altraside_wimg2 = $altraside_url .'img2.png';
	$altraside_wimg3 = $altraside_url .'img3.png';
	$altraside_wimg4 = $altraside_url .'img4.png';
	$altraside_wimg5 = $altraside_url .'img5.png';
	
	$altraside_db="INSERT INTO `$table_name` (`id`, `title`, `url`, `image_url`, `sortorder`, `date_upload`, `target`) VALUES
	(1, 'Home', 'http://www.google.com/', '$altraside_wimg1', 0, '1532410434', 1),
	(2, 'Camera',  'http://www.facebook.com/', '$altraside_wimg2', 1, '1532413671', 1),
	(3, 'Mail',  'http://www.facebook.com/', '$altraside_wimg3', 2, '1532491705', 1),
	(4, 'Download','https://www.google.co.in', '$altraside_wimg4', 3, '1532491554', 1),
	(5, 'Search', 'https://www.google.co.in', '$altraside_wimg5', 4, '1532491556', 1)";
	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql2);
	dbDelta($altraside_db);
	
    add_option( 'altra-jquery-load', 'yes');
	add_option( 'altra-width', '70');
	add_option( 'altra-height', '70');
	add_option( 'altra-margin', '50');
	add_option( 'altra-row-count', '1');
	add_option( 'altra-menu-direction', 'left');
	add_option( 'altra-opacity', '9');
	add_option( 'altra-menu-bet-box', '2');
	add_option( 'altra-bg-color', '#c7fcc2');
	add_option( 'altra-border-color', '#14d902');
	add_option( 'altra-border-size', '1px');
	add_option( 'altra-font-color', '#000000');
	add_option( 'altra-font-size', '16');
	add_option( 'altra-font-weight', 'bold');
	add_option( 'altra-font-family', 'calibri');
	add_option( 'altra-keep-data', 'no');
	add_option( 'altra-image-width', '70');
	add_option( 'altra-background-image-size','40px 40px');
	
  }
}

function altra_side_process_post(){
	global $wpdb,$err,$msg,$baseDir;
	if ( isset($_POST['altra_side_submit_button']) && check_admin_referer('altra_insert_icon') ) {
	
		if ($_POST['action'] == 'update')
		{
		
			$err = "";
			$msg = "";
			
			$image_file_path = $baseDir;
				
			if ($err == '')
			{
				$table_name = $wpdb->prefix . "altra_side_menu";
				$results = $wpdb->insert( 
					$table_name, 
					array( 
						'title' => sanitize_text_field($_POST['title']), 
						'url' => sanitize_text_field($_POST['url']), 
						'image_url' => sanitize_text_field($_POST['image_file']), 
						'sortorder' => sanitize_text_field($_POST['sortorder']), 
						'date_upload' => time(), 
						'target' => sanitize_text_field($_POST['target']), 
					), 
					array( 
						'%s', 
						'%s',
						'%s', 
						'%d',
						'%s', 
						'%d',
					) 
				);
				
				if (!$results)
					$err .= "Fail to update database" . "<br />";
				else
					$msg .= "Add New Menu successfully !" . "<br />";
			
			}
		}// add function
		
		if ( $_POST['action'] == 'edit' and $_POST['id'] != '' )
		{
			$err = "";
			$msg = "";
			$url = sanitize_text_field($_POST['url']);
			$target = sanitize_text_field($_POST['target']);
			$image_file_path = $baseDir;
			
			$table_name = $wpdb->prefix . "altra_side_menu";
			$sql = "SELECT * FROM ".$table_name." WHERE id =".sanitize_text_field($_POST['id']);
			$video_info = $wpdb->get_results($sql);
			$image_file_name = $video_info[0]->image_url;
			$update = "";
			
			$type= 1;
			
			if ($err == '')
			{
				$table_name = $wpdb->prefix . "altra_side_menu";
				$result3 = $wpdb->update( 
					$table_name, 
					array( 
						'title' => sanitize_text_field($_POST['title']),
						'url' => sanitize_text_field($_POST['url']),
						'image_url' => sanitize_text_field($_POST['image_file']),
						'sortorder' => sanitize_text_field($_POST['sortorder']),
						'date_upload' => time(),
						'target' => sanitize_text_field($_POST['target']),
					), 
					array( 'id' => sanitize_text_field($_POST['id']) ), 
					array( 
						'%s',
						'%s',
						'%s',
						'%d',
						'%s',
						'%d',
					), 
					array( '%d' ) 
				);		
				
				if (false === $result3){
					$err .= "Update fails !". "<br />";
				}
				else
				{
					$msg = "Update successful !". "<br />";
				}
			}
			
		} // end edit function
	}
}//Altra_process_post end
function altra_menu_icon_sort_fn() {
	global $wpdb,$baseURL;
	
		if(isset($_POST['save']))
	{
		if(empty($_POST['delete_quote']))
		{
		$result = "Please select any Quote and after delete.";
		}
		else
		{
		$checkbox = sanitize_text_field($_POST['delete_quote']);
		for($i=0;$i<count($checkbox);$i++)
		{
		$del_id = $checkbox[$i];
		$table_name = $wpdb->prefix . "altra_side_menu";
		$wpdb->query("DELETE FROM $table_name WHERE id='".$del_id."'");
		$result = "Data deleted successfully !";
		}
		}
	}
	$altra_width = get_option('altra-width');
	$altra_height = get_option('altra-height');
	
	$image_file_path = $baseURL;
	$table_name = $wpdb->prefix . "altra_side_menu";
	$sql = "SELECT * FROM ".$table_name." WHERE 1 ORDER BY sortorder";
	$video_info = $wpdb->get_results($sql);

?>
	<div class="wrap">
     <h3 style="padding:8px;  background: none repeat scroll 0 0 #00a0d2;border-color: #0073aa;box-shadow: 0 1px 0 rgba(120, 200, 230, 0.5) inset, 0 1px 0 rgba(0, 0, 0, 0.15);color:#fff;font-weight:bold;">Manage Menu <span style="font-size:10px;">(Manage Ordering from Here.Just Drag and Drop)</span></h3>
	 
  
	 <script type="text/javascript">
        jQuery.noConflict();
            function show_confirm($title,$id){
                var x = confirm('Are you sure you want to delete '+$title+' slide ?');

                if(x){
                    jQuery.get("<?php echo $_SERVER['PHP_SELF'];?>?page=altra_menu_icon_page&delete=y&id=", { id:$id }, function(data){

                    });

                }else{
     
                    return false;
                }
            }

     </script>

     <div id="ajax-response"></div>
     <noscript>
     	<div class="error message">
     		<p><?php _e('This plugin can\'t work without javascript, because it\'s use drag and drop and AJAX.', 'cpt') ?></p>
     	</div>
     </noscript>
     <div id="order-post-type">
     	<form id="quotes-frm" method="post">
		<div class="multiple-delete">
			<button type="submit" class="add-new-h2" name="save">Delete</button>
		</div>
        <table class="widefat page fixed" cellspacing="0">
        	<thead>
			<tr valign="top">
			    <th class="check-column"><input id="asidem-selectall" type="checkbox"></th>	
				<th class="manage-column column-title" scope="col">Title</th>
				<th class="manage-column column-title" scope="col">URL</th>
				<th class="manage-column column-title" scope="col" width="100">Open In</th>
				<th class="manage-column column-title" scope="col" width="100">Icon</th>
                <th class="manage-column column-title" scope="col" width="100">Order</th>
				<th class="manage-column column-title" scope="col" width="70">Edit</th>
				<th class="manage-column column-title" scope="col" width="70">Delete</th>
			</tr>
		   </thead>
		</table>
		<ul id="side_sortable">
			<?php 
			foreach($video_info as $vdoinfo) { 
				if(strpos($vdoinfo->image_url,'/')===false)
					$image_url = $image_file_path.'/'.$vdoinfo->image_url;
				else
					$image_url = $vdoinfo->image_url;
			?>
			<li id="item_<?php echo esc_attr($vdoinfo->id) ?>">
				<table width="100%" border="0" cellspacing="0" cellpadding="0" >
					<tr style="background-color:#fff;">
						<td width="50" align="center"><input class="cb-element" id="asidem-select-<?php echo esc_attr($vdoinfo->id) ?>" name="delete_quote[]" value="<?php echo esc_attr($vdoinfo->id) ?>" type="checkbox"></td>
						<td style="padding:8px 10px;" colspan="2"><?php echo esc_html($vdoinfo->title);?></td>
						<td width="336">
							<?php echo esc_html($vdoinfo->url);?>
						</td>
						<td width="123">
							<?php echo $vdoinfo->target==1?'New Window':'Same Window' ?>
						</td>
		                <td width="146">&nbsp;
		                <img src="<?php echo esc_url($image_url);?>" border="0" width="<?php echo $altra_width-30 ?>" height="<?php echo $altra_height-30 ?>" alt="<?php echo esc_attr($vdoinfo->title);?>" />
		                </td>
		                <td width="105">
							<?php echo esc_html($vdoinfo->sortorder);?>
						</td>
						<td width="89">
						<a href="?page=altra_menu_icon_add&mode=edit&id=<?php echo esc_attr($vdoinfo->id);?>"><strong><img src="<?php echo plugins_url('/images/edit.png', __FILE__)?>" style="width: 20px;height: 20px;"></strong></a>
						</td>
						<td width="80">
						<a onclick="show_confirm('<?php echo addslashes($vdoinfo->title)?>','<?php echo $vdoinfo->id;?>');" href="?page=altra_menu_icon_page"><strong><img src="<?php echo plugins_url('/images/delete.png', __FILE__)?>" style="width: 20px;height: 20px;"></strong></a>
						</td>
						</tr>
		                </table>
						</li>
					<?php } ?>
					</ul>
					<table class="widefat page fixed" cellspacing="0" >
					<tfoot>
					<tr valign="top">
					<td class="manage-column column-cb check-column">
						<label class="screen-reader-text" for="cb-select-all-2">Select All</label>
						<input id="asidem-select-all-2" type="checkbox">
					</td>
					<th class="manage-column column-title" scope="col">Title</th>
					<th class="manage-column column-title" scope="col">URL</th>
					<th class="manage-column column-title" scope="col" width="100">Open In</th>
					<th class="manage-column column-title" scope="col" width="100">Icon</th>
					<th class="manage-column column-title" scope="col" width="100">Order</th>
					<th class="manage-column column-title" scope="col" width="70">Edit</th>
					<th class="manage-column column-title" scope="col" width="70">Delete</th>
					</tr>
					</tfoot>
					</table>	
					<div class="clear"></div>
				</div>

				<script>
					$("#asidem-selectall").click(function () {
					$('input:checkbox').not(this).prop('checked', this.checked);
					});
				</script>
			</form>
			<script type="text/javascript">
				jQuery(document).ready(function() {
				jQuery("#side_sortable").sortable({
					tolerance:'intersect',
					cursor:'pointer',
					items:'li',
					placeholder:'placeholder'
				});
				jQuery("#side_sortable").disableSelection();
				jQuery("#save-order").bind( "click", function() {
					jQuery.post( ajaxurl, { action:'update-menu-icon-order', order:jQuery("#side_sortable").sortable("serialize") }, function(response) {
						jQuery("#ajax-response").html('<div class="message updated fade"><p>Items Order Updated</p></div>');
						jQuery("#ajax-response div").delay(3000).hide("slow");
						window.location.reload();
						
					});
				});
				});
				
			</script>
	</div>
<?php
}//function for shorting
function altra_side_save_ajax_order() 
{
	global $wpdb;
	$table_name = $wpdb->prefix . "altra_side_menu";
	parse_str($_POST['order'], $data);
	if (is_array($data))
	foreach($data as $key => $values ) 
	{
	
		if ( $key == 'item' ) 
		{
			foreach( $values as $position => $id ) 
				{
					$wpdb->update( $table_name, array('sortorder' => $position), array('id' => $id) );
				} 
		} 
	
	}
}

function altra_menu_icon_add_fn() {

	global $err,$msg,$baseURL;
	
	$altra_width = get_option('altra-width');
	$altra_height = get_option('altra-height');
	$altra_image_width = get_option('altra-image-width');
	$altra_image_height = get_option('altra-image-height');

	if (isset($_GET['mode'])) {
		if ( $_GET['mode'] != '' and $_GET['mode'] == 'edit' and  $_GET['id'] != '' )
		{
			$page_title = 'Edit Menu';
			$uptxt = 'Menu Image';
			
			global $wpdb;
			$table_name = $wpdb->prefix . "altra_side_menu";
			$image_file_path = $baseURL;
			$sql = "SELECT * FROM ".$table_name." WHERE id =".sanitize_text_field($_GET['id']);
			$video_info = $wpdb->get_results($sql);
			
			if (!empty($video_info))
			{
				$id = $video_info[0]->id;
				$title = $video_info[0]->title;
				$url = $video_info[0]->url;
				$image_url = $video_info[0]->image_url;
				$sortorder = $video_info[0]->sortorder;
				$target = $video_info[0]->target;
				
				if(strpos($image_url,'/')===false)
					$image_url = $image_file_path.'/'.$image_url;
				else
					$image_url = $image_url;
				
			}
		}
	}
	else
	{
		$page_title = 'Add New Menu';
		$title = "";
		$url = "";
		$image_url = "";
		$blank_img = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7";
		$sortorder = "0";
		$target = "";
		$uptxt = 'Menu Image';
	}
?>
    <div class="wrap">
    <?php
    if($msg!='') echo '<div id="message" class="updated fade">'.$msg.'</div>';
    if($err!='') echo '<div id="message" class="error fade">'.$err.'</div>';
    ?>
    <h3 style="padding:8px; background: none repeat scroll 0 0 #00a0d2;border-color: #0073aa;box-shadow: 0 1px 0 rgba(120, 200, 230, 0.5) inset, 0 1px 0 rgba(0, 0, 0, 0.15);color:#fff;font-weight:bold;width:663px;"><?php echo esc_html($page_title);?></h3>
    <form method="post" enctype="multipart/form-data" action="<?php //echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
    <?php wp_nonce_field('altra_insert_icon'); ?>
    <table class="form-table">
        <tr valign="top">
			<th scope="row">Menu Title</th>
			<td>
				<input type="text" name="title" id="title" class="regular-text" value="<?php echo esc_attr($title)?>" />
			</td>
        </tr>
        <tr valign="top">
			<th scope="row"><?php echo esc_html($uptxt);?></th>
			<td>
				

				<input style="vertical-align:top" type="text" name="image_file" id="image_file" class="regular-text" value="<?php echo esc_attr($image_url) ?>" />
				<input style="vertical-align:top" id="logo_image_button" class="button" type="button" value="Choose Image" />
				<img style="vertical-align:top" id="logoimg" src="<?php echo $image_url==''?$blank_img:$image_url; ?>" border="0"  width="40"  height="40" alt="<?php echo esc_attr($title)?>" /><br />
			</td>
        </tr>	
        <tr valign="top">
			<th scope="row">Menu URL</th>
			<td><input type="text" name="url" id="url" class="regular-text" value="<?php echo esc_attr($url)?>" /><br /><i>Example: <strong>http://facebook.com/your-fan-page</strong> &ndash; don't forget the <strong><code>http://</code></strong></i></td>
        </tr>		
        <tr valign="top">
			<th scope="row">Order Number</th>
			<td>
				<input type="text" name="sortorder" id="sortorder" class="small-text" value="<?php echo esc_attr($sortorder)?>" />
			</td>
        </tr>		
		<tr valign="top">
			<th scope="row">Target</th>
			<td>
				<input type="radio" name="target" id="new" checked="checked" value="1" />&nbsp;<label for="new">Open new window</label>&nbsp;<br />
				<input type="radio" name="target" id="same" value="0" />&nbsp;<label for="same">Open same window</label>&nbsp;
			</td>
        </tr>				
    </table>	
	<?php if (isset($_GET['mode']) ) { ?>
	<input type="hidden" name="action" value="edit" />
	<input type="hidden" name="id" id="id" value="<?php echo esc_attr($id);?>" />
	<?php } else {?>
	<input type="hidden" name="action" value="update" />
	<?php } ?>
    <p class="submit">
    <input type="submit" id="altra_side_submit_button" name="altra_side_submit_button" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
    </form>
    </div>
<?php 
} 

function altra_side_format_titles($str) {
	$pattern = '/[^a-zA-Z0-9]/';
	return preg_replace($pattern,'-',$str);
}

function altra_side_menu() {
		$urlpath = get_site_url();
		$altra_jquery_load = get_option('altra-jquery-load');
		$altra_width = get_option('altra-width');
		$altra_height = get_option('altra-height');
		$altra_margin = get_option('altra-margin');
		$altra_rows = get_option('altra-row-count');
		$altra_opacity = get_option('altra-opacity');
		$altra_menu_bet_box = get_option('altra-menu-bet-box');
		$altra_bg_color = get_option('altra-bg-color');
		$altra_font_color = get_option('altra-font-color');
		$altra_font_size = get_option('altra-font-size');
		$altra_font_weight = get_option('altra-font-weight');
		$altra_font_family = get_option('altra-font-family');
		$altra_border_color = get_option('altra-border-color');
		$text_align = get_option('altra-menu-direction');
		$altra_keep_data = get_option('altra-keep-data');
		$altra_border_size = get_option('altra-border-size');
		$altra_background_image_size = get_option('altra-background-image-size');
		$altra_widths= $altra_width-15;
			if($jelly_jquery_load == 'yes') { wp_enqueue_script('jquery-3.3.1.min', plugins_url('/js/jquery-3.3.1.min', __FILE__ ), array('jquery'), false, false);}
		if($text_align == 'right'){ 
		echo "<script>
		jQuery(function() {
		jQuery('#navigation a').stop().animate({'marginLeft':'-10px'},1000);
		jQuery('#navigation > li').hover(
		function () {
		jQuery('a',jQuery(this)).stop().animate({'marginLeft':'-".$altra_widths."px'},200);
		},
		function () {
		jQuery('a',jQuery(this)).stop().animate({'marginLeft':'-10px'},200);
		}
		);
		});
		</script>"; 
		} else {
		echo "<script>
		jQuery(function() {
  		jQuery('#navigation a').stop().animate({'marginLeft':'-".$altra_widths."px'},1000);
  		jQuery('#navigation > li').hover(
  	 	function () {
   		jQuery('a',jQuery(this)).stop().animate({'marginLeft':'-2px'},200);
   		},
  		function () {
  		jQuery('a',jQuery(this)).stop().animate({'marginLeft':'-".$altra_widths."px'},200);
  		}
 		);
		});
 		</script>";	
		}
		
		global $wpdb,$baseURL;
		$table_name = $wpdb->prefix . "altra_side_menu";
		$image_file_path = $baseURL;
		$sql = "SELECT * FROM ".$table_name." WHERE image_url<>'' AND url<>'' ORDER BY sortorder";
		$video_info = $wpdb->get_results($sql);
		$icon_count = count($video_info);
	
		$_collectionSize = count($video_info);
		$_rowCount = $altra_rows ? $altra_rows : 1;
		$_columnCount = ceil($_collectionSize/$_rowCount);
		$li_margin = round($altra_margin/2);
		?>

		<ul id="navigation" style="top:<?php echo esc_attr($altra_margin)?>px;<?php echo esc_attr($text_align)?>:0;">
			<?php
			$i=0;
			foreach($video_info as $icon)
				{
					if(strpos($icon->image_url,'/')===false)
			            $image_url = $image_file_path.'/'.$icon->image_url;
			        else
			            $image_url = $icon->image_url;
			        ?>
			        <li style="display: contents !important;" class="menu">
			        	<a <?php echo ($icon->target==1)?'target="_blank"':'target="_parent"' ?> title="<?php echo esc_attr($icon->title) ?>" href="<?php echo esc_url($icon->url) ?>" style="background-image:url(<?php echo $image_url?>);    width:<?php echo esc_attr($altra_width)?>px;
			        	height:<?php echo esc_attr($altra_height)?>px; margin-<?php echo $text_align?>: -77px;opacity:0.<?php echo esc_attr($altra_opacity)?>;filter:progid:DXImageTransform.Microsoft.Alpha(opacity=<?php echo esc_attr($altra_opacity)?>0);background-color:<?php echo esc_attr($altra_bg_color)?>;background-size:<?php echo esc_attr($altra_background_image_size);?>;border:<?php echo esc_attr($altra_border_size)?>px solid <?php echo esc_attr($altra_border_color)?>;margin-bottom:<?php echo esc_attr($altra_menu_bet_box)?>px;color:<?php echo esc_attr($altra_font_color);?> !important;">
			        	<p class="linkp"  style="font-size:<?php echo esc_attr($altra_font_size)?>px;font-family:<?php echo esc_attr($altra_font_family)?>;font-weight:<?php echo esc_attr($altra_font_weight)?>;color:<?php echo esc_attr($altra_font_color);?> !important;"><?php echo esc_attr($icon->title) ?></p>
			        	</a>
			        </li>
			        <?php
			        $i++;
			        }
			        ?>
		</ul>
		<?php
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}
	?>
	<?php
	/**
	* Uninstallation
	*/
	function altra_side_menu_uninstall() {
		global $wpdb;
		$altra_keep_data = get_option( 'altra-keep-data');
		if ( $altra_keep_data == 'no' )
			{
				$table_name = $wpdb->prefix . "altra_side_menu";
				$wpdb->query("DROP TABLE IF EXISTS $table_name");
			}
		}
		if ( function_exists('register_uninstall_hook') )
			{
				register_uninstall_hook( __FILE__, 'altra_side_menu_uninstall' );
			}
			?>


