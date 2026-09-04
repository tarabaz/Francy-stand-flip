/* global jQuery, wp, fsfAdmin */
( function ( $ ) {
	'use strict';

	if ( typeof fsfAdmin === 'undefined' ) {
		return;
	}

	var CARD_KEYS = [ 'card_x', 'card_y', 'card_w', 'card_rot' ];
	var STAND_KEYS = [ 'stand_x', 'stand_y', 'stand_w' ];
	var BG_KEYS = [ 'bg_color', 'bg_color2' ];

	/**
	 * Numero pulito, come FSF_Render::num() in PHP.
	 */
	function num( value ) {
		var n = parseFloat( value );
		if ( isNaN( n ) ) {
			n = 0;
		}
		return String( Math.round( n * 1000 ) / 1000 );
	}

	function clamp( value, min, max ) {
		return Math.max( min, Math.min( max, parseFloat( value ) || 0 ) );
	}

	function on( value ) {
		return parseInt( value, 10 ) === 1;
	}

	/**
	 * hex + opacità (0-100) -> rgba(), come FSF_Render::rgba().
	 */
	function rgba( hex, opacity ) {
		var clean = String( hex || '' ).replace( '#', '' );
		if ( clean.length === 3 ) {
			clean = clean[ 0 ] + clean[ 0 ] + clean[ 1 ] + clean[ 1 ] + clean[ 2 ] + clean[ 2 ];
		}
		if ( ! /^[0-9a-f]{6}$/i.test( clean ) ) {
			clean = '000000';
		}
		var alpha = Math.max( 0, Math.min( 100, parseFloat( opacity ) || 0 ) ) / 100;
		return 'rgba(' + parseInt( clean.substr( 0, 2 ), 16 ) + ',' +
			parseInt( clean.substr( 2, 2 ), 16 ) + ',' +
			parseInt( clean.substr( 4, 2 ), 16 ) + ',' + num( alpha ) + ')';
	}

	/**
	 * Impostazioni correnti: globali + valori attualmente nel form.
	 * Le sezioni di override valgono solo se la relativa spunta è attiva.
	 */
	function currentSettings() {
		var settings = $.extend( {}, fsfAdmin.settings );

		var $cardOvr = $( 'input[name="_fsf_override"]' );
		var $standOvr = $( 'input[name="_fsf_stand_ovr"]' );
		var $bgOvr = $( 'input[name="_fsf_bg_ovr"]' );

		var cardEnabled = ! $cardOvr.length || $cardOvr.is( ':checked' );
		var standEnabled = ! $standOvr.length || $standOvr.is( ':checked' );
		var bgEnabled = ! $bgOvr.length || $bgOvr.is( ':checked' );

		$( '[data-fsf-key]' ).each( function () {
			var $el = $( this );
			var key = $el.data( 'fsfKey' );

			if ( ! key || String( key ).indexOf( '_fsf_' ) === 0 ) {
				return;
			}
			if ( ! cardEnabled && CARD_KEYS.indexOf( key ) !== -1 ) {
				return;
			}
			if ( ! standEnabled && STAND_KEYS.indexOf( key ) !== -1 ) {
				return;
			}
			if ( ! bgEnabled && BG_KEYS.indexOf( key ) !== -1 ) {
				return;
			}

			var type = fsfAdmin.fieldTypes[ key ] || 'text';

			if ( $el.is( ':checkbox' ) || type === 'checkbox' ) {
				settings[ key ] = $el.is( ':checked' ) ? 1 : 0;
				return;
			}

			var value = $el.val();
			if ( value === '' || value === null ) {
				return;
			}
			settings[ key ] = value;
		} );

		// Vero solo nella schermata dello stand, con la spunta attiva.
		settings.__standOvr = $standOvr.length > 0 && $standOvr.is( ':checked' );

		return settings;
	}

	/**
	 * Variabili CSS del box: specchio di FSF_Render::box_vars().
	 */
	function buildVars( s ) {
		var bg = s.bg_type === 'gradient'
			? 'linear-gradient(' + num( s.bg_angle ) + 'deg, ' + s.bg_color + ' 0%, ' + s.bg_color2 + ' 100%)'
			: s.bg_color;

		var valign = [ 'start', 'center', 'end' ].indexOf( s.content_valign ) !== -1
			? 'flex-' + s.content_valign
			: 'flex-end';

		var boxW = Math.max( 1, parseFloat( s.box_w ) || 560 );
		var boxH = Math.max( 1, parseFloat( s.box_h ) || 320 );
		var free = on( s.stand_free );

		// Posizione/scala effettiva della foto: come in PHP.
		var standX = ( s.__standOvr || free ) ? parseFloat( s.stand_x ) : 50;
		var standY = ( s.__standOvr || free ) ? parseFloat( s.stand_y ) : 50;
		var standW = parseFloat( s.stand_w );

		var objpos = ( ! free && s.__standOvr )
			? num( clamp( standX, 0, 100 ) ) + '% ' + num( clamp( standY, 0, 100 ) ) + '%'
			: s.stand_pos;

		var cardXpx = s.card_unit === 'px' ? parseFloat( s.card_x ) : boxW * parseFloat( s.card_x ) / 100;
		var cardYpx = s.card_unit === 'px' ? parseFloat( s.card_y ) : boxH * parseFloat( s.card_y ) / 100;
		var cardWpx = s.card_w_unit === 'px' ? parseFloat( s.card_w ) : boxW * parseFloat( s.card_w ) / 100;

		return {
			'--fsf-boxw': num( s.box_w ),
			'--fsf-boxh-n': num( s.box_h ),
			'--fsf-box-max': num( s.box_w ) + 'px',
			'--fsf-minw-n': num( s.min_w ),
			'--fsf-ratio': num( s.box_w ) + ' / ' + num( s.box_h ),
			'--fsf-radius-n': num( s.radius ),
			'--fsf-bg': bg,
			'--fsf-border-n': num( s.border_w ),
			'--fsf-border-color': rgba( s.border_color, s.border_opacity ),
			'--fsf-bsy-n': num( s.box_shadow_y ),
			'--fsf-bsb-n': num( s.box_shadow_blur ),
			'--fsf-bs-color': rgba( s.box_shadow_color, s.box_shadow_opacity ),

			'--fsf-card-x-n': num( s.card_x ),
			'--fsf-card-y-n': num( s.card_y ),
			'--fsf-card-w-n': num( s.card_w ),
			'--fsf-card-xpx-n': num( cardXpx ),
			'--fsf-card-ypx-n': num( cardYpx ),
			'--fsf-card-wpx-n': num( cardWpx ),
			'--fsf-card-rot': num( s.card_rot ) + 'deg',
			'--fsf-card-radius-n': num( s.card_radius ),
			'--fsf-csx-n': num( s.card_shadow_x ),
			'--fsf-csy-n': num( s.card_shadow_y ),
			'--fsf-csb-n': num( s.card_shadow_blur ),
			'--fsf-cs-color': rgba( s.card_shadow_color, s.card_shadow_opacity ),

			'--fsf-stand-w-n': num( standW ),
			'--fsf-stand-x-n': num( standX ),
			'--fsf-stand-y-n': num( standY ),
			'--fsf-stand-xpx-n': num( boxW * standX / 100 ),
			'--fsf-stand-ypx-n': num( boxH * standY / 100 ),
			'--fsf-stand-wpx-n': num( boxW * standW / 100 ),
			'--fsf-stand-fit': s.stand_fit === 'contain' ? 'contain' : 'cover',
			'--fsf-stand-objpos': objpos,
			'--fsf-stand-inset-n': num( s.stand_inset ),

			'--fsf-pad-n': num( s.inner_pad ),
			'--fsf-content-w-n': num( s.content_w ),
			'--fsf-content-wpx-n': num( boxW * parseFloat( s.content_w ) / 100 ),
			'--fsf-valign': valign,
			'--fsf-title-n': num( s.title_size ),
			'--fsf-title-color': s.title_color,
			'--fsf-title-below-color': s.title_below_color,
			'--fsf-title-below-align': [ 'left', 'center', 'right' ].indexOf( s.title_below_align ) !== -1 ? s.title_below_align : 'center',
			'--fsf-title-weight': num( s.title_weight ),
			'--fsf-desc-n': num( s.desc_size ),
			'--fsf-desc-color': s.desc_color,
			'--fsf-desc-lines': parseInt( s.desc_lines, 10 ) || 2,

			'--fsf-btn-n': num( s.btn_size ),
			'--fsf-btnr-n': num( s.btn_radius ),
			'--fsf-btn-bg': s.btn_bg,
			'--fsf-btn-color': s.btn_color,
			'--fsf-btn2-color': s.btn2_color
		};
	}

	/**
	 * Classi del box: specchio di FSF_Render::box_classes().
	 */
	function buildClasses( s ) {
		var classes = [ 'fsf-box', 'fsf-box--preview' ];

		if ( s.card_unit === 'px' ) {
			classes.push( 'fsf-box--pos-px' );
		}
		if ( s.card_w_unit === 'px' ) {
			classes.push( 'fsf-box--cw-px' );
		}
		if ( s.stand_side === 'left' ) {
			classes.push( 'fsf-box--flip' );
		}
		if ( on( s.stand_fade ) ) {
			classes.push( 'fsf-box--fade' );
		}
		if ( parseFloat( s.stand_inset ) > 0 ) {
			classes.push( 'fsf-box--stand-inset' );
		}
		if ( ! on( s.box_shadow ) ) {
			classes.push( 'fsf-box--no-shadow' );
		}
		if ( ! on( s.card_shadow ) ) {
			classes.push( 'fsf-box--no-card-shadow' );
		}
		if ( on( s.stand_free ) ) {
			classes.push( 'fsf-box--stand-free' );
		}
		if ( on( s.fluid ) ) {
			classes.push( 'fsf-box--fluid' );
		}
		if ( s.btn_post_style !== 'custom' ) {
			classes.push( 'fsf-box--ig-btn' );
		}

		return classes.join( ' ' );
	}

	/**
	 * Aggiorna l'anteprima.
	 */
	function refresh() {
		var $stage = $( '#fsf-preview-stage' );
		if ( ! $stage.length ) {
			return;
		}

		var $item = $stage.find( '.fsf-item' ).first();
		var $box = $stage.find( '.fsf-box' ).first();
		if ( ! $item.length ) {
			return;
		}

		var settings = currentSettings();
		var vars = buildVars( settings );
		var style = [];

		Object.keys( vars ).forEach( function ( key ) {
			style.push( key + ':' + vars[ key ] );
		} );

		$item.attr( 'style', style.join( ';' ) );
		$item.attr( 'class', on( settings.fluid ) ? 'fsf-item fsf-item--fluid' : 'fsf-item' );
		$box.attr( 'class', buildClasses( settings ) );

		if ( settings.label_post ) {
			$box.find( '.fsf-btn--primary .fsf-btn__label' ).text( settings.label_post );
		}
		if ( settings.label_reel ) {
			$box.find( '.fsf-btn--ghost .fsf-btn__label' ).text( settings.label_reel );
		}
		$box.find( '.fsf-ico' ).toggle( on( settings.btn_icon ) );

		// Titolo: dentro il box, sotto come didascalia, o nascosto.
		var showTitle = on( settings.show_title );
		var titleBelow = settings.title_pos === 'below';
		$box.find( '.fsf-content .fsf-title' ).prop( 'hidden', ! showTitle || titleBelow );
		$item.find( '.fsf-title--below' ).prop( 'hidden', ! showTitle || ! titleBelow );

		var $title = $( '#title' );
		if ( $title.length ) {
			$item.find( '.fsf-title' ).text( $title.val() || fsfAdmin.i18n.noTitle );
		}

		var $desc = $( '#fsf-desc' );
		var descText = $desc.length ? $.trim( $desc.val() ) : $.trim( $box.find( '.fsf-desc' ).text() );
		if ( $desc.length ) {
			$box.find( '.fsf-desc' ).text( descText );
		}
		$box.find( '.fsf-desc' ).prop( 'hidden', ! on( settings.show_desc ) || descText === '' );
	}

	/**
	 * Abilita/disabilita visivamente i campi di override.
	 */
	function syncOverrideState() {
		$( [
			[ '_fsf_override', '.fsf-override-fields' ],
			[ '_fsf_stand_ovr', '.fsf-override-stand' ],
			[ '_fsf_bg_ovr', '.fsf-override-bg' ]
		] ).each( function ( index, pair ) {
			var $checkbox = $( 'input[name="' + pair[ 0 ] + '"]' );
			if ( $checkbox.length ) {
				$( pair[ 1 ] ).toggleClass( 'is-disabled', ! $checkbox.is( ':checked' ) );
			}
		} );
	}

	/**
	 * Selettore immagini dalla libreria media.
	 */
	function initMedia() {
		var frames = {};

		$( '.fsf-media-field' ).each( function () {
			var $field = $( this );
			var key = $field.data( 'fsfMedia' );
			var $input = $field.find( '.fsf-media-input' );
			var $thumb = $field.find( '.fsf-media-thumb' );
			var $clear = $field.find( '.fsf-media-clear' );
			var target = key === '_fsf_card_id' ? '.fsf-card' : '.fsf-stand';
			var placeholder = key === '_fsf_card_id' ? fsfAdmin.placeholders.card : fsfAdmin.placeholders.stand;

			$field.find( '.fsf-media-pick' ).on( 'click', function ( event ) {
				event.preventDefault();

				if ( ! frames[ key ] ) {
					frames[ key ] = wp.media( {
						title: fsfAdmin.i18n.pickCard,
						button: { text: fsfAdmin.i18n.use },
						library: { type: 'image' },
						multiple: false
					} );

					frames[ key ].on( 'select', function () {
						var attachment = frames[ key ].state().get( 'selection' ).first().toJSON();
						var url = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
						var large = attachment.sizes && attachment.sizes.large ? attachment.sizes.large.url : attachment.url;

						$input.val( attachment.id );
						$thumb.removeClass( 'is-empty' ).html( $( '<img>' ).attr( { src: url, alt: '' } ) );
						$clear.show();

						var $img = $( '#fsf-preview-stage' ).find( target + ' img' );
						if ( $img.length ) {
							$img.attr( 'srcset', '' ).attr( 'src', large );
						}
						$( '#fsf-preview-stage' ).find( target ).prop( 'hidden', false );
					} );
				}

				frames[ key ].open();
			} );

			$clear.on( 'click', function ( event ) {
				event.preventDefault();
				$input.val( '' );
				$thumb.addClass( 'is-empty' ).html( $( '<span>' ).text( fsfAdmin.i18n.noImage ) );
				$clear.hide();

				var $img = $( '#fsf-preview-stage' ).find( target + ' img' );
				if ( $img.length ) {
					$img.attr( 'srcset', '' ).attr( 'src', placeholder );
				}
			} );
		} );
	}

	$( function () {
		if ( $.fn.wpColorPicker ) {
			$( '.fsf-color' ).wpColorPicker( {
				change: function () {
					var $input = $( this );
					window.setTimeout( function () {
						$input.trigger( 'fsf:changed' );
						refresh();
					}, 0 );
				},
				clear: refresh
			} );
		}

		initMedia();
		syncOverrideState();
		refresh();

		$( document ).on( 'input change', '[data-fsf-key], #title, #fsf-desc', function () {
			syncOverrideState();
			refresh();
		} );

		$( document ).on( 'click', '.fsf-copy-field', function () {
			var field = this;
			field.select();
			try {
				document.execCommand( 'copy' );
				$( field ).addClass( 'is-copied' );
				window.setTimeout( function () {
					$( field ).removeClass( 'is-copied' );
				}, 1200 );
			} catch ( e ) {
				// Niente clipboard: resta la selezione manuale.
			}
		} );
	} );
} )( jQuery );
