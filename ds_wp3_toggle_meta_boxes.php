<?php 
/*
Plugin Name: Toggle Meta Boxes Sitewide
Plugin URI: http://wordpress.org/extend/plugins/toggle-meta-boxes-sitewide/
Description: WP3 multisite mu-plugin. Go to Site Admin-->Options to "Enable Administration Meta Boxes". Meta boxes(post, page, link, and dashboard) are unchecked and disabled by default. Extra options to toggle the Quick Edit buttons, Media buttons, Screen Options and Help links.
Author: D Sader
Version: 3.0.3.1
Author URI: http://dsader.snowotherway.org

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 
*/


//------------------------------------------------------------------------//
//---Hooks----------------------------------------------------------------//
//------------------------------------------------------------------------//
add_action( 'wpmu_options','ds_meta_box_option' ); // "Menu Settings->Enable Administration Menus->Plugins"
add_action( 'admin_head', 'ds_toggle_meta_boxes' ); // toggle metaboxes
add_action( 'network_admin_menu', 'ds_toggle_meta_boxes' ); // toggle metaboxes TODO parse these options for network admin dashboard.
add_action( 'admin_head', 'ds_extras_remove'  ); // toggle some extras

//------------------------------------------------------------------------//
//---Functions to Enable/Disable admin menus------------------------------//
//------------------------------------------------------------------------//

function ds_toggle_meta_boxes() {
		global $tax_name, $post_type;

	$menu_perms = get_site_option( "menu_items" );

	if( !isset($menu_perms[ 'super_admin_mb' ] ) && is_super_admin()) 
	return;
	
	/* POSTS edit-form-advanced.php	
	 */
		if( !isset($menu_perms[ 'format_mb' ]) ) 
			remove_meta_box('formatdiv', $post_type, 'side');		


		if( !isset($menu_perms[ 'publish_mb' ]) ) 
			remove_meta_box('submitdiv', $post_type, 'side');		

		if( !isset($menu_perms[ 'tags_mb' ]) ) 
			remove_meta_box('tagsdiv-' . $tax_name, $post_type, 'side');

		if( !isset($menu_perms[ 'tax_cats_mb' ]) ) 
			remove_meta_box($tax_name . 'div', $post_type, 'side');

		if( !isset($menu_perms[ 'cats_mb' ]) ) 
			remove_meta_box('categorydiv', $post_type, 'side');

		if( !isset($menu_perms[ 'att_mb' ]) ) 
			remove_meta_box('pageparentdiv', $post_type, 'side');

		if( !isset($menu_perms[ 'feat_img_mb' ]) ) 
			remove_meta_box('postimagediv', $post_type, 'side');

		if( !isset($menu_perms[ 'excerpt_mb' ]) ) 
			remove_meta_box('postexcerpt', $post_type, 'normal');

		if( !isset($menu_perms[ 'track_mb' ]) ) 
			remove_meta_box('trackbacksdiv', $post_type, 'normal');

		if( !isset($menu_perms[ 'custom_field_mb' ]) ) 
			remove_meta_box('postcustom', $post_type, 'normal');

		if( !isset($menu_perms[ 'disc_mb' ]) ) 
			remove_meta_box('commentstatusdiv', $post_type, 'normal');

		if( !isset($menu_perms[ 'slug_mb' ]) ) {
			remove_meta_box('slugdiv', $post_type, 'normal');
	 	}
		if( !isset($menu_perms[ 'author_mb' ]) ) 
			remove_meta_box('authordiv', $post_type, 'normal');
		if( !isset($menu_perms[ 'revs_mb' ]) ) 
			remove_meta_box('revisionsdiv', $post_type, 'normal'); // still saves aplenty unless redefined, though

		if( !isset($menu_perms[ 'comments_mb' ]) ) 
			remove_meta_box('commentsdiv', $post_type, 'normal');

	/* LINKS edit-link-form.php
	 */
	if(current_user_can('manage_links')) {
		if( !isset($menu_perms[ 'link_save_mb' ]) ) 
			remove_meta_box('linksubmitdiv', 'link', 'side');
		if( !isset($menu_perms[ 'link_cat_mb' ]) ) 
			remove_meta_box('linkcategorydiv', 'link', 'normal');
		if( !isset($menu_perms[ 'link_target_mb' ]) ) 
			remove_meta_box('linktargetdiv', 'link', 'normal');
		if( !isset($menu_perms[ 'link_xfn_mb' ]) ) 
			remove_meta_box('linkxfndiv', 'link', 'normal');
		if( !isset($menu_perms[ 'link_adv_mb' ]) ) 
			remove_meta_box('linkadvanceddiv', 'link', 'normal');
	}

	/* DASHBOARD dashboard.php
	*/
	if(current_user_can('read')) {
		if( !isset($menu_perms[ 'dash_prim_mb' ]) ) {
			remove_meta_box('dashboard_primary', 'dashboard', 'side');
			remove_meta_box('dashboard_primary', 'dashboard-network', 'side');
		}
		if( !isset($menu_perms[ 'dash_sec_mb' ]) ) {
			remove_meta_box('dashboard_secondary', 'dashboard', 'side');
			remove_meta_box('dashboard_secondary', 'dashboard-network', 'side');
		}
		if( !isset($menu_perms[ 'dash_links_mb' ]) ) 
			remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');
		if( !isset($menu_perms[ 'dash_comments_mb' ]) ) 
			remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
		if( !isset($menu_perms[ 'dash_right_now_mb' ]) ) 
			remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
	}
	if ( current_user_can('edit_posts') ) {
		if( !isset($menu_perms[ 'dash_drafts_mb' ]) ) 
			remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');
		if( !isset($menu_perms[ 'dash_quick_mb' ]) ) 
			remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
	}
	if ( is_super_admin() && current_user_can( 'activate_plugins' ) ) {
		if( !isset($menu_perms[ 'dash_plug_mb' ]) ) 
			remove_meta_box('dashboard_plugins', 'dashboard', 'normal');
			remove_meta_box('dashboard_plugins', 'dashboard-network', 'normal');
	}
/* COMMENTS
// TODO WP3.x doesn't make the comment editing form with meta_box similar to other edit forms
*/
}
/* Dashboards
// WP3.1 has 3 custom dashboards for users, admins, and Network admins
*/
//Define the function which unsets the boxes
function ds_remove_network_widgets() {
        global $wp_meta_boxes;

        # Remove "WordPress News"
        unset($wp_meta_boxes['dashboard-network']['side']['core']['dashboard_primary']);
        unset($wp_meta_boxes['dashboard-network']['side']['core']['dashboard_secondary']);
        unset($wp_meta_boxes['dashboard-network']['normal']['core']['dashboard_primary']);
        unset($wp_meta_boxes['dashboard-network']['normal']['core']['dashboard_secondary']);
}
// Now hook in to the action
//add_action('wp_network_dashboard_setup', 'ds_remove_network_widgets', 20, 0);

