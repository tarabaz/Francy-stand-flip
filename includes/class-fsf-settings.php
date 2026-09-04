<?php
/**
 * Impostazioni globali del plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FSF_Settings {

	/**
	 * Cache delle impostazioni risolte.
	 *
	 * @var array|null
	 */
	protected static $cache = null;

	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'register' ) );
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
	}

	/**
	 * Valori di default.
	 */
	public static function defaults() {
		return array(
			// Box.
			'box_w'              => 560,
			'box_h'              => 320,
			'fluid'              => 0,
			'min_w'              => 380,
			'radius'             => 24,
			'bg_type'            => 'gradient',
			'bg_color'           => '#1c1033',
			'bg_color2'          => '#40206e',
			'bg_angle'           => 135,
			'border_w'           => 0,
			'border_color'       => '#ffffff',
			'border_opacity'     => 15,
			'box_shadow'         => 1,
			'box_shadow_y'       => 18,
			'box_shadow_blur'    => 40,
			'box_shadow_color'   => '#000000',
			'box_shadow_opacity' => 35,

			// Carta Pokémon.
			'card_unit'           => 'percent',
			'card_x'              => -10,
			'card_y'              => -18,
			'card_w_unit'         => 'percent',
			'card_w'              => 25,
			'card_rot'            => -8,
			'card_radius'         => 10,
			'card_shadow'         => 1,
			'card_shadow_x'       => 6,
			'card_shadow_y'       => 12,
			'card_shadow_blur'    => 16,
			'card_shadow_color'   => '#000000',
			'card_shadow_opacity' => 45,

			// Immagine stand.
			'stand_side'  => 'right',
			'stand_w'     => 44,
			'stand_fit'   => 'cover',
			'stand_pos'   => 'center',
			'stand_inset' => 0,
			'stand_fade'  => 1,
			'stand_free'  => 0,
			'stand_x'     => -4,
			'stand_y'     => -8,

			// Contenuti.
			'inner_pad'      => 24,
			'content_w'      => 64,
			'content_valign' => 'end',
			'show_title'     => 1,
			'show_desc'      => 1,
			'title_pos'      => 'inside',
			'title_below_align' => 'center',
			'title_below_color' => '#1c1033',
			'title_size'     => 24,
			'title_color'    => '#ffffff',
			'title_weight'   => 700,
			'desc_size'      => 14,
			'desc_color'     => '#ded7ee',
			'desc_lines'     => 2,

			// Pulsanti.
			'btn_post_style' => 'instagram',
			'btn_size'      => 12,
			'btn_radius'    => 999,
			'btn_bg'        => '#ffffff',
			'btn_color'     => '#1c1033',
			'btn2_color'    => '#ffffff',
			'btn_icon'      => 1,
			'label_post'    => 'Vedi il post',
			'label_reel'    => 'Guarda il reel',
			'link_target'   => '_blank',

			// Griglia.
			'cols'        => 2,
			'cols_tablet' => 2,
			'cols_mobile' => 1,
			'gap'         => 44,
			'pad_auto'    => 1,
			'grid_pad'    => 40,
		);
	}

	/**
	 * Definizione dei campi, raggruppati per sezione.
	 */
	public static function fields() {
		return array(
			'box'     => array(
				'label'  => __( 'Box', 'francy-stand-flip' ),
				'fields' => array(
					'box_w'              => array( 'type' => 'number', 'label' => 'Larghezza massima box (px)', 'min' => 200, 'max' => 1400, 'step' => 1, 'help' => 'Il box resta proporzionale: sotto questa larghezza si rimpicciolisce mantenendo lo stesso aspetto.' ),
					'box_h'              => array( 'type' => 'number', 'label' => 'Altezza box (px)', 'min' => 120, 'max' => 1200, 'step' => 1, 'help' => 'Usata come proporzione (larghezza / altezza), così su mobile non si deforma. Con la larghezza dinamica diventa l\'altezza fissa del box.' ),
					'fluid'              => array( 'type' => 'checkbox', 'label' => 'Larghezza dinamica', 'help' => 'Il box occupa tutto lo spazio disponibile: niente larghezza massima, solo una minima. Altezza e dimensioni dei testi restano quelle di progetto, la foto dello stand si allarga. Utile per shortcode messi uno sotto l\'altro a tutta pagina.' ),
					'min_w'              => array( 'type' => 'number', 'label' => 'Larghezza minima (px)', 'min' => 200, 'max' => 1200, 'step' => 1, 'help' => 'Usata solo con la larghezza dinamica attiva.' ),
					'radius'             => array( 'type' => 'number', 'label' => 'Arrotondamento angoli (px)', 'min' => 0, 'max' => 200, 'step' => 1 ),
					'bg_type'            => array( 'type' => 'select', 'label' => 'Tipo di sfondo', 'options' => array( 'solid' => 'Colore pieno', 'gradient' => 'Sfumatura' ) ),
					'bg_color'           => array( 'type' => 'color', 'label' => 'Colore sfondo' ),
					'bg_color2'          => array( 'type' => 'color', 'label' => 'Secondo colore (sfumatura)' ),
					'bg_angle'           => array( 'type' => 'number', 'label' => 'Angolo sfumatura (deg)', 'min' => 0, 'max' => 360, 'step' => 1 ),
					'border_w'           => array( 'type' => 'number', 'label' => 'Spessore bordo (px)', 'min' => 0, 'max' => 20, 'step' => 1 ),
					'border_color'       => array( 'type' => 'color', 'label' => 'Colore bordo' ),
					'border_opacity'     => array( 'type' => 'number', 'label' => 'Opacità bordo (%)', 'min' => 0, 'max' => 100, 'step' => 1 ),
					'box_shadow'         => array( 'type' => 'checkbox', 'label' => 'Ombra sul box' ),
					'box_shadow_y'       => array( 'type' => 'number', 'label' => 'Ombra box: offset Y (px)', 'min' => -100, 'max' => 100, 'step' => 1 ),
					'box_shadow_blur'    => array( 'type' => 'number', 'label' => 'Ombra box: sfocatura (px)', 'min' => 0, 'max' => 200, 'step' => 1 ),
					'box_shadow_color'   => array( 'type' => 'color', 'label' => 'Ombra box: colore' ),
					'box_shadow_opacity' => array( 'type' => 'number', 'label' => 'Ombra box: opacità (%)', 'min' => 0, 'max' => 100, 'step' => 1 ),
				),
			),
			'card'    => array(
				'label'  => __( 'Carta Pokémon', 'francy-stand-flip' ),
				'fields' => array(
					'card_unit'           => array( 'type' => 'select', 'label' => 'Unità posizione', 'options' => array( 'percent' => '% del box (consigliato)', 'px' => 'px (scalano col box)' ), 'help' => 'Le coordinate sono sempre relative al box: 0/0 = angolo in alto a sinistra del box. Valori negativi fanno sbordare la carta.' ),
					'card_x'              => array( 'type' => 'number', 'label' => 'Posizione X', 'min' => -200, 'max' => 200, 'step' => 0.5 ),
					'card_y'              => array( 'type' => 'number', 'label' => 'Posizione Y', 'min' => -200, 'max' => 200, 'step' => 0.5 ),
					'card_w_unit'         => array( 'type' => 'select', 'label' => 'Unità larghezza carta', 'options' => array( 'percent' => '% della larghezza box', 'px' => 'px' ) ),
					'card_w'              => array( 'type' => 'number', 'label' => 'Larghezza carta', 'min' => 1, 'max' => 1000, 'step' => 0.5 ),
					'card_rot'            => array( 'type' => 'number', 'label' => 'Rotazione (deg)', 'min' => -90, 'max' => 90, 'step' => 0.5 ),
					'card_radius'         => array( 'type' => 'number', 'label' => 'Arrotondamento carta (px)', 'min' => 0, 'max' => 80, 'step' => 1 ),
					'card_shadow'         => array( 'type' => 'checkbox', 'label' => 'Ombra sulla carta' ),
					'card_shadow_x'       => array( 'type' => 'number', 'label' => 'Ombra carta: offset X (px)', 'min' => -100, 'max' => 100, 'step' => 1 ),
					'card_shadow_y'       => array( 'type' => 'number', 'label' => 'Ombra carta: offset Y (px)', 'min' => -100, 'max' => 100, 'step' => 1 ),
					'card_shadow_blur'    => array( 'type' => 'number', 'label' => 'Ombra carta: sfocatura (px)', 'min' => 0, 'max' => 120, 'step' => 1 ),
					'card_shadow_color'   => array( 'type' => 'color', 'label' => 'Ombra carta: colore' ),
					'card_shadow_opacity' => array( 'type' => 'number', 'label' => 'Ombra carta: opacità (%)', 'min' => 0, 'max' => 100, 'step' => 1 ),
				),
			),
			'stand'   => array(
				'label'  => __( 'Immagine stand', 'francy-stand-flip' ),
				'fields' => array(
					'stand_side'  => array( 'type' => 'select', 'label' => 'Lato immagine stand', 'options' => array( 'right' => 'Destra', 'left' => 'Sinistra' ), 'help' => 'La carta resta sul lato opposto ai testi.' ),
					'stand_w'     => array( 'type' => 'number', 'label' => 'Larghezza area immagine (% del box)', 'min' => 10, 'max' => 90, 'step' => 1 ),
					'stand_fit'   => array( 'type' => 'select', 'label' => 'Adattamento immagine', 'options' => array( 'cover' => 'Riempi (cover)', 'contain' => 'Contieni (contain)' ) ),
					'stand_pos'   => array( 'type' => 'select', 'label' => 'Messa a fuoco immagine', 'options' => array( 'center' => 'Centro', 'top' => 'Alto', 'bottom' => 'Basso', 'left' => 'Sinistra', 'right' => 'Destra' ) ),
					'stand_inset' => array( 'type' => 'number', 'label' => 'Margine interno immagine (px)', 'min' => 0, 'max' => 80, 'step' => 1 ),
					'stand_fade'  => array( 'type' => 'checkbox', 'label' => 'Sfumatura di raccordo verso i testi', 'help' => 'Non si applica alla foto libera.' ),
					'stand_free'  => array( 'type' => 'checkbox', 'label' => 'Foto libera: può sbordare dal box', 'help' => 'Come la carta. La foto non viene più ritagliata nel riquadro: usa le coordinate qui sotto e conviene una foto scontornata in PNG. La carta resta comunque dietro la foto.' ),
					'stand_x'     => array( 'type' => 'number', 'label' => 'Foto libera: distanza dal lato (%)', 'min' => -100, 'max' => 100, 'step' => 0.5, 'help' => 'Distanza dal lato scelto sopra. Negativa = la foto esce dal box.' ),
					'stand_y'     => array( 'type' => 'number', 'label' => 'Foto libera: distanza dall\'alto (%)', 'min' => -100, 'max' => 100, 'step' => 0.5 ),
				),
			),
			'content' => array(
				'label'  => __( 'Testi', 'francy-stand-flip' ),
				'fields' => array(
					'inner_pad'      => array( 'type' => 'number', 'label' => 'Padding interno (px)', 'min' => 0, 'max' => 120, 'step' => 1 ),
					'content_w'      => array( 'type' => 'number', 'label' => 'Larghezza colonna testi (% del box)', 'min' => 20, 'max' => 100, 'step' => 1 ),
					'content_valign' => array( 'type' => 'select', 'label' => 'Allineamento verticale testi', 'options' => array( 'start' => 'Alto', 'center' => 'Centro', 'end' => 'Basso' ) ),
					'show_title'     => array( 'type' => 'checkbox', 'label' => 'Mostra il titolo' ),
					'show_desc'      => array( 'type' => 'checkbox', 'label' => 'Mostra la descrizione', 'help' => 'Togliendo titolo e/o descrizione il resto si ricompone da solo: restano i pulsanti in fondo al box.' ),
					'title_pos'      => array( 'type' => 'select', 'label' => 'Posizione del titolo', 'options' => array( 'inside' => 'Dentro il box', 'below' => 'Sotto il box (didascalia)' ), 'help' => 'Sotto il box il titolo sta sulla pagina, non sul box: per questo ha un colore suo.' ),
					'title_below_align' => array( 'type' => 'select', 'label' => 'Titolo sotto: allineamento', 'options' => array( 'left' => 'A sinistra', 'center' => 'Centrato', 'right' => 'A destra' ) ),
					'title_below_color' => array( 'type' => 'color', 'label' => 'Titolo sotto: colore' ),
					'title_size'     => array( 'type' => 'number', 'label' => 'Titolo: dimensione (px)', 'min' => 8, 'max' => 80, 'step' => 1 ),
					'title_weight'   => array( 'type' => 'select', 'label' => 'Titolo: peso', 'options' => array( '400' => 'Normale', '500' => 'Medio', '600' => 'Semi-bold', '700' => 'Bold', '800' => 'Extra-bold', '900' => 'Black' ) ),
					'title_color'    => array( 'type' => 'color', 'label' => 'Titolo: colore' ),
					'desc_size'      => array( 'type' => 'number', 'label' => 'Descrizione: dimensione (px)', 'min' => 8, 'max' => 40, 'step' => 1 ),
					'desc_color'     => array( 'type' => 'color', 'label' => 'Descrizione: colore' ),
					'desc_lines'     => array( 'type' => 'number', 'label' => 'Descrizione: righe massime', 'min' => 1, 'max' => 10, 'step' => 1, 'help' => 'Oltre questo numero il testo viene troncato con i puntini: serve a tenere il box sempre della stessa dimensione.' ),
				),
			),
			'buttons' => array(
				'label'  => __( 'Pulsanti Instagram', 'francy-stand-flip' ),
				'fields' => array(
					'btn_post_style' => array( 'type' => 'select', 'label' => 'Stile pulsante post', 'options' => array( 'instagram' => 'Colori Instagram (sfumatura)', 'custom' => 'Colori personalizzati' ) ),
					'btn_size'    => array( 'type' => 'number', 'label' => 'Dimensione testo (px)', 'min' => 8, 'max' => 28, 'step' => 1 ),
					'btn_radius'  => array( 'type' => 'number', 'label' => 'Arrotondamento (px)', 'min' => 0, 'max' => 999, 'step' => 1 ),
					'btn_bg'      => array( 'type' => 'color', 'label' => 'Pulsante post: sfondo', 'help' => 'Usato solo con lo stile "colori personalizzati".' ),
					'btn_color'   => array( 'type' => 'color', 'label' => 'Pulsante post: testo' ),
					'btn2_color'  => array( 'type' => 'color', 'label' => 'Pulsante reel: testo e bordo' ),
					'btn_icon'    => array( 'type' => 'checkbox', 'label' => 'Mostra icona Instagram' ),
					'label_post'  => array( 'type' => 'text', 'label' => 'Etichetta pulsante post' ),
					'label_reel'  => array( 'type' => 'text', 'label' => 'Etichetta pulsante reel' ),
					'link_target' => array( 'type' => 'select', 'label' => 'Apertura link', 'options' => array( '_blank' => 'Nuova scheda', '_self' => 'Stessa scheda' ) ),
				),
			),
			'grid'    => array(
				'label'  => __( 'Griglia (shortcode globale)', 'francy-stand-flip' ),
				'fields' => array(
					'cols'        => array( 'type' => 'number', 'label' => 'Colonne desktop', 'min' => 1, 'max' => 6, 'step' => 1 ),
					'cols_tablet' => array( 'type' => 'number', 'label' => 'Colonne tablet (<1024px)', 'min' => 1, 'max' => 4, 'step' => 1 ),
					'cols_mobile' => array( 'type' => 'number', 'label' => 'Colonne mobile (<768px)', 'min' => 1, 'max' => 3, 'step' => 1 ),
					'gap'         => array( 'type' => 'number', 'label' => 'Spazio tra i box (px)', 'min' => 0, 'max' => 200, 'step' => 1 ),
					'pad_auto'    => array( 'type' => 'checkbox', 'label' => 'Spazio automatico per la carta che sborda', 'help' => 'Calcola da solo il margine intorno alla griglia in base a posizione e dimensione della carta. Consigliato.' ),
					'grid_pad'    => array( 'type' => 'number', 'label' => 'Margine manuale griglia (px)', 'min' => 0, 'max' => 200, 'step' => 1, 'help' => 'Usato solo se lo spazio automatico è disattivato.' ),
				),
			),
		);
	}

	/**
	 * Impostazioni correnti unite ai default.
	 */
	public static function get() {
		if ( null === self::$cache ) {
			$saved = get_option( FSF_OPTION, array() );
			if ( ! is_array( $saved ) ) {
				$saved = array();
			}
			self::$cache = wp_parse_args( $saved, self::defaults() );
		}
		return self::$cache;
	}

	public static function register() {
		register_setting(
			'fsf_settings_group',
			FSF_OPTION,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize' ),
				'default'           => self::defaults(),
			)
		);
	}

	/**
	 * Sanitizzazione di tutti i campi in base al tipo dichiarato.
	 *
	 * @param mixed $input Dati dal form.
	 * @return array
	 */
	public static function sanitize( $input ) {
		$defaults = self::defaults();
		$out      = $defaults;

		if ( ! is_array( $input ) ) {
			return $out;
		}

		foreach ( self::fields() as $section ) {
			foreach ( $section['fields'] as $key => $field ) {
				switch ( $field['type'] ) {
					case 'checkbox':
						$out[ $key ] = empty( $input[ $key ] ) ? 0 : 1;
						break;

					case 'color':
						$color       = isset( $input[ $key ] ) ? sanitize_hex_color( $input[ $key ] ) : '';
						$out[ $key ] = $color ? $color : $defaults[ $key ];
						break;

					case 'number':
						if ( ! isset( $input[ $key ] ) || '' === $input[ $key ] || ! is_numeric( $input[ $key ] ) ) {
							$out[ $key ] = $defaults[ $key ];
							break;
						}
						$val = (float) $input[ $key ];
						if ( isset( $field['min'] ) ) {
							$val = max( (float) $field['min'], $val );
						}
						if ( isset( $field['max'] ) ) {
							$val = min( (float) $field['max'], $val );
						}
						$out[ $key ] = ( $val === floor( $val ) ) ? (int) $val : round( $val, 2 );
						break;

					case 'select':
						$val         = isset( $input[ $key ] ) ? sanitize_text_field( $input[ $key ] ) : '';
						$out[ $key ] = array_key_exists( $val, $field['options'] ) ? $val : $defaults[ $key ];
						break;

					default:
						$out[ $key ] = isset( $input[ $key ] ) ? sanitize_text_field( $input[ $key ] ) : $defaults[ $key ];
						break;
				}
			}
		}

		return $out;
	}

	public static function menu() {
		add_submenu_page(
			'edit.php?post_type=' . FSF_POST_TYPE,
			__( 'Impostazioni Stand Flip', 'francy-stand-flip' ),
			__( 'Impostazioni', 'francy-stand-flip' ),
			'manage_options',
			'fsf-settings',
			array( __CLASS__, 'page' )
		);
	}

	/**
	 * Pagina impostazioni con anteprima live.
	 */
	public static function page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$settings = self::get();
		$sections = self::fields();
		$sample   = FSF_Render::sample_stand();
		?>
		<div class="wrap fsf-settings-wrap">
			<h1><?php esc_html_e( 'Francy Stand Flip — impostazioni globali', 'francy-stand-flip' ); ?></h1>
			<p class="fsf-intro">
				<?php esc_html_e( 'Queste impostazioni valgono per tutti i box. Ogni singolo stand può sovrascrivere sfondo, posizione, rotazione e dimensione della carta dalla sua schermata di modifica.', 'francy-stand-flip' ); ?>
			</p>

			<div class="fsf-settings-layout">
				<form method="post" action="options.php" class="fsf-settings-form" id="fsf-settings-form">
					<?php settings_fields( 'fsf_settings_group' ); ?>

					<?php foreach ( $sections as $section_id => $section ) : ?>
						<div class="fsf-card-panel">
							<h2><?php echo esc_html( $section['label'] ); ?></h2>
							<table class="form-table" role="presentation">
								<tbody>
								<?php foreach ( $section['fields'] as $key => $field ) : ?>
									<tr>
										<th scope="row">
											<label for="fsf-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
										</th>
										<td>
											<?php self::render_field( $key, $field, $settings[ $key ] ); ?>
											<?php if ( ! empty( $field['help'] ) ) : ?>
												<p class="description"><?php echo esc_html( $field['help'] ); ?></p>
											<?php endif; ?>
										</td>
									</tr>
								<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					<?php endforeach; ?>

					<?php submit_button( __( 'Salva impostazioni', 'francy-stand-flip' ) ); ?>
				</form>

				<div class="fsf-preview-col">
					<div class="fsf-preview-sticky">
						<h2><?php esc_html_e( 'Anteprima live', 'francy-stand-flip' ); ?></h2>
						<div class="fsf-preview-stage" id="fsf-preview-stage">
							<?php echo FSF_Render::box( $sample, $settings, array( 'preview' => true ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
						<p class="description">
							<?php esc_html_e( 'L\'anteprima si aggiorna mentre modifichi i valori. Ricordati di salvare.', 'francy-stand-flip' ); ?>
						</p>
						<div class="fsf-shortcode-help">
							<h3><?php esc_html_e( 'Shortcode', 'francy-stand-flip' ); ?></h3>
							<p><code>[francy_stands]</code> — <?php esc_html_e( 'tutti gli stand in griglia', 'francy-stand-flip' ); ?></p>
							<p><code>[francy_stand id="123"]</code> — <?php esc_html_e( 'un singolo stand', 'francy-stand-flip' ); ?></p>
							<p class="description"><?php esc_html_e( 'Gli shortcode pronti da copiare li trovi nella lista degli stand.', 'francy-stand-flip' ); ?></p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Stampa un singolo campo del form.
	 *
	 * @param string $key      Chiave impostazione.
	 * @param array  $field    Definizione campo.
	 * @param mixed  $value    Valore corrente.
	 */
	protected static function render_field( $key, $field, $value ) {
		$name = FSF_OPTION . '[' . $key . ']';
		$id   = 'fsf-' . $key;

		switch ( $field['type'] ) {
			case 'checkbox':
				printf(
					'<label class="fsf-switch"><input type="checkbox" id="%1$s" name="%2$s" value="1" data-fsf-key="%3$s" %4$s><span></span></label>',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( $key ),
					checked( 1, (int) $value, false )
				);
				break;

			case 'color':
				printf(
					'<input type="text" class="fsf-color" id="%1$s" name="%2$s" value="%3$s" data-fsf-key="%4$s" data-default-color="%5$s">',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( $value ),
					esc_attr( $key ),
					esc_attr( $value )
				);
				break;

			case 'select':
				printf(
					'<select id="%1$s" name="%2$s" data-fsf-key="%3$s">',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( $key )
				);
				foreach ( $field['options'] as $opt_val => $opt_label ) {
					printf(
						'<option value="%1$s" %2$s>%3$s</option>',
						esc_attr( $opt_val ),
						selected( (string) $value, (string) $opt_val, false ),
						esc_html( $opt_label )
					);
				}
				echo '</select>';
				break;

			case 'number':
				printf(
					'<input type="number" class="small-text" id="%1$s" name="%2$s" value="%3$s" min="%4$s" max="%5$s" step="%6$s" data-fsf-key="%7$s">',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( $value ),
					esc_attr( isset( $field['min'] ) ? $field['min'] : '' ),
					esc_attr( isset( $field['max'] ) ? $field['max'] : '' ),
					esc_attr( isset( $field['step'] ) ? $field['step'] : 'any' ),
					esc_attr( $key )
				);
				break;

			default:
				printf(
					'<input type="text" class="regular-text" id="%1$s" name="%2$s" value="%3$s" data-fsf-key="%4$s">',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( $value ),
					esc_attr( $key )
				);
				break;
		}
	}
}
