<?php
/**
 * Various elements that make up the UI.
 *
 * @package   @TODO
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link      
 * @copyright 2014 Josh Pollock
 */

namespace ht_dms\ui\output;

use ht_dms\api\internal\access;
use ht_dms\helper\json;
use ht_dms\ui\build\baldrick\modal;
use ht_dms\ui\build\elements\consensus_ui;

class elements {
	/**
	 * Show comments and comment form.
	 *
	 * Comment form is in a modal.
	 *
	 * @param   int 	$id 		Post ID.
	 * @param 	int 	$per_page	Optional. Number of comments per page. Default is ten.
	 * @param 	bool	$form		Optional. Whether to show new comment form or not. Default is true.
	 *
	 * @return 	string	$out		The comments and form.
	 *
	 * @since 0.0.1
	 */
	public function discussion( $id, $form = true ) {
		$out = '<div id="discussion" data-id="'.esc_attr( $id ).'">';


		$out .=  '</div>';
		if ( $form !== false ) {
			$text = __( 'Respond', 'holotree' );
			$out .= $this->modal( $this->comment_form( $id ), 'discussion-modal', $text, 'large', true );
		}

		return $out;

	}

	/**
	 * Return JSON for comments
	 *
	 * @since 0.3.0
	 *
	 * @param int $id Decision or group ID
	 * @param null|array $comments Optional. Pre supplied comments data. If null, the default, all comments for current id are returned.
	 *
	 * @return mixed|string|void
	 */
	public static function comment_json( $id, $comments  = null ) {
		//Gather comments for a specific page/post
		if ( is_null( $comments ) ) {
			$comments = get_comments( array(
				'post_id' => $id,
				'status'  => 'approve'
			) );
		}

		return json::prepare_comments( $comments );

	}

	/**
	 * The comment form
	 *
	 * @param 	int 	$id	ID of post to add comment to.
	 *
	 * @return 	string		The comment form.
	 *
	 * @since 0.0.1
	 */
	public function comment_form( $id ) {
		$link = ht_dms_url( $id, 'post-type' );
		$link = add_query_arg( 'add-comment', true, $link );
		$link = add_query_arg( 'dms_id', $id, $link );


		$form = sprintf(  '<form action="%1s" method="POST" id="dms-comment-form">', $link );
		$form .= sprintf( '<input type="hidden" name="dms_id" value="%1s">', $id );
		$form .= wp_nonce_field( 'ht_dms_comment_nonce', 'ht_dms_nonce' );
		$form .= '<input type="hidden" name="dms_action" value="add-comment">';
		$form .= '<label>Comment Text
						<textarea name="dms_comment_text" placeholder=""></textarea>
		  		</label>';
		$form .= '<input type="submit" />';
		$form .= '</form>';

		return $form;

	}

	/**
	 * Create a Foundation Reveal modal.
	 *
	 * @param 	string		$content		Content of the modal itself.
	 * @param 	string      $modal_id		ID for modal.
	 * @param 	string      $trigger_text	Text for link that triggers modal.
	 * @param 	string 		$size 			Optional. Size of modal. tiny|small|medium|large|xlarge Default is large.
	 * @param 	bool   		$button			Optional. Whether to make the trigger link a button or not. Default is false--not a button.
	 *
	 * @see		http://foundation.zurb.com/docs/components/reveal.html
	 *
	 * @return 	string						Modal + Trigger
	 *
	 * @since	0.0.1
	 */
	public function modal( $content, $modal_id, $trigger_text, $size= 'large', $button = true ) {
		$class = '';
		if ( $button !== false ) {
			$class = 'button';
		}
		$class .= ' '.$modal_id;
		$trigger = '<a href="#" data-reveal-id="'.$modal_id.'" class="'.$class.'" data-reveal>'.$trigger_text.'</a>';
		$modal = '<div id="'.$modal_id.'" class="reveal-modal '.$size.'" data-reveal>';
		$modal .= $content;
		$modal .= '</div>';

		return $trigger.$modal;

	}

