# WordPress Multisite Enhancements
Enhance Multisite for Network Admins with different topics

## Description
When you work quite a bit with WordPress Multisites, sometimes you need more information or menu items. This plugin enhance the network area for super admins with useful functions.

* Add Blog and User ID in network view [more](http://wpengineer.com/2188/view-blog-id-in-wordpress-multisite/)
* Enables an 'Add New' link under the Plugins menu for Network admins
* Adds several useful items to the multisite 'Network Admin' admin bar
* On the network plugins page, show which site have this plugin active
* On the network theme page, show which blog have the theme active and is it a Child theme
* Change Admin footer text for Administrators to view fast currently used RAM, SQL, RAM Version
* Add Favicon from theme folder to the admin area to easier identify the blog, use the `favicon.ico` file in the theme folder of the active theme in each blog
* Add Favicon to each blog on the Admin Bar Item 'My Sites'. If you a like a custom path for each favicon, please see the [documentation](https://github.com/bueltge/WordPress-Multisite-Enhancements/wiki/Filter-Hook-for-Favicon-File-Path) for this feature.
* Remove also the 'W' logo and his sublinks in admin bar
* Add functions to be used in your install
	 * The function `get_blog_list()` is currently deprecated in the WP Core, but currently usable. The plugin check this and get a alternative in [`inc/autoload/core.php`](https://github.com/bueltge/WordPress-Multisite-Enhancements/blob/master/inc/autoload/core.php)
	 * If you will develop with the alternative to this function from my source, then use the method `get_blog_list()` in class `Multisite_Core`. She use also caching with the Transient API. See more about the function on the function in [`inc/autoload/class-core.php`](https://github.com/bueltge/WordPress-Multisite-Enhancements/blob/master/inc/autoload/class-core.php).
	 * If you use WordPress version 3.7 and higher, then check the function `wp_get_sites()`, the new alternative function inside the core to get all sides inside the network. The function accept a array with arguments, see the [description](http://wpseek.com/wp_get_sites/).


### Screenshots
 1. [Blog ID on Sites](https://github.com/bueltge/WordPress-Multisite-Enhancements/blob/master/assets/screenshot-1.png)
 
 ![Blog ID on Sites](https://raw.github.com/bueltge/WordPress-Multisite-Enhancements/master/assets/screenshot-1.png)
 2. [User ID on Users](https://github.com/bueltge/WordPress-Multisite-Enhancements/blob/master/assets/screenshot-2.png)
 
 ![User ID on Users](https://raw.github.com/bueltge/WordPress-Multisite-Enhancements/master/assets/screenshot-2.png)
 3. [Add New link to install new plugin on each blog](https://github.com/bueltge/WordPress-Multisite-Enhancements/blob/master/assets/screenshot-3.png)
 
 ![Add New link to install new plugin on each blog](https://raw.github.com/bueltge/WordPress-Multisite-Enhancements/master/assets/screenshot-3.png)
 4. [Manage Comments with Counter on Admin Bar](https://github.com/bueltge/WordPress-Multisite-Enhancements/blob/master/assets/screenshot-4.png)
 
 ![Manage Comments with Counter on Admin Bar](https://raw.github.com/bueltge/WordPress-Multisite-Enhancements/master/assets/screenshot-4.png)
 5. [On which blog is the plugin active](https://github.com/bueltge/WordPress-Multisite-Enhancements/blob/master/assets/screenshot-5.png)
 
 ![On which blog is the plugin active](https://raw.github.com/bueltge/WordPress-Multisite-Enhancements/master/assets/screenshot-5.png)
 6. [On which blog is the theme active](https://github.com/bueltge/WordPress-Multisite-Enhancements/blob/master/assets/screenshot-6.png)
 
 ![On which blog is the theme active](https://raw.github.com/bueltge/WordPress-Multisite-Enhancements/master/assets/screenshot-6.png)
 7. [New Admin footer text](https://github.com/bueltge/WordPress-Multisite-Enhancements/blob/master/assets/screenshot-7.png)
 
 ![New Admin footer text](https://raw.github.com/bueltge/WordPress-Multisite-Enhancements/master/assets/screenshot-7.png)
 8. [Favicon on Admin bar](https://github.com/bueltge/WordPress-Multisite-Enhancements/blob/master/assets/screenshot-8.png)
 
 ![Favicon on Admin bar](https://raw.github.com/bueltge/WordPress-Multisite-Enhancements/master/assets/screenshot-8.png)

## Other Notes

### Made by [Inpsyde](http://inpsyde.com) &middot; We love WordPress
Have a look at the premium plugins in our [market](http://marketpress.com).

### Bugs, technical hints or contribute
Please give me feedback, contribute and file technical bugs on this 
[GitHub Repo](https://github.com/bueltge/WordPress-Multisite-Enhancements/issues), use Issues.

### License
Good news, this plugin is free for everyone! Since it's released under the GPL, 
you can use it free of charge on your personal or commercial blog. But if you enjoy this plugin, 
you can thank me and leave a 
[small donation](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6069955 "Paypal Donate link") 
for the time I've spent writing and supporting this plugin. 
And I really don't want to know how many hours of my life this plugin has already eaten ;)

### Contact & Feedback
The plugin is designed and developed by me [Frank Bültge](http://bueltge.de), [G+ Page](https://plus.google.com/111291152590065605567/about?rel=author)

Please let me know if you like the plugin or you hate it or whatever ... 
Please fork it, add an issue for ideas and bugs.

### Disclaimer
I'm German and my English might be gruesome here and there. 
So please be patient with me and let me know of typos or grammatical parts. Thanks

***

