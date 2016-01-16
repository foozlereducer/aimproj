<?php
/**********
Settings
***********/

// 'liv' short for liviam should preface all functions in this theme

/**
 * Set the site locale to en-CA
 */
function liv_site_locale() {
	return 'en-CA';
}
add_filter( 'locale', 'liv_site_locale' );

/**
 * Filter in a link to a content ID attribute for the next/previous image links on image attachment pages
 */
function liv_enhanced_image_navigation( $url, $id ) {
	if ( ! is_attachment() && ! wp_attachment_is_image( $id ) )
		return $url;

	$image = get_post( $id );
	if ( ! empty( $image->post_parent ) && $image->post_parent != $id )
		$url .= '#main';

	return $url;
}
add_filter( 'attachment_link', 'liv_enhanced_image_navigation', 10, 2 );

/**
 * Filters wp_title to print a neat <title> tag based on what is being viewed.
 */
function liv_wp_title( $title, $sep ) {
	global $page, $paged;

	if ( is_feed() )
		return $title;

	// Add the blog name
	$title .= get_bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title .= " $sep $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		$title .= " $sep " . sprintf( __( 'Page %s', 'liviam' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'liv_wp_title', 10, 2 );

/**
 * Limit text by char count.
 */
if ( ! function_exists( 'liv_trim_chars' ) ) :
	function liv_trim_chars( $s, $i_limit, $s_ellipsis = '...', $b_count_ellipsis = FALSE ) {
		
		// truncate all
		if ( $i_limit <= 0 ) {
			return '';
			
			// no truncation
		} else if ( strlen( $s ) <= $i_limit ) {
			return $s;
		}
		
		// truncate
		$s_ellipsis_count    = $b_count_ellipsis ? $s_ellipsis : NULL;
		$i_truncate_position = stripos( $s, ' ', $i_limit - strlen( $s_ellipsis_count ) );

		if ( $i_truncate_position ) {
			$s_output = substr( trim( $s ), 0, $i_truncate_position );
		} else {
			$s_output = trim( $s );
		}
		
		while ( strlen( $s_output.$s_ellipsis_count ) > $i_limit ) {
			$s_output = preg_replace( '/\s*\S+\s*$/', '', $s_output );
		}
		return $s_output.$s_ellipsis;
	}
endif;