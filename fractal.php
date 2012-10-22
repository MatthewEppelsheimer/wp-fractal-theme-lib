<?php
/*
Plugin Name: Fractal
Version: 0.1
Plugin URI: http://rocketlift.com/software/fractal
Description: Adds a custom post type called 'people'.
Author: Matthew Eppelsheimer
Author URI: http://rocketlift.com/
License: GPL 2
*/

// activation

function wp_fractal_activate() {
	/*	Stuff	
		Such as detecting themes that support this and highlighting them maybe
	*/
}

register_activation_hook( __FILE__, 'wp_fractal_activate' );


// deactivation

function wp_fractal_deactivate() {
	/*	Stuff	
		Such as detecting themes that require this plugin's support, and possibly disable them
	*/
}

register_deactivation_hook( __FILE__, 'wp_fractal_deactivate' );

/*
 *	Core Fractal Setup
 */
 
/*
 *	fractal_template_setup()
 *	@description	Prepares global the Fractal templating engine before loading templates
 */

function fractal_template_setup() {
	global $fractal;
	$fractal = array();
	$fractal['crawl'] = false;
}

add_action( 'template_redirect', 'fractal_template_setup', 1 );

/*
 *	fractal_block( $block_name, $block_function )
 *
 *	@description	Defines a template block and adds it to the $fractal chain
 *
 *	@param	$block	The unique name of the block as a string
 *	@param	$block_closure	A function that generates a string of html for output
 */

function fractal_block( $block, $block_closure ) {
	global $fractal;
	
	$fractal[$block]['closures'][] = $block_closure;
	if ( $fractal['crawl'] )
		fractal_crawl( $block );	
}

/*
 *	fractal( $fractal_parent_file )
 *	@description	Template tag called at the end of each fractal template file.
 *					Handles inheritance starts the chain collapse when at the base
 *
 *	@param	$fractal_parent_file	The parent file this calling file inherits from
 */

function fractal( /* $fractal_parent_file */ ) {
	global $fractal;
	
	// if there is a parent file

	/*	STUFF HERE	*/	
	
	// if there is not a parent file

	// switch to crawl mode;
	$fractal['crawl'] = true;

	// Start the fractal chain collapse and echo results
	echo fractal_crawl( 'base' );
}

/*
 *	fractal_crawl()
 *	@description	Crawl up the assembled fractal chain to assemble output and echo it.
 *
 *	@param	$block	The block to stitch
 */

function fractal_crawl( $block ) {
	global $fractal;
	$fractal['working_block'] = $block;

	/*echo "\n<p>Called fractal_crawl( '$block' ). Entering with:</p>\n";
	print_r( $fractal );
	echo "\n";*/
	
	while ( count( $fractal[$block]['closures'] ) > 0 ) {
		
		$closure = array_pop( $fractal[$block]['closures'] );
		if ( is_callable( $closure ) ) {
			$fractal[$block]['html'] = call_user_func( $closure );
		}
		/*echo "\n<p>Through the while loop within fractal_crawl(). fractal is:</p>\n";
		print_r( $fractal );
		echo "\n";*/
	
	}	
	return $fractal[$block]['html'];
}
 

/*
 *	Shortcode Setup
 *
 *	@todo: Extend support for declaring blocks in post content that are filtered like normal shortcodes on the_content();.
 */

/*
 *	fractal_template() is a TO BE IMPLEMENTED function that handles shortcode support
 *	For now it does nothing, but exists for forward compatibility.
 */
 
function fractal_template() {
	
	return;
}

/*
 *	fractal_parent()
 */

function fractal_parent() {
	global $fractal;
	
	if ( ! isset( $fractal['working_block'] ) )
		return "<p>fractal_parent returned false</p>";
	$working_block = $fractal['working_block'];
	if ( ! isset( $fractal[$working_block]['html'] ) )
		return "<p>fractal_parent returned false</p>";

	echo "<p>Working block is " . $fractal['working_block'] . ". The html is " . $fractal[$working_block]['html'] . ".</p>";
	
	return $fractal[$working_block]['html'];
}