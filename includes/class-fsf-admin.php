<?php
/**
 * Asset e rifiniture del backend.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FSF_Admin {

	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
		add_filter( 'manage_' . FSF_POST_TYPE . '_posts_columns', array( __CLASS__, 'columns' ) );
		add_action( 'manage_' . FSF_POST_TYPE . '_posts_custom_column', array( __CLASS__, 'column_content' ), 10, 2 );
		add_filter( 'plugin_action_links_' . plugin_basename( FSF_FILE ), array( __CLASS__, 'action_links' ) );
	}

	/**
	 * Vero se siamo su una schermata del plugin.
	 *
	 * @return bool
	 */
	protected static function is_plugin_screen() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen ) {
			return false;
		}
		if ( FSF_POST_TYPE === $screen->post_type && in_array( $screen->base, array( 'post', 'edit' ), true ) ) {
			return true;
		}
		return false !== strpos( (string) $screen->id, 'fsf-settings' );
	}

	/**
	 * CSS/JS del backend.
	 */
	public static function assets() {
		if ( ! self::is_plugin_screen() ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'fsf-front', FSF_URL . 'assets/css/front.css', array(), FSF_VERSION );
		wp_enqueue_style( 'fsf-admin', FSF_URL . 'assets/css/admin.css', array( 'fsf-front' ), FSF_VERSION );
		wp_enqueue_script( 'fsf-admin', FSF_URL . 'assets/js/admin.js', array( 'jquery', 'wp-color-picker' ), FSF_VERSION, true );

		$field_types = array();
		foreach ( FSF_Settings::fields() as $section ) {
			foreach ( $section['fields'] as $key => $field ) {
				$field_types[ $key ] = $field['type'];
			}
		}

		wp_localize_script(
			'fsf-admin',
			'fsfAdmin',
			array(
				'settings'     => FSF_Settings::get(),
				'fieldTypes'   => $field_types,
				'placeholders' => array(
					'card'  => FSF_Render::placeholder( 'card' ),
					'stand' => FSF_Render::placeholder( 'stand' ),
				),
				'i18n'         => array(
					'pickCard'  => __( 'Scegli l\'immagine', 'francy-stand-flip' ),
					'use'       => __( 'Usa questa immagine', 'francy-stand-flip' ),
					'noImage'   => __( 'Nessuna immagine', 'francy-stand-flip' ),
					'copied'    => __( 'Shortcode copiato', 'francy-stand-flip' ),
					'noTitle'   => __( 'Nome dello stand', 'francy-stand-flip' ),
				),
			)
		);
	}

	/**
	 * Colonne della lista stand.
	 *
	 * @param array $columns Colonne.
	 * @return array
	 */
	public static function columns( $columns ) {
		$new = array();
		foreach ( $columns as $key => $label ) {
			$new[ $key ] = $label;
			if ( 'title' === $key ) {
				$new['fsf_card']      = __( 'Carta', 'francy-stand-flip' );
				$new['fsf_stand']     = __( 'Stand', 'francy-stand-flip' );
				$new['fsf_links']     = __( 'Instagram', 'francy-stand-flip' );
				$new['fsf_shortcode'] = __( 'Shortcode', 'francy-stand-flip' );
			}
		}
		return $new;
	}

	/**
	 * Contenuto delle colonne custom.
	 *
	 * @param string $column  Colonna.
	 * @param int    $post_id ID post.
	 */
	public static function column_content( $column, $post_id ) {
		switch ( $column ) {
			case 'fsf_card':
			case 'fsf_stand':
				$meta_key = 'fsf_card' === $column ? '_fsf_card_id' : '_fsf_stand_id';
				$img_id   = (int) FSF_Metabox::meta( $post_id, $meta_key, 0 );
				if ( ! $img_id && 'fsf_stand' === $column ) {
					$img_id = (int) get_post_thumbnail_id( $post_id );
				}
				if ( $img_id ) {
					echo wp_get_attachment_image( $img_id, array( 60, 60 ), false, array( 'class' => 'fsf-col-thumb' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					echo '<span class="fsf-col-missing">—</span>';
				}
				break;

			case 'fsf_links':
				$post_url = FSF_Metabox::meta( $post_id, '_fsf_ig_post', '' );
				$reel_url = FSF_Metabox::meta( $post_id, '_fsf_ig_reel', '' );
				$out      = array();
				if ( $post_url ) {
					$out[] = '<a href="' . esc_url( $post_url ) . '" target="_blank" rel="noopener noreferrer">post</a>';
				}
				if ( $reel_url ) {
					$out[] = '<a href="' . esc_url( $reel_url ) . '" target="_blank" rel="noopener noreferrer">reel</a>';
				}
				echo $out ? implode( ' · ', $out ) : '<span class="fsf-col-missing">—</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;

			case 'fsf_shortcode':
				printf(
					'<input type="text" class="fsf-copy-field" readonly value="%s" onclick="this.select();">',
					esc_attr( '[francy_stand id="' . (int) $post_id . '"]' )
				);
				break;
		}
	}

	/**
	 * Link "Impostazioni" nella pagina plugin.
	 *
	 * @param array $links Link.
	 * @return array
	 */
	public static function action_links( $links ) {
		$url = admin_url( 'edit.php?post_type=' . FSF_POST_TYPE . '&page=fsf-settings' );
		array_unshift( $links, '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Impostazioni', 'francy-stand-flip' ) . '</a>' );
		return $links;
	}
}
