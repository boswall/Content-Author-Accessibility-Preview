=== Content Author Accessibility Preview ===
Contributors: boswall,grahamarmfield
Donate link: https://github.com/boswall/Content-Author-Accessibility-Preview
Tags: accessibility, accessible, wcag, a11y, section508, alt text, labels, aria, preview
Requires at least: 4.6
Tested up to: 5.8
Requires PHP: 5.6
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Flag up potential accessibility issues when your content authors preview the post or page that they have just added or amended

== Description ==
Flag up potential accessibility issues when your content authors preview the post or page that they have just added or amended.

Currently contains checks for:

* Images with empty alt attributes
* Links that open new windows
* Links that have a title attribute
* images that have no alt attribute
* images that have the title attribute
* svg files that don`t have role="img"
* inline svgs that don`t have role="img"
* empty headings
* empty links
* empty buttons
* empty headings
* empty table header cells
* empty table data cells

Flags each element found with an outline. Where possible explains what the issue is on the page.

Can be configured to only be visible to certain user roles.

Site visitors who are not logged in will not see the potential issues.

Based on the work from [Graham Armfield's CSS tool for highlighting accessibility issues](https://github.com/grahamarmfield/wp-preview-csshacks)

== Installation ==

1. Download the plugin's zip file, extract the contents, and upload them to your wp-content/plugins folder.
2. Login to your WordPress dashboard, click "Plugins", and activate "Content Author Accessibility Preview".
3. Customise your settings by going to: Settings > Content Author Accessibility Preview.

== Frequently Asked Questions ==

= Will this plugin make my site "fully accessible"? =

Absolutely not. There is no quick fix for accessibility, this plugin is to be used to highlight any potential accessibility issues to your content creators.

= Will this be visible to the public? =

No. By default, issues will only be highlighted to anyone who is logged in. You can also stop any particular user role from seeing the highlighting in the settings page. ( for example: if you have WooCommerce, you might want to deselect "Customers" from seeing the highlights ).

== Screenshots ==

1. Admin settings

== Changelog ==

= Future =

* Visible while editing in Gutenburg
* Fix false positives in the admin bar
* ~~Limit to the content area of the page~~
* Show a legend
* Animated flashing boarders - to really make it obvious
* ~~Options to hide certain tests~~
[Suggest a change or feature!](https://github.com/boswall/Content-Author-Accessibility-Preview/issues)



= 1.2.0 =

* Added a Test class
* Options to stop certain tests from running

= 1.1.1 =

* Bug fix
* Improved colour contrast on labels

= 1.1.0 =

* Added a container option to limit the tests to a specific area
* All highlights are found using JS (not pure CSS anymore)
* Added labels to highlighted elements

= 1.0.0 =

* Initial release!

== Upgrade Notice ==

Initial release!
