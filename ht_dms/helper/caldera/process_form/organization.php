<?php
/**
 * Process editing or creation or an organization via Caldera Forms
 *
 * @package   @ht_dms
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link      
 * @copyright 2015 Josh Pollock
 */

namespace ht_dms\helper\caldera\process_form;


use ht_dms\helper\caldera\forms;

class organization implements \Action_Hook_SubscriberInterface {

	/**
	 * Set actions
	 *
	 * @since 0.0.3
	 *
	 * @return array
	 */
	public static function get_actions() {
		return array(
			'ht_dms_new_organization_process' => array( 'process_organization', 10, 2 ),
			'cf_geo_autocomplete_data' => array( 'organization_geo_code', 10, 3 ),
		);

	}

	/**
	 * Process and save data from CF form.
	 *
	 * @since 0.3.0
	 *
	 * @uses 'ht_dms_new_organization_process' action generated by CF.
	 *
	 * @param array $data Form data.
	 * @param array $form Form info.
	 *
	 * @return int|bool ID of item on success or false on fail.
	 */
	public static function process_organization( $data, $form ) {
		if( ! wp_verify_nonce( pods_v_sanitized( '_cf_verify', 'post' ), 'caldera_forms_front' ) ) {
			return;
		}

		$data = self::sanitize_data( $data );
		$obj = $id = false;
		if ( self::is_new( pods_v( 'ID', $form ) ) && self::verify_code( $data ) ) {
			$obj = ht_dms_organization_class()->object();
		}else{
			if ( !  is_null( $id = pods_v( 'org_id', $data ) ) ) {
				$obj = ht_dms_organization_class()->object( true, $id );
			}

		}

		$data = self::prepare_save_data( $data );

		if ( ! empty( $data ) && is_object( $obj ) ) {
			$id = $obj->save->data( $data );
		}

		return $id;

	}

	/**
	 * Reformat the 2 select fields
	 *
	 * @since 0.3.0
	 *
	 * @see https://github.com/Desertsnowman/Caldera-Forms/issues/58
	 *
	 * @param $data
	 */
	protected static function fix_selects( $data ) {
		$open_access = pods_v( 'open_access', $data );
		if (  ! is_null( $open_access ) ) {
			if ( 'Membership Requires Invitation Or Approval' === $open_access ) {
				$data['open_access'] = false;
			} else {
				$data['open_access'] = true;
			}
		}

		$visibility = pods_v( 'visibility', $data );
		if ( ! is_null( $visibility ) ) {
			$data[ 'visibility' ] = strtolower( $visibility );
		}

		return $data;

	}

	/**
	 * Verify invite code
	 *
	 * @tod abstract to checks based on plan.
	 *
	 * @since 0.3.0
	 *
	 * @access protected
	 *
	 * @param array $data Form data.
	 *
	 * @return bool
	 */
	protected static function verify_code( $data ) {
		$verify = false;
		if ( ! is_null( $invite_code = pods_v( 'invite', $data ) ) ) {
			$verify = new \ht_dms\helper\registration\organization\verify( $invite_code, false );
		}

		return $verify;

	}

	/**
	 * Prepare form data for saving.
	 *
	 * @since 0.3.0
	 *
	 * @access protected
	 *
	 * @param array $data Form data.
	 *
	 * @return array
	 */
	protected static function prepare_save_data( $data ) {
		if ( array_key_exists(  'save', $data ) ) {
			unset( $data[ 'save' ] );
		}

		if ( isset( $data[ 'invite' ] ) ) {
			unset( $data [ 'invite' ] );
		}

		$data = self::process_geo_data( $data );

		return $data;

	}

	/**
	 * Check if creating or editing organization.
	 *
	 * @since 0.3.0
	 *
	 * @access protected
	 *
	 * @param array|string $form Form config info, or form ID.
	 *
	 * @return bool
	 */
	public static function is_new( $form ) {
		if ( is_array( $form ) ) {
			$form = pods_v( 'ID', $form );
		}

		if ( ! is_string( $form ) ) {
			ht_dms_error();
		}

		$form_class = self::get_form_class();
		if ( $form === pods_v(  'new_organization_form_id', $form_class ) ) {
			return true;
		}

	}

	/**
	 * Form geocoded data.
	 *
	 * @since 0.3.0
	 *
	 * @access protected
	 *
	 * @var array Stores the geocoded data from form.
	 */
	protected static $geo_data;

	/**
	 * Prepare form data for saving.
	 *
	 * @uses 'cf_geo_autocomplete_data' action
	 *
	 * @since 0.3.0
	 *
	 * @param array $data Form data.
	 * @param array $post_data Post data from submission, sanitized.
	 * @param array $form From config.
	 */
	public static function organization_geo_code( $data, $post_data, $form ) {
		self::$geo_data = $data;

	}

	/**
	 * Prepares the geolocation fields for saving.
	 *
	 * @since 0.3.0
	 *
	 * @param array $data Form data.
	 *
	 * @return array
	 */
	protected static function process_geo_data( $data ) {
		$geo_data = self::$geo_data;
		if ( is_array( $geo_data ) ) {
			$data['latitude']    = pods_v( 'lat', $geo_data );
			$data['longitude']   = pods_v( 'lng', $geo_data );
			$data['geolocation'] = json_encode( $geo_data );

		}

		return $data;

	}

	/**
	 * Holds an instance of the form class
	 *
	 * @var \ht_dms\helper\caldera\forms
	 */
	protected static $form_class;

	/**
	 * Get an instance of the form class.
	 *
	 * @since  0.3.0
	 *
	 * @access protected
	 *
	 * @return \ht_dms\helper\caldera\forms
	 */
	protected static function get_form_class() {
		if ( is_null( self::$form_class ) ) {
			self::$form_class = new forms();
		}

		return self::$form_class;
	}

	/**
	 * Holds the instance of this class.
	 *
	 * @since  0.3.0
	 *
	 * @access private
	 *
	 * @var    \ht_dms\helper\caldera\process_form\organization|object
	 */
	private static $instance;


	/**
	 * Returns the instance.
	 *
	 * @since  0.3.0
	 *
	 * @access public
	 *
	 * @return \ht_dms\helper\caldera\process_form\organization|object
	 */

	public static function init() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}

	/**
	 * Sanitize and validate data
	 *
	 * @since 0.2.0
	 *
	 * @param array $data The submitted $_POST data
	 *
	 * @return array|mixed|object|string|void
	 */
	protected static function sanitize_data( $data ) {
		$data                = pods_sanitize( $data );
		if ( ! isset( $data[ 'description' ] ) ) {
			$data['description'] = wp_kses_post( $data['description'] );
		}

		$data                = self::fix_selects( $data );

		return $data;

	}

}
