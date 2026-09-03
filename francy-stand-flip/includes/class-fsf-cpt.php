<?php
/**
 * Custom post type "Stand" e tassonomia opzionale.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FSF_CPT {

	const TAXONOMY = 'fsf_stand_group';

	public static function init() {
		add_action( 'init', array( __CLASS__, 'register' ) );
	}

	public static function register() {
		register_post_type(
			FSF_POST_TYPE,
			array(
				'labels'              => array(
					'name'               => __( 'Stand', 'francy-stand-flip' ),
					'singular_name'      => __( 'Stand', 'francy-stand-flip' ),
					'menu_name'          => __( 'Stand Flip', 'francy-stand-flip' ),
					'add_new'            => __( 'Aggiungi stand', 'francy-stand-flip' ),
					'add_new_item'       => __( 'Aggiungi nuovo stand', 'francy-stand-flip' ),
					'edit_item'          => __( 'Modifica stand', 'francy-stand-flip' ),
					'new_item'           => __( 'Nuovo stand', 'francy-stand-flip' ),
					'view_item'          => __( 'Vedi stand', 'francy-stand-flip' ),
					'search_items'       => __( 'Cerca stand', 'francy-stand-flip' ),
					'not_found'          => __( 'Nessuno stand trovato', 'francy-stand-flip' ),
					'not_found_in_trash' => __( 'Nessuno stand nel cestino', 'francy-stand-flip' ),
					'all_items'          => __( 'Tutti gli stand', 'francy-stand-flip' ),
				),
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_rest'        => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'has_archive'         => false,
				'rewrite'             => false,
				'menu_position'       => 26,
				'menu_icon'           => 'dashicons-id-alt',
				'supports'            => array( 'title', 'excerpt', 'thumbnail', 'page-attributes' ),
			)
		);

		register_taxonomy(
			self::TAXONOMY,
			FSF_POST_TYPE,
			array(
				'labels'            => array(
					'name'          => __( 'Gruppi', 'francy-stand-flip' ),
					'singular_name' => __( 'Gruppo', 'francy-stand-flip' ),
					'menu_name'     => __( 'Gruppi', 'francy-stand-flip' ),
					'add_new_item'  => __( 'Aggiungi gruppo', 'francy-stand-flip' ),
				),
				'public'            => false,
				'show_ui'           => true,
				'show_admin_column' => true,
				'hierarchical'      => true,
				'rewrite'           => false,
				'show_in_rest'      => false,
			)
		);
	}
}
