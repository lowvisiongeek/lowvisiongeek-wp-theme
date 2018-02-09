<?php
// =============================================================================
// Start the engine
include_once( get_template_directory() . '/lib/init.php' );

// Child theme (do not remove)
define( 'CHILD_THEME_NAME', 'Low Vision Geek' );
define( 'CHILD_THEME_URL', 'http://lowvisiongeek.com/' );
define( 'CHILD_THEME_VERSION', '1.0' );

// Enqueue Fonts
add_action( 'wp_enqueue_scripts', 'lvg_enqueue_fonts' );
function lvg_enqueue_fonts() {
	wp_enqueue_style('google-fonts', '//fonts.googleapis.com/css?family=Lato:300,400,700',
	                 array(), CHILD_THEME_VERSION );
	wp_enqueue_style('font-awesome',
	                 '//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css',
	                 array(), '4.5.0');
}

// Add HTML5 markup structure
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );

// Add viewport meta tag for mobile browsers
add_theme_support( 'genesis-responsive-viewport' );

// Add support for custom background
add_theme_support( 'custom-background' );

// Add support for 3-column footer widgets
add_theme_support( 'genesis-footer-widgets', 3 );

// Add and/or rename navigation menus
add_theme_support('genesis-menus', array('primary'   => 'Primary Navigation Menu',
                                         'secondary' => 'Secondary Navigation Menu',
                                         'footer'    => 'Footer Navigation Menu'));

// =============================================================================
// Head section modifications

// Remove favicon
remove_action('wp_head', 'genesis_load_favicon');

// =============================================================================
// Footer customizations

// Change the footer credits
add_filter('genesis_footer_creds_text', 'lvg_footer_creds_text');
function lvg_footer_creds_text( $creds ) {
	$creds = '<strong itemprop="text">[footer_copyright before="Copyright " first="2015" 
	          after=" Low Vision Geek, All rights reserved"]</strong>';
	return $creds;
}

// Add content after the copyright
add_action('genesis_footer', 'lvg_custom_footer');
function lvg_custom_footer() {
	lvg_footer_menu();
	echo '<p class="host-referral">Hosted by <a href="' . home_url('/goto/hawkhost/') . '">HawkHost</a></p>';
}

// Create the footer navigation menu
function lvg_footer_menu() {

	// Do nothing if there is no menu assigned to the footer
	if ( ! has_nav_menu('footer') ) {return;}
	
	// Nav tag is added manually in order to include extra markup (NOTE: Alternative would
	// be to create a context with genesis_markup, and then use genesis_structural_wrap,
	// along with a filter added to genesis_attr_$context for the extra attributes)
	echo '<nav class="nav-footer" itemprop="hasPart" itemscope itemtype="http://schema.org/SiteNavigationElement">';
	wp_nav_menu(array('theme_location'  => 'footer',
	                  'container'       => 'div',
	                  'container_class' => 'wrap',
	                  'menu_class'      => 'menu genesis-nav-menu menu-footer',
	                  'depth'           => 1));
	echo '</nav>';
}

// =============================================================================
// Microdata Customizations

// Special webpage types
add_filter('genesis_attr_body','lvg_page_microdata');
function lvg_page_microdata($attributes) {
	if (is_page('contact')) {
		$attributes['itemtype'] = 'http://schema.org/ContactPage';
	} elseif (is_page('about')) {
		$attributes['itemtype'] = 'http://schema.org/AboutPage';
	} elseif (is_search()) {
		$attributes['itemtype'] = 'http://schema.org/SearchResultsPage';
	}
	return $attributes;
}

// Correct markup of main content (must be a WepPageElement)
add_filter('genesis_attr_content','lvg_content_microdata');
function lvg_content_microdata($attributes) {
	$attributes['itemprop']  = 'mainContentOfPage';
	$attributes['itemscope'] = 'itemscope';
	$attributes['itemtype']  = 'http://schema.org/WebPageElement';
	return $attributes;
}

// Nest elements and creative works inside the page
$contexts = array('entry','site-header','site-footer','nav-primary','nav-secondary',
                  'sidebar-primary','sidebar-secondary');
foreach ($contexts as $context) {
	add_filter('genesis_attr_' . $context,'lvg_nest_microdata');
}
function lvg_nest_microdata($attributes) {
	$attributes['itemprop']  = 'hasPart';
	return $attributes;
}