	/**
	 * Make an AJAX modal using reveal or Baldrick
	 *
	 *
	 * @since 0.3.0
	 *
	 * @param string $type
	 * @param $action
	 * @param string $trigger_text
	 * @param array $args
	 *
	 * @return string
	 */
	public function ajax_modal( $type = 'baldrick', $action, $trigger_text = '', $args = array() ) {
		if ( 'baldrick' == $type ) {
			return modal::make( $action, $args, $trigger_text );

		}

		$url = access::get_url( $action );

		if ( isset( $args[ 'url_params' ] ) ) {
			foreach( $args[ 'url_params' ] as $arg => $value ) {
				if ( $arg && $value ) {
					$url = add_query_arg( urlencode( $arg ), urlencode( $value ), $url );
				}

			}

		}

		$default_id = 'ht-dms-ajax-modal'.rand();
		$id = pods_v( 'id', $arg, $default_id );

		return sprintf( '<a data-reveal-ajax="%1s" id="%2s" data-reveal-ajax="true">%3s</a>', $url, $id, $trigger_text );

	}

	/**
	 * Creates tabbed UI
	 *
	 *
	 * @param	array	 $tabs {
	 *     For each tab, label and content. First tab will be active by default.
	 *
	 *     @type string $label 		Label for tab.
	 *     @type string $content 	Content of tab.
	 * }
	 * @param string|bool $tab_prefix Optional. Prefix for tab IDs. Default is "tab_"
	 * @param string $class Optional Class for outermost container.
	 * @param string|bool $id    Optional. ID for outermost container.
	 *
	 * @return 	string
	 *
	 * @since 	0.0.1
	 */
	public function tab_maker( $tabs, $tab_prefix = 'tab_', $class = '', $id = false ) {

		return $this->tabs( $tabs, $tab_prefix, $class, false, $id );


	}

	/**
	 * Create the actual tabs
	 *
	 * @access protected
	 *
	 *
	 * @param	array	 $tabs {
	 *     For each tab, label and content. First tab will be active by default.
	 *
	 *     @type string $label 		Label for tab.
	 *     @type string $content 	Content of tab.
	 * }
	 * @param string|bool $tab_prefix Optional. Prefix for tab IDs. Default is "tab_"
	 * @param bool $class Optional. If true, tabs will be vertical. If false, the default, they will be horizontal.
	 * @param string|bool $id    Optional. ID for outermost container.
	 *
	 * @return string
	 */
	protected function tabs( $tabs, $tab_prefix = 'tab_', $class = '', $vertical = false, $id = false ) {

		if ( ! $tab_prefix ) {
			$tab_prefix = 'tab_';
		}
		$class = $class.' tabs';

		/**
		 * Filter to change value of $vertical to force vertical tabs.
		 *
		 * @param bool $vertical True for vertical tabs, false for horizontal tabs.
		 *
		 * @return bool
		 *
		 * @since 0.0.2
		 */
		$vertical = apply_filters( 'ht_dms_foundation_vertical_tabs', $vertical, $tab_prefix );

		if ( $vertical ) {
			$vertical = 'vertical';
		}


		if ( $vertical ) {
			$class = $class. ' '.$vertical;

		}
		else {
			$vertical = '';
		}

		$out = sprintf( '<ul class="%1s" id="ht_dms-tabs" data-tab >', $class );
		if ( isset( $tabs[0]) ) {
			$out .= '<li class="tab-title active"><a href="#' . $tab_prefix . '1">' . $tabs[0]['label'] . '</a></li>';
		}
		$i = 2;
		foreach ( $tabs as $key => $value ) {
			if ( $key != 0 ) {
				$out .= '<li class="tab-title"><a href="#'.$tab_prefix.''.$i.'">'.$value[ 'label' ].'</a></li>';
				$i++;
			}

		}
		$out .= apply_filters( 'ht_dms_after_foundation_tab_choice', '' );
		$out .= '</ul>';

		$i = 1;

		if ( false == $id ) {
			$id = 'tabs';
		}

		$out .= sprintf( '<div id="%0s" class="tabs-content %1s" >', $id, $vertical );


		foreach ( $tabs as $key => $tab) {
			if ( $key === 0 ) {
				$out .= sprintf( '<div class="content active" id="%1s">%2s</div>', $tab_prefix.$i, $tab[ 'content']  );
				$i++;
			}
			else {
				$out .= sprintf( '<div class="content" id="%1s">%2s</div>', $tab_prefix.$i, $tab[ 'content']  );
				$i++;
			}

		}


		$out .= '</div><!--#tabs-->';

		return $out;
	}


