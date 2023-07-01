jQuery( 'body' ).on( 'click', '.hb-add-attachment-link', function() {
	'use strict';

	var $wrapper = jQuery( this ).parents( '.hb-add-attachment' );
	var media_window = wp.media({
		title: hb_text.select_attachments,
		multiple: 'add',
	});

	media_window.on( 'open', function() {
		var selection = media_window.state().get( 'selection' );
		var ids_str = $wrapper.find( 'input' ).val();

		if ( ids_str != '' ) {
			ids = ids_str.split( ',' );
			for ( var i = 0; i < ids.length; i++ ) {
				var attachment = wp.media.attachment( ids[i] );
				selection.add( attachment );
			}
		}
	});

	media_window.on( 'select', function() {
		var selection = media_window.state().get( 'selection' );
		var ids = [];
		var titles = [];
		selection.map( function( attachment ) {
			attachment = attachment.toJSON();
			if ( attachment.url ) {
				ids.push( attachment.id );
				titles.push( attachment.title );
			}
		});
		hb_media_titles[ ids.join() ] = titles.join( ', ' );
		$wrapper.find( 'input' ).val( ids.join() ).change();
	});

	media_window.open();
});