<?php
/**
 * CartFlows Flow data.
 *
 * @package CartFlows
 */

namespace CartflowsAdmin\AdminCore\Api;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use CartflowsAdmin\AdminCore\Api\ApiBase;
use CartflowsAdmin\AdminCore\Inc\AdminHelper;

use CartflowsAdmin\AdminCore\Inc\FlowMeta;

/**
 * Class Admin_Query.
 */
class FlowData extends ApiBase {

	/**
	 * Route base.
	 *
	 * @var string
	 */
	protected $rest_base = '/admin/flow-data/';

	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class object.
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * Initiator
	 *
	 * @since 1.0.0
	 * @return object initialized object of class.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Init Hooks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_routes() {

		$namespace = $this->get_api_namespace();

		register_rest_route(
			$namespace,
			$this->rest_base . '(?P<id>[\d-]+)',
			array(
				'args'   => array(
					'id' => array(
						'description' => __( 'Flow ID.', 'cartflows' ),
						'type'        => 'integer',
					),
				),
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),

				/*
				Start - Not yet working but needed
					array(
						'methods'             => \WP_REST_Server::EDITABLE,
						'callback'            => array( $this, 'update_item' ),
						'permission_callback' => array( $this, 'update_items_permissions_check' ),
					),
					End - Not yet working but needed
				*/
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	/**
	 * Get flow data.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_item( $request ) {

		$flow_id = $request->get_param( 'id' );

		$meta_options = AdminHelper::get_flow_meta_options( $flow_id );

		/* Setup steps data */
		$steps = $meta_options['wcf-steps'];

		if ( is_array( $steps ) && ! empty( $steps ) ) {

			foreach ( $steps as $in => $step ) {
				$step_id                             = $step['id'];
				$steps[ $in ]['title']               = get_the_title( $step_id );
				$steps[ $in ]['is_product_assigned'] = \Cartflows_Helper::has_product_assigned( $step_id );

				$steps[ $in ]['actions']      = $this->get_step_actions( $flow_id, $step_id );
				$steps[ $in ]['menu_actions'] = $this->get_step_actions( $flow_id, $step_id, 'menu' );

				if ( _is_cartflows_pro() && in_array( $step['type'], array( 'upsell', 'downsell' ), true ) ) {

					$wcf_step_obj = wcf_pro_get_step( $step_id );
					$flow_steps   = $wcf_step_obj->get_flow_steps();
					$control_step = $wcf_step_obj->get_control_step();
					if ( 'upsell' === $step['type'] ) {
						$next_yes_steps = wcf_pro()->flow->get_next_step_id_for_upsell_accepted( $wcf_step_obj, $flow_steps, $step_id, $control_step );
						$next_no_steps  = wcf_pro()->flow->get_next_step_id_for_upsell_rejected( $wcf_step_obj, $flow_steps, $step_id, $control_step );
					}

					if ( 'downsell' === $step['type'] ) {
						$next_yes_steps = wcf_pro()->flow->get_next_step_id_for_downsell_accepted( $wcf_step_obj, $flow_steps, $step_id, $control_step );
						$next_no_steps  = wcf_pro()->flow->get_next_step_id_for_downsell_rejected( $wcf_step_obj, $flow_steps, $step_id, $control_step );
					}

					if ( ! empty( $next_yes_steps ) && false !== get_post_status( $next_yes_steps ) ) {

						$yes_label = __( 'YES : ', 'cartflows' ) . get_the_title( $next_yes_steps );
					} else {
						$yes_label = __( 'YES : Step not Found', 'cartflows' );
					}

					if ( ! empty( $next_no_steps ) && false !== get_post_status( $next_no_steps ) ) {

						$no_label = __( 'No : ', 'cartflows' ) . get_the_title( $next_no_steps );
					} else {
						$no_label = __( 'No : Step not Found', 'cartflows' );
					}

					$steps[ $in ]['offer_yes_next_step'] = $yes_label;
					$steps[ $in ]['offer_no_next_step']  = $no_label;
				}

				/* Add variation data */
				if ( ! empty( $steps[ $in ]['ab-test-variations'] ) ) {

					$ab_test_variations = $steps[ $in ]['ab-test-variations'];

					foreach ( $ab_test_variations as $variation_in => $variation ) {

						$ab_test_variations[ $variation_in ]['title']               = get_the_title( $variation['id'] );
						$ab_test_variations[ $variation_in ]['actions']             = $this->get_ab_test_step_actions( $flow_id, $variation['id'] );
						$ab_test_variations[ $variation_in ]['menu_actions']        = $this->get_ab_test_step_actions( $flow_id, $variation['id'], 'menu' );
						$ab_test_variations[ $variation_in ]['is_product_assigned'] = \Cartflows_Helper::has_product_assigned( $variation['id'] );
					}

					$steps[ $in ]['ab-test-variations'] = $ab_test_variations;
				}

				if ( ! empty( $steps[ $in ]['ab-test-archived-variations'] ) ) {

					$ab_test_archived_variations = $steps[ $in ]['ab-test-archived-variations'];

					foreach ( $ab_test_archived_variations as $variation_in => $variation ) {

						$ab_test_archived_variations[ $variation_in ]['actions'] = $this->get_ab_test_step_archived_actions( $flow_id, $variation['id'], $variation['deleted'] );
						$ab_test_archived_variations[ $variation_in ]['hide']    = get_post_meta( $variation['id'], 'wcf-hide-step', true );
					}

					$steps[ $in ]['ab-test-archived-variations'] = $ab_test_archived_variations;
				}
			}
		}

		$data = array(
			'id'            => $flow_id,
			'title'         => get_the_title( $flow_id ),
			'slug'          => get_post_field( 'post_name', $flow_id, 'edit' ),
			'link'          => get_permalink( $flow_id ),
			'status'        => get_post_status( $flow_id ),
			'steps'         => $steps,
			'options'       => $meta_options,
			'settings_data' => FlowMeta::get_meta_settings( $flow_id ),
		);

		$response = new \WP_REST_Response( $data );
		$response->set_status( 200 );

		return $response;
	}

	/**
	 * Get step actions.
	 *
	 * @param  int    $flow_id Flow id.
	 * @param  int    $step_id Step id.
	 * @param  string $type type.
	 *
	 * @return array
	 */
	public function get_step_actions( $flow_id, $step_id, $type = 'inline' ) {

		if ( 'menu' === $type ) {
			$actions = array(
				'clone'  => array(
					'slug'       => 'clone',
					'class'      => 'wcf-step-clone',
					'icon_class' => 'dashicons dashicons-admin-page',
					'text'       => __( 'Clone', 'cartflows' ),
					'pro'        => true,
					'link'       => '#',
					'ajaxcall'   => 'cartflows_clone_step',
				),
				'delete' => array(
					'slug'       => 'delete',
					'class'      => 'wcf-step-delete',
					'icon_class' => 'dashicons dashicons-trash',
					'text'       => __( 'Delete', 'cartflows' ),
					'link'       => '#',
					'ajaxcall'   => 'cartflows_delete_step',
				),
				'abtest' => array(
					'slug'       => 'abtest',
					'class'      => 'wcf-step-abtest',
					'icon_class' => 'dashicons dashicons-forms',
					'text'       => __( 'A/B Test', 'cartflows' ),
					'pro'        => true,
					'link'       => '#',
				),

				/*
				Action.
					// 'export' => array(
					// 'slug'       => 'export',
					// 'class'      => 'wcf-step-export',
					// 'icon_class' => 'dashicons dashicons-database-export',
					// 'text'       => __( 'Export', 'cartflows' ),
					// 'link'       => '#',
					// 'pro'        => true,
					// ),
				*/
			);
		} else {
			$actions = array(
				'view' => array(
					'slug'       => 'view',
					'class'      => 'wcf-step-view',
					'icon_class' => 'dashicons dashicons-visibility',
					'target'     => 'blank',
					'text'       => __( 'View', 'cartflows' ),
					'link'       => get_permalink( $step_id ),
				),
				'edit' => array(
					'slug'       => 'edit',
					'class'      => 'wcf-step-edit',
					'icon_class' => 'dashicons dashicons-edit',
					'text'       => __( 'Edit', 'cartflows' ),
					'link'       => admin_url( 'admin.php?page=cartflows&action=wcf-edit-step&step_id=' . $step_id . '&flow_id=' . $flow_id ),
				),
			);
		}
		return $actions;
	}

	/**
	 * Get step actions.
	 *
	 * @param  int    $flow_id Flow id.
	 * @param  int    $step_id Step id.
	 * @param  string $type type.
	 *
	 * @return array
	 */
	public function get_ab_test_step_actions( $flow_id, $step_id, $type = 'inline' ) {

		if ( 'menu' === $type ) {

			$actions = array(
				'clone'    => array(
					'slug'       => 'clone',
					'class'      => 'wcf-ab-test-step-clone',
					'icon_class' => 'dashicons dashicons-admin-page',
					'text'       => __( 'Clone', 'cartflows' ),
					'link'       => '#',
					'pro'        => true,
					'ajaxcall'   => 'cartflows_clone_ab_test_step',
				),
				'delete'   => array(
					'slug'       => 'delete',
					'class'      => 'wcf-ab-test-step-delete',
					'icon_class' => 'dashicons dashicons-trash',
					'text'       => __( 'Delete', 'cartflows' ),
					'link'       => '#',
					'ajaxcall'   => 'cartflows_delete_ab_test_step',
				),
				'archived' => array(
					'slug'       => 'archived',
					'class'      => 'wcf-ab-test-step-archived',
					'icon_class' => 'dashicons dashicons-archive',
					'text'       => __( 'Archived', 'cartflows' ),
					'link'       => '#',
				),
				'winner'   => array(
					'slug'       => 'winner',
					'class'      => 'wcf-declare-winner',
					'icon_class' => 'dashicons dashicons-yes-alt',
					'text'       => __( 'Declare as Winner', 'cartflows' ),
					'link'       => '#',
				),
			);

		} else {

			$actions = array(
				'view' => array(
					'slug'       => 'view',
					'class'      => 'wcf-step-view',
					'icon_class' => 'dashicons dashicons-visibility',
					'target'     => 'blank',
					'text'       => __( 'View', 'cartflows' ),
					'link'       => get_permalink( $step_id ),
				),
				'edit' => array(
					'slug'       => 'edit',
					'class'      => 'wcf-step-edit',
					'icon_class' => 'dashicons dashicons-edit',
					'text'       => __( 'Edit', 'cartflows' ),
					'link'       => admin_url( 'admin.php?page=cartflows&action=wcf-edit-step&step_id=' . $step_id . '&flow_id=' . $flow_id ),
				),
			);
		}

		return $actions;
	}

	/**
	 * Get ab test step action.
	 *
	 * @param  int  $flow_id Flow id.
	 * @param  int  $step_id Step id.
	 * @param  bool $deleted Step deleted or archived.
	 * @return array
	 */
	public function get_ab_test_step_archived_actions( $flow_id, $step_id, $deleted ) {

		if ( $deleted ) {
			$actions = array(
				'archive-hide' => array(
					'slug'        => 'hide',
					'class'       => 'wcf-step-archive-hide',
					'icon_class'  => 'dashicons dashicons-hidden',
					'target'      => 'blank',
					'before_text' => __( 'Deleted variation can\'t be restored.', 'cartflows' ),
					'text'        => __( 'Hide', 'cartflows' ),
					'link'        => '#',
				),
			);
		} else {

			$actions = array(
				'archive-restore' => array(
					'slug'       => 'restore',
					'class'      => 'wcf-step-archive-restore',
					'icon_class' => 'dashicons dashicons-open-folder',
					'target'     => 'blank',
					'text'       => __( 'Restore', 'cartflows' ),
					'link'       => '#',
				),
				'archive-delete'  => array(
					'slug'       => 'delete',
					'class'      => 'wcf-step-archive-delete',
					'icon_class' => 'dashicons dashicons-trash',
					'target'     => 'blank',
					'text'       => __( 'Delete', 'cartflows' ),
					'link'       => '#',
				),
			);
		}

		return $actions;
	}

	/**
	 * Check whether a given request has permission to read notes.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return WP_Error|boolean
	 */
	public function get_item_permissions_check( $request ) {

		if ( ! current_user_can( 'manage_options' ) ) {
			return new \WP_Error( 'cartflows_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'cartflows' ), array( 'status' => rest_authorization_required_code() ) );
		}

		return true;
	}
}
