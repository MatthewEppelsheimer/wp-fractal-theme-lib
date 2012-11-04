<?php
/*
Plugin Name: Fractal
Version: 0.1
Plugin URI: http://rocketlift.com/software/fractal
Description: A templating engine for WordPress that supports template heirarchy and infinite nesting
Author: Matthew Eppelsheimer
Author URI: http://rocketlift.com/
License: GPL 2
*/

// deactivation

/* @todo
*/

function fractal_deactivate() {
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

	if ( isset( $fractal[$block]['needs_parent'] ) ) {
		// If we're done with this block (it's already been declared without calling fractal_parent), do nothing.
		if ( ! $fractal[$block]['needs_parent'] ) 
			return;
	}
	// This is either the first time we've encountered this block, or we're here because its parent has been called.
	// Set 'needs_parent' to false before evaluating the closure.
	$fractal[$block]['needs_parent'] = false;

	do_action( 'fractal_block_begin', $block );

	// Store the closure
	$fractal[$block]['closures'][] = $block_closure;

	// If we are crawling, call fractal_crawl and return its output
	if ( $fractal['crawl'] ) {
		$output = fractal_crawl( $block );	
		return $output;
	}

	// Set this to the working block
	$fractal['working_block'] = $block;

	// Call the closure (but do not destroy it or do anything with the returned results). 
	// Here we are giving fractal_parent an opportunity to set 'needs_parent' back to true.
	if ( is_callable( $block_closure ) );
		$trash_bin = $block_closure();
	
	do_action( 'fractal_block_end', $block );
}

/*
 *	fractal_parent()
 *
 *	Called for one of two reasons:
 *		1) we're looking for parents before crawling
 *		2) we're actually crawling, and need to return parent code.
 */

function fractal_parent() {
	global $fractal;
	
	// Store working block for convenience
	$working_block = $fractal['working_block'];

	// Are we doing setup or crawling?
	if ( isset( $fractal['crawling'] ) ) {
		// We're actually crawling
		if ( ! isset( $fractal[$working_block]['html'] ) )
		echo $fractal[$working_block]['html'];
	} else { 
		// We are doing setup. We are here to detect whether the working block
		// incorporates its parent. We now know that it does.
		$fractal[$working_block]['needs_parent'] = true;
	}
}

/*
 *	fractal( $fractal_parent )
 *	@description	Template tag called at the end of each fractal template file.
 *					Handles inheritance starts the chain collapse when at the base
 *
 *	@param	$fractal_parent	The parent file this calling file inherits from
 */

function fractal( $fractal_parent = null ) {
	global $fractal;
	
	// if there is a parent file
	if ( isset( $fractal_parent ) ) {
		locate_template( "/fractal/fractal.$fractal_parent.php", true );
		return true;
	}
	
	// switch to crawl mode;
	$fractal['crawl'] = true;

	// Start the fractal chain collapse and echo results
	echo fractal_crawl( 'base' );
	
	// the Fractal process is done.
	do_action( 'fractal_after' );
}

/*
 *	fractal_crawl()
 *	@description	Crawl up the assembled fractal chain to assemble output and echo it.
 *
 *	@param	$block	The block to assemble
 */

function fractal_crawl( $block ) {
	global $fractal;
	do_action( 'fractal_crawl_begin', $block );

	$fractal['working_block'] = $block;

	// Assemble output by calling closures in the block's chain
	while ( count( $fractal[$block]['closures'] ) > 0 ) {
		
		$closure = array_pop( $fractal[$block]['closures'] );
		if ( is_callable( $closure ) ) {
			ob_start();
			call_user_func( $closure );
			$output = ob_get_contents();
			ob_end_clean();
			$fractal[$block]['html'] = $output;
		}
	}	

	do_action( 'fractal_crawl_end', $block );
	echo $fractal[$block]['html'];
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

/**
 *	Enable fractal debugging
 */

function fractal_debug( $bool = true ) {
	global $fractal;
	$fractal['debug'] = $bool;
}

/**
 *	Conditional to check if fractal debugging is enabled
 */

function fractal_is_debug() {
	global $fractal;
	if ( isset( $fractal['debug'] ) )
		return $fractal['debug'];
	return false;
}

/**
 *	Generate debugging info capture point
 */

function fractal_debug_capture( $calling_function = null, $context = null , $block = null ) {
	global $fractal;
	// Filter hook allows to change the condition
	if ( apply_filters( 'fractal_debug_report_conditional', fractal_is_debug() ) ) {
		do_action( "fractal_debug_capture_$calling_function", $calling_function, $context, $block );
	} else {
		return;
	}
}
