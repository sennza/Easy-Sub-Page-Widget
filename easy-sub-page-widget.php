<?php
/**
 * Plugin Name: Easy Sub Page Widget
 * Plugin URI: http://www.sennza.com.au/
 * Description: Displays the parent page as a title and lists sub pages (child pages). If there are no sub pages then
 * don't display anything.
 * Version: 1.0
 * Author: Sennza Pty Ltd, Bronson Quick, Ryan McCue, Lachlan MacPherson
 * Author URI: http://www.sennza.com.au/
 */

add_action( 'widgets_init', 'sz_easysubpage_register' );

function sz_easysubpage_register() {
	return register_widget( 'Sennza_Easy_Sub_Page_Widget' );
}

class Sennza_Easy_Sub_Page_Widget extends WP_Widget {
	/**
	 * Constructor
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'sz-easy-sub-page-widget',
			'description' => 'Displays the parent page as a title and lists child pages',
		);
		$this->WP_Widget( 'sz-easy-sub-page-widget', 'Easy Sub Page Widget', $widget_ops );
	}

	/**
	 * Output the widget
	 */
	public function widget( $args, $instance ) {
		global $post;

		$defaults = array(
			'title_li' => '',
			'echo' => false,
		);

		// Add a filter for developers. This takes the same args as http://codex.wordpress.org/Function_Reference/wp_list_pages
		$args = apply_filters( 'sz_sub_pages', $args );

		$args = wp_parse_args( $args, $defaults );

		extract( $args );

		if ( empty( $args['child_of'] ) ) {
			if ( $post->post_parent ) {
				$args['child_of'] = $post->post_parent;
			} else {
				$args['child_of'] = $post->ID;
			}
		}

		$parent_title = get_the_title( $args['child_of'] );
		$parent_link = get_permalink( $args['child_of'] );

		$children = trim( wp_list_pages( $args ) );

		// Only show the title and sub pages if there are sub pages for that page
		if ( ! empty( $children ) ) {
?>

		<h3><a href="<?php echo $parent_link; ?>" title="<?php echo $parent_title;?>"><?php echo $parent_title;?></a></h3>
		<ul>
			<?php echo $children; ?>
		</ul>

<?php
		}
	}
}