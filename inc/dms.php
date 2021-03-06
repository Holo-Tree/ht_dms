<?php
/**
 * Get DMS classes or objects from class.
 *
 * @package   @holotree_dms
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link      
 * @copyright 2014 Josh Pollock
 */

/**
 * Return decision object or field array
 *
 * @param 	int|bool	$id 	Optional. ID of decision. Default is true, which returns complete object for post type.
 * @param 	obj|null	$obj	Optional. Prebuilt Object. If is valid Pods object, <em>of any Pod</em> this object is returned and no other parameters matter. If not is new object is created.
 * @param 	bool		$cached	Optional. Whether to get cached value, if possible. Default is true.
 * @param	bool		$fields Optional. Whether to return field array instead of object. Default is false, which returns object.
 *
 * @return 	obj|array|Pods			Decision Pods object or field array.
 *
 * @since 	0.0.1
 */
function ht_dms_decision( $id = true, $obj = null, $cached = true, $fields = false ) {

	return ht_dms_decision_class()->item( $id, $obj, null, $cached, $fields );

}

/**
 * Get an instance of the Decision class
 *
 * @return object|ht_dms\decision
 *
 * @since	0.0.1
 */
function ht_dms_decision_class() {

	return ht_dms\decision::init();


}

/**
 * Return group object or field array
 *
 * @param 	int|bool	$id 	Optional. ID of group. Default is true, which returns complete object for post type.
 * @param 	obj|null	$obj	Optional. Prebuilt Object. If is valid Pods object, <em>of any Pod</em> this object is returned and no other parameters matter. If not is new object is created.
 * @param 	bool		$cached	Optional. Whether to get cached value, if possible. Default is true.
 * @param	bool		$fields Optional. Whether to return field array instead of object. Default is false, which returns object.
 *
 * @return 	obj|array|Pods			Group Pods object or field array.
 *
 * @since 	0.0.1
 */
function ht_dms_group( $id = true, $obj = null, $cached = true, $fields = false ) {

	return ht_dms_group_class()->item( $id, $obj, null, $cached, $fields );

}

/**
 * Get an instance of the Group class
 *
 * @return object|ht_dms\group
 *
 * @since	0.0.1
 */
function ht_dms_group_class() {

	return ht_dms\group::init();


}

/**
 * Get an instance of the Task class
 *
 * @return 	HoloTree_DMS_Task
 *
 * @return object|ht_dms\task
 *
 * @since	0.0.1
 */
function ht_dms_task_class() {

	return ht_dms\task::init();

}

/**
 * Return task object or field array
 *
 * @param 	int|bool	$id 	Optional. ID of task. Default is true, which returns complete object for post type.
 * @param 	obj|null	$obj	Optional. Prebuilt Object. If is valid Pods object, <em>of any Pod</em> this object is returned and no other parameters matter. If not is new object is created.
 * @param 	bool		$cached	Optional. Whether to get cached value, if possible. Default is true.
 * @param	bool		$fields Optional. Whether to return field array instead of object. Default is false, which returns object.
 *
 * @return 	obj|array|Pods			Task Pods object or field array.
 *
 * @since 	0.0.1
 */
function ht_dms_task( $id = true, $obj = null, $cached = true, $fields = false ) {

	return ht_dms_task_class()->item( $id, $obj, null, $cached, $fields );

}

/**
 * Get an instance of the Consensus class
 *
 * @return obj|ht_dms\helper\consensus
 *
 * @since	0.0.1
 */
function ht_dms_consensus_class() {

	return  ht_dms\helper\consensus::init();

}

/**
 * Get a consensus_ui or create one if it does not exist.
 *
 * @param 	int 	$dID ID of decision to get/ create for.
 *
 * @return 	array		The consensus_ui array.
 *
 * @since 	0.0.1
 */
function ht_dms_consensus( $dID ) {

	return ht_dms_consensus_class()->consensus( $dID );

}

/**
 * Get an instance of the Consensus class
 *
 * @return object|ht_dms\organization
 *
 * @since	0.0.1
 */
function ht_dms_organization_class() {

	return ht_dms\organization::init();

}

/**
 * Return organization object or field array
 *
 * @param 	int|bool	$id 	Optional. ID of organization. Default is true, which returns complete object for post type.
 * @param 	obj|null	$obj	Optional. Prebuilt Object. If is valid Pods object, <em>of any Pod</em> this object is returned and no other parameters
 * @param 	bool		$cached	Optional. Whether to get cached value, if possible. Default is true.
 * @param	bool		$fields Optional. Whether to return field array instead of object. Default is false, which returns object.
 * matter. If not is new object is created.
 *
 * @return 	obj|array|Pods			Organization Pods object or field array.
 *
 * @since 	0.0.1
 */
function ht_dms_organization( $id = true, $obj = false, $cached = true, $fields = false ) {
	
	return ht_dms_organization_class()->item( $id, $obj, null, $cached, $fields );

}

/**
 * Returns an instance of the notification class object
 *
 * @return object|ht_dms\notification
 *
 * @since 0.0.3
 */
function ht_dms_notification_class() {

	return ht_dms\notification::init();

}

