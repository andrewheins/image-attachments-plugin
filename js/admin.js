var IAP = IAP || {};

(function ($) {
	"use strict";
	
	IAP.attach_images = function( post_id, images ) {
		var data = {
	    	'action': 'attach_images',
			'images': images,
			'post_id': post_id,
	    };
			    
	    $.ajax({
	    	'type': 'POST',
			'url': ajaxurl,
			'data': data,
			'success': function(d, t, j) {
				console.log( d );
				IAP.refresh_attachments( post_id );
			},
	    });
	    

	};
	
	IAP.refresh_attachments = function( post_id ) {
		var data = {
	    	'action': 'get_all_image_attachments',
			'post_id': post_id,
	    };
		    
	    $.ajax({
	    	'type': 'POST',
			'url': ajaxurl,
			'data': data,
			'success': function(d, t, j) {
				console.log( d );
				$( '#image-attachments .inside' ).html( d );
			},
	    });
	};
	
	IAP.remove_attachment = function( post_id, attachment_id ) {
	    var data = {
	    	'action': 'remove_image_attachment',
			'attachment_id': attachment_id,
			'post_id': post_id,
	    };
	    
	    $.ajax({
	    	'type': 'POST',
			'url': ajaxurl,
			'data': data,
			'success': function(d, t, j) {
				console.log( d );
				IAP.refresh_attachments( post_id );
			},
	    });
	};
	
	IAP.remove_all_attachments = function( post_id ) {
		var data = {
	    	'action': 'remove_all_attachments',
			'post_id': post_id,
	    };
		if( confirm( 'This will remove all attachments from this post. Proceed?' ) ) {
		    
		    $.ajax({
		    	'type': 'POST',
				'url': ajaxurl,
				'data': data,
				'success': function(d, t, j) {
				console.log( d );	
					IAP.refresh_attachments( post_id );
				},
		    });
	    }
	};
	
	
	
	
	
	$(function () {
		$.fn.image_attachments = function( options ) {

            var selector = $( this ).selector	; // Get the selector
            // Set default options
            var defaults = {
                'preview' : '.preview-upload',
                'text'    : '.text-upload',
                'button'  : '.button-upload',
                'remove'  : '.remove',
                'remove_all': '.remove_all',
            };
            var options  = $.extend( defaults, options );
            
            var file_frame;
            
              // When the Button is clicked...
		    $( options.button ).on( 'click', function( event ) {
		    	event.preventDefault();
	    	
				var $button = $(this);
	    	
				// If our media frame exists, reopen it.
	    	    if ( file_frame ) {
			      file_frame.open();
			      return;
			    }
			    
			    // Create the media frame.
			    file_frame = wp.media.frames.file_frame = wp.media({
			      title: "Attach Images",
			      button: {
			        text: "Attach These Images",
			      },
			      multiple: true  // Set to true to allow multiple files to be selected
			    });
			    
			    // When an image is selected, run a callback.
				file_frame.on( 'select', function() {
				
					var selection = file_frame.state().get('selection'), 
					image_array = [], 
					post_id = $('#post_ID').val();
					
					selection.map( function( attachment ) {
					  	// Do something with attachment.id and/or attachment.url here					
						image_array.push( attachment.id );
					});
					
					IAP.attach_images( post_id, image_array );
				});
		    			 
		        // Show WP Media Editor popup
		        file_frame.open();
		    } );
		    
			$( options.remove ).on( 'click', function( event ) {
			    IAP.remove_attachment( $('#post_ID').val(), $(this).data('id') );
			    event.preventDefault();
		    });
		    
		    $( options.remove_all ).on( 'click', function( event ) {
			    IAP.remove_all_attachments( $('#post_ID').val() );
			    event.preventDefault();
		    });

		    
        }	
		
		// if .uploader exists, fire our JS.
		$( '.uploader' ).image_attachments();

    });
    
        
}(jQuery));