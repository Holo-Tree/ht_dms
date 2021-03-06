<?php
/**
 * HoloTree DMS Task Management
 *
 * @package   @holotree_dms
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link      
 * @copyright 2014 Josh Pollock
 */

namespace ht_dms;

class task extends \ht_dms\dms\dms implements \Hook_SubscriberInterface {

	/**
	 * Set name of CPT this class is for.
	 *
	 * @var string
	 *
	 * @since 0.0.1
	 */
	public static $type = HT_DMS_TASK_POD_NAME;


	/**
	 * Set actions
	 *
	 * @since 0.0.3
	 *
	 * @return array
	 */
	public static function get_actions() {
		$type = self::$type;
		return array();
	}

	/**
	 * Set filters
	 *
	 * @since 0.0.3
	 *
	 * @return array
	 */
	public static function get_filters() {
		$type = self::$type;
		return array(
			"ht_dms_{$type}_edit_form_fields" => array( 'form_fields', 10, 6 ),
			"pods_api_post_save_pod_item_{$type}" => array( 'post_save', 10, 2 ),
		);

	}

	/**
	 * Set the name of the CPT
	 *
	 * @param 	string 	$type
	 *
	 * @since 0.0.1
	 */
	function set_type() {

		return self::$type;

	}

	/**
	 * Holds the instance of this class.
	 *
	 *
	 * @access private
	 * @var    object
	 */
	private static $instance;


	/**
	 * Returns the instance.
	 *
	 * @since  0.0.1
	 * @access public
	 * @return object
	 */
	public static function init() {

		if ( !self::$instance )
			self::$instance = new self;

		return self::$instance;

	}

	/**
	 * Set the form fields for editing/ creating
	 *
	 * parameters are documented in ht_dms/dms.php
	 *
	 * @see dms::edit()
	 *
	 * @return 	array
	 *
	 * @since	0.0.1
	 */
	function form_fields( $form_fields, $new, $id, $obj, $oID, $uID  ) {

		if ( is_array( $oID ) ) {
			$oID = $oID[ 0 ];
		}
		$dID =  $form_fields[ 'decision' ][ 'default' ];

		unset( $form_fields );


		$form_fields[ 'name' ] = array(

		);
		$form_fields[ 'task_description' ] = array();
		$form_fields[ 'assigned_user' ] = array( 'default' => $uID );
		$form_fields[ 'blockers' ] = array(
			'label' => __( 'Tasks that must be completed before this task is completed.', 'ht_dms' ),
		);
		$form_fields[ 'blocking' ] = array(
			'label' => __( 'Tasks that can only be completed after this task is completed.', 'ht_dms' )
		);
		$gID = $this->get_group( $id, $obj, $dID, false );


		if ( $new  ) {

			$form_fields[ 'decision' ] = array(
				'default' => $dID,
				'type' => 'hidden'
			);
			$form_fields[ 'organization' ]= array(
				'default' => $oID,
				'type' => 'hidden',
			);

			$form_fields[ 'decision_group' ] = array(
				'default' => $gID,
				'type' => 'hidden',
			);
		}

		return $form_fields;

	}

	/**
	 * Hide form fields.
	 *
	 * parameters are documented in ht_dms/dms.php
	 *
	 * @see dms::edit()
	 *
	 * @return 	string
	 *
	 * @since 	0.0.1
	 */
	function form_fix_jQuery( $jQuery, $new = true ) {
		if ( $new  ) {
			//fix for new decision form
			$jQuery = "//fix for new decision form
			$( 'li.pods-form-ui-row-name-decision-group, li.pods-form-ui-row-name-decision' ).hide();
			";
		}
		else {
			//fix for propose-modify form
			$jQuery = "//fix for propose-modify form
		$( 'li.pods-form-ui-row-name-decision-group, li.pods-form-ui-row-name-decision' ).hide();
		";
		}

		return $jQuery;

	}

	function status_decider( $id, $obj = null ) {
		$obj = $this->task( $id, true, false, $obj );
		if ( $obj->id() != $id ) {
			ht_dms_error( __LINE__, var_dump (array( $id, $obj->id() )) );
		}
		if ( $this->is_blocked( $id, $obj ) ) {
			$status = 'blocked';

		}
		else {
			$status = 'doable';
		}

		foreach( $obj->fields( ) as $key =>$value ) {
			$data[ $key ] = $obj->field( $key );
		}

		$data ['task_status' ] = $status;
		$id = $obj->save( $data );
		if ( $obj->id() != $id ) {
			ht_dms_error( __LINE__, __METHOD__ );
		}
		$this->reset_cache( $id );
		ht_dms_decision_class()->reset_cache( $id );
		//$id = $this->update( $id, 'task_status', $status, $obj );

		return $id;
	}

