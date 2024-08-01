=== VK All in One Expansion Unit ===
Contributors: vektor-inc,kurudrive,jim912,hideokamoto,nc30,SaoriMiyazaki,catherine8007,naoki0h,rickaddison7634,una9,kaorock72,kurishimak,chiakikouno,daikiweb23,doshimaf,shimotomoki,mtdkei
Donate link:
Tags: Google Analytics, Related Posts, sitemap, Facebook Page Plugin, OG tags
Requires at least: 6.2
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 9.99.3.0
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

== Changelog ==

= 9.99.3 =
[ Bug fix ] Fix an issue where the activation screen causes an error in version 9.99.2

= 9.99.2 =
[ Bug fix ] Fix the layout issue of the CTA

= 9.99.1 =
[ Bug fix ] Roll back to 9.98.1

= 9.99.0 =
[ Specification Change ][ Page top button ] Add #top on body for pagetop btn
[ Specification Change ][ Taxonomy Widget ] Add dropdown mode. 
[ Specification Change ] Foce Load JS from footer is abolished.
[ Fix ] Add a title attribute on Google Tag Manager (noscript)
[ Bug fix ] Fix XSS of Widgets, CTA, Custom Post Type Manager.

= 9.98.1 =
[ Bug fix ] Fix translation

= 9.98.0 =
[ Add setting ][ Post Type Manager ] Add a menu icon setting.
[ Bug fix ][ Custom CSS ] Preserving media query operators in custom CSS.

= 9.97.2 =
[ Bug fix ] In WordPress 6.5, a link to the plugin page has been added to the admin bar on the front end by default. Consequently, ExUnit has been modified to no longer add this link on the front end.

= 9.97.1 =
[ Bug fix ][ Child page index ] In the case of automatically inserting the child page list without using a block, the issue where all pages were displayed in WordPress 6.5 has been fixed.
[ Design Bug Fix ][ Faq ] Fix stitching styles in Group blocks.

= 9.97.0 =
[ Specification Change ] Grouping in the activation interface.
[ Bug fix ][ Child page index ] Fix className vulnerability.
[ Bug Fix ][ Description ] Fixed the issue about PHP error under specified conditions.

= 9.96.0 =
[ Add setting ][ Post Type Manager ] Add a custom field setting.
[ Specification Change ][ Post Type Manager ] Change so that "with_front" can be specified as false in permalink settings
[ Bug Fix ][ SNS ] Fix OGP description on password protected page.
[ Bug Fix ][ Contact Section ] Fixed an issue where icons do not display when input in <i> tag format. 
[ Design Bug Fix ][ Child Page List ] Fixed an issue where a blank space appeared above the excerpt.

= 9.95.0 =
[ Add function ][ Icon Accessability ] Font Awesome Icon A11y Hide.
[ Other ][ PostType Manager ] Add Rewrite option of Post Type / Taxonomy.
[ Bug Fix ][ Page content to widget ] Fixed an issue where a warning occurs when the target page is deleted.

= 9.94.2 =
[ Add filter ][ HTML Sitemap ] veu_sitemap_exclude_post_types
[ Bug Fix ] Update CSS Optimizer 0.2.2

= 9.94.1 =
[ Specification Change ][ ChildPageList ] add post id / class for post infomation.
[ Bug Fix ][ CTA Block ] Fix error at widget.
[ Other ][ PostTypeManager ] Fix alert of post type slug and taxonomy slug.

= 9.94.0 =
[ Update ][ Font Awesome ] Update font awesome 6.4.3 ( with delete version 5 )
[ Bug Fix ][ Promotion Alert ] Fixed a bug where the 'Promotion Alert' settings metabox was not displayed on the post edit screen for custom post types.

= 9.93.3 =
[ Bug Fix ][ Promotion Alert ] Fix post-type checkbox.
[ Bug Fix ][ Share Button ] Display on site editor.

= 9.93.2 =
[ Bug Fix ][ Profile Widget ] Fix Twitter icon to X icon

= 9.93.1 =
[ Specification Change ] Change Footer Copyright

= 9.93.0 =
[ Add function ][ Contact Form Optimize ] Add code to prevent reCAPTCHA from displaying on pages other than Contact Form 7.
[ Add function ] Add function of front page structure data
[ Specification Change ][ Custom Post Type Manager ] Auto flash rewrite rule.
[ Bug fix ][ Promotion Alert ] In the checkbox for selecting post types to display advertising alerts, we fixed a bug where changes to the label names of posts and pages were not reflected.
[ Bug fix ][ Promotion Alert ] Fix no post type error.

= 9.92.4 =
[ Bug fix ][ Share Button ] Changed to always display the checkbox for hiding the share button.

= 9.92.3 =
[ Bug fix ][ Share Button ] Fixed a bug where the hide function did not work properly when the display target was not set to 'post content'.


= 9.92.2 =
[ Specification Change ][ Promotion Alert ] Change HTML structure.
[ Specification Change ][ Promotion Alert ] Change the label 'Alert Content' to 'Custom Alert Content'.

= 9.92.1 =
[ Specification Change ][ Promotion Alert ] Change labels of post type choice.
[ Specification Change ][ Promotion Alert ] Change HTML escape method of "Alert Content".

= 9.92.0 =
[ Add Function ] Promotion Alert
[ Bug fix ] Fixed the bug that causes a Fatal error when trying to preview a block theme on the theme selection screen.

= 9.91.1 =
[ Bug fix ][ Description ] delete "do_blocks".

= 9.91.0 =
[ Specification Change ] Change "Twitter" to "X".
[ Bug fix ] Custum CSS on article will affect on front view on Block theme.

= 9.90.3.2 =
[ Other ] Version only

= 9.90.3.1 =
[ Bug fix ][ VK Admin ] Fixed problem of filepath on Windows local environment.
[ Bug fix ][ Share button ] load sns icon correctly on site editor.

