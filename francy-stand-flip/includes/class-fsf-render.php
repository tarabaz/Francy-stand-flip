<?php
/**
 * Generazione del markup e delle variabili CSS del box.
 *
 * Le variabili inline sono numeriche e senza unità: le unità vengono applicate
 * nel CSS moltiplicando per --fsf-u, che vale 1px come fallback e
 * "100cqw / larghezza box" dove le container query sono supportate.
 * Così tutto il box (testi, padding, carta, ombre) scala in proporzione.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FSF_Render {

	/**
	 * Icona Instagram inline.
	 */
	public static function ig_icon() {
		return '<svg class="fsf-ico" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor"><path d="M12 2.2c3.2 0 3.6 0 4.9.07 1.2.05 1.8.25 2.2.42.6.23 1 .5 1.5.95.45.45.72.9.95 1.5.17.4.37 1 .42 2.2.06 1.3.07 1.7.07 4.9s0 3.6-.07 4.9c-.05 1.2-.25 1.8-.42 2.2-.23.6-.5 1-.95 1.5-.45.45-.9.72-1.5.95-.4.17-1 .37-2.2.42-1.3.06-1.7.07-4.9.07s-3.6 0-4.9-.07c-1.2-.05-1.8-.25-2.2-.42-.6-.23-1-.5-1.5-.95-.45-.45-.72-.9-.95-1.5-.17-.4-.37-1-.42-2.2C2.2 15.6 2.2 15.2 2.2 12s0-3.6.07-4.9c.05-1.2.25-1.8.42-2.2.23-.6.5-1 .95-1.5.45-.45.9-.72 1.5-.95.4-.17 1-.37 2.2-.42C8.4 2.2 8.8 2.2 12 2.2zm0 1.98c-3.14 0-3.5.01-4.73.07-.95.04-1.42.2-1.75.32-.44.17-.72.37-1.03.68-.31.31-.51.6-.68 1.03-.13.33-.28.8-.32 1.75-.06 1.23-.07 1.6-.07 4.73s.01 3.5.07 4.73c.04.95.2 1.42.32 1.75.17.44.37.72.68 1.03.31.31.6.51 1.03.68.33.13.8.28 1.75.32 1.23.06 1.6.07 4.73.07s3.5-.01 4.73-.07c.95-.04 1.42-.2 1.75-.32.44-.17.72-.37 1.03-.68.31-.31.51-.6.68-1.03.13-.33.28-.8.32-1.75.06-1.23.07-1.6.07-4.73s-.01-3.5-.07-4.73c-.04-.95-.2-1.42-.32-1.75a2.8 2.8 0 0 0-.68-1.03 2.8 2.8 0 0 0-1.03-.68c-.33-.13-.8-.28-1.75-.32-1.23-.06-1.6-.07-4.73-.07zm0 3.37a4.45 4.45 0 1 1 0 8.9 4.45 4.45 0 0 1 0-8.9zm0 7.34a2.89 2.89 0 1 0 0-5.78 2.89 2.89 0 0 0 0 5.78zm5.66-7.53a1.04 1.04 0 1 1-2.08 0 1.04 1.04 0 0 1 2.08 0z"/></svg>';
	}

	/**
	 * Icona play (reel).
	 */
	public static function reel_icon() {
		return '<svg class="fsf-ico" viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="currentColor"><path d="M8.5 6.6v10.8c0 .5.55.8.97.54l8.4-5.4a.64.64 0 0 0 0-1.08l-8.4-5.4a.64.64 0 0 0-.97.54z"/></svg>';
	}

	/**
	 * Converte hex + opacità in rgba().
	 *
	 * @param string $hex     Colore hex.
	 * @param float  $opacity 0-100.
	 * @return string
	 */
	public static function rgba( $hex, $opacity ) {
		$hex = ltrim( (string) $hex, '#' );
		if ( 3 === strlen( $hex ) ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
			$hex = '000000';
		}
		$r     = hexdec( substr( $hex, 0, 2 ) );
		$g     = hexdec( substr( $hex, 2, 2 ) );
		$b     = hexdec( substr( $hex, 4, 2 ) );
		$alpha = max( 0, min( 100, (float) $opacity ) ) / 100;

		return sprintf( 'rgba(%d,%d,%d,%s)', $r, $g, $b, self::num( $alpha ) );
	}

	/**
	 * Numero formattato senza zeri inutili.
	 *
	 * @param mixed $value Valore.
	 * @return string
	 */
	public static function num( $value ) {
		$out = rtrim( rtrim( number_format( (float) $value, 3, '.', '' ), '0' ), '.' );
		return ( '' === $out || '-' === $out ) ? '0' : $out;
	}

	/**
	 * Dati normalizzati di uno stand.
	 *
	 * @param int   $post_id  ID stand (0 per demo).
	 * @param array $settings Impostazioni globali.
	 * @return array
	 */
	public static function stand_data( $post_id, $settings ) {
		$data = array(
			'id'        => (int) $post_id,
			'title'     => __( 'Nome dello stand', 'francy-stand-flip' ),
			'desc'      => __( 'Breve descrizione dello stand: cosa contiene, quali pezzi sono stampati e dipinti a mano, dove è stato esposto.', 'francy-stand-flip' ),
			'card_id'   => 0,
			'stand_id'  => 0,
			'ig_post'   => '',
			'ig_reel'   => '',
			'override'  => 0,
			'card_x'    => $settings['card_x'],
			'card_y'    => $settings['card_y'],
			'card_w'    => $settings['card_w'],
			'card_rot'  => $settings['card_rot'],
			'bg_ovr'    => 0,
			'bg_color'  => $settings['bg_color'],
			'bg_color2' => $settings['bg_color2'],
		);

		if ( ! $post_id ) {
			return $data;
		}

		$post = get_post( $post_id );
		if ( ! $post || FSF_POST_TYPE !== $post->post_type ) {
			return $data;
		}

		$desc = FSF_Metabox::meta( $post_id, '_fsf_desc', '' );
		if ( '' === $desc && has_excerpt( $post_id ) ) {
			$desc = get_the_excerpt( $post_id );
		}

		$stand_id = (int) FSF_Metabox::meta( $post_id, '_fsf_stand_id', 0 );
		if ( ! $stand_id ) {
			$stand_id = (int) get_post_thumbnail_id( $post_id );
		}

		$data['title']    = get_the_title( $post_id );
		$data['desc']     = $desc;
		$data['card_id']  = (int) FSF_Metabox::meta( $post_id, '_fsf_card_id', 0 );
		$data['stand_id'] = $stand_id;
		$data['ig_post']  = FSF_Metabox::meta( $post_id, '_fsf_ig_post', '' );
		$data['ig_reel']  = FSF_Metabox::meta( $post_id, '_fsf_ig_reel', '' );
		$data['override'] = (int) FSF_Metabox::meta( $post_id, '_fsf_override', 0 );
		$data['bg_ovr']   = (int) FSF_Metabox::meta( $post_id, '_fsf_bg_ovr', 0 );

		if ( $data['override'] ) {
			$data['card_x']   = FSF_Metabox::meta( $post_id, '_fsf_card_x', $settings['card_x'] );
			$data['card_y']   = FSF_Metabox::meta( $post_id, '_fsf_card_y', $settings['card_y'] );
			$data['card_w']   = FSF_Metabox::meta( $post_id, '_fsf_card_w', $settings['card_w'] );
			$data['card_rot'] = FSF_Metabox::meta( $post_id, '_fsf_card_rot', $settings['card_rot'] );
		}
		if ( $data['bg_ovr'] ) {
			$data['bg_color']  = FSF_Metabox::meta( $post_id, '_fsf_bg_color', $settings['bg_color'] );
			$data['bg_color2'] = FSF_Metabox::meta( $post_id, '_fsf_bg_color2', $settings['bg_color2'] );
		}

		return $data;
	}

	/**
	 * Variabili CSS del box (numeriche + colori).
	 *
	 * @param array $settings Impostazioni globali.
	 * @param array $data     Dati stand.
	 * @return array
	 */
	public static function box_vars( $settings, $data ) {
		$bg = 'gradient' === $settings['bg_type']
			? sprintf( 'linear-gradient(%sdeg, %s 0%%, %s 100%%)', self::num( $settings['bg_angle'] ), $data['bg_color'], $data['bg_color2'] )
			: $data['bg_color'];

		return array(
			'--fsf-boxw'         => self::num( $settings['box_w'] ),
			'--fsf-box-max'      => self::num( $settings['box_w'] ) . 'px',
			'--fsf-ratio'        => self::num( $settings['box_w'] ) . ' / ' . self::num( $settings['box_h'] ),
			'--fsf-radius-n'     => self::num( $settings['radius'] ),
			'--fsf-bg'           => $bg,
			'--fsf-border-n'     => self::num( $settings['border_w'] ),
			'--fsf-border-color' => self::rgba( $settings['border_color'], $settings['border_opacity'] ),
			'--fsf-bsy-n'        => self::num( $settings['box_shadow_y'] ),
			'--fsf-bsb-n'        => self::num( $settings['box_shadow_blur'] ),
			'--fsf-bs-color'     => self::rgba( $settings['box_shadow_color'], $settings['box_shadow_opacity'] ),

			'--fsf-card-x-n'     => self::num( $data['card_x'] ),
			'--fsf-card-y-n'     => self::num( $data['card_y'] ),
			'--fsf-card-w-n'     => self::num( $data['card_w'] ),
			'--fsf-card-rot'     => self::num( $data['card_rot'] ) . 'deg',
			'--fsf-card-radius-n' => self::num( $settings['card_radius'] ),
			'--fsf-csx-n'        => self::num( $settings['card_shadow_x'] ),
			'--fsf-csy-n'        => self::num( $settings['card_shadow_y'] ),
			'--fsf-csb-n'        => self::num( $settings['card_shadow_blur'] ),
			'--fsf-cs-color'     => self::rgba( $settings['card_shadow_color'], $settings['card_shadow_opacity'] ),

			'--fsf-stand-w'      => self::num( $settings['stand_w'] ) . '%',
			'--fsf-stand-fit'    => 'contain' === $settings['stand_fit'] ? 'contain' : 'cover',
			'--fsf-stand-pos'    => $settings['stand_pos'],
			'--fsf-stand-inset-n' => self::num( $settings['stand_inset'] ),

			'--fsf-pad-n'        => self::num( $settings['inner_pad'] ),
			'--fsf-content-w'    => self::num( $settings['content_w'] ) . '%',
			'--fsf-valign'       => in_array( $settings['content_valign'], array( 'start', 'center', 'end' ), true ) ? 'flex-' . $settings['content_valign'] : 'flex-end',
			'--fsf-title-n'      => self::num( $settings['title_size'] ),
			'--fsf-title-color'  => $settings['title_color'],
			'--fsf-title-weight' => self::num( $settings['title_weight'] ),
			'--fsf-desc-n'       => self::num( $settings['desc_size'] ),
			'--fsf-desc-color'   => $settings['desc_color'],
			'--fsf-desc-lines'   => (int) $settings['desc_lines'],

			'--fsf-btn-n'        => self::num( $settings['btn_size'] ),
			'--fsf-btnr-n'       => self::num( $settings['btn_radius'] ),
			'--fsf-btn-bg'       => $settings['btn_bg'],
			'--fsf-btn-color'    => $settings['btn_color'],
			'--fsf-btn2-color'   => $settings['btn2_color'],
		);
	}

	/**
	 * Classi del box in base alle scelte che non sono numeriche.
	 *
	 * @param array $settings Impostazioni.
	 * @param array $args     Argomenti render.
	 * @return array
	 */
	public static function box_classes( $settings, $args ) {
		$classes = array( 'fsf-box' );

		if ( 'px' === $settings['card_unit'] ) {
			$classes[] = 'fsf-box--pos-px';
		}
		if ( 'px' === $settings['card_w_unit'] ) {
			$classes[] = 'fsf-box--cw-px';
		}
		if ( 'left' === $settings['stand_side'] ) {
			$classes[] = 'fsf-box--flip';
		}
		if ( $settings['stand_fade'] ) {
			$classes[] = 'fsf-box--fade';
		}
		if ( (float) $settings['stand_inset'] > 0 ) {
			$classes[] = 'fsf-box--stand-inset';
		}
		if ( ! $settings['box_shadow'] ) {
			$classes[] = 'fsf-box--no-shadow';
		}
		if ( ! $settings['card_shadow'] ) {
			$classes[] = 'fsf-box--no-card-shadow';
		}
		if ( ! empty( $args['preview'] ) ) {
			$classes[] = 'fsf-box--preview';
		}

		return $classes;
	}

	/**
	 * Variabili CSS della griglia: colonne, gap e margine per la carta che sborda.
	 *
	 * @param array $settings Impostazioni globali.
	 * @param array $atts     Attributi già normalizzati (columns, gap).
	 * @return array
	 */
	public static function grid_vars( $settings, $atts = array() ) {
		$cols  = isset( $atts['columns'] ) ? (int) $atts['columns'] : (int) $settings['cols'];
		$cols  = max( 1, min( 6, $cols ) );
		$gap   = isset( $atts['gap'] ) ? (float) $atts['gap'] : (float) $settings['gap'];
		$box_w = max( 1, (float) $settings['box_w'] );
		$box_h = max( 1, (float) $settings['box_h'] );

		$card_w = 'percent' === $settings['card_w_unit'] ? $box_w * (float) $settings['card_w'] / 100 : (float) $settings['card_w'];
		$card_h = $card_w * 1.4; // proporzione tipica di una carta Pokémon (63x88 mm).
		$card_x = 'percent' === $settings['card_unit'] ? $box_w * (float) $settings['card_x'] / 100 : (float) $settings['card_x'];
		$card_y = 'percent' === $settings['card_unit'] ? $box_h * (float) $settings['card_y'] / 100 : (float) $settings['card_y'];

		// Ingombro extra dovuto alla rotazione (differenza tra bounding box ruotata e originale).
		$rot   = deg2rad( abs( (float) $settings['card_rot'] ) );
		$rot_w = max( 0, ( $card_w * cos( $rot ) + $card_h * sin( $rot ) - $card_w ) / 2 );
		$rot_h = max( 0, ( $card_h * cos( $rot ) + $card_w * sin( $rot ) - $card_h ) / 2 );

		if ( $settings['pad_auto'] ) {
			$pad_l = max( 0, -( $card_x - $rot_w ) ) + 8;
			$pad_t = max( 0, -( $card_y - $rot_h ) ) + 8;
			$pad_r = max( 0, ( $card_x + $card_w + $rot_w ) - $box_w ) + 8;
			$pad_b = max( 0, ( $card_y + $card_h + $rot_h ) - $box_h ) + 8;
		} else {
			$pad_l = (float) $settings['grid_pad'];
			$pad_t = $pad_l;
			$pad_r = $pad_l;
			$pad_b = $pad_l;
		}

		return array(
			'--fsf-cols'   => $cols,
			'--fsf-cols-t' => max( 1, min( (int) $settings['cols_tablet'], $cols ) ),
			'--fsf-cols-m' => max( 1, min( (int) $settings['cols_mobile'], $cols ) ),
			'--fsf-gap'    => self::num( max( 0, $gap ) ) . 'px',
			'--fsf-gpad-t' => self::num( ceil( $pad_t ) ) . 'px',
			'--fsf-gpad-r' => self::num( ceil( $pad_r ) ) . 'px',
			'--fsf-gpad-b' => self::num( ceil( $pad_b ) ) . 'px',
			'--fsf-gpad-l' => self::num( ceil( $pad_l ) ) . 'px',
		);
	}

	/**
	 * Trasforma un array di variabili in attributo style.
	 *
	 * @param array $vars Variabili.
	 * @return string
	 */
	public static function style_attr( $vars ) {
		$out = array();
		foreach ( $vars as $key => $value ) {
			$out[] = $key . ':' . $value;
		}
		return implode( ';', $out );
	}

	/**
	 * URL immagine placeholder per l'anteprima.
	 *
	 * @param string $type card|stand.
	 * @return string
	 */
	public static function placeholder( $type ) {
		return FSF_URL . 'assets/img/' . ( 'card' === $type ? 'placeholder-card.svg' : 'placeholder-stand.svg' );
	}

	/**
	 * Primo stand pubblicato, usato come campione nell'anteprima delle impostazioni.
	 *
	 * @return int
	 */
	public static function sample_stand() {
		$ids = get_posts(
			array(
				'post_type'      => FSF_POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'orderby'        => 'menu_order date',
				'order'          => 'ASC',
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);
		return $ids ? (int) $ids[0] : 0;
	}

	/**
	 * Markup di un singolo box (wrapper .fsf-item + box).
	 *
	 * @param int   $post_id  ID stand (0 = demo).
	 * @param array $settings Impostazioni globali (null = correnti).
	 * @param array $args     preview => bool, class => string.
	 * @return string
	 */
	public static function box( $post_id, $settings = null, $args = array() ) {
		$settings = $settings ? $settings : FSF_Settings::get();
		$args     = wp_parse_args(
			$args,
			array(
				'preview' => false,
				'class'   => '',
			)
		);
		$preview = (bool) $args['preview'];
		$data    = self::stand_data( $post_id, $settings );

		$item_classes = array( 'fsf-item' );
		if ( $args['class'] ) {
			foreach ( explode( ' ', $args['class'] ) as $class ) {
				$class = sanitize_html_class( $class );
				if ( $class ) {
					$item_classes[] = $class;
				}
			}
		}

		$target = '_self' === $settings['link_target'] ? '_self' : '_blank';
		$rel    = '_blank' === $target ? ' rel="noopener noreferrer"' : '';

		$stand_html = '';
		if ( $data['stand_id'] ) {
			$stand_html = wp_get_attachment_image(
				$data['stand_id'],
				'large',
				false,
				array(
					'class'    => 'fsf-stand__img',
					'loading'  => $preview ? 'eager' : 'lazy',
					'decoding' => 'async',
				)
			);
		} elseif ( $preview ) {
			$stand_html = '<img class="fsf-stand__img" src="' . esc_url( self::placeholder( 'stand' ) ) . '" alt="">';
		}

		$card_html = '';
		if ( $data['card_id'] ) {
			$card_html = wp_get_attachment_image(
				$data['card_id'],
				'medium_large',
				false,
				array(
					'class'    => 'fsf-card__img',
					'loading'  => $preview ? 'eager' : 'lazy',
					'decoding' => 'async',
				)
			);
		} elseif ( $preview ) {
			$card_html = '<img class="fsf-card__img" src="' . esc_url( self::placeholder( 'card' ) ) . '" alt="">';
		}

		ob_start();
		?>
		<div class="<?php echo esc_attr( implode( ' ', $item_classes ) ); ?>" style="<?php echo esc_attr( self::style_attr( self::box_vars( $settings, $data ) ) ); ?>"<?php echo $data['id'] ? ' data-fsf-id="' . (int) $data['id'] . '"' : ''; ?>>
			<article class="<?php echo esc_attr( implode( ' ', self::box_classes( $settings, $args ) ) ); ?>">
				<div class="fsf-box__inner">
					<div class="fsf-stand"><?php echo $stand_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>

					<div class="fsf-content">
						<h3 class="fsf-title"><?php echo esc_html( $data['title'] ); ?></h3>
						<p class="fsf-desc"<?php echo $data['desc'] ? '' : ' hidden'; ?>><?php echo esc_html( $data['desc'] ); ?></p>

						<div class="fsf-actions">
							<a class="fsf-btn fsf-btn--primary"
								href="<?php echo esc_url( $data['ig_post'] ? $data['ig_post'] : '#' ); ?>"
								target="<?php echo esc_attr( $target ); ?>"<?php echo $rel; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php echo ( $data['ig_post'] || $preview ) ? '' : 'hidden'; ?>>
								<?php
								if ( $settings['btn_icon'] ) {
									echo self::ig_icon(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								}
								?>
								<span class="fsf-btn__label"><?php echo esc_html( $settings['label_post'] ); ?></span>
								<span class="screen-reader-text"> — <?php echo esc_html( $data['title'] ); ?></span>
							</a>

							<a class="fsf-btn fsf-btn--ghost"
								href="<?php echo esc_url( $data['ig_reel'] ? $data['ig_reel'] : '#' ); ?>"
								target="<?php echo esc_attr( $target ); ?>"<?php echo $rel; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php echo ( $data['ig_reel'] || $preview ) ? '' : 'hidden'; ?>>
								<?php
								if ( $settings['btn_icon'] ) {
									echo self::reel_icon(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								}
								?>
								<span class="fsf-btn__label"><?php echo esc_html( $settings['label_reel'] ); ?></span>
								<span class="screen-reader-text"> — <?php echo esc_html( $data['title'] ); ?></span>
							</a>
						</div>
					</div>
				</div>

				<div class="fsf-card"<?php echo $card_html ? '' : ' hidden'; ?>><?php echo $card_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			</article>
		</div>
		<?php
		return trim( ob_get_clean() );
	}

	/**
	 * Wrapper griglia attorno a uno o più box.
	 *
	 * @param string $inner    HTML dei box.
	 * @param array  $settings Impostazioni.
	 * @param array  $atts     Attributi (columns, gap, class).
	 * @return string
	 */
	public static function grid( $inner, $settings, $atts = array() ) {
		$classes = array( 'fsf-grid' );
		if ( ! empty( $atts['class'] ) ) {
			foreach ( explode( ' ', $atts['class'] ) as $class ) {
				$class = sanitize_html_class( $class );
				if ( $class ) {
					$classes[] = $class;
				}
			}
		}

		return sprintf(
			'<div class="%1$s" style="%2$s">%3$s</div>',
			esc_attr( implode( ' ', $classes ) ),
			esc_attr( self::style_attr( self::grid_vars( $settings, $atts ) ) ),
			$inner
		);
	}
}
