<?php
/**
 * @TODO What this does.
 *
 * @package   @TODO
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link      
 * @copyright 2014 Josh Pollock
 */

namespace ht_dms\helper\registration;


class register implements \Hook_SubscriberInterface {


	/**
	 * Set actions
	 *
	 * @since 0.0.3
	 *
	 * @return array
	 */
	public static function get_actions() {

		return array(
			'user_register' => array( 'add_to_organization', 10, 1 ),
			'login_message' => 'change_login_message',

		);

	}

	/**
	 * Set filters
	 *
	 * @since 0.0.3
	 *
	 * @return array
	 */
	public static function get_filters() {
		return array(
			'registration_errors' => array( 'pre_save_verify', 1, 3 ),
		);
	}

	function add_to_organization( $user_id ) {
		if (  ! is_null( $code = pods_v_sanitized( 'invitation_code', 'post' ) ) ) {
			if ( $oID = ht_dms_integer( $this->verify_code( $code, pods_v_sanitized( 'user_email', 'post' ) ) ) ) {

				ht_dms_organization_class()->add_member( $oID, $user_id );

			}
		}
	}

	function pre_save_verify( $errors, $sanitized_user_login, $user_email ) {

		if ( false ===$this->verify_code( $user_email, pods_v_sanitized( 'invitation_code', 'post' ) ) ) {
			$errors->add( 'ht_dms_bad_code', __( '<strong>ERROR</strong>: Your invite code is not valid.','holotree') );
		}

		return $errors;

	}

	private function verify_code( $email, $code ) {

		return ht_dms_invite_code( false, $email, false, $code );

	}

	/**
	 * Change login messages
	 *
	 * @since 0.0.3
	 *
	 * @param $message
	 *
	 * @return string
	 */

	function change_login_message( $message ) {

		// Registration
		if ( strpos( $message, 'Register' ) !== false ) {
			$message = '<p class="message register">' . __( 'Registration for HoloTree currently requires an invite code. If you have one, you can use the from below to register.', 'holotree' ) . '</p>';
			$message .= '<p class="message register">' . __( sprintf( 'If you are already registered, %1s to register.', ht_dms_login_link() ), 'holotree' );

		}

		//login
		if ( $message == '' ) {
			$message = '<p class="message register">' . ht_dms_registeration_link() . ht_dms_lost_password_link() . '</p>';
		}

		return $message;

	}

	/**
	 * Holds the instance of this class.
	 *
	 * @since 0.0.3
	 *
	 * @access private
	 * @var    object
	 */
	private static $instance;


	/**
	 * @since 0.0.3
	 *
	 * @return register|object
	 */
	public static function init() {

		if ( !self::$instance )
			self::$instance = new self();

		return self::$instance;

	}
} 