# Change Log
All notable changes to this project will be documented in this file. This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.6.1...HEAD), only in master branch
* Fixing fatal error triggered by a missing slash, [#70](https://github.com/bueltge/wordpress-multisite-enhancements/pull/70). Probs @brasofilo
* Enhance the footer information to make more clear about the memory values. [#71](https://github.com/bueltge/wordpress-multisite-enhancements/issues/71)
* Fix php note for favicon functionality. [#65](https://github.com/bueltge/wordpress-multisite-enhancements/issues/65)
* Change dashicons from lock/unlock to yes/no to optimize the visual difference of the icon to spot http usage easier. Probs @Zodiac1978 [#76](https://github.com/bueltge/wordpress-multisite-enhancements/pull/70)
* Added functionality to see when a user last time logs in
* Update minimum PHP Version to 7.2
* Added Namespace, Autoloading, and many more PHP improvements and cleanups

## [1.6.1](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.6.0...1.6.1) - 2021-01-20
* Fix path for css/js files.

## [1.6.0](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.5.4...1.6.0) - 2021-01-17
* Add settings page, Big probs to @hvianna

## [1.5.4](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.5.3...1.5.4) - 2020-11-16
* Quickfix for custom favicon.

## [1.5.3](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.5.2...1.5.3) - 2020-11-09
* Show site path for sites with blank titles in the themes and plugins lists.
* Show status of deleted sites.
* Apply styles for archived and deleted sites in the lists.
* Replace obsolete HTML 'nobr' element.
* Improve site label/icon positioning, #66, Probs @JoryHogeveen
* Small fix for php notice on missing url paramters from Favicon feature.

## [1.5.2](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.5.1...1.5.2) - 2019-11-14
* Fix style problem on list of all sites for admin bar.
* Change enqueue of styles and script to default variants.
* Remove filter script on plugins, because is a part of the core.

## [1.5.1](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.5.0...1.5.1) - 2019-02-25
* Remove feature 'Networkmenu is now scrollable' - to much problems.
* More clearance for the message about inactive cache.

## [1.5.0](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.4.3...1.5.0) - 2019-02-24
* Networkmenu is now scrollable.
* More clearance for the message about inactive cache.

## [1.4.3](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.4.2...1.4.3) - 2018-05-17
* Fix undefined index Network on plugin list, #46
* Fix small php notes.
* Fix the possibility to translate the footer message about RAM, SQL queries.
* Add fix to leave message for the parent theme in a single line.
* Add Ui change to list more as 4 themes in Theme usage overview. Probs @n-goncalves #44

## [1.4.2](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.4.1...1.4.2) - 2018-02-22
* Change syntax for usage of plugins, themes; probs @cfoellmann
* Count the usage in sites for plugins, themes; probs @cfoellmann
* Change of the autoloader, the removel of a function is now more solid. See [the wiki page](https://github.com/bueltge/wordpress-multisite-enhancements/wiki/Remove-features); probs @cfoellmann
* Added option to display or hide the list of sites if too big #44; probs @n-goncalves

## [1.4.1](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.4.0...1.4.1) - 2017-08-10
* Added a change for initialization of the class to run also on php 5.3 installs.

## [1.4.0](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.3.8...1.4.0) - 2017-07-23
* Added ssl identifier to each site in network site view page.

## [1.3.8](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.3.7...1.3.8) - 2017-02-23
* Fixed duplicated view on parent theme usage.
* Adds status text to a site, if is a archived site on plugin/theme list.

## [1.3.7](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.3.6...1.3.7) - 2016-10-24
* Fix the usage of plugins for each site in the network on the plugin network view. #32
* Add helpers to remove transient caching for development, debugging, if `WP_DEBUG` is true.
* Fix markup error of missing closing tag in footer text. #31
* Fix markup error on comment admin bar menu item in each site item of the item 'My Sites'. #31
* Add possibility to translate the plugin.

## [1.3.6](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.3.5...1.3.6) - 2016-10-07
* Switch to new core function `get_sites`
* Remove Plugin Search, now inside the core, since WP 3.6.0
* Add Theme filter search to single and network theme page to find fast and simple the result.

## [1.3.5](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.3.4...1.3.5) - 2016-05-30
* Performance: Change the function to get all sites of a user to set favicon. [#25](https://github.com/bueltge/wordpress-multisite-enhancements/issues/25)

## [1.3.4](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.3.3...1.3.4) - 2016-05-19
* Fix value type for nodes from admin bar.
* Enhance the Multisite requirements check.

## [1.3.3](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.3.2...1.3.3) - 2016-01-15
* Fix Blog Id enhancement, change filter type.
* Change different code topics for better performance and stability.

## [1.3.2](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.3.1...1.3.2) - 2015-12-17
* Prevent PHP Warning. Props noelboss

## [1.3.1](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.3.0...1.3.1) - 2015-12-03
* Enhance the external domain check for more exactly check, that's also work on root domain of multisite. Props Matt [Thread](https://wordpress.org/support/topic/main-blog-being-tagged-as-external-domain)

## [1.3.0](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.2.1...1.3.0) - 2015-11-28
* Add new functionality to filter plugin list live.
* Improve status label filter `multisite_enhancements_status_label`, now with the parameters `$blogname` and `$blog`.

## [1.2.1](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.2.0...1.2.1) - 2015-09-24
* Bugfix: Correction for the site icon topic. The functions "has_site_icon" and "get_site_icon_url" aren't compatible with multisites. Icon only displayed when on that blog, in network or other blog the WP logo showed.
* Enhancement: Check for active usage of admin bar before add favicon to Admin Bar.

## [1.2.0](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.1.0...1.2.0) - 2015-09-03
* Add support for Favicon feature `wp_site_icon` since WP 4.3, probs [JoryHogeveen](https://github.com/JoryHogeveen)
* Add status label to each site in the admin bar, probs JoryHogeveen
* Codex changes
* Add hook `multisite_enhancements_autoload` to unset files, there not necessary on autoload, see also the [Wiki](https://github.com/bueltge/wordpress-multisite-enhancements/wiki) for more information

## [1.1.0](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.0.7...1.1.0) - 2015-02-26
* Some modifications to plugin and theme admin columns for better performance and usage on Multisites with more as 100 blogs, plugins, themes [Issue #16](https://github.com/bueltge/wordpress-multisite-enhancements/pull/16)
* Code inspections
* Enhance the value to get sites inside the network form WordPress default 100 to 9999
* Add hook `multisite_enhancements_sites_limit` to change this value, see [wiki page](https://github.com/bueltge/wordpress-multisite-enhancements/wiki/Large-Network-Problem)

## [1.0.7](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.0.6...1.0.7) - 09/23/2014
* Code maintenance
* Add parameters for custom favicon, see [documentation](https://github.com/bueltge/WordPress-Multisite-Enhancements/wiki/Filter-Hook-for-Favicon-File-Path)

## [1.0.6](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.0.5...1.0.6) - 09/13/2014
* Add check for child theme, that you fast see, if is a child and what is the parent inside the network view of themes

## [1.0.5](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.0.4...1.0.5) - 05/15/2014
* Fix list of active plugin in plugin network view
* Add hook for custom favicon path, see [documentation](https://github.com/bueltge/WordPress-Multisite-Enhancements/wiki/Filter-Hook-for-Favicon-File-Path)

## [1.0.4](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.0.3...1.0.4) - 04/27/2014
* Add break, if no plugin is active, fixed [Error in "Active In" column](http://wordpress.org/support/topic/error-in-active-in-column)

## [1.0.3](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.0.2...1.0.3) - 03/09/2014
* Remove Super Admin check, that works the enhancements also on other roles.
* Add indicator for "Network Only" Plugins.
* Add Favicon Indicator also in Admin Bar on Front end side.

## [1.0.2](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.0.1...1.0.2) - 02/03/2014
 * Add Favicon in Admin Bar also in Front end
 * Enhance style for favicon size
 * Grammar fix in tags, readme
 * Small changes for columns and 3.8 design

## [1.0.1](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.0.0...1.0.1) - 01/03/2014
 * Add CSS rule for new WP 3.8 back end design](https://github.com/bueltge/wordpress-multisite-enhancements/compare/1.3.4...1.3.5) - mp6 plugin)
 * Add more whitespace on the comment count in admin bar
 * Enhance the filter to list active plugins [#1](https://github.com/bueltge/WordPress-Multisite-Enhancements/issues/1)


## 1.0.0 - 2013-10-31
* First release on wordpress.org after different installs with different users.
