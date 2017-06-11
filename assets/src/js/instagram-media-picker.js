/**
 * Created by Nabeel on 20-Feb-17.
 */
(function ( w, $, doc, undefined ) {
	"use strict";

	$( function () {
		// vars
		var $current_value_input    = null,
		    $current_button         = null,
		    current_values          = null,
		    $current_username_input = null,
		    ajax_request            = null,
		    is_loading              = false,
		    media_item_template     = $( '#acf-imp-media-item-template' ).html();

		// on browse modal open
		$( '.acf-fields' ).on( 'show.bs.modal', '.acf-imp-browse-modal', function ( e ) {
			// trigger load on
			var $modal     = $( this ),
			    $load_more = $modal.find( '.acf-imp-load-more' );

			// current focused field input & value
			$current_button         = $( e.relatedTarget );
			$current_value_input    = $( '#' + $modal.data( 'value-input' ) );
			$current_username_input = $( '#' + $modal.data( 'username-input' ) );
			current_values          = $current_value_input.val().split( ',' ).filter( function ( value ) {
				return value.trim().length > 0;
			} );

			$modal.find( '.acf-imp-username' ).val( $current_username_input.val() );

			if ( 0 === $load_more.attr( 'data-max-id' ).length ) {
				// trigger media load on first open
				$load_more.trigger( 'acf-imp-click', [ $modal ] );
			}
		} )
		// on value update
		.on( 'acf-imp-update-value', '.acf-imp-browse-modal', function () {
			var $selected_media = $( this ).find( 'input[type=checkbox]:checked' );

			if ( $selected_media.length > $( this ).data( 'media-limit' ) ) {
				// limit reached
				$selected_media.each( function ( index, input ) {
					if ( current_values.indexOf( input.value ) < 0 ) {
						// remove selection from over limit
						input.checked = false;
					}
				} );
			} else {
				// fetch values
				current_values = $selected_media.map( function ( index, input ) {
					return input.value;
				} ).toArray();

				// update field
				$current_value_input.val( current_values.join( ',' ) );

				if ( current_values.length ) {
					// set selected media count
					$current_button.text( $current_button.data( 'browse-label' ) + ' (' + current_values.length.toString() + ')' );
				}
			}
		} )
		// on item selection
		.on( 'change', '.acf-imp-media-item input[type=checkbox]', function ( e ) {
			// trigger field value update
			$( this ).closest( '.acf-imp-browse-modal' ).trigger( 'acf-imp-update-value' );
		} )
		// load media
		.on( 'acf-imp-load-media', '.acf-imp-browse-modal', function () {
			var $modal     = $( this ),
			    username   = $modal.find( '.acf-imp-username' ).val().trim().replace( /[^a-zA-Z0-9\._]/g, '' ),
			    $load_more = $modal.find( '.acf-imp-load-more' );

			if ( username.length < 3 ) {
				// invalid username!
				return true;
			}

			if ( ajax_request ) {
				// terminate previous ongoing request
				ajax_request.abort();
			}

			// enable loading status
			$modal.trigger( 'acf-imp-loading' );
			is_loading = true;

			var items_container = $modal.find( '.acf-imp-media-items' ).addClass( 'hide' );

			// load data
			ajax_request = $.post( acf_imp_media_picker.ajax_url, {
				action  : 'fetch_instagram_media_items',
				nonce   : acf_imp_media_picker.ajax_nonce,
				username: username
			}, function ( response ) {
				if ( response.success ) {
					// data found
					var media_item = null,
					    new_items  = [];

					// walk through items list
					for ( var i = 0, length = response.data.length; i < length; i++ ) {
						media_item = response.data[ i ];
						// fill in placeholders
						new_items.push(
							media_item_template.replace( /\{code\}/g, media_item.code )
							.replace( '{type}', media_item.type )
							.replace( '{thumbnail}', media_item.image.thumbnail )
							.replace( '{likes}', media_item.counts.likes )
							.replace( '{comments}', media_item.counts.comments )
							.replace( '{checked}', current_values.indexOf( media_item.code ) > -1 ? 'checked="checked"' : '' )
						);
					}

					// append the new items
					items_container.html( new_items.join( '' ) );

					// no more to load after that
					$load_more.addClass( 'hidden' );
				} else {
					// error loading data
					alert( response.data );
				}
			}, 'json' ).always( function () {
				// disable loading status
				$modal.trigger( 'acf-imp-loading-done' );
				is_loading = false;
				items_container.removeClass( 'hide' );
			} );
		} )
		// on load more button clicked
		.on( 'click acf-imp-click', '.acf-imp-load-more', function ( e, $modal ) {
			if ( undefined === $modal ) {
				$modal = $( this ).closest( '.acf-imp-browse-modal' );
			}

			// load first/more media
			$modal.trigger( 'acf-imp-load-media' );
		} )
		// on enter key pressed while focusing on username field
		.on( 'keydown keyup', '.acf-imp-username', function ( e ) {
			var $this = $( this );

			if ( 'keydown' === e.type && 13 === e.keyCode ) {
				// prevent from submitting the form
				e.preventDefault();

				if ( false === is_loading ) {
					// run load code
					$this.siblings( '.acf-imp-load-more' ).trigger( 'click' );
				}
			} else {
				// bind value
				$( '#' + $this.data( 'value-input' ) ).val( $this.val() );
			}
		} )
		// on loading
		.on( 'acf-imp-loading', '.acf-imp-browse-modal', function () {
			this.className += ' is-loading';
		} )
		// on loading done
		.on( 'acf-imp-loading-done', '.acf-imp-browse-modal', function () {
			this.className = this.className.replace( ' is-loading', '' );
		} );
	} );

	if ( !Array.prototype.filter ) {
		Array.prototype.filter = function ( fun/*, thisArg*/ ) {
			'use strict';

			if ( this === void 0 || this === null ) {
				throw new TypeError();
			}

			var t   = Object( this );
			var len = t.length >>> 0;
			if ( typeof fun !== 'function' ) {
				throw new TypeError();
			}

			var res     = [];
			var thisArg = arguments.length >= 2 ? arguments[ 1 ] : void 0;
			for ( var i = 0; i < len; i++ ) {
				if ( i in t ) {
					var val = t[ i ];

					// NOTE: Technically this should Object.defineProperty at
					//       the next index, as push can be affected by
					//       properties on Object.prototype and Array.prototype.
					//       But that method's new, and collisions should be
					//       rare, so use the more-compatible alternative.
					if ( fun.call( thisArg, val, i, t ) ) {
						res.push( val );
					}
				}
			}

			return res;
		};
	}
})( window, jQuery );