//------------------------------------------------------------------------//
//--- Function to toggle extra administration cruft----------------------//
//----Note: Media buttons limit file types by default in ms-options.php --//
//----"Quick Edit" inline editing: http://core.trac.wordpress.org/ticket/12940---//
//------------------------------------------------------------------------//

function ds_extras_remove() {
	$menu_perms = get_site_option( "menu_items" );
	if( is_array( $menu_perms ) == false )
		$menu_perms = array();
			if( !isset($menu_perms[ 'super_admin_mb' ] ) && is_super_admin())
			return;
	// css trickery for Slug/Permalink/Short URL
 	if( !isset($menu_perms[ 'edit_slug_box' ]) ) 
	 	echo '<style>#edit-slug-box { display:none;}</style>';
 	// css trickery for Screen Options and Help
	if( !isset($menu_perms[ 'screen_options_link' ]) ) 
	 	echo '<style>#screen-options-link-wrap { display: none;}</style>';
 	if( !isset($menu_perms[ 'contextual_help_link' ]) ) 
	 	echo '<style>#contextual-help-link-wrap { display: none;}</style>';
	// Quick Edit
 	//disable quickedit in post rows
 	if( !isset($menu_perms[ 'quick_edit_posts' ]) ) 
		add_filter('post_row_actions', create_function('$actions, $post', 'unset($actions["inline hide-if-no-js"]); return $actions ;'), 10, 2); 
 	//disable quickedit in page rows
 	if( !isset($menu_perms[ 'quick_edit_pages' ]) ) 
		add_filter('page_row_actions', create_function('$actions, $post', 'unset($actions["inline"]); return $actions ;'), 10, 2);
 	//disable quickedit in tag and category rows
 	if( !isset($menu_perms[ 'quick_edit_tags' ]) ) 
		add_filter('tag_row_actions', create_function('$actions, $post', 'unset($actions["inline hide-if-no-js"]); return $actions ;'), 10, 2);
	//disable quickedit in link rows
	if( !isset($menu_perms[ 'quick_edit_link_cats' ]) ) 
		add_filter('link_cat_row_actions', create_function('$actions, $post', 'unset($actions["inline hide-if-no-js"]); return $actions ;'), 10, 2);
	//disable quickedit in comment rows
	if( !isset($menu_perms[ 'quick_edit_comments' ]) ) 
		add_filter('comment_row_actions', create_function('$actions, $post', 'unset($actions["quickedit"]); return $actions ;'), 10, 2);
	// Media Buttons
	if( !isset($menu_perms[ 'media_buttons' ]) ) 
	 	remove_action( 'media_buttons', 'media_buttons' );
}

