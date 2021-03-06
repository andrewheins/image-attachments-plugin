<?php 
	$gallery_args = array(
		'posts_per_page'   => -1,
		'post_type'        => 'attachment',
		'post_parent'      => $post->ID,
	);
	
	$gallery_images = get_posts( $gallery_args );
	
	echo( '<ul class="attached_images_list">' );
	foreach ( $gallery_images as $post ) {
		setup_postdata( $post );
		
		$image_tag = wp_get_attachment_image( $post->ID );
		
		echo( '<li>' . $image_tag . '</li>');
	} 
	echo( '</ul>' );
	
	wp_reset_postdata();