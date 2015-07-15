<?php
/**
 * Codeschool Blog Heroes - Admin Module
 *
 * Contains functions for loading on the
 * WP admin side, with meta and checks
 *
 * @package Codeschool Blog Heroes
 */

// check for the class
if ( ! class_exists( 'CSBH_Admin' ) ) {

// Start up the engine
class CSBH_Admin
{

	/**
	 * fire it up
	 *
	 */
	public function init() {

		// bail on non admin
		if ( ! is_admin() ) {
			return;
		}

		// call our functions
		add_action( 'admin_enqueue_scripts',        array( $this, 'scripts_styles'          )           );
		add_action( 'add_meta_boxes',               array( $this, 'load_metaboxes'          ),  11      );
		add_action( 'save_post',                    array( $this, 'save_metadata'           )           );
	}

	/**
	 * load our CSS and JS files
	 *
	 * @return [type] [description]
	 */
	public function scripts_styles() {

		// our CSS file
		wp_enqueue_style( 'csbh-admin', plugins_url( 'css/csbh-admin.css', __FILE__ ), array(), CSBH_VER, 'all' );

		// load media pieces for uploaders
		wp_enqueue_media();

		// our JS file
		wp_enqueue_script( 'csbh-admin', plugins_url( 'js/csbh-admin.js', __FILE__ ), array( 'jquery' ), CSBH_VER, true );
	}

	/**
	 * load our metaboxes
	 *
	 * @return [type] [description]
	 */
	public function load_metaboxes() {
		add_meta_box( 'blog-heroes', 'Hero Images', array( __class__, 'blog_heroes_meta' ), 'post', 'normal', 'high' );
	}

	/**
	 * metabox for blog hero images
	 *
	 * @param  object $post the global post object
	 *
	 * @return [type]       [description]
	 */
	public static function blog_heroes_meta( $post ) {

		// run our conversion script
		self::convert_existing_meta( $post_id );

		// get array of input data
		$meta   = get_post_meta( $post->ID, '_csbh_meta', true );

		// slice our each part
		$space      = ! empty( $meta['space'] ) ? true : false;
		$color      = ! empty( $meta['color'] ) ? $meta['color'] : 'transparent';
		$image      = ! empty( $meta['image'] ) ? $meta['image'] : '';
		$ximage     = ! empty( $meta['ximage'] ) ? $meta['ximage'] : '';
		$repeat     = ! empty( $meta['repeat'] ) ? $meta['repeat'] : 'no-repeat';
		$size       = ! empty( $meta['size'] ) ? $meta['size'] : 'contain';

		// fetch my vars for dropdowns
		$rptvars    = self::get_repeat_css_vars();
		$sizevars   = self::get_size_css_vars();

		// build the boxes
		echo '<table class="form-table csbh-meta-table">';
		echo '<tbody>';

		// build it
		echo '<tr class="csbh-meta-field csbh-meta-checkbox-field">';
			echo '<th>Display</th>';
			echo '<td>';
			echo '<input type="checkbox" value="1" id="csbh-space" name="csbh-meta[space]" ' . checked( $space, true, false ) . '>';
			echo '<label for="csbh-space">Display a hero image on this post</label>';
			echo '</td>';
		echo '</tr>';

		echo '<tr class="csbh-meta-field">';
			echo '<th>Background Color</th>';
			echo '<td>';
			echo '<input type="text" class="three-fourths" id="csbh-color" name="csbh-meta[color]" value="' . esc_attr( $color ) . '">';
			echo '</td>';
		echo '</tr>';

		echo '<tr class="csbh-meta-field csbh-meta-upload-field">';
			echo '<th>Hero Image</th>';
			echo '<td class="csbh-meta-upload-block">';
			echo '<input type="url" class="three-fourths csbh-upload-field" id="csbh-image" name="csbh-meta[image]" value="' . esc_url( $image ) . '">';
			echo '<button type="button" class="button button-secondary csbh-upload-button"><i class="dashicons dashicons-upload"></i></button>';
			echo '</td>';
		echo '</tr>';

		echo '<tr class="csbh-meta-field csbh-meta-upload-field">';
			echo '<th>Repeating Image</th>';
			echo '<td class="csbh-meta-upload-block">';
			echo '<input type="url" class="three-fourths csbh-upload-field" id="csbh-ximage" name="csbh-meta[ximage]" value="' . esc_url( $ximage ) . '">';
			echo '<button type="button" class="button button-secondary csbh-upload-button"><i class="dashicons dashicons-upload"></i></button>';
			echo '<p class="description">This is only used when the repeat is set to "Repeat X"</p>';
			echo '</td>';
		echo '</tr>';

		echo '<tr class="csbh-meta-field csbh-meta-dropdown-field">';
			echo '<th>Background Repeat</th>';
			echo '<td>';
			echo '<select class="one-half" name="csbh-meta[repeat]" id="csbh-repeat">';
				echo '<option value="">(Select)</option>';
				// loop my repeat vars
				foreach ( $rptvars as $key => $label ) {
					echo '<option value="' . esc_attr( $key ) . '" ' . selected( $repeat, $key, false ) . '>' . esc_attr( $label ) . '</option>';
				}
			echo '</select>';
			echo '</td>';
		echo '</tr>';

		echo '<tr class="csbh-meta-field csbh-meta-dropdown-field">';
			echo '<th>Background Size</th>';
			echo '<td>';
			echo '<select class="one-half" name="csbh-meta[size]" id="csbh-size">';
				echo '<option value="">(Select)</option>';
				// loop my size vars
				foreach ( $sizevars as $key => $label ) {
					echo '<option value="' . esc_attr( $key ) . '" ' . selected( $size, $key, false ) . '>' . esc_attr( $label ) . '</option>';
				}
			echo '</select>';
			echo '</td>';
		echo '</tr>';

		// end table
		echo '</tbody>';
		echo '</table>';
	}

	/**
	 * save our meta data
	 * @param  [type] $post_id [description]
	 * @return [type]          [description]
	 */
	public function save_metadata( $post_id ) {

		// run various checks to make sure we aren't doing anything weird
		if ( self::meta_save_check( $post_id ) ) {
			return $post_id;
		}

		// bail on anything that isn't a post
		if ( 'post' !== get_post_type( $post_id ) ) {
			return $post_id;
		}

		// bail if nothing was passed (not empty)
		if ( ! isset( $_POST['csbh-meta'] ) ) {
			return $post_id;
		}

		// set my passed meta
		$meta   = $_POST['csbh-meta'];

		// set my empty data
		$data   = array();

		// check for the checkbox
		if ( ! empty( $meta['space'] ) ) {
			$data['space']  = true;
		}

		// check for the color with fallback
		$data['color']  = ! empty( $meta['color'] ) ? sanitize_text_field( $meta['color'] ) : 'transparent';

		// check for the base image
		if ( ! empty( $meta['image'] ) ) {
			$data['image']  = esc_url( $meta['image'] );
		}

		// check for the repeat image
		if ( ! empty( $meta['ximage'] ) ) {
			$data['ximage']  = esc_url( $meta['ximage'] );
		}

		// check for the repeat and size with fallbacks
		$data['repeat'] = ! empty( $meta['repeat'] ) ? sanitize_text_field( $meta['repeat'] ) : 'no-repeat';
		$data['size']   = ! empty( $meta['size'] ) ? sanitize_text_field( $meta['size'] ) : 'contain';

		// filter empty
		$data   = array_filter( $data );

		// update or delete
		if ( ! empty( $data ) ) {
			update_post_meta( $post_id, '_csbh_meta', $data );
		} else {
			delete_post_meta( $post_id, '_csbh_meta' );
		}
	}

	/**
	 * confirm the user has permission to save meta
	 *
	 * @param  integer $post_id [description]
	 * @param  string  $cap     [description]
	 * @return [type]           [description]
	 */
	public static function meta_save_check( $post_id = 0, $cap = 'edit_post' ) {

		// Bail out if running an autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return true;
		}

		// Bail out if running an ajax/
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return true;
		}

		// Bail out if running a cron */
		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			return true;
		}

		// Bail out if user does not have permissions
		if ( ! empty( $post_id ) && ! current_user_can( $cap, $post_id ) ) {
			return $post_id;
		}

		// return false
		return false;
	}

	/**
	 * get the available CSS background-repeat types
	 *
	 * @return [type] [description]
	 */
	public static function get_repeat_css_vars() {

		// build the array and return
		return array(
			'no-repeat' => 'No Repeat',
			'repeat'    => 'Repeat on X and Y axis',
			'repeat-x'  => 'Repeat on X axis',
			'repeat-y'  => 'Repeat on Y axis',
		);
	}

	/**
	 * get the available CSS background-size types
	 *
	 * @return [type] [description]
	 */
	public static function get_size_css_vars() {

		// build the array and return
		return array(
			'cover'     => 'Cover',
			'contain'   => 'Contain',
		);
	}

	/**
	 * convert the existing meta if it exists
	 *
	 * @param  [type] $post_id [description]
	 * @return [type]          [description]
	 */
	public static function convert_existing_meta( $post_id ) {

		// do our check first
		$check  = get_post_meta( $post_id, '_csbh_meta_convert', true );

		// bail if we have run already
		if ( ! empty( $check ) ) {
			return;
		}

		// pull out the existing
		$space      = get_post_meta( $post_id, 'hero_space', true );
		$color      = get_post_meta( $post_id, 'hero_background_color', true );
		$image      = get_post_meta( $post_id, 'hero_background_image', true );
		$ximage     = get_post_meta( $post_id, 'hero_background_repeat_image', true );
		$repeat     = get_post_meta( $post_id, 'hero_background_repeat', true );
		$size       = get_post_meta( $post_id, 'hero_background_size', true );

		// set my empty data
		$data   = array();

		// check for the checkbox
		if ( ! empty( $space ) ) {
			$data['space']  = true;
		}

		// check for color
		if ( ! empty( $color ) ) {
			$data['color']  = sanitize_text_field( $color );
		}

		// check for the base image
		if ( ! empty( $image ) ) {
			$data['image']  = esc_url( $image );
		}

		// check for the repeat image
		if ( ! empty( $ximage ) ) {
			$data['ximage']  = esc_url( $ximage );
		}

		// check for repeat
		if ( ! empty( $repeat ) ) {
			$data['repeat']  = sanitize_text_field( $repeat );
		}

		// check for size
		if ( ! empty( $size ) ) {
			$data['size']  = sanitize_text_field( $size );
		}

		// filter empty
		$data   = array_filter( $data );

		// update or delete
		if ( ! empty( $data ) ) {
			update_post_meta( $post_id, '_csbh_meta', $data );
		} else {
			delete_post_meta( $post_id, '_csbh_meta' );
		}

		// and set our key
		update_post_meta( $post_id, '_csbh_meta_convert', true );

		// and finish
		return;
	}

/// end class
}

/// end exists check
}

// Instantiate our class
$CSBH_Admin = new CSBH_Admin();
$CSBH_Admin->init();