= 9.90.2.0 =
[ Bug fix ][ Share button ] Fix js file path ( // -> / )

= 9.90.1.0 =
[ Bug fix ][ Nwe post widget ] Fix PHP Error

= 9.90.0.1 =
[ Add filter ][ New Posts Widget ] Add veu_widget_new_posts_query filter
[ Bug Fix ] Fix widget panel design corruption / Update VK Admin 0.4.0
[ Other ] Cope with English Information / Update VK Admin 0.4.0
[ Other ] Delete VK Helper Class

= 9.89.1.0 =
[ Bug fix ] Fix media uploader not work
[ Bug fix ][ CTA ] Fixed the display in the site editor.

= 9.89.0.0 =
[ Specification Change ] Change default option value that in case of Block Theme
[ Bug fix ][ Recent Posts widget ] Fix php error

= 9.88.2.0 =
[ Bug fix ][ sitemap ] compatible for PHP 8.2
[ Bug fix ] fix block translation
[ Bug fix ][ Article Structure Data ] Cope with XSS
[ Bug fix ][ CTA ] Cope with classic CTA XSS

= 9.88.1.0 =
[ Bug fix ] fix meta derscription.

= 9.88.0.0 =
[ Add Function ] Exclude Page from Page List

= 9.87.3.0 =
[ Bug fix ] Lightning Footer URL

= 9.87.2.1 =
[ Bug fix ] Cope with PHP8.1

= 9.87.1.0 =
[ Bug fix ][ CSS Customize ] Fix cope with XSS in CSS form

= 9.87.0.1 =
[ Specification Change ][ All Block ] Fix Block Structure ( Cope with WordPress 6.2 ).
[Bug fix][ All Block ]Fix translate.

= 9.86.2.0 =
[ Bug fix ][ CTA ] Fix PHP error on no post page

= 9.86.1.0 =
[ Bug fix ][ Main setting ] Fix php error

= 9.86.0.0 =
[ Bug fix ][ Post List Ancestor ] Cope with XSS
[ Bug fix ][ CTA ] Cope with XSS
[ Bug fix ][ CTA ] Fix Error under no CTA Registered
[ Bug fix ][ CSS Optimize ] Fix Tree Shaking and Preload.
[ Bug fix ][ wp title ] Fix separator filter not work ( vkExUnit_get_wp_head_title_sep )
[ Bug fix ][ wp title ] Fix cope with custom post types
[ Bug fix ] Security Update

= 9.85.0.1 =
[ Specification Change ][ SNS : Share button ] Changed show/hide settings to only affect the_content and action hooks
[ Specification Change ][ SNS/FollowMe ] Moove setting position

= 9.84.3.0 =
[ Other ][ CTA ] Edit button position tuning

= 9.84.2.0 =
[ Specification Change ][ Default Thumbnail ] Change to default active

= 9.84.1.0 =
[ Bug fix ][ Google Analytics ] Fix PHP error in 9.82.0.0
[ Specification Change ][ Google Analytics ] Remove Customizer Setting.

= 9.84.0.0 =
[ Specification Change ][ title tag ] Changed the title tag of the Home page so that the title tag specified in the fixed page has priority over the content specified in "ExUnit > Main setting screen".

= 9.83.1.0 =
[ Bug fix ][ article structure ] Fix warning on 404 page.

= 9.83.0.0 =
[ Specification Change ][ body class ] Add post top page to class

= 9.82.0.0 =
[ Add Function ][ Google Analytics ] Be able to set Both UA and GA4 tag.

= 9.81.3.0 =
[ Bug fix ]Fixed broken function of article structure data on user edit page

= 9.81.2.0 =
Update translate

= 9.81.1.0 =
[ Specification Change ] Change order of active setting

= 9.81.0.0 =
[ Add function ] Add function of article structure data

= 9.80.1.0 =
[ Bug fix ] Fixed broken layout of setting page

= 9.80.0.0 =
[ Add Function ] Add CTA Block.
[ Other ] Update admin setting page library 2.5.0

= 9.79.0.1 =
[ Other ] Default setting corresponds to block theme
[ Other ] Update admin settingpage library 2.4.0

= 9.78.1.0 =
[ Bug Fix ][ Auto Eycatch ] Fix auto eyecatch do not display

= 9.78.0.0 =
[ Add function ][ SNS ] Add function of change out put action hook.
[ Specification Change ][ Page list from ancestor block ] change icon

= 9.77.0.0 =
[ Specification Change ][ Custom Post Type Manager ] Change to Cope with Block Editor default
[ Bug Fix ] Fix register thumbnail on edit screen under welcart environment

= 9.76.3.0 =
* [ Bug Fix ][ HTML Site Map ] Delete wp_reset_postdata();

= 9.76.2.0 =
* [ Design Bug Fix ][ Child Page List ] Fixed an issue button positions distorted depending on the theme

= 9.76.1.0 =
* [ Bug Fix ][ Custom Post Type Manager ] Fix translate
* [ Bug Fix ][ CSS Optimize lib ] library Update
* [ Bug Fix ] Fix load Font Awesome Files on WordPress.com
* [ Other ][ BreadCrumb ] Update composer library 0.2.2

= 9.76.0.1 =
* [ Specification Change ] Update VK Font Awesome Versions 0.4.0
* [ Bug fix ][ Child Page List / Contact section / Page list from ancestor ] Fix duplicate Additional CSS classes.

= 9.75.0.0 =
* [ Bug fix ] Fixed add common attributes ( attribute from VK Blocks 1.29 - )
* [ Specification Change ] Use composer vk-term-color

= 9.74.1.0 =
* [ Bug fix ] Fixed ExUnit icon appearing in other menus

= 9.74.0.0 =
* [ Google Analytics ][ Add function ] Add option for disable tracking of logged in user

= 9.73.3.0 =
* [ Bug fix ] Fix Admin Fatal Error

= 9.73.2.0 =
* [ Bug fix ] Fix Activate setting page warning
* [ Bug fix ] Fix Facebook btn brand color

= 9.73.1.0 =
* [ Bug fix ] Fix admin escape

= 9.73.0.0 =
* [ Add function ] Cope with font awesome 6
* [ Bug fix ][ title ] Fix admin title escape
* [ other ] Add ExUnit icon on admin menu

= 9.72.0.0 =
* [ Delete Function ] Print meta keyword tag
* [ Delete Function ] Print Favicon
* [ Delete Function ] Bootstrap
* [ Delete Function ] TinyMCE Stye
* [ Delete Function ] VK Blocks

= 9.71.1.1 =
* [ Bug fix ][ Share Btn ] Cope with other theme

= 9.71.0.27 =
* [ Add function ] Add bread crumb scheme

= 9.70.2.0 =
* [ Bug fix ] fix active settingscreen about widget.

= 9.70.1.0 =
* [ Bug fix ][ sitemap / page-list-from-ancesters / share ] Fix preview on WP5.9

= 9.70.0.0 =
* [ Specification Change ][ Design Tuning ] Customize panel font size

= 9.69.3.0 =
* [ Bug Fix ][ OG Title ] Fix custom post type archive page og title bug

= 9.69.2.0 =
* [ Design Bug Fix ][ Related Posts ] Fix layout in case of long title text

= 9.69.1.0 =
* [ Design Bug Fix ][ Related Posts ] Fix layout in case of not vektor theme

= 9.69.0.0 =
* [ Other ] Cope with Lightning G3 Pro Unit 404 Page customize
* [ Design tuning ] metabox css tuning in edit screen

= 9.68.4.0 =
* [ Bug fix ][ smooth scroll ] fix scroll top position on admin bar

= 9.68.3.0 =
* [ Bug fix ][ smooth scroll ] fix scroll top position on lightning g3

= 9.68.2.0 =
* [ Bug fix ][ smooth scroll ] fix scroll top position on lightning g3

= 9.68.1.0 =
* [ Bug fix ] fix wp title php error

= 9.68.0.0 =
* [ Add Function ][ head title ] Add change head title function
* [ Bug fix ][ SNS ] Fix share button on customize screen

= 9.67.2.0 =
* [ Other ][ term color ] Fix php error

= 9.67.1.0 =
* [ Other ][ Smooth scroll ] Description adjustment

= 9.67.0.0 =
* [ Other ] Partial refactoring
* [ Other ][ IE Alert ] Add notice to Edge IE mode

= 9.66.2.0 =
* [ Bug fix ][ SNS ] Fix share button error

= 9.66.1.0 =
* [ Bug fix ][ 3PR Widget ] Fix layout
* [ Specification Change ][ Post type Manager ] automatic permalink update

= 9.66.0.0 =
* [ Specification Change ][ Smooth scroll ] Fix bug on Safari and add CSS mode.
* [ SNS ] Add copy button.

= 9.65.0.0 =
* [ Specification Change ] Add filter vk_term_color_taxonomy / vk_get_single_term_with_color / vk_post_view

= 9.64.6.0 =
* [ Other ][ CTA ] PHPUnit test update

= 9.64.5.0 =
* [ Bug fix ][ CTA ] Fix Old CTA ( by Custom Fieleds ) Title

= 9.64.4.0 =
* [ Bug fix ][ Banner widget ] Added due to missing URL input field

= 9.64.3.0 =
* [ Bug fix ][ Call To Action ] Fix global $post pollution it's bring to bug for child page list and so on.

= 9.64.2.0 =
* [ Bug fix ][ Call To Action ] Fix comment bug.

= 9.64.1.0 =
* [ Bug fix ][ Banner Widget ] Fix change banner image on widget setting page

= 9.64.0.0 =
* [ Specification Change ][ CTA ] Change disable priority by individual post

= 9.63.1.0 =
* [ Other ][ CTA ] text only.

= 9.63.0.0 =
* [ Add function ][ CTA ] Add function of change out put action hook.

= 9.62.0.0 =
* [ Specification Change ][ Child page list ] readmore btn class btn-xs -> btn-sm
* [ Specification Change ][ HTML Sitemap ] tuning of taxonomy css
* [ Other ] Cope with WordPress 5.8

= 9.61.6.0 =
* [ Bug Fix ] Fix SNS icon

= 9.61.5.0 =
* [ Specification Change ] Update VK Admin Library.

= 9.61.4.0 =
* [ Specification Change ] Update VK Admin Library.

= 9.61.3.0 =
* [ Specification Change ] Update VK Admin Library.

= 9.61.2.0 =
* [ Specification Change ] Admin dashboard banner include
* [ Specification Change ] Stop exclude CSS Var by Tree shaking

= 9.61.1.0 =
* [ Bug fix ][ Page Top Btn ] fix underline

= 9.61.0.0 =
* [ Design specification Change ][ Contact section ] Change btn padding
* [ Bug fix ]Cope with PHP 8
* [ Other ] update VK Helpers
* [ Other ] update CSS Optimize

= 9.60.1.0 =
* [ Bug fix ][ 3PR Widget ] Fix Break Point

= 9.60.0.0 =
* [ Specification Change ] Moved Google Tag Manager functionality to ExUnit

= 9.52.1.0 =
* [ Specification Change ] Update VK-Admin Library

= 9.52.0.0 =
* [ Design Change ][ block icon ] Change block icon

= 9.51.0.0 =
* [ Specification Change ] Change font awesome default version to 5
* [ Specification Change ][ Contact section ] Add tel color.
* [ Specification Change ][ CSS Customize ] delete css comment
* [ Design bug fix ][ Share button ] fix no margin bottom

= 9.50.0.0 =
* [ Specification Change ][ NewPostsWidget ] Delete target blank from term link
* [ Specification Change ][ Contact Section ] class and margin tuning
* [ Specification Change ] come css chenge to css variable

= 9.49.7.0 =
* [ bug fix ][ cta ] enable render_block in cta

= 9.49.6.0 =
* [ bug fix ][ sns ] fix unnecessary link error

= 9.49.5.0 =
* [ bug fix ][ sns ] fix php notice

= 9.49.4.0 =
* [ bug fix ][ share button ] cope with loop display

= 9.49.3.0 =
* [ bug fix ][ Before loop widget area ] fix translate

= 9.49.2.0 =
* [ bug fix ][ noindex ] fix php error

= 9.49.1.0 =
* [ Bug fix ][ Archive widget ] fix PHP error

= 9.49.0.0 =
* [ Add function ][ Noindex ] Cope with print to archive page
* [ Add function ][ Before loop widget area ] Add widget area to before loop on archive page

= 9.48.3.0 =
* [ Bug fix ] Cope with before 5.0

= 9.48.2.0 =
* [ Specification Change ] Load Term Color on init

= 9.48.1.0 =
* [ Bug fix ] fix active settingscreen error.

= 9.48.0.0 =
* [ Specification Change ][ Archive widget ] default setting tuning and add no select option.

= 9.47.0.0 =
* [ Specification Change ] Dashboard information adjustment.

= 9.46.1.0 =
* [ Bug fix ] fix term color can't edit.

= 9.46.0.0 =
* [ Add function ][ Archive list widget ] can be select list style ( add "select" )

= 9.45.0.0 =
* [ Add function ] Add plugin short cut link to adminbar
* [ Design specification Change ][ Child Page List / Page list from ancestors ] When set to first object that delete margin top.

= 9.44.2.0 =
* [ Design bug fix ] Fix drop in contact phone number layer during mainSection-base-on

= 9.44.1.0 =
* [ Design bug fix ] To be don't display underline on category label at hover.

= 9.44.0.9 =
* [ Other ] ReUpdate

= 9.44.0.0 =
* [ Specification Change ] Can be use term color in VK Recent Post Widget.
* [ Specification Change ] Can be active control the CSS Optimize.

= 9.43.2.0 =
* [ Bug fix ][ CSS Optimize ] fix sanitize bug ( Can not save )

= 9.43.1.0 =
* [ Bug fix ][ CSS Optimize ] fix woo css exclude

= 9.43.0.0 =
* [ Specification Change ][ CSS Optimize ] default off / exclude wooCommerce preload
* [ Add function ][ CSS Optimize ] Add exclude handles

= 9.42.1.0 =
* [ Bug fix ] Fix Customize error ( ad vk customize helpers )

= 9.42.0.0 =
* [ Specification Change ][ CSS Optimize ] Change to common setting

= 9.41.0.0 =
* [ Specification Change ] Cope with G-XXXXXXXXXX
* [ Bug fix ][ Facebook Page Plugin / twitter widget ] fix unload

= 9.40.0.0 =
* [ Add filter ][ ie alert ] Can be able to change the message in the filter

= 9.39.1.0 =
* [ Bug fix ][ Child Page Index Block ] To be able to select parent page to cureent page

= 9.39.0.0 =
* [ Specification Change ][ wp title ] Change search result title in case of no keyword
* [ Specification Change ][ Child Page Index ] To be able to change thumbnail outer html(Change filter specification).

= 9.38.0.0 =
* [ Add function ][ contact section ] Add link target setting
* [ bug fix ] fix when plugin VK Blocks activation that ExUnit widget activation setting was delete

= 9.37.2.0 =
* [ Design bug fix ] Scrolled Page top button CSS bug fix

= 9.37.1.1 =
Only Change Version Number

= 9.37.1.0 =
* [ Design bug fix ] Page top button CSS bug fix / childPage list title CSS bug fix

= 9.37.0.0 =
* [ Specification change ][ Related posts ] convert html part to iindependent function
* [ Specification change ][ follow me ] convert html part to indipendent function

= 9.36.1.0 =
* [ bug fix ][ SNS/OG ] Fix print default image

= 9.36.0.0 =
* [ other ][ page top button ] add setting to ExUnit Main Setting screen

= 9.35.0.0 =
* [ Specification Change ][ post type manager ] max taxonomy change from 3 to 5

= 9.34.1.0 =
* [ Bug fix ][ page top button ] fix text display bug

= 9.34.0.0 =
* [ Specification Change ][ page top button ] Change mobile hidden option

= 9.33.0.0 =
* [ Add option ][ page top button ] Add mobile display option

= 9.32.0.0 =
* [ Specification change ][ contact section ] change horizontal layout class name

= 9.31.11.0 =
* [ Design tuning ][ Contact section ]

= 9.31.10.0 =
* [ bug fix ][ Block ] Fix Attributes Error

= 9.31.9.0 =
* [ bug fix ][ Default thumbnail ] Thumbnail doesn't display on VK Posts

= 9.31.8.0 =
* [ bug fix ][ Default thumbnail ] fix OGP don't refrect


= 9.31.7.0 =
Only Change Version Number

= 9.31.6.0 =
* [ bug fix ] Fix the date of relate posts

= 9.31.5.0 =
Only Change Version Number

= 9.31.4.0 =
Only Change Version Number

= 9.31.3.0 =
* [ bug fix ] SNS button css tuning

= 9.31.2.0 =
* [ Specification Change ] Change CSS Optimize default setting

= 9.31.1.0 =
* [ bug fix ] PageTop css tuning

= 9.31.0.0 =
* [ Add function ] CSS Optimize(Tree shaking)

= 9.30.2.0 =
* [ bug fix ] WP5.5 API Alert

= 9.30.1.0 =
* [ bug fix ] IE Alert css tuning

= 9.30.0.0 =
* [ Add function ][ IE Alert ]
* [ Add function ][ Disable Emojis ]
* [ bug fix ][ Smooth Scroll ] Fix malfunction on lightning default slide.
* [ Other ] Functional description improvement

= 9.29.7.0 =
* [ bug fix ][ Contact Section ] Fix Btn text color become defeat by footer design setting.

= 9.29.6.0 =
* [ bug fix ][ Smooth Scroll ] Fix Pagetop smooth scroll was not working on Firefox.

= 9.29.5.0 =
* [ bug fix ][ Contact Section ] Fix Btn text color become defeat by footer design setting.

= 9.29.4.0 =
* [ Bug fix ] Button block align bug fix

= 9.29.3.0 =
* [ Bug fix ] Cope with Gutenberg 8.6 bug

= 9.29.2.0 =
* [ Bug fix ] Add CTA Alt

= 9.29.1.0 =
* [ Bug fix ] VK Blocks update to 0.38.5 / Delete button class in src/blocks/button/ / Cope with key color at btn-outline-primary.

= 9.29.0.0 =
* [ Other ] Cope with ExUnit Contact Section on Lightning Sidebar and Footer.
* [ Other ] Change function description comment.

= 9.28.3.0 =
* [ Bug fix ][ child-page-index ] Fix Parent Page Save not working

= 9.28.2.0 =
* [ Bug fix ][ editor css ] Fix editor-css for ExUnit Blocks not working

= 9.28.1.0 =
* [ VK Blocks Update ] 0.38.2
* [ Bug fix ][ smooth scroll ] Fix smooth scroll not working on Button block
* [ Bug fix ][ Share Button ] count js error fix

= 9.28.0.0 =
* [ VK Blocks Update ] 0.38.1
* [ Add function ][ Profile Widget ] Add icon style option

= 9.27.0.0 =
* [ VK Blocks Update ] 0.37.4
* [ CTA ] Add old layout choice function

= 9.26.2.0 =
* [ Bug fix ][ Child Page Index ] CSS bug fix

= 9.26.1.0 =
* [ Specification Change ][ Child Page Index ] CSS tuning

= 9.26.0.0 =
* [ Specification Change ][ Child Page Index ] Change CSS layout from flatheight.js

= 9.25.0.0 =
* [ Other ] VK Blocks Update 0.35.5
* [ Specification Change ] Child Page List Add Link
* [ Bug fix ] PHP notice fix

= 9.24.0.0 =
* [ Add function ] Add Reusable block menu

= 9.23.0.0 =
* [ Add function ] JS move to footer

= 9.22.1.0 =
* [ Bug fix ] ExUnit Block hidden function bug fix
* [ Group block style ] Add alert style

= 9.22.0.0 =
* [ Add function ] Default eyecatch image

= 9.21.0.0 =
* [ Specification change ] Font Awesome 5.11 -> 5.13
* [ bug fix ][ Page list from ancestor ] do not display bug fix

= 9.20.0.0 =
* [ VK Blocks Update ] 0.31.0
* [ Add new block ][ Border Box ]
* [ bug fix ][ list ] 2digits number display bug fix

= 9.19.0.0 =
* [ Add Function ][ Add block ] HTML SiteMap
* [ Add Function ][ Add block ] Page list from ancestor

= 9.18.1.0 =
* [ bug fix ][ other widget ] bug fix of save all widget disable setting.

= 9.18.0.0 =
* [ Add Function ][ Add block ] Contact Section
* [ Add Function ][ Add block ] Child Page Index
* [ Design tuning ][ VK Block 0.27.0 ] CSS Bug fix on Edit screen

= 9.17.0.0 =
* [ Specification change ][ sns twitter card ] Print Twitter Card if Twitter ID no setted
* [ Post Type Manager ] Improved REST API description
* [ Post Type Manager ] Add term column to admin column

= 9.16.2.0 =
* [ bug fix ][ Block 0.26.7 ] merely js rebuild

= 9.16.1.0 =
* [ bug fix ][ Block 0.26.5 ] button color change bug fix

= 9.16.0.2 =
* [ bug fix ][ Block 0.26.4 ] editor css build miss fix

= 9.16.0.1 =
* [ bug fix ][ Block 0.26.3 ] Outline style

= 9.16.0.0 =
* [ Add function ][ Block 0.26.2 ] Add hidden function
* [ Add function ][ Block 0.26.2 ] Add Button text style
* [ Add function ][ Block 0.26.2 ] Add wide size

= 9.15.5.0 =
* [ bugfix ][ smoothscroll ]

= 9.15.4.0 =
* [ bugfix ][ smoothscroll ]
* [ bugfix ][ metakeyword ]

= 9.15.3.0 =
* [ bugfix ][ no index no follow ] don't work...

= 9.15.2.0 =
* [ bugfix ] MuitiSite Bug fix

= 9.15.0.0 =
* [ Add function ][ Contactform7 Asset Optimize ] Contact Form Speeding up
* [ Specification change ] JacaScript refactaring ( Speeding up )

= 9.14.0.0 =
* [ Add function ][ post list widget ] Cope with multi post types
* [ Add function ] Add share button block

= 9.13.1.0 =
* [ bugfix ] fix bug of cant save widget enablation

= 9.13.0.0 =
* [ specific change ][ TaxListWidget ][ add filter ] add tax list args filter 'veu_widget_taxlist_args'

= 9.12.0.0 =
* [ Add function ] Widget Active Controll

= 9.11.5.0 =
* [ bug fix ][ blocks ] Editor css was not working
* [ Specification change ] Delete language files ( use GlotPress )

= 9.11.4.0 =
* [ Bug fix ] Fix Lightning Design skin specific design overwrite not working.

= 9.11.3.0 =
* [ Specification change ] Default display the customizer ExUnit Panel

= 9.11.2.0 =
* [ Specification change ][ vk blocks ] YouTube display width : 100%
* [ Specification change ][ vk blocks ] css load point controll

= 9.11.1.0 =
* [ bug fix ] css customize error fix

= 9.11.0.0 =
* [ Add function ] Speeding setting ( load point controll of css file and css customize )

= 9.10.1.0 =
* [ Specification change ] load ExUnit css and block css on header from footer

= 9.10.0.0 =
* [ Specification change ] load ExUnit css on footer from header

= 9.9.0.0 =
* [ Specification change ] load block css awesome on footer from header
* [ Specification change ] load font awesome on footer from header

= 9.8.3.0 =
* [ Design Tuning ] Add margin bottom of Related Posts

= 9.8.2.0 =
* [ Design Tuning ] Add margin bottom to share button

= 9.8.1.0 =
vk blocks 0.17.6 update

= 9.8.0.3 =
Deploy setting

= 9.8.0.0 =
vk blocks 0.17.2 update

= 9.7.1.0 =
* [ bugfix ][ content widget ] Cope with title style from block.

= 9.7.0.0 =
* [ Add function ][ Block ] Core block style expand


= 9.6.9.0 =
* [ bugfix ] Use with VK Post Author Display bug fix

= 9.6.8.0 =
* [ Specification change ] ファイル階層一部変更

= 9.6.7.0 =
* [ library update ] library update

= 9.6.6.0 =
* [ bug fix ][ vk blocks ] WP 5.3 column bug fix
* [ bug fix ][ SNS ] Facebook OG size first load

= 9.6.5.0 =
* [ bug fix ][ New Posts Widget ] Term display
* [ bug fix ][ Related Posts ] Lightning BS4 Layout

= 9.6.4.0 =
* [ bug fix ] Chrome metabox position fix

= 9.6.3.0 =
* [ Specification change ][ HTML SiteMap ] Change class name.

= 9.6.2.0 =
* [ Specification change ][ HTML SiteMap ] Add class name.
* [ bug fix ][ HTML SiteMap ] PHP undefined error

= 9.6.1.0 =
* [ Add function ][ HTML SiteMap ] Exclude post type
* [ Specification change ][ HTML SiteMap ] Abolished hidden page setting on ExUnit Main Setting Page.
* [ bug fix ] metabox display

= 9.6.0.0 =
* [ Specification change ][ font awesome ] 5.6 -> 5.10.1
* [ bug fix ] metabox display

= 9.5.3.0 =
Chhhange requires at least: 5.1.0

= 9.5.1.0 =
* [ Bug fix ][ font awesome ] css and js path bug fix

= 9.5.0.0 =
* [ Specification change ][ Child Page list / Page list from ancestor / contact section ] CSS priority change

= 9.4.3.1 =
Merely Version Change

= 9.4.3.0( Beta ) =
* [ Bug fix ][ admin information ] Bug fix of setting page on english version

= 9.4.2.0( Beta ) =
* [ Bug fix ][ VK Blocks ][ title ] no style bug fix

= 9.4.1.0( Beta ) =
* [ Bug fix ][ VK Blocks ][ baloon ] mobile layout bug fix

= 9.4.0.0( Beta ) =
* [ Add Function ][ VK Blocks ] Add marker

= 9.3.3.0( Beta ) =
* [ bugfix ][ OG title ] When front-page that to be single page title, not blog name bug fixed.

= 9.3.2.0( Beta ) =
* [ Desing tuning ] customize panel design tuning

= 9.3.1( Beta ) =
* [ Bug fix ][ title ] Save bug fix
* [ Bug fix ][ smooth scroll ] wooCommerce error fix

= 9.3.0( Beta ) =
* [ Add function ][ VK Blocks ] Add table of contents block ( pro version )

= 9.2.0.6( Beta ) =
Restore version 9

= 9.1.3(8.3.1) =
* add GitHub information

= 9.1.2(8.3.1) =
* Back version to 8.3.1

= 9.1.1 =
* [ Delete function ] test version update checker.

= 9.1.1 =
* [ Delete function ] test version update checker.

= 9.1.0 =
* [ Delete function ] test version update checker.

= 9.0.7 =
* [ Specification change ][ New Posts Widget ] Change title escape wp_kses().

= 9.0.6 =
* [ bug fix ] Can not access ExUnit admin page bug fix.

= 9.0.3 =
* [ Admin ][ CSS Tuning ] metabox css tuning.

= 9.0.2 =
* [ Specification change ][ SNS Button ] Change css priority.

= 9.0.1 =
* [ bug fix ] Post Author Display Bug fix

= 8.9.4 =
* [ Specification change ][ VK Blocks ][ Staff ] font style tuning.

= 8.9.3 =
* [ Specification change ][ VK Blocks ][ Staff ] Change H tag and deal with Lightning 1 column template.

= 8.9.2 =
* [ Bug fix ][ VK Blocks ] Load block failed.

= 8.9.1 =
* [ Bug fix ][ Block ][ title ] When title margin set that Title align not work.

= 8.9.0 =
* [ Add function ][VK Blocks][Add New Block] Staff Block

= 8.4.0 =
* [ Specification change ] marge meta box & refactaring

= 8.4.0 =
* [ Specification change ] marge meta box & refactaring
* [ Add Function ] Add no index tag setting.

= 8.3.2 =
* [ Add Function ] Add beta tester.

= 8.3.1 =
* [ Design tuning ][ SNS Button ] Insert sns btns to before content

= 8.3.0 =
* [ Add function ][ SNS Button ] Insert sns btns to before content

= 8.2.0 =
* [ Child Page index ][ Add filter ] veu_childPage_list_read_more_txt
* [ Specification change ][ textdomain ] step to GlotPress

= 8.1.3 =
* [ bugfix ][ insert ads ] can't remove post type check box

= 8.1.2 =
* [ bugfix ][ new-posts-widget ] Undefined variable error fixed

= 8.1.1 =
* [ bug fix ] sns title custom value do not save bug fix

= 8.1.0 =
* [ Specification change ][ OG title custom ] Add custom post type support.

= 8.0.7 =
* [ Specification change ][ Auto Eye Catch ] Change hook name

= 8.0.6 =
* [ Specification change ][ Auto Eye Catch ] AAdd post type filter hook

= 8.0.5 =
* [ Bug fix ][ main setting page ] Admin page image uploader select button not transrated

= 8.0.4 =
* [ Bug fix ][ main setting page ] media uploader bug fix ( Cope with main setting page suffix changed )

= 8.0.3 =
* [ Bug fix ][ template-tags ][ vk get_post_type ] no post bug fix
* [ Specification change ][ template-tags ][ meta description ] escape & add_filter

= 8.0.2 =
* [ Other ][ dashboard ] Add Link banner

= 8.0.0 =
* [ Bug fix ][ PR Blocks ] When link url not set that no print a Tags
* [ Specification change ][ PR Blocks ] Change outer tag article to div
* [ Specification change ][ PR Blocks ] Change h1 tag to h3 tag

= 7.9.1 =
* [ Bug fix ][ CSS Customize ] encode bug fix

= 7.9.0 =
* [ Add Function][ Add Insert ] Google Auto Ads
* [ Bug fix ][ smooth scroll ] Anchor link header fix offset

= 7.8.0 =
* [ Add New Block ][ VK Blocks ] Title
* [ Add New Block ][ VK Blocks ] Responsive Spacer
* [ Bug fix ][ VK Blocks ][ Outer ] FireFox and Eddge design fix
* [ Add Function][ VK Blocks ][ Outer ] Add link id setting

= 7.7.4 =
* [ Specification change ][ Contact section ] Tel icon default setting.

= 7.7.3 =
* [ Design tuning ][ Contact section ] Tel icon position tuning.

= 7.7.2 =
* [ Bug fix ][ VK Blocks ][ outer ] Lightning Pro theme（No child） no work bug fix

= 7.7.1 =
* [ Bug fix ][ VK Blocks ][ outer ] Child theme no work bug fix

= 7.7.0 =
* [ Add function ][ CTA ] Add content field free layout function
* [ Add function ][ New post widget ] Add default thumbnail function

= 7.6.0 =
* [ Add function ][ VK Blocks ][ Outer ] Add border setting function.

= 7.5.0 =
* [ Add function ][ VK Blocks ] Add Outer & PR Content block.

= 7.4.0 =
* [ Bug fix ][ Nav menu ] Default permalink bug fix
* [ Specification change ][ smooth scroll ] This function can be off by user.

= 7.3.1 =
* [ Bug fix ] Nav menu class custom.

= 7.3.0 =
* [ Add function ] Nav menu class custom.

= 7.2.0 =
* [ Add function ] Add body class.

= 7.1.0 =
* [ Add function ][ VK Blocks ] Add Button & PR Blocks block.

= 7.0.2 =
* [ bug fix ][ Site Map ] Excrude no show ui taxonomy.

= 7.0.1 =
* [ bug fix ][ Page Top Button ] js error fixed.

= 7.0.0 =
* [ Specification change ][ vk_get_page_description ] delete do_shortcode 6 movt escape point.
* [ Specification change ][ Page Top Button ] Change system.
* [ Specification change ][ Site Map ] Add Post type class to print html.
* [ Specification change ][ CTA ] Add id to outer tag.
* [ Add function ][ Insert Ads  ] Add function of add Ads to custom post types.

= 6.11.0 =
* [ Specification change ][ PostTypeManager ] Change add action point 'init' to 'after_setup_theme'

= 6.10.0 =
* [ FontAwesome ] Version up to 5.6

= 6.9.2 =
* [ bug fix ][ vk blocks ] Fixed bug that becomes unusable in WordPress 5.0

= 6.9.0 =
* [ Specification change ][ VK Blocks ] Gutenberg Blocks Change css class name

= 6.8.0 =
* [ Add function ][ VK Blocks ] Add Gutenberg Blocks!

= 6.7.1 =
* [ Main Setting Page ][ Bug fix ] Media Upload bug fix

= 6.6.6 =
* [ Button Widget ][ Add Function ] Reflect the widget label
* [ Banner Widget ][ Add Function ] Reflect the widget label

= 6.6.3 =
* [ Profile Widget ][ bug fix ] Font Awesome 5 RSS icon bug fix
* [ PAGETOP BTN ] Change init name
* [ Font Awesome ] Change default version to 5

= 6.6.0 =
* [ Custom CSS ][ Add Function ] Singlur page custom css
* [ SNS Btn ][ bug fix ] No background count color fix

= 6.5.4 =
* [ veu_flowBox ][ bug fix ] Allow image 404 fix

= 6.5.3 =
* [ Page Top ][ bug fix ] Hover Pointer

= 6.5.1 =
* [ Follow Me ][ bug fix ] feedly btn image url http to https.
* [ Page Top ][ Specification change ] Abolish Font Awesome and change to original image loading.
* [ FontAwesome ] delete old files

= 6.4.0 =
* [ Font Awesome ] Corresponding to Font Awesome 5.0

= 6.3.0 =
* [ GA ] add gtag.js setting

= 6.2.2 =
* [ GA ][ bug fix ] customize error fix

= 6.2.0 =
* [ SNS ][ Follow Me Section ] Change design
* [ SNS ][ Add function ] Add customizer setting

= 6.1.0 =
* [ Add Function ] Add Page Top Button
* [ Google Analytics ][ Add function ] Add customizer setting

= 6.0.0 =
* [ Add Widget ] Add Twitter Widget.
* [ Page Widget ][ Add function ] Improve the dropdown list order.

= 5.9.1 =
* [ bugfix ] php 7.2
* [ Design tuning ]

= 5.7.0 =
* [ SNS Button ][ Add function ] Add color setting & fill or outline
* [ Contact info widget ][ Add function ] New widget!!
* [ 3PR / PR Block ][ bug fix ] reload response improvement.
* [ Page Widget ][ Add function ] Can use child page index and ancestor page list display.

= 5.5.0 =
* [ Custom Post Type Manager ][ add Function ] Add taxonomy tag setting.

= 5.4.6 =
* [ CTA Widget ][ add function ] Random display

= 5.4.5 =
* [ 3PR Widget ][ add filter ] read more text

= 5.4.0 =
* [ Package Manager ][ Bug fix ] Image height fix
* [ Profile Widget ][ Add Function ] SNS Icon Color Change
* [ Profile Widget ][ Specification change ] Allow html tag
* [ Page Widget ][ Add Function ] Title text setting
* [ New Posts Widget ][ Add Function ] More Link Text setting
* [ CTA ][ Add Function ] Add Random display
* [ Taxonomy Widget ][ Add Function ] Select null post term hide

= 5.3.6 =
* [ Child Page List ][ Bug fix ] Image height fix

= 5.3.4 =
* [ Share Button ][ bug fix ] js error
* [ 3PR Area Widget ][ bugfix ] Link open blank not work …
* [ Facebook Page Plugin ] Multilingual

= 5.2.4 =
* [ Bug fix ][ CTA Widget ] PHP notice

= 5.2.0 =
* [ Add Function ] PHP Version activate

= 5.1.0 =
* [ Add Function ][ Button Widget ] Button widget is now available!!
* [ Bug fix ][ Page Widget ] New Posts Widget PHP error fix

= 5.0.0 =
* [ Add Function ][ New Post Widget ] The display in the update date order is now available.
* [ Add Function ][ Banner Widget ] Banner widget is now available!!
* [ Add Function ][ Page Widget ] Allow Private post content display

= 4.7.0 =
* [ CTA ][ Add Function ]Add CTA Widget
* [ Contact info ][ Add Function ] Tel call link / html or image instead
* [ Main setting ][ bug fix ] image picker

= 4.6.0 =
* [ HTML Site Map ][ Bug fix ] Hidden no item taxonomy name.
* [ SNS Buttons ][ Add function ] Hide with specified post type
* [ Page List Ancestors ][ Design Tuning ]
* [ 3PR area ][ Design tuning ]
* [ Child Page Index ][ Specification change] Bootstrap dependence abolished.

= 4.5.0 =
* [ Add Function ][ PR Blocks Widget] Add PR Blocks Design type.

= 4.4.0 =
* [ Add Function ] Custom post type and custom taxonomy manager

= 4.3.9 =
* [ PR Block / 3PR Block ][ Design tuning ] Change summary line height.
* [ Contact section ][ Design tuning ] Fix break point of the text and btn.

= 4.3.8 =
* [ Bug fix ][ Page widget ] PHP notice

= 4.3.7 =
* [ Design Tuning ][ SNS Button ] Margin Tuning

= 4.3.3 =
* [ Design Tuning ] Related Posts / Profile widget

= 4.3.2 =
* [ Design Tuning ] Ancestor page list

= 4.3.1 =
* [ Other Tuning ] Improvement of RSS reading speed
* [ Specification change ] ! Change Admin RSS filter name

= 4.3.0 =
* [ Design Tuning ][ Ancester Page List ]
* [ Design Tuning ][ VK PR Blocks Widget ] Change break point
* [ Specification change ] Admin page common UI

= 4.2.2 =
* [ Bug fix ][ SNS button] Hide archive page

= 4.2.1 =
* [ Specification change ] Change the insert point(filter) of the content bottom item
* [ Specification change ] Change font awesome version
* [ Design bug fix ] page card size

= 4.1.0 =
* [ Add Function ][ Add Widget ] Child Page list
* [ Other ][ SNS Button ] Redesign

= 4.0.0 =

* [ Add Function ][ Insert Ads ] Add insert ads before contents
* [ Specification change ][ SNS Button ] Redesign

= 3.9.1 =
* [ bug fix ][ titile tag ]

= 3.9.0 =
* [ Add Function ][ title tag ] The ability to customize the title tag of homepage.

= 3.8.2 =
* [ bug fix ][ SNS Buttons ] ignore post id no work...

= 3.8.1 =
* [ bug fix ][ SNS Buttons ] ignore post id no work...

= 3.8.0 =
* [ Add Function ] Add TinyMCE Style Tags ( bootstrap buttons )
* [ Specification change ][ New post Widget ] add filter & action hooks.
* [ Specification change ][ PR Block Widget ] Change html markup.
* [ bug fix ] Corresponding to WordPress4.5.
* [ bug fix ][ Profile Widget ] markup bug fix.

= 3.7.10 =
* [ bug fix ] Archive Widget Link bug fix.

= 3.7.0 =
* [ Add function ] Insert Page list from ancestor.
* [ Specification change ][ Related post ] Change related logic.
* [ bug fix ][ Description ] Delete <br />
* [ bug fix ] php7

= 3.6.3 =
* [ bug fix( by twitter api Specification change) ] Stop display the tweet count.

= 3.6.2 =
* [ bug fix ] Child Page Index query

= 3.6.0 =
* [ Specification change ] Change home page title "site name → site name | side description"

= 3.5.3 =
* [ bug fix ] front page OGP

= 3.5.2 =
* [ bug fix ] SNS Button count SSL error
* [ bug fix ] Related post layout
* [ bug fix ] Function active setting

= 3.5.0 =
* [ Add function ] Add SNS Button count.

= 3.4.0 =
* [ Add function ] Add feedly button to Follow me section.

= 3.3.0 =
* [ Specification change ] Add classname "veu_" at Plugin output html.

= 3.2.0 =
* [ Specification change ] Change file name of css.

= 3.1.1 =
* [ bug fix ]

= 3.1.0 =
* [ Add functions ] Add new layout in the New posts widget.

= 3.0.0 =
* [ Add functions ] Add Contact Button Widget.
* [ Add functions ] Add Child Page List Widget.
* [ Specification change ] Change mark up of the widget.
* [ Specification change ] Change Setting Page UI.
* [ Specification change ] Change Related Post list logic.

= 2.3.0 =
* [ bug fix ] WordPress4.3 bug fix.
* [ Add functions ] Delete db options.

= 2.2.0 =
* [ Desgin tuning ] Change Child Page Index design.

= 2.1.0 =
* [ Add functions ] Insert CTA to contents.

= 2.0.0 =
* [ Add functions ] Insert Ads to contents.
* [ Add functions ] New PR Blocks Widget.
* [ Add functions ] Display contact infomation to contents of bottom.
* [ bug fix ] Translation leakage

= 1.0.0 =
* [ Add functions ] Insert ads to post contents.
* [ Add functions ] Heading of "Follow me box" can now be changed.
* [ Add functions ] Added the setting of share and OG page title.
* [ bug fix ] Translation leakage

= 0.1.6.6 =
* Profile Widget bug fix.

= 0.1.6.4 =
* Child Page Index page order bug fix.

= 0.1.6.3 =
* Profile Widget & Child Page Index bug fix.

= 0.1.6.0 =
* Add HTML auto sitemap.

= 0.1.5.3 =
* bug fix

= 0.1.5.0 =
* Add Archive widget.
* Add Category & Custom taxonomy widget.

= 0.1.4.0 =
* Add page contents widget.
* Add favicon setting.
* Add Eyecatch image auto insert.
* Add Transration.

= 0.1.3.1 =
* Some bug fix

= 0.1.2.0 =
* Add Japanese transration

= 0.1.1.1 =
* Setting page url bug fix

= 0.1.1.0 =
* Hellow world

== Upgrade Notice ==

Nothing.
