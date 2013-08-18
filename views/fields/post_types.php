<?php
	$post_types = get_post_types();
	$options = get_option('ai_active_post_types');
	
	echo( '<ul class="ai_active_post_types">');
	
	foreach( $post_types as $type ) {
		$value = "";
		if( isset( $options[$type] ) ) {
			$value = $options[$type];
		}
		
		if(! in_array( $type, $this->forbidden_post_types ) ) {
			echo( '<li><input type="checkbox" id="' . $type . '" name="ai_active_post_types[' . $type . ']" value="1" ' . checked(1, $value, false) . '/> ' . $type . '</li>' );
		}
	}
	
	echo( '</ul>' );