	/**
	 * Create an accordion
	 *
	 * @param	array	 $panels {
	 *     For each tab, label and content. First tab will be active by default.
	 *
	 *     @type string $label 		Label for tab.
	 *     @type string $content 	Content of tab.
	 * }
	 * @param string $prefix Optional prefix for panel.
	 * @param string $class Optional. Class for accordion.
	 *
	 * @return string
	 */
	protected function accordion( $panels, $prefix = 'panel_', $class = '' ) {

			$out = '<dl class="accordion ' . $class . '" data-accordion>';
			$i = 0;
			foreach ( $panels as $panel ) {
				if ( isset( $panel[ 'content' ] )  && isset( $panel[ 'label' ] ) ) {


					$out .= '<dd>';
					$out .= '<a href="#' . $prefix . $i . '">' . $panel[ 'label' ] . '</a>';
					$out .= '<div id="' . $prefix . $i . '" class="content';
					if ( $i === 0 ) {
						$out .= ' accActive';
					}
					$out .= '">';
					$out .= $panel[ 'content' ];
					$out .= '</div></dd><!---' . $prefix . $i . '-->';

					$i++;
				}
			}
			$out .= '</dl><!--' . $class . ' accordion-->';

			return $out;

	}

	/**
	 * The URL of current page.
	 *
	 * @return 	string	The URL of current page.
	 *
	 * @since	0.0.1
	 */
	public function current_page_url() {
		return pods_current_url();

	}


	/**
	 * Get instance of UI class
	 *
	 * @return \ht_dms\ui\ui
	 *
	 * @since 	0.0.1
	 */
	public function ui(){
		$ui = ht_dms_ui();

		return $ui;

	}

	/**
	 * Create breadcrumbs markup
	 *
	 * @since 0.3.0
	 *
	 * @return string
	 */
	public function breadcrumbs() {
		$out = '';

		$name = apply_filters( 'ht_dms_name', 'ht_dms' );

		$logo = 'http://holotree.net/ht/plugins/gus_ui_mods/assets/img/ht-logo-tree-only-white-50.png';

		if ( $logo  ) {
			$logo = sprintf( '<img src="%1s" alt="Home" height="50" width="50" />', esc_url( $logo ) );
		}

		$home_link = ht_dms_home();
		$home_link = sprintf( '<div class="breadcrumb-part" id="site-logo-home-link"><a href="%0s" title="HoloTree Home">%1s</a></div>', $home_link, $logo );

		if (  ! ht_dms_is( 'home' ) || ht_dms_is_notification() ) {
			$titles = $oID = $gID = $dID = $tID = false;

			$id = get_queried_object_id();

			if ( ht_dms_is_organization( $id ) ) {
				$oID = $id;
			} elseif ( ht_dms_is_group( $id ) ) {
				$gID = $id;
				$oID = ht_dms_group_class()->get_organization( $gID );

			} elseif ( ht_dms_is_decision( $id ) ) {

				$dID = $id;
				$obj = ht_dms_decision( $id );
				$oID = ht_dms_decision_class()->get_organization( $id, $obj );
				$gID = ht_dms_decision_class()->get_group( $id, $obj );
			}

		}
		else{
			$out = sprintf( '<div id="breadcrumbs" class="breadcrumb-part">%1s</div>', $home_link );
			return $out;
		}

		$build_elements = ht_dms_ui()->build_elements();


		$titles = array( $home_link);
		foreach ( array(
			HT_DMS_ORGANIZATION_POD_NAME => $oID,
			HT_DMS_GROUP_POD_NAME => $gID,
			HT_DMS_DECISION_POD_NAME => $dID,
		) as $type => $id ) {
			if ( ht_dms_integer( $id ) ) {
				$name = get_the_title( $id );
				$link = get_the_permalink( $id );

				$type = ht_dms_prefix_remover( $type );
				$bread_names[ $type ] = $name;
				$icon = $build_elements->icon( $type );

				$titles[] = sprintf( '<div class="breadcrumb-part %2s"><a href="%3s">%4s<span class="title show-for-large-up">%5s</span></a></div>', $type, $link, $icon, $name );
			}

		}

		$out .= sprintf( '<div id="breadcrumbs">%1s</div>', implode( $titles ) );

		return $out;

	}



