<?php
/**
 * Verify a organization creation code.
 *
 * @package   @ht_dms
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link      
 * @copyright 2014 Josh Pollock
 */

namespace ht_dms\api\internal\actions;


class new_organization_code extends action implements action_interface {

	/**
	 * Check the code
	 *
	 * @since 0.1.0
	 *
	 * @param $params
	 *
	 * @return bool
	 */
	public static function act( $params ) {
		if ( false != ( $invite_code = pods_v( 'invite', $params ) ) ) {
			$verify = new \ht_dms\helper\registration\organization\verify( $invite_code, false );
			if( false !== $verify->check() ) {
				return 200;
			}

		}

		return 550;


	}

	/**
	 * Params for this route
	 *
	 * @since 0.1.0
	 *
	 * @return array
	 */
	public static function args() {
		return array(
			'invite'
		);

	}

} 