	function post_save( $pieces, $id ) {

		$this->status_decider( $id );
		$uID = $pieces[ 'fields' ][ 'assigned_user' ][ 'value' ];

		$this->user_fix( $uID );


	}

	function user_fix( $id ) {
		if ( get_user_by( 'id', $uID ) !== false ) {
			$tasks = get_user_meta(  $uID, 'tasks_assigned' );
			$tasks[] = $id;
			update_user_meta( $uID, 'tasks_assigned', $tasks );
		}
	}

	function block_change( $id, $which = 'blocker', $add = true, $obj = null ) {
		if ( $which !== 'blocker' || 'blocking' ) {
			ht_dms_error( "$which must ===  'blocker' || 'blocking' in ", __METHOD__ );
		}
		else {
			$obj = $this->null_obj( $obj, $id );
			$value = $obj->field( $which );
			if ( $add ) {
				$value[] = $id;
			}
			else {
				if ( ( $key = array_search( $id, $value ) ) !== false) {
					unset( $value[ $key ] );
				}
			}

			$id = $this->update( $id, $which, $value );
		}

		return $id;

	}

	function add_blocker( $id, $blocker_id, $obj= null ) {
		$id = $this->block_change( $id, 'blocker', true, $obj );

		return $id;

	}

	function add_blocking( $id, $blocking_id, $obj= null ) {
		$id = $this->block_change( $id, 'blocking', true, $obj );

		return $id;

	}

	function remove_blocker( $id, $blocker_id, $obj= null ) {
		$id = $this->block_change( $id, 'blocker', false, $obj );

		return $id;

	}

	function remove_blocking( $id, $blocker_id, $obj= null ) {
		$id = $this->block_change( $id, 'blocking', false, $obj );

		return $id;
	}

	/**
	 * Check status of tasks for a decison. Correct if need be, alert if need be.
	 *
	 * Hook this to something!
	 *
	 * @param 	int $dID ID of decision to check tasks for.
	 *
	 * @since	0.0.1
	 *
	 */
	function status_checks( $dID ) {
		$params = array(
			'where' => 'd.decision.ID = "'.$dID.'"',
		);
		$obj = $this->pods_object( false );
		if ( $obj->total() > 0 ) {
			while ( $obj->fetch() ) {
				$changes = $this->status_check( $obj->id(), $obj );
				if ( $changes[ 'changed'] === true ) {
					$this->task_alert( $obj->id(), $obj= null, $changes[ 'status' ] );
				}

			}

		}

	}

	/**
	 * Checks the status of a task and updates if update is needed.
	 *
	 * @param 	int			$id
	 * @param 	obj|null 	$obj Optional. Single task Pods object. If no object is provided, one will be created.
	 *
	 * @returns	array		Array containing ID, new status and boolean that is true if a change was made, false if not.
	 *
	 * @since 	0.0.1
	 */
	function status_check( $id, $obj= null ) {
		$changed = false;
		$obj = $obj = $this->null_obj( $obj, $id );
		$current_status = $this->status( $id, $obj );
		if ( $current_status === 'doable' ) {
			//@TODO make sure false is right return if there are no blockers
			if ( $this->is_blocked( $id, $obj ) !== false ) {
				$status = 'blocked';
				$changed = true;
				//send notification that task is now blocked?
			}

		}
		else {
			if ( $this->is_blocked( $id, $obj ) !== false ) {
				$status = 'doable';
				$changed = true;
				//@TODO Send notification that task is now doable
			}
		}

		$this->update( $id, 'task_status', $status, $obj );

		$check = array( $id, $status, $changed );

		return $check;

	}

	/**
	 * Get tasks's status.
	 *
	 * Note: Will set status if none has been set. If a sttatus is set this method returns it whether it is right or not. $this->status_decider() can be used to check/correct.
	 *
	 * @param   int 		$id		ID of task.
	 * @param 	obj|null 	$obj 	Optional. Single task Pods object. If no object is provided, one will be created.
	 *
	 * @return 	string				The status.
	 *
	 * @since	0.0.1
	 */
	function status( $id, $obj = null  ) {
		$obj = $obj = $this->null_obj( $obj, $id );
		$status = $obj->field( 'task_status' );
		if ( $status === false ) {
			$this->status_decider( $id, $obj );
			$status = $obj->field( 'task_status' );
		}

		return $status;

	}

