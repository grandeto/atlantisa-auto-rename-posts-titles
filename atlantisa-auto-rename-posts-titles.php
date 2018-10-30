<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * Plugin Name: Atlantisa Auto Rename Posts Titles
 * Plugin URI: https://atlantisa.com
 * Description: This WordPress plugin helps you to auto rename posts' titles, slugs and dates on every status change of a post. It is useful if you are doing auto-blogging with a plugin like Auto Featured Image Post. You can set up this plugin to automatically rename the posts' titles for instance like Quote of the day, Beer of the day and etc.
 * Version: 1.0.0
 * Author: www.AtlantisA.com
 * Author URI: https://atlantisa.com
 * License: Apache License 2.0
 */

// Prepare admin menu
add_action('admin_menu', 'atlantisa_auto_rename_posts_titles_menu');
function atlantisa_auto_rename_posts_titles_menu() {
    add_submenu_page('options-general.php', 'Atlantisa Auto Rename Posts Titles Settings', 'Atlantisa Auto Rename Posts Titles', 'administrator', 'atlantisa-auto-rename-posts-titles-settings', 'atlantisa_auto_rename_posts_titles_settings_page');
}

// Prepare plugin settings page
add_action( 'admin_init', 'atlantisa_auto_rename_posts_titles_settings' );
function atlantisa_auto_rename_posts_titles_settings() {
    register_setting( 'atlantisa-auto-rename-posts-titles-settings-group', 'atlantisa_auto_post_title' );
    register_setting( 'atlantisa-auto-rename-posts-titles-settings-group', 'atlantisa_add_date_to_title' );
    register_setting( 'atlantisa-auto-rename-posts-titles-settings-group', 'atlantisa_title_delimiter' );
}



// Implement the main functionality
add_filter('wp_insert_post_data','reset_post_date',99,2);
function reset_post_date($data, $postarr) {
    $atlantisaTitle = esc_attr( get_option('atlantisa_auto_post_title') );
    $atlantisaDate = esc_attr( get_option('atlantisa_add_date_to_title') );
    $atlantisaDate = ($atlantisaDate != "" ? $atlantisaDate : "");
    $atlantisaDelimeter = ($atlantisaDate != "" ? ' - ' : "");
    
    if (get_post_type($post) == 'post' && $atlantisaTitle != "") {
        $data['post_date'] = $data['post_modified'];
        $data['post_date_gmt'] = $data['post_modified_gmt'];
        //Add post meta to post title
        $data['post_title'] = $atlantisaTitle . $atlantisaDelimeter . current_time($atlantisaDate);
        //Update the slug of the post for the URL
        $data['post_name'] = wp_unique_post_slug( sanitize_title( $data['post_title'] ), $postarr['ID'], $data['post_status'],$data['post_type'], $data['post_parent'] );
        return $data;  
    } else {
        return $data;
    }
}

// Get user options
function atlantisa_auto_rename_posts_titles_settings_page() {
?>
<div class="wrap">
<h2>Atlantisa Auto Rename Posts Titles</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'atlantisa-auto-rename-posts-titles-settings-group' ); ?>
    <?php do_settings_sections( 'atlantisa-auto-rename-posts-titles-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">My Auto Post Title</th>
        <td><input type="text" name="atlantisa_auto_post_title" value="<?php echo esc_attr( get_option('atlantisa_auto_post_title') ); ?>" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Delimiter</th>
        <td><input type="text" name="atlantisa_title_delimiter" value="<?php echo esc_attr( get_option('atlantisa_title_delimiter') ); ?>" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Add Date To Title ( d.m.Y )</th>
        <td><input type="text" name="atlantisa_add_date_to_title" value="<?php echo esc_attr( get_option('atlantisa_add_date_to_title') ); ?>" /></td>
        </tr>
    </table>
    <?php submit_button(); ?>

</form>
<h2 class="previewTitle"><span style="color:green;">Preview:</span> <?php echo esc_attr( get_option('atlantisa_auto_post_title') ) . ' ' . esc_attr( get_option('atlantisa_title_delimiter') ) . ' ' . current_time( esc_attr( get_option('atlantisa_add_date_to_title') ) ); ?></h2>
<?php echo 'Made with Love <i class="fa fa-heart heart"></i> by <a class="designByAtlantisa" href="https://atlantisa.com" target="_blank">www.AtlantisA.com</a>' ?>
</div>
<?php } ?>