<?php
/**
 * Campi del singolo stand (immagini, testi, link, override aspetto).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FSF_Metabox {

	const NONCE = 'fsf_meta_nonce';

	/**
	 * Meta gestite dal plugin: chiave => tipo.
	 */
	public static function meta_map() {
		return array(
			'_fsf_card_id'    => 'int',
			'_fsf_stand_id'   => 'int',
			'_fsf_desc'       => 'textarea',
			'_fsf_ig_post'    => 'url',
			'_fsf_ig_reel'    => 'url',
			'_fsf_override'   => 'bool',
			'_fsf_card_x'     => 'float',
			'_fsf_card_y'     => 'float',
			'_fsf_card_w'     => 'float',
			'_fsf_card_rot'   => 'float',
			'_fsf_bg_ovr'     => 'bool',
			'_fsf_bg_color'   => 'color',
			'_fsf_bg_color2'  => 'color',
		);
	}

	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add' ) );
		add_action( 'save_post_' . FSF_POST_TYPE, array( __CLASS__, 'save' ), 10, 2 );
	}

	public static function add() {
		add_meta_box(
			'fsf_content',
			__( 'Contenuti dello stand', 'francy-stand-flip' ),
			array( __CLASS__, 'box_content' ),
			FSF_POST_TYPE,
			'normal',
			'high'
		);
		add_meta_box(
			'fsf_appearance',
			__( 'Aspetto di questo box (sovrascrive le impostazioni globali)', 'francy-stand-flip' ),
			array( __CLASS__, 'box_appearance' ),
			FSF_POST_TYPE,
			'normal',
			'default'
		);
		add_meta_box(
			'fsf_shortcode',
			__( 'Shortcode', 'francy-stand-flip' ),
			array( __CLASS__, 'box_shortcode' ),
			FSF_POST_TYPE,
			'side',
			'high'
		);
	}

	/**
	 * Valore meta con fallback.
	 *
	 * @param int    $post_id ID post.
	 * @param string $key     Chiave meta.
	 * @param mixed  $default Default.
	 * @return mixed
	 */
	public static function meta( $post_id, $key, $default = '' ) {
		$value = get_post_meta( $post_id, $key, true );
		return ( '' === $value || null === $value ) ? $default : $value;
	}

	public static function box_content( $post ) {
		wp_nonce_field( self::NONCE, self::NONCE );
		$card_id  = (int) self::meta( $post->ID, '_fsf_card_id', 0 );
		$stand_id = (int) self::meta( $post->ID, '_fsf_stand_id', 0 );
		$desc     = self::meta( $post->ID, '_fsf_desc', '' );
		$ig_post  = self::meta( $post->ID, '_fsf_ig_post', '' );
		$ig_reel  = self::meta( $post->ID, '_fsf_ig_reel', '' );
		?>
		<div class="fsf-meta-grid">
			<div class="fsf-meta-col">
				<?php
				self::media_field(
					'_fsf_card_id',
					__( 'Immagine carta Pokémon', 'francy-stand-flip' ),
					$card_id,
					__( 'PNG con trasparenza consigliato. Sborda dal box secondo le impostazioni.', 'francy-stand-flip' )
				);
				self::media_field(
					'_fsf_stand_id',
					__( 'Foto dello stand', 'francy-stand-flip' ),
					$stand_id,
					__( 'Se vuota viene usata l\'immagine in evidenza del post.', 'francy-stand-flip' )
				);
				?>
			</div>
			<div class="fsf-meta-col">
				<p>
					<label for="fsf-desc"><strong><?php esc_html_e( 'Breve descrizione', 'francy-stand-flip' ); ?></strong></label>
					<textarea id="fsf-desc" name="_fsf_desc" rows="4" class="widefat" maxlength="400"><?php echo esc_textarea( $desc ); ?></textarea>
					<span class="description"><?php esc_html_e( 'Tienila corta: viene troncata alle righe impostate nelle opzioni globali. Se vuota viene usato il riassunto del post.', 'francy-stand-flip' ); ?></span>
				</p>
				<p>
					<label for="fsf-ig-post"><strong><?php esc_html_e( 'Link al post Instagram', 'francy-stand-flip' ); ?></strong></label>
					<input type="url" id="fsf-ig-post" name="_fsf_ig_post" class="widefat" placeholder="https://www.instagram.com/p/..." value="<?php echo esc_attr( $ig_post ); ?>">
				</p>
				<p>
					<label for="fsf-ig-reel"><strong><?php esc_html_e( 'Link al reel Instagram (opzionale)', 'francy-stand-flip' ); ?></strong></label>
					<input type="url" id="fsf-ig-reel" name="_fsf_ig_reel" class="widefat" placeholder="https://www.instagram.com/reel/..." value="<?php echo esc_attr( $ig_reel ); ?>">
					<span class="description"><?php esc_html_e( 'Se lo lasci vuoto il pulsante del reel non viene mostrato.', 'francy-stand-flip' ); ?></span>
				</p>
			</div>
		</div>
		<?php
	}

	public static function box_appearance( $post ) {
		$settings = FSF_Settings::get();
		$override = (int) self::meta( $post->ID, '_fsf_override', 0 );
		$bg_ovr   = (int) self::meta( $post->ID, '_fsf_bg_ovr', 0 );
		$card_x   = self::meta( $post->ID, '_fsf_card_x', $settings['card_x'] );
		$card_y   = self::meta( $post->ID, '_fsf_card_y', $settings['card_y'] );
		$card_w   = self::meta( $post->ID, '_fsf_card_w', $settings['card_w'] );
		$card_rot = self::meta( $post->ID, '_fsf_card_rot', $settings['card_rot'] );
		$bg1      = self::meta( $post->ID, '_fsf_bg_color', $settings['bg_color'] );
		$bg2      = self::meta( $post->ID, '_fsf_bg_color2', $settings['bg_color2'] );
		$unit     = 'percent' === $settings['card_unit'] ? '%' : 'px';
		$w_unit   = 'percent' === $settings['card_w_unit'] ? '%' : 'px';
		?>
		<div class="fsf-meta-grid">
			<div class="fsf-meta-col">
				<p>
					<label>
						<input type="checkbox" name="_fsf_override" value="1" data-fsf-key="_fsf_override" <?php checked( 1, $override ); ?>>
						<strong><?php esc_html_e( 'Posiziona la carta solo per questo stand', 'francy-stand-flip' ); ?></strong>
					</label>
					<span class="description"><?php esc_html_e( 'Se disattivato valgono le coordinate globali.', 'francy-stand-flip' ); ?></span>
				</p>
				<div class="fsf-override-fields">
					<p>
						<label for="fsf-card-x"><?php printf( esc_html__( 'Posizione X (%s)', 'francy-stand-flip' ), esc_html( $unit ) ); ?></label>
						<input type="number" step="0.5" min="-200" max="200" id="fsf-card-x" name="_fsf_card_x" value="<?php echo esc_attr( $card_x ); ?>" data-fsf-key="card_x">
					</p>
					<p>
						<label for="fsf-card-y"><?php printf( esc_html__( 'Posizione Y (%s)', 'francy-stand-flip' ), esc_html( $unit ) ); ?></label>
						<input type="number" step="0.5" min="-200" max="200" id="fsf-card-y" name="_fsf_card_y" value="<?php echo esc_attr( $card_y ); ?>" data-fsf-key="card_y">
					</p>
					<p>
						<label for="fsf-card-w"><?php printf( esc_html__( 'Larghezza carta (%s)', 'francy-stand-flip' ), esc_html( $w_unit ) ); ?></label>
						<input type="number" step="0.5" min="1" max="1000" id="fsf-card-w" name="_fsf_card_w" value="<?php echo esc_attr( $card_w ); ?>" data-fsf-key="card_w">
					</p>
					<p>
						<label for="fsf-card-rot"><?php esc_html_e( 'Rotazione (deg)', 'francy-stand-flip' ); ?></label>
						<input type="number" step="0.5" min="-90" max="90" id="fsf-card-rot" name="_fsf_card_rot" value="<?php echo esc_attr( $card_rot ); ?>" data-fsf-key="card_rot">
					</p>
				</div>
				<hr>
				<p>
					<label>
						<input type="checkbox" name="_fsf_bg_ovr" value="1" data-fsf-key="_fsf_bg_ovr" <?php checked( 1, $bg_ovr ); ?>>
						<strong><?php esc_html_e( 'Sfondo personalizzato per questo stand', 'francy-stand-flip' ); ?></strong>
					</label>
				</p>
				<div class="fsf-override-bg">
					<p>
						<label for="fsf-bg-color"><?php esc_html_e( 'Colore sfondo', 'francy-stand-flip' ); ?></label><br>
						<input type="text" class="fsf-color" id="fsf-bg-color" name="_fsf_bg_color" value="<?php echo esc_attr( $bg1 ); ?>" data-fsf-key="bg_color" data-default-color="<?php echo esc_attr( $settings['bg_color'] ); ?>">
					</p>
					<p>
						<label for="fsf-bg-color2"><?php esc_html_e( 'Secondo colore (se sfumatura)', 'francy-stand-flip' ); ?></label><br>
						<input type="text" class="fsf-color" id="fsf-bg-color2" name="_fsf_bg_color2" value="<?php echo esc_attr( $bg2 ); ?>" data-fsf-key="bg_color2" data-default-color="<?php echo esc_attr( $settings['bg_color2'] ); ?>">
					</p>
				</div>
			</div>
			<div class="fsf-meta-col">
				<h4><?php esc_html_e( 'Anteprima live', 'francy-stand-flip' ); ?></h4>
				<div class="fsf-preview-stage" id="fsf-preview-stage">
					<?php echo FSF_Render::box( $post->ID, $settings, array( 'preview' => true ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<p class="description"><?php esc_html_e( 'Le immagini scelte qui sopra e i valori di posizione si aggiornano subito. Salva per rendere definitive le modifiche.', 'francy-stand-flip' ); ?></p>
			</div>
		</div>
		<?php
	}

	public static function box_shortcode( $post ) {
		$shortcode = '[francy_stand id="' . (int) $post->ID . '"]';
		?>
		<p><?php esc_html_e( 'Shortcode di questo stand:', 'francy-stand-flip' ); ?></p>
		<input type="text" class="widefat fsf-copy-field" readonly value="<?php echo esc_attr( $shortcode ); ?>" onclick="this.select();">
		<p class="description"><?php esc_html_e( 'Incollalo in un elemento "Codice" o "Testo" di Avada.', 'francy-stand-flip' ); ?></p>
		<hr>
		<p><?php esc_html_e( 'Tutti gli stand in griglia:', 'francy-stand-flip' ); ?></p>
		<input type="text" class="widefat fsf-copy-field" readonly value="[francy_stands]" onclick="this.select();">
		<?php
	}

	/**
	 * Campo di selezione immagine dalla libreria media.
	 *
	 * @param string $key   Nome meta.
	 * @param string $label Etichetta.
	 * @param int    $id    ID allegato corrente.
	 * @param string $help  Testo di aiuto.
	 */
	protected static function media_field( $key, $label, $id, $help = '' ) {
		$url = $id ? wp_get_attachment_image_url( $id, 'medium' ) : '';
		?>
		<div class="fsf-media-field" data-fsf-media="<?php echo esc_attr( $key ); ?>">
			<p><strong><?php echo esc_html( $label ); ?></strong></p>
			<div class="fsf-media-thumb <?php echo $url ? '' : 'is-empty'; ?>">
				<?php if ( $url ) : ?>
					<img src="<?php echo esc_url( $url ); ?>" alt="">
				<?php else : ?>
					<span><?php esc_html_e( 'Nessuna immagine', 'francy-stand-flip' ); ?></span>
				<?php endif; ?>
			</div>
			<input type="hidden" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $id ); ?>" class="fsf-media-input">
			<p>
				<button type="button" class="button fsf-media-pick"><?php esc_html_e( 'Scegli immagine', 'francy-stand-flip' ); ?></button>
				<button type="button" class="button-link fsf-media-clear" <?php echo $url ? '' : 'style="display:none"'; ?>><?php esc_html_e( 'Rimuovi', 'francy-stand-flip' ); ?></button>
			</p>
			<?php if ( $help ) : ?>
				<p class="description"><?php echo esc_html( $help ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Salvataggio delle meta.
	 *
	 * @param int     $post_id ID post.
	 * @param WP_Post $post    Post.
	 */
	public static function save( $post_id, $post ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! isset( $_POST[ self::NONCE ] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE ] ) ), self::NONCE ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		foreach ( self::meta_map() as $key => $type ) {
			$raw = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : null;

			switch ( $type ) {
				case 'bool':
					update_post_meta( $post_id, $key, empty( $raw ) ? 0 : 1 );
					break;

				case 'int':
					update_post_meta( $post_id, $key, absint( $raw ) );
					break;

				case 'float':
					if ( null === $raw || '' === $raw || ! is_numeric( $raw ) ) {
						delete_post_meta( $post_id, $key );
					} else {
						update_post_meta( $post_id, $key, round( (float) $raw, 2 ) );
					}
					break;

				case 'color':
					$color = sanitize_hex_color( is_string( $raw ) ? $raw : '' );
					if ( $color ) {
						update_post_meta( $post_id, $key, $color );
					} else {
						delete_post_meta( $post_id, $key );
					}
					break;

				case 'url':
					$url = esc_url_raw( is_string( $raw ) ? trim( $raw ) : '' );
					if ( $url ) {
						update_post_meta( $post_id, $key, $url );
					} else {
						delete_post_meta( $post_id, $key );
					}
					break;

				case 'textarea':
				default:
					$text = sanitize_textarea_field( is_string( $raw ) ? $raw : '' );
					if ( '' !== $text ) {
						update_post_meta( $post_id, $key, $text );
					} else {
						delete_post_meta( $post_id, $key );
					}
					break;
			}
		}
	}
}
