=== Map Contact Form 7 ===
Contributors: tanakayasuo
Donate link: 
Tags: cf7, contact form 7, google map
Requires at least: 5.7.2
Tested up to: 5.7.2
Stable tag: 4.3
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Addon of contact form 7 that adds place field.  Places are overlooked by shortcode( 'map-contact-form-7' ).

== Description ==

Map Contact Form 7 collects data with place and analyze the data with google map.
Preparation:
   (1) get google map api key and set the key in the settings of Map Contact Form 7. 
   (2) set language and region in the settings of Map Contact Form 7.
   (3) to collect data, put place field on contact form and add the contact form form-id to target forms in the settings of Map Contact Form 7.
   (4) add the Contact Form 7 form in Pages menu in wordpress. 
   (5) add Map Contact Form 7 shortcode( 'map-contact-form-7' ) with the form id of Contact Form 7. examples is the follwoing.
   	'[map-contact-form-7 form-id="the form id"]'

Collect data:
   (1) access the page with Contact Form 7 form and send the data.
   (2) the data is listed in 'Contact Forms List With Places' menu in admin page.

Analyze data:
   (1) access the page with Map Contact Form 7 shortcode.
   (2) if radio buttons are included in the Contact Form 7 form, you can focus in radio button items. In case of there multiple radio buttons, it is focused by and condition.

== Frequently Asked Questions ==

= A question that someone might have =

= What about foo bar? =

== Screenshots ==

1. screenshot-1.png

== Changelog ==

First release.

== Upgrade Notice ==

