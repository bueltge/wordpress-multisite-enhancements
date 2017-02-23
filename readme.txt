=== Multisite Enhancements ===
Contributors: Bueltge, inpsyde
Tags: multisite, administration, admin bar, network,
Requires at least: 3.0.0
Tested up to: 4.7.2
Stable tag: 1.3.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Enhance Multisite for Network Admins with different topics

== Description ==
When you work quite a bit with WordPress Multisites, sometimes you need more information or menu items. This plugin enhances the network area for super admins with useful functions.

* Adds Blog and User ID in network view [more](http://wpengineer.com/2188/view-blog-id-in-wordpress-multisite/)
* Enables an 'Add New' link under the Plugins menu for Network admins
* Adds several useful items to the multisite 'Network Admin' admin bar
* On the network plugins page, shows which site has this plugin active
* On the network theme page, shows which blog has the theme active and is a Child theme
* Change Admin footer text for Administrators to view currently used RAM, SQL, RAM version fast
* Adds Favicon from theme folder to the admin area to easily identify the blog, use the `favicon.ico` file in the theme folder of the active theme in each blog
* Adds Favicon to each blog on the Admin Bar Item 'My Sites'. If you a like a custom path for each favicon, please see the [documentation](https://github.com/bueltge/WordPress-Multisite-Enhancements/wiki/Filter-Hook-for-Favicon-File-Path) for this feature.
* Removes also the 'W' logo and his sub-links in admin bar
* Adds the status to each site in the admin bar to identify fastly if the site has a `noindex` status and to see the external url.
* Add functions to be used in your install
   * The function `get_blog_list()` is currently deprecated in the WP Core, but currently usable. The plugin checks this and gets an alternative in [`inc/autoload/core.php`](./inc/autoload/core.php)
   * If you will develop with the alternative to this function from my source, then use the method `get_blog_list()` in class `Multisite_Core`. My source also use caching with the Transient API. More about the function in  [`inc/autoload/class-core.php`](./inc/autoload/class-core.php).
   * If you use WordPress version 3.7 and higher, then check the function `wp_get_sites()`, the new alternative function inside the core to get all sides inside the network. The function accepts a array with arguments, see the [description](http://wpseek.com/wp_get_sites/).
   * But if you use WordPress 4.6 and higher then that new alternative ;) - `get_sites()` - is the current function to get all sites in the network. The helper method of this plugin `Multisite_Core::get_blog_list()` or the function `get_blog_list()` have all checks included.

* Filter theme list to find your target fast. Works on single theme page and also network theme page.

= Crafted by Inpsyde =
The team at [Inpsyde](http://inpsyde.com/) is engineering the web and WordPress since 2006.

= Donation? =
You want to donate - we prefer a [positive review](https://wordpress.org/support/view/plugin-reviews/multisite-enhancements?rate=5#postform), nothing more.

== Installation ==

= Requirements =
* WordPress Multisite 3.0+
* PHP 5.3*, newer PHP versions will work faster.

= Installation =
* Use the installer via back-end of your install or ...

1. Unpack the download-package
2. Upload the files to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Network/Plugins' menu in WordPress and hit 'Network Activate'
4. No options, no settings, it works

== Screenshots ==
1. Add Blog-ID on Sites
2. Add User-ID on Users
3. Add New link to install new plugin on each blog
4. Manage Comments with Counter on Admin Bar
5. On which blog is the plugin active
6. On which blog is the theme active
7. New Admin footer text
8. Favicon on Admin bar
9. Filter Themes

== Other Notes ==

**Crafted by [Inpsyde](http://inpsyde.com) · The team is engineering the Web since 2006.**

= Hints, knowledge =
See also for helpful hints on the [wiki page](https://github.com/bueltge/wordpress-multisite-enhancements/wiki).
Especially the follow topics are interest:

* [Filter Hook for Favicon File Path - Define your custom Favicon path](https://github.com/bueltge/WordPress-Multisite-Enhancements/wiki/Filter-Hook-for-Favicon-File-Path)
* [Large Network Problem](https://github.com/bueltge/wordpress-multisite-enhancements/wiki/Large-Network-Problem)

= Bugs, technical hints or contribute =
Please give me feedback, contribute and file technical bugs on this
[GitHub Repo](https://github.com/bueltge/WordPress-Multisite-Enhancements/issues), use Issues.

= License =
Good news, this plugin is free for everyone! Since it's released under the GPL,
you can use it free of charge on your personal or commercial blog. But if you enjoy this plugin,
you can thank me and leave a
[small donation](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6069955 "Paypal Donate link")
for the time I've spent writing and supporting this plugin.
And I really don't want to know how many hours of my life this plugin has already eaten ;)

= Contact & Feedback =
The plugin is designed and developed by me [Frank Bültge](http://bueltge.de), [G+ Page](https://plus.google.com/+FrankBültge/about?rel=author)

Please let me know if you like the plugin or you hate it or whatever ...
Please fork it, add an issue for ideas and bugs on the [Github Repository](https://github.com/bueltge/WordPress-Multisite-Enhancements).

= Disclaimer =
I'm German and my English might be gruesome here and there.
So please be patient with me and let me know of typos or grammatical parts. Thanks

== Changelog ==
= 1.3.8 (2017-02-23) =
* Fixed duplicated view on parent theme usage.
* Adds status text to a site, if is a archived site on plugin/theme list.

= 1.3.7 (2016-10-24) =
* Fix the usage of plugins for each site in the network on the plugin network view.
* Add helpers to remove transient caching for development, debugging, if `WP_DEBUG` is true.
* Fix markup error of missing closing tag in footer text.
* Fix markup error on comment admin bar menu item in each site item of the item 'My Sites'.
* Add possibility to translate the plugin.

= 1.3.6 (2016-10-07) =
* Switch to new core function `get_sites`
* Remove Plugin Search, now inside the core, since WP 3.6.0
* Add Theme filter search to single and network theme page to find fast and simple the result.

= 1.3.5 (2016-05-30) =
* Performance: Change the function to get all sites of a user to set favicon. [#25](https://github.com/bueltge/wordpress-multisite-enhancements/issues/25)

= 1.3.4 (2016-05-19) =
* Fix value type for nodes from admin bar.
* Enhance the Multisite requirements check.

= 1.3.3 (2016-01-15) =
* Fix Blog Id enhancement, change filter type.
* Change different code topics for better performance and stability.

= 1.3.2 (2015-12-17) =
* Prevent PHP Warning. Props noelboss

= 1.3.1 (2015-12-03) =
* Enhance the external domain check for more exactly check, that's also work on root domain of multisite. Props Matt [Thread](https://wordpress.org/support/topic/main-blog-being-tagged-as-external-domain)

= 1.3.0 (2015-11-28) =
* Add new functionality to filter plugin list live.
* Improve status label filter `multisite_enhancements_status_label`, now with the parameters `$blogname` and `$blog`.

= 1.2.1 (2015-09-24) =
* Bugfix: Correction for the site icon topic. The functions "has_site_icon" and "get_site_icon_url" aren't compatible with multisites. Icon only displayed when on that blog, in network or other blog the WP logo showed.
* Enhancement: Check for active usage of admin bar before add favicon to Admin Bar.

= 1.2.0 (2015-09-03) =
* Add support for Favicon feature `wp_site_icon` since WP 4.3, probs [JoryHogeveen](https://github.com/JoryHogeveen)
* Add status label to each site in the admin bar, probs JoryHogeveen
* Codex changes
* Add hook `multisite_enhancements_autoload` to unset files, there not necessary on autoload, see also the [Wiki](https://github.com/bueltge/wordpress-multisite-enhancements/wiki) for more information

= 1.1.0 (2015-02-26) =
* Some modifications to plugin and theme admin columns for better performance and usage on Multisites with more as 100 blogs, plugins, themes [Issue #16](https://github.com/bueltge/wordpress-multisite-enhancements/pull/16)
* Code inspections
* Enhance the value to get sites inside the network form WordPress default 100 to 9999
* Add hook `multisite_enhancements_sites_limit` to change this value, see [wiki page](https://github.com/bueltge/wordpress-multisite-enhancements/wiki/Large-Network-Problem)

= 1.0.7 (09/23/2014) =
* Code maintenance
* Add parameters for custom favicon, see [documentation](https://github.com/bueltge/WordPress-Multisite-Enhancements/wiki/Filter-Hook-for-Favicon-File-Path)

= 1.0.6 (09/13/2014) =
* Add check for child theme, that you fast see, if is a child and what is the parent inside the network view of themes

= 1.0.5 (05/15/2014) =
* Fix list of active plugin in plugin network view
* Add hook for custom favicon path, see [documentation](https://github.com/bueltge/WordPress-Multisite-Enhancements/wiki/Filter-Hook-for-Favicon-File-Path)

= 1.0.4 (04/27/2014) =
* Add break, if no plugin is active, fixed [Error in "Active In" column](http://wordpress.org/support/topic/error-in-active-in-column)

= 1.0.3 (03/09/2014) =
* Remove Super Admin check, that works the enhancements also on other roles.
* Add indicator for "Network Only" Plugins.
* Add Favicon Indicator also in Admin Bar on Front end side.

= 1.0.2 (02/03/2014) =
 * Add Favicon in Admin Bar also in Front end
 * Enhance style for favicon size
 * Grammar fix in tags, readme
 * Small changes for columns and 3.8 design

= 1.0.1 (01/03/2014) =
 * Add CSS rule for new WP 3.8 back end design (mp6 plugin)
 * Add more whitespace on the comment count in admin bar
 * Enhance the filter to list active plugins [#1](https://github.com/bueltge/WordPress-Multisite-Enhancements/issues/1)

= 1.0.0 (10/31/2013) =
 * First release on wordpress.org after different installs with different users

For more information about changes see the commits on the [working repository](https://github.com/bueltge/WordPress-Multisite-Enhancements/commits/master).
