<?php
/**
 * AJAX Callback functions
 *
 * @package   @ht_dms
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2014 Josh Pollock
 */

namespace ht_dms\ui\ajax;


class Callbacks  {
	/**
	 * Reload consensus on ht_dms_reload_consensus AJAX action
	 *
	 * @uses 'wp_ajax_ht_dms_reload_consensus' action
	 *
	 * @since 0.0.3
	 */
	function reload_consensus( ) {
		if ( check_ajax_referer( 'ht-dms', 'nonce' ) ) {

			$dID = pods_v_sanitized( 'dID', $_REQUEST );
			if ( $dID ) {
				$post = get_post( $dID );
				if ( is_object( $post ) && isset( $post->post_type ) && HT_DMS_DECISION_POD_NAME === $post->post_type ) {
					wp_cache_flush();
					//$users = ht_dms_decision_class()->consensus_members( $dID );
					$consensus = ht_dms_ui()->output_elements()->view_consensus( $dID );

					if ( is_string( $consensus ) ) {
						wp_die(  $consensus );
					}

				}

			}

		}

	}

	/**
	 * Reload notification on ht_dms_notification action
	 *
	 * @uses 'wp_ajax_ht_dms_notification'
	 *
	 * @since 0.0.3
	 */
	function load_notification( ) {
		if ( check_ajax_referer( 'ht-dms', 'nonce' ) ) {
			$nID = pods_v_sanitized( 'nID', $_REQUEST );
			if ( $nID ) {

				wp_die( ht_dms_ui()->views()->notification( null, $nID ) );

			}

		}

	}

	/**
	 * Update decision status on ht_dms_update_decision_status AJAX action
	 *
	 * @uses 'wp_ajax_ht_dms_update_decision_status' action
	 *
	 * @since 0.0.3
	 */
	function update_decision_status() {
		if ( check_ajax_referer( 'ht-dms', 'nonce' ) ) {
			$dID = pods_v_sanitized( 'dID', $_REQUEST );
			if ( $dID ) {
				wp_die( ucwords( ht_dms_decision_class()->status( $dID ) ) );

			}

		}
	}

	/**
	 * Reload's membership view on ht_dms_reload_membership AJAX action
	 *
	 * @uses 'wp_ajax_ht_dms_reload_membership' action
	 *
	 * @since 0.0.3
	 */
	function reload_membership() {
		if ( check_ajax_referer( 'ht-dms', 'nonce' ) ) {
			$gID = pods_v_sanitized( 'gID', $_REQUEST );
			if ( $gID ) {
				wp_die( ht_dms_ui()->build_elements()->group_membership( $gID) );
			}
		}
	}


	/**
	 * Mark a notification viewed or unviewed via AJAX
	 *
	 * @uses 'wp_ajax_ht_dms_mark_notification' action
	 *
	 * @since 0.0.3
	 */
	function mark_notification()  {
		if ( check_ajax_referer( 'ht-dms', 'nonce' ) ) {
			$nID = pods_v_sanitized( 'nID', $_REQUEST );
			$value =  ( pods_v( 'mark', $_REQUEST ) );

			if ( $nID && in_array( $value, array( 1, 0 ) ) ) {
				$id = ht_dms_notification_class()->viewed( $nID, null, $value );

				if ( $id == $nID ) {
					wp_die( 1 );
				}
				else {
					wp_die( 0 );
				}
			}

		}

	}

	function members() {
		if ( check_ajax_referer( 'ht-dms', 'nonce' ) ) {
			$id = pods_v_sanitized( 'id', $_REQUEST );
			$type = pods_v_sanitized( 'type', $_REQUEST );
			if ( $id && $type ) {
				if ( in_array( $type, array( 'group', 'organization' ) ) ) {
					$is_group = false;
					if ( $type == 'group' ) {
						$is_group = true;
					}
					$members = ht_dms_membership_class()->all_members( $id, null, $is_group );
					$members = ht_dms_ui()->output_elements()->members_details_view( $members, 20, 20, true );
					if ( is_string( $members ) ) {
						wp_die(  $members  );
					}


				}

			}
			else {

			}
		}

	}

	/**
	 * Returns an array, which is used to build hooks for AJAX actions.
	 *
	 * @return array
	 *
	 * @since 0.0.3
	 */
	public function callbacks() {

		$callbacks = get_class_methods( __CLASS__ );

		unset( $callbacks [ __METHOD__ ] );

		return $callbacks;

	}

}