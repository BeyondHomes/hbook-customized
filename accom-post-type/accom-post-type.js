jQuery( document ).ready( function( $ ) {
	'use strict';

	$( 'input[name="hb-accom-default-page"]' ).on( 'change', function() {
		if ( $( 'input[name="hb-accom-default-page"]:checked' ).val() == 'yes' ) {
			$( '.hb-accom-select-template' ).show();
			$( '.hb-accom-select-linked-page' ).hide();
		} else {
			$( '.hb-accom-select-template' ).hide();
			$( '.hb-accom-select-linked-page' ).show();
		}
	}).change();

	if ( $( '#hb-accom-num-name-wrapper' ).length ) {

		var current_db_index = $( '#hb-accom-num-name-index' ).val();

		update_accom_num_name_inputs();

		$( '#hb-accom-quantity' ).on( 'keyup', function() {
			var accom_quantity = parseInt( $( '#hb-accom-quantity' ).val(), 10 ) || 1;
			if ( accom_quantity > 999 ) {
				accom_quantity = 999;
			}
			if ( $( '#hb-accom-quantity' ).val() != '' ) {
				$( '#hb-accom-quantity' ).val( accom_quantity );
			}
			update_accom_num_name_inputs();
		});

		function update_accom_num_name_inputs() {
			var new_accom_num_name_markup = '',
				accom_quantity = parseInt( $( '#hb-accom-quantity' ).val(), 10 ) || 0;

			$( '.hb-accom-num-name' ).hide();

			if ( $( '.hb-accom-num-name' ).length < accom_quantity ) {
				var id = 0;
				if ( $( '.hb-accom-num-name' ).length ) {
					id = $( '.hb-accom-num-name' ).last().find( 'input' ).data( 'id' );
					if ( id < current_db_index ) {
						id = current_db_index;
					}
				} else {
					id = current_db_index;
				}
				for ( var i = 0; i < accom_quantity - $( '.hb-accom-num-name' ).length; i++ ) {
					id++;
					new_accom_num_name_markup += '<p class="hb-accom-num-name">';
					new_accom_num_name_markup += '<input data-id="' + id + '" type="text" value="' + id + '" /> ';
					new_accom_num_name_markup += '<a class="hb-accom-num-name-delete" href="#">' + hb_accom_post_text.delete_accom_num_name + '<a/>'
					new_accom_num_name_markup += '</p>';
				}
				$( '#hb-accom-num-name-wrapper' ).append( new_accom_num_name_markup );
				if ( id > current_db_index ) {
					$( '#hb-accom-num-name-index' ).val( id );
				}
			}

			for ( var i = 0; i < accom_quantity; i++ ) {
				$( '.hb-accom-num-name' ).eq( i ).show();
			}

		}

		$( 'body' ).on( 'click', '.hb-accom-num-name-delete', function() {
			$( this ).blur();
			var accom_title = $( '#title' ).val() || $( '#post-title-0' ).val();
			var accom_num_name = accom_title + ' (' + $( this ).parents( '.hb-accom-num-name' ).find( 'input' ).val() + ')';
			var confirm_text = hb_accom_post_text.delete_accom_num_name_text.split( '%s' ).join( accom_num_name );
			if ( confirm( confirm_text ) ) {
				$( this ).parent().remove();
				var accom_quantity = parseInt( $( '#hb-accom-quantity' ).val(), 10 );
				accom_quantity--;
				$( '#hb-accom-quantity' ).val( accom_quantity );
			}
			return false;
		});

		$( '.hb-edit-accom-numbering' ).on( 'click', function() {
			$( this ).blur();
			$( '#hb-accom-num-name-wrapper' ).slideToggle();
			return false;
		});

		update_accom_num_name_json();

		$( 'body' ).on( 'click', '.editor-post-publish-button', function() {
			return validate_accom_settings();
		});

		$( '#post' ).on( 'submit', function() {
			return validate_accom_settings();
		});

		function validate_accom_settings() {
			if ( $( '#hb-accom-starting-price' ).val() && ! $.isNumeric( $( '#hb-accom-starting-price' ).val() ) ) {
				alert( hb_accom_post_text.starting_price_not_number );
				$( '#hb-accom-starting-price' ).focus();
				return false;
			}
			if ( $( '#hb-accom-quantity' ).length ) {
				var accom_quantity = parseInt( $( '#hb-accom-quantity' ).val(), 10 );
				if ( ! accom_quantity ) {
					alert( hb_accom_post_text.accom_number_zero );
					$( '#hb-accom-quantity' ).focus();
					return false;
				}
				$( '#hb-accom-quantity' ).val( accom_quantity );
			}
			update_accom_num_name_inputs();
			update_accom_num_name_json();
			return true;
		}

		function update_accom_num_name_json() {
			var accom_quantity = parseInt( $( '#hb-accom-quantity' ).val(), 10 ) || 0;
			var accom_num_name = {};
			for ( var i = 0; i < accom_quantity; i++ ) {
				var $input = $( '.hb-accom-num-name' ).eq( i ).find( 'input' );
				var id = $input.data( 'id' );
				var num_name = $input.val();
				accom_num_name[ id ] = num_name;
			}
			$( '#hb-accom-num-name-json' ).val( JSON.stringify( accom_num_name ) );
		}

	}

	$( 'body' ).on( 'click', '#delete-action a, .editor-post-trash', function() {
		return confirm( hb_accom_post_text.delete_accom_text );
	});

	var add_accom_id_timer;
	if ( $( '.gutenberg' ).length || $( '.block-editor-page' ).length ) {
		add_accom_id_timer = setInterval( add_accom_id, 1000 );
	}

	function add_accom_id() {
		if ( $('.editor-post-title__input').length ) {
			if ( hb_blocks_data.current_accom_id ) {
				var accom_id_text = '<p style="color: #aaa; font-size: 13px; padding: 0 28px;">';
				accom_id_text += hb_blocks_text.accom_id + ' ' + hb_blocks_data.current_accom_id;
				accom_id_text += '</p>';
				$('.editor-post-title__input').after( accom_id_text );
			}
			clearInterval( add_accom_id_timer );
		}
	}

});