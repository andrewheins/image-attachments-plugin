(function ($) {
	"use strict";
	$(function () {
		$.fn.image_attachments = function( options ) {

            var selector = $( this ).selector	; // Get the selector
            // Set default options
            var defaults = {
                'preview' : '.preview-upload',
                'text'    : '.text-upload',
                'button'  : '.button-upload'
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
				
					var selection = file_frame.state().get('selection');
					
					selection.map( function( attachment ) {
					  	// Do something with attachment.id and/or attachment.url here					
						console.log( attachment );
						attachment = attachment.toJSON();
					});
				});
		    			 
		        // Show WP Media Editor popup
		        file_frame.open();
		    } );
        }	
		
		// if .uploader exists, fire our JS.
		$( '.uploader' ).image_attachments();

    });
    
        
}(jQuery));