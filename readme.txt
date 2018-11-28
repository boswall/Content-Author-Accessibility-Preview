=== Content Author Accessibility Preview ===
Contributors: boswall,grahamarmfield
Requires at least: 4.6
Tested up to: 4.9.8
Requires PHP: 5.6
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Flag up potential accessibility issues when your content authors preview the post or page that they have just added or amended

== Description ==
Flag up potential accessibility issues when your content authors preview the post or page that they have just added or amended.

Site visitors who are not logged in will not see the potential issues.

Currently contains checks for:
* Images with empty alt attributes
* Links that open new windows
* Links that have a title attribute
* images that have no alt attribute
* images that have the title attribute
* svg files that don\'t have role=\"img\"
* inline svgs that don\'t have role=\"img\"
* empty headings
* empty links
* empty buttons
* empty headings
* empty table header cells
* empty table data cells

Flags each element found with an outline. Where possible explains what the issue is on the page.

== Installation ==
1. Upload \"test-plugin.php\" to the \"/wp-content/plugins/\" directory.
1. Activate the plugin through the \"Plugins\" menu in WordPress.