	/**
	 * Mark a task completed.
	 *
	 * @param   int 		$id		ID of task.
	 * @param 	obj|null 	$obj 	Optional. Single task Pods object. If no object is provided, one will be created.
	 *
	 * @return 	bool
	 *
	 * @since 	0.0.1				ID of task.
	 */
	function completed( $id, $obj = null ) {
		$obj = $this->task( $id, true, false, $obj );
		$id = $this->update( $id, 'task_status', 'done', $obj );

		return $id;

	}

	/**
	 * Check if a task is blocked.
	 *
	 * @param   int 		$id		ID of task.
	 * @param 	obj|null 	$obj 	Optional. Single task Pods object. If no object is provided, one will be created.
	 *
	 * @return 	bool
	 *
	 * @since 	0.0.1				True if it is blocked.
	 */
	function is_blocked( $id, $obj = null ) {
		$obj = $this->task( $id, true, false, $obj );
		if ( $obj->field('blockers') !== false ) {
			return true;
		}

	}

	/**
	 * Check if a task is possible, IE it has no blockers.
	 *
	 * @param   int 		$id		ID of task.
	 * @param 	obj|null 	$obj 	Optional. Single task Pods object. If no object is provided, one will be created.
	 *
	 * @return 	bool
	 *
	 * @since 	0.0.1				True if it is possible.
	 */
	function possible( $id, $obj= null ) {
		$obj = $obj = $this->null_obj( $obj, $id );
		$status = $this->status( $id, $obj );

		if ( $status === 'doable' ) {
			return true;
		}

	}

	/**
	 * Get the tasks that a task is blocked by. Can return field array of the blocking task(s) or array of IDs of the blocking tasks.
	 *
	 *
	 * @param   int 		$id			ID of task to check which tasks it is blocking
	 * @param 	obj|null 	$obj 		Optional. Single task Pods object. If no object is provided, one will be created.
	 * @param	bool		$id_only	Optional. Return IDs only. Default is false. If false the whole field array is returned foreach.
	 *
	 * @return 	array					Either array of fields array(s) or array of IDs.
	 *
	 * @since 0.0.1
	 */
	function blocked_by( $id, $obj= null, $ids_only = false ) {
		$obj = $this->task( $id, true, false, $obj );
		$blockers = $obj->field( 'blockers' );

		if ( $ids_only && is_array( $blockers ) ) {
			foreach ( $blockers as $blocker ) {
				$ids[ ] = $blocker[ 'term_id' ];
			}

			return $ids;

		}
		else {
			return $blockers;
		}

	}

	/**
	 * Find which tasks a task is blocking. Can return field array of the the task(s) it is blocking or array of IDs of the task(s) it is blocking.
	 *
	 * @param   int 		$id 	ID of task to check for which tasks it is blocking
	 * @param 	obj|null 	$obj 	Optional. Single task Pods object. If no object is provided, one will be created.
	 * @param	bool		$id_only	Optional. Return IDs only. Default is false. If false the whole field array is returned foreach.
	 *
	 * @return 	array				Either array of fields array(s) or array of IDs.
	 *
	 * @since 0.0.1
	 */
	function blocking( $id, $obj = null, $ids_only = false ) {
		$obj = $this->task( $id, true, false, $obj );
		$blocking = $obj->field( 'blocking' );

		if ( $ids_only && is_array( $blocking ) ) {
			foreach( $blocking as $block ) {
				$ids[ ] = $block[ 'term_id' ];

				return $ids;

			}

		}
		else {
			return $blocking;

		}


	}

	/**
	 * @param      $id
	 * @param null $uID
	 * @param null $obj
	 *
	 * @return mixed
	 */
	function assign( $id, $uID = null, $obj = null ) {
		$obj = $obj = $this->null_obj( $obj, $id );
		$uID = $this->null_user( $uID );
		if ( $this->user_exists( $uID ) ) {
			$id = $this->update( $id, 'assigned_user', $uID, $obj );

			return $id;

		}

	}

