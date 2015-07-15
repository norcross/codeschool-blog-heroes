<?php
/**
 * Codeschool Blog Heroes - Front Module
 *
 * Contains functions for loading on the
 * site front end
 *
 * @package Codeschool Blog Heroes
 */

function csbh_svg_banner( $name = '' ) {

	// get my image directory
	$folder = get_bloginfo( 'template_directory' ) . '/images/';

	// set my file
	$file   = $folder . $name;

	// make sure it exists
	$check  = get_headers( $file );

	// and return it
	return ! empty( $check[0] ) && stripos( $check[0], '200 OK' ) ? '<img src="' . esc_url( $file ) . '" width="670" />' : false;
}


/**
 * load and display our post hero
 *
 * @param  integer $post_id  [description]
 * @param  string  $classes  [description]
 * @param  boolean $echo     [description]
 * @return [type]            [description]
 */
function csbh_post_hero( $post_id = 0, $classes = '', $echo = true ) {

	// check for a post ID if one wasn't passed
	if ( empty( $post_id ) ) {

		// load the global
		global $post;

		// bail if it isn't there
		if ( empty( $post ) || ! is_object( $post ) || empty( $post->ID ) ) {
			return;
		}

		// set my post ID
		$post_id    = absint( $post->ID );
	}

	// fetch our meta
	$meta   = get_post_meta( $post_id, '_csbh_meta', true );

	// set an empty HTML block and style block
	$html   = '';
	$style  = '';

	// bail if we have no meta, or the image isn't there
	if ( empty( $meta ) || empty( $meta['image'] ) ) {

		// fetch my category
		$catobj = get_the_category( $post_id );
		$cslug  = ! empty( $catobj[0] ) && ! empty( $catobj[0]->slug ) ? $catobj[0]->slug : '';
		$csvg   = 'bg-hero-' . $cslug . '.svg';

		// set the HTML class with the SVG
		$html  .= '<div class="' . $classes . ' hero--post--category hero--post--category--' . $cslug . '">';
			$html  .= '<div class="cell cell--m well well--l">';
			$html  .= csbh_svg_banner( $csvg );
			$html  .= '</div>';
		$html  .= '</div>';

		// echo if requested
		if ( ! empty( $echo ) ) {
			echo $html;
		}

		// no echo. return
		return $html;
	}

	// ok. we have stuff. do stuff. start by setting some vars
	$medium     = ! empty( $meta['space'] ) ? 'hero--post--m' : '';
	$image      = ! empty( $meta['image'] ) ? $meta['image'] : '';
	$color      = ! empty( $meta['color'] ) ? $meta['color'] : 'transparent';
	$ximage     = ! empty( $meta['ximage'] ) ? $meta['ximage'] : '';
	$repeat     = ! empty( $meta['repeat'] ) ? $meta['repeat'] : 'no-repeat';
	$size       = ! empty( $meta['size'] ) ? $meta['size'] : 'contain';

	// if we don't have a background size (for some reason) do a quick output
	if ( empty( $size ) ) {

		// set the style
		$style .= 'background: ' . esc_attr( $color ) . ' url( ' . esc_url( $image ) . ' ) center ' . esc_attr( $repeat ) . ';';
	}

	// we have a size, so do our check for repeats
	if ( ! empty( $size ) ) {

		// we set repeating. build it.
		if ( $repeat == 'repeat-x' && ! empty( $ximage ) ) {

			// set the style
			$style .= 'background: url( ' . esc_url( $image ) . ' ) center no-repeat, url( ' . esc_url( $ximage ) . ' ) center repeat-x;';
			$style .= 'background-size: ' . esc_attr( $size ) . ', ' . esc_attr( $size ) . ';';

		} else { // we had a normal one.

			// set the style
			$style .= 'background: ' . esc_attr( $color ) . ' url( ' . esc_url( $image ) . ' ) center ' . esc_attr( $repeat ) . ';';
			$style .= 'background-size: ' . esc_attr( $size ) . ';';
		}
	}

	// set the HTML class, pulling in our style
	$html  .= '<div class="' . $classes . ' hero--post--custom ' . $medium . '" style="' . $style . '">';
		$html  .= '<div class="cell cell--m well well--l"></div>';
	$html  .= '</div>';

	// echo if requested
	if ( ! empty( $echo ) ) {
		echo $html;
	}

	// no echo. return
	return $html;
}

/**
 * fetch the category info and display it
 *
 * @param  integer $post_id  [description]
 * @param  boolean $echo     [description]
 * @return [type]            [description]
 */
function csbh_post_category_text( $post_id = 0, $echo = true ) {

	// check for a post ID if one wasn't passed
	if ( empty( $post_id ) ) {

		// load the global
		global $post;

		// bail if it isn't there
		if ( empty( $post ) || ! is_object( $post ) || empty( $post->ID ) ) {
			return;
		}

		// set my post ID
		$post_id    = absint( $post->ID );
	}

	// fetch my category
	$catobj = get_the_category( $post_id );

	// bail with no category data
	if ( empty( $catobj ) || ! is_array( $catobj ) || empty( $catobj[0] ) ) {
		return;
	}

	// get my category link
	$link   = get_category_link( $catobj[0]->term_id );

	// set an empty HTML block and style block
	$html   = '';

	// build it out
	$html  .= '<em class="tcs"> in ';
	$html  .= '<a class="bdrb tcs" href="' . esc_url( $link ) . '">' . esc_attr( $catobj[0]->name ) . '</a>';
	$html  .= '</em>';

	// echo if requested
	if ( ! empty( $echo ) ) {
		echo $html;
	}

	// no echo. return
	return $html;
}