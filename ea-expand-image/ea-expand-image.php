<?php /*
Plugin Name: EA Expand Image

Description: Use CSS to exand image in content (altenative to a light box.)

Version: 0.5

Author:  Tim Suellentrop & zig
Date: Aug 2016
Author URI: www.reachmaine.com

License: GPL3

*/ 
/* ea-expand-image */
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	function ea_image_enqueue_assets() {

	   wp_enqueue_script('ea_expand_image_js',plugin_dir_url( __FILE__ ).'/ea-expand-image.js', array( 'jquery' ) );
	   wp_enqueue_style( 'ea_expand_image_style', plugin_dir_url( __FILE__ ).'/ea-expand-image.css' );

	}
	add_action( 'wp_enqueue_scripts', 'ea_image_enqueue_assets' );
	
	
	/* Add class to caption when insert into content */
	add_filter( 'img_caption_shortcode', 'ea_img_caption_shortcode', 10, 3 );
	function ea_img_caption_shortcode( $empty, $attr, $content ){
		$attr = shortcode_atts( array(
			'id'      => '',
			'align'   => 'alignnone',
			'width'   => '',
			'caption' => ''
		), $attr );

		if ( 1 > (int) $attr['width'] || empty( $attr['caption'] ) ) {
			return '';
		}

		if ( $attr['id'] ) {
			$saved_id = $attr['id'];
			$attr['id'] = 'id="' . esc_attr( $attr['id'] ) . '" ';
		}

		return '<div ' . $attr['id']
		. 'class="wp-caption ea-contracted-image ' . esc_attr( $attr['align'] ) . '" '
		. 'style="max-width: ' . ( 10 + (int) $attr['width'] ) . 'px;">'
		. do_shortcode( $content )
		. '<p class="wp-caption-text">' . $attr['caption'] . '</p>'
		. '</div>';

	}

/******  media screen *****/
	// add the checkbox to the add media screen
	function IMGattachment_fields($form_fields, $post) {
		$exp =  get_post_meta($post->ID, '_eaNoExpand', true);
		$checked = ($exp) ? 'checked' : '';
		$form_fields['eaExpand'] = array(
			'label' => 'Dont Expand',
			'input' => 'html',
			'html' => "<input type='checkbox' {$checked} name='attachments[{$post->ID}][eaExpand]' id='attachments[{$post->ID}][eaExpand]' />",
			'value' => $exp,
			'helps' => 'Prevent this image from expanding in content.'
			);
	    return $form_fields;
	}
	add_filter("attachment_fields_to_edit", "IMGattachment_fields", null, 2);

	// save the checkbox value
	function ea_image_attachment_fields_save($post, $attachment) {
	    update_post_meta($post['ID'], '_eaNoExpand', $attachment['eaExpand']);
	    return $post;
	}
	add_filter("attachment_fields_to_save", "ea_image_attachment_fields_save", null, 2);


	/* add a class based on post_meta on image */
	function ea_add_image_class($class, $id, $align, $size){
	    if (get_post_meta($id, '_eaNoExpand', true) != 'on') {
	    	$class .= ' ea-expandable';
	    } 
	    return $class;
	}
	add_filter('get_image_tag_class','ea_add_image_class', 10, 4);


