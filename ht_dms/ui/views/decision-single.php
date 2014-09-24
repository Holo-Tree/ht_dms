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

global $post;
global $id;
$id = $post->ID;
$d = ht_dms_decision_class();
$ui = ht_dms_ui();
$obj = $d->item( $id );
if ( !is_object( $obj) ) {
	ht_dms_error( 'Not an object!', __FILE__ );
}

if ( pods_v( 'dms_action', 'get', false, true ) === 'changing' ) {
	return $ui->add_modify()->modify_decision( $id, $obj, null );

}
else {

		$paginated_view_args = ht_dms_default_paginated_view_arguments( array( 'dID' => $id ) );
		$current = $ui->views()->decision( $obj, $id );
		$status = $obj->field( 'decision_status' );
		$status = strtolower( $status );
		if ( $status === 'new' ) {
			$status = 'open';
		}
		$what = $status.'-decision';
		$current .= $ui->views()->action_buttons( $obj, $id, $what );

		$tabs = array (
			array (
				'label'   => ht_dms_add_icon( __( 'Decision Information', 'ht_dms' ), 'details' ),
				'content' => $current
			),
			array(
				'label'	 	=> ht_dms_add_icon( __( 'Discussion', 'ht_dms' ), 'discussion' ),
				'content' 	=> $ui->elements()->discussion( $id, 5, true ),
			),
			array(
				'label'		=> ht_dms_add_icon( __( 'Decision Documents', 'ht_dms' ), 'docs' ),
				'content'	=> $ui->views()->docs( $obj, $id, 'decision' ),
			),
			array(
				'label'	 	=> ht_dms_add_icon( __( 'View Tasks', 'ht_dms' ), 'task' ),
				//'content'	=> ht_dms_paginated_view_container( 'decisions_tasks', $paginated_view_args )
				'content' 	=> $ui->views()->decisions_tasks( null, $id ),
			),
			array (
				'label'   => ht_dms_add_icon( __( 'Add Task', 'ht_dms' ), array( 'new', 'task') ),
				'content' => $ui->add_modify()->new_task(  null, null, $id ),
			),
		);

		$proposed_modifications = $ui->views()->proposed_modifications( $id );

		if ( is_string( $proposed_modifications ) ) {
			$tabs[ ] = array (
				'label'   => ht_dms_add_icon( __( 'Proposed Modifications', 'ht_dms' ), 'modification' ),
				'content' => $proposed_modifications,
			);
		}

		return $ui->elements()->output_container( $tabs );


}
