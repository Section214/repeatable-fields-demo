<?php
/**
 * Plugin Name:  Repeatable Fields Demo
 * Description:  Demo plugin for creating repeatable fields in meta boxes
 * Version:      1.0.0
 * Author:       Daniel J Griffiths
 * Author URI:   https://section214.com
 * Text Domain:  repeatable-fields-demo
 *
 * @package      RepeatableFieldsDemo
 * @author       Daniel J Griffiths <dgriffiths@section214.com>
 * @copyright    Copyright (c) 2017, Daniel J Griffiths
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Register new meta box
 *
 * @return    void
 */
function repeatable_demo_add_meta_box() {
	add_meta_box( 'repeatable_demo', 'Repeatable Demo', 'repeatable_demo_render_meta_box', 'post', 'normal', 'default' );
}
add_action( 'add_meta_boxes', 'repeatable_demo_add_meta_box' );


/**
 * Render the meta box
 *
 * @global    object $post The WordPress object for this post
 * @return    void
 */
function repeatable_demo_render_meta_box() {
	global $post;

	$demo_data = get_post_meta( $post->ID, '_repeatable_demo_data', true );
	?>
	<table class="widefat repeatable-demo-repeatable-table" width="100%" cellpadding="0" cellspacing="0">
		<thead>
			<th>Field 1</th>
			<th>Field 2</th>
			<th style="width: 20px"></th>
		</thead>
		<tbody>
			<?php
			if( ! empty( $demo_data ) ) {
				foreach( $demo_data as $key => $value ) {
					$field1 = isset( $value['field1'] ) ? $value['field1'] : '';
					$field2 = isset( $value['field2'] ) ? $value['field2'] : '';
					$args = compact( 'field1', 'field2' );

					repeatable_demo_render_row( $key, $args, $post->ID );
				}
			} else {
				repeatable_demo_render_row( 1, array( 'field1' => '', 'field2' => '' ), $post->ID );
			}
			?>
			<tr>
				<td class="submit" colspan="4" style="float: none; clear: both; background: #fff;">
					<button class="button-secondary repeatable-demo-add-repeatable" style="margin: 6px 0;">Add New</button>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}


/**
 * Render individual meta box rows
 *
 * @param      int $key The key for a given row
 * @param      array $args The values we are passing to the function
 * @param      int $post_id The ID of the post we are editing
 * @return     void
 */
function repeatable_demo_render_row( $key, $args = array(), $post_id ) {
	?>
	<tr class="repeatable-demo-wrapper repeatable-demo-repeatable-row" data-key="<?php echo esc_attr( $key ); ?>">
		<td>
			<input type="text" name="_repeatable_demo_data[<?php echo $key; ?>][field1]" value="<?php echo $args['field1']; ?>" />
		</td>
		<td>
			<input type="text" name="_repeatable_demo_data[<?php echo $key; ?>][field2]" value="<?php echo $args['field2']; ?>" />
		</td>
		<td>
			<button class="repeatable-demo-remove-repeatable" data-type="image" style="background: url(<?php echo admin_url( '/images/xit.gif' ); ?>) no-repeat;"><span aria-hidden="true">x</span></button>
		</td>
	</tr>
	<?php
}


/**
 * Save post meta when the save_post action is called
 *
 * @param      int $post_id The ID of the post we are saving
 * @global     object $post The post we are saving
 * @return     void
 */
function repeatable_demo_save_meta_box( $post_id ) {
	global $post;

	// All the fields we want to save
	$fields = array(
		'_repeatable_demo_data'
	);

	foreach( $fields as $field ) {
		if( isset( $_POST[$field] ) ) {
			if( is_string( $_POST[$field] ) ) {
				$new = esc_attr( $_POST[$field] );
			} else {
				$new = $_POST[$field];
			}

			update_post_meta( $post_id, $field, $new );
		} else {
			delete_post_meta( $post_id, $field );
		}
	}
}
add_action( 'save_post', 'repeatable_demo_save_meta_box' );


/**
 * Load admin scripts
 *
 * @since      1.0.0
 * @param      string $hook The page hook
 * @return     void
 */
function repeatable_demo_admin_scripts( $hook ) {
	wp_enqueue_script( 'repeatable-demo', plugin_dir_url( __FILE__ ) . 'assets/js/admin.js', array( 'jquery' ) );
	wp_enqueue_style( 'repeatable-demo', plugin_dir_url( __FILE__ ) . 'assets/css/admin.css' );
}
add_action( 'admin_enqueue_scripts', 'repeatable_demo_admin_scripts', 100 );