	/**
	 * Main title section/breadcrumbs
	 *
	 *
	 * @param 	int  		$id		ID of item to get title of
	 * @param 	null|obj	$obj	Optional. Single item object of current item. Not used for groups.
	 * @param 	bool		$task	Optional. Set to true if getting title for a task. Default is false.
	 *
	 * @return	string
	 *
	 * @since	0.0.1
	 */
	function title( $id, $obj = null, $task = false, $separator = ' - ' ) {
		return $this->breadcrumbs();
		$name = apply_filters( 'ht_dms_name', 'ht_dms' );

		$logo = apply_filters( 'ht_dms_logo_instead_of_name_in_title', false );

		if ( $logo  ) {
			$name = sprintf( '<img src="%1s" alt="Home" height="50" width="50" />', $logo );
		}
		$name = $this->link( null, 'front', $name );

		if (  ! ht_dms_is( 'home' ) || ht_dms_is_notification() ) {
			$titles = $oID = $gID = $dID = $tID = false;

			$id = get_queried_object_id();

			if ( ht_dms_is_organization( $id ) ) {
				$oID = $id;
			}
			elseif( ht_dms_is_group( $id  ) ) {
				$gID = $id;
				$oID = ht_dms_group_class()->get_organization( $gID );

			}
			elseif( ht_dms_is_decision( $id ) ) {

				$dID = $id;
				$obj = ht_dms_decision( $id );
				$oID = ht_dms_decision_class()->get_organization( $id, $obj );
				$gID = ht_dms_decision_class()->get_group( $id, $obj );
			}
			elseif( ht_dms_is_task( $id ) ) {
				//@todo if ! https://github.com/HoloTree/ht_dms/issues/55
			}



			foreach ( array(
				HT_DMS_ORGANIZATION_POD_NAME => $oID,
				HT_DMS_GROUP_POD_NAME => $gID,
				HT_DMS_DECISION_POD_NAME => $dID,
			) as $type => $id ) {
				if ( ht_dms_integer( $id ) ) {
					$titles[] = sprintf(
						'<span class="breadcrumbs-component" ><span class="breadcrumbs-label breadcrumbs-component">%1s:</span> %2s </span>',
						$build_elements->visualize_hierarchy_icon( $type ),
						$this->link( $id, 'permalink', get_the_title( $id ) )
					);
				}
			}

			if ( is_array( $titles ) ) {
				$name .= sprintf( '<span id="breadcrumbs-titles">%1s</span>', implode( $titles ) );
			}

		}

		$name = apply_filters( 'ht_dms_title_override', $name, $id );
		return $name;

	}

	/**
	 * For safely appending variables to urls. By default in the dms_action={action}&dms_id={id} pattern.
	 *
	 * @param 	string			$url	Base URL
	 * @param 	string|array	$action	Variable to append. If string should be value for 'dms_action'. To set action and value pass array.
	 *   Array arguments {
	 * 		@type string var 	The name of the variable to append.
	 * 		@type string value	The value of the variable.
	 *   }
	 * @param int               $id     ID of post.
	 * @param bool              $add_nonce Add the dms action nonce.
	 *
	 * @return 	string					URL
	 *
	 * @since 	0.0.1
	 */
	function action_append( $url, $action, $id = false, $add_nonce = true ) {
		if ( is_array( $action ) ) {
			$action_name = pods_v( 'var', $action, false, true );
			$action = pods_v( 'value', $action, false, true );
			if ( ! $action || ! $action_name ) {
				ht_dms_error();
			}
		}
		else {
			$action_name = apply_filters( 'ht_dms_action_name', 'dms_action' );
		}

		$url = add_query_arg( $action_name, $action, $url );

		if ( $id !== false ) {
			$id_var = apply_filters( 'ht_dms_action_id_var', 'dms_id' );

			$url = add_query_arg( $id_var, $id, $url );
		}

		if ( $add_nonce  ) {
			$url = $this->action_nonce( $url );
		}

		return $url;

	}

	/**
	 * @var string Nonce name used for action nonce
	 *
	 * @since 0.1.0
	 */
	public $action_nonce_name = 'dms-action-nonce';

	/**
	 * @var string Nonce action for action nonce
	 *
	 * @since 0.1.0
	 */
	public $action_nonce_action = 'dms-nonce-action';