	/**
	 * @param null $uID
	 * @param bool $dID
	 * @param int  $limit
	 * @param 	bool		$completed	Optional. If true (the default) completed task will be included.

	 *
	 * @return array
	 */
	function users_tasks( $uID = null, $dID = false, $limit = 5, $return = 'ids', $completed = true ) {
		$tasks = null;
		$uID = $this->null_user( $uID );
		$where = 'assigned_user.ID = "'.$uID.'"';
		if ( $dID ) {
			$where .= ' AND decision.ID = "'.$dID.'"';
		}

		if ( $completed !== true ) {
			$where .= ' AND d.completed = "0" ';
		}

		if ( ! $limit ) {
			$limit = -1;
		}

		$obj = $this->item( null, null, array( 'where' => $where, 'limit' => $limit ) );
		if ( $obj->total() > 0 ) {
			while( $obj->fetch() ) {
				if ( $return === 'ids' ) {
					//$tasks[ ] = $obj->field( 'term_id' );
					$tasks[] = array( $obj->field( 'term_id' ), $obj->field( 'decision' ) );
				}
				elseif( $return === 'array' ) {
					$fields = $obj->fields();
					foreach ( $fields as $field ) {
						$task[ $field[ 'name'] ] = $obj->field( $field[ 'name' ] );
					}
					$task[ 'id' ] = $obj->id();
					$task[ 'term_id' ] = $obj->field( 'term_id' );
					$task[ 'name' ] = $obj->field( 'name' );

					$tasks[] = $task;
				}
				else {
					$tasks[] = ht_dms_link( $obj->field( 'term_id' ), 'tax', $obj->display( 'name' ), $obj->display( 'name') );
				}

			}

			if ( $return === 'links' ) {
				$tasks = implode( '<br>', $tasks );
			}

			return $tasks;

		}


	}

	/**
	 * The number of task assigned to a user.
	 *
	 * @param      $uID
	 * @param null $obj
	 *
	 * @return mixed
	 *
	 * @since 0.0.1
	 */
	function number_assigned_tasked( $uID, $obj = null ) {
		$uID = $this->null_user( $uID );
		$obj = $obj = $this->null_obj( $obj );
		$obj = $obj->find( array( 'where' => 'd.completed = "0"' ) );

		return $obj->total();

	}

	function task_exists( $id ) {
		if ( term_exists( $id, HT_DMS_TASK_POD_NAME ) ) {
			return true;
		}

	}

	function get_link( $id ) {
		if ( $this->task_exists( $id ) ) {
			$link = get_term_link( $id, HT_DMS_TASK_POD_NAME );
			return $link;
		}

	}

	/**
	 * Get group this task belongs to.
	 *
	 * Will update value if it isn't set, based on the decision field.
	 *
	 * @param 	int     		$id		Task ID.
	 * @param 	null|obj|Pods 	$obj	Optional. Task object.
	 * @param 	int|null		$dID	Optional. Decision ID. Needed for setting decision for new decisions.
	 * @param	bool			$fix	Optional. Whether to attempt to fix if not set. Defaults to true.
	 *
	 * @return 	int|null
	 *
	 * @since	0.0.1
	 */
	function get_group( $id, $obj = null, $dID = null, $fix = true ) {
		$obj = $this->null_object( $obj, $id );
		$gID = (int) $obj->display( 'group.ID' );
		if ( ! $gID ) {

			$dObj = ht_dms_decision( $dID );

			$gID = (int) $dObj->display( 'group.ID' );

			if ( $fix ) {
				$this->update( $id, 'decision_group', $dID );
			}

		}

		return $gID;

	}

	/**
	 * Get decision this task belongs to.
	 *
	 * @param 	int     		$id		Task ID.
	 * @param 	null|obj|Pods 	$obj	Optional. Task object.
	 *
	 * @return 	int|null
	 *
	 * @since	0.0.1
	 */
	function get_decision( $id, $obj = null ) {
		$obj = $this->null_object( $obj, $id );

		return (int) $obj->display( 'decision.ID' );
	}

	/**
	 * Get organization this task belongs to.
	 *
	 * Will update value if it isn't set, based on the decision field.
	 *
	 * @param 	int     		$id		Task ID.
	 * @param 	null|obj|Pods 	$obj	Optional. Task object.
	 * @param 	int|null		$dID	Optional. Decision ID. Needed for setting decision for new decisions.
	 * @param	bool			$fix	Optional. Whether to attempt to fix if not set. Defaults to true.
	 *
	 * @return 	int|null
	 *
	 * @since	0.0.1
	 */
	function get_organization( $id, $obj = null, $dID = null, $fix = true ) {
		$obj = $this->null_object( $obj, $id );
		$oID = (int) $obj->display( 'organization.ID' );
		if ( ! $oID ) {
			if ( is_null( $dID ) ) {
				ht_dms_error();
			}

			$dObj = ht_dms_decision( $dID );
			$oID = (int) $dObj->display( 'organization.ID' );

			if ( $fix ) {
				$this->update( $id, 'organization', $oID, $obj );
			}

		}

		return $oID;

	}

} 
