<?php
/**
 * Plugin Name: Easy Subpage Widget
 * Plugin URI: http://www.sennza.com.au/
 * Description: Displays the parent page as a title and lists child pages.
 * Version: 0.1
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
	 * Display the option form
	 */
	public function form( $instance ) { }

	/**
	 * Update the widget's options
	 */
	public function update( $new_instance, $old_instance ) { }

	/**
	 * Output the widget
	 */
	public function widget( $args, $instance ) {
		global $post;
		extract( $args, EXTR_SKIP );

		$parent_title = get_the_title( $post->post_parent );

		$query = array(
			'title_li' => '',
			'echo' => false,
		);
		if ( $post->post_parent ) {
			$query['child_of'] = $post->post_parent;
		}
		else {
			$query['child_of'] = $post->ID;
		}
		$children = wp_list_pages( $query );

		echo $before_widget;
?>
		<h3><?php echo $parent_title;?></h3>

		<?php if ( $children ): ?>
			<ul>
				<?php echo $children; ?>
			</ul>
		<?php endif; ?>

<?php
		echo $after_widget;
	}
}