	/**
	 * Add the action nonce to dms_action URLs.
	 *
	 * Designed to be used by $this->action_append() only.
	 *
	 * @since 0.1.0
	 *
	 * @access private
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	private function action_nonce( $url ) {

		return add_query_arg( $this->action_nonce_name, wp_create_nonce( $this->action_nonce_action ), $url );

	}

	/**
	 * Verify the action nonce
	 *
	 * @since 0.1.0
	 *
	 * @param string $nonce The nonce value to check.
	 *
	 * @return bool
	 */
	public function check_action_nonce( $nonce ) {
		return wp_verify_nonce( $nonce, $this->action_nonce_action );

	}


	/**
	 * For creating links with optional button, class and ID.
	 *
	 * @param int|string    $id			ID of post, post type or taxonomy to get link to or a complete URL, as a string.
	 * @param string 		$type		Optional. Type of content being linked to. post|post_type_archive|taxonomy|user. Not used if $id is a string. Default is post.
	 * @param bool|string 	$text		Optional. Text Of the link. If false, post title will be used.
	 * @param null|string	$title		Optional. Text for title attribute. If null none is used.
	 * @param bool|string   $button		Optional. Whether to output as a button or not. Defaults to false.
	 * @param bool|string   $classes	Optional. Any classes to add to link. Defaults to false.
	 * @param bool|string   $link_id	Optional. CSS ID to add to link. Defaults to false.
	 * @param bool|array	$append		Optional. Action and ID to append to array. should be action, id. If ID isn't set $id param is used. Default is true.
	 *
	 *
	 * @return null|string
	 */
	public function link( $id, $type = 'permalink', $text = 'view', $title = null, $button = false, $classes = false, $link_id = false, $append = false  ) {
		if ( is_object( $type ) ) {
			$type = 'post';
		}

		if ( is_object( $id ) ) {
			return false;
		}
		elseif( $type === 'home' || $type === 'front' ) {
			$url = ht_dms_home();
		}
		elseif ( intval( $id ) !== 0 ) {
			if ( $type === 'permalink' || $type === 'post' ) {
				$url = get_permalink( $id );
			}
			elseif ( $type === 'post_type_archive' || $type === 'cpt-archive' ) {
				$url = get_post_type_archive_link( $id );
			}
			elseif ( $type === 'taxonomy' || $type === 'tax' ) {
				if ( term_exists( $id, HT_DMS_TASK_POD_NAME ) ) {
					$url = get_term_link( $id, HT_DMS_TASK_POD_NAME );
				}
				else {
					$url = '#';
				}


			}
			elseif ( $type === 'user' ) {
				$url = get_author_posts_url( $id );
			}
			else {
				ht_dms_error( $type . ' Is an unacceptable $type for', __FUNCTION__ );
			}
		}
		else {
			$url = $id;
		}

		if ( ( $text === 'view' || is_null( $text ) ) && ( $type === 'permalink' || $type === 'post' ) ) {
			$post = get_post( $id );
			if ( is_object( $post ) && is_string( $post->post_title ) && !empty( $post->post_title ) ) {
				$text = $post->post_title;
			}
		}

		$class = '';
		if ( $classes !== FALSE ) {
			$class = $classes;
		}
		if ( $button !== FALSE ) {
			$class .= 'button';
		}

		$stuff = '';
		if ( $class !== '' ) {
			$stuff .= ' class="'. $class . '" ';
		}
		if ( $link_id !== false ) {
			$stuff .= ' id="' . $link_id . '" ';
		}
		if ( $title !== false  ) {
			$title = get_the_title( $id );
			$stuff .= ' title=" ' . $title . ' " ';
		}

		if ( $append !== false && isset( $append[ 'action' ] ) ) {
			$action = $append[ 'action' ];
			if ( isset( $append[ 'ID' ] ) ) {
				$id = $append[ 'ID' ];
			}

			$url = $this->action_append( $url, $action, $id );
		}

		$link = '<a href="' . $url . '"' . $stuff . ' >' . $text . '</a>';



		return $link;
	}

