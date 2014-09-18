<?php
/**
 * Notifications front-end view
 *
 * @package   @ht_dms
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2014 Josh Pollock
 */

$ui = holotree_dms_ui();

$uID = get_current_user_id();

$view_id = pods_v( 'dms_id' );


$tabs = array(
	array(
		'label'		=> ht_dms_add_icon( __( 'Profile', 'holotree' ), 'profile' ),
		'content' 	=> $ui->views()->preferences( $uID, false, false ),
	),
	array(
		'label'		=> ht_dms_add_icon( __( 'Edit Profile', 'holotree' ), array( 'edit', 'profile' ) ),
		'content' 	=> $ui->views()->preferences( $uID, true, false ),
	),
	array(
		'label'		=> ht_dms_add_icon( __( 'Notification Settings', 'holotree' ), 'profile' ),
		'content' 	=> $ui->views()->preferences( $uID, true, true ),
	),
);

if ( ! is_null( $view_id ) && (int) $view_id !== (int) $uID ) {
	unset( $tabs[1] );
	unset( $tabs[2] );
	$tabs[] = array(
		'label' 	=> ht_dms_add_icon( __( 'Send Message', 'holotree' ), array( 'new', 'notification' ) ),
		'content'	=> 'Functionality not complete',
	);
}

return $ui->elements()->output_container( $tabs );
