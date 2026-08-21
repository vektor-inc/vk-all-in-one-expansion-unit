=== VK All in One Expansion Unit ===
Contributors: vektor-inc,kurudrive,jim912,hideokamoto,nc30,SaoriMiyazaki,catherine8007,naoki0h,rickaddison7634,una9,kaorock72,kurishimak,chiakikouno,daikiweb23,doshimaf,shimotomoki,mtdkei,mt8biz,thisismyurl
Donate link:
Tags: Google Analytics, Related Posts, sitemap, Facebook Page Plugin, OG tags
Requires at least: 6.5
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 9.122.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plug-in is an integrated plug-in with a variety of features that make it powerful your web site.

== Description ==

This plug-in is an integrated plug-in with a variety of features that make it powerful your web site.

Many features can be stopped individually.

[ Powerful　Widgets ]

* Recent Posts - display the link text and the date of the latest article title.
* Page content to widget - display the contents of the page to the widgets.
* Profile - display the profile entered in the widget.
* FB Page Plugin - display the Facebook Page Plugin.
* 3PR area - display the 3PR area.
* PR Blocks - display the PR Blocks.
* Categories/tags list - Displays a categories, tags or format list.
* Archive list - Displays a list of archives. You can choose the post type and also to display archives by month or by year.
* Facebook Page Plugin widget
* Image Banner widget
* Text Button widget
* Contact Button widget

[ Gutenberg Blocks ]

* HTML SiteMap
* Child Page List
* Page list from ancestor
* Share Button
* Contact Section

[ Social media ]

* Print Social Bookmarks
* Print OG Tags
* Print X Card Tags

[ Others ]

* Print Google Analytics tag
* Print meta description tag
* Rewrite the title tag
* Insert Related Posts
* Insert Call to action
* Insert Child page List to page
* Insert Page list from ancestor
* Insert Auto HTML Site Map
* Automatic Eye Catch insert
* Custom post type and custom taxonomy manager

and more.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==



== Screenshots ==

1. Feature can be stopped individually.
2. This is an example of SNS cooperation setting screen.

== Credits ==

This plugin's share button icons (Facebook, X, Bluesky, Threads, Hatena Bookmark, LINE, Copy) are inline SVG traced from the following third-party icon sets:

