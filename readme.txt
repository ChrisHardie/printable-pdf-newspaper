=== Printable PDF Newspaper ===
Contributors: chrishardie
Donate link: https://chrishardie.com/refer/donate
Tags: print,pdf,newspaper,newsletter,journalism,news
Stable tag: 1.1.2
Requires at least: 5.2.2
Tested up to: 6.6
Requires PHP: 5.6

Generates a printable PDF newspaper from post content.

== Description ==

Generates a printable PDF newspaper from post content. Great for distributing your articles and posts in a print format for offline audiences.

To use, from the admin area of your site, select what content you want to include (supports posts, pages or custom post types and you can filter by tag or category), how many items, whether to truncate the body content (or excerpt) at a certain character length, how many columns to format with, and which fields to display. You can even upload a "masthead" header image for a more authentic newspaper feel, and the plugin can automatically generate QR codes to allow link scanning with a mobile phone camera.

Download the resulting PDF or save it to your media library for easy public linking and sharing.

This plugin does not require any remote PDF generation services or subscriptions to create the printable PDF file, everything is done within the plugin itself.

Credit to [TCPDF](https://tcpdf.org) for the PDF generation library and [Freepik](https://www.flaticon.com/authors/freepik) for the plugin icon.

= Contributing =

Feature suggestions, bug reports and pull requests on [GitHub](https://github.com/ChrisHardie/printable-pdf-newspaper) are welcome.

== Installation ==

Printable PDF Newspaper is most easily installed via the Plugins tab in your admin dashboard.

== Frequently Asked Questions ==

= How can I customize the PDF content styles? =

You can customize the PDF newspaper layout and styles using limited CSS definitions, in two different ways:

1. Enter your custom style definitions in the "Custom CSS" input field when generating the PDF.
1. In your theme, filter the output of `ppn_pdf_template_css_file_path` to specify the full filesystem path to a file containing CSS styles.

When specifying custom styles, do not enclose them in a `<style>` tag or any other HTML. Invalid CSS may break the PDF generation process.

Here are the CSS classes you may wish to adjust:

* *ppn-article-title*: Headlines / post titles
* *ppn-article-wrapper*: Wrapper around the loop of all included articles
* *ppn-author*: Author byline and display name (if included)
* *ppn-date*: Article date (if included)
* *ppn-content* and *ppn-excerpt*: article body content
* *ppn-permalink-text*: the "Continue Reading" permalink introductory text
* *ppn-permalink-qr-code-image*: image class for the QR Code (if included)
* *ppn-article-bottom-border*: horizontal line dividing articles

You can view the default style definitions in the plugin file `assets/admin/css/pdf-template-styles.css` or [in Trac](https://plugins.trac.wordpress.org/browser/printable-pdf-newspaper/trunk/assets/admin/css/pdf-template-styles.css).

Note that TCPDF only supports a limited subset of the full CSS specification. Also note that any fonts referenced must be available in the TCPDF library used to generate the PDF. You can [view the TCPDF core font list](https://tcpdf.org/docs/fonts/).
There's also an experimental filter, `ppn_font_file_paths`, that allows you to add to or change the array of TTF font file paths being loaded.

Currently the header image size/position and subheading styles are not easily customizable, but will be in the future.

= What filters and hooks are available? =

These filters are available to further customize the plugin functionality:

* `ppn_post_query_args`: override the array of arguments to WP_Query to control which posts are included
* `ppn_pdf_configuration`: override the array of PDF configuration values specified by the admin user
* `ppn_pdf_template_css_file_path`: override the full filesystem path to a CSS file for PDF content styling
* `ppn_font_file_paths`: override the array of filesystem paths to TTF font files to include in the PDF

For example, to customize the number of posts included in the PDF, add something like this to your theme:

`add_filter( 'ppn_pdf_configuration', function( $config ) { $config['number'] = 2; return $config; }, 10, 1 );`

= What features will be added in the future? =

* Allow saving of PDF configuration for easy re-use in future runs
* More customizable header size and layout
* Generate QR Codes within the plugin instead of Google Chart API
* Ability to auto-generate the PDF on a schedule
* Better controls for limiting number of pages generated and column breaks.

= Why would anyone print anything? =

Some people still encounter things and ideas through engagement with objects in the physical world. If you are looking to attract readers to your WordPress-powered writing, distributing a printed "teaser" version might just help.

== Screenshots ==

1. Example generated PDF file.
2. Admin PDF configuration screen.

== Changelog ==

= 1.1.2 =

* Enhancement: initial support for RTL text
* Maintenance: Tested against WordPress 5.9

= 1.1.1 =

* Tested against WordPress 5.7
* Fix: display more useful error message when TCPDF cannot retrieve post images

= 1.1.0 =

* Feature: users can specify custom CSS to control PDF appearance
* Feature: add WordPress filters so developers can customize functionality
* Maintenance: upgrade Select2 Javascript library
* Maintenance: improve internationalization in Javascript UI elements
* Maintenance: other minor improvements for code standards

= 1.0.2 =

* Maintenance: Tested against WordPress 5.4
* Maintenance: Updated TCPDF library to latest release

= 1.0.1 =

* Tested against WordPress 5.3
* Fix: address minor PHP index warning with empty check

= 1.0.0 =

* Initial release.

== Upgrade Notice ==

= 1.1.0 =

Introduces CSS-based and filter-based PDF appearance customization along with better support for internationalization.