//------------------------------------------------------------------------//
//---Function SiteAdmin->Options------------------------------------------//
//---Options are saved as site_options on wpmu-options.php page-----------//
//------------------------------------------------------------------------//
function ds_meta_box_option() {
	$meta_perms = get_site_option( "menu_items" );
	if( is_array( $meta_perms ) == false )
		$meta_perms = array();
			$meta_items = array(
		'super_admin_mb'	=> __(  'Super Admin gets the following limited meta boxes, too?' ),

//Extras
		'edit_slug_box'			=> __(  'Edit Slug Box' ),
		'screen_options_link'	=> __(  'Screen Options Link' ),
		'contextual_help_link'	=> __(  'Contextual Help Link' ),
		'media_buttons'			=> __(  'Media Upload Buttons' ),
		'quick_edit_posts'		=> __(  'Quick Edit Posts' ),
		'quick_edit_pages'		=> __(  'Quick Edit Pages' ),
		'quick_edit_tags'		=> __(  'Quick Edit Tags and Cats' ),
		'quick_edit_link_cats'	=> __(  'Quick Edit Link Cats' ),
		'quick_edit_comments'	=> __(  'Quick Edit Comments' ),

//Meta Boxes
		'format_mb'			=> __(	'Format' ),
		'publish_mb'		=> __(	'Publish' ),
		'tags_mb'			=> __(	'Tags' ),
		'tax_cats_mb'		=> __(	'Taxonomy Categories' ),
		'cats_mb'			=> __(	'Categories' ),
		'att_mb'			=> __(	'Attributes' ),
		'feat_img_mb'		=> __(	'Featured Image' ),
		'excerpt_mb'		=> __(	'Excerpt' ),
		'track_mb'			=> __(	'Send Trackbacks' ),
		'custom_field_mb'	=> __(	'Custom Fields' ),
		'disc_mb'			=> __(	'Discussion' ),
		'slug_mb'			=> __(	'Slug' ),
		'author_mb'			=> __(	'Author' ),
		'revs_mb'			=> __(	'Revisions' ),
		'comments_mb'		=> __(	'Comments' ),
		'link_save_mb'		=> __(	'Link Save' ),
		'link_cat_mb'		=> __(	'Link Categories' ),
		'link_target_mb'	=> __(	'Link Target' ),
		'link_xfn_mb'		=> __(	'Link Relationship (XFN)' ),
		'link_adv_mb'		=> __(	'Link Advanced' ),
		'dash_prim_mb'		=> __(	'Dashboard Primary' ),
		'dash_sec_mb'		=> __(	'Dashboard Secondary' ),
		'dash_links_mb'		=> __(	'Dashboard Incoming Links' ),
		'dash_comments_mb'	=> __(	'Dashboard Recent Comments' ),
		'dash_right_now_mb'	=> __(	'Dashboard Right Now' ),
		'dash_drafts_mb'	=> __(	'Dashboard Recent Drafts' ),
		'dash_quick_mb'		=> __(	'Dashboard QuickPress' ),
		'dash_plug_mb'		=> __(	'Dashboard Plugins' )
			);

?>
		<h3><?php _e( 'Meta Boxes' ); ?></h3>
<?php
/*
global $wp_meta_boxes;
echo '<pre>';
print_r($wp_meta_boxes);
echo '</pre>';
*/
?>
 		<table id="menu" class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e( 'Enable Administration Meta Boxes' ); ?></th>
				<td>
			<?php
			foreach ( (array) $meta_items as $key => $val ) {
				echo "<label><input type='checkbox' name='menu_items[" . $key . "]' value='1'" .  ( isset( $meta_perms[$key] ) ? checked( $meta_perms[$key], '1', false ) : '' ) . " /> " . esc_html( $val ) . "</label><br/>";
			}
			?>
				</td>
			</tr>
		</table>	
<?php
}
?>