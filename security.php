<?php
/**
 * Security File
 * - Disable comments
 * - Disable meta wordpress
 * - Disable theme wordpress edit in BO
 * - Disable automatic update
 * 
 * @see http://wpchannel.com/masquer-numero-version-votre-site-wordpress/
 * @see https://www.dfactory.eu/turn-off-disable-comments/
 * @see https://www.icontrolwp.com/2012/11/more-wordpress-security-disallow-file-edit-setting-wordpress/
 * @see http://codex.wordpress.org/Hardening_WordPress
 * @see http://www.wpbeginner.com/wp-tutorials/9-most-useful-htaccess-tricks-for-wordpress/
 * 
 * test slack integration
 * 
 */


//Enlever les balises meta générés par wordpress
remove_action('wp_head', 'wp_generator');
//on enleve l'editeur dans le BO
define( 'DISALLOW_FILE_EDIT', true );

//on enlève la mise à jour auto
define( 'WP_AUTO_UPDATE_CORE', false );


/* supression automatique du fichier readme.html et license.txt */
add_action('init', 'remove_unecessary_file');
function remove_unecessary_file(){
    
    $tpl_dir = parse_url(get_template_directory_uri() . '/functions');
    $path = str_replace($tpl_dir['path'], '', dirname(__FILE__));
    
    if(file_exists($path . '/readme.html')){
        unlink($path . '/readme.html');
    }
    
    if(file_exists($path . '/license.txt')){
        unlink($path . '/license.txt');
    }
    
}





// Disable support for comments and trackbacks in post types
function df_disable_comments_post_types_support() {
	$post_types = get_post_types();
	foreach ($post_types as $post_type) {
		if(post_type_supports($post_type, 'comments')) {
			remove_post_type_support($post_type, 'comments');
			remove_post_type_support($post_type, 'trackbacks');
		}
	}
}
add_action('admin_init', 'df_disable_comments_post_types_support');

// Close comments on the front-end
function df_disable_comments_status() {
	return false;
}
add_filter('comments_open', 'df_disable_comments_status', 20, 2);
add_filter('pings_open', 'df_disable_comments_status', 20, 2);

// Hide existing comments
function df_disable_comments_hide_existing_comments($comments) {
	$comments = array();
	return $comments;
}
add_filter('comments_array', 'df_disable_comments_hide_existing_comments', 10, 2);

// Remove comments page in menu
function df_disable_comments_admin_menu() {
	remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'df_disable_comments_admin_menu');

// Redirect any user trying to access comments page
function df_disable_comments_admin_menu_redirect() {
	global $pagenow;
	if ($pagenow === 'edit-comments.php') {
		wp_redirect(admin_url()); exit;
	}
}
add_action('admin_init', 'df_disable_comments_admin_menu_redirect');

// Remove comments metabox from dashboard
function df_disable_comments_dashboard() {
	remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}
add_action('admin_init', 'df_disable_comments_dashboard');

// Remove comments links from admin bar
function df_disable_comments_admin_bar() {
	if (is_admin_bar_showing()) {
		remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
	}
}
add_action('init', 'df_disable_comments_admin_bar');
