<?php /* ea_expand_image */
/* purpose - to allow a light box type of image expansion with out the light box stuff */
// add ea-contracted-image class to the caption.
add_filter( 'img_caption_shortcode', 'my_img_caption_shortcode', 10, 3 );
function my_img_caption_shortcode( $empty, $attr, $content ){
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

//
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

function my_image_attachment_fields_save($post, $attachment) {
    //if ( isset($attachment['eaExpand']) )
    update_post_meta($post['ID'], '_eaNoExpand', $attachment['eaExpand']);
    return $post;
}
add_filter("attachment_fields_to_save", "my_image_attachment_fields_save", null, 2);


/* this works, but not quite what we want.   - dont want a new ID, but cool.

function filter_image_send_to_editor($html, $id, $caption, $title, $align, $url, $size, $alt) {
  $html = str_replace('<img ', '<img id="my-super-special-id" ', $html);

  return $html;
}
add_filter('image_send_to_editor', 'filter_image_send_to_editor', 10, 8); */

/* add a class based on post_meta on image? */
function add_image_class($class, $id, $align, $size){
    if (get_post_meta($id, '_eaNoExpand', true) != 'on') {
    	$class .= ' ea-expandable';
    } 
    return $class;
}
add_filter('get_image_tag_class','add_image_class', 10, 4);


// now default image ddl attributes.