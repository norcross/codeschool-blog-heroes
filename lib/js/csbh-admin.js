//********************************************************************************************************************************
// using file uploader
//********************************************************************************************************************************
function imageUploader( event, divBlock, divField ) {

	var file_frame;

	event.preventDefault();

	// If the media frame already exists, reopen it.
	if ( file_frame ) {
		file_frame.open();
		return;
	}

	// Create the media frame.
	file_frame = wp.media.frames.file_frame = wp.media({
		multiple: false
	});

	// run the callback when selected
	file_frame.on( 'select', function() {
		// make sure to only deal with the first item
		attachment = file_frame.state().get( 'selection' ).first().toJSON();

		// run the MIME type check to make sure it's an OK one
		if ( ! jQuery.inArray( attachment.mime, [ 'image/gif', 'image/jpeg', 'image/png', 'application/pdf', 'application/zip' ] ) ) {
			return;
		}

		// Populate the field with the URL
		jQuery( divBlock ).find( divField ).val( attachment.url );
	});

	// Finally, open the modal
	file_frame.open();
}


//********************************************************************************************************************************
// now start the engine
//********************************************************************************************************************************
jQuery(document).ready( function($) {

//********************************************************************************************************************************
// quick helper to check for an existance of an element
//********************************************************************************************************************************
	$.fn.divExists = function(callback) {
		// slice some args
		var args = [].slice.call( arguments, 1 );
		// check for length
		if ( this.length ) {
			callback.call( this, args );
		}
		// return it
		return this;
	};

//********************************************************************************************************************************
// set variables
//********************************************************************************************************************************
	var divBlock;
	var divField;

//********************************************************************************************************************************
// fire up image uploader on table postmeta
//********************************************************************************************************************************
	$( 'td.csbh-meta-upload-block' ).on( 'click', 'button.csbh-upload-button', function( event ) {

		// get my variables
		divBlock    = $( this ).parents( 'td.csbh-meta-upload-block' );
		divField    = $( divBlock ).find( 'input.csbh-upload-field' );

		// pass them
		imageUploader( event, divBlock, divField );
	});

//********************************************************************************************************************************
// that's all folks. we're done here
//********************************************************************************************************************************
});
