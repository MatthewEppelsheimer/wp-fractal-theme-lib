<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Furrow Pump
 * @since Furrow Pump 1.0
 */

if ( ! defined( 'FRACTAL_LEAF' ) )
	define( 'FRACTAL_LEAF', __FILE__ );

class Fractal {

	function __construct() {
		$this->init();
		?><!DOCTYPE html>
		<!--[if lte IE 7]>     <html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
		<!--[if IE 8]>         <html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
		<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
			<head>
				<?php $this->html_head(); ?>
			</head>

			<body <?php $this->body_class(); ?>>
				<?php $this->body_inside_beginning(); ?>
				<?php $this->body(); ?>
				<?php $this->body_inside_ending(); ?>
			</body>
		</html>
		
	<?php
	}
	
	// Utiility for children to instantiate object vars.
	function init() {
		$this->theme_prefix = 'fractal_theme';
	}
	
	function body_class() {
		body_class();
	}
	
	function html_head() {?>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width" />
		<title><?php
			/*
			 * Print the <title> tag based on what is being viewed.
			 */
			global $page, $paged;
		
			// Add the queried object title (if there is one)
			wp_title( '|', true, 'right' );
		
			// Add the site name.
			bloginfo( 'name' );
		
			// Add the blog description for the home/front page.
			$site_description = get_bloginfo( 'description', 'display' );
			if ( $site_description && ( is_home() || is_front_page() ) )
				echo " | $site_description ";
		
			// Add a page number if necessary:
			
			if ( $paged >= 2 || $page >= 2 )
				echo ' | ' . sprintf( __( 'Page %s ', 'fractal_theme' ), max( $paged, $page ) );
		
			?></title>
		<link rel="profile" href="http://gmpg.org/xfn/11" />
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		<!--[if lt IE 9]>
		<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
		<![endif]-->
		
		<?php wp_head();
	}
	
	function body() {?>
		<!--[if lt IE 7]>
			<p class="chromeframe">You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
		<![endif]-->
		<div id="page" class="hfeed site">
			<?php do_action( 'before' ); ?>
			<header id="masthead" class="site-header" role="banner">
				<hgroup>
					<p class="site-title"><a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
					<p class="site-description"><?php bloginfo( 'description' ); ?></p>
				</hgroup>
	
				<nav role="navigation" class="site-navigation main-navigation">
					<p class="assistive-text"><?php _e( 'Menu', 'fractal_theme' ); ?></p>
					<div class="assistive-text skip-link"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'fractal_theme' ); ?>"><?php _e( 'Skip to content', 'fractal_theme' ); ?></a></div>
	
					<?php
						wp_nav_menu( 
							array( 
								'theme_location' => 'primary',
								'walker' => new RLI_Nav_Walker()
							)
						);
					?>
				</nav><!-- .site-navigation .main-navigation -->
			</header><!-- #masthead .site-header -->
	
			<div id="main"<?php $this->main_classes(); ?>>
				<?php $this->main(); ?>
			</div><!-- #main -->
	
		</div><!-- #page .hfeed .site -->

		<footer class="site-footer" role="contentinfo">
			<?php $this->site_footer(); ?>
		</footer><!-- #colophon .site-footer -->
	
