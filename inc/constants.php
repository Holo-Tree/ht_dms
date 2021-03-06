<?php
/**
 * Define HoloTree DMS constants
 *
 * @package   @holotree_dms
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link      
 * @copyright 2014 Josh Pollock
 */


if ( !defined ( 'HT_DMS_DIR' )  ) {
	define( 'HT_DMS_DIR', trailingslashit( HT_DMS_ROOT_DIR ) . 'ht_dms' );
}

if ( !defined ( 'HT_DMS_UI_DIR' ) && defined( 'HT_DMS_DIR' ) ) {
	define( 'HT_DMS_UI_DIR', trailingslashit( HT_DMS_DIR ) . 'ui' );
}

if ( !defined( 'HT_DMS_VIEW_DIR' ) && defined( 'HT_DMS_DIR' ) ) {
	define( 'HT_DMS_VIEW_DIR', trailingslashit( HT_DMS_UI_DIR ) . 'templates' );
}

if ( !defined( 'HT_DMS_DECISION_POD_NAME' ) ) {
	define( 'HT_DMS_DECISION_POD_NAME', 'ht_dms_decision' );
}

if ( !defined( 'HT_DMS_GROUP_POD_NAME' ) ) {
	define( 'HT_DMS_GROUP_POD_NAME', 'ht_dms_group' );
}

if ( !defined( 'HT_DMS_TASK_POD_NAME' ) ) {
	define( 'HT_DMS_TASK_POD_NAME', 'ht_dms_task' );
}

if ( !defined( 'HT_DMS_NOTIFICATION_POD_NAME' ) ) {
	define( 'HT_DMS_NOTIFICATION_POD_NAME', 'ht_dms_notification' );
}

if ( !defined( 'HT_DMS_ORGANIZATION_POD_NAME' ) ) {
	define( 'HT_DMS_ORGANIZATION_POD_NAME', 'ht_dms_organization' );
}

if ( !defined( 'HT_HT_DMS_USE' ) ) {
	define( 'HT_HT_DMS_USE', true );
}

if ( !defined( 'HT_DMS_PREFIX' ) ) {
	define( 'HT_DMS_PREFIX', 'ht_dms' );
}

if ( ! defined( 'HT_DEV_MODE' ) ){
	define( 'HT_DEV_MODE', false );
}
if ( ! defined( 'HT_FOUNDATION' ) ) {
	define( 'HT_FOUNDATION', true );
}
