<?php 
/*
Plugin Name: Toggle Meta Boxes Sitewide
Plugin URI: http://wordpress.org/extend/plugins/toggle-meta-boxes-sitewide/
Version: 3.8.1
Description: WP3.8.1 multisite network mu-plugin. Go to Network-->Settings to "Enable Administration Meta Boxes". Meta boxes(post, page, link, and dashboard) are unchecked and disabled by default. Extra options to toggle the Quick Edit buttons, Media buttons, Screen Options and Help links. Toggle to Restrict Comment Editing to Editor+ roles. SuperAdmin comments can only be edited by a SuperAdmin.
Author: D Sader
Author URI: http://dsader.snowotherway.org
Network: true

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.
 
*/

class ds_meta {
		var $l10n_prefix;

	function ds_meta() {
			$this->l10n_prefix = 'toggle-meta-boxes-sitewide';

	//------------------------------------------------------------------------//
	//---Hooks----------------------------------------------------------------//
	//------------------------------------------------------------------------//
	add_action( 'admin_init', array(&$this, 'ds_localization_init' ));
	add_action( 'wpmu_options', array(&$this, 'ds_meta_box_option' )); // "Menu Settings->Enable Administration Menus->Plugins"
	add_action( 'admin_head', array(&$this, 'ds_toggle_meta_boxes' )); // toggle metaboxes
	add_action( 'admin_head', array(&$this, 'ds_extras_remove'  )); // toggle some extras
	add_action( 'wp_dashboard_setup', array(&$this, 'ds_remove_dashboard_widgets' ));
	add_action( 'wp_network_dashboard_setup', array(&$this, 'ds_remove_network_dashboard_widgets' ));
	add_filter( 'comment_row_actions', array(&$this, 'ds_remove_comment_edit'), 1, 2); //Comment Editing Restricition
	add_filter( 'map_meta_cap', array(&$this, 'ds_network_admin_restrict_comment_editing'), 10, 4 ); //Comment Editing Restricitons
	add_action( 'admin_head-nav-menus.php', array(&$this, 'ds_nav_menus' ));
//	add_filter( 'manage_nav-menus_columns', array(&$this, 'ds_nav_menu_manage_columns'),99 ); //not working yet: TODO
	
}