	/**
	 * Create an alert using the foundation class.
	 *
	 * @see http://foundation.zurb.com/docs/components/alert_boxes.html
	 *
	 * @param 	string  	$text 		Text of string.
	 * @param 	null|String $type		Type of alert. null|success|warning|alert|info|secondary. Default is null, which uses alert.
	 * @param 	bool 		$closeable	Optional. If true alert can be closed/ dismissed. Defaults to false.
	 * @param 	bool 		$rounded	Optional. If true corners will be rounded. Default is false.
	 *
	 * @return 	string
	 *
	 * @since	0.0.1
	 */
	public function alert( $text, $type = null, $closeable = false, $rounded = false ) {
		$alert = '<div data-alert class="alert-box ';
		if ( is_null( $type ) ) {
			$alert .= 'alert';
		}
		else {
			$alert .= '';
		}

		if ( $rounded ) {
			$alert .= ' rounded';
		}

		$alert .= '" >';

		$alert .= $text;
		if ( $closeable ) {
			$alert .= '<a href="#" class="close">&times;</a>';
		}

		$alert .= '</div><!--.alert-box-->';

		return $alert;

	}

	function task_link( $id = null, $text = null, $title = null, $button = false ) {

		if ( is_null( $id ) ) {
			$id = get_queried_object_id();
		}

		$url = get_term_link( $id, HT_DMS_TASK_POD_NAME );

		if ( is_null( $text ) ) {
			$term = get_term( $id, HT_DMS_TASK_POD_NAME );
			if ( is_object( $term ) && ! is_a( $term, 'WP_Error' ) ) {
				$text = $term->name;
			}
			if ( is_a( $term, 'WP_Error' ) ) {
				$text = 'task';
			}

		}

		if ( is_null( $title ) ) {
			if ( is_null( $text ) || is_object( $text ) || ! is_string( $text ) ) {
				$text = 'Task';
			}

			$title = 'View '.$text;
		}

		$class = '';
		if ( $button ) {
			$class = 'class="button"';
		}


		if ( is_string( $url ) && is_string( $title ) && is_string( $text ) ) {
			$out = '<a href=' . esc_url( $url ) . '" text="' . esc_attr( $title ) . '">' . $text . '</a>';

			return $out;
		}

	}

	/**
	 * Outputs content in tabs or accordion according to device detection.
	 *
	 * @param array        $content The content to output. Should be a multi-dimensional array with each index containing keys for 'content' and 'label'
	 * @param null|string   $prefix Optional The prefix to use for the t
	 * @param string $class Optional. Class for outermost container
	 * @param bool|string    Optional. ID for outermost container.
	 *
	 * @return string The container
	 *
	 * @since 0.0.1
	 */
	public function output_container( $content, $prefix = null, $class = '', $id = false ) {
		foreach( $content as $i => $c ) {

			if ( ! isset( $c[ 'content' ] ) || ! is_string( $c[ 'content' ] ) ) {

				unset( $content[ $i ] );
				if ( HT_DEV_MODE ) {
					echo sprintf( __( 'The tab %1s was not a string, so it was unset from output container.', 'ht_dms' ), $i );
					print_c3( $c );
				}

			}

		}

		if ( ( function_exists( 'is_phone' ) && is_phone() ) || ( defined( 'HT_DEVICE' ) && HT_DEVICE === 'phone' ) ) {
			return $this->accordion( $content, $prefix, $class, $id );
		}
		else {
			return $this->tabs( $content, $prefix, $class, $id );
		}
	}

	/**
	 * Creates a hamburger menu
	 *
	 * @param array $menu_items Should be in form of link => link text
	 *
	 * @return array|bool|string
	 */
	public function hamburger( $menu_items ) {

		if ( is_array( $menu_items ) ) {
			foreach( $menu_items as $link => $text ) {
				$menu[] = sprintf( '<a href="%1s">%2s</a>', $link, $text );
			}
			if ( is_array( $menu ) ) {
				$out[] = sprintf( '<div>%1s</div>', implode( $menu ) );
			}
			else {
				$out = false;
			}

			if ( is_array( $out ) ) {
				$out = sprintf( '<nav id="ht-sub-menu"><span class="button" id="ht-sub-menu-button"></span>%1s</nav>', implode( $out ) );
			}

		}

		if ( is_string( $out ) ) {
			return $out;
		}


	}

