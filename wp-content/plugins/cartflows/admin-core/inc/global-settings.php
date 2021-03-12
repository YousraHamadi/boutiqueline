<?php
/**
 * CartFlows Global Data.
 *
 * @package CartFlows
 */

namespace CartflowsAdmin\AdminCore\Inc;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class flowMeta.
 */
class GlobalSettings {


	/**
	 * Get flow meta options.
	 */
	public static function get_global_settings_fields() {

		$origin = get_site_url();

		$settings = array(
			'general'        => array(
				'title'  => __( 'General ', 'cartflows' ),
				'fields' => array(
					'search_engine'   => array(
						'type'  => 'checkbox',
						'name'  => '_cartflows_common[disallow_indexing]',
						'label' => __( 'Disallow search engine from indexing flows.', 'cartflows' ),
					),
					'global_checkout' => array(
						'type'  => 'select',
						'name'  => '_cartflows_common[global_checkout]',
						'label' => __( 'Global Checkout', 'cartflows' ),
						'desc'  => __(
							'Be sure not to add any product in above selected Global Checkout step. Please read information about how to set up Global Checkout here.
                        ',
							'cartflows'
						),
					),
					'page_builder'    => array(
						'type'    => 'select',
						'name'    => '_cartflows_common[default_page_builder]',
						'label'   => __( 'Ready Templates for', 'cartflows' ),
						'desc'    => __( 'CartFlows offers flow templates that can be imported in one click. These templates are available in few different page builders. Please choose your preferred page builder from the list so you will only see templates that are made using that page builder.', 'cartflows' ),
						'options' => array(
							array(
								'value' => 'elementor',
								'label' => __( 'Elementor', 'cartflows' ),
							),
							array(
								'value' => 'beaver-builder',
								'label' => __( 'Beaver Builder', 'cartflows' ),
							),
							array(
								'value' => 'divi',
								'label' => __( 'Divi', 'cartflows' ),
							),
							array(
								'value' => 'gutenberg',
								'label' => __( 'Gutenberg', 'cartflows' ),
							),
							array(
								'value' => 'other',
								'label' => __( 'Other', 'cartflows' ),
							),
						),

					),
				),
			),
			'permalink'      => array(
				'title'  => __( 'Permalinks', 'cartflows' ),
				'fields' => array(
					'perma-structure' => array(
						'type'    => 'radio',
						'name'    => '_cartflows_permalink[permalink_structure]',
						'options' => array(
							array(
								'value' => '',
								'label' => __( 'Default Permalinks', 'cartflows' ),
								'desc'  => __( 'Default WordPress Permalink', 'cartflows' ),
							),
							array(
								'value' => '/cartflows_flow/%flowname%/cartflows_step',
								'label' => __( 'Flow and Step Slug', 'cartflows' ),
								'desc'  => $origin . '/cartflows_flow/%flowname%/cartflows_step/%stepname%/',
							),
							array(
								'value' => '/cartflows_flow/%flowname%',
								'label' => __( 'Flow Slug', 'cartflows' ),
								'desc'  => $origin . '/cartflows_flow/%flowname%/%stepname%/',
							),
							array(
								'value' => '/%flowname%/cartflows_step',
								'label' => __( 'Step Slug', 'cartflows' ),
								'desc'  => $origin . '/%flowname%/cartflows_step/%stepname%/',
							),
						),
					),
					'perma-heading'   => array(
						'type'  => 'heading',
						'label' => __( 'Post Type Permalink Base', 'cartflows' ),
					),
					'perma-step-base' => array(
						'type'  => 'text',
						'label' => __( 'Step Base', 'cartflows' ),
						'name'  => '_cartflows_permalink[permalink]',
						'class' => 'input-field',
					),
					'perma-flow-base' => array(
						'type'  => 'text',
						'label' => __( 'Flow Base', 'cartflows' ),
						'name'  => '_cartflows_permalink[permalink_flow_base]',
						'class' => 'input-field',
					),
				),
			),
			'facebook-pixel' => array(
				'title'  => __( 'FaceBook Pixel', 'cartflows' ),
				'fields' => array(
					'enable-fb-pixel'               => array(
						'type'  => 'checkbox',
						'label' => __( 'Enable Facebook Pixel Tracking For CartFlows Pages', 'cartflows' ),
						'name'  => '_cartflows_facebook[facebook_pixel_tracking]',
					),
					'enable-fb-pixel-for-site'      => array(
						'type'       => 'checkbox',
						'label'      => __( 'Enable Facebook Pixel Tracking For the whole site', 'cartflows' ),
						'name'       => '_cartflows_facebook[facebook_pixel_tracking_for_site]',
						'desc'       => __( 'If this option is unchecked, it will only apply to CartFlows steps.', 'cartflows' ),
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_facebook[facebook_pixel_tracking]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
					),
					'pixel-id'                      => array(
						'type'       => 'text',
						'label'      => __( 'Enter Facebook pixel ID', 'cartflows' ),
						'name'       => '_cartflows_facebook[facebook_pixel_id]',
						'class'      => 'input-field',
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_facebook[facebook_pixel_tracking]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
					),
					'pixel-event-heading'           => array(
						'type'       => 'heading',
						'label'      => __( 'Facebook Pixel Events', 'cartflows' ),
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_facebook[facebook_pixel_tracking]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
					),

					'pixel-event-ini-checkout'      => array(
						'type'       => 'checkbox',
						'label'      => __( 'Initiate Checkout', 'cartflows' ),
						'name'       => '_cartflows_facebook[facebook_pixel_initiate_checkout]',
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_facebook[facebook_pixel_tracking]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
					),

					'pixel-event-payment-info'      => array(
						'type'       => 'checkbox',
						'label'      => __( 'Add Payment Info', 'cartflows' ),
						'name'       => '_cartflows_facebook[facebook_pixel_add_payment_info]',
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_facebook[facebook_pixel_tracking]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
					),
					'pixel-event-purchase-complete' => array(
						'type'       => 'checkbox',
						'label'      => __( 'Purchase Complete', 'cartflows' ),
						'name'       => '_cartflows_facebook[facebook_pixel_purchase_complete]',
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_facebook[facebook_pixel_tracking]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
					),
				),
			),
			'ga-analytics'   => array(
				'title'  => __( 'Google Analytics', 'cartflows' ),
				'fields' => array(
					'enable-ga-analytics'          => array(
						'type'  => 'checkbox',
						'label' => __( 'Enable Google Analytics Tracking For CartFlows Pages', 'cartflows' ),
						'name'  => '_cartflows_google_analytics[enable_google_analytics]',
					),
					'enable-ga-analytics-for-site' => array(
						'type'       => 'checkbox',
						'label'      => __( 'Enable Google Analytics Tracking For the whole site', 'cartflows' ),
						'name'       => '_cartflows_google_analytics[enable_google_analytics_for_site]',
						'desc'       => __( 'If this option is unchecked, it will only apply to CartFlows steps.', 'cartflows' ),
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_google_analytics[enable_google_analytics]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
					),
					'ga-id'                        => array(
						'type'       => 'text',
						'label'      => __( 'Enter Google Analytics ID', 'cartflows' ),
						'name'       => '_cartflows_google_analytics[google_analytics_id]',
						'class'      => 'input-field',
						/* translators: %1$1s: link html start, %2$12: link html end*/
						'desc'       => sprintf( __( 'Log into your %1$1s google analytics account %2$2s to find your ID. eg: UA-XXXXXX-X.', 'cartflows' ), '<a href="https://analytics.google.com/" target="_blank">', '</a>' ),
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_google_analytics[enable_google_analytics]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
					),
					'ga-event-heading'             => array(
						'type'       => 'heading',
						'label'      => __( 'Google Analytics Events', 'cartflows' ),
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_google_analytics[enable_google_analytics]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
					),
					'ga-event-ini-checkout'        => array(
						'type'       => 'checkbox',
						'label'      => __( 'Begin Checkout', 'cartflows' ),
						'name'       => '_cartflows_google_analytics[enable_begin_checkout]',
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_google_analytics[enable_google_analytics]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
					),

					'ga-event-add-to-cart'         => array(
						'type'       => 'checkbox',
						'label'      => __( 'Add To Cart', 'cartflows' ),
						'name'       => '_cartflows_google_analytics[enable_add_to_cart]',
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_google_analytics[enable_google_analytics]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
					),
					'ga-event-payment-info'        => array(
						'type'       => 'checkbox',
						'label'      => __( 'Add Payment Info', 'cartflows' ),
						'name'       => '_cartflows_google_analytics[enable_add_payment_info]',
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_google_analytics[enable_google_analytics]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
					),
					'ga-event-purchase-complete'   => array(
						'type'       => 'checkbox',
						'label'      => __( 'Purchase', 'cartflows' ),
						'name'       => '_cartflows_google_analytics[enable_purchase_event]',
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_google_analytics[enable_google_analytics]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
					),

					'ga-not-work-doc'              => array(
						'type'       => 'doc',
						'label'      => '',
						'name'       => 'ga-not-work-doc',
						/* translators: %1$1s: link html start, %2$12: link html end*/
						'content'    => sprintf( __( 'Google Analytics not working correctly? %1$1s Click here %2$2s to know more.', 'cartflows' ), '<a href="https://cartflows.com/docs/troubleshooting-google-analytics-tracking-issues/" target="_blank">', '</a>' ),
						'conditions' => array(
							'fields' => array(
								array(
									'name'     => '_cartflows_google_analytics[enable_google_analytics]',
									'operator' => '===',
									'value'    => 'enable',
								),
							),
						),
					),
				),
			),
		);

		$settings = apply_filters( 'cartflows_global_settings_data', $settings );

		return $settings;
	}
}
