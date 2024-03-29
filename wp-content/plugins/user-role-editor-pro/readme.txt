=== User Role Editor Pro ===
Contributors: Vladimir Garagulya (https://www.role-editor.com)
Tags: user, role, editor, security, access, permission, capability
Requires at least: 4.4
Tested up to: 6.1
Stable tag: 4.63.3
Requires PHP: 7.3
License URI: https://www.role-editor.com/end-user-license-agreement/

User Role Editor Pro WordPress plugin makes user roles and capabilities changing easy. Edit/add/delete WordPress user roles and capabilities.

== Description ==

User Role Editor Pro WordPress plugin allows you to change user roles and capabilities easy.
Just turn on check boxes of capabilities you wish to add to the selected role and click "Update" button to save your changes. That's done. 
Add new roles and customize its capabilities according to your needs, from scratch of as a copy of other existing role. 
Unnecessary self-made role can be deleted if there are no users whom such role is assigned.
Role assigned every new created user by default may be changed too.
Capabilities could be assigned on per user basis. Multiple roles could be assigned to user simultaneously.
You can add new capabilities and remove unnecessary capabilities which could be left from uninstalled plugins.
Multi-site support is provided.

== Installation ==

Installation procedure:

1. Deactivate plugin if you have the previous version installed.
2. Extract "user-role-editor-pro.zip" archive content to the "/wp-content/plugins/user-role-editor-pro" directory.
3. Activate "User Role Editor Pro" plugin via 'Plugins' menu in WordPress admin menu. 
4. Go to the "Settings"-"User Role Editor" and adjust plugin options according to your needs. For WordPress multisite URE options page is located under Network Admin Settings menu.
5. Go to the "Users"-"User Role Editor" menu item and change WordPress roles and capabilities according to your needs.

In case you have a free version of User Role Editor installed: 
Pro version includes its own copy of a free version (or the core of a User Role Editor). So you should deactivate free version and can remove it before installing of a Pro version. 
The only thing that you should remember is that both versions (free and Pro) use the same place to store their settings data. 
So if you delete free version via WordPress Plugins Delete link, plugin will delete automatically its settings data. Changes made to the roles will stay unchanged.
You will have to configure lost part of the settings at the User Role Editor Pro Settings page again after that.
Right decision in this case is to delete free version folder (user-role-editor) after deactivation via FTP, not via WordPress.

== Changelog ==

= [4.63.3] 02.11.2022 =
* Core version: 4.63.1
* Fix: Navigation menu admin access add-on: Warning: Attempt to read property “slug” on int in /wp-content/plugins/user-role-editor-pro/pro/includes/classes/nav-menus-admin-access.php on line 73

= [4.63.2] 29.09.2022 =
* Core version: 4.63.1
* Fix: Admin menu access add-on: Error message: Uncaught TypeError: in_array(): Argument #2 ($haystack) must be of type array, null given in /wp-content/plugins/user-role-editor-pro/pro/includes/classes/admin-menu-access.php:522

= [4.63.2] 29.09.2022 =
* Core version: 4.63.1
* Fix: Edit access restrictions add-on: Prohibit by selected posts ID list criteria worked incorrectly - all posts were prohibited instead of selected only.
* Update: Edit access restrictions add-on: It skips Elementor's internal custom post types to reduce general time execution. 

= [4.63.1] 21.09.2022 =
* Core version: 4.63.1
* Marked as compatible with WordPress version 6.0.2
* Fix: Edit access restrictions add-on: It was possible to open a post from a prohibited post type for editing via direct link by post ID, like /wp-admin/post.php?post=NN&action=edit
* Update: Admin menu access add-on: URL Parameters White List: convert parameter name to lower case before processing.
* Update: German translation was updated.
* Core version was updated to 4.63.1
* Fix: PHP Warning: Attempt to read property “ID” on null in ./includes/classes/user-role-editor.php on line 369
* Fix: Deprecated: Automatic conversion of false to array is deprecated in ./includes/classes/base-lib.php on line 212


Full list of changes is available in changelog.txt file.
