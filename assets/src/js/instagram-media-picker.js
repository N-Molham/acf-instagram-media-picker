/**
 * Created by Nabeel on 20-Feb-17.
 */
(function ( w, $, doc, undefined ) {
	"use strict";

	$( function () {
		// vars
		var $current_field      = null,
		    current_values      = null,
		    ajax_request        = null,
		    media_item_template = $( '#acf-imp-media-item-template' ).html();

		// on browse modal open
		$( '.gform_wrapper' ).on( 'show.bs.modal', '.acf-imp-browse-modal', function () {
			// trigger load on
			var $load_more = $( this ).find( '.acf-imp-load-more' );

			// current focused field input & value
			$current_field = $( '#' + $( this ).data( 'target-input' ) );
			current_values = $current_field.val().split( ',' ).filter( function ( value ) {
				return value.trim().length > 0;
			} );

			if ( 0 === $load_more.attr( 'data-max-id' ).length ) {
				// trigger media load on first open
				$load_more.trigger( 'acf-imp-click' );
			}
		} )
		// on value update
		.on( 'acf-imp-update-value', '.acf-imp-browse-modal', function () {
			// fetch values
			current_values = $( this ).find( 'input[type=checkbox]:checked' ).map( function ( index, input ) {
				return input.value;
			} ).toArray();

			// update field
			$current_field.val( current_values.join( ',' ) );
		} )
		// on item selection
		.on( 'change', '.acf-imp-media-item input[type=checkbox]', function ( e ) {
			// trigger field value update
			$( this ).closest( '.acf-imp-browse-modal' ).trigger( 'acf-imp-update-value' );
		} )
		// load media
		.on( 'acf-imp-load-media', '.acf-imp-browse-modal', function ( e ) {
			var $modal     = $( this ),
			    $load_more = $modal.find( '.acf-imp-load-more' );

			if ( ajax_request ) {
				// terminate previous ongoing request
				ajax_request.abort();
			}

			// enable loading status
			$modal.trigger( 'acf-imp-loading' );

			// load data
			ajax_request = $.post( slc_media_picker.ajax_url, {
				action: 'fetch_instagram_media_items',
				max_id: $load_more.attr( 'data-max-id' )
			}, function ( response ) {
				if ( response.success ) {
					// data found
					var media_item = null,
					    new_items  = [];

					// walk through items list
					for ( var i = 0, length = response.data.media_items.length; i < length; i++ ) {
						media_item = response.data.media_items[ i ];
						// fill in placeholders
						new_items.push(
							media_item_template.replace( /\{id\}/g, media_item.media_id )
							.replace( '{type}', media_item.media_type )
							.replace( '{thumbnail}', media_item.image.thumbnail )
							.replace( /\{caption\}/g, media_item.caption )
							.replace( '{likes}', media_item.counts.likes )
							.replace( '{comments}', media_item.counts.comments )
						);
					}

					// append the new items
					$modal.find( '.acf-imp-media-items' ).append( new_items.join( '' ) );

					if ( response.data.next_max_id ) {
						// update load more data
						$load_more.attr( 'data-max-id', response.data.next_max_id );
					} else {
						// no more to load after that
						$load_more.addClass( 'hidden' );
					}
				} else {
					// error loading data
					alert( response.data );
				}
			}, 'json' ).always( function () {
				// disable loading status
				$modal.trigger( 'acf-imp-loading-done' );
			} );
		} )
		// on load more button clicked
		.on( 'click acf-imp-click', '.acf-imp-load-more', function () {
			// load first/more media
			$( this ).closest( '.acf-imp-browse-modal' ).trigger( 'acf-imp-load-media' );
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