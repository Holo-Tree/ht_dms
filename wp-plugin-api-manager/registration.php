<?php
/**
 * Register classes to use the WP Plugin Hook Interfaces
 *
 * @package   @TODO
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link      
 * @copyright 2014 Josh Pollock
 */
class HT_DMS_WP_API_Registration {
	private $api_manager;

	private $booted = false;

	public function __construct() {
		$this->api_manager = new HT_DMS_WP_API_Manager();
	}

	public function get_subscribers() {
		return array(
			ht_dms_ui()->ajax_callbacks(),
			ht_dms_ui()->view_loaders(),
			ht_dms_organization_class(),
			ht_dms_decision_class(),
			ht_dms_task_class(),
			ht_dms_group_class(),
			ht_dms_notification_class(),
			ht_dms_automatic_notifications_class(),
			new ht_dms\helper\caldera_actions(),
			ht_dms_common_class(),
		);
	}

	public function boot() {
		if ($this->booted) {
			return;
		}

		foreach ($this->get_subscribers() as $subscriber) {
			$this->api_manager->register($subscriber);
		}

		$this->booted = true;
	}
}

