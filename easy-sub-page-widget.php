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
			'classname'   => 'sz-easy-sub-page-widget',
			'description' => 'Displays the parent page as a title and lists child pages',
		);
		parent::__construct( 'sz-easy-sub-page-widget', __( 'Easy Sub Page Widget', 'sz-easy-sub-page-widget' ), $widget_ops );
	}

	/**
	 * Output the widget
	 */
	public function widget( $args, $instance ) {
		global $post;

		extract( $args );

		$defaults = array(
			'title_li' => '',
			'echo'     => false,
		);

		// Add a filter for developers. This takes the same args as http://codex.wordpress.org/Function_Reference/wp_list_pages
		$sub_page_args = apply_filters( 'sz_sub_pages', $defaults );

		if ( empty( $sub_page_args['child_of'] ) ) {
			if ( $post->post_parent ) {
				$sub_page_args['child_of'] = $post->post_parent;
			} else {
				$sub_page_args['child_of'] = $post->ID;
			}
		}

		// Use Parent ID from widget if specified
		if ( ! empty( $instance['parent_page_id'] ) && 0 != $instance['parent_page_id'] ) {
			$sub_page_args['child_of'] = absint( $instance['parent_page_id'] );
		}

		// Exclude any specified pages
		if ( isset( $instance['exclude'] ) && ! empty( $instance['exclude'] ) ) {
			$sub_page_args['exclude'] = $instance['exclude'];
		}

		$parent_title = get_the_title( $sub_page_args['child_of'] );
		$parent_link  = get_permalink( $sub_page_args['child_of'] );

		$children = trim( wp_list_pages( $sub_page_args ) );

		// Only show the title and sub pages if there are sub pages for that page
		if ( ! empty( $children ) ) {
			echo $args['before_widget']; ?>

			<h3 class="widget-title">
				<a href="<?php echo esc_url( $parent_link ); ?>" title="<?php echo esc_attr( $parent_title ); ?>">
					<?php echo esc_html( $parent_title ); ?>
				</a>
			</h3>

			<ul>
				<?php echo $children; ?>
			</ul>

		<?php }

		echo $args['after_widget'];

	}

	public function form( $instance ) {

		if ( isset( $instance['exclude'] ) && ! empty( $instance['exclude'] ) ) {
			$exclude = $instance['exclude'];
		} else {
			$exclude = '';
		}
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_name( 'parent_page_id' ) ); ?>"><?php _e( 'Parent page:', 'sz-easy-sub-page-widget' ); ?></label>

				<?php
				$sz_dropdown_args = array(
					'class'            => 'widefat',
					'show_option_none' => 'Current page (default)',
					'name'             => $this->get_field_name( 'parent_page_id' ),
					'id'               => $this->get_field_id( 'parent_page_id' ),
				);

				if ( isset( $instance['parent_page_id'] ) ) {
					$sz_dropdown_args['selected'] = absint( $instance['parent_page_id'] );
				}

				wp_dropdown_pages( $sz_dropdown_args );
				?>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'exclude' ) ); ?>"><?php esc_html_e( 'Exclude pages (optional):', 'sz-easy-sub-page-widget' ); ?>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'exclude' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'exclude' ) ); ?>" type="text" value="<?php echo esc_attr( $exclude ); ?>" /></label><br />
			<small>Page IDs, separated by commas.</small>
		</p>

		<?php
	}

	public function update( $new_instance, $old_instance ) {

		$new_instance['parent_page_id'] = absint( $new_instance['parent_page_id'] );
		$new_instance['exclude'] = str_replace( ' ', '', sanitize_text_field( $new_instance['exclude'] ) );

		return $new_instance;
	}

}
