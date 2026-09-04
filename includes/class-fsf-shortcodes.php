<?php
/**
 * Shortcode: griglia globale e singolo stand.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FSF_Shortcodes {

	public static function init() {
		add_shortcode( 'francy_stands', array( __CLASS__, 'grid' ) );
		add_shortcode( 'francy_stand', array( __CLASS__, 'single' ) );

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_assets' ) );
	}

	/**
	 * Registra (e se serve accoda) il CSS del front-end.
	 */
	public static function register_assets() {
		wp_register_style( 'fsf-front', FSF_URL . 'assets/css/front.css', array(), FSF_VERSION );

		$post = get_post();
		if ( $post instanceof WP_Post && ( has_shortcode( $post->post_content, 'francy_stands' ) || has_shortcode( $post->post_content, 'francy_stand' ) ) ) {
			wp_enqueue_style( 'fsf-front' );
		}
	}

	/**
	 * Accoda il CSS anche quando lo shortcode arriva da un template o da un builder.
	 */
	protected static function enqueue() {
		if ( ! wp_style_is( 'fsf-front', 'registered' ) ) {
			wp_register_style( 'fsf-front', FSF_URL . 'assets/css/front.css', array(), FSF_VERSION );
		}
		if ( ! wp_style_is( 'fsf-front', 'enqueued' ) ) {
			wp_enqueue_style( 'fsf-front' );
		}
	}

	/**
	 * [francy_stands] — tutti gli stand in griglia.
	 *
	 * @param array $atts Attributi.
	 * @return string
	 */
	public static function grid( $atts ) {
		$atts = shortcode_atts(
			array(
				'ids'     => '',
				'exclude' => '',
				'group'   => '',
				'limit'   => -1,
				'orderby' => 'menu_order',
				'order'   => 'ASC',
				'columns' => '',
				'gap'     => '',
				'class'   => '',
			),
			$atts,
			'francy_stands'
		);

		$settings = FSF_Settings::get();
		self::enqueue();

		$allowed_orderby = array( 'menu_order', 'date', 'title', 'rand', 'modified' );
		$orderby         = in_array( $atts['orderby'], $allowed_orderby, true ) ? $atts['orderby'] : 'menu_order';
		if ( 'menu_order' === $orderby ) {
			$orderby = 'menu_order date';
		}

		$query_args = array(
			'post_type'           => FSF_POST_TYPE,
			'post_status'         => 'publish',
			'posts_per_page'      => (int) $atts['limit'] > 0 ? (int) $atts['limit'] : -1,
			'orderby'             => $orderby,
			'order'               => 'DESC' === strtoupper( $atts['order'] ) ? 'DESC' : 'ASC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'fields'              => 'ids',
		);

		$ids = self::id_list( $atts['ids'] );
		if ( $ids ) {
			$query_args['post__in'] = $ids;
			if ( 'menu_order date' === $orderby ) {
				$query_args['orderby'] = 'post__in';
			}
		}

		$exclude = self::id_list( $atts['exclude'] );
		if ( $exclude ) {
			$query_args['post__not_in'] = $exclude;
		}

		if ( $atts['group'] ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => FSF_CPT::TAXONOMY,
					'field'    => 'slug',
					'terms'    => array_filter( array_map( 'sanitize_title', explode( ',', $atts['group'] ) ) ),
				),
			);
		}

		$posts = get_posts( $query_args );

		if ( ! $posts ) {
			if ( current_user_can( 'edit_posts' ) ) {
				return '<p class="fsf-empty">' . esc_html__( 'Nessuno stand pubblicato: aggiungine uno da "Stand Flip" nella bacheca.', 'francy-stand-flip' ) . '</p>';
			}
			return '';
		}

		$inner = '';
		foreach ( $posts as $post_id ) {
			$inner .= FSF_Render::box( (int) $post_id, $settings );
		}

		$grid_atts = array( 'class' => $atts['class'] );
		if ( '' !== $atts['columns'] && is_numeric( $atts['columns'] ) ) {
			$grid_atts['columns'] = max( 1, min( 6, (int) $atts['columns'] ) );
		}
		if ( '' !== $atts['gap'] && is_numeric( $atts['gap'] ) ) {
			$grid_atts['gap'] = max( 0, min( 200, (float) $atts['gap'] ) );
		}

		return FSF_Render::grid( $inner, $settings, $grid_atts );
	}

	/**
	 * [francy_stand id="123"] — singolo stand.
	 *
	 * @param array $atts Attributi.
	 * @return string
	 */
	public static function single( $atts ) {
		$atts = shortcode_atts(
			array(
				'id'    => 0,
				'slug'  => '',
				'class' => '',
			),
			$atts,
			'francy_stand'
		);

		$settings = FSF_Settings::get();
		self::enqueue();

		$post_id = absint( $atts['id'] );

		if ( ! $post_id && $atts['slug'] ) {
			$found = get_posts(
				array(
					'post_type'      => FSF_POST_TYPE,
					'post_status'    => 'publish',
					'name'           => sanitize_title( $atts['slug'] ),
					'posts_per_page' => 1,
					'fields'         => 'ids',
					'no_found_rows'  => true,
				)
			);
			$post_id = $found ? (int) $found[0] : 0;
		}

		$post = $post_id ? get_post( $post_id ) : null;

		if ( ! $post || FSF_POST_TYPE !== $post->post_type || 'publish' !== $post->post_status ) {
			if ( current_user_can( 'edit_posts' ) ) {
				return '<p class="fsf-empty">' . esc_html__( 'Stand non trovato: controlla l\'ID nello shortcode.', 'francy-stand-flip' ) . '</p>';
			}
			return '';
		}

		return FSF_Render::grid(
			FSF_Render::box( $post_id, $settings ),
			$settings,
			array(
				'columns' => 1,
				'class'   => trim( 'fsf-grid--single ' . $atts['class'] ),
			)
		);
	}

	/**
	 * Normalizza una lista di ID separati da virgola.
	 *
	 * @param string $raw Valore attributo.
	 * @return array
	 */
	protected static function id_list( $raw ) {
		if ( ! $raw ) {
			return array();
		}
		return array_values( array_filter( array_map( 'absint', explode( ',', $raw ) ) ) );
	}
}
