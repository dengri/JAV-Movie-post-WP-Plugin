/*
 * Attaches the image uploader to the input field
 */
jQuery(document).ready(function($){

		select_image( '#cover_image_button', '#cover_image_hidden', '#cover_image' );
		select_image( '#screenshot_image_button', '#screenshot_image_hidden', '#screenshot_image' );

 
function select_image( select_button_id, hidden_field_id, image_id ){

	// Instantiates the variable that holds the media library frame.
	var meta_image_frame;

  // Runs when the image button is clicked.
  $( select_button_id ).click(function(e){

      // Prevents the default action from occuring.
      e.preventDefault();

      // If the frame already exists, re-open it.
      if ( meta_image_frame ) {
          meta_image_frame.open();
          return;
      }

      // Sets up the media library frame
      meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
          title: meta_image.title,
          button: { text:  meta_image.button },
          library: { type: 'image' }
      });

      // Runs when an image is selected.
      meta_image_frame.on('select', function(){

          // Grabs the attachment selection and creates a JSON representation of the model.
          var media_attachment = meta_image_frame.state().get('selection').first().toJSON();

          // Sends the attachment URL to our custom image input field.
          $( hidden_field_id ).val( media_attachment.url );
          $( image_id ).attr("src", media_attachment.url);
          $( image_id ).removeClass("hide_when_no_image");

      });

      // Opens the media library frame.
      meta_image_frame.open();
  });
 }
});