	/**
	 * Creates view of a group of members
	 *
	 *
	 * @param  array    $users Array of user IDs.
	 * @param int  $desktop_wide Number of items wide in desktop view
	 * @param bool $mobile_wide Optional. Number of items wide in mobile view. If false, the default, will be half of $desktop_wide.
	 *
	 * @since 0.0.3
	 *
	 * @return string
	 */
	public function members_details_view( $users, $desktop_wide = 8, $mobile_wide = false, $mini_mode = false ) {
		$members = false;
		if ( is_array( $users ) ) {
			foreach( $users as $key => $user ) {
				if ( ht_dms_integer( $user ) ) {
					$user = ht_dms_ui()->build_elements()->member_details( $user );
				}
				if ( ! pods_v( 'name', $user ) && isset( $user[0] )) {
					$user = $user[0];

				}


				$name = pods_v( 'name', $user );

				if ( ! is_null( $name ) ) {
					$avatar = pods_v( 'avatar', $user, ht_dms_fallback_avatar() );
					if ( ! $mini_mode ) {
						$members[ ] = sprintf( '<li class="member-view"><div class="avatar">%1s</span><div class="name">%2s</span></li>', $avatar, $name );
					}
					else {
						$members[] = sprintf( '<li class="member-view"><div class="mini-avatar" name="%1s">%2s</div></li>', $name, $avatar );
					}
				}
			}
		}

		if ( is_array( $members ) ) {
			if ( ! $mobile_wide ) {
				$mobile_wide = $desktop_wide / 2;
			}

			$class = 'members-view';
			if ( $mini_mode ) {
				$class .= ' mini-mode';
			}

 			return sprintf( '<div class="%0s"><ul class="small-block-grid-%d large-block-grid-%d">%3s</ul></div>', $class, $mobile_wide, $desktop_wide, implode( $members ) );


		}
		else {
			return '';
		}


	}

	/**
	 * Visual display of the current status of a consensus_ui
	 *
	 * @todo deprecate or rm
	 *
	 * @since 0.0.3
	 */
	public function view_consensus( $dID ) {

		return consensus_ui::view( $dID );

	}

	/**
	 * Returns the third element with its wrapping markup.
	 *
	 * @param $type network|user|organization|group|consenus
	 *
	 * @return string
	 */
	public function third_element( $type, $id ) {
		if ( $type == 'consensus_ui' ) {
			ht_dms_consensus($id);
			$content = $this->view_consensus( $id );
		}
		elseif ( in_array( $type, array( 'network', 'user', 'organization', 'group' ) ) ) {
			$content = call_user_func( array( ht_dms_ui()->activity( $type, $id ),  $type ), $id );
		}
		else{
			ht_dms_error();
		}

		return sprintf( '<div id="ht-dms-third-element" class="ht-dms-third-element-%1s">%2s</div>', $type, $content );

	}

	/**
	 * Tabs for organization facilitators to use to faciliate organizations
	 *
	 * @since 0.1.0
	 *
	 * @param int $oID The organization ID
	 * @param int $uID The user editing organization.
	 * @param \Pods $oObj Pods object for organization.
	 *
	 * @return array
	 */
	public function organization_facilitator_tabs( $oID, $uID, $oObj ) {
		$ui = ht_dms_ui();
		$tabs[] = array (
			'label'   => ht_dms_add_icon( __( 'New Group In Organization', 'ht_dms' ), array( 'new', 'group') ),
			'content' => $ui->add_modify()->new_group(  $oID, $uID ),
		);
		$tabs[] = array(
			'label'		=> ht_dms_add_icon( __( 'Edit Organization', 'ht_dms' ), array( 'edit', 'organization') ),
			'content'	=> $ui->add_modify()->edit_organization( $oID, $uID, $oObj ),
		);
		$tabs[]  = array(
			'label'		=> ht_dms_add_icon( __( 'Organization Location', 'ht_dms' ), array( 'edit', 'organization' ) ),
			'content'   => $ui->add_modify()->organization_details(),
		);
		$tabs[] = array(
			'label'		=> ht_dms_add_icon( __( 'Invite Members', 'ht_dms' ), array( 'new', 'user') ),
			'content'	=> $ui->add_modify()->invite_member( $oID, $oObj, false ),
		);

		return $tabs;
	}


	/**
	 * Holds the instance of this class.
	 *
	 * @since  0.0.1
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

}

