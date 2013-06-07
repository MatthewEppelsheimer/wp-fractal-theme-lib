<?php
/**
 * Fractal Tools
 *
 * Utility methods.
 *
 * @package Fractal Framework
 * @since Fractal Framework 1.0
 */

if ( ! defined( 'FRACTAL_LEAF' ) )
	define( 'FRACTAL_LEAF', __FILE__ );

class FractalTools {

	/**
	 * Utility to get get posts of a given post type
	 *
	 *	@param		str		$post_type	(required) post type to query for
	 *	@param		array	$query_args	(optional) Array of arguments formulated
	 *						to pass to the WP_Query class constructor
	 *
	 * 	@uses				FractalTools::get_posts()
	 *
	 *	@returns			A WP_Query object with query results from the custom post type
	 *
	 *	@since		2013/06/06
	 */
	function get_posts_of_type( $post_type, $query_args = array()  ) {
		$query_args['post_type'] = $post_type;
		$results = $this->get_posts( $query_args );
		return $results;
	}

	/**
	 * Utility to perform custom query
	 *
	 *	@param		array	$query_args	(optional) Array of arguments formulated
	 *						to pass to the WP_Query class constructor
	 *
	 *	@returns			A WP_Query object 
	 *
	 *	@since		2013/06/06
	 */
	function get_posts( $query_args = array() ) {
		$defaults = array(
			'posts_per_page' => -1,
			'order' => 'ASC',
			'orderby' => 'menu_order'
		);
		$query_args = wp_parse_args( $query_args, $defaults );

		$results = new WP_Query( $query_args );
		return $results;
	}

	/*
	 *	Utility to display custom query loop. Takes query arguments and 
	 *	performs the query, manages a custom loop, and echoes html
	 *
	 *	@param	$args				(optional) An array of $args formatted for WP_Query to accept
	 *	@param	$template_callback	(required) A template callback to render output inside the loop.
	 *	
	 *	@return						Loop HTML output if the query has results; false if not
	 *
	 *	@since		2013/06/06
	 */
	
	function custom_query_loop( $args, $template_callback ) {
		if ( empty( $template_callback ) )
			return '<pre>$template_callback not set when calling ' . __FUNCTION__ . ' in ' . __FILE__ . ' at ' . __LINE__ . '</pre>'; 

		global $post;
	
		$query = FractalTools::get_posts( $args );
	
		if ( $query->have_posts() ) {
			$output = "";
			while ( $query->have_posts() ) {
				$query->the_post();
	
				/*	BUILD HTML	*/
				$output .= $template_callback( $post );
	
			}
			wp_reset_query();
			return $output;
		}
		wp_reset_query();
		return false;
	}
}

if ( FRACTAL_LEAF === __FILE__ )
	new FractalTools();