		<?php wp_footer();
	}
	
	function main_classes() {
		// If a page does not have a sidebar redefine this funciton to {}
		echo ' class="has-sidebar"';
	}
	
	function primary_classes() { echo 'site-content has-sidebar'; }
	
	function sub_nav_menu() { 
		//optional block used by children
	}
	
	function sub_nav_breadcrumb() {
		if (function_exists('HAG_Breadcrumbs')) { 
			HAG_Breadcrumbs(
				array(
					'wrapper_element' => 'nav',
					'wrapper_id' => 'sub-nav-breadcrumb',
					'last_class' => 'crumb_current'
				)
			); 
		}
	}
	
	function secondary_classes() {
		echo 'widget-area';
	}
	
	function sidebar() {
		do_action( 'before_sidebar' );
		if ( ! dynamic_sidebar( 'sidebar-1' ) ) : ?>

			<aside id="search" class="widget widget_search">
				<?php get_search_form(); ?>
			</aside>

			<aside id="archives" class="widget">
				<h1 class="widget-title"><?php _e( 'Archives', 'fractal_theme' ); ?></h1>
				<ul>
					<?php wp_get_archives( array( 'type' => 'monthly' ) ); ?>
				</ul>
			</aside>

			<aside id="meta" class="widget">
				<h1 class="widget-title"><?php _e( 'Meta', 'fractal_theme' ); ?></h1>
				<ul>
					<?php wp_register(); ?>
					<li><?php wp_loginout(); ?></li>
					<?php wp_meta(); ?>
				</ul>
			</aside>

		<?php endif; // end sidebar widget area
	}
	
	function main() {?>
		<section id="primary" class="<?php $this->primary_classes(); ?>" role="main">
			<?php 
			$this->sub_nav_menu();
			$this->sub_nav_breadcrumb();
			$this->content();
			$this->downloads_menu();
			$this->post_child_info();
			?>
		</section><!-- #primary .site-content -->
	
		<section id="secondary" class="<?php $this->secondary_classes(); ?>" role="complementary">
			<?php $this->sidebar(); ?>
		</section><!-- #secondary .widget-area -->
	<?php
	}
	
	function content() {
		$this->loop();
	}
	
	function loop( $query = '', $start = '', $end = '' ) {
		global $wp_query;

		if ( '' == $query )
			$query = $wp_query;
			
		if ( $query->have_posts() ) :
			$this->loop_have_posts( $query, $start, $end );
		endif;
	}
	
	function loop_have_posts( $query, $start = '', $end = '' ) {
		if ( '' != $start ) echo $start;
		$count = 1;
		while ( $query->have_posts() ) : 
			$query->the_post();
			$this->loop_template( $count );
			$count++;
		endwhile;		
		if ( '' != $end ) echo $end;
	}
	
	function loop_template( $count ) {
		get_template_part( 'content', 'page' );
		comments_template( '', true );		
	}
	
	function downloads_menu() {}
	function post_child_info() {}
	
	function site_footer() {?>
		<div class="logo">
			<h2>Furrow Pump</h2>
		</div>
		<div class="site-digest">
			<div class="site-nav">
				<h4>Site</h4>
				<nav>
					<a>Home</a>
					<a>Services</a>
					<a>Products</a>
					<a>News</a>
					<a>Resources</a>
					<a>Contact</a>
				</nav>
			</div>
			<div class="address">
				<h4>Address</h4>
				<dt>Location</dt><dd>8525 SW St. Helens Dr.</dd>
				<dt>Mail</dt><dd>P.O. Box 1849</dd>
				<dt>City/State</dt><dd>Wilsonville, OR 97070</dd>
			</div>
			<div class="contact-info">
				<h4>Contact</h4>
				<dt>Telephone</dt><dd>1 800 937 3666</dd>
				<dt>Facsimile</dt><dd>1 800 377 9960</dd>
				<dt>Email</dt><dd>sales@furrowpump.com</dd>
			</div>
		</div>
		<div class="site-info">
			<?php /* do_action( 'fractal_theme_credits' ); ?>
			<a href="http://wordpress.org/" title="<?php esc_attr_e( 'A Semantic Personal Publishing Platform', 'fractal_theme' ); ?>" rel="generator"><?php printf( __( 'Proudly powered by %s', 'fractal_theme' ), 'WordPress' ); ?></a>
			<span class="sep"> | </span>
			<?php printf( __( 'Theme: %1$s by %2$s.', 'fractal_theme' ), 'fractal_theme', '<a href="http://automattic.com/" rel="designer">Automattic</a>' ); */ ?>
		</div><!-- .site-info -->
	<?php
	}
	
	// Tool Box Functions
	
	function no_results() {?>
		<article id="post-0" class="post no-results not-found">
			<header class="entry-header">
				<h1 class="entry-title"><?php _e( 'Nothing Found', 'rocket_lift_parent_theme' ); ?></h1>
			</header><!-- .entry-header -->
	
			<div class="entry-content">
				<?php if ( is_home() ) : ?>
	
					<p><?php printf( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'rocket_lift_parent_theme' ), admin_url( 'post-new.php' ) ); ?></p>
	
				<?php elseif ( is_search() ) : ?>
	
					<p><?php _e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'rocket_lift_parent_theme' ); ?></p>
					<?php get_search_form(); ?>
	
				<?php else : ?>
	
					<p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'rocket_lift_parent_theme' ); ?></p>
					<?php get_search_form(); ?>
	
				<?php endif; ?>
			</div><!-- .entry-content -->
		</article><!-- #post-0 .post .no-results .not-found -->
	<?php
	}
	
	function comments() {
		/*
		 * If the current post is protected by a password and
		 * the visitor has not yet entered the password we will
		 * return early without loading the comments.
		 */
		if ( post_password_required() )
			return;
		?>
	
			<div id="comments" class="comments-area">
	
			<?php // You can start editing here -- including this comment! ?>
	
			<?php if ( have_comments() ) : ?>
				<h2 class="comments-title">
					<?php
						printf( _n( 'One thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', get_comments_number(), 'rocket_lift_parent_theme' ),
							number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );
					?>
				</h2>
	
				<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
				<nav role="navigation" id="comment-nav-above" class="site-navigation comment-navigation">
					<h1 class="assistive-text"><?php _e( 'Comment navigation', 'rocket_lift_parent_theme' ); ?></h1>
					<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'rocket_lift_parent_theme' ) ); ?></div>
					<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'rocket_lift_parent_theme' ) ); ?></div>
				</nav><!-- #comment-nav-before .site-navigation .comment-navigation -->
				<?php endif; // check for comment navigation ?>
	
				<ol class="commentlist">
					<?php
						/* Loop through and list the comments. Tell wp_list_comments()
						 * to use rocket_lift_parent_theme_comment() to format the comments.
						 * If you want to overload this in a child theme then you can
						 * define rocket_lift_parent_theme_comment() and that will be used instead.
						 * See rocket_lift_parent_theme_comment() in inc/template-tags.php for more.
						 */
						wp_list_comments( array( 'callback' => 'rocket_lift_parent_theme_comment' ) );
					?>
				</ol><!-- .commentlist -->
	
				<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
				<nav role="navigation" id="comment-nav-below" class="site-navigation comment-navigation">
					<h1 class="assistive-text"><?php _e( 'Comment navigation', 'rocket_lift_parent_theme' ); ?></h1>
					<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'rocket_lift_parent_theme' ) ); ?></div>
					<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'rocket_lift_parent_theme' ) ); ?></div>
				</nav><!-- #comment-nav-below .site-navigation .comment-navigation -->
				<?php endif; // check for comment navigation ?>
	
			<?php endif; // have_comments() ?>
	
			<?php
				// If comments are closed and there are comments, let's leave a little note, shall we?
				if ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
			?>
				<p class="nocomments"><?php _e( 'Comments are closed.', 'rocket_lift_parent_theme' ); ?></p>
			<?php endif; ?>
	
			<?php comment_form(); ?>
	
		</div><!-- #comments .comments-area -->
	<?php
	}

	function download_list_template( $array ) {
		global $post;
		$count = 0;
		$output = '';
		foreach ( $array as $post ) {
			setup_postdata( $post );			
			$output .= "<li class='wp-pubarch-download " . even_or_odd( $count ) . "'><a href='" . get_permalink( $post->ID ) . "'>" . $post->post_title . "</a></li>";
			$count++;
		}
		wp_reset_postdata();
		return $output;
	}
	
	function entry_featured_image() {?>
		<div class="entry-featured-image">
			<?php
				$thumbnail = get_the_post_thumbnail( null, 'product-thumbnail' );
			
				if ( '' != $thumbnail ) {
					echo $thumbnail;
				} else {
					echo "<img src='http://placehold.it/333x250' />";
				}
			?>
		</div>
	<?php 
	}
	
	/**
	 * jQuery document ready mechanism
	 *
	 * To use this in a child class, do two things:
	 *
	 *     1. Set use_jquery_document_ready = true inside of init().
	 *     2. Override inside_jquery_document_ready(), which will be automatically wrapped
	 *        inside jQuery(document).ready(function($){}.
	**/
	function body_inside_ending() {
		$this->jquery_document_ready();
	}

	function jquery_document_ready() {?>
		<script type="text/javascript">
			jQuery(document).ready(function($){
				<?php $this->inside_jquery_document_ready(); ?>
			});
		</script>
	<?php
	}
	
	// For child templates to echo javascript within the jQuery(document).ready(function($){} wrapper.
	function inside_jquery_document_ready() { ?>
		$('html').removeClass('no-js');
	<?php
	}
	
}

if ( FRACTAL_LEAF === __FILE__ )
	new Fractal();