/**
 * Return organization object or field array
 *
 * @param 	int|bool	$id 	Optional. ID of notification. Default is true, which returns complete object for post type.
 * @param 	obj|null	$obj	Optional. Prebuilt Object. If is valid Pods object, <em>of any Pod</em> this object is returned and no other parameters
 * @param 	bool		$cached	Optional. Whether to get cached value, if possible. Default is true.
 * @param	bool		$fields Optional. Whether to return field array instead of object. Default is false, which returns object.
 * matter. If not is new object is created.
 *
 * @return 	obj|array|Pods		Notification Pods object or field array.
 *
 * @since 	0.0.3
 */
function ht_dms_notification( $id, $obj = null, $cached = true, $fields = false ) {

	return ht_dms_notification_class()->item( $id, $obj, null, $cached, $fields );

}

/**
 * Returns an instance of the Preferences class
 *
 * @return ht_dms\helper\preferences
 *
 * @since 0.0.3
 */
function ht_dms_preferences_class() {

	return ht_dms\helper\preferences::init();

}

/**
 * Return an instance of the membership class
 *
 * @return ht_dms\helper\membership
 *
 * @since 0.0.2
 */
function ht_dms_membership_class() {

	return ht_dms\helper\membership::init();

}

/**
 * Get an instance of the DMS Common class
 *
 * @return ht_dms\helper\common
 *
 * @since 	0.0.1
 */
function ht_dms_common_class() {

	return ht_dms\helper\common::init();

}

/**
 * Get an instance of the ui class.
 *
 * @return \ht_dms\ui\ui
 *
 * @since 0.0.2
 */
function ht_dms_ui() {

	return ht_dms\ui\ui::init();

}

/**
 * Return an instance of the build elements class
 *
 * @return object|ht_dms\ui\build\elements
 *
 * @since 0.0.2
 */
function ht_dms_ui_build_elements() {

	return ht_dms_ui()->build_elements();

}

/**
 * Return an instance of the output elements class
 *
 * @return object|ht_dms\ui\output\elements
 *
 * @since 0.0.2
 */
function ht_dms_ui_output_elements() {

	return ht_dms_ui()->output_elements();

}



/**
 * Returns an instance of the automatic notifications class
 *
 * @return \ht_dms\helper\automaticNotifications|object
 *
 * @since 0.0.3
 */
function ht_dms_automatic_notifications_class() {

	return ht_dms\helper\automaticNotifications::init();

}

/**
 * Get any view defined in the ht_dms\ui\build\views class
 *
 * Wrapper for ht_dms\ui\build\views::get_view(). Exists to power ht_dms_ui_ajax_view(), but can be used independently.
 *
 * @param string 		$view 	The name of any method in the class.
 * @param array 		$args 	An array of arguments in order for the chosen method.
 * @param null|string 	$return	Optional. What to return. If used overrides, $args[ 'return'] Options: template|Pods|JSON|urlstring
 *
 * @return null|string|obj|Pods|JSON Either HTML for the view, Pods object, JSON object of the posts, or a URL string to get those posts via REST API.
 *
 * @since 0.0.1
 */
function ht_dms_ui_get_view( $view, $args, $return = null ) {

	return ht_dms_ui()->get_view( $view, $args, $return );

}

/**
 * Loads a view from the ht_dms\ui\build\views class via AJAX

 *
 * @since 0.0.1
 */
add_action( 'wp_ajax_ht_dms_ui_ajax_view', 'ht_dms_ui_ajax_view' );
add_action( 'wp_ajax_nopriv_ht_dms_ui_ajax_view', '__return_false' );
function ht_dms_ui_ajax_view() {
	if ( isset( $_REQUEST['nonce'] ) ) {
		if ( ! wp_verify_nonce( $_REQUEST[ 'nonce' ], 'ht-dms' ) ) {
			wp_die( __( 'Your attempt to request data via ajax using the function ht_dms_ui_ajax_view was denied as the nonce did not match.', 'ht_dms' ) );
		}

		if ( isset( $_REQUEST[ 'view' ] ) && isset( $_REQUEST[ 'args' ] ) ) {
			$view = $_REQUEST[ 'view' ];
			$args = $_REQUEST[ 'args' ];
		}
		else {
			exit;
		}

		$return = 'template';

		if ( isset( $_REQUEST[ 'returnType' ] ) && in_array( $_REQUEST[ 'returnType' ], array( 'template', 'JSON', 'urlstring' ) ) ) {
			$return = $_REQUEST[ 'returnType' ];
		}

		if ( $return === 'JSON' || 'urlstring' ) {
			if ( ! defined( 'PODS_JSON_API_VERSION' ) || ! defined( 'JSON_API_VERSION' ) ) {
				wp_die( __( 'Error! Error! You must install Pods JSON API and WordPress REST API to get objects from the API!', 'ht_dms' ) );
			}
		}

		$methods = get_class_methods( ht_dms_ui()->views() );

		if ( is_array( $methods ) && in_array( $view, $methods ) && ! in_array( $view, array ( 'ui', 'models', 'type_view', 'init' ) ) ) {

			wp_die( ht_dms_ui_get_view( $view, $args, $return ) );

		}
	}
	else {
		if (  ! ( defined ( 'HT_DEV_MODE' ) || ! HT_DEV_MODE ) ) {
			exit;
		}
		else {
			wp_die( print_r2(
					array(
						'view' => $view,
						'args' => $args,
						'request' => $_REQUEST,
					)
				)
			);
		}

	}

}