	function ds_localization_init() {
		load_plugin_textdomain( 'toggle-meta-boxes-sitewide', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	function ds_nav_menu_manage_columns($array) {
		//I want these advanced link properties hidden, better than this
		$menu_perms = get_site_option( "menu_items" );
		if( isset($menu_perms[ 'nav_menu_links_adv' ]) ) {

	
		$array = array(
			'_title' => __('Show advanced menu properties'),
			'cb' => '<input type="checkbox" />',
			'link-target' => __('Link Target'),
			'css-classes' => __('CSS Classes'),
			'xfn' => __('Link Relationship (XFN)'),
			'description' => __('Description'),
		);
//			$user = wp_get_current_user();
//			update_user_option($user->ID, 'managenav-menuscolumnshidden',
//			array( 0 => 'link-target', 1 => 'css-classes', 2 => 'xfn', 3 => 'description', ), true);

	return $array;
	}
}


	function ds_nav_menus() {
		// Menus nav-menus.php
  		if( current_user_can( 'edit_theme_options' )) {
 			$menu_perms = get_site_option( "menu_items" );
    		$screen = get_current_screen();  
   	
		if( !isset($menu_perms[ 'nav_menu_links' ]) ) 
 				remove_meta_box( 'add-custom-links', 'nav-menus','side' );

 		function ds_nav_menu_post_type_meta_boxes() {
			$post_types = get_post_types( array( 'show_in_nav_menus' => true ), 'object' );
				if ( ! $post_types )
				return;

			foreach ( $post_types as $post_type ) {
				if ( $post_type ) {
					$id = $post_type->name;
 				remove_meta_box("add-{$id}", 'nav-menus','side' );
				}
			}
 		}
 		
		function ds_nav_menu_taxonomy_meta_boxes() {
			$taxonomies = get_taxonomies( array( 'show_in_nav_menus' => true ), 'object' );
				if ( !$taxonomies )
				return;

			foreach ( $taxonomies as $tax ) {
				if ( $tax ) {
					$id = $tax->name;
				remove_meta_box("add-{$id}", 'nav-menus','side' );
				}
			}
		} 
		if( !isset($menu_perms[ 'nav_menu_pages' ]) ) ds_nav_menu_post_type_meta_boxes();
 		if( !isset($menu_perms[ 'nav_menu_cats' ]) ) ds_nav_menu_taxonomy_meta_boxes();
    	}
	}
    
	function ds_remove_dashboard_widgets() {	
	// DASHBOARD /wp-admin/
		$menu_perms = get_site_option( "menu_items" );
		if(current_user_can('read')) {
		if( !isset($menu_perms[ 'dash_prim_mb' ]) ) {
			remove_meta_box('dashboard_primary', 'dashboard', 'side');
		}
		if( !isset($menu_perms[ 'dash_sec_mb' ]) ) {
			remove_meta_box('dashboard_secondary', 'dashboard', 'side');
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
	}
	function ds_remove_network_dashboard_widgets() {
	// Network Dashboard wp-admin/network/		
		$menu_perms = get_site_option( "menu_items" );

		if( !isset($menu_perms[ 'dash_net_prim_mb' ]) ) {
			remove_meta_box('dashboard_primary', 'dashboard-network', 'side');
		}
		if( !isset($menu_perms[ 'dash_net_sec_mb' ]) ) {
			remove_meta_box('dashboard_secondary', 'dashboard-network', 'side');
		}
		if( !isset($menu_perms[ 'dash_net_plugins' ]) ) {
			remove_meta_box( 'dashboard_plugins', 'dashboard-network', 'normal' );
		}
		if( !isset($menu_perms[ 'dash_net_right_now_mb' ]) ) {
			remove_meta_box( 'network_dashboard_right_now', 'dashboard-network', 'normal' );
		}
	} 

	//------------------------------------------------------------------------//
	//---Functions to Enable/Disable admin menus------------------------------//
	//------------------------------------------------------------------------//
	function ds_toggle_meta_boxes() {
		global $tax_name, $post_type;

	$menu_perms = get_site_option( "menu_items" );

	if( !isset($menu_perms[ 'super_admin_mb' ] ) && is_super_admin()) 
	return;
	
	// POSTS edit-form-advanced.php	
	 
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

	// LINKS edit-link-form.php
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
	
	}

	//------------------------------------------------------------------------//
	//--- Function to toggle extra administration cruft----------------------//
	//----"Quick Edit" inline editing: http://core.trac.wordpress.org/ticket/12940---//
	//----Comment Editing Restritions----------------------------------------//
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
		add_filter('page_row_actions', create_function('$actions, $post', 'unset($actions["inline hide-if-no-js"]); return $actions ;'), 10, 2);

 	//disable quickedit in any tag rows
 	if( !isset($menu_perms[ 'quick_edit_tag' ]) ) 
		add_filter('tag_row_actions', create_function('$actions, $post', 'unset($actions["inline hide-if-no-js"]); return $actions ;'), 10, 2);
 	//disable quickedit in taxonomy=post_tag rows
 	if( !isset($menu_perms[ 'quick_edit_post_tag' ]) ) 
		add_filter('post_tag_row_actions', create_function('$actions, $post', 'unset($actions["inline hide-if-no-js"]); return $actions ;'), 10, 2);
 	//disable quickedit in taxonomy=category rows
 	if( !isset($menu_perms[ 'quick_edit_category' ]) ) 
		add_filter('category_row_actions', create_function('$actions, $post', 'unset($actions["inline hide-if-no-js"]); return $actions ;'), 10, 2);
	//disable quickedit in taxonomy=link_category rows
	if( !isset($menu_perms[ 'quick_edit_link_category' ]) ) 
		add_filter('link_category_row_actions', create_function('$actions, $post', 'unset($actions["inline hide-if-no-js"]); return $actions ;'), 10, 2);

	//disable quickedit in comment rows
	if( !isset($menu_perms[ 'quick_edit_comments' ]) ) 
		add_filter('comment_row_actions', create_function('$actions, $post', 'unset($actions["quickedit"]); return $actions ;'), 10, 2);
	// Media Buttons
	if( !isset($menu_perms[ 'media_buttons' ]) ) 
	 	remove_action( 'media_buttons', 'media_buttons' );
	
	//Welcome Panel
	if( !isset($menu_perms[ 'welcome_panel' ]) ) 
	 	remove_action( 'welcome_panel', 'wp_welcome_panel' );
	}

	//Comment Editing Restrictions
	// http://scribu.net/wordpress/prevent-blog-authors-from-editing-comments.html
	function ds_network_admin_restrict_comment_editing( $caps, $cap, $user_id, $args ) {
		$menu_perms = get_site_option( "menu_items" );

		if( isset($menu_perms[ 'comment_edit' ]) ) {

			if ( 'edit_comment' == $cap ) {
				$comment = get_comment( $args[0] );
  			
			if ( is_super_admin( $comment->user_id ) )
				$caps[] = 'manage_network'; //NetworkAdmin is only role that can edit this comment - no user can approve or trash it either!
			if ( ! is_super_admin( $comment->user_id ) && $comment->user_id != $user_id ) 
				$caps[] = 'moderate_comments'; //NetworkAdmin, Admins, Editors roles can edit others comments
			}
		}
		return $caps;
	}

    function ds_remove_comment_edit($actions, $comment) {
		$menu_perms = get_site_option( "menu_items" );
		if( !isset($menu_perms[ 'super_admin_mb' ] ) && is_super_admin()) 
		return;
		
		if( isset($menu_perms[ 'comment_edit' ]) ) {

        	$user_id = get_current_user_id();
        	if ($comment->user_id != $user_id) {
            	unset($actions['edit']); // edit link appears only on own comments
            	unset($actions['quickedit']); //quickedit may already be toggled for all roles
        	}
		}
        return $actions;
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
		'super_admin_mb'	=> __(  'Super Admin gets the following limited meta boxes, too?', $this->l10n_prefix ),
	//Extras
		'screen_options_link'	=> __(  'Screen Options Link', $this->l10n_prefix ),
		'contextual_help_link'	=> __(  'Contextual Help Link', $this->l10n_prefix ),
		'edit_slug_box'			=> __(  'Edit Slug Box', $this->l10n_prefix ),
		'media_buttons'			=> __(  'Add Media Button', $this->l10n_prefix ),
	//Meta Boxes
		'format_mb'			=> __(	'Format', $this->l10n_prefix ),
		'publish_mb'		=> __(	'Publish', $this->l10n_prefix ),
		'tags_mb'			=> __(	'Tags', $this->l10n_prefix ),
		'tax_cats_mb'		=> __(	'Taxonomy Categories', $this->l10n_prefix ),
		'cats_mb'			=> __(	'Categories', $this->l10n_prefix ),
		'att_mb'			=> __(	'Attributes', $this->l10n_prefix ),
		'feat_img_mb'		=> __(	'Featured Image', $this->l10n_prefix ),
		'excerpt_mb'		=> __(	'Excerpt', $this->l10n_prefix ),
		'track_mb'			=> __(	'Send Trackbacks', $this->l10n_prefix ),
		'custom_field_mb'	=> __(	'Custom Fields', $this->l10n_prefix ),
		'disc_mb'			=> __(	'Discussion', $this->l10n_prefix ),
		'slug_mb'			=> __(	'Slug', $this->l10n_prefix ),
		'author_mb'			=> __(	'Author', $this->l10n_prefix ),
		'revs_mb'			=> __(	'Revisions', $this->l10n_prefix ),
		'comments_mb'		=> __(	'Comments', $this->l10n_prefix ),
		'link_save_mb'		=> __(	'Link Save', $this->l10n_prefix ),
		'link_cat_mb'		=> __(	'Link Categories', $this->l10n_prefix ),
		'link_target_mb'	=> __(	'Link Target', $this->l10n_prefix ),
		'link_xfn_mb'		=> __(	'Link Relationship (XFN)', $this->l10n_prefix ),
		'link_adv_mb'		=> __(	'Link Advanced', $this->l10n_prefix ),
		
		'nav_menu_links'		=> __(	'Appearance Menu Links', $this->l10n_prefix ),
		'nav_menu_links_adv'		=> __(	'Appearance Menu Link Show Advanced Properties', $this->l10n_prefix ),
		'nav_menu_pages'		=> __(	'Appearance Menu Pages', $this->l10n_prefix ),
		'nav_menu_cats'		=> __(	'Appearance Menu Categories', $this->l10n_prefix ),

		'welcome_panel'		=> __(	'Welcome Panel', $this->l10n_prefix ),
		'dash_prim_mb'		=> __(	'Dashboard Primary', $this->l10n_prefix ),
		'dash_sec_mb'		=> __(	'Dashboard Secondary', $this->l10n_prefix ),
		'dash_links_mb'		=> __(	'Dashboard Incoming Links', $this->l10n_prefix ),
		'dash_comments_mb'	=> __(	'Dashboard Recent Comments', $this->l10n_prefix ),
		'dash_right_now_mb'	=> __(	'Dashboard Right Now', $this->l10n_prefix ),
		'dash_drafts_mb'	=> __(	'Dashboard Recent Drafts', $this->l10n_prefix ),
		'dash_quick_mb'		=> __(	'Dashboard QuickPress', $this->l10n_prefix ),
		'dash_net_right_now_mb'	=> __(	'Network Dashboard Right Now', $this->l10n_prefix ),
		'dash_net_prim_mb'		=> __(	'Network Dashboard Primary', $this->l10n_prefix ),
		'dash_net_sec_mb'		=> __(	'Network Dashboard Secondary', $this->l10n_prefix ),
		'dash_net_plugins'		=> __(	'Network Dashboard Plugins', $this->l10n_prefix ),
		'quick_edit_posts'		=> __(  'Quick Edit Posts', $this->l10n_prefix ),
		'quick_edit_pages'		=> __(  'Quick Edit Pages', $this->l10n_prefix ),
		'quick_edit_tag'		=> __(  'Quick Edit Any Tag', $this->l10n_prefix ),
		'quick_edit_post_tag'		=> __(  'Quick Edit Post Tag', $this->l10n_prefix ),
		'quick_edit_category'		=> __(  'Quick Edit Post Category', $this->l10n_prefix ),
		'quick_edit_link_category'	=> __(  'Quick Edit Link Category', $this->l10n_prefix ),
		'quick_edit_comments'	=> __(  'Quick Edit Comments', $this->l10n_prefix ),
		'comment_edit'		=> __(  'Restrict Comment Editing', $this->l10n_prefix ),

			);
?>
		<h3><?php _e( 'Meta Boxes', $this->l10n_prefix ); ?></h3>
 		<table id="menu" class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e( 'Enable Administration Meta Boxes', $this->l10n_prefix ); ?></th>
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
}
if (class_exists("ds_meta")) {
	$ds_meta = new ds_meta();	
}
?>