* Facebook, X, Bluesky, Threads, Hatena Bookmark and LINE icons: Simple Icons ( https://simpleicons.org/ ), licensed under CC0 1.0 Universal.
* Copy icon: Heroicons ( https://heroicons.com/ ), square-2-stack (solid), licensed under the MIT License.

Full license texts are included in LICENSE-THIRD-PARTY.txt, bundled with this plugin.

== Changelog ==

= 9.122.1 =
[ Security Fix ][ Article Structured Data ] Added a capability check and sanitization when saving the author structured data settings (author_type / author_name / author_url / author_sameAs) on the user profile screen, as defense-in-depth hardening.
[ Security Fix ][ Custom CSS ] Added a permission check on save and hardened input handling on the classic edit screen as defense in depth.
[ Security Fix ] Added direct file access protection to PHP files.

= 9.122.0 =
[ New Feature ][ Share Button ] Added an option to always display the share button block regardless of the "Exclude Post Types" setting.
[ Spec Change ][ SNS Share Button ] Unified the Facebook, X, Bluesky, Hatena Bookmark and LINE icons to inline SVG to match Threads and Copy. The markup no longer uses the ".vk_icon_w_r_sns_*" web font classes; the classes and the font itself are kept for backward compatibility.
[ Bug Fix ][ Block ] Fixed the Share Button, HTML Sitemap and other blocks showing "This block has encountered an error and cannot be previewed" in the editor when the post contains a cross-origin iframe embed.
[ Bug Fix ][ Share Button ] Fixed the share button block always showing in the editor even when excluded from the front end, hiding the mismatch while editing. The editor now explains why it will not display.
[ Bug Fix ][ SNS Share Button ] Fixed the Threads and Copy icons not being displayed on themes that do not load Font Awesome (e.g. Twenty Twenty-Five), by replacing them with inline SVG.
[ Bug Fix ][ CTA ] Fixed a PHP deprecated warning shown on the first save of the CTA settings (which looked like the save had failed) and CTA default values not being applied.
[ Bug Fix ][ SNS Share Button ] Fixed the Threads share button's label, admin checkbox and Customizer control showing "Threads" with a capital T instead of the official lowercase "threads".
[ Security Fix ][ CTA ] Added a permission check on save and strengthened output escaping on the classic edit screen as defense-in-depth hardening.

= 9.121.1 =
[ Bug Fix ][ HTML Sitemap ] Fixed a fatal error on the HTML Sitemap settings screen when used together with plugins that bundle an older copy of the shared template tag package (e.g. VK Post Author Display).

= 9.121.0 =
[ New Feature ][ HTML Sitemap ] Added an option to exclude specific taxonomies from the HTML sitemap, in addition to the existing post type exclusion.

= 9.120.0 =
[ New Feature ] Added an alternative text setting for the inquiry banner image. When it is left blank, the alternative text set on the media library is filled in automatically on save.
[ Spec Change ] Fixed the inquiry banner image being read aloud as "contact_txt" by screen readers; it now uses the alternative text you set, or the contact button text as the link name when it is blank.
[ Design Bug Fix ][ Widget / CTA ] Fixed the Related Posts, 3PR Area, PR Blocks and Call To Action titles getting unintended backgrounds, borders and spacing on themes such as Lightning and X-T9. The `<h1>` to `<h2>` change made in 9.119.0 has been reverted, so a page can again contain more than one `<h1>`.

= 9.119.0 =
[ Spec Change ] Update vektor-inc/font-awesome-versions from 0.7.4 to 0.7.5
[ Spec Change ] Changed the heading tag of the Related Posts, 3PR Area and PR Blocks widgets, and the Call To Action block / shortcode, from `<h1>` to `<h2>` to avoid multiple `<h1>` elements on a page ( the CSS class names are unchanged, so class-based styling is unaffected ).
[ Spec Change ] Hid decorative icons from screen readers so they are no longer read aloud together with the visible label.
[ Spec Change ] Added accessible names to the Profile widget's icon-only social media links.
[ Bug Fix ][ Widget ] Fixed a PHP warning that occurred on the admin widget form of the Recent Posts widget when its title was empty.
[ Bug Fix ] Fixed PHP warnings, notices and deprecations ( and a potential fatal error ) logged in debug mode on recent PHP versions, while keeping PHP 7.4 support.
[ Bug Fix ][ CTA ] Fixed the CTA image not being saved and the button text / message losing their allowed HTML when saved from the classic edit screen.
[ Bug Fix ][ Custom Post Type Manager ] Fixed custom post types saved by older versions losing "title" support when re-registered.
[ Bug Fix ][ Custom Post Type Manager ] Fixed a fatal TypeError on PHP 8 when re-registering a custom post type whose legacy support data was not stored as an array.
[ Bug Fix ][ Custom CSS ] Fixed the per-post Custom CSS leaking into the whole admin screen and breaking admin elements such as headings; it is now applied only to the block editor content and the front end.
[ Bug Fix ][ Smooth Scroll ] Fixed the "CSS only" option label showing outdated information that it does not work on Safari; modern Safari versions support it.
[ Bug Fix ][ Custom CSS ] Fixed the success and error notices shown after saving the CSS customizer always appearing in English because they referenced an old text domain.
[ Security Fix ][ CTA ] Added allowlist validation on save and escaping on output for the CTA image position custom field, which allowed stored XSS.
[ Security Fix ][ CTA ] Fixed the CTA title being output without any filtering. Decorative HTML such as line breaks and inline tags keeps working, while script tags and event attributes are now removed.
[ Security Fix ][ CTA ] Fixed the link URLs output by the CTA display not being escaped.

= 9.118.0 =
[ New Feature ][ SNS Share Button ] Added a Threads share button, with a show / hide toggle under ExUnit > Main Setting.
[ Spec Change ] Update vektor-inc/font-awesome-versions from 0.7.2 to 0.7.4
[ Bug Fix ][ Widget ] Fixed undefined array key `form_sort` warning in PHP 8 on the categories/taxonomies list widget form
[ Bug Fix ][ Facebook Page Plugin ] Fixed an issue where the Facebook Page timeline could stop displaying due to a change in Facebook's embed specification.
[ Design Bug Fix ][ Sitemap ] Changed the sitemap link color from a hardcoded value to `color: inherit` so it inherits the theme's color scheme correctly.
[ Design Bug Fix ][ SNS Share Button ] Changed the box-shadow color of the X and Threads share buttons to a lighter gray so the pressed-button effect meets the 3:1 non-text contrast ratio ( WCAG 1.4.11 ).
[ Security Fix ][ Widget ] Added allowlist validation for `form_sort` value in the widget save process
[ Security Fix ][ SNS Share Button ] Escaped the share button link URLs with esc_url() / esc_attr().

= 9.117.5 =
[ Bug Fix ][ Widget ] Added show_instance_in_rest to the ExUnit classic widgets so their settings are kept inline in the block-based widgets editor, preventing them from being hidden when reference widget resolution fails.

= 9.117.4 =
[ Bug Fix ][ Widget ] Fixed PHP 8.x warnings that were written to the error log when saving the Profile widget or the PR Blocks widget with certain settings.

= 9.117.3 =
[ Bug Fix ][ Setting Page ] Updated the shared admin component so that the setting screen styles and scripts are reloaded reliably after an update instead of being served from the cache, and so that the left side navigation is no longer cut off while a notice is displayed.
[ Spec Change ][ Page Top Button ] Improved screen reader and keyboard accessibility with an accessible label, a keyboard focus indicator, and removal from keyboard focus while the button is hidden.
[ Spec Change ][ Page Top Button ] The focus indicator now follows the silhouette of an uploaded image, and the show / hide animation respects the reduced motion preference.
[ Bug Fix ][ Article Structured Data ] Stopped outputting an empty "image" value in the JSON-LD when a post has no featured image, omitting the image field entirely instead.
[ Spec Change ][ Article Structured Data ] Changed the JSON-LD "image" to the ImageObject format (url/width/height) and switched the source to the original full-resolution image.
[ Bug Fix ][ Widget ] Fixed PHP 8.x undefined array key and undefined variable warnings that were written to the error log when saving widgets with unchecked checkboxes (Contact Section, Page, FB Page Plugin) or an invalid colour selection (Button, Twitter).

= 9.117.2 =
[ Bug Fix ] Fixed a warning in the article structured data output when the author user could not be retrieved.
[ Bug Fix ] Fixed an "Array to string conversion" PHP warning and an invalid "post-type-Array" body class that occurred on archives whose main query sets post_type to an array and that have no matching posts.
[ Bug Fix ] Fixed an issue where the Related Posts Settings section disappeared from the Customizer when both the Contact Section and Social Media Integration features were disabled.

= 9.117.1 =
[ Bug Fix ] Removed an unnecessary veu_get_common_options() call in vwu_register_css() that could trigger a "Call to undefined function" fatal error when the enqueue hooks ran before the packages were loaded in some environments.

= 9.117.0 =
[ Feature ][ Page Top Button ] Added "Width" and "Height" settings (in pixels) so users can resize the page top button image from the main setting page and the Customizer. The default 40 x 38 px size is preserved when either value is left blank.
[ Spec Change ][ Page Top Button ] Promoted the Customizer "Page top button image" label to an h2 group heading, and widened the gap between the description and the image thumbnail by 1.4x for easier scanning.
[ Bug Fix ][ CTA ] Fixed a PHP 8.1+ deprecation notice from ltrim() emitted by the CTA block on the frontend for visitors without the edit_post capability.
[ Spec Change ] Update vektor-inc/vk-admin from 0.7.0 to 0.8.0 to drop the duplicated VK_Custom_Html_Control / VK_Custom_Text_Control files that have been migrated to vk-helpers.
[ Spec Change ] Update vektor-inc/vk-breadcrumb from 0.2.8 to 0.2.9 and vektor-inc/vk-helpers from 0.2.1 to 0.3.0. VK_Custom_Html_Control / VK_Custom_Text_Control classes now ship from vk-helpers instead of vk-admin.
[ Bug Fix ][ Page Top Button ] Removed the unintended dark background, padding and border-radius inline style from the image preview on the ExUnit main setting page so the uploaded icon is no longer rendered with a black box.
[ Spec Change ][ Page Top Button ] Changed the "Configure with live preview in the Customizer" link on the main setting page to open in a new tab so it no longer interrupts editing.

= 9.116.0 =
[ Security Fix ][ Page Top Button ] Hardened the page top button image URL sanitizer to close additional CSS injection vectors (multi-byte C1 control characters and URL-encoded representations of dangerous characters).
[ Spec Change ][ Page Top Button ] Unified the hide_mobile sanitizer to use the shared veu_sanitize_boolean() callback, matching the Customizer setting. Added an is_array() guard to veu_pagetop_render() to prevent warnings on non-array arguments.
[ Bug Fix ] Fixed stylelint font-family-name-quotes violations by using the quoted "Font Awesome 5 Free" form and configuring gulp-clean-css to preserve quotes around font-family names.
[ Feature ][ Page Top Button ] Added an "image" setting so users can upload their own icon for the page top button from the main setting page and the Customizer. URL is sanitized to mitigate CSS injection.
[ Spec Change ][ Page Top Button ] Renamed the --ver_page_top_button_url CSS custom property to --veu_page_top_button_url. SCSS keeps a fallback so existing themes / custom CSS overriding the old name continue to work without any change.
[ Bug Fix ] Fixed an issue where JS files under vendor/ (such as vk_admin.js and Font Awesome *.min.js files) were not included in the release zip, causing 404 errors in the WordPress admin screen on sites installed from the dist package.

= 9.115.1 =
[ Security Fix ][ SNS Share Button ] Strengthened URL validation in the Hatena Bookmark and Facebook share count REST API callbacks. Host names are now compared using a case-insensitive exact match instead of substring matching; subdomains are not allowed.
[ Bug Fix ] Fixed vk_admin.js and the related CSS returning 404 on sites where WordPress is installed in a custom directory or wp-content has been moved. The asset URL is now resolved via plugins_url().
[ Spec Change ] Update vektor-inc/vk-admin from 0.5.0 to 0.5.1.

= 9.115.0 =
[ Spec Change ][ Post Type Manager ] Custom post types now always support 'custom-fields'. The checkbox has been replaced with an "Always enabled" indicator so ExUnit settings (noindex / CSS / CTA, etc.) are guaranteed to be saved.
[ Bug Fix ] Fixed an issue where settings such as noindex were silently lost on save for custom post types that do not declare 'custom-fields' support. The legacy metabox is now kept as a fallback on such post types.
[ Bug Fix ][ SNS Share Button ] Fixed an issue where URL validation in the Hatena Bookmark and Facebook share count REST API callbacks was always skipped, allowing share counts to be fetched for URLs other than the current site.
[ Bug Fix ][ Post Type Manager ] Fixed a PHP 8 warning triggered by add_post_type() when the 'veu_taxonomy' meta is stored as an empty string instead of an array. Non-array values are now safely treated as an empty list before iteration.

= 9.114.0 =
[ Spec Change ] Migrate post editor settings UI to block editor sidebar panels
[ Bug Fix ] Fixed binary files (images, fonts, etc.) being corrupted during dist process
[ Bug Fix ] Fixed block editor sidebar panels not appearing on sites installed from the dist zip because the build/ directory was excluded from the dist package.
[ Other ] Replace htmlspecialchars() with sanitize_text_field( wp_unslash() ) for $_POST input sanitization in save_post handlers.

= 9.113.6 =
[ Specification Change ] Update vektor-inc/font-awesome-versions from 0.7.0 to 0.7.2
[ Specification Change ] Update vektor-inc/vk-admin from 0.4.1 to 0.5.0
[ Specification Change ] Update vektor-inc/vk-term-color from 0.1.0 to 0.7.1

= 9.113.5 =
[ Bug Fix ] Fix array-type custom fields (e.g. veu_head_title) not being saved due to the SNS title XSS fix.
[ Design Bug Fix ][ Share Button / Related Posts / Contact Section ] Fix margin-top being overwritten by core margin-block-start.

= 9.113.4 =
[ Security Fix ][ SNS Share Button ] Fix stored XSS vulnerability in SNS Title meta box field (vkExUnit_sns_title). Added esc_attr() escaping on output to data-clipboard-text attribute and sanitize_text_field() on save to prevent attribute-breakout injection.

= 9.113.3 =
[ Bug Fix ][ Post Type Manager ] Prevent saving when required fields are empty (Post Type ID / Supports).
[ Bug Fix ] Fix editor CSS loading method.

= 9.113.2 =
[ Bug Fix ][ Contact Widget ] Delete unintended aaaa string

= 9.113.1 =
[ Other ] Font Awesome 7.1.0 support
[ Bug Fix ] Fix issue where first save of Active Setting turned all modules on and all widgets off.

= 9.113.0 =
[ Bug Fix ] Fix PHP Deprecated.
[ Specification Change ][ IE Alert ] Delete IE Alert function.

= 9.112.4 =
[ Bug Fix ][ SNS OGP Title ] Fix XSS.
[ Bug Fix ][ Custom CSS Single ] Fix backslash removal in custom CSS metabox on save.
[ Bug Fix ][ CTA ] Fixed an issue where if a CTA was placed using an action hook, even if it was set to hidden for the post type in the main settings, the CTA would still be displayed if it was set to visible for an individual post displayed in the list.

= 9.112.3 =
[ Bug Fix ][ Custom CSS Single ] Fix can't save css.

= 9.112.2 =
[ Specification Change ][ Add Reusable block menu ] Change menu name "Manage all reusable blocks" -> "Patterns"
[ Specification Change ][ Promotion Alert ] Change UI labels from "Promotion Alert" to "Promotion Disclosure" for better accuracy of functionality description.
[ Bug Fix ] Fix CTA / Custom CSS XSS.
[ Bug Fix ][ Title Tag ] Prevent the separator from appearing on the front page when the site description is empty.

= 9.112.1 =
[ Bug Fix ][ Default Thumbnail ] Fix issue where default thumbnail appears in media library list view.

= 9.112.0 =
[ Add function ][ Title Tag ] Add taxonomy title tag setting functionality for archive pages of categories, tags, and custom taxonomies.
[ Bug Fix ][ Default Thumbnail ] Fix an issue where post_thumbnail_id() returns null even when a default thumbnail is specified, in cases where no featured image is set.

= 9.111.0 =
[ Specification Change ][ sitemap ] Terms and taxonomies with zero articles will no longer be displayed.
[ Bug Fix ][ New Post Widget ] Fix PHP warning.

= 9.110.1 =
[ Bug Fix ][ Page List Ancestor ] Fix PHP Warning.

= 9.110.0 =
[ Specification Change ][ CTA ] Fix edit button color.
[ Specification Change ][ body class ] The body tag of the post page now has classes that include the category, tag, and taxonomy slugs.

Note: The changelog history prior to version 9.110.0 has been moved to the bundled changelog.txt file to keep this readme within WordPress.org's changelog length limit. You can also view it on GitHub: https://github.com/vektor-inc/vk-all-in-one-expansion-unit/blob/master/changelog.txt

== Upgrade Notice ==

Nothing.
