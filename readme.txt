=== Toggle Meta Boxes ===
Contributors: dsader
Donate link: http://dsader.snowotherway.org
Tags: menus, administration menus, admin menus, multisite, toggle meta boxes, edit form, edit, media buttons, quick edit,
Requires at least: 3.2.1
Tested up to: 3.2.1
Stable tag: Trunk

WP3.0 multisite "mu-plugin" to toggle administration meta boxes for the entire network of sites. Install in mu-plugins.

== Description ==
WP3 multisite mu-plugin. Go to Network Admin-->Settings to "Enable Administration Meta Boxes". Meta boxes (post, page, and link edit forms, and dashboard) are unchecked and disabled by default. Extra options to toggle the Quick Edit buttons, Media buttons, Screen Options and Help links.

I use the plugin to simplify the various edit forms available to the entire network of sites. I use this plugin in a school(k-12) WP3 Multisite installation to simplify the Post, Page, Link, and Comment editing forms for student and faculty users.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `ds_wp3_toggle_meta_boxes.php` to the `/wp-content/mu-plugins/` directory
2. Set multisite "Meta Boxes" option at Network Admin->Settings page

== Frequently Asked Questions ==

* Will this plugin also hide meta boxes added by plugins? No.
* Will this plugin disable media buttons? Yes, but is already a Super Admin->Option.
* Can I have different meta boxes for different roles of users on different blogs? No, this plugin toggles meta boxes for all users and all blogs regardless of Cap/Role (only SuperAdmin can override the limits of the plugin however).

== Screenshots ==

1. Meta Box Super Admin Options: Enable Administration Meta Boxes

== Changelog ==
= 3.0.3.1 =
* More testing up to: WP 3.2.1

= 3.0.3 = 
* Tested up to: WP 3.2.1

= 3.0.1 = 
* fixed typo allowing SuperAdmin override option with "extras"

= 3.0.0 = 
initial release

== Upgrade Notice ==
= 3.0.0 = 

initial release