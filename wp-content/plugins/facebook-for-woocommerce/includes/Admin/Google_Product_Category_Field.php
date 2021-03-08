<?php
/**
 * Copyright (c) Facebook, Inc. and its affiliates. All Rights Reserved
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * @package FacebookCommerce
 */

namespace SkyVerge\WooCommerce\Facebook\Admin;

defined( 'ABSPATH' ) or exit;

/**
 * Google product category field.
 *
 * @since 2.1.0
 */
class Google_Product_Category_Field {


	/** @var string the WordPress option name where the full categories list is stored */
	const OPTION_GOOGLE_PRODUCT_CATEGORIES = 'wc_facebook_google_product_categories';


	/**
	 * Instantiates the JS handler for the Google product category field.
	 *
	 * @since 2.1.0
	 *
	 * @param string $input_id element that should receive the latest concrete category ID value
	 */
	public function render( $input_id ) {

		$js = sprintf(
			"window.wc_facebook_google_product_category_fields = new WC_Facebook_Google_Product_Category_Fields( %s, '%s' );",
			json_encode( $this->get_categories() ),
			esc_js( $input_id )
		);

		wc_enqueue_js( $js );

	}


	/**
	 * Gets the full categories list from Google and stores it.
	 *
	 * @since 2.1.0
	 */
	public function get_categories() {

		// only fetch again if not fetched less than one hour ago
		$categories = get_transient( self::OPTION_GOOGLE_PRODUCT_CATEGORIES );

		if ( empty ( $categories ) ) {

			// fetch from the URL
			$categories_response = wp_remote_get( 'https://www.google.com/basepages/producttype/taxonomy-with-ids.en-US.txt' );

			$categories = $this->parse_categories_response( $categories_response );

			if ( ! empty( $categories ) ) {

				set_transient( self::OPTION_GOOGLE_PRODUCT_CATEGORIES, $categories, WEEK_IN_SECONDS );
				update_option( self::OPTION_GOOGLE_PRODUCT_CATEGORIES, $categories );
			}
		}

		if ( empty( $categories ) ) {

			// get the categories from the saved option
			$categories = get_option( self::OPTION_GOOGLE_PRODUCT_CATEGORIES, [] );
		}

		return $categories;
	}


	/**
	 * Parses the categories response from Google.
	 *
	 * @since 2.1.0
	 *
	 * @param array|\WP_Error $categories_response categories response from Google
	 * @return array
	 */
	protected function parse_categories_response( $categories_response ) {

		$categories = [];

		if ( is_array( $categories_response ) && isset( $categories_response['body'] ) ) {

			$categories_body = $categories_response['body'];
			$categories_body = explode( "\n", $categories_body );

			// format: ID - Top level category > ... > Parent category > Category label
			// example: 7385 - Animals & Pet Supplies > Pet Supplies > Bird Supplies > Bird Cage Accessories
			foreach ( $categories_body as $category_line ) {

				if ( strpos( $category_line, ' - ' ) === false ) {

					// not a category, skip it
					continue;
				}

				list( $category_id, $category_tree ) = explode( ' - ', $category_line );

				$category_id    = (string) trim( $category_id );
				$category_tree  = explode( ' > ', $category_tree );
				$category_label = end( $category_tree );

				$category = [
					'label'   => $category_label,
					'options' => [],
				];

				if ( $category_label === $category_tree[0] ) {

					// top-level category
					$category['parent'] = '';

				} else {

					$parent_label = $category_tree[ count( $category_tree ) - 2 ];

					$parent_category = array_search( $parent_label, array_map( function ( $item ) {

						return $item['label'];
					}, $categories ) );

					$category['parent'] = (string) $parent_category;

					// add category label to the parent's list of options
					$categories[ $parent_category ]['options'][ $category_id ] = $category_label;
				}

				$categories[ (string) $category_id ] = $category;
			}
		}

		return $categories;
	}


	/**
	 * Gets the category options (children) for a given category.
	 *
	 * @since 2.1.0
	 *
	 * @param string $category_id category ID
	 * @param array $categories full category list
	 * @return array
	 */
	public function get_category_options( $category_id, $categories ) {

		return array_filter( array_map( function ( $category ) use ( $category_id ) {

			return $category['parent'] === $category_id ? $category['label'] : false;
		}, $categories ) );
	}


}
