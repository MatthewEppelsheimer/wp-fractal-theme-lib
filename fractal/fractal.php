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

/*
 *	Fractal System Template Tags
 */
 
/*
 *	fractal_template() 
 *
 *	@description		Template tag required at the beginning of fractal template files. 
 *						This primarily exists for forward compatibility. In the future it
 *						shall enable other delightful things, such as shortcode support.
 */
 
function fractal_template() {

	if ( ! isset( $fractal['initialized'] ) )
		fractal_system_setup();
	
	return;
}

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

	// For debugging
	do_action( 'fractal_block_pre', $block );
	
	// Decide whether we need to store and use this version of the block.
	if ( isset( $fractal[$block]['needs_parent'] ) ) {
		if ( ! $fractal[$block]['needs_parent'] ) 
			// This block has already been declared without calling fractal_parent, so we are done with it. Do nothing.
			return;
	}
	// This is either the first time we've encountered this block, or we're here because its parent has been called.
	// Set 'needs_parent' to false before evaluating the closure.
	$fractal[$block]['needs_parent'] = false;

	// For extensibility and debugging
	do_action( 'fractal_block_begin', $block );

	// Store the closure
	$fractal[$block]['closures'][] = $block_closure;

	// If we are collapsing, call fractal_collapse and return its output
	if ( $fractal['collapse'] ) {
		$output = fractal_collapse( $block );	
		return $output;
	}

	// Set this to the working block
	$fractal['working_block'] = $block;

	// Call the closure (but do not destroy it or do anything with the returned results). 
	// Here we are giving fractal_parent an opportunity to set 'needs_parent' back to true.
	// We are also looking for nested fractal_block calls, to build their chains.

	// @todo ONLY DO THIS IF WE AREN'T COLLAPSING.

	if ( is_callable( $block_closure ) );
		$html = $block_closure();
	
	// For extensibility and debugging
	do_action( 'fractal_block_end', $block );
} 

/*
 *	fractal_parent()
 *
 *	Called for one of two reasons:
 *		1) we're looking for parents before collapsing
 *		2) we're actually collapsing, and need to return parent code.
 */

function fractal_parent() {
	global $fractal;
	
	// Get and store working block for convenience
	$working_block = $fractal['working_block'];

	// Are we doing setup or collapsing?
	if ( $fractal['collapse'] ) {
		// We're actually collapsing
		if ( ! isset( $fractal[$working_block]['html'] ) )
		echo $fractal[$working_block]['html'];
	} else { 
		// We are doing setup. We are here to detect whether the working block
		// incorporates its parent. We now know that it does.
		$fractal[$working_block]['needs_parent'] = true;
	}
}

/*
 *	fractal()
 *	@description	Template tag called at the end of each fractal template file.
 *					Handles inheritance and starts the chain collapse after the base.
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
	
	// switch to collapse mode;
	$fractal['collapse'] = true;

	// Start the fractal chain collapse and echo results
	echo fractal_collapse( 'base' );
	
	// the Fractal process is done.
	do_action( 'fractal_after' );
}

/*
 *	Core Fractal System Internals
 */

/*
 *	fractal_system_setup()
 *	@description		Internal function to prepare globals for the templating 
 *						engine before loading templates
 *
 *	@use				Loaded on the 'template_redirect' hook.
 */

function fractal_system_setup() {
	global $fractal;
	$fractal = array();
	$fractal['collapse'] = false;
	$fractal['initialized'] = true;
	return true;
}

add_action( 'template_redirect', 'fractal_template_setup', 1 );

/*
 *	fractal_collapse()
 *	@description	Internal function to assemble fractal chain to output and echo it.
 *
 *	@param	$block	The block to assemble
 */

function fractal_collapse( $block ) {
	global $fractal;
	do_action( 'fractal_collapse_begin', $block );

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

	do_action( 'fractal_collapse_end', $block );
	echo $fractal[$block]['html'];
}
 
/*
 *	Fractal System Debugging
 */

/**
 *	Enable fractal debugging (or disable)